// ===== SPLIT BOOKING JS =====
let rowCount = 1;

function addServiceRow() {
  rowCount++;
  const container = document.getElementById('servicesContainer');
  const providersOptions = document.querySelector('.provider-select').innerHTML;
  const row = document.createElement('div');
  row.className = 'service-row';
  row.style.cssText = 'background:var(--bg-light);border-radius:12px;padding:1rem;margin-bottom:.75rem;border:1px solid var(--card-border);';
  const timeDefault = String(8 + rowCount).padStart(2,'0') + ':00';
  row.innerHTML = `
    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
      <div class="timeline-dot" style="width:32px;height:32px;font-size:.8rem;">${rowCount}</div>
      <span style="font-weight:600;">Service ${rowCount}</span>
    </div>
    <div class="row g-2">
      <div class="col-md-5"><select name="services[${rowCount-1}][provider_id]" class="form-control provider-select" onchange="updateAmount(this)" required>${providersOptions}</select></div>
      <div class="col-md-3"><input type="time" name="services[${rowCount-1}][time]" class="form-control" value="${timeDefault}" required></div>
      <div class="col-md-3"><input type="number" name="services[${rowCount-1}][amount]" class="form-control amount-input" placeholder="₹ Amount" required></div>
      <div class="col-md-1 d-flex align-items-center"><button type="button" onclick="removeRow(this)" style="background:rgba(239,68,68,.1);border:none;border-radius:8px;padding:.35rem .5rem;color:var(--danger);cursor:pointer;"><i class="fas fa-times"></i></button></div>
    </div>`;
  container.appendChild(row);
  updateSummary();
}

function removeRow(btn) {
  const rows = document.querySelectorAll('.service-row');
  if (rows.length <= 1) return alert('At least one service is required.');
  btn.closest('.service-row').remove();
  updateSummary();
}

function updateAmount(select) {
  const option = select.options[select.selectedIndex];
  const amount = option.dataset.amount || '';
  const row = select.closest('.service-row');
  if (row) row.querySelector('.amount-input').value = amount;
  updateSummary();
}

function updateSummary() {
  const rows = document.querySelectorAll('.service-row');
  const timeline = document.getElementById('summaryTimeline');
  let total = 0;
  let html = '';

  rows.forEach((row, i) => {
    const select = row.querySelector('.provider-select');
    const timeInput = row.querySelector('input[type=time]');
    const amountInput = row.querySelector('.amount-input');
    const providerName = select.options[select.selectedIndex]?.text || 'Select provider';
    const timeRaw = timeInput?.value || '';
    let timeFormatted = '--:--';
    if (timeRaw) {
      const [h, m] = timeRaw.split(':');
      const hourNum = parseInt(h, 10);
      const period = hourNum >= 12 ? 'PM' : 'AM';
      const hour12 = hourNum % 12 || 12;
      timeFormatted = String(hour12).padStart(2, '0') + ':' + m + ' ' + period;
    }
    const amount = parseFloat(amountInput?.value) || 0;
    total += amount;
    html += `<div class="timeline-item">
      <div class="timeline-dot">${i+1}</div>
      <div style="flex:1;"><div style="font-weight:600;font-size:.85rem;">${providerName.split('(')[0].trim()}</div><div style="font-size:.75rem;color:var(--text-secondary);"><i class="fas fa-clock me-1"></i>${timeFormatted}</div></div>
      <div style="font-weight:700;color:var(--primary);">₹${amount.toFixed(0)}</div>
    </div>`;
  });

  if (timeline) timeline.innerHTML = html || '<p style="color:var(--text-muted);font-size:.85rem;">No services added yet.</p>';
  const totalEl = document.getElementById('totalAmount');
  if (totalEl) totalEl.textContent = total.toFixed(0);
}

// Event listeners
document.addEventListener('change', e => {
  if (e.target.classList.contains('amount-input') || e.target.type === 'time') updateSummary();
});
document.addEventListener('DOMContentLoaded', updateSummary);
