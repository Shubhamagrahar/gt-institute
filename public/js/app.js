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

  /* ── SIDEBAR NAV SEARCH (debounced) ─────────── */
  const sidebarSearch = document.getElementById('sidebar-search');

  if (sidebarSearch) {
    let sidebarSearchTimer;
    const sidebarEl = document.getElementById('sidebar') || document.querySelector('.gt-sidebar');

    function runSidebarSearch(q) {
      if (!sidebarEl) return;
      const navItems = sidebarEl.querySelectorAll('.gt-nav-item');
      const sections = sidebarEl.querySelectorAll('.gt-sidebar-section');

      if (!q) {
        navItems.forEach(el => el.style.display = '');
        sections.forEach(el => el.style.display = '');
        return;
      }

      navItems.forEach(el => {
        el.style.display = el.textContent.trim().toLowerCase().includes(q) ? '' : 'none';
      });

      sections.forEach(section => {
        let sibling = section.nextElementSibling;
        let hasVisible = false;
        while (sibling && !sibling.classList.contains('gt-sidebar-section')) {
          if (sibling.classList.contains('gt-nav-item') && sibling.style.display !== 'none') {
            hasVisible = true;
            break;
          }
          sibling = sibling.nextElementSibling;
        }
        section.style.display = hasVisible ? '' : 'none';
      });
    }

    sidebarSearch.addEventListener('input', function () {
      clearTimeout(sidebarSearchTimer);
      const q = this.value.trim().toLowerCase();
      sidebarSearchTimer = setTimeout(() => runSidebarSearch(q), 250);
    });

    sidebarSearch.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        this.value = '';
        clearTimeout(sidebarSearchTimer);
        runSidebarSearch('');
      }
    });
  }

});

/* ── GT DataTable — auto-pagination for all .gt-table ───────────────────
   Skip tables with data-no-dt attribute.
   Uses existing search input in the same .gt-card if present.
───────────────────────────────────────────────────────────────────────── */
(function () {
  var PER_PAGE = 10;

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.gt-table:not([data-no-dt])').forEach(gtDtInit);
  });

  function gtDtInit(table) {
    if (table._gtDt) return;
    var tbody = table.querySelector('tbody');
    if (!tbody) return;

    var card  = table.closest('.gt-card');
    var srch  = card ? (card.querySelector('input[id*="search"]') || card.querySelector('input[placeholder*="earch"]')) : null;
    var wrap  = table.closest('.gt-table-wrap') || table.parentElement;
    var pager = document.createElement('div');
    pager.className = 'gt-dt-pager';
    wrap.insertAdjacentElement('afterend', pager);

    var state = { q: '', page: 1 };
    table._gtDt = state;

    if (srch) {
      var srchTimer;
      srch.addEventListener('input', function () {
        clearTimeout(srchTimer);
        var v = this.value;
        srchTimer = setTimeout(function () {
          state.q = v.toLowerCase().trim();
          state.page = 1;
          gtDtRender(table, tbody, pager, state);
        }, 300);
      });
    }

    gtDtRender(table, tbody, pager, state);
  }

  function gtDtRows(tbody) {
    return Array.from(tbody.querySelectorAll('tr')).filter(function (r) {
      return !r.querySelector('td[colspan]');
    });
  }

  function gtDtEmpty(tbody) {
    return Array.from(tbody.querySelectorAll('tr')).find(function (r) {
      return r.querySelector('td[colspan]');
    });
  }

  function gtDtRender(table, tbody, pager, state) {
    var all      = gtDtRows(tbody);
    var filtered = state.q ? all.filter(function (r) { return r.textContent.toLowerCase().includes(state.q); }) : all;
    var total    = filtered.length;
    var pages    = Math.max(1, Math.ceil(total / PER_PAGE));
    if (state.page > pages) state.page = pages;
    var start    = (state.page - 1) * PER_PAGE;

    all.forEach(function (r) { r.style.display = 'none'; });
    filtered.slice(start, start + PER_PAGE).forEach(function (r) { r.style.display = ''; });

    var er = gtDtEmpty(tbody);
    if (er) er.style.display = total === 0 ? '' : 'none';

    if (total === 0) { pager.innerHTML = ''; return; }

    var from = start + 1;
    var to   = Math.min(start + PER_PAGE, total);
    var cp   = state.page;
    var html = '<span class="gt-dt-info">Showing ' + from + '–' + to + ' of ' + total + '</span>';

    if (pages > 1) {
      var btns = '';
      btns += gtDtBtn('‹', cp - 1, cp === 1, false);
      for (var p = 1; p <= pages; p++) {
        if (pages <= 7 || p === 1 || p === pages || (p >= cp - 1 && p <= cp + 1)) {
          btns += gtDtBtn(p, p, false, p === cp);
        } else if (p === cp - 2 || p === cp + 2) {
          btns += '<span class="gt-dt-ell">…</span>';
        }
      }
      btns += gtDtBtn('›', cp + 1, cp === pages, false);
      html += '<div class="gt-dt-btns">' + btns + '</div>';
    }

    pager.innerHTML = html;

    if (pages > 1) {
      var tRef = table, bRef = tbody, pRef = pager, sRef = state;
      pager.querySelectorAll('.gt-dt-b').forEach(function (b) {
        b.addEventListener('click', function () {
          sRef.page = parseInt(this.dataset.p, 10);
          gtDtRender(tRef, bRef, pRef, sRef);
        });
      });
    }
  }

  function gtDtBtn(label, p, disabled, active) {
    return '<button class="gt-dt-b' + (active ? ' gt-dt-b-on' : '') +
           '" data-p="' + p + '"' + (disabled ? ' disabled' : '') + '>' + label + '</button>';
  }

  /* ── Strip leading zeros from number inputs ──────────────────────── */
  document.addEventListener('input', function (e) {
    var el = e.target;
    if (el.type === 'number' && el.value !== '' && el.value !== '-') {
      var n = parseFloat(el.value);
      if (!isNaN(n) && el.value !== String(n) && el.value !== n.toFixed(el.step && el.step < 1 ? 2 : 0)) {
        el.value = n;
      }
    }
  }, true);

}());
