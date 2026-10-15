let chatHistory = [];
const csrfToken = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').getAttribute('content') : '';

function getCurrentTime() {
  return new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
}

function formatMarkdown(text) {
  let formatted = text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

  // Markdown links: [text](url) -> <a href="url" class="chat-link">text</a>
  formatted = formatted.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="chat-link" style="color:var(--primary);font-weight:700;text-decoration:underline;">$1</a>');

  // Bold: **text** -> <strong>text</strong>
  formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

  // Newlines -> <br>
  formatted = formatted.replace(/\n/g, '<br>');

  return formatted;
}

function addMessage(text, role) {
  const container = document.getElementById('chatMessages');
  if (!container) return null;
  const bubble = document.createElement('div');
  bubble.className = `message-bubble ${role}`;
  bubble.innerHTML = `<div class="bubble">${formatMarkdown(text)}</div><div class="time">${getCurrentTime()}</div>`;
  container.appendChild(bubble);
  container.scrollTop = container.scrollHeight;
  return bubble;
}

function showTyping() {
  const container = document.getElementById('chatMessages');
  if (!container) return;
  const typing = document.createElement('div');
  typing.className = 'message-bubble ai';
  typing.id = 'typingIndicator';
  typing.innerHTML = `<div class="bubble" style="display:flex;gap:5px;align-items:center;padding:12px 18px;">
    <span style="width:7px;height:7px;background:var(--primary);border-radius:50%;animation:pulse 0.8s ease infinite;"></span>
    <span style="width:7px;height:7px;background:var(--primary);border-radius:50%;animation:pulse 0.8s ease 0.2s infinite;"></span>
    <span style="width:7px;height:7px;background:var(--primary);border-radius:50%;animation:pulse 0.8s ease 0.4s infinite;"></span>
  </div>`;
  container.appendChild(typing);
  container.scrollTop = container.scrollHeight;
}

function removeTyping() {
  const el = document.getElementById('typingIndicator');
  if (el) el.remove();
}

async function sendMessage() {
  const input = document.getElementById('chatInput');
  if (!input) return;
  const message = input.value.trim();
  if (!message) return;

  input.value = '';
  input.style.height = 'auto';
  const sendBtn = document.getElementById('sendBtn');
  if (sendBtn) sendBtn.disabled = true;

  addMessage(message, 'user');
  chatHistory.push({role: 'user', text: message});
  showTyping();

  try {
    const res = await fetch('/ai/chat', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN': csrfToken},
      body: JSON.stringify({message, history: chatHistory.slice(-10)})
    });
    const data = await res.json();
    removeTyping();
    addMessage(data.response, 'ai');
    chatHistory.push({role: 'model', text: data.response});
  } catch(e) {
    removeTyping();
    addMessage('⚠️ Sorry, I encountered a connection issue. Please try again.', 'ai');
  }
  if (sendBtn) sendBtn.disabled = false;
}

function handleKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
}

function sendSuggestion(text) {
  const input = document.getElementById('chatInput');
  if (input) {
    input.value = text;
    sendMessage();
  }
}

function clearChat() {
  chatHistory = [];
  const container = document.getElementById('chatMessages');
  if (container) {
    container.innerHTML = '<div class="message-bubble ai"><div class="bubble">Chat cleared! 🏠 How can I help you today?</div><div class="time">' + getCurrentTime() + '</div></div>';
  }
}

async function diagnoseIssue() {
  const issueInput = document.getElementById('issueInput');
  if (!issueInput) return;
  const issue = issueInput.value.trim();
  if (!issue) return alert('Please describe your issue first.');
  const resultEl = document.getElementById('diagnosisResult');
  if (!resultEl) return;

  resultEl.style.display = 'block';
  resultEl.innerHTML = '<div style="text-align:center;padding:1rem;color:var(--text-muted);"><i class="fas fa-spinner loading-spin"></i> Analyzing your issue...</div>';

  try {
    const res = await fetch('/ai/suggest-service', {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
      body: JSON.stringify({issue})
    });
    const data = await res.json();
    const urgencyColor = data.urgency==='high'?'var(--danger)':data.urgency==='medium'?'var(--warning)':'var(--success)';
    const urgencyBadge = data.urgency==='high'?'🚨 HIGH URGENCY':data.urgency==='medium'?'⚡ MEDIUM URGENCY':'🟢 NORMAL';

    resultEl.innerHTML = `
      <div style="background:#fff;border:1.5px solid var(--card-border);border-radius:14px;padding:1.25rem;box-shadow:var(--shadow-sm);">
        <div style="font-weight:800;font-size:.92rem;margin-bottom:.6rem;color:var(--text-primary);">Diagnosis Summary</div>
        <div style="display:flex;gap:.5rem;align-items:center;margin-bottom:.75rem;flex-wrap:wrap;">
          <span style="background:rgba(245,158,11,.15);color:var(--primary);border-radius:8px;padding:.35rem .8rem;font-size:.85rem;font-weight:800;">📋 ${data.category}</span>
          <span style="background:${data.urgency==='high'?'rgba(239,68,68,.1)':'rgba(245,158,11,.1)'};color:${urgencyColor};border-radius:8px;padding:.35rem .7rem;font-size:.75rem;font-weight:800;">${urgencyBadge}</span>
        </div>
        <p style="font-size:.84rem;color:var(--text-secondary);line-height:1.6;margin:0 0 .75rem;">${data.reason}</p>
        <a href="/search?search=${encodeURIComponent(data.category)}" class="btn-primary-custom btn btn-sm w-100" style="justify-content:center;padding:.5rem;font-weight:700;"><i class="fas fa-search me-1"></i>Find Verified ${data.category}s</a>
      </div>`;
  } catch(e) {
    resultEl.innerHTML = '<div class="alert-custom alert-error-custom">Diagnostic analysis failed. Please try again.</div>';
  }
}

