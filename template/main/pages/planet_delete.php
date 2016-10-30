<form action="?set=overview&mode=renameplanet&pl=<?=$parse['planet_id'] ?>" method=POST>
	<input type="hidden" name="password" value="<?=md5($parse['number_check']) ?>">
	<table width=519>
		<tr>
			<td class=c colspan=3>Система безопасности</td>
		</tr>
		<tr>
			<th colspan=3>Подтвердите удаление планеты <?=$parse['galaxy_galaxy'] ?>:<?=$parse['galaxy_system'] ?>:<?=$parse['galaxy_planet'] ?> вводом правильного ответа</th>
		</tr>
		<tr>
			<th><?=$parse['number_1'] ?> + <?=$parse['number_2'] ?> * <?=$parse['number_3'] ?> = ???</th>
			<th><input type="text" name=pw></th>
			<th><input type=submit name=action value="Удалить колонию"></th>
		</tr>
	</table>
	<input type="hidden" name="kolonieloeschen" value="1">
	<input type=hidden name=deleteid value="<?=$parse['planet_id'] ?>">
</form>