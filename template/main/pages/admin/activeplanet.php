<table class="table">
	<tr>
		<td class="c" colspan="4">Активные планеты</td>
	</tr>
	<tr>
		<th>Название</th>
		<th>Позиция</th>
		<th width="150">Активность</th>
	</tr>
	<? foreach ($parse['rows'] AS $planet): ?>
		<tr>
			<td class="b center"><b><?=$planet['name'] ?></b></td>
			<td class="b center"><b><?=$planet['position'] ?></b></td>
			<td class="b center"><b><?=\Xcms\Strings::pretty_time($planet['activity']) ?></b></td>
		</tr>
	<? endforeach; ?>
	<tr>
		<td class="b center" colspan="3"><?=$pagination ?></td>
	</tr>
	<tr>
		<td class="b center" colspan="3">Активно <?=$parse['total'] ?> планет<?=\Xcms\Strings::morph($parse['total'], 'feminine', 5) ?></td>
	</tr>
</table>
