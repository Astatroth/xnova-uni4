<table class="table">
	<tr>
		<td class="c" colspan="4"><?_getText('adm_er_list') ?> [<a href="?set=admin&mode=errors&deleteall=yes"><?=_getText('adm_er_clear') ?></a>]</td>
	</tr>
	<tr>
		<th><?=_getText('adm_er_idmsg') ?></th>
		<th><?=_getText('adm_er_type') ?></th>
		<th><?=_getText('adm_er_play') ?></th>
		<th><?=_getText('adm_er_time') ?></th>
	</tr>

	<? foreach ($parse['rows'] AS $data): ?>
	<tr>
		<th rowspan=2><?=$data['ID'] ?></th>
		<th><?=$data['TYPE'] ?> [<a href=?delete=<?=$data['ID'] ?>>X</a>]</th>
		<th><?=$data['SENDER'] ?></th>
		<th><?=datezone('d/m/Y h:i:s', $data['TIME']) ?></th>
	</tr>
	<tr>
		<td class=b colspan=4 width=500><?=$data['TEXT'] ?></td>
	</tr>
	<? endforeach; ?>

	<tr>
		<th class="b center" colspan=4><?=$parse['total'] ?> <?=_getText('adm_er_nbs') ?></th>
	</tr>

</table>
