// ===== HOME SERVICE HUB — SHARED UTILITIES =====

/**
 * Toggle password field visibility.
 * @param {string} inputId  - id of the <input type="password">
 * @param {HTMLElement} icon - the eye icon element
 */
function togglePasswordVisibility(inputId, icon) {
  const input = document.getElementById(inputId);
  if (!input) return;
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}
