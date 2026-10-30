<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | Albatech HUB</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
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
            <input type="password" name="password" required>
        </label>
        <label class="check"><input type="checkbox" name="remember"> Remember me</label>
        <button type="submit" class="btn">Sign in</button>
    </form>
</body>
</html>