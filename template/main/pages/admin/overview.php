<table class="table">
	<tr>
		<td class="c" colspan="2">Информация</td>
	</tr>
	<tr>
		<td class="b" colspan="2" style="color:#FFFFFF">Версия игры: <strong><?=$parse['adm_ov_data_yourv'] ?></strong></td>
	</tr>
</table>
<br>
<table class="table">
	<tr>
		<td class="c" colspan="5">Online</td>
	</tr>
	<tr>
		<th width="40"><a href="?set=admin&mode=overview&cmd=sort&type=id">ЛС</a></th>
		<th><a href="?set=admin&mode=overview&cmd=sort&type=username">Логин игрока</a></th>
		<th width="150"><a href="?set=admin&mode=overview&cmd=sort&type=user_lastip">IP</a></th>
		<th><a href="?set=admin&mode=overview&cmd=sort&type=ally_name">Альянс</a></th>
		<th width="150"><a href="?set=admin&mode=overview&cmd=sort&type=onlinetime">Активность</a></th>
	</tr>
	<? foreach ($parse['adm_ov_data_table'] AS $list): ?>
	<tr>
		<th><a href="?set=messages&mode=write&id=<?=$list['adm_ov_data_id'] ?>"><img src="<?=RPATH ?><?=DPATH ?>img/<?=$list['adm_ov_data_pict'] ?>" width="16"></a></th>
		<th><a href="?set=admin&mode=paneladmina&result=usr_data&player=<?=$list['adm_ov_data_name'] ?>"><?=$list['adm_ov_data_name'] ?></a></th>
		<th><a style="color:<?=$list['adm_ov_data_clip'] ?>;" href="http://network-tools.com/default.asp?prog=trace&host=<?=$list['adm_ov_data_adip'] ?>">[<?=$list['adm_ov_data_adip'] ?>]</a></th>
		<th><?=$list['adm_ov_data_ally'] ?></th>
		<th><?=$list['adm_ov_data_activ'] ?></th>
	</tr>
	<? endforeach; ?>
	<tr>
		<th class="b center" colspan="5">Игроков в сети: <?=$parse['adm_ov_data_count'] ?></th>
	</tr>
</table>