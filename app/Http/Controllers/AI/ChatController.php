<?php
namespace App\Http\Controllers\AI;
use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class ChatController extends Controller {
    private GeminiService $gemini;
    public function __construct(GeminiService $gemini) { $this->gemini = $gemini; }

    public function index() {
        return view('ai.assistant');
    }
    public function chat(Request $request) {
        $request->validate(['message' => 'required|string|max:1000']);
        $history = $request->history ?? [];
        $user = auth()->user();
        $customer = ($user && $user->role === 'customer') ? $user : null;
        $response = $this->gemini->chat($request->message, $history, $customer);
        return response()->json(['response' => $response, 'timestamp' => now()->format('H:i')]);
    }
    public function suggestService(Request $request) {
        $request->validate(['issue' => 'required|string|max:500']);
        $suggestion = $this->gemini->suggestService($request->issue);
        return response()->json($suggestion);
    }
}
