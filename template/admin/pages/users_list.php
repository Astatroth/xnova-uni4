<div class="table-responsive">
	<table class="table table-striped table-hover table-advance">
		<thead>
			<tr>
				<th><a href="/admin/mode/users/?cmd=sort&type=id">ID</a></th>
				<th><a href="/admin/mode/users/?cmd=sort&type=username">Логин игрока</a></th>
				<th><a href="/admin/mode/users/?cmd=sort&type=email">E-Mail</a></th>
				<th><a href="/admin/mode/users/?cmd=sort&type=user_lastip">IP</a></th>
				<th><a href="/admin/mode/users/?cmd=sort&type=register_time">Регистрация</a></th>
			</tr>
		</thead>
		<? foreach ($list AS $l): ?>
			<tr>
				<td><a href="/admin/mode/users/action/edit/id/<?=$l['id'] ?>/"><?=$l['id'] ?></a></td>
				<td><a href="/admin/mode/users/action/edit/id/<?=$l['id'] ?>/"><?=$l['username'] ?></a></td>
				<td><?=$l['email'] ?></td>
				<td><?=long2ip($l['user_lastip']) ?></td>
				<td><?=date("d.m.Y H:i:s", $l['register_time']) ?><br><?=date("d.m.Y H:i:s", $l['onlinetime']) ?></td>
			</tr>
		<? endforeach; ?>
	</table>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="dataTables_paginate paging_bootstrap">
			<?=$pagination ?>
		</div>
	</div>
</div>