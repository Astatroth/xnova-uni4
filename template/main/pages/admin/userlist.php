<table class="table">
	<tr>
		<td class="c" colspan="9"><?=_getText('adm_ul_ttle2') ?></td>
	</tr>
	<tr>
		<th><a href="?set=admin&mode=userlist&cmd=sort&type=id">ID</a></th>
		<th><a href="?set=admin&mode=userlist&cmd=sort&type=username">Логин игрока</a></th>
		<th><a href="?set=admin&mode=userlist&cmd=sort&type=email">E-Mail</a></th>
		<th><a href="?set=admin&mode=userlist&cmd=sort&type=user_lastip">IP</a></th>
		<th><a href="?set=admin&mode=userlist&cmd=sort&type=register_time">Регистрация</a></th>
		<th><a href="?set=admin&mode=userlist&cmd=sort&type=onlinetime">Последний вход</a></th>
		<th><a href="?set=admin&mode=userlist&cmd=sort&type=banaday">Блок</a></th>
		<th>Инфо</th>
	</tr>

	<? foreach ($parse['adm_ul_table'] AS $list): ?>

	<tr>
		<th><?=$list['adm_ul_data_id'] ?></th>
		<th><?=$list['adm_ul_data_name'] ?></th>
		<th><?=$list['adm_ul_data_mail'] ?></th>
		<th><?=$list['adm_ul_data_adip'] ?></th>
		<th><?=$list['adm_ul_data_regd'] ?></th>
		<th><?=$list['adm_ul_data_lconn'] ?></th>
		<th><?=$list['adm_ul_data_banna'] ?></th>
		<th><?=$list['adm_ul_data_detai'] ?></th>
	</tr>

	<? endforeach; ?>

	<tr>
		<th class="b center" colspan="9"><?=$parse['adm_ul_count'] ?></th>
	</tr>
</table>