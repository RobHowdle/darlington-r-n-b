<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Darlington RnB Audio Manager</title>
    <style>
        :root{font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#17202a;background:#f3f5f7}
        body{margin:0}.wrap{max-width:1080px;margin:36px auto;padding:0 20px}
        .top,.toolbar,.row{display:flex;align-items:center;justify-content:space-between;gap:16px}
        .top{margin-bottom:24px}.top h1{margin:0}.toolbar{margin:20px 0}
        .card{background:#fff;border:1px solid #dce2e8;border-radius:10px;padding:20px;margin-bottom:14px}
        .row{justify-content:flex-start}.cover{width:88px;height:88px;object-fit:cover;border-radius:6px;background:#eee}
        .grow{flex:1}.muted{color:#5e6b75}.pill{font-size:12px;padding:4px 9px;border-radius:99px;background:#e8edf2}
        .published{background:#d9f3e4;color:#145b32}.actions{display:flex;gap:10px;align-items:center}
        a,button{color:#075a9c}a{font-weight:600}button,.button{border:0;border-radius:6px;background:#075a9c;color:white;padding:10px 14px;font:inherit;text-decoration:none;cursor:pointer}
        button.danger{background:#a93131}.link-button{border:0;background:transparent;color:#a93131;padding:6px}
        label{display:block;font-weight:650;margin:16px 0 6px}input[type=text],input[type=number],input[type=file]{box-sizing:border-box;width:100%;padding:10px;border:1px solid #aeb8c2;border-radius:6px;font:inherit}
        input[type=checkbox]{margin-right:8px}small,.error{display:block;margin-top:5px}.error{color:#b42318}.status{padding:12px;background:#e2f5e8;border-radius:6px;margin:16px 0}
        .form-actions{display:flex;gap:14px;align-items:center;margin-top:24px}
    </style>
</head>
<body>
    <main class="wrap">
        <div class="top">
            <div><h1>Darlington RnB audio</h1><p class="muted">Manage the tracks and artwork shown on the public Audio page.</p></div>
            <a href="{{ route('admin.rnb-audio.index') }}">Audio library</a>
        </div>
        @if (session('status'))<div class="status">{{ session('status') }}</div>@endif
        @yield('content')
    </main>
</body>
</html>
