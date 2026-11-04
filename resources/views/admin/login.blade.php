<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | Albatech HUB</title>
    @include('layout.favicon')
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ @filemtime(public_path('assets/css/admin.css')) }}">
    <style>
        .login-card .pw-wrap { position: relative; display: block; width: 100%; line-height: 0; }
        .login-card .pw-wrap input { display: block; width: 100%; box-sizing: border-box; padding-right: 48px; line-height: normal; }
        .login-card .pw-wrap input::-ms-reveal, .login-card .pw-wrap input::-ms-clear { display: none; }
        .login-card .pw-toggle { position: absolute; top: 0; bottom: 0; right: 0; width: 44px; height: auto; margin: 0; padding: 0; display: flex; align-items: center; justify-content: center; background: transparent; border: 0; border-radius: 0 6px 6px 0; color: #5b6676; cursor: pointer; line-height: 0; z-index: 1; }
        .login-card .pw-toggle:hover, .login-card .pw-toggle:focus-visible { color: #e5321b; outline: none; }
        .login-card .pw-toggle svg { display: block; width: 20px; height: 20px; flex: none; pointer-events: none; }
    </style>
</head>
<body class="login-body">
    <form method="POST" action="{{ route('admin.login.submit') }}" class="login-card">
        @csrf
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