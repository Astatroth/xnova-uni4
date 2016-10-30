<table class="table tutorial">
	<tr>
		<td class="k">
			<h3>Задание <?=$parse['info']['TITLE'] ?></h3>
		</td>
	</tr>
	<tr>
		<td class="k left">
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
		</td>
	</tr>
	<tr>
		<td class="k">
			<? if (!$errors): ?>
				<input type="button" class="end" onclick="load('?set=tutorial&q=<?=$stage ?>&continue=1')" value="Закончить">
			<? endif; ?>
			<div class="solution">
				<?=$parse['info']['SOLUTION'] ?>
			</div>
		</td>
	</tr>
</table>