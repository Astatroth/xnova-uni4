<? if (isset($errors)): ?>
	<div class="error"><?=$errors ?></div>
<? endif; ?>
<form action="?set=reg&ajax&popup&xd" method="post" id="regForm" class="form">
	<table class="table">
		<tbody>
		<tr>
			<th width="40%">E-Mail<br>(используется для входа)</th>
			<th><input name="email" size="20" maxlength="40" type="text" value="<?=is($_POST, 'email') ?>"></th>
		</tr>
		<tr>
			<th>Пароль</th>
			<th><input name="passwrd" id="password" size="20" maxlength="20" type="password"></th>
		</tr>
		<tr>
			<th>Подтверждение пароля</th>
			<th><input name="сpasswrd" size="20" maxlength="20" type="password"></th>
		</tr>
		<tr>
			<th><img src="/captcha.php?rnd=<?=mt_rand(0, 11111) ?>"></th>
			<th><input type="text" name="captcha" size="20" maxlength="20"/></th>
		</tr>
		<tr>
			<th colspan=2 class="text-left">
				<input name="sogl" id="sogl" type="checkbox" <?=(is($_POST, 'sogl') ? 'checked' : '') ?>>
				<label for="sogl">Я принимаю</label> <a href="?set=sogl" target="_blank">Пользовательское соглашение</a>
			</th>
		</tr>
		<tr>
			<th colspan=2 class="text-left">
				<input name="rgt" id="rgt" type="checkbox" <?=(is($_POST, 'rgt') ? 'checked' : '') ?>>
				<label for="rgt">Я принимаю</label> <a href="?set=agb" target="_blank">Законы игры</a>
			</th>
		</tr>
		<tr>
			<th colspan=2><input name="submit" type="submit" value="Регистрация"></th>
		</tr>
	</table>
</form>
<script>
	$(document).ready(function()
	{
		$('#regForm').validate({
			submitHandler: function(form)
			{
				$(form).ajaxSubmit({
					target: '#windowDialog'
				});
			},
			focusInvalid: false,
			focusCleanup: true,
			rules:
			{
				'passwrd': 'required',
				'сpasswrd': {required: true, 'equalTo': '#password'},
				'email': {required: true, email: true},
				'captcha': 'required'
			},
			messages:
			{
				'passwrd': 'Введите пароль от игры',
				'сpasswrd': {required: 'Введите подтверждение пароля', equalTo: 'Пароли не совпадают'},
				'email': {required: 'Введите Email адрес', email: 'Введите корректный Email адрес'},
				'captcha': 'Введите проверочный код с картинки'
			}
		});
	});
</script>