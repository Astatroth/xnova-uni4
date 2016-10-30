<form action="?set=admin&mode=settings" method="post">
	<input type="hidden" name="save" value="Y">
	<table class="table">
		<? foreach ($parse['settings'] AS $setting): ?>
			<tr>
				<th><?=$setting['key'] ?></th>
				<th>
					<? if ($setting['type'] == 'textarea'): ?>
						<textarea rows="5" name="setting[<?=$setting['key'] ?>]"><?=$setting['value'] ?></textarea>
					<? else: ?>
						<input name="setting[<?=$setting['key'] ?>]" value="<?=$setting['value'] ?>" type="text">
					<? endif; ?>
				</th>
			</tr>
		<? endforeach; ?>
		<tr>
			<th colspan="2"><input value="Сохранить" type="submit"></th>
		</tr>
	</table>
</form>
