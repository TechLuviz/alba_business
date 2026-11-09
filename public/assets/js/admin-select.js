/* Admin dropdowns: every single-choice <select> gets our own button + list.
   The native <select> stays in the DOM (hidden) so forms, validation and change handlers keep working.
   The list always matches the field width at any screen size / zoom, and the arrow turns up while it is open. */
(function () {
    function build(select) {
        if (select.dataset.adSelect || select.multiple || select.size > 1 || select.hasAttribute('data-no-custom')) return;
        select.dataset.adSelect = '1';
        var parent = select.parentNode, inline = select.offsetWidth && select.offsetWidth < parent.clientWidth - 4;

        var wrap = document.createElement('div');
        wrap.className = 'ad-dd' + (inline ? ' is-inline' : '');
        parent.insertBefore(wrap, select);

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'ad-dd-btn';
        btn.setAttribute('aria-haspopup', 'listbox');
        btn.setAttribute('aria-expanded', 'false');
        var txt = document.createElement('span');
        txt.className = 'ad-dd-text';
        btn.appendChild(txt);

        var list = document.createElement('ul');
        list.className = 'ad-dd-list';
        list.setAttribute('role', 'listbox');

        wrap.appendChild(btn);          // button first, so a wrapping <label> activates it
        wrap.appendChild(select);
        wrap.appendChild(list);
        select.classList.add('ad-dd-native');
        select.tabIndex = -1;
        select.setAttribute('aria-hidden', 'true');
        if (select.id) {
            var lb = document.querySelector('label[for="' + select.id + '"]');
            if (lb) { if (!lb.id) lb.id = select.id + 'Label'; btn.setAttribute('aria-labelledby', lb.id); }
        }

        var active = -1;
        function render() {
            list.innerHTML = '';
            Array.prototype.forEach.call(select.options, function (o, i) {
                var li = document.createElement('li');
                li.className = 'ad-dd-opt';
                li.setAttribute('role', 'option');
                li.dataset.index = i;
                li.textContent = o.textContent;
                if (o.disabled) li.style.opacity = '.5';
                list.appendChild(li);
            });
            sync();
        }
        function sync() {
            var o = select.options[select.selectedIndex];
            txt.textContent = o ? o.textContent : '';
            btn.disabled = select.disabled;
            Array.prototype.forEach.call(list.children, function (li, i) {
                var sel = i === select.selectedIndex;
                li.classList.toggle('is-selected', sel);
                li.setAttribute('aria-selected', sel ? 'true' : 'false');
            });
        }
        function setActive(i) {
            var items = list.children;
            if (!items.length) return;
            active = Math.max(0, Math.min(items.length - 1, i));
            Array.prototype.forEach.call(items, function (li, k) { li.classList.toggle('is-active', k === active); });
            items[active].scrollIntoView({ block: 'nearest' });
        }
        function open() {
            if (select.disabled || wrap.classList.contains('is-open')) return;
            wrap.classList.add('is-open');
            btn.setAttribute('aria-expanded', 'true');
            wrap.classList.remove('is-up');
            var r = btn.getBoundingClientRect(), need = Math.min(list.scrollHeight, 280);
            if (window.innerHeight - r.bottom < need + 12 && r.top > need + 12) wrap.classList.add('is-up');
            setActive(select.selectedIndex < 0 ? 0 : select.selectedIndex);
        }
        function close() { wrap.classList.remove('is-open'); btn.setAttribute('aria-expanded', 'false'); }
        function choose(i) {
            if (select.options[i] && select.options[i].disabled) return;
            if (select.selectedIndex !== i) {
                select.selectedIndex = i;
                select.dispatchEvent(new Event('input', { bubbles: true }));
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
            sync(); close(); btn.focus();
        }

        btn.addEventListener('click', function () { wrap.classList.contains('is-open') ? close() : open(); });
        list.addEventListener('mousedown', function (e) { e.preventDefault(); });
        list.addEventListener('click', function (e) {
            e.preventDefault(); e.stopPropagation();           // never let a wrapping <label> re-click the button
            var li = e.target.closest('.ad-dd-opt');
            if (li) choose(+li.dataset.index);
        });
        list.addEventListener('mousemove', function (e) { var li = e.target.closest('.ad-dd-opt'); if (li) setActive(+li.dataset.index); });
        btn.addEventListener('keydown', function (e) {
            var isOpen = wrap.classList.contains('is-open');
            switch (e.key) {
                case 'ArrowDown': e.preventDefault(); isOpen ? setActive(active + 1) : open(); break;
                case 'ArrowUp': e.preventDefault(); isOpen ? setActive(active - 1) : open(); break;
                case 'Home': if (isOpen) { e.preventDefault(); setActive(0); } break;
                case 'End': if (isOpen) { e.preventDefault(); setActive(list.children.length - 1); } break;
                case 'Enter': case ' ': e.preventDefault(); isOpen ? choose(active) : open(); break;
                case 'Escape': if (isOpen) { e.preventDefault(); close(); } break;
                case 'Tab': close(); break;
            }
        });
        document.addEventListener('mousedown', function (e) { if (!wrap.contains(e.target)) close(); });
        window.addEventListener('resize', close);

        select.addEventListener('change', sync);
        if (select.form) select.form.addEventListener('reset', function () { setTimeout(sync, 0); });
        var desc = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'value');
        if (desc && desc.set) {
            Object.defineProperty(select, 'value', {
                configurable: true,
                get: function () { return desc.get.call(select); },
                set: function (v) { desc.set.call(select, v); sync(); }
            });
        }
        select.focus = function () { btn.focus(); };
        render();
    }
    function init() { document.querySelectorAll('select').forEach(build); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
