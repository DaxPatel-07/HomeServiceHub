@extends('layouts.app')
@section('title','AI Assistant')
@section('content')
<div class="container" style="padding:2rem 0;">
  <div class="row g-4">
    <div class="col-lg-4">
      <div class="card-custom p-4">
        <div class="text-center mb-3">
          <div class="ai-avatar mx-auto mb-2" style="width:64px;height:64px;font-size:1.8rem;">🤖</div>
          <h5 style="font-weight:800;">AI Assistant</h5>
          <p style="color:var(--text-secondary);font-size:.85rem;">Powered by Gemini AI</p>
        </div>
        <div class="divider"></div>
        <div style="font-size:.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:.75rem;">Suggested Questions</div>
        <div class="d-flex flex-column gap-2">
          @foreach(['Find electricians near me','My AC is not cooling','Need a plumber urgently','Recommend a house cleaner','What services do you offer?','How do I book a split service?'] as $q)
          <button onclick="sendSuggestion('{{ $q }}')" style="background:var(--bg-light);border:1px solid var(--card-border);border-radius:10px;padding:.55rem .9rem;font-size:.82rem;text-align:left;cursor:pointer;transition:.2s;" onmouseover="this.style.borderColor='var(--brand-blue)';this.style.color='var(--brand-blue)'" onmouseout="this.style.borderColor='var(--card-border)';this.style.color='inherit'">
            <i class="fas fa-comment-dots me-2" style="color:var(--brand-blue);"></i>{{ $q }}
          </button>
          @endforeach
        </div>
        <div class="divider"></div>
        <div style="font-size:.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:.75rem;">Issue Diagnostics</div>
        <div style="background:var(--bg-light);border-radius:12px;padding:1rem;">
          <p style="font-size:.8rem;color:var(--text-secondary);margin-bottom:.75rem;">Describe your home problem and AI will identify the service:</p>
          <textarea id="issueInput" style="width:100%;border:1.5px solid var(--card-border);border-radius:8px;padding:.65rem;font-size:.82rem;resize:none;" rows="3" placeholder="e.g. My ceiling fan is making noise..."></textarea>
          <button onclick="diagnoseIssue()" class="btn-primary-custom btn w-100 mt-2" style="justify-content:center;padding:.6rem;"><i class="fas fa-search me-2"></i>Diagnose Issue</button>
        </div>
        <div id="diagnosisResult" style="display:none;margin-top:1rem;"></div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="ai-chat-container" style="height:calc(100vh - 180px);">
        <div class="ai-chat-header">
          <div class="ai-avatar" style="flex-shrink:0;">🤖</div>
          <div style="flex:1;">
            <div style="color:#fff;font-weight:700;">AI Assistant</div>
            <div style="color:rgba(255,255,255,.85);font-size:.75rem;">Gemini-Powered Home Service Expert</div>
          </div>
          <button onclick="clearChat()" style="background:rgba(255,255,255,.2);border:none;border-radius:8px;padding:.4rem .8rem;color:#fff;cursor:pointer;font-size:.78rem;font-weight:600;"><i class="fas fa-trash me-1"></i>Clear</button>
        </div>

        <div class="ai-chat-messages" id="chatMessages">
          <div class="message-bubble ai">
            <div class="bubble">Hello! I'm your Home Service Hub AI Assistant. 🏠<br><br>I can help you:<br>• Find the right service provider<br>• Diagnose your home issues<br>• Book single or split services<br>• Track your bookings<br><br>What home problem can I help you with today?</div>
            <div class="time">Just now</div>
          </div>
        </div>

        {{-- Quick Topic Suggestion Chips --}}
        <div class="p-2 px-3 border-top" style="background:#f8fafc; overflow-x:auto; white-space:nowrap; display:flex; gap:.5rem;">
          @php
            $quickSuggestions = [
              ['Services', 'What home services are available in Ahmedabad & Gandhinagar?'],
              ['Booking', 'How do I book a single or split home service?'],
              ['Pricing', 'What are the typical service rates and pricing?'],
              ['Providers', 'How are service providers verified and rated?'],
              ['Availability', 'Are emergency and same-day services available?'],
              ['Support', 'How do I track bookings or contact customer support?'],
            ];
          @endphp
          @foreach($quickSuggestions as $chip)
          <button type="button" onclick="sendSuggestion('{{ addslashes($chip[1]) }}')" class="btn btn-sm" style="background:#fff; border:1.5px solid var(--card-border); border-radius:20px; font-size:.76rem; font-weight:700; color:var(--color-text-dark); padding:.28rem .85rem; flex-shrink:0; transition:.2s;" onmouseover="this.style.borderColor='var(--color-primary)'; this.style.color='var(--color-primary)';" onmouseout="this.style.borderColor='var(--card-border)'; this.style.color='var(--color-text-dark)';">
            <i class="fas fa-sparkles me-1" style="color:var(--gold);"></i>{{ $chip[0] }}
          </button>
          @endforeach
        </div>

        <div class="ai-chat-input">
          <textarea id="chatInput" placeholder="Type your message..." rows="1" onkeydown="handleKey(event)" oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"></textarea>
          <button class="ai-send-btn" onclick="sendMessage()" id="sendBtn"><i class="fas fa-paper-plane"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('js/ai-chat.js') }}"></script>
@endpush
