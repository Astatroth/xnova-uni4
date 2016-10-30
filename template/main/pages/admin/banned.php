<br>
<form action="?set=admin&mode=banned" method="post">
	<input type="hidden" name="modes" value="banit">
	<table width="500">
		<tr>
			<td class="c" colspan="2">Банилка</td>
		</tr>
		<tr>
			<th width="129">Логин</th>
			<th width="268"><input name="name" type="text" size="25"/></th>
		</tr>
		<tr>
			<th>Причина</th>
			<th><input name="why" type="text" value="" size="25" maxlength="50"></th>
		</tr>
		<tr>
			<td class="c" colspan="2">Время бана</td>
		</tr>
		<tr>
			<th>Дни</th>
			<th><input name="days" type="text" value="0" size="5"/></th>
		</tr>
		<tr>
			<th>Часы</th>
			<th><input name="hour" type="text" value="0" size="5"/></th>
		</tr>
		<tr>
			<th>Минуты</th>
			<th><input name="mins" type="text" value="0" size="5"/></th>
		</tr>
		<tr>
			<th>Секунды</th>
			<th><input name="secs" type="text" value="0" size="5"/></th>
		</tr>
		<tr>
			<th>РО</th>
			<th><input name="ro" type="checkbox" value="1"/></th>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" value="Забанить"/></th>
		</tr>
	</table>
</form>
