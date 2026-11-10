@php $f = config('banner.contact_form'); @endphp
@if(isset($contactInfo) && $contactInfo->is_active)
    <section class="contact-section" id="get-in-touch">
        <div class="container contact-grid">
            <div class="contact-copy">
                @php
                    // the last word of the accent line gets a butterfly flying above it
                    $accentWords = preg_split('/\s+/', trim((string) $contactInfo->heading_accent));
                    $accentLast = array_pop($accentWords);
                @endphp
                <h2><span>{{ $contactInfo->heading }}</span><em>@if($accentWords){{ implode(' ', $accentWords) }} @endif<span class="bf-word">{{ $accentLast }}<svg class="butterfly" viewBox="0 0 64 48" aria-hidden="true" focusable="false"><g class="bf-body"><g class="bf-wing bf-left"><path d="M32 22C22 4 6 2 4 12c-2 9 8 14 16 13-8 2-13 8-10 14 3 6 14 3 22-12z" fill="#c8914a"/><path d="M32 24c-8 2-14 8-12 14" fill="none" stroke="#fff" stroke-opacity=".5" stroke-width="1.2"/></g><g class="bf-wing bf-right"><path d="M32 22C42 4 58 2 60 12c2 9-8 14-16 13 8 2 13 8 10 14-3 6-14 3-22-12z" fill="#e2b273"/><path d="M32 24c8 2 14 8 12 14" fill="none" stroke="#fff" stroke-opacity=".5" stroke-width="1.2"/></g><rect x="30.5" y="16" width="3" height="22" rx="1.5" fill="#3a2c1a"/><path d="M31 16c-2-6-5-8-7-9M33 16c2-6 5-8 7-9" fill="none" stroke="#3a2c1a" stroke-width="1.2" stroke-linecap="round"/></g></svg></span></em></h2>
                @if($contactInfo->description)<p class="contact-desc">{{ $contactInfo->description }}</p>@endif

                <div class="contact-cards">
                    @if($contactInfo->email)
                        <a class="contact-card" draggable="false" href="mailto:{{ $contactInfo->email }}">
                            <span class="contact-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg></span>
                            <span class="contact-txt"><small>Email us</small><strong>{{ $contactInfo->email }}</strong></span>
                            <span class="contact-go" aria-hidden="true">&nearr;</span>
                        </a>
                    @endif
                    @if($contactInfo->phone)
                        <a class="contact-card" draggable="false" href="tel:{{ preg_replace('/[^0-9+]/', '', $contactInfo->phone) }}">
                            <span class="contact-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg></span>
                            <span class="contact-txt"><small>Call us</small><strong>{{ $contactInfo->phone }}</strong></span>
                            <span class="contact-go" aria-hidden="true">&nearr;</span>
                        </a>
                    @endif
                    @if($contactInfo->address)
                        <a class="contact-card" draggable="false" href="https://maps.app.goo.gl/9M3KPnybNWkFCRXp7" target="_blank" rel="noopener noreferrer">
                            <span class="contact-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.800 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg></span>
                            <span class="contact-txt"><small>Our location</small><strong class="is-address">{{ $contactInfo->address }}</strong></span>
                            <span class="contact-go" aria-hidden="true">&nearr;</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="contact-form-wrap">
                <form class="contact-form" id="contactForm" action="{{ route('contact.submit') }}" method="POST" novalidate>
                    @csrf
                    <input type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true" class="nl-hp">

                    <div class="cf-field">
                        <label for="cfName">Name <b>*</b></label>
                        <input type="text" id="cfName" name="name" placeholder="Your full name" minlength="{{ $f['name_min'] }}" maxlength="{{ $f['name_max'] }}" autocomplete="name" required>
                        <p class="cf-err" data-for="name" role="alert"></p>
                    </div>
                    <div class="cf-field">
                        <label for="cfEmail">Email ID <b>*</b></label>
                        <input type="email" id="cfEmail" name="email" placeholder="name@example.com" maxlength="{{ $f['email_max'] }}" autocomplete="email" inputmode="email" required>
                        <p class="cf-err" data-for="email" role="alert"></p>
                    </div>
                    <div class="cf-field">
                        <label for="cfPhone">Phone number <b>*</b></label>
                        <input type="tel" id="cfPhone" name="phone" placeholder="Enter your phone number" maxlength="{{ $f['phone_max'] }}" autocomplete="tel" inputmode="tel" required>
                        <p class="cf-err" data-for="phone" role="alert"></p>
                    </div>
                    <div class="cf-field">
                        <label for="cfCourse">Interested course <b>*</b></label>
                        <select id="cfCourse" name="course" required>
                            <option value="">Select a course</option>
                            @foreach($courses as $title)
                                <option value="{{ $title }}">{{ $title }}</option>
                            @endforeach
                            <option value="{{ $f['other_course'] }}">{{ $f['other_course'] }}</option>
                        </select>
                        <p class="cf-err" data-for="course" role="alert"></p>
                    </div>
                    <div class="cf-field">
                        <label for="cfMessage">Message <b>*</b></label>
                        <textarea id="cfMessage" name="message" rows="4" placeholder="Write your message or inquiry here..." minlength="{{ $f['message_min'] }}" maxlength="{{ $f['message_max'] }}" required></textarea>
                        <div class="cf-foot"><p class="cf-err" data-for="message" role="alert"></p><span class="cf-count" id="cfCount">0 / {{ $f['message_max'] }}</span></div>
                    </div>

                    <button type="submit" class="cf-submit"><span>Send message</span><i aria-hidden="true">&rarr;</i></button>
                    <p class="cf-status" id="cfStatus" role="status" aria-live="polite"></p>
                    {{-- Static note: change the text here in the code --}}
                    <p class="cf-note">By submitting this form, you agree to our confidential institutional charter and global candidate privacy policies.</p>
                </form>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        // selecting text inside a contact card (to copy it) must not also open the mail app / dialer / map
        document.querySelectorAll('.contact-card').forEach(function (card) {
            card.addEventListener('click', function (e) {
                const sel = window.getSelection();
                if (sel && !sel.isCollapsed && card.contains(sel.anchorNode)) e.preventDefault();
            });
        });

        // mailto: does nothing when no mail app is registered. If the page never loses focus after the click
        // (i.e. no mail app opened), fall back to a web compose window addressed to the same email.
        document.querySelectorAll('.contact-card[href^="mailto:"]').forEach(function (card) {
            card.addEventListener('click', function (e) {
                if (e.defaultPrevented) return;
                const to = card.getAttribute('href').replace(/^mailto:/i, '');
                let handled = false;
                const mark = function () { handled = true; };
                window.addEventListener('blur', mark, { once: true });
                document.addEventListener('visibilitychange', mark, { once: true });
                setTimeout(function () {
                    window.removeEventListener('blur', mark);
                    document.removeEventListener('visibilitychange', mark);
                    if (!handled) window.open('https://mail.google.com/mail/?view=cm&fs=1&to=' + encodeURIComponent(to), '_blank', 'noopener');
                }, 1500);
            });
        });
    </script>
    <script>
        (function () {
            const form = document.getElementById('contactForm');
            const L = { nameMin: {{ $f['name_min'] }}, nameMax: {{ $f['name_max'] }}, msgMin: {{ $f['message_min'] }}, msgMax: {{ $f['message_max'] }} };
            const fields = ['name', 'email', 'phone', 'course', 'message'].map(n => form.elements[n]);
            let statusTimer;
            const status = document.getElementById('cfStatus'), count = document.getElementById('cfCount');
            const EMAIL = /^[A-Za-z0-9.!#$%&'*+\/=?^_`{|}~-]+@[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?(?:\.[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?)*\.[A-Za-z]{2,}$/;
            // Incomplete / mistyped endings (e.g. name@site.co instead of .com) are rejected.
            const BAD_TLD = /\.(co|cm|con|cmo|ocm|vom|xom|cpm|comm|coom|nte|ogr|ne|or)$/i;

            const MSG_SYMBOLS = /[^A-Za-z0-9\s.,'"?!&():;\/-]/, SYMBOL_MSG = 'Symbols like # @ % are not allowed.';
            const rules = {
                name(v) {
                    v = v.replace(/\s+/g, ' ').trim();
                    if (!v) return 'Please enter your name.';
                    if (!/^[A-Za-z][A-Za-z .'-]*$/.test(v)) return "Use letters, spaces and . ' - only (no numbers, emojis or symbols).";
                    if (v.length < L.nameMin) return 'The name must be at least ' + L.nameMin + ' characters.';
                    if (v.length > L.nameMax) return 'The name can be at most ' + L.nameMax + ' characters.';
                    return '';
                },
                email(v) {
                    v = v.trim();
                    if (!v) return 'Please enter your email address.';
                    if (/\s/.test(v)) return 'The email address cannot contain spaces.';
                    if (!v.includes('@')) return 'The email address must contain an @.';
                    if (!EMAIL.test(v) || BAD_TLD.test(v)) return 'Enter a valid email address with a complete domain, like name@example.com.';
                    return '';
                },
                phone(v) {
                    v = v.trim();
                    if (!v) return 'Please enter your phone number.';
                    if (!/^\+?[0-9][0-9 ()-]*[0-9]$/.test(v)) return 'Use digits only, with an optional leading + and spaces, dashes or brackets.';
                    const d = v.replace(/\D/g, '').length;
                    if (d < 7 || d > 15) return 'A phone number has 7 to 15 digits (you entered ' + d + ').';
                    return '';
                },
                course(v) { return v ? '' : 'Please choose the course you are interested in.'; },
                message(v) {
                    const t = v.trim();
                    if (!t) return 'Please write your message.';
                    if (/[^\x20-\x7E\r\n]/.test(v)) return 'Emojis are not allowed.';
                    if (MSG_SYMBOLS.test(v)) return SYMBOL_MSG;
                    if (t.length < L.msgMin) return 'The message must be at least ' + L.msgMin + ' characters (currently ' + t.length + ').';
                    if (t.length > L.msgMax) return 'The message can be at most ' + L.msgMax + ' characters.';
                    if (t.split(/\s+/).some(w => w.length > 40)) return 'A single word is too long - please add spaces.';
                    return '';
                },
            };
            const errTimers = {};
            const ERR_MS = 4000; // validation warnings fade away after a few seconds
            function show(el, msg) {
                const out = form.querySelector('.cf-err[data-for="' + el.name + '"]');
                out.textContent = msg;
                el.closest('.cf-field').classList.toggle('is-bad', !!msg);
                clearTimeout(errTimers[el.name]);
                if (msg) errTimers[el.name] = setTimeout(() => {
                    out.textContent = '';
                    el.closest('.cf-field').classList.remove('is-bad');
                }, ERR_MS);
                return msg;
            }
            const check = el => show(el, rules[el.name](el.value));

            // characters that can never be entered in each field: removed as typed or pasted
            const BLOCK = {
                name:    { re: /[^A-Za-z .'-]/g, msg: "Only letters, spaces and . ' - are allowed in the name." },
                email:   { re: /[^A-Za-z0-9.!#$%&'*+\/=?^_`{|}~@-]/g, msg: 'Spaces and unsupported characters are not allowed in an email address.' },
                phone:   { re: /[^0-9+ ()-]/g, msg: 'Only digits, +, spaces, dashes and brackets are allowed in a phone number.' },
                message: { re: /[^A-Za-z0-9\s.,'"?!&():;\/-]/g, msg: v => /[^\x20-\x7E\r\n]/.test(v) ? 'Emojis are not allowed.' : SYMBOL_MSG },
            };
            function sanitize(el) {
                const b = BLOCK[el.name];
                if (!b) return null;
                let v = el.value, pos = el.selectionStart, bad = false;
                const clean = s => { const r = s.replace(b.re, ''); if (r !== s) bad = true; return r; };
                const before = clean(v.slice(0, pos)), after = clean(v.slice(pos));
                let out = before + after, cur = before.length;
                if (el.name === 'name') {
                    const t = out.replace(/ {2,}/g, ' ').replace(/^[ .'-]+/, '');
                    if (t !== out) { bad = true; cur = Math.max(0, cur - (out.length - t.length)); out = t; }
                }
                if (el.name === 'phone') {
                    const t = out.replace(/(?!^)\+/g, '').replace(/^ +/, '').replace(/ {2,}/g, ' ');
                    if (t !== out) { bad = true; cur = Math.max(0, cur - (out.length - t.length)); out = t; }
                }
                if (bad) { el.value = out; try { el.setSelectionRange(cur, cur); } catch (x) {} return typeof b.msg === 'function' ? b.msg(v) : b.msg; }
                return null;
            }
            fields.forEach(el => {
                el.addEventListener('input', () => {
                    // Phone: digits only (plus one leading +). Letters and symbols are dropped as they are typed or pasted.
                    if (el.name === 'phone') {
                        const clean = el.value.replace(/[^\d+]/g, '').replace(/(?!^)\+/g, '');
                        if (clean !== el.value) el.value = clean;
                    }
                    if (el.tagName !== 'SELECT') {
                        const blocked = sanitize(el);
                        if (el.name === 'message') count.textContent = el.value.length + ' / ' + L.msgMax;
                        if (blocked) { el.dataset.touched = '1'; show(el, blocked); return; }
                    }
                    if (el.dataset.touched) check(el);
                });
                el.addEventListener('blur', () => {
                    el.dataset.touched = '1';
                    if (el.tagName === 'INPUT') el.value = el.name === 'name' ? el.value.replace(/\s+/g, ' ').trim() : el.value.trim();
                    check(el);
                });
                el.addEventListener('change', () => { el.dataset.touched = '1'; check(el); });
            });

            form.addEventListener('submit', async e => {
                e.preventDefault();
                clearTimeout(statusTimer);
                status.className = 'cf-status'; status.textContent = '';
                let first = null;
                fields.forEach(el => { el.dataset.touched = '1'; if (check(el) && !first) first = el; });
                if (first) { first.focus(); return; }

                const btn = form.querySelector('.cf-submit'); btn.disabled = true; btn.classList.add('is-busy');
                try {
                    const r = await fetch(form.action, { method: 'POST', headers: { 'Accept': 'application/json' }, body: new FormData(form) });
                    const j = await r.json().catch(() => ({}));
                    if (r.ok) {
                        form.reset(); count.textContent = '0 / ' + L.msgMax;
                        fields.forEach(el => { delete el.dataset.touched; show(el, ''); });
                        status.className = 'cf-status ok'; status.textContent = j.message || 'Thank you! We will be in touch soon.';
                        statusTimer = setTimeout(() => { status.className = 'cf-status'; status.textContent = ''; }, 5000);
                    } else if (r.status === 422 && j.errors) {
                        fields.forEach(el => { if (j.errors[el.name]) { el.dataset.touched = '1'; show(el, j.errors[el.name][0]); } });
                        const bad = fields.find(el => j.errors[el.name]); if (bad) bad.focus();
                    } else if (r.status === 429) {
                        status.className = 'cf-status bad'; status.textContent = 'Too many messages sent. Please wait a few minutes and try again.';
                    } else if (r.status !== 422) {
                        status.className = 'cf-status bad'; status.textContent = 'Something went wrong. Please try again later.';
                    }
                } catch (x) {
                    status.className = 'cf-status bad'; status.textContent = 'Cannot reach the server. Check your connection and try again.';
                }
                if (status.classList.contains('bad')) statusTimer = setTimeout(() => { status.className = 'cf-status'; status.textContent = ''; }, 6000);
                btn.disabled = false; btn.classList.remove('is-busy');
            });
        })();
    </script>
    @endpush
@endif
