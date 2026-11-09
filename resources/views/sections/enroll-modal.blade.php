    @php($ef = config('banner.contact_form'))
    <div class="enroll-modal" id="enrollModal" role="dialog" aria-modal="true" aria-labelledby="enrollTitle" hidden>
        <div class="enroll-box">
            <button type="button" class="enroll-close" aria-label="Close" data-enroll-close>&times;</button>
            <h3 id="enrollTitle">Enroll in a program</h3>
            <form class="contact-form" id="enrollForm" action="{{ route('contact.submit') }}" method="POST" novalidate>
                @csrf
                <input type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true" class="nl-hp">
                <div class="cf-field">
                    <label for="enName">Name <b>*</b></label>
                    <input type="text" id="enName" name="name" placeholder="Your full name" minlength="{{ $ef['name_min'] }}" maxlength="{{ $ef['name_max'] }}" autocomplete="name" required>
                    <p class="cf-err" data-for="name" role="alert"></p>
                </div>
                <div class="cf-field">
                    <label for="enEmail">Email ID <b>*</b></label>
                    <input type="email" id="enEmail" name="email" placeholder="name@example.com" maxlength="{{ $ef['email_max'] }}" autocomplete="email" inputmode="email" required>
                    <p class="cf-err" data-for="email" role="alert"></p>
                </div>
                <div class="cf-field">
                    <label for="enPhone">Phone number <b>*</b></label>
                    <input type="tel" id="enPhone" name="phone" placeholder="Enter your phone number" maxlength="{{ $ef['phone_max'] }}" autocomplete="tel" inputmode="tel" required>
                    <p class="cf-err" data-for="phone" role="alert"></p>
                </div>
                <div class="cf-field">
                    <label for="enCourse">Interested course <b>*</b></label>
                    <select id="enCourse" name="course" required>
                        <option value="">Select a course</option>
                        @foreach($programCards as $pc)<option value="{{ $pc->title }}">{{ $pc->title }}</option>@endforeach
                        <option value="{{ $ef['other_course'] }}">{{ $ef['other_course'] }}</option>
                    </select>
                    <p class="cf-err" data-for="course" role="alert"></p>
                </div>
                <div class="cf-field">
                    <label for="enMessage">Message <b>*</b></label>
                    <textarea id="enMessage" name="message" rows="4" placeholder="Write your message or inquiry here..." minlength="{{ $ef['message_min'] }}" maxlength="{{ $ef['message_max'] }}" required></textarea>
                    <div class="cf-foot"><p class="cf-err" data-for="message" role="alert"></p><span class="cf-count" id="enCount">0 / {{ $ef['message_max'] }}</span></div>
                </div>
                <button type="submit" class="cf-submit"><span>Send message</span><i aria-hidden="true">&rarr;</i></button>
                <p class="cf-status" id="enStatus" role="status" aria-live="polite"></p>
            </form>
        </div>
    </div>


    @push('scripts')
    <script>
        (function () {
            const modal = document.getElementById('enrollModal'), form = document.getElementById('enrollForm');
            const L = { nameMin: {{ $ef['name_min'] }}, nameMax: {{ $ef['name_max'] }}, msgMin: {{ $ef['message_min'] }}, msgMax: {{ $ef['message_max'] }} };
            const fields = ['name', 'email', 'phone', 'course', 'message'].map(n => form.elements[n]);
            let statusTimer;
            const status = document.getElementById('enStatus'), count = document.getElementById('enCount');
            const EMAIL = /^[A-Za-z0-9.!#$%&'*+\/=?^_`{|}~-]+@[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?(?:\.[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?)*\.[A-Za-z]{2,}$/;
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
                    if (!EMAIL.test(v)) return 'Enter a valid email address, like name@example.com.';
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
                    if (/[^\x20-\x7E\r\n]/.test(v)) return 'Use plain text only - emojis and special symbols are not allowed.';
                    if (t.length < L.msgMin) return 'The message must be at least ' + L.msgMin + ' characters (currently ' + t.length + ').';
                    if (t.length > L.msgMax) return 'The message can be at most ' + L.msgMax + ' characters.';
                    if (t.split(/\s+/).some(w => w.length > 40)) return 'A single word is too long - please add spaces.';
                    return '';
                },
            };
            function show(el, msg) {
                form.querySelector('.cf-err[data-for="' + el.name + '"]').textContent = msg;
                el.closest('.cf-field').classList.toggle('is-bad', !!msg);
                return msg;
            }
            const check = el => show(el, rules[el.name](el.value));
            const NOT_PLAIN = /[^\x20-\x7E\r\n]/, NOT_PLAIN_ALL = /[^\x20-\x7E\r\n]/g;
            fields.forEach(el => {
                el.addEventListener('input', () => {
                    if (el.tagName !== 'SELECT' && NOT_PLAIN.test(el.value)) {
                        el.value = el.value.replace(NOT_PLAIN_ALL, ''); el.dataset.touched = '1';
                        if (el.name === 'message') count.textContent = el.value.length + ' / ' + L.msgMax;
                        show(el, 'Emojis and special unicode characters are not allowed - they were removed.');
                        return;
                    }
                    if (el.name === 'message') count.textContent = el.value.length + ' / ' + L.msgMax;
                    if (el.dataset.touched) check(el);
                });
                el.addEventListener('blur', () => {
                    el.dataset.touched = '1';
                    if (el.tagName === 'INPUT') el.value = el.name === 'name' ? el.value.replace(/\s+/g, ' ').trim() : el.value.trim();
                    check(el);
                });
                el.addEventListener('change', () => { el.dataset.touched = '1'; check(el); });
            });

            let lastFocus = null;
            function openModal(course) {
                lastFocus = document.activeElement;
                form.reset(); count.textContent = '0 / ' + L.msgMax; status.className = 'cf-status'; status.textContent = '';
                fields.forEach(el => { delete el.dataset.touched; show(el, ''); });
                if ([...form.elements.course.options].some(o => o.value === course)) form.elements.course.value = course;
                modal.hidden = false; document.body.style.overflow = 'hidden';
                setTimeout(() => form.elements.name.focus(), 30);
            }
            function closeModal() { modal.hidden = true; document.body.style.overflow = ''; lastFocus && lastFocus.focus && lastFocus.focus(); }
            // delegated: the endless carousel clones cards, so the clones' buttons must work too
            document.addEventListener('click', e => {
                const b = e.target.closest('[data-enroll]');
                if (b) { e.preventDefault(); openModal(b.dataset.enroll); }
            });
            modal.addEventListener('click', e => { if (e.target === modal || e.target.closest('[data-enroll-close]')) closeModal(); });
            document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) closeModal(); });

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
                    } else {
                        status.className = 'cf-status bad'; status.textContent = 'Something went wrong. Please try again later.';
                    }
                } catch (x) {
                    status.className = 'cf-status bad'; status.textContent = 'Cannot reach the server. Check your connection and try again.';
                }
                btn.disabled = false; btn.classList.remove('is-busy');
            });
        })();
    </script>
    @endpush
