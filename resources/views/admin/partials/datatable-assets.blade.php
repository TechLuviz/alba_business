{{-- Shared admin data-table behaviour: select-all / bulk-delete bar + toolbar, sorting and pager styles --}}
<script>
(function () {
    var all = document.getElementById('checkAll'), bar = document.getElementById('bulkForm'),
        label = document.getElementById('bulkCount'), rows = [].slice.call(document.querySelectorAll('.row-check'));
    if (!all || !bar) return;
    var noun = bar.dataset.noun || 'item', nouns = bar.dataset.nouns || 'items';
    function sync() {
        var n = rows.filter(function (r) { return r.checked; }).length;
        bar.hidden = n === 0;
        label.textContent = n + ' selected';
        bar.dataset.confirm = 'Delete ' + n + ' selected ' + (n === 1 ? noun : nouns) + '?';
        all.checked = n > 0 && n === rows.length;
        all.indeterminate = n > 0 && n < rows.length;
        rows.forEach(function (r) { r.closest('tr').classList.toggle('is-selected', r.checked); });
    }
    all.addEventListener('change', function () { rows.forEach(function (r) { r.checked = all.checked; }); sync(); });
    rows.forEach(function (r) { r.addEventListener('change', sync); });
    window.addEventListener('pageshow', sync); // restore state after back/forward navigation
    sync();
})();
</script>

<style>
    .dt-bulk { display: flex; align-items: center; gap: 12px; padding: 10px 22px; background: #fff5f3; border-bottom: 1px solid var(--line); font-weight: 600; }
    .dt-bulk[hidden] { display: none; }
    .tbl tr.is-selected td { background: #fff8f6; }
    .tbl input[type=checkbox] { width: 16px; height: 16px; cursor: pointer; accent-color: var(--red); }
    .dt-bar { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; padding: 14px 22px; border-bottom: 1px solid var(--line); }
    .dt-len { display: flex; flex-direction: row; align-items: center; gap: 8px; font-weight: 500; }
    .dt-len select { width: auto; padding: 6px 28px 6px 10px; }
    .dt-search { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .dt-search input[type=search] { width: 280px; max-width: 100%; padding: 8px 12px; border: 1px solid #cbd3df; border-radius: 6px; font: inherit; }
    .dt-clear { font-size: .85rem; color: var(--red); }
    .dt-sort { color: inherit; text-decoration: none; white-space: nowrap; }
    .dt-sort:hover { color: var(--red); }
    .dt-foot { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; padding: 14px 22px; border-top: 1px solid var(--line); }
    .dt-info { color: var(--muted); font-size: .85rem; }
    .dt-pager { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
    .dt-pager a, .dt-pager span { min-width: 34px; height: 34px; padding: 0 10px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--line); border-radius: 6px; background: #fff; color: var(--ink); text-decoration: none; font-size: .85rem; }
    .dt-pager a:hover { border-color: var(--red); color: var(--red); }
    .dt-pager .cur { background: var(--red); border-color: var(--red); color: #fff; font-weight: 600; }
    .dt-pager .dis { color: #a0a9b8; background: #f4f6fa; }
    .dt-pager .gap { border: 0; background: none; min-width: 0; padding: 0 2px; }
</style>
