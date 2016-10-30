<div class="error"><?=$error ?></div>
<form action="?set=lostpassword&step=2&ajax" method="post" id="lostForm" class="form">
	<table class="table">
		<tr>
			<th>Введите ваш Email, который вы указали при регистрации. При нажатии на кнопку "Получить пароль" на ваш e-mail будет выслана ссылка на новый пароль.</th>
		</tr>
		<tr>
			<th>Ваш Email: <input type="text" name="login"/></th>
		</tr>
		<tr>
			<th><input name="submit" type="submit" value="Выслать пароль"/></th>
		</tr>
	</table>
</form>

<script>
	$(document).ready(function()
	{
		$('#lostForm').validate({
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
				'login': {required: true, email: true}
			},
			messages:
			{
				'login': {required: 'Введите Email адрес', email: 'Введите корректный Email адрес'}
			}
		});
	});
</script>