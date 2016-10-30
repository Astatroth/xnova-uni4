<br>
<table width="600" style="color:#FFFFFF">
	<?=$parse['desc'] ?><?=$parse['edit'] ?><?=$parse['name'] ?><?=$parse['member'] ?><?=$parse['member_row'] ?><?=$parse['mail'] ?><?=$parse['leader'] ?>

	<tr>
		<td class="c" colspan="11">Список альянсов</td>
	</tr>
	<tr>
		<th><a href="?set=admin&mode=alliancelist&cmd=sort&type=id">ID</a></th>
		<th>Название</th>
		<th>Обозначение</th>
		<th>Лидер</th>
		<th>Основан</th>
		<th>Описание альянса</th>
		<th>Количество учатников</th>
		<th>Послать сообщение</th>
		<th>Удалить</th>
	</tr>
	<?=$parse['alliance'] ?>
	<?=$parse['allianz'] ?>
</table>
