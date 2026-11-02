@if(isset($programSection) && $programSection->is_active && $programCards->isNotEmpty())
    <section class="home-programs" id="programs">
        <div class="container">
            <div class="programs-head">
                @if($programSection->eyebrow)
                    <p class="programs-eyebrow"><span></span>{{ $programSection->eyebrow }}<span></span></p>
                @endif
                <h2>
                    @if($programSection->heading_accent)<em>{{ $programSection->heading_accent }}</em>@endif
                    @if($programSection->heading)<span>{{ $programSection->heading }}</span>@endif
                </h2>
                @if($programSection->description)<p class="programs-desc">{{ $programSection->description }}</p>@endif
            </div>

            @php($carousel = $programCards->count() > 4)
            <div class="programs-wrap {{ $carousel ? 'is-carousel' : '' }}" id="programsWrap">
            <div class="programs-viewport">
            <div class="programs-grid" data-count="{{ $programCards->count() }}">
                @foreach($programCards as $i => $card)
                    <article class="program-card {{ $card->is_featured ? 'is-featured' : '' }}" style="--i: {{ $i }}">
                        <p class="program-level"><span>Level {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><i></i></p>
                        <div class="program-media">
                            @if($card->image)
                                <img src="{{ asset($card->image) }}" alt="{{ $card->title }}" loading="lazy">
                            @endif
                        </div>
                        <h3><a href="{{ route('programs.show', $card->slug) }}" class="program-link">{{ $card->title }}</a></h3>
                        <ul class="program-features">
                            @foreach($card->features() as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                        <div class="program-foot">
                            <div class="program-meta"><strong>{{ is_numeric($card->duration) ? $card->duration . ' ' . Str::plural('Month', (int) $card->duration) : $card->duration }}</strong><span>{{ $card->mode }}</span></div>
                            {{-- Static button: change the text / link here in the code --}}
                            <button type="button" class="program-btn" data-enroll="{{ $card->title }}">Enroll Now &rarr;</button>
                        </div>
                    </article>
                @endforeach
            </div>
            </div>
            @if($carousel)
                <div class="programs-nav">
                    <button type="button" class="programs-nav-btn" id="programsPrev" aria-label="Previous programs">&larr;</button>
                    <button type="button" class="programs-nav-btn is-next" id="programsNext" aria-label="Next programs">&rarr;</button>
                </div>
            @endif
            </div>
        </div>
    </section>

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
                    <input type="tel" id="enPhone" name="phone" placeholder="+91 98765 43210" maxlength="{{ $ef['phone_max'] }}" autocomplete="tel" inputmode="tel" required>
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
        (function () {
            const cards = document.querySelectorAll('.program-card');

            // 5+ cards: slide as a carousel (4 / 3 / 2 / 1 cards visible depending on screen width)
            const wrap = document.getElementById('programsWrap');
            // phones (<600px) stack the cards one under another instead of sliding
            if (wrap && wrap.classList.contains('is-carousel') && window.matchMedia('(min-width: 600px)').matches) {
                const track = wrap.querySelector('.programs-grid');
                const prev = document.getElementById('programsPrev'), next = document.getElementById('programsNext');
                const n = cards.length;
                let index = 0, timer = null, hovered = false, busy = false;

                // endless loop: append copies of the first cards so the last slide runs straight into the first
                Array.from(cards).slice(0, Math.min(4, n)).forEach(c => {
                    const copy = c.cloneNode(true);
                    copy.setAttribute('aria-hidden', 'true');
                    copy.classList.add('in');
                    track.appendChild(copy);
                });

                function setPos(i, animate) {
                    index = i;
                    track.style.transition = animate ? '' : 'none';
                    const step = cards[0].offsetWidth + parseFloat(getComputedStyle(track).columnGap || 0);
                    track.style.transform = 'translateX(' + (-index * step) + 'px)';
                    if (!animate) track.getBoundingClientRect(); // flush so the next change animates
                }
                function slide(dir) {
                    if (busy) return;
                    busy = true;
                    if (dir < 0 && index === 0) setPos(n, false);
                    setPos(index + dir, true);
                    setTimeout(() => { busy = false; }, 750);
                }
                track.addEventListener('transitionend', e => {
                    if (e.target === track && e.propertyName === 'transform' && index >= n) setPos(0, false);
                });
                function restart() {
                    clearInterval(timer);
                    timer = setInterval(() => { if (!hovered && !document.hidden) slide(1); }, 3500);
                }
                cards.forEach(c => c.classList.add('in')); // clipped cards never intersect the viewport, so reveal them now
                prev.addEventListener('click', () => { slide(-1); restart(); });
                next.addEventListener('click', () => { slide(1); restart(); });
                wrap.addEventListener('mouseenter', () => { hovered = true; });
                wrap.addEventListener('mouseleave', () => { hovered = false; });
                window.addEventListener('resize', () => setPos(index >= n ? 0 : index, false));
                window.addEventListener('pagehide', () => clearInterval(timer));
                // swipe on touch screens
                let x0 = null;
                track.addEventListener('touchstart', e => { x0 = e.touches[0].clientX; }, { passive: true });
                track.addEventListener('touchend', e => {
                    if (x0 === null) return;
                    const dx = e.changedTouches[0].clientX - x0; x0 = null;
                    if (Math.abs(dx) > 40) { slide(dx < 0 ? 1 : -1); restart(); }
                }, { passive: true });
                restart();
            }

            if (!('IntersectionObserver' in window)) { cards.forEach(c => c.classList.add('in')); return; }
            const io = new IntersectionObserver(entries => entries.forEach(e => {
                if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
            }), { threshold: .15 });
            cards.forEach(c => io.observe(c));

            // 3D hover: neighbours of the hovered card move outward (left cards left, right cards right)
            const grid = document.querySelector('.programs-grid');
            if (grid && window.matchMedia('(hover: hover) and (min-width: 901px)').matches) {
                const all = Array.from(grid.querySelectorAll('.program-card'));
                const reset = () => {
                    grid.classList.remove('is-hovering');
                    all.forEach(c => { c.classList.remove('is-hovered', 'is-pushed'); c.style.removeProperty('--dx'); c.style.removeProperty('--ry'); c.style.removeProperty('--sc'); });
                };
                all.forEach((card, k) => {
                    card.addEventListener('mouseenter', () => {
                        if (!card.classList.contains('in')) return;
                        grid.classList.add('is-hovering');
                        all.forEach((c, j) => {
                            c.classList.toggle('is-hovered', j === k);
                            c.classList.toggle('is-pushed', j !== k);
                            if (j === k) { c.style.setProperty('--dx', '0px'); c.style.setProperty('--ry', '0deg'); return; }
                            const dir = Math.sign(j - k), dist = Math.abs(j - k);
                            c.style.setProperty('--dx', (dir * (46 + 18 * (dist - 1))) + 'px');
                            c.style.setProperty('--ry', (-dir * 16) + 'deg');
                            c.style.setProperty('--sc', '.94');
                        });
                    });
                });
                grid.addEventListener('mouseleave', reset);
            }
        })();
    </script>
    @endpush
@endif
