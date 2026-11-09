<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | Albahub</title>
    @include('layout.favicon')
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ @filemtime(public_path('assets/css/admin.css')) }}">
    <style>
        .login-card .pw-wrap { position: relative; display: block; width: 100%; line-height: 0; }
        .login-card .pw-wrap input { display: block; width: 100%; box-sizing: border-box; padding-right: 48px; line-height: normal; }
        .login-card .pw-wrap input::-ms-reveal, .login-card .pw-wrap input::-ms-clear { display: none; }
        .login-card .pw-toggle { position: absolute; top: 0; bottom: 0; right: 0; width: 44px; height: auto; margin: 0; padding: 0; display: flex; align-items: center; justify-content: center; background: transparent; border: 0; border-radius: 0 6px 6px 0; color: #5b6676; cursor: pointer; line-height: 0; z-index: 1; }
        .login-card .pw-toggle:hover, .login-card .pw-toggle:focus-visible { color: #e5321b; outline: none; }
        .login-card .pw-toggle svg { display: block; width: 20px; height: 20px; flex: none; pointer-events: none; }

        /* Animated background: slow drifting glow orbs + faint grid on the dark navy base (same look as the site). */
        .login-body { position: relative; overflow: hidden; background: #0a1628; }
        .login-card { position: relative; z-index: 2; }
        .login-bg { position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none; }
        .login-bg::before { content: ''; position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px); background-size: 56px 56px; -webkit-mask-image: radial-gradient(ellipse at center, #000 30%, transparent 75%); mask-image: radial-gradient(ellipse at center, #000 30%, transparent 75%); animation: lgGrid 24s linear infinite; }
        .login-bg i { position: absolute; border-radius: 50%; filter: blur(80px); opacity: .55; will-change: transform; }
        .login-bg i:nth-child(1) { width: 46vmax; height: 46vmax; left: -12vmax; top: -14vmax; background: #c8914a; animation: lgFloat1 18s ease-in-out infinite alternate; }
        .login-bg i:nth-child(2) { width: 40vmax; height: 40vmax; right: -12vmax; bottom: -14vmax; background: #e5321b; opacity: .4; animation: lgFloat2 22s ease-in-out infinite alternate; }
        .login-bg i:nth-child(3) { width: 30vmax; height: 30vmax; left: 40%; top: 55%; background: #1d3b6e; animation: lgFloat3 26s ease-in-out infinite alternate; }
        @keyframes lgFloat1 { to { transform: translate(18vmax, 12vmax) scale(1.15); } }
        @keyframes lgFloat2 { to { transform: translate(-16vmax, -10vmax) scale(1.2); } }
        @keyframes lgFloat3 { to { transform: translate(-14vmax, -18vmax) scale(.85); } }
        @keyframes lgGrid { to { background-position: 56px 56px; } }
        /* Login card: dark glass panel with gold accents, matching the background and the main site */
        .login-body .login-card { background: linear-gradient(160deg, rgba(18,32,56,.82), rgba(8,16,30,.9)); border: 1px solid rgba(232,179,106,.28); border-radius: 18px; padding: 38px 34px; max-width: 400px; color: #e6ebf3; backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); box-shadow: 0 30px 70px rgba(0,0,0,.55), 0 0 0 1px rgba(255,255,255,.04) inset, 0 0 60px rgba(200,145,74,.12); animation: lgIn .7s cubic-bezier(.2,.8,.2,1) both; }
        .login-body .login-card h1 { color: #fff; font-size: 1.6rem; margin: 0 0 4px; position: relative; padding-bottom: 14px; }
        .login-body .login-card h1::after { content: ''; position: absolute; left: 0; bottom: 0; width: 44px; height: 3px; border-radius: 3px; background: linear-gradient(90deg, #e8b36a, #c8914a); }
        .login-body .login-card label { color: #aab4c3; font-weight: 600; }
        .login-body .login-card input[type=email], .login-body .login-card input[type=password], .login-body .login-card input[type=text] { background: rgba(6,11,19,.75); border: 1px solid rgba(255,255,255,.14); color: #fff; border-radius: 10px; transition: border-color .25s, box-shadow .25s; }
        .login-body .login-card input:focus { outline: 0; border-color: #e8b36a; box-shadow: 0 0 0 3px rgba(232,179,106,.22); }
        .login-body .login-card input:-webkit-autofill { -webkit-text-fill-color: #fff; -webkit-box-shadow: 0 0 0 40px #0b1424 inset; caret-color: #fff; }
        .login-body .login-card .pw-toggle { color: #aab4c3; }
        .login-body .login-card .pw-toggle:hover, .login-body .login-card .pw-toggle:focus-visible { color: #e8b36a; }
        .login-body .login-card .check { color: #c3ccd9; font-weight: 500; }
        .login-body .login-card input[type=checkbox] { accent-color: #c8914a; }
        .login-body .login-card .btn { background: linear-gradient(135deg, #e8b36a, #c8914a); color: #1a1205; font-weight: 700; border: 0; border-radius: 10px; box-shadow: 0 10px 24px rgba(200,145,74,.35); transition: transform .2s, box-shadow .2s, filter .2s; }
        .login-body .login-card .btn:hover { transform: translateY(-2px); filter: brightness(1.07); box-shadow: 0 14px 30px rgba(200,145,74,.5); }
        .login-body .login-card .alert.error { background: rgba(229,50,27,.14); border: 1px solid rgba(229,50,27,.45); color: #ffb4a8; }
        .login-brand { text-align: center; padding-bottom: 18px; margin-bottom: 2px; border-bottom: 1px solid rgba(255,255,255,.1); }
        .login-brand .login-logo { display: block; margin: 0 auto 14px; max-width: 170px; max-height: 64px; width: auto; height: auto; object-fit: contain; }
        .login-brand h2 { margin: 0 0 6px; font-size: 1.05rem; font-weight: 700; color: #e8b36a; letter-spacing: .02em; }
        .login-brand p { margin: 0; font-size: .85rem; line-height: 1.5; color: #aab4c3; }
        .login-body .login-card h1 { font-size: 1.25rem; padding-bottom: 12px; }
        @keyframes lgIn { from { opacity: 0; transform: translateY(24px) scale(.97); } to { opacity: 1; transform: none; } }
        @media (prefers-reduced-motion: reduce) { .login-body .login-card { animation: none; } }

        /* Business layer: growing bar chart + rising trend line + floating business icons */
        .login-bg .biz-chart { position: absolute; left: 0; right: 0; bottom: 0; width: 100%; height: auto; aspect-ratio: 1200 / 400; overflow: visible; }
        @media (max-width: 700px) { .login-bg .biz-chart { height: 42vh; aspect-ratio: auto; } }
        .biz-chart rect { fill: url(#bizBar); transform-box: fill-box; transform-origin: bottom; animation: bizGrow 5s ease-in-out infinite alternate; }
        .biz-chart rect:nth-child(odd) { animation-duration: 6.5s; }
        .biz-chart .trend { fill: none; stroke: #e8b36a; stroke-width: 3; stroke-linecap: round; stroke-linejoin: round; stroke-dasharray: 1700; stroke-dashoffset: 1700; animation: bizLine 9s ease-in-out infinite; filter: drop-shadow(0 0 6px rgba(232,179,106,.7)); }
        .biz-chart .dot { fill: #e8b36a; animation: bizPulse 2s ease-in-out infinite; transform-box: fill-box; transform-origin: center; }
        @keyframes bizGrow { from { transform: scaleY(.45); } to { transform: scaleY(1); } }
        @keyframes bizLine { 0% { stroke-dashoffset: 1700; } 60%, 100% { stroke-dashoffset: 0; } }
        @keyframes bizPulse { 50% { transform: scale(1.8); opacity: .5; } }
        .biz-ico { position: absolute; width: 54px; height: 54px; display: flex; align-items: center; justify-content: center; border-radius: 16px; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12); color: #e8b36a; backdrop-filter: blur(4px); animation: bizBob 7s ease-in-out infinite alternate; }
        .biz-ico svg { width: 26px; height: 26px; }
        .biz-ico:nth-of-type(1) { left: 9%; top: 16%; }
        .biz-ico:nth-of-type(2) { right: 11%; top: 20%; animation-delay: -2s; }
        .biz-ico:nth-of-type(3) { left: 14%; top: 56%; animation-delay: -4s; }
        .biz-ico:nth-of-type(4) { right: 15%; top: 58%; animation-delay: -1s; }
        @keyframes bizBob { from { transform: translateY(-14px) rotate(-4deg); } to { transform: translateY(14px) rotate(4deg); } }
        @media (max-width: 700px) { .biz-ico { width: 40px; height: 40px; border-radius: 12px; } .biz-ico svg { width: 20px; height: 20px; } }
        @media (prefers-reduced-motion: reduce) { .login-bg::before, .login-bg i, .biz-chart rect, .biz-chart .trend, .biz-chart .dot, .biz-ico { animation: none; } .biz-chart .trend { stroke-dashoffset: 0; } }
    </style>
</head>
<body class="login-body">
    <div class="login-bg" aria-hidden="true"><i></i><i></i><i></i>
        <svg class="biz-chart" viewBox="0 0 1200 400" preserveAspectRatio="xMidYMax meet">
            <defs><linearGradient id="bizBar" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#c8914a" stop-opacity=".55"/><stop offset="1" stop-color="#c8914a" stop-opacity="0"/></linearGradient></defs>
            <rect x="30" y="300" width="60" height="100"/><rect x="130" y="270" width="60" height="130"/><rect x="230" y="285" width="60" height="115"/>
            <rect x="330" y="235" width="60" height="165"/><rect x="430" y="250" width="60" height="150"/><rect x="530" y="200" width="60" height="200"/>
            <rect x="630" y="215" width="60" height="185"/><rect x="730" y="160" width="60" height="240"/><rect x="830" y="175" width="60" height="225"/>
            <rect x="930" y="120" width="60" height="280"/><rect x="1030" y="90" width="60" height="310"/><rect x="1130" y="60" width="50" height="340"/>
            <path class="trend" d="M60 290 L160 255 L260 270 L360 215 L460 232 L560 180 L660 196 L760 140 L860 155 L960 100 L1060 70 L1155 36"/>
            <circle class="dot" cx="1155" cy="36" r="7"/>
        </svg>
        <span class="biz-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"/></svg></span>
        <span class="biz-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18h6M10 21h4M12 3a6 6 0 0 0-4 10.5c.7.7 1 1.5 1 2.5h6c0-1 .3-1.8 1-2.5A6 6 0 0 0 12 3z"/></svg></span>
        <span class="biz-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M14.5 9a2.5 2 0 0 0-2.5-1.5c-1.5 0-2.5.8-2.5 1.8s1 1.6 2.5 1.9 2.5.9 2.5 1.9-1 1.9-2.5 1.9A2.7 2.2 0 0 1 9.5 15M12 6v1.5M12 16.5V18"/></svg></span>
        <span class="biz-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.2"/></svg></span>
    </div>
    <form method="POST" action="{{ route('admin.login.submit') }}" class="login-card">
        @csrf
        <div class="login-brand">
            @php($lb = \App\Models\HeroBanner::current())
            @if($lb && $lb->brand_logo)
                <img src="{{ asset($lb->brand_logo) }}" alt="Albahub" class="login-logo">
            @endif
            <h2>Albahub &mdash; School of Business</h2>
            <p>Welcome back. Sign in to manage the Albahub website, programs, blogs and enquiries.</p>
        </div>
        <h1>Admin Login</h1>
        @error('email')<div class="alert error">{{ $message }}</div>@enderror
        <label>Email
            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        </label>
        <label>Password
            <span class="pw-wrap">
                <input type="password" name="password" id="pw" required autocomplete="current-password">
                <button type="button" class="pw-toggle" id="pwToggle" aria-label="Show password" aria-pressed="false">
                    <svg class="eye-on" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="eye-off" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.9 10.9 0 0 1 12 19C5 19 1 12 1 12a18.5 18.5 0 0 1 5.06-5.94M9.9 4.24A9.1 9.1 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19M1 1l22 22"/><path d="M14.12 14.12A3 3 0 1 1 9.88 9.88"/></svg>
                </button>
            </span>
        </label>
        <label class="check"><input type="checkbox" name="remember"> Remember me</label>
        <button type="submit" class="btn">Sign in</button>
    </form>
<script>
(function () {
    var i = document.getElementById("pw"), b = document.getElementById("pwToggle");
    // open eye = password visible, slashed eye = password hidden
    b.addEventListener("mousedown", function (e) { e.preventDefault(); });
    b.addEventListener("click", function (e) {
        e.preventDefault(); e.stopPropagation();
        var show = i.type === "password", pos = i.selectionStart;
        i.type = show ? "text" : "password";
        b.setAttribute("aria-pressed", show ? "true" : "false");
        b.setAttribute("aria-label", show ? "Hide password" : "Show password");
        b.querySelector(".eye-on").style.display = show ? "" : "none";
        b.querySelector(".eye-off").style.display = show ? "none" : "";
        i.focus();
        try { i.setSelectionRange(pos, pos); } catch (err) {}
    });
})();
</script>
</body>
</html>