/* ==========================================================================
   FLOATING CHATBOX WIDGET LOGIC
   ========================================================================== */
let floatingChatHistory = [];

function toggleFloatingChat() {
  const box = document.getElementById('aiChatFloatingBox');
  if (!box) return;
  if (box.style.display === 'none' || box.style.display === '') {
    box.style.display = 'flex';
    const input = document.getElementById('floatingChatInput');
    if (input) setTimeout(() => input.focus(), 150);
  } else {
    box.style.display = 'none';
  }
}

function addFloatingMessage(text, role) {
  const container = document.getElementById('floatingChatMessages');
  if (!container) return null;
  const bubble = document.createElement('div');
  bubble.className = `message-bubble ${role}`;
  bubble.innerHTML = `<div class="bubble">${formatMarkdown(text)}</div><div class="time">${getCurrentTime()}</div>`;
  container.appendChild(bubble);
  container.scrollTop = container.scrollHeight;
  return bubble;
}

function showFloatingTyping() {
  const container = document.getElementById('floatingChatMessages');
  if (!container) return;
  const typing = document.createElement('div');
  typing.className = 'message-bubble ai';
  typing.id = 'floatingTypingIndicator';
  typing.innerHTML = `<div class="bubble" style="display:flex;gap:5px;align-items:center;padding:10px 16px;">
    <span style="width:6px;height:6px;background:var(--primary);border-radius:50%;animation:pulse 0.8s ease infinite;"></span>
    <span style="width:6px;height:6px;background:var(--primary);border-radius:50%;animation:pulse 0.8s ease 0.2s infinite;"></span>
    <span style="width:6px;height:6px;background:var(--primary);border-radius:50%;animation:pulse 0.8s ease 0.4s infinite;"></span>
  </div>`;
  container.appendChild(typing);
  container.scrollTop = container.scrollHeight;
}

function removeFloatingTyping() {
  const el = document.getElementById('floatingTypingIndicator');
  if (el) el.remove();
}

async function sendFloatingMessage() {
  const input = document.getElementById('floatingChatInput');
  if (!input) return;
  const message = input.value.trim();
  if (!message) return;

  input.value = '';
  input.style.height = 'auto';
  const sendBtn = document.getElementById('floatingSendBtn');
  if (sendBtn) sendBtn.disabled = true;

  addFloatingMessage(message, 'user');
  floatingChatHistory.push({role: 'user', text: message});
  showFloatingTyping();

  try {
    const res = await fetch('/ai/chat', {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
      body: JSON.stringify({message, history: floatingChatHistory.slice(-10)})
    });
    const data = await res.json();
    removeFloatingTyping();
    addFloatingMessage(data.response, 'ai');
    floatingChatHistory.push({role: 'model', text: data.response});
  } catch(e) {
    removeFloatingTyping();
    addFloatingMessage('⚠️ Sorry, I encountered a connection issue. Please try again.', 'ai');
  }
  if (sendBtn) sendBtn.disabled = false;
}

function handleFloatingKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendFloatingMessage();
  }
}

function sendFloatingSuggestion(text) {
  const input = document.getElementById('floatingChatInput');
  if (input) {
    input.value = text;
    sendFloatingMessage();
  }
}

function clearFloatingChat() {
  floatingChatHistory = [];
  const container = document.getElementById('floatingChatMessages');
  if (container) {
    container.innerHTML = '<div class="message-bubble ai"><div class="bubble">Chat cleared! 🏠 How can I help you today?</div><div class="time">' + getCurrentTime() + '</div></div>';
  }
}

