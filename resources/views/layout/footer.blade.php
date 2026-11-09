<footer class="site-footer" id="contact">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a href="{{ route('home') }}" class="logo footer-logo">
                @if($brand->brand_logo)
                    <img src="{{ asset($brand->brand_logo) }}" alt="{{ $brand->brand_name ?: 'Albahub' }}" class="logo-img">
                @else
                    @php($parts = explode(' ', $brand->brand_name ?: 'ALBATECH HUB', 2))
                    <span class="logo-name">{{ $parts[0] }} @if(isset($parts[1]))<span class="logo-light">{{ $parts[1] }}</span>@endif <sup>TM</sup></span>
                    <span class="logo-sub">{{ strtoupper($brand->brand_tagline ?: 'School of Business') }}</span>
                @endif
            </a>
            {{-- Static tagline: change the text here in the code --}}
            <p class="footer-tag">Ideas. People. Business. A Brighter Tomorrow.<br>Empowering the next generation of business leaders.</p>
        </div>

        <nav class="footer-col" aria-label="Quick links">
            <h4>Quick Links</h4>
            <ul>
                @foreach($navItems as $item)
                    <li><a href="{{ str_starts_with($item->url, '#') ? route('home') . $item->url : (str_starts_with($item->url, '/') ? url($item->url) : $item->url) }}">{{ $item->label }}</a></li>
                @endforeach
            </ul>
        </nav>

        @if($footerPrograms->isNotEmpty())
            <nav class="footer-col" aria-label="Programs">
                <h4>Programs</h4>
                <ul>
                    @foreach($footerPrograms as $slug => $title)
                        <li><a href="{{ route('programs.show', $slug) }}">{{ $title }}</a></li>
                    @endforeach
                </ul>
            </nav>
        @endif

        <div class="footer-col footer-connect">
            <h4>Connect</h4>
            <p>Subscribe to receive thought leadership and hub updates.</p>
            <form id="newsletterForm" action="{{ route('newsletter.subscribe') }}" method="POST" novalidate>
                @csrf
                <input type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true" class="nl-hp">
                <div class="nl-field">
                    <input type="email" name="email" id="nlEmail" placeholder="Your email address" maxlength="254" autocomplete="email" inputmode="email" required aria-describedby="nlMsg" aria-label="Email address">
                    <button type="submit" aria-label="Subscribe">&rarr;</button>
                </div>
                <p class="nl-msg" id="nlMsg" role="status" aria-live="polite"></p>
            </form>
        </div>
    </div>
    <div class="container footer-bottom">
        <p>&copy; {{ date('Y') }} Albahub. All rights reserved.</p>
        {{-- Static links: change the text / links here in the code --}}
        <nav class="footer-legal" aria-label="Legal">
            @if($privacyActive)<a href="{{ route('privacy') }}">Privacy Policy</a>@endif
            @if($termsActive)<a href="{{ route('terms') }}">Terms of Service</a>@endif
            <a href="{{ route('sitemap') }}">Sitemap</a>
        </nav>
    </div>
</footer>

<script>
    (function () {
        const form = document.getElementById('newsletterForm'), input = document.getElementById('nlEmail'), msg = document.getElementById('nlMsg');
        const RE = /^[A-Za-z0-9.!#$%&'*+\/=?^_`{|}~-]+@[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?(?:\.[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?)*\.[A-Za-z]{2,}$/;
        function check(v) {
            if (v === '') return 'Please enter your email address.';
            if (/\s/.test(v)) return 'The email address cannot contain spaces.';
            if (/[^\x20-\x7E]/.test(v)) return 'Use plain letters and numbers - emojis and special characters are not allowed.';
            if (!v.includes('@')) return 'The email address must contain an @.';
            if ((v.match(/@/g) || []).length > 1) return 'The email address can contain only one @.';
            if (/\.\./.test(v) || /^\.|\.@|@\./.test(v)) return 'The email address has a misplaced dot.';
            if (!RE.test(v)) return 'Enter a valid email address, like name@example.com.';
            if (v.length > 254) return 'The email address is too long.';
            return '';
        }
        let hideTimer;
        function show(text, ok, autoHide) {
            clearTimeout(hideTimer);
            if (autoHide && text) hideTimer = setTimeout(() => show('', false), 5000);
            msg.textContent = text; msg.className = 'nl-msg' + (text ? (ok ? ' ok' : ' bad') : '');
            input.classList.toggle('invalid', !!text && !ok);
        }
        // Typing clears any error straight away; the field is validated again on blur / submit.
        input.addEventListener('input', () => show('', false));
        input.addEventListener('blur', () => { input.value = input.value.trim(); if (input.value) show(check(input.value), false, true); });
        form.addEventListener('submit', async e => {
            e.preventDefault();
            const v = input.value.trim(), err = check(v);
            if (err) { show(err, false, true); input.focus(); return; }
            const btn = form.querySelector('button'); btn.disabled = true;
            try {
                const r = await fetch(form.action, { method: 'POST', headers: { 'Accept': 'application/json' }, body: new FormData(form) });
                const j = await r.json().catch(() => ({}));
                if (r.ok) {
                    const text = j.message || 'Thank you for subscribing!';
                    show(text, true, true); form.reset();
                } else if (r.status === 422) {
                    const text = (j.errors && j.errors.email && j.errors.email[0]) || 'Please check your email address.';
                    const already = /already/i.test(text);
                    show(text, already, true);
                    if (already) form.reset();
                }
                else if (r.status === 429) show('Too many attempts. Please wait a minute and try again.', false, true);
                else show('Something went wrong. Please try again later.', false, true);
            } catch (x) { show('Cannot reach the server. Check your connection and try again.', false, true); }
            btn.disabled = false;
        });
    })();
</script>
