// ===== HOME SERVICE HUB - MAIN JS =====

// Animate counters on scroll
function initCounters() {
  const counters = document.querySelectorAll('[data-count]');
  if (!counters.length) return;
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const target = parseInt(el.dataset.count);
        let count = 0;
        const step = Math.ceil(target / 60);
        const timer = setInterval(() => {
          count = Math.min(count + step, target);
          el.textContent = count.toLocaleString() + '+';
          if (count >= target) clearInterval(timer);
        }, 30);
        observer.unobserve(el);
      }
    });
  });
  counters.forEach(el => observer.observe(el));
}

// Add staggered animation to cards
function initAnimations() {
  const cards = document.querySelectorAll('.provider-card, .stat-card, .card-custom');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        entry.target.style.animationDelay = (i * 0.05) + 's';
        entry.target.classList.add('animate-fade-up');
        observer.unobserve(entry.target);
      }
    });
  }, {threshold: 0.1});
  cards.forEach(c => observer.observe(c));
}

// Flash message auto-dismiss
function initAlerts() {
  document.querySelectorAll('.alert-custom').forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity .5s';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    }, 4000);
  });
}

document.addEventListener('DOMContentLoaded', () => {
  initCounters();
  initAnimations();
  initAlerts();
});
