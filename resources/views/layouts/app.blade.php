<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Small E-commerce') - {{ config('app.name') }}</title>
    <style>
        :root { --bg:#f3f4f6; --card:#fff; --text:#1f2937; --muted:#6b7280; --primary:#2563eb; --danger:#dc2626; --border:#e5e7eb; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:var(--bg); color:var(--text); line-height:1.5; }
        a { color:var(--primary); text-decoration:none; }
        a:hover { text-decoration:underline; }
        .nav { background:#111827; padding:0 20px; display:flex; align-items:center; gap:18px; flex-wrap:wrap; }
        .nav a, .nav button { color:#e5e7eb; padding:14px 0; font-size:15px; background:none; border:0; cursor:pointer; font-family:inherit; }
        .nav .brand { font-weight:700; color:#fff; margin-right:12px; }
        .nav .spacer { flex:1; }
        .nav .who { color:#9ca3af; font-size:13px; }
        .container { max-width:1100px; margin:24px auto; padding:0 16px; }
        h1 { margin:0 0 16px; font-size:24px; }
        .card { background:var(--card); border:1px solid var(--border); border-radius:10px; padding:20px; margin-bottom:20px; }
        .toolbar { display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
        .btn { display:inline-block; background:var(--primary); color:#fff; border:0; padding:8px 14px; border-radius:6px; font-size:14px; cursor:pointer; text-decoration:none; font-family:inherit; }
        .btn:hover { text-decoration:none; opacity:.9; }
        .btn.secondary { background:#e5e7eb; color:var(--text); }
        .btn.danger { background:var(--danger); }
        .btn.small { padding:4px 10px; font-size:13px; }
        table { width:100%; border-collapse:collapse; background:var(--card); border:1px solid var(--border); border-radius:10px; overflow:hidden; }
        th, td { padding:10px 12px; border-bottom:1px solid var(--border); text-align:left; font-size:14px; vertical-align:middle; }
        th { background:#f9fafb; color:var(--muted); font-weight:600; }
        td.actions { white-space:nowrap; }
        td.actions form { display:inline; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(230px, 1fr)); gap:16px; }
        .product { background:var(--card); border:1px solid var(--border); border-radius:10px; overflow:hidden; display:flex; flex-direction:column; }
        .product .body { padding:14px; display:flex; flex-direction:column; gap:6px; flex:1; }
        .product h3 { margin:0; font-size:16px; }
        .price { font-weight:700; font-size:18px; }
        .muted { color:var(--muted); font-size:13px; }
        .thumb { width:100%; height:160px; object-fit:cover; background:#e5e7eb; display:block; }
        .thumb-sm { width:48px; height:48px; object-fit:cover; border-radius:6px; background:#e5e7eb; }
        .noimg { display:flex; align-items:center; justify-content:center; color:var(--muted); font-size:13px; }
        .form label { display:block; font-weight:600; margin:14px 0 4px; font-size:14px; }
        .form input[type=text], .form input[type=email], .form input[type=password], .form input[type=number], .form input[type=file], .form textarea, .form select {
            width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:14px; font-family:inherit; background:#fff; }
        .form textarea { min-height:90px; }
        .form .check { display:flex; gap:8px; align-items:center; font-weight:400; }
        .form .check input { width:auto; }
        .form .buttons { margin-top:20px; display:flex; gap:10px; }
        .error { color:var(--danger); font-size:13px; margin-top:4px; }
        .flash { padding:10px 14px; border-radius:8px; margin-bottom:16px; font-size:14px; }
        .flash.success { background:#dcfce7; color:#166534; }
        .flash.error { background:#fee2e2; color:#991b1b; }
        .badge { display:inline-block; background:#e5e7eb; border-radius:99px; padding:2px 10px; font-size:12px; }
        .badge.green { background:#dcfce7; color:#166534; }
        .stats { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:20px; }
        .stat { background:var(--card); border:1px solid var(--border); border-radius:10px; padding:18px; }
        .stat .n { font-size:30px; font-weight:700; }
        .pager { display:flex; gap:12px; align-items:center; justify-content:center; margin-top:18px; font-size:14px; }
        .pager .off { color:#9ca3af; }
        .narrow { max-width:520px; margin:0 auto; }
        .wide-form { max-width:640px; }
    </style>
</head>
<body>
<nav class="nav">
    <a class="brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
    <a href="{{ route('home') }}">Shops</a>

    @auth
        @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.shops.index') }}">Manage Shops</a>
            <a href="{{ route('admin.products.index') }}">Manage Products</a>
            <a href="{{ route('admin.shop-owners.index') }}">Shop Owners</a>
        @else
            <a href="{{ route('owner.dashboard') }}">My Shop</a>
            <a href="{{ route('owner.catalog') }}">Shared Catalog</a>
            <a href="{{ route('owner.products.create') }}">New Product</a>
        @endif
        <span class="spacer"></span>
        <span class="who">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    @else
        <span class="spacer"></span>
        <a href="{{ route('login') }}">Login</a>
    @endauth
</nav>

<main class="container">
    @if (session('success'))
        <div class="flash success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="flash error">{{ session('error') }}</div>
    @endif

    @yield('content')
</main>
</body>
</html>
