<div class="table-responsive">
	<table class="table table-striped table-hover table-advance">
		<thead>
			<tr>
				<th width="50">ID</th>
				<th width="20%">Игрок</th>
				<th>Тема</th>
				<th width="150">Статус</th>
				<th width="150">Дата</th>
			</tr>
		</thead>
		<? foreach ($tickets['open'] AS $list): ?>
		<tr>
			<td><?=$list['id'] ?></td>
			<td><?=$list['username'] ?></td>
			<td><a href="?set=admin&amp;mode=support&amp;action=detail&amp;id=<?=$list['id'] ?>"><?=$list['subject'] ?></a></td>
			<td><?=$list['status'] ?></td>
			<td><?=$list['date'] ?></td>
		</tr>
		<? endforeach; ?>
		<? if (count($tickets['open']) == 0): ?>
			<th colspan="5" class="c">Нет новых запросов</th>
		<? endif; ?>
	</table>
</div>
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
	<table class="table table-advance">
		<thead>
			<tr>
				<th width="50">
					<center>ID</center>
				</th>
				<th width="20%">
					<center>Игрок</center>
				</th>
				<th>
					<center>Тема</center>
				</th>
				<th width="150">
					<center>Статус</center>
				</th>
				<th width="150">
					<center>Дата</center>
				</th>
			</tr>
		</thead>
		<tr>
			<td><?=$parse['t_id'] ?></td>
			<td><?=$parse['t_username'] ?></td>
			<td><?=$parse['t_subject'] ?></a></td>
			<td><?=$parse['t_statustext'] ?></td>
			<td><?=$parse['t_date'] ?></td>
		</tr>
	</table>
	<div class="separator"></div>
	<table class="table table-advance">
		<thead>
			<tr>
				<th>Текст запроса:</th>
			</tr>
		</thead>
		<tr>
			<td><?=$parse['t_text'] ?></td>
		</tr>
	</table>
	<div class="portlet box green">
		<div class="portlet-title">
			<div class="caption">Ответ</div>
		</div>
		<div class="portlet-body form">
			<form action="?set=admin&amp;mode=support&amp;action=send&amp;id=<?=$parse['t_id'] ?>" method="POST">
				<div class="form-body">
					<div class="form-group">
						<textarea class="form-control" rows="10" name="text"></textarea>
					</div>
					<div class="form-actions">
						<button type="submit" class="btn green">Ответить</button>
					</div>
				</div>
			</form>
			<hr>
			<? if ($parse['t_status'] != 0): ?>
			<form action="?set=admin&amp;mode=support&amp;action=close&amp;id=<?=$parse['t_id'] ?>" method="POST">
				<input type="submit" value="Закрыть"></form>
			<? else: ?>
			<form action="?set=admin&amp;mode=support&amp;action=open&amp;id=<?=$parse['t_id'] ?>" method="POST">
				<input type="submit" value="Открыть"></form>
			<? endif; ?>
		</div>
	</div>
<? endif; ?>