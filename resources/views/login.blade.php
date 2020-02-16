<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Login</title>
	<link rel="stylesheet" href="{{ URL::asset('/css/auth.css') }}">
</head>

<body>
	<div class="lowin">
		<div class="lowin-brand">
			<img src="{{ URL::asset('/images/kodinger.jpg') }}" alt="logo">
		</div>
        

		<div class="lowin-wrapper">
			<div class="lowin-box lowin-login">
				<div class="lowin-box-inner">
					<form action="/login" method="POST">
						<p>登入 / 註冊新用戶</p>
                        {{csrf_field()}}
						<div class="lowin-group">
							<label>用戶名 </label>
							<input type="name" autocomplete="name" name="name" class="lowin-input" placeholder="請輸入6-20位的用戶名">
						</div>
						<div class="lowin-group password-group">
							<label>密碼 </label>
							<input type="password" name="password" autocomplete="current-password" class="lowin-input" placeholder="請輸入6-20位的密碼">
						</div>
						<button class="lowin-btn login-btn">
							登入 / 註冊
						</button>
                        @if ($errors->count())
                        <div class="text-foot" style="color:red">
                            @foreach($errors->all() as $error)
                            {{$error }}<br/>
                            @endforeach
						</div>
                        @endif
					</form>
				</div>
			</div>
		</div>
	
		<footer class="lowin-footer" style="display:none;">
			Design By @itskodinger. More Templates <a href="http://www.cssmoban.com/" target="_blank" title="模板之家">模板之家</a> - Collect from <a href="http://www.cssmoban.com/" title="网页模板" target="_blank">网页模板</a>
		</footer>
	</div>
</body>
</html>