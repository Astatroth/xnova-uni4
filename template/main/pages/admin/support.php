<table class="table">
	<tr>
		<td colspan="5" class="c">Служба техподдержки</td>
	</tr>
	<tr>
		<td class="c" width="10%">
			<center>ID</center>
		</td>
		<td class="c" width="10%">
			<center>Игрок</center>
		</td>
		<td class="c" width="40%">
			<center>Тема</center>
		</td>
		<td class="c" width="15%">
			<center>Статус</center>
		</td>
		<td class="c" width="25%">
			<center>Дата</center>
		</td>
	</tr>
	<? foreach ($tickets['open'] AS $list): ?>
	<tr>
		<th><?=$list['id'] ?></th>
		<th><?=$list['username'] ?></th>
		<th><a href="?set=admin&amp;mode=support&amp;action=detail&amp;id=<?=$list['id'] ?>"><?=$list['subject'] ?></a></th>
		<th><?=$list['status'] ?></th>
		<th><?=$list['date'] ?></th>
	</tr>
	<? endforeach; ?>
	<? if (count($tickets['open']) == 0): ?>
	<th colspan="5" class="c">Нет новых запросов</th>
	<? endif; ?>
</table>
<? if (!empty($tickets['closed'])): ?>
<br><br>
<table class="table">
	<tr>
		<td colspan="5" class="c">
			<center>Служба техподдержки</center>
		</td>
	</tr>
	<tr>
		<td class="c" width="10%">
			<center>ID</center>
		</td>
		<td class="c" width="10%">
			<center>Игрок</center>
		</td>
		<td class="c" width="40%">
			<center>Тема</center>
		</td>
		<td class="c" width="15%">
			<center>Статус</center>
		</td>
		<td class="c" width="25%">
			<center>Дата</center>
		</td>
	</tr>
	<? foreach ($tickets['closed'] AS $list): ?>
	<tr>
		<th><?=$list['id'] ?></th>
		<th><?=$list['username'] ?></th>
		<th><a href="?set=admin&amp;mode=support&amp;action=detail&amp;id=<?=$list['id'] ?>"><?=$list['subject'] ?></a></th>
		<th><?=$list['status'] ?></th>
		<th><?=$list['date'] ?></th>
	</tr>
	<? endforeach; ?>
</table>
<? endif; ?>
<? if (isset($parse['t_id'])): ?>
	<br><br>
	<table class="table">
		<tr>
			<td class="c" width="10%">
				<center>ID</center>
			</td>
			<td class="c" width="10%">
				<center>Игрок</center>
			</td>
			<td class="c" width="40%">
				<center>Тема</center>
			</td>
			<td class="c" width="15%">
				<center>Статус</center>
			</td>
			<td class="c" width="25%">
				<center>Дата</center>
			</td>
		</tr>
		<tr>
			<th><?=$parse['t_id'] ?></th>
			<th><?=$parse['t_username'] ?></th>
			<th><?=$parse['t_subject'] ?></a></th>
			<th><?=$parse['t_statustext'] ?></th>
			<th><?=$parse['t_date'] ?></th>
		</tr>
	</table>
	<div class="separator"></div>
	<table class="table">
		<tr>
			<td class="c">Текст запроса:</td>
		</tr>
		<tr>
			<th style="text-align:left"><?=$parse['t_text'] ?></th>
		</tr>
		<tr>
			<td class="c">Ответ</td>
		</tr>
		<tr>
			<th>
				<form action="?set=admin&amp;mode=support&amp;action=send&amp;id=<?=$parse['t_id'] ?>" method="POST">
					<textarea style="width: 99%" rows="10" name="text"></textarea>
					<br><br><input type="submit" value="Ответить">
				</form>
				<hr>
				<? if ($parse['t_status'] != 0): ?>
				<form action="?set=admin&amp;mode=support&amp;action=close&amp;id=<?=$parse['t_id'] ?>" method="POST">
					<input type="submit" value="Закрыть"><br><br></form>
				<? else: ?>
				<form action="?set=admin&amp;mode=support&amp;action=open&amp;id=<?=$parse['t_id'] ?>" method="POST">
					<input type="submit" value="Открыть"><br><br></form>
				<? endif; ?>
			</th>
		</tr>
	</table>
<? endif; ?>