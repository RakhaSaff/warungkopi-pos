<!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login - Warung Kopi</title>
<style>
body{font-family:Poppins,Arial,sans-serif;background:#fff4e6;display:grid;place-items:center;min-height:100vh;margin:0}
.card{background:white;width:360px;padding:28px;border-radius:18px;box-shadow:0 10px 30px #0001}
h1{font-size:22px;margin:0 0 16px}label{font-size:12px;font-weight:700;color:#666}
input{width:100%;box-sizing:border-box;padding:12px;border:1px solid #ead7bd;border-radius:12px;margin:6px 0 12px}
button{width:100%;padding:12px;border:0;border-radius:12px;background:#F28C28;color:white;font-weight:700;cursor:pointer}
.err{background:#ffecec;color:#c0392b;padding:8px;border-radius:8px;font-size:12px;margin-bottom:10px}
.small{font-size:12px;color:#777;margin-top:14px;line-height:1.5}
</style></head><body>
<form class="card" method="POST" action="{{ route('login.post') }}">
@csrf
<h1>☕ Warung Kopi Nusantara</h1>
@if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif
<label>Email</label><input type="email" name="email" value="{{ old('email','owner@warungkopi.com') }}" required>
<label>Password</label><input type="password" name="password" value="password" required>
<button>Masuk</button>
<div class="small">Owner: owner@warungkopi.com / password<br>Kasir: kasir@warungkopi.com / password</div>
</form>
</body></html>
