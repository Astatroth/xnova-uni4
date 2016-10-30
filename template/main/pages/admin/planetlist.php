<table class="table">
	<tr>
		<td class="c" colspan="6">Планеты во вселенной</td>
	</tr>
	<tr>
		<th>ID</th>
		<th>Название планеты</th>
		<th>Галактика</th>
		<th>Система</th>
		<th>Планета</th>
		<th>Переход</th>
	</tr>
	<? foreach ($planetlist AS $planet): ?>
		<tr>
			<td class="b center"><b><?=$planet['id'] ?></b></td>
			<td class="b center"><b><?=$planet['name'] ?></b></td>
			<td class="b center"><b><?=$planet['galaxy'] ?></b></td>
			<td class="b center"><b><?=$planet['system'] ?></b></td>
			<td class="b center"><b><?=$planet['planet'] ?></b></td>
			<td class="b center"><b><?=BuildPlanetAdressLink($planet) ?></b></td>
		</tr>
	<? endforeach; ?>
	<tr>
		<th class="b center" colspan="6"><?=$pagination ?></th>
	</tr>
	<tr>
		<th class="b center" colspan="6">В игре <?=$all ?> планет<?=\Xcms\Strings::morph($all, 'feminine', 5) ?></th>
	</tr>
</table>
