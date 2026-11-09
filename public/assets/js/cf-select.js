/* Custom dropdown for `.cf-field select`.
   The native <select> stays in the DOM (hidden) so form.elements.course, validation and submit keep working;
   the visible list is our own element, so it always matches the field width at any zoom / screen size. */
(function () {
    function build(select) {
        if (select.dataset.cfSelect) return;
        select.dataset.cfSelect = '1';

        var wrap = document.createElement('div');
        wrap.className = 'cf-dd';
        select.parentNode.insertBefore(wrap, select);
        wrap.appendChild(select);
        select.classList.add('cf-dd-native');
        select.tabIndex = -1;
        select.setAttribute('aria-hidden', 'true');

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'cf-dd-btn';
        btn.setAttribute('aria-haspopup', 'listbox');
        btn.setAttribute('aria-expanded', 'false');
        var label = select.id && document.querySelector('label[for="' + select.id + '"]');
        if (label) {
            if (!label.id) label.id = select.id + 'Label';
            btn.setAttribute('aria-labelledby', label.id);
            label.addEventListener('click', function (e) { e.preventDefault(); btn.focus(); });
        }

        var txt = document.createElement('span');
        txt.className = 'cf-dd-text';
        btn.appendChild(txt);

        var list = document.createElement('ul');
        list.className = 'cf-dd-list';
        list.setAttribute('role', 'listbox');
        list.tabIndex = -1;

        wrap.appendChild(btn);
        wrap.appendChild(list);

        var active = -1;

        function render() {
            list.innerHTML = '';
            Array.prototype.forEach.call(select.options, function (o, i) {
                var li = document.createElement('li');
                li.className = 'cf-dd-opt' + (o.value === '' ? ' is-placeholder' : '');
                li.setAttribute('role', 'option');
                li.dataset.index = i;
                li.textContent = o.textContent;
                list.appendChild(li);
            });
            sync();
        }

        function sync() {
            var o = select.options[select.selectedIndex];
            txt.textContent = o ? o.textContent : '';
            btn.classList.toggle('is-placeholder', !o || o.value === '');
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
            if (wrap.classList.contains('is-open')) return;
            wrap.classList.add('is-open');
            btn.setAttribute('aria-expanded', 'true');
            // Flip upwards if there is no room below.
            wrap.classList.remove('is-up');
            var r = btn.getBoundingClientRect();
            var need = Math.min(list.scrollHeight, 280);
            if (window.innerHeight - r.bottom < need + 12 && r.top > need + 12) wrap.classList.add('is-up');
            setActive(select.selectedIndex < 0 ? 0 : select.selectedIndex);
        }

        function close() {
            wrap.classList.remove('is-open');
            btn.setAttribute('aria-expanded', 'false');
        }

        function choose(i) {
            if (select.selectedIndex !== i) {
                select.selectedIndex = i;
                select.dispatchEvent(new Event('input', { bubbles: true }));
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
            sync();
            close();
            btn.focus();
        }

        btn.addEventListener('click', function () { wrap.classList.contains('is-open') ? close() : open(); });

        list.addEventListener('mousedown', function (e) { e.preventDefault(); });
        list.addEventListener('click', function (e) {
            var li = e.target.closest('.cf-dd-opt');
            if (li) choose(+li.dataset.index);
        });
        list.addEventListener('mousemove', function (e) {
            var li = e.target.closest('.cf-dd-opt');
            if (li) setActive(+li.dataset.index);
        });

        btn.addEventListener('keydown', function (e) {
            var isOpen = wrap.classList.contains('is-open');
            switch (e.key) {
                case 'ArrowDown': e.preventDefault(); isOpen ? setActive(active + 1) : open(); break;
                case 'ArrowUp': e.preventDefault(); isOpen ? setActive(active - 1) : open(); break;
                case 'Home': if (isOpen) { e.preventDefault(); setActive(0); } break;
                case 'End': if (isOpen) { e.preventDefault(); setActive(list.children.length - 1); } break;
                case 'Enter':
                case ' ':
                    e.preventDefault();
                    isOpen ? choose(active) : open();
                    break;
                case 'Escape': if (isOpen) { e.preventDefault(); close(); } break;
                case 'Tab': close(); break;
            }
        });

        document.addEventListener('mousedown', function (e) { if (!wrap.contains(e.target)) close(); });
        window.addEventListener('resize', close);

        // Keep in sync with programmatic changes (select.value = ..., form.reset()).
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

        // Validation code calls select.focus(): send focus to the visible button.
        select.focus = function () { btn.focus(); };

        render();
    }

    function init() { document.querySelectorAll('.cf-field select').forEach(build); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
