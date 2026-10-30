/* Shared admin behaviour: right-side canvas, preview modal, active toggles. */
const Admin = (() => {
    const csrf = document.querySelector('meta[name=csrf-token]').content;
    const backdrop = document.getElementById('backdrop');

    function closeAll() {
        document.querySelectorAll('.offcanvas.open').forEach(c => c.classList.remove('open'));
        document.querySelectorAll('.modal.show').forEach(m => { m.classList.remove('show'); m.querySelectorAll('video').forEach(v => v.pause()); });
        backdrop.classList.remove('show');
    }

    // opts: { title, id (edit), data (field values), media (html), fileRequired (name), keepValues }
    function openForm(canvasId, opts = {}) {
        const canvas = document.getElementById(canvasId);
        const form = canvas.querySelector('form');
        const editing = !!opts.id;

        form.action = editing ? canvas.dataset.update.replace('__ID__', opts.id) : canvas.dataset.store;
        form.elements['_method'].value = editing ? 'PUT' : 'POST';
        form.elements['_id'].value = opts.id || '';
        canvas.querySelector('.canvas-title').textContent = opts.title || '';

        if (!opts.keepValues && opts.data) {
            Object.entries(opts.data).forEach(([k, v]) => {
                const el = form.elements[k];
                if (!el || el.type === 'file') return;
                if (el.type === 'checkbox') el.checked = !!v; else el.value = v ?? '';
            });
        }

        form.querySelectorAll('input[type=file]').forEach(f => { f.value = ''; f.required = (f.name === opts.fileRequired); });
        const mc = canvas.querySelector('.media-current');
        if (mc) mc.innerHTML = opts.media || '';
        canvas.querySelectorAll('.field-error').forEach(x => x.textContent = '');
        canvas.querySelectorAll('.invalid').forEach(x => x.classList.remove('invalid'));
        canvas.querySelectorAll('input[type=file]').forEach(f => f.dataset.err = '');
        refreshCounters(canvas);

        closeAll();
        canvas.classList.add('open');
        backdrop.classList.add('show');
    }

    function openView(info) {
        const media = info.type === 'text' ? '' : info.type === 'video'
            ? `<video src="${info.src}" controls autoplay muted loop></video>`
            : `<img src="${info.src}" alt="">`;
        document.getElementById('viewMedia').innerHTML = media;
        document.getElementById('viewTitle').textContent = info.title || '';
        document.getElementById('viewDesc').textContent = info.desc || '';
        closeAll();
        document.getElementById('viewModal').classList.add('show');
        backdrop.classList.add('show');
    }

    /* ---- toast: custom messages instead of browser alerts ---- */
    function toast(msg, type = 'error') {
        let box = document.getElementById('toastBox');
        if (!box) { box = document.createElement('div'); box.id = 'toastBox'; document.body.appendChild(box); }
        const t = document.createElement('div');
        t.className = 'toast ' + type;
        t.setAttribute('role', 'alert');
        t.innerHTML = '<span></span><button type="button" aria-label="Close">&times;</button>';
        t.firstChild.textContent = msg;
        t.lastChild.onclick = () => t.remove();
        box.appendChild(t);
        setTimeout(() => t.remove(), type === 'error' ? 9000 : 6000);
    }
    try { // message queued before a page reload
        const queued = sessionStorage.getItem('adminToast');
        if (queued) { sessionStorage.removeItem('adminToast'); document.addEventListener('DOMContentLoaded', () => toast(queued, 'success')); }
    } catch (e) { /* storage unavailable */ }

    const REASONS = {
        401: 'Your session has expired. Please reload the page and log in again.',
        403: 'You do not have permission to change this banner.',
        404: 'This banner no longer exists. Reload the page to see the latest list.',
        419: 'Your session has expired (security token). Please reload the page and try again.',
        429: 'Too many requests. Please wait a moment and try again.',
    };
    function post(url, checked) {
        return fetch(url, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ is_active: checked }),
        }).catch(() => { throw new Error('Cannot reach the server. Check that it is running and your connection works, then try again.'); })
          .then(async r => {
              if (r.ok) return r.json();
              let detail = '';
              try { detail = (await r.json()).message || ''; } catch (e) { /* not JSON */ }
              throw new Error(REASONS[r.status] || (r.status >= 500
                  ? 'The server hit an error while saving' + (detail ? ' (' + detail + ')' : '') + '. Please try again.'
                  : 'Could not update the status (error ' + r.status + (detail ? ': ' + detail : '') + ').'));
          });
    }
    // a change that switched other banners off needs a reload so every toggle shows the saved state
    function afterToggle(r, group) {
        if (r.reload) {
            try { if (r.message) sessionStorage.setItem('adminToast', r.message); } catch (e) { /* storage unavailable */ }
            return location.reload();
        }
        if (group) syncToggleAll(group);
    }

    function syncToggleAll(group) {
        const rows = [...document.querySelectorAll(`input[data-toggle][data-group=${group}]`)];
        const all = document.querySelector(`input[data-toggle-all][data-group=${group}]`);
        if (all) all.checked = rows.length > 0 && rows.every(r => r.checked);
    }

    document.addEventListener('click', e => {
        if (e.target.closest('[data-close]') || e.target.closest('[data-close-modal]') || e.target === backdrop) closeAll();
        const v = e.target.closest('[data-view]');
        if (v) openView(JSON.parse(v.dataset.view));
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAll(); });

    document.addEventListener('change', e => {
        const t = e.target;
        if (t.matches('input[data-toggle]')) {
            post(t.dataset.toggle, t.checked)
                .then(r => afterToggle(r, t.dataset.group))
                .catch(err => { t.checked = !t.checked; toast(err.message); });
        } else if (t.matches('input[data-toggle-all]')) {
            post(t.dataset.toggleAll, t.checked)
                .then(r => {
                    if (r.reload) return afterToggle(r);
                    document.querySelectorAll(`input[data-toggle][data-group=${t.dataset.group}]`).forEach(x => x.checked = t.checked);
                })
                .catch(err => { t.checked = !t.checked; toast(err.message); });
        }
    });


    /* ---- character counters on every field that has a maxlength ---- */
    function initCounter(el) {
        if (el.dataset.counter) return;
        el.dataset.counter = '1';
        const span = document.createElement('small');
        span.className = 'counter';
        el.closest('label').appendChild(span);
        const update = () => {
            const n = el.value.length, min = parseInt(el.getAttribute('minlength') || 0, 10), max = el.getAttribute('maxlength');
            span.textContent = n + ' / ' + max;
            span.classList.toggle('bad', n > 0 && n < min);
        };
        el.addEventListener('input', update);
        el.counterUpdate = update;
        update();
    }
    function refreshCounters(scope = document) {
        scope.querySelectorAll('input[maxlength], textarea[maxlength]').forEach(el => { initCounter(el); el.counterUpdate(); });
    }

    /* ---- file checks: size, type, emptiness and (for images) pixel dimensions ---- */
    function fileError(input, msg) {
        input.dataset.err = msg || '';
        let box = input.parentElement.querySelector('.field-error');
        if (!box) { box = document.createElement('small'); box.className = 'field-error'; input.parentElement.appendChild(box); }
        box.textContent = msg || '';
        input.classList.toggle('invalid', !!msg);
    }
    function rejectFile(input, msg) { input.value = ''; fileError(input, msg); input.dispatchEvent(new Event('rejected')); }
    document.addEventListener('change', e => {
        const input = e.target;
        if (!input.matches('input[type=file][data-max-kb]')) return;
        fileError(input, '');
        if (!input.files[0]) { if (input.required) fileError(input, 'Please choose a file.'); return; }
        const file = input.files[0], d = input.dataset;
        if (file.size === 0) return rejectFile(input, 'The file is empty.');
        if (input.name === 'video' && !/\.(mp4|webm|ogg|ogv)$/i.test(file.name)) return rejectFile(input, 'Only MP4, WebM or OGG videos are allowed.');
        if (file.size > d.maxKb * 1024) return rejectFile(input, `File is ${(file.size / 1048576).toFixed(1)} MB - the maximum is ${Math.round(d.maxKb / 1024)} MB.`);
        if (input.name === 'video') {
            // real type check: look at the file's first bytes, not just its name
            file.slice(0, 12).arrayBuffer().then(buf => {
                const b = new Uint8Array(buf), s = String.fromCharCode(...b);
                const ok = s.slice(4, 8) === 'ftyp' || s.startsWith('OggS') || (b[0] === 0x1A && b[1] === 0x45 && b[2] === 0xDF && b[3] === 0xA3);
                if (!ok && input.files[0] === file) rejectFile(input, 'This is not a real MP4, WebM or OGG video (file is corrupted or renamed).');
            });
            return;
        }
        if (!d.minW || !file.type.startsWith('image/')) return;
        const img = new Image();
        img.onload = () => {
            const w = img.naturalWidth, h = img.naturalHeight;
            if (w < d.minW || h < d.minH || w > d.maxW || h > d.maxH) {
                rejectFile(input, `Image is ${w} x ${h} px - it must be between ${d.minW} x ${d.minH} and ${d.maxW} x ${d.maxH} px.`);
            }
            URL.revokeObjectURL(img.src);
        };
        img.onerror = () => rejectFile(input, 'This image is corrupted or not a valid image.');
        img.src = URL.createObjectURL(file);
    });

    /* ---- inline validation for banner canvas forms (while typing, on blur and before submit) ---- */
    const FIELDS = '.canvas-body input[name], .canvas-body textarea[name]';
    const TEXT = '[name^=heading_], [name=description]';
    function fieldMsg(el) {
        if (el.type === 'hidden' || el.type === 'checkbox') return '';
        if (el.type === 'file') return el.files[0] ? '' : (el.dataset.err || (el.required ? 'Please choose a file.' : ''));
        const v = el.value, t = v.trim();
        const label = (el.closest('label')?.firstChild?.textContent || el.name).trim().replace(/\s*\*$/, '');
        if (el.name === 'sort_order') {
            const n = Number(t);
            return t !== '' && (!Number.isInteger(n) || n < 0 || n > Number(el.max)) ? `Enter a whole number between 0 and ${el.max}.` : '';
        }
        if (!el.matches(TEXT)) return '';
        if (v !== '' && t === '') return `${label} cannot be blank spaces only.`;
        if (/[^\x20-\x7E\r\n]/.test(v) || (el.tagName !== 'TEXTAREA' && /[\r\n]/.test(v))) return 'Emojis, special unicode and control characters are not allowed.';
        if (t === '') return el.required ? `${label} is required.` : '';
        if (el.minLength > 0 && t.length < el.minLength) return `Must be at least ${el.minLength} characters.`;
        if (el.maxLength > 0 && t.length > el.maxLength) return `Must be at most ${el.maxLength} characters.`;
        return '';
    }
    function showField(el) {
        const msg = fieldMsg(el);
        const box = el.closest('label');
        if (box) {
            let err = box.querySelector('.field-error');
            if (!err) { err = document.createElement('small'); err.className = 'field-error'; box.appendChild(err); }
            err.textContent = msg;
        }
        el.classList.toggle('invalid', !!msg);
        return msg;
    }
    document.addEventListener('input', e => { if (e.target.matches(FIELDS) && e.target.type !== 'file') showField(e.target); });
    document.addEventListener('focusout', e => {
        const el = e.target;
        if (!el.matches(FIELDS) || el.type === 'file') return;
        if (el.matches(TEXT) && el.value.trim() !== el.value && !/[^\x20-\x7E\r\n]/.test(el.value)) el.value = el.value.trim();
        showField(el);
        el.counterUpdate && el.counterUpdate();
    });
    document.addEventListener('submit', e => {
        if (!e.target.matches('.canvas-body')) return;
        let first = null;
        e.target.querySelectorAll(FIELDS).forEach(el => { if (showField(el) && !first) first = el; });
        if (first) { e.preventDefault(); first.focus(); }
    });

    /* ---- generic live validation for forms marked data-live-validate (inputs marked data-live="line") ---- */
    const LIVE_RE = /^[A-Za-z0-9 .,'&!?-]*$/;
    function liveMsg(el) {
        const v = el.value, t = v.trim();
        const label = (el.closest('label')?.firstChild?.textContent || el.name).trim().replace(/\s*\*$/, '');
        if (el.dataset.live === 'date') {
            if (!v) return el.required ? `${label} is required.` : '';
            if (!/^\d{4}-\d{2}-\d{2}$/.test(v) || isNaN(Date.parse(v))) return 'Enter a valid date.';
            const d = new Date(v + 'T00:00:00'), max = new Date(); max.setFullYear(max.getFullYear() + 1);
            if (el.min && v < el.min) return 'The date cannot be before ' + el.min + '.';
            if (d > max) return 'The date cannot be more than a year in the future.';
            return '';
        }
        const kind = el.dataset.live;
        if (kind === 'email' || kind === 'phone' || kind === 'address') {
            if (t === '') return el.required ? `${label} is required.` : '';
            if (/^\s/.test(v)) return 'Leading spaces are not allowed.';
            if (/\s{2,}/.test(v)) return 'Multiple spaces in a row are not allowed.';
            if (kind === 'email') {
                if (/\s/.test(v)) return 'The email address cannot contain spaces.';
                if (!/^[A-Za-z0-9.!#$%&'*+\/=?^_`{|}~-]+@[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?(?:\.[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?)*\.[A-Za-z]{2,}$/.test(v)) return 'Enter a valid email address, like name@example.com.';
            } else if (kind === 'phone') {
                if (!/^\+?[0-9][0-9 ()-]*[0-9]$/.test(v)) return 'Use digits only, with an optional leading + and spaces, dashes or brackets.';
                const digits = v.replace(/\D/g, '').length;
                if (digits < 7 || digits > 15) return 'A phone number has 7 to 15 digits (currently ' + digits + ').';
            } else if (!/^[A-Za-z0-9 .,'&#\/-]+$/.test(v)) {
                return "Only letters, numbers, spaces and . , ' & # / - are allowed in the address.";
            }
            if (el.minLength > 0 && t.length < el.minLength) return `Must be at least ${el.minLength} characters (currently ${t.length}).`;
            if (el.maxLength > 0 && t.length > el.maxLength) return `Must be at most ${el.maxLength} characters.`;
            return '';
        }
        if (el.dataset.live === 'url') { // https link to an allowed social platform
            if (t === '') return el.required ? `${label} is required.` : '';
            if (/\s/.test(v)) return 'The link may not contain spaces.';
            let u;
            try { u = new URL(v); } catch (e) { return 'Enter a full link starting with https://'; }
            if (u.protocol !== 'https:') return 'The link must start with https://';
            const hosts = (el.dataset.hosts || '').split(',');
            if (el.dataset.hosts && !hosts.some(h => u.hostname === h || u.hostname.endsWith('.' + h))) return 'The link must be from: ' + (el.dataset.names || hosts.join(', ')) + '.';
            if (v.length > el.maxLength) return `Must be at most ${el.maxLength} characters.`;
            return '';
        }
        if (!LIVE_RE.test(v)) return "Only letters, numbers, spaces and . , ' - & ! ? are allowed (no emojis, symbols or special characters).";
        if (/^ /.test(v)) return 'Leading spaces are not allowed.';
        if (/ {2,}/.test(v)) return 'Multiple spaces in a row are not allowed.';
        if (v !== '' && t === '') return `${label} cannot be blank spaces only.`;
        if (t === '') return el.required ? `${label} is required.` : '';
        if (el.minLength > 0 && t.length < el.minLength) return `Must be at least ${el.minLength} characters (currently ${t.length}).`;
        if (el.maxLength > 0 && t.length > el.maxLength) return `Must be at most ${el.maxLength} characters.`;
        const max = parseInt(el.dataset.wordMax, 10);
        const long = max && t.split(/\s+/).find(w => w.length > max);
        if (long) return `"${long.slice(0, 12)}${long.length > 12 ? '...' : ''}" is too long - words may be at most ${max} characters so the text wraps cleanly.`;
        return '';
    }
    function liveShow(el) {
        const msg = liveMsg(el), label = el.closest('label');
        let err = label.querySelector('.field-error');
        if (!err) {
            err = document.createElement('small'); err.className = 'field-error';
            label.insertBefore(err, label.querySelector('.counter'));
        }
        err.textContent = msg;
        el.classList.toggle('invalid', !!msg);
        return msg;
    }
    const LIVE = 'form[data-live-validate] [data-live]';
    document.addEventListener('input', e => { if (e.target.matches(LIVE)) liveShow(e.target); });
    document.addEventListener('focusout', e => {
        const el = e.target;
        if (!el.matches(LIVE)) return;
        if (LIVE_RE.test(el.value) || ['url', 'email', 'phone', 'address'].includes(el.dataset.live)) el.value = el.value.trim();
        liveShow(el);
        el.counterUpdate && el.counterUpdate();
    });
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll(LIVE).forEach(el => { if (el.value) liveShow(el); });
    });
    document.addEventListener('submit', e => {
        if (!e.target.matches('form[data-live-validate]')) return;
        let first = null;
        e.target.querySelectorAll('[data-live]').forEach(el => { if (liveShow(el) && !first) first = el; });
        e.target.querySelectorAll('input[type=file]').forEach(f => {
            if (f.required && !f.files[0] && !f.dataset.err) fileError(f, 'Please choose an image.');
            if (f.dataset.err && !first) first = f;
        });
        if (first) { e.preventDefault(); first.focus(); }
    });

    document.addEventListener('DOMContentLoaded', () => refreshCounters());

    return { openForm, openView, closeAll, refreshCounters, toast };
})();
/* ---- custom confirmation popup (replaces the browser's confirm box) for forms marked data-confirm="message" ---- */
(function () {
    let modal = null, pending = null, lastFocus = null;

    function build() {
        modal = document.createElement('div');
        modal.className = 'confirm-modal';
        modal.setAttribute('role', 'alertdialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-labelledby', 'confirmTitle');
        modal.innerHTML =
            '<div class="confirm-box">' +
                '<div class="confirm-icon" aria-hidden="true"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/></svg></div>' +
                '<h3 id="confirmTitle">Delete this item?</h3>' +
                '<p class="confirm-text"></p>' +
                '<div class="confirm-actions"><button type="button" class="btn ghost" data-no>Cancel</button><button type="button" class="btn red" data-yes>Yes, delete</button></div>' +
            '</div>';
        document.body.appendChild(modal);
        modal.addEventListener('click', e => { if (e.target === modal || e.target.closest('[data-no]')) close(); });
        modal.querySelector('[data-yes]').addEventListener('click', () => {
            const form = pending; close(true);
            if (form) { form.dataset.confirmed = '1'; form.requestSubmit ? form.requestSubmit() : form.submit(); }
        });
    }
    function open(form) {
        if (!modal) build();
        pending = form; lastFocus = document.activeElement;
        const msg = form.dataset.confirm || 'Are you sure?';
        modal.querySelector('.confirm-text').textContent = msg + ' This cannot be undone.';
        modal.querySelector('#confirmTitle').textContent = /^delete/i.test(msg) ? msg.replace(/\?$/, '') + '?' : 'Please confirm';
        modal.classList.add('show');
        modal.querySelector('[data-no]').focus();
    }
    function close(keepPending) {
        if (!modal) return;
        modal.classList.remove('show');
        if (!keepPending) pending = null;
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }

    document.addEventListener('submit', e => {
        const form = e.target;
        if (!form.matches || !form.matches('form[data-confirm]')) return;
        if (form.dataset.confirmed) { delete form.dataset.confirmed; return; }
        e.preventDefault();
        open(form);
    }, true);
    document.addEventListener('keydown', e => {
        if (!modal || !modal.classList.contains('show')) return;
        if (e.key === 'Escape') close();
        if (e.key === 'Tab') { // keep focus inside the dialog
            const btns = modal.querySelectorAll('button'), first = btns[0], last = btns[btns.length - 1];
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        }
    });
})();

/* ---- page alerts (saved / deleted / errors) fade away on their own; hover pauses, x closes ---- */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert').forEach(box => {
        const ms = box.classList.contains('success') ? 5000 : 10000;
        let timer = null;
        const close = () => { box.classList.add('is-hiding'); setTimeout(() => box.remove(), 500); };
        const start = () => { clearTimeout(timer); timer = setTimeout(close, ms); };

        const x = document.createElement('button');
        x.type = 'button'; x.className = 'alert-close'; x.setAttribute('aria-label', 'Dismiss'); x.innerHTML = '&times;';
        x.addEventListener('click', close);
        box.appendChild(x);

        box.addEventListener('mouseenter', () => clearTimeout(timer));
        box.addEventListener('mouseleave', start);
        start();
    });
});
