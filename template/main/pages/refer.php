<table class="table">
	<tr>
		<td class=c colspan=3>Привлечённые игроки</td>
	<tr>
	<? if (count($parse['ref']) > 0): ?>
		<tr>
			<td class=c>Ник</td>
			<td class=c>Дата регистрации</td>
			<td class=c>Уровень развития</td>
		</tr>
		<? foreach ($parse['ref'] AS $list): ?>
			<tr>
				<th><? if (datezone("d", $list['register_time']) >= 15)
					echo '+&nbsp;'; ?><a href="?set=players&id=<?=$list['id'] ?>"><?=$list['username'] ?></a></th>
				<th><?=datezone("d.m.Y H:i", $list['register_time']) ?></th>
				<th>П:<?=$list['lvl_minier'] ?>, В:<?=$list['lvl_raid'] ?></th>
			</tr>
		<? endforeach; ?>
	<? else: ?>
	<tr>
		<th colspan="3">Нет записей</th>
	</tr>
	<? endif; ?>
</table>
<? if (isset($parse['you'])): ?>
<br><br>
<table class="table">
	<tr>
		<th>Вы были привлечены игроком:</th>
		<th><a href="?set=players&id=<?=$parse['you']['id'] ?>"><?=$parse['you']['username'] ?></a></th>
	</tr>
</table>
<? endif; ?>