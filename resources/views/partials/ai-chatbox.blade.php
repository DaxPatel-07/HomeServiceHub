@php
  $isCustomer = auth()->check() && auth()->user()->role === 'customer';
  $isHome = request()->routeIs('home');

  if ($isCustomer) {
    $suggestedQuestions = [
      'Show my bookings',
      'What is my booking status?',
      'How can I contact my provider?',
      'How can I cancel a booking?',
      'What services can I book?'
    ];
  } else {
    $suggestedQuestions = [
      'What services are available?',
      'How can I book a service?',
      'Show available providers',
      'How does HomeServiceHub work?',
      'How can I get help?'
    ];
  }
@endphp

<!-- AI Floating Chatbox Trigger Button -->
<div id="aiChatFloatingTrigger" class="ai-chat-trigger" onclick="toggleFloatingChat()" title="Chat with AI Assistant">
  <div class="ai-trigger-inner">
    <i class="fas fa-robot"></i>
    <span class="ai-trigger-badge">AI</span>
  </div>
</div>

<!-- AI Floating Chatbox Window -->
<div id="aiChatFloatingBox" class="ai-floating-box" style="display: none;">
  <!-- Header -->
  <div class="ai-floating-header">
    <div class="d-flex align-items-center gap-2">
      <div class="ai-avatar-badge">🤖</div>
      <div>
        <div class="ai-header-title">HomeServiceHub AI</div>
        <div class="ai-header-subtitle">
          @if($isCustomer)
            Customer Assistant &bull; Online
          @else
            Smart Assistant &bull; Online
          @endif
        </div>
      </div>
    </div>
    <div class="d-flex align-items-center gap-1">
      <button type="button" class="ai-box-btn" onclick="clearFloatingChat()" title="Clear chat history">
        <i class="fas fa-trash-alt"></i>
      </button>
      <button type="button" class="ai-box-btn" onclick="toggleFloatingChat()" title="Close chat">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>

  <!-- Messages Scroll Area -->
  <div class="ai-chat-messages" id="floatingChatMessages">
    <div class="message-bubble ai">
      <div class="bubble">
        @if($isCustomer)
          Hello <strong>{{ auth()->user()->name }}</strong>! 👋 How can I help with your home services or bookings today?
        @else
          Hello! 👋 Welcome to HomeServiceHub. What service can I help you find or book today?
        @endif
      </div>
      <div class="time">Just now</div>
    </div>
  </div>

  <!-- Suggested Questions Section (Inside chatbox, above message input) -->
  <div class="ai-suggested-section">
    <div class="ai-suggested-label">
      <i class="fas fa-sparkles me-1 text-primary"></i>Suggested Questions:
    </div>
    <div class="ai-suggested-chips">
      @foreach($suggestedQuestions as $q)
        <button type="button" class="ai-suggestion-chip" onclick="sendFloatingSuggestion('{{ addslashes($q) }}')">
          {{ $q }}
        </button>
      @endforeach
    </div>
  </div>

  <!-- Chat Input Bar -->
  <div class="ai-chat-input ai-floating-input">
    <textarea id="floatingChatInput" placeholder="Ask a question..." rows="1" onkeydown="handleFloatingKey(event)" oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"></textarea>
    <button class="ai-send-btn" onclick="sendFloatingMessage()" id="floatingSendBtn" title="Send message">
      <i class="fas fa-paper-plane"></i>
    </button>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('open_ai') === '1') {
    toggleFloatingChat();
  }
});
</script>
