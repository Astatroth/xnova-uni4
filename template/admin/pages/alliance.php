<div class="table-responsive">
	<table class="table table-striped table-bordered table-advance">
		<?=$parse['desc'] ?><?=$parse['edit'] ?><?=$parse['name'] ?><?=$parse['member'] ?><?=$parse['member_row'] ?><?=$parse['mail'] ?><?=$parse['leader'] ?>
		<thead>
			<tr>
				<th><a href="?set=admin&mode=alliancelist&cmd=sort&type=id">ID</a></th>
				<th>Название</th>
				<th>Обозначение</th>
				<th>Лидер</th>
				<th>Основан</th>
				<th>Описание альянса</th>
				<th>Кол-во учатников</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<? foreach ($parse['alliance'] AS $u): ?>
			<tr>
				<td><?=$u['id'] ?></td>
				<td><a href="/?set=admin&mode=alliancelist&allyname=<?=$u['id'] ?>"><?=$u['ally_name'] ?></a></td>
				<td><a href="/?set=admin&mode=alliancelist&allyname=<?=$u['id'] ?>"><?=$u['ally_tag'] ?></a></td>
				<td><a href="/?set=admin&mode=alliancelist&leader=<?=$u['id'] ?>"><?=$u['username'] ?></a></td>
				<td><?=date("d/m/Y H:i:s", $u['ally_register_time']) ?></td>
				<td><a href="/?set=admin&mode=alliancelist&desc=<?=$u['id'] ?>">Смотреть</a>/<a href="?set=admin&mode=alliancelist&edit=<?=$u['id'] ?>">Редактировать</a></td>
				<td><a href="/?set=admin&mode=alliancelist&mitglieder=<?=$u['id'] ?>"><?=$u['ally_members'] ?></a></td>
				<td><a href="/?set=admin&mode=alliancelist&mail=<?=$u['id'] ?>"><img src="<?=RPATH ?>images/r5.png"></a></td>
				<td><a href="/?set=admin&mode=alliancelist&del=<?=$u['id'] ?>">X</a></td>
			</tr>
		<? endforeach; ?>
		<tr><th colspan="9">Всего <?=count($parse['alliance']) ?> альянсов</th></tr>
	</table>
</div>