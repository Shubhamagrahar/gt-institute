/* GT Institute - Main JS */

document.addEventListener('DOMContentLoaded', function () {

  /* ── SIDEBAR TOGGLE (mobile) ─────────────────── */
  const sidebar  = document.querySelector('.gt-sidebar');
  const overlay  = document.querySelector('.gt-overlay');
  const hamburger = document.querySelector('.gt-hamburger');

  function openSidebar()  { sidebar?.classList.add('open'); overlay?.classList.add('open'); }
  function closeSidebar() { sidebar?.classList.remove('open'); overlay?.classList.remove('open'); }

  hamburger?.addEventListener('click', openSidebar);
  overlay?.addEventListener('click', closeSidebar);

  /* ── ACTIVE NAV LINK ─────────────────────────── */
  const currentPath = window.location.pathname;
  document.querySelectorAll('.gt-nav-item').forEach(link => {
    const href = link.getAttribute('href');
    if (href && currentPath.startsWith(href) && href !== '/') {
      link.classList.add('active');
    }
  });

  /* ── AUTO-DISMISS ALERTS ─────────────────────── */
  document.querySelectorAll('.gt-alert[data-auto-close]').forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity .5s';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    }, 4000);
  });

  /* ── PLAN PRICE + ADDON CALCULATOR ──────────────
     Used on: owner/institutes/create
  ────────────────────────────────────────────── */
  const planSelect       = document.getElementById('plan_id');
  const addonBoxes       = document.querySelectorAll('.addon-feature-check');
  const discountTypeEl   = document.getElementById('discount_type');
  const discountPercentEl = document.getElementById('discount_percent_input');
  const discountRupeeEl  = document.getElementById('discount_rupee_input');
  const planPriceEl      = document.getElementById('plan_price_display');
  const addonTotalEl     = document.getElementById('addon_total_display');
  const subtotalEl       = document.getElementById('subtotal_display');
  const discountAmtEl    = document.getElementById('discount_amt_display');
  const finalAmtEl       = document.getElementById('final_amt_display');
  const discountValInput = document.getElementById('discount_value');

  if (planSelect) {
    let planPrices = {};
    try { planPrices = JSON.parse(document.getElementById('plan-prices-data')?.textContent || '{}'); } catch(e){}

    function recalculate() {
      const planId = planSelect.value;
      const planPrice = parseFloat(planPrices[planId] || 0);

      let addonTotal = 0;
      addonBoxes.forEach(box => {
        if (box.checked) addonTotal += parseFloat(box.dataset.price || 0);
      });

      const subtotal = planPrice + addonTotal;
      const discType = discountTypeEl?.value || 'NONE';
      let discVal   = parseFloat(discountValInput?.value || 0);
      let discAmt   = 0;

      if (discType === 'PERCENT') {
        discAmt = Math.round(subtotal * discVal / 100 * 100) / 100;
        if (discountRupeeEl) discountRupeeEl.value = discAmt.toFixed(2);
      } else if (discType === 'FLAT') {
        discAmt = discVal;
        if (discountPercentEl && subtotal > 0) {
          discountPercentEl.value = Math.round(discAmt / subtotal * 10000) / 100;
        }
      }

      const finalAmt = subtotal - discAmt;

      if (planPriceEl)  planPriceEl.textContent  = '₹' + planPrice.toLocaleString('en-IN');
      if (addonTotalEl) addonTotalEl.textContent = '₹' + addonTotal.toLocaleString('en-IN');
      if (subtotalEl)   subtotalEl.textContent   = '₹' + subtotal.toLocaleString('en-IN');
      if (discountAmtEl) discountAmtEl.textContent = '₹' + discAmt.toLocaleString('en-IN', {minimumFractionDigits:2});
      if (finalAmtEl)   finalAmtEl.textContent   = '₹' + finalAmt.toLocaleString('en-IN', {minimumFractionDigits:2});
    }

    // Plan features reveal
    planSelect.addEventListener('change', function () {
      const planId = this.value;
      document.querySelectorAll('.plan-features-block').forEach(b => b.classList.add('d-none'));
      document.querySelector(`.plan-features-block[data-plan="${planId}"]`)?.classList.remove('d-none');
      recalculate();
    });

    addonBoxes.forEach(box => {
      box.addEventListener('change', function () {
        this.closest('.gt-check')?.classList.toggle('checked', this.checked);
        recalculate();
      });
    });

    discountTypeEl?.addEventListener('change', () => {
      discountValInput.value = '';
      if (discountPercentEl) discountPercentEl.value = '';
      if (discountRupeeEl)   discountRupeeEl.value   = '';
      const showDiscount = discountTypeEl.value !== 'NONE';
      document.getElementById('discount-fields')?.classList.toggle('d-none', !showDiscount);
      recalculate();
    });

    discountPercentEl?.addEventListener('input', function () {
      discountValInput.value = this.value;
      recalculate();
    });

    discountRupeeEl?.addEventListener('input', function () {
      discountValInput.value = this.value;
      recalculate();
    });

    recalculate();
  }

  /* ── CONFIRM DELETE ──────────────────────────── */
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function (e) {
      if (!confirm(this.dataset.confirm || 'Are you sure?')) {
        e.preventDefault();
      }
    });
  });

  /* ── ATTENDANCE CHECKBOXES ──────────────────── */
  const markAllPresent = document.getElementById('mark-all-present');
  const markAllAbsent  = document.getElementById('mark-all-absent');

  markAllPresent?.addEventListener('click', () => {
    document.querySelectorAll('.att-radio-p').forEach(r => r.checked = true);
  });
  markAllAbsent?.addEventListener('click', () => {
    document.querySelectorAll('.att-radio-a').forEach(r => r.checked = true);
  });

  /* ── SEARCH FILTER TABLE ─────────────────────── */
  const searchInput = document.getElementById('table-search');
  const tableRows   = document.querySelectorAll('.gt-table tbody tr');

  searchInput?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    tableRows.forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });

});
