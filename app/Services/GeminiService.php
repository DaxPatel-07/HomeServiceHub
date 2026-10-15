<?php
namespace App\Services;
use App\Models\Provider;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService {
    private string $apiKey;
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct() {
        $this->apiKey = config('gemini.api_key', env('GEMINI_API_KEY', ''));
    }

    /**
     * Handle multi-turn conversation with Gemini API or intelligent local engine.
     * When $customer is provided, customer-specific booking context is injected.
     */
    public function chat(string $message, array $history = [], ?\App\Models\User $customer = null): string {
        $customerContext = '';
        if ($customer && $customer->role === 'customer') {
            $customerContext = $this->buildCustomerContext($customer);
        }

        if (!empty($this->apiKey)) {
            try {
                $contents = [];
                foreach ($history as $h) {
                    $role = ($h['role'] === 'user') ? 'user' : 'model';
                    $contents[] = ['role' => $role, 'parts' => [['text' => $h['text']]]];
                }
                $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

                $systemInstruction = "You are the AI Assistant for Home Service Hub (a premium home services platform in Ahmedabad and Gandhinagar). "
                    . "Services available: Electrician, Plumber, AC Technician, Carpenter, Painter, Cleaner, Appliance Repair. "
                    . "Features: Single booking, Split booking (multi-trade scheduling), verified background-checked providers, 4.8+ rating guarantee. "
                    . "Payment methods: Cash, Card, Wallet. "
                    . ($customerContext ? "CURRENT AUTHENTICATED CUSTOMER DATA:\n{$customerContext}\nAnswer questions for this customer accurately. Do not leak other customer data." : "PUBLIC USER: Do not expose private customer data.")
                    . "\nBe friendly, accurate, practical, and helpful. Use emojis, bold text, and bullet points. Never invent information. If information is unavailable, say so clearly.";

                $response = Http::timeout(15)->post($this->apiUrl . '?key=' . $this->apiKey, [
                    'system_instruction' => ['parts' => [['text' => $systemInstruction]]],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 800,
                    ]
                ]);

                if ($response->successful()) {
                    $text = $response->json('candidates.0.content.parts.0.text');
                    if (!empty($text)) {
                        return $text;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Gemini API call failed, using intelligent local engine: ' . $e->getMessage());
            }
        }

        // Intelligent local response engine
        return $this->intelligentResponse($message, $history, $customer);
    }

    /**
     * Build customer context for AI
     */
    private function buildCustomerContext(\App\Models\User $customer): string {
        $bookings = \App\Models\Booking::where('customer_id', $customer->id)
            ->with(['provider.user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        if ($bookings->isEmpty()) {
            return "Customer Name: {$customer->name}\nTotal Bookings: 0 (No past or active bookings)";
        }

        $lines = ["Customer Name: {$customer->name}", "Recent Bookings:"];
        foreach ($bookings as $b) {
            $proName = $b->provider ? $b->provider->name : 'Unassigned';
            $proPhone = ($b->provider && $b->provider->user) ? $b->provider->user->phone : 'N/A';
            $catName = $b->category ? $b->category->name : 'Service';
            $date = $b->booking_date ? $b->booking_date->format('d M Y') : 'N/A';
            $time = $b->formatted_time ?: $b->booking_time;
            $lines[] = "- Booking #{$b->booking_code}: {$catName} with {$proName} (Phone: {$proPhone}) on {$date} at {$time}, Status: {$b->status}, Amount: ₹{$b->total_amount}, City: {$b->city}";
        }

        return implode("\n", $lines);
    }

    /**
     * Smart Diagnostic Tool: identify category, urgency, and technical reason
     */
    public function suggestService(string $issue): array {
        if (!empty($this->apiKey)) {
            try {
                $prompt = "Analyze this home issue: '{$issue}'. "
                    . "Choose the best category from [Electrician, Plumber, Carpenter, Painter, AC Technician, Cleaner, Appliance Repair]. "
                    . "Return ONLY valid JSON with keys: 'category', 'reason', 'urgency' (low|medium|high). No markdown formatting.";
                
                $response = Http::timeout(10)->post($this->apiUrl . '?key=' . $this->apiKey, [
                    'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]]
                ]);

                if ($response->successful()) {
                    $raw = $response->json('candidates.0.content.parts.0.text');
                    $clean = trim(preg_replace('/```json|```/', '', $raw));
                    $data = json_decode($clean, true);
                    if (isset($data['category'])) {
                        return $data;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Gemini suggestService fallback to local engine: ' . $e->getMessage());
            }
        }

        // Local diagnostic classification
        return $this->localDiagnostic($issue);
    }

    /**
     * Local Diagnostic Rule Engine
     */
    private function localDiagnostic(string $issue): array {
        $lower = strtolower($issue);

        if (preg_match('/(spark|electric|shock|wiring|short circuit|fuse|switch|mcb|tripping|light|fan|power)/i', $lower)) {
            $urgency = (str_contains($lower, 'spark') || str_contains($lower, 'shock') || str_contains($lower, 'smoke')) ? 'high' : 'medium';
            return [
                'category' => 'Electrician',
                'reason' => 'Potential electrical fault or component wear. Requires inspection by a certified electrician to avoid short circuits.',
                'urgency' => $urgency,
            ];
        }

        if (preg_match('/(leak|pipe|water|drain|tap|faucet|clog|sink|flush|sewage|seep|geyser)/i', $lower)) {
            $urgency = (str_contains($lower, 'burst') || str_contains($lower, 'overflow') || str_contains($lower, 'flood')) ? 'high' : 'medium';
            return [
                'category' => 'Plumber',
                'reason' => 'Water line or drainage blockage detected. A plumber can inspect piping, seals, and restore normal water flow.',
                'urgency' => $urgency,
            ];
        }

        if (preg_match('/(ac|cooling|cool|air condition|hvac|gas refill|filter|condenser|compressor)/i', $lower)) {
            $urgency = (str_contains($lower, 'burning') || str_contains($lower, 'smell')) ? 'high' : 'medium';
            return [
                'category' => 'AC Technician',
                'reason' => 'AC unit cooling or gas pressure issue. Certified AC technician will diagnose refrigerant levels, coils, and compressor.',
                'urgency' => $urgency,
            ];
        }

        if (preg_match('/(wood|door|hinge|lock|drawer|cabinet|furniture|bed|chair|table|cupboard|carpenter)/i', $lower)) {
            return [
                'category' => 'Carpenter',
                'reason' => 'Woodwork or structural hardware repair needed. Our skilled carpenters can fix hinges, locks, and alignments.',
                'urgency' => 'low',
            ];
        }

        if (preg_match('/(paint|wall|stain|damp|peeling|crack|color|whitewash|texture)/i', $lower)) {
            return [
                'category' => 'Painter',
                'reason' => 'Wall surface preparation, damp treatment, or fresh protective coating recommended for long-lasting aesthetics.',
                'urgency' => 'low',
            ];
        }

        if (preg_match('/(clean|dust|deep clean|sanitize|mop|bathroom|sofa|carpet|kitchen clean|pest)/i', $lower)) {
            return [
                'category' => 'Cleaner',
                'reason' => 'Deep sanitation and professional cleaning recommended with specialized equipment and eco-safe supplies.',
                'urgency' => 'low',
            ];
        }

        if (preg_match('/(washing machine|fridge|refrigerator|microwave|oven|ro|purifier|tv|chimney|appliance)/i', $lower)) {
            return [
                'category' => 'Appliance Repair',
                'reason' => 'Home appliance malfunction. Our technician will test motor, thermostat, circuits, and mechanical parts.',
                'urgency' => 'medium',
            ];
        }

        return [
            'category' => 'General Inspection',
            'reason' => 'Our verified multi-skilled technicians will conduct an on-site diagnosis and provide an upfront quote.',
            'urgency' => 'medium',
        ];
    }

    /**
     * Comprehensive Local NLP Knowledge Base
     */
    private function intelligentResponse(string $message, array $history = [], ?\App\Models\User $customer = null): string {
        $lower = trim(strtolower($message));

        // Check for follow-up context from history
        $lastAiMessage = '';
        $lastUserMessage = '';
        if (!empty($history)) {
            for ($i = count($history) - 1; $i >= 0; $i--) {
                if (($history[$i]['role'] ?? '') === 'model' && empty($lastAiMessage)) {
                    $lastAiMessage = strtolower($history[$i]['text'] ?? '');
                }
                if (($history[$i]['role'] ?? '') === 'user' && empty($lastUserMessage)) {
                    $lastUserMessage = strtolower($history[$i]['text'] ?? '');
                }
            }
        }

        // =========================================================
        // A. CUSTOMER-SPECIFIC BOOKING QUERIES (when logged-in)
        // =========================================================
        if ($customer && $customer->role === 'customer') {
            // 1. My Bookings / List bookings / "show my bookings" / "what bookings do I have"
            if (preg_match('/(show (my )?bookings|what bookings|my booking|list (my )?bookings|view bookings|all bookings|which service did i book|what service did i book)/i', $lower)) {
                $bookings = \App\Models\Booking::where('customer_id', $customer->id)->with(['provider', 'category'])->latest()->take(5)->get();
                if ($bookings->isEmpty()) {
                    return "📋 **Your Bookings**\n\n"
                         . "Hello {$customer->name}, you have **no bookings yet**.\n\n"
                         . "Ready to schedule a service? Browse our top verified providers in Ahmedabad & Gandhinagar:\n"
                         . "👉 [Find Services & Providers](/search)";
                }

                $res = "📋 **Your Recent Bookings ({$bookings->count()})**\n\n";
                foreach ($bookings as $b) {
                    $pro = $b->provider ? $b->provider->name : 'Assigned Provider';
                    $statusBadge = ucfirst(str_replace('_', ' ', $b->status));
                    $time = $b->formatted_time ?: $b->booking_time;
                    $res .= "• **#{$b->booking_code}**: {$b->category->name} with **{$pro}**\n"
                         . "  🗓️ {$b->booking_date->format('d M Y')} at {$time} | 📍 {$b->city}\n"
                         . "  ⚡ Status: **{$statusBadge}** | ₹" . number_format($b->total_amount) . "\n\n";
                }
                $res .= "👉 [View All Bookings in Dashboard](/customer/bookings)";
                return $res;
            }

            // 2. Booking Status Query / "what is my booking status"
            if (preg_match('/(booking status|what is my (booking )?status|status of my booking|is my booking (confirmed|accepted|pending)|track (my )?booking|check (my )?status)/i', $lower)) {
                $latest = \App\Models\Booking::where('customer_id', $customer->id)->with(['provider', 'category'])->latest()->first();
                if (!$latest) {
                    return "ℹ️ **Booking Status**\n\n"
                         . "You do not have any active or past bookings registered yet.\n\n"
                         . "👉 [Book a Service Now](/search)";
                }

                $statusText = match($latest->status) {
                    'pending' => '⏳ **Pending** — Waiting for the service provider to accept your booking request.',
                    'accepted' => '✅ **Accepted** — Your provider has confirmed the booking and will arrive at the scheduled time.',
                    'in_progress' => '🚀 **In Progress** — Your service is currently underway.',
                    'completed' => '🎉 **Completed** — The job has been successfully finished.',
                    'cancelled' => '❌ **Cancelled** — This booking was cancelled.',
                    default => ucfirst(str_replace('_', ' ', $latest->status)),
                };

                $proName = $latest->provider ? $latest->provider->name : 'Assigned Provider';
                $time = $latest->formatted_time ?: $latest->booking_time;

                return "🔍 **Latest Booking Status**\n\n"
                     . "• **Booking Code**: `#{$latest->booking_code}`\n"
                     . "• **Service**: {$latest->category->name}\n"
                     . "• **Provider**: **{$proName}**\n"
                     . "• **Schedule**: {$latest->booking_date->format('d M Y')} at {$time}\n"
                     . "• **Status**: {$statusText}\n"
                     . "• **Total Amount**: ₹" . number_format($latest->total_amount) . "\n\n"
                     . "👉 [Manage Booking Details](/customer/bookings/{$latest->id}) | [All Bookings](/customer/bookings)";
            }

            // 3. Contact Provider / Provider assigned / "which provider is assigned"
            if (preg_match('/(contact (my )?provider|call (my )?provider|which provider|provider assigned|who is my provider|provider details|provider phone)/i', $lower)) {
                $latest = \App\Models\Booking::where('customer_id', $customer->id)
                    ->whereIn('status', ['pending', 'accepted', 'in_progress'])
                    ->with(['provider.user', 'category'])
                    ->latest()
                    ->first();

                if (!$latest) {
                    $latest = \App\Models\Booking::where('customer_id', $customer->id)->with(['provider.user', 'category'])->latest()->first();
                }

                if (!$latest || !$latest->provider) {
                    return "📞 **Provider Contact**\n\n"
                         . "You currently do not have an active booking with an assigned provider.\n\n"
                         . "👉 [Find a Provider](/search) to book a service.";
                }

                $pro = $latest->provider;
                $phone = ($pro->user && $pro->user->phone) ? $pro->user->phone : 'Contact available after confirmation';
                $rating = $pro->average_rating ? "{$pro->average_rating}★" : 'New Provider';

                return "📞 **Your Assigned Provider Details**\n\n"
                     . "• **Provider Name**: **{$pro->name}**\n"
                     . "• **Service**: {$latest->category->name} (Booking `#{$latest->booking_code}`)\n"
                     . "• **Contact Phone**: `{$phone}`\n"
                     . "• **Rating**: {$rating} ({$pro->completed_jobs} completed jobs)\n"
                     . "• **Service Area**: {$pro->service_area}\n\n"
                     . "💡 *You can manage your booking or view full details here:*\n"
                     . "👉 [View Booking Details](/customer/bookings/{$latest->id})";
            }

            // 4. Cancel Booking for logged-in customer
            if (preg_match('/(how (can|do) i cancel|cancel (my )?booking|cancel appointment|want to cancel)/i', $lower)) {
                $cancellable = \App\Models\Booking::where('customer_id', $customer->id)
                    ->whereIn('status', ['pending', 'accepted'])
                    ->latest()
                    ->first();

                if ($cancellable) {
                    return "❌ **Cancelling a Booking**\n\n"
                         . "You have an active booking: **#{$cancellable->booking_code}** ({$cancellable->category->name}) scheduled on {$cancellable->booking_date->format('d M Y')}.\n\n"
                         . "**How to cancel:**\n"
                         . "1. Visit your **[My Bookings Page](/customer/bookings)**\n"
                         . "2. Click the red **Cancel** button next to booking `#{$cancellable->booking_code}`\n"
                         . "3. Confirm the cancellation prompt\n\n"
                         . "⚠️ *Note: Only pending or accepted bookings can be cancelled before service delivery.*";
                }

                return "❌ **How to Cancel a Booking**\n\n"
                     . "1. Go to your **[My Bookings](/customer/bookings)** page in your Customer Portal\n"
                     . "2. Find the booking you wish to cancel\n"
                     . "3. Click the red **Cancel** button next to it\n"
                     . "4. Confirm your cancellation\n\n"
                     . "💡 You currently have no pending or accepted bookings requiring cancellation.";
            }

            // 5. What services can I book?
            if (preg_match('/(what services can i book|services available|what services do you (provide|offer)|show services)/i', $lower)) {
                $categories = \App\Models\ServiceCategory::where('is_active', true)->get();
                $catList = "";
                foreach ($categories as $c) {
                    $catList .= "• **{$c->name}** — {$c->description}\n";
                }

                return "🛠️ **Available Services on HomeServiceHub**\n\n"
                     . "Hello {$customer->name}, you can book any of the following verified home services:\n\n"
                     . $catList . "\n"
                     . "⚡ **Special Options:**\n"
                     . "• **Single Booking**: Book one specialist for your date & 12-hour AM/PM time slot\n"
                     . "• **Split Booking**: Schedule multiple services simultaneously (e.g. Electrician + Plumber)\n\n"
                     . "👉 [Find Services](/search) | [Split Booking](/customer/split-booking)";
            }
        }

        // =========================================================
        // B. GENERAL PLATFORM QUERIES (Homepage & Platform Usage)
        // =========================================================

        // 1. Greetings
        if (preg_match('/^(hi|hello|hey|greetings|good (morning|afternoon|evening)|namaste|hola)\b/i', $lower)) {
            $nameGreeting = ($customer && $customer->name) ? " **{$customer->name}**" : "";
            return "👋 **Hello{$nameGreeting}! Welcome to HomeServiceHub.**\n\n"
                 . "I'm your intelligent home assistant. How can I help you today?\n\n"
                 . "• 🔌 **Electrical Issues** (wiring, switches, fans)\n"
                 . "• 🔧 **Plumbing Repairs** (leaks, blockages, taps)\n"
                 . "• ❄️ **AC Servicing** (cooling, gas refill)\n"
                 . "• 🪚 **Carpentry & Furniture**\n"
                 . "• 🎨 **Painting & Waterproofing**\n"
                 . "• 🧹 **Deep Cleaning**\n"
                 . "• ⚡ **Split Booking** (multi-trade scheduling)\n\n"
                 . "Ask me any question or describe what needs fixing!";
        }

        // 2. Services Available / What services are available / "what services do you provide"
        if (preg_match('/(what services|services available|services do you (provide|offer)|list of services|available services)/i', $lower)) {
            $categories = \App\Models\ServiceCategory::where('is_active', true)->get();
            $catList = "";
            foreach ($categories as $c) {
                $catList .= "• **{$c->name}**: {$c->description}\n";
            }

            return "🛠️ **Services Available on HomeServiceHub**\n\n"
                 . "We connect homeowners in **Ahmedabad** and **Gandhinagar** with verified local pros:\n\n"
                 . $catList . "\n"
                 . "All providers are background-checked with verified skill scores and ratings.\n\n"
                 . "👉 [Explore All Services & Book](/search)";
        }

        // 3. How can I book a service / booking process
        if (preg_match('/(how (can|do) i book|how to book|booking process|book a service|make a booking)/i', $lower)) {
            return "📅 **How to Book a Service on HomeServiceHub**\n\n"
                 . "Booking is simple and takes under 2 minutes:\n\n"
                 . "1. **Search & Select**: Visit **[Find Providers](/search)** and choose your required service (e.g. Electrician, Plumber)\n"
                 . "2. **Choose Provider**: Pick a verified pro based on ratings, hourly rates, and badges\n"
                 . "3. **Select Schedule**: Choose your preferred date and **12-Hour AM/PM time slot**\n"
                 . "4. **Add Address**: Enter your home address in Ahmedabad or Gandhinagar\n"
                 . "5. **Payment Method**: Pay with Cash on Delivery, Card, or Wallet\n"
                 . "6. **Confirmation**: You'll receive instant tracking and your provider will be notified immediately!\n\n"
                 . "👉 [Book a Service Now](/search) | [Try Split Booking](/customer/split-booking)";
        }

        // 4. Show available providers
        if (preg_match('/(show (available )?providers|available providers|find providers|top providers|recommend providers)/i', $lower)) {
            $topPros = \App\Models\Provider::with(['user', 'category'])
                ->where('verification_status', 'verified')
                ->where('is_active', true)
                ->orderByDesc('skill_score')
                ->take(4)
                ->get();

            if ($topPros->isEmpty()) {
                return "👷 **Available Service Providers**\n\n"
                     . "We have certified, verified service technicians ready across Ahmedabad and Gandhinagar.\n\n"
                     . "👉 [Browse Verified Providers](/search)";
            }

            $res = "⭐ **Top-Rated Verified Providers Available Today**\n\n";
            foreach ($topPros as $p) {
                $res .= "• **{$p->name}** — {$p->category->name}\n"
                     . "  Rating: {$p->average_rating}★ | ₹" . number_format($p->hourly_rate) . "/hr | Area: {$p->service_area}\n\n";
            }
            $res .= "👉 [View All Providers & Filter by Area](/search)";
            return $res;
        }

        // 5. How does HomeServiceHub work
        if (preg_match('/(how does (it|homeservicehub|this) work|what is homeservicehub|how it works)/i', $lower)) {
            return "🏠 **How HomeServiceHub Works**\n\n"
                 . "HomeServiceHub is Gujarat's trusted on-demand home maintenance platform:\n\n"
                 . "1. **Verified Pros**: Every technician is identity-checked, skill-assessed, and vetted before going live.\n"
                 . "2. **Dynamic Skill Score**: We rank providers using actual completion rates and genuine customer reviews.\n"
                 . "3. **Flexible Appointments**: Book 12-hour AM/PM slots that fit your daily schedule.\n"
                 . "4. **Split Booking**: Need an Electrician and a Plumber on the same day? Book them together in a single order!\n"
                 . "5. **Secure & Transparent**: Clear upfront pricing with no hidden charges.\n\n"
                 . "👉 [Get Started Today](/search)";
        }

        // 6. Help / How can I get help
        if (preg_match('/(how can i get help|help|support|contact|customer care|contact us|phone number|call you)/i', $lower)) {
            return "🤝 **How You Can Get Help**\n\n"
                 . "We're here to assist you with bookings, questions, or provider feedback:\n\n"
                 . "• 📞 **Toll-Free Helpline**: `+91 1800-HSH-HELP`\n"
                 . "• ✉️ **Email Support**: `support@hsh.com`\n"
                 . "• ⏰ **Operating Hours**: Mon–Sun, 8:00 AM – 8:00 PM\n"
                 . "• 📍 **Service Coverage**: Ahmedabad & Gandhinagar\n\n"
                 . "If you already booked, you can track or cancel your booking directly from your **[Dashboard](/customer/bookings)**.";
        }

        // 7. Pricing / How much does a service cost
        if (preg_match('/(how much|pricing|price|cost|charge|rate|rates|hourly rate)/i', $lower)) {
            return "💳 **Transparent Pricing on HomeServiceHub**\n\n"
                 . "Our service charges are clear, competitive, and displayed upfront:\n\n"
                 . "• 🔌 **Electrician**: Typically ₹300 – ₹500/hr\n"
                 . "• 🔧 **Plumber**: Typically ₹250 – ₹450/hr\n"
                 . "• ❄️ **AC Technician**: Typically ₹400 – ₹650/hr\n"
                 . "• 🪚 **Carpenter**: Typically ₹350 – ₹550/hr\n"
                 . "• 🎨 **Painter**: Typically ₹300 – ₹500/hr\n"
                 . "• 🧹 **Deep Cleaning**: Typically ₹500 – ₹1,200 (package-based)\n\n"
                 . "💡 *No hidden fees. Payment is completed only once the job is marked complete to your satisfaction.*\n\n"
                 . "👉 [Browse Verified Providers & Rates](/search)";
        }

        // 8. Track Booking (General / Guest)
        if (preg_match('/(track (my )?booking|booking status)/i', $lower)) {
            return "🔍 **Track Your Booking**\n\n"
                 . "To view live booking status and assigned provider details:\n\n"
                 . "1. **[Login to your Account](/login)**\n"
                 . "2. Open your **[Customer Dashboard](/customer/bookings)**\n"
                 . "3. Check the real-time status of your active appointments (Pending, Accepted, In Progress, Completed)\n\n"
                 . "👉 [Login to Track Booking](/login)";
        }

        // 9. Cancellation Policy (General)
        if (preg_match('/(cancellation policy|cancel policy|refund|can i cancel)/i', $lower)) {
            return "🛡️ **Cancellation & Refund Policy**\n\n"
                 . "• **Free Cancellation**: You can cancel any booking free of charge while it is in `Pending` or `Accepted` status before the provider arrives.\n"
                 . "• **Refunds**: If paid in advance via Card/Wallet, refunds are initiated immediately to your original payment method.\n"
                 . "• **Rescheduling**: You can easily request a new date or 12-hour AM/PM time slot.\n\n"
                 . "👉 [Go to My Bookings](/customer/bookings)";
        }

        // 10. Split Booking
        if (preg_match('/(split booking|split|multiple service|multi-service|book two services|together)/i', $lower)) {
            return "⚡ **Split Booking Feature**\n\n"
                 . "Need multiple services on the same day (e.g. an **Electrician AND a Plumber**)? Our unique **Split Booking** lets you schedule multiple verified providers in a single checkout request!\n\n"
                 . "**How Split Booking works:**\n"
                 . "1. Visit the **[Split Booking Page](/customer/split-booking)**\n"
                 . "2. Add required services & pick individual 12-hour AM/PM time slots\n"
                 . "3. Enter your address once\n"
                 . "4. Confirm with a single click & track all appointments from your dashboard!\n\n"
                 . "👉 [Create a Split Booking Now](/customer/split-booking)";
        }

        // 11. Follow-up handling (context-aware)
        if (preg_match('/^(yes|sure|okay|ok|yeah|yep|tell me more|how|why|details)\b/i', $lower)) {
            if (str_contains($lastAiMessage, 'electric')) {
                return "🔌 **Electrician Booking Details**\n\n"
                     . "Would you like to find top-rated electricians in Ahmedabad or Gandhinagar? Our verified electricians can arrive today.\n\n"
                     . "👉 [Click here to view Electrician Profiles](/search?category=electrician)";
            }
            if (str_contains($lastAiMessage, 'plumb')) {
                return "🔧 **Plumbing Service Details**\n\n"
                     . "Our plumbers handle drain blocks, leaking pipes, faucet fitting, and water heaters.\n\n"
                     . "👉 [Click here to view Plumber Profiles](/search?category=plumber)";
            }
            if (str_contains($lastAiMessage, 'ac')) {
                return "❄️ **AC Technician Details**\n\n"
                     . "We offer AC cleaning, gas leak checking, repair, and new unit installation.\n\n"
                     . "👉 [Click here to view AC Technicians](/search?category=ac-technician)";
            }
            return "I'm ready to help! You can ask me to find a specific provider, diagnose a home repair issue, or check on your booking status.\n\n"
                 . "👉 [Find Services](/search) | [Customer Dashboard](/customer/dashboard)";
        }

        // 12. Specific Trades (Electrician, Plumber, AC, Carpenter, Painter, Cleaning)
        if (preg_match('/(electric|wiring|short circuit|switch|fan|light|fuse|mcb|tripping|spark)/i', $lower)) {
            return "⚡ **Electrical Service Assistance**\n\n"
                 . "Safety First: If you see sparks or smell burning wires, shut off the main breaker immediately!\n\n"
                 . "**Our verified electricians handle:**\n"
                 . "• Switchboard & socket repair\n"
                 . "• Ceiling fan installation & capacitor replacement\n"
                 . "• Short-circuit fault diagnosis & rewiring\n"
                 . "• Inverter & MCB box troubleshooting\n\n"
                 . "👉 [Find Verified Electricians](/search?category=electrician) | [Book Service](/search)";
        }

        if (preg_match('/(plumb|leak|pipe|drain|tap|faucet|flush|water tank|clog|sink|toilet)/i', $lower)) {
            return "🔧 **Plumbing Service Solutions**\n\n"
                 . "**Our verified plumbers can fix:**\n"
                 . "• Dripping faucets, showerheads & mixer taps\n"
                 . "• Blocked kitchen sinks, floor drains & toilet flushes\n"
                 . "• Concealed & exposed pipe leaks\n"
                 . "• Water heater (Geyser) installation & connection\n\n"
                 . "👉 [Find Plumbers Near You](/search?category=plumber) | [Book a Plumber](/search)";
        }

        if (preg_match('/(ac|cooling|cool|air condition|hvac|gas refill|filter|condenser)/i', $lower)) {
            return "❄️ **AC Service & Maintenance**\n\n"
                 . "Is your AC blowing warm air or leaking water?\n\n"
                 . "• **Deep Jet Cleaning**: Cleans cooling coils & blower\n"
                 . "• **Gas Leak Check & Refill**: Restores peak chilling\n"
                 . "• **Compressor & PCB Diagnosis**: Fixes sudden tripping\n\n"
                 . "👉 [Browse Top AC Technicians](/search?category=ac-technician)";
        }

        if (preg_match('/(carpenter|wood|door|hinge|lock|drawer|cabinet|furniture)/i', $lower)) {
            return "🪚 **Carpentry & Furniture Services**\n\n"
                 . "• Door locks, latch replacements & squeaky hinges\n"
                 . "• Modular kitchen cabinet repairs\n"
                 . "• Bed, wardrobe & study table assembly\n\n"
                 . "👉 [Find Professional Carpenters](/search?category=carpenter)";
        }

        if (preg_match('/(paint|wall|whitewash|texture|damp|waterproof)/i', $lower)) {
            return "🎨 **Professional Painting & Waterproofing**\n\n"
                 . "• Interior wall painting (Luxury & washable finishes)\n"
                 . "• Exterior weather-guard protection\n"
                 . "• Wall waterproofing & damp treatment\n\n"
                 . "👉 [Find Painting Experts](/search?category=painter)";
        }

        if (preg_match('/(clean|deep clean|sanitize|mop|sofa|carpet|housekeeping)/i', $lower)) {
            return "🧹 **Deep Cleaning & Sanitization**\n\n"
                 . "• Full home deep cleaning (kitchen, bathroom, floor)\n"
                 . "• Sofa & mattress shampooing\n"
                 . "• Bathroom descaling & sanitization\n\n"
                 . "👉 [Book a Cleaning Session](/search?category=cleaner)";
        }

        // 13. Default intelligent response
        return "🏠 **HomeServiceHub Assistant**\n\n"
             . "I can help you with:\n\n"
             . "• **Services**: Electrician, Plumber, AC Technician, Carpenter, Painter, Cleaner\n"
             . "• **Booking Help**: Single appointments or Split Bookings\n"
             . "• **Verified Providers**: Find top-rated specialists in Ahmedabad & Gandhinagar\n"
             . "• **Account & Bookings**: Track status, check scheduled times, or contact providers\n\n"
             . "Feel free to ask a specific question or describe any home issue you have!";
    }
}
