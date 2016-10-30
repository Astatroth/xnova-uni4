<table class="table tutorial">
	<tr>
		<td class="c big" colspan="<?=$total ?>">Обучение</td>
	</tr>
	<tr>
		<? for ($i = 1; $i <= $total; $i++): ?>
			<th width="<?=ceil(100 / $total) ?>%" valign="middle">
				<a href="?set=tutorial&p=<?= $i ?>">Шаг <?= $i ?></a><img src="images/<?=($parse['quest_' . $i] ? 'check' : 'none') ?>.gif" height="11" width="13" align='absmiddle'>
			</th>
		<? endfor; ?>
	</tr>
	<tr>
		<td class="k" colspan="<?=$total ?>">
			<h3>Задание <?=$stage ?> - <?=$parse['info']['TITLE'] ?></h3>
		</td>
	</tr>
	<tr>
		<td class="k left" colspan="<?=$total ?>">
			<div class="row">
				<div class="column four center">
					<img src="<?=RPATH ?>images/tutorial/<?=$stage ?>.jpg" class="pic">
				</div>
				<div class="column eight">
					<div class="description">
						<?=$parse['info']['DESCRIPTION'] ?>
					</div>
					<h3>Задачи:</h3>
					<ul>
						<? foreach ($parse['task'] AS $task): ?>
							<li>
								<span><?=$task[0] ?></span>
								<span><img src="images/<?=($task[1] ? 'check' : 'none') ?>.gif" height="11" width="12"></span>
							</li>
						<? endforeach; ?>
					</ul>
					<div style="color:orange;">
						Награда: <?=implode(', ', $parse['rewd']) ?>
					</div>
				</div>
			</div>
	</tr>
	<tr>
		<td class="k" colspan="10">
			<? if (!$errors): ?>
				<input type="button" class="end" onclick="load('?set=tutorial&p=<?=$stage ?>&continue=1')" value="Закончить">
			<? endif; ?>
			<div class="solution">
				<?=$parse['info']['SOLUTION'] ?>
			</div>
		</td>
	</tr>
</table>