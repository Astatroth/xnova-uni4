<table width="100%" id="building">
	<? foreach ($parse['BuildList'] AS $list): ?>
		<tr>
			<? if ($list['BuildMode'] == 'build'): ?>
				<td class="c">
					<?=$list['ListID'] ?>.: <?=$list['ElementTitle'] ?> <?=$list['BuildLevel'] ?>
					<? if ($list['BuildMode'] != 'build'): ?><?=_getText('destroy') ?><? endif; ?>
				</td>
			<? endif; ?>
			<td class="k">
				<? if ($list['ListID'] == 1): ?>
					<div id="blc" class="z"></div>
					<script type="text/javascript">BuildTimeout(<?=$list['BuildTime'] ?>, <?=$list['ListID'] ?>, <?=$list['PlanetID'] ?>, <?=(isset($_SESSION['LAST_ACTION_TIME']) ? $_SESSION['LAST_ACTION_TIME'] : 0) ?>);</script>
					<div class="positive"><?=datezone("d.m H:i:s", $list['BuildEndTime']) ?></div>
				<? else: ?>
					<a href="?set=buildings&listid=<?=$list['ListID'] ?>&cmd=remove&planet=<?=$list['PlanetID'] ?>">Удалить</a>
				<? endif; ?>
			</td>
		</tr>
	<? endforeach; ?>
	<tr>
		<th>Занятость полей</th>
		<th>
			<font color="#00FF00"><?=$parse['planet_field_current'] ?></font> / <font color="#FF0000"><?=$parse['planet_field_max'] ?></font> Осталось <?=$parse['field_libre'] ?> свободных полей
		</th>
	</tr>

	<? $i = 0; foreach ($parse['BuildingsList'] AS $build): $i++; ?>
	<?= (($i % 2 == 1) ? '<tr>' : '') ?>
	<td class="j" width="50%">
		<div class="viewport buildings <? if (!$build['access']): ?>shadow<? endif; ?>">
			<? if (!$build['access']): ?>
				<div class="notAvailable tooltip" data-tooltip-content="Требования:<br><?=str_replace('"', '\'', getTechTree($build['i'])) ?>" onclick="showWindow('<?=_getText('tech', $build['i']) ?>', '?set=infos&gid=<?=$build['i'] ?>&ajax&popup', 600)"><span>недоступно</span></div>
			<? endif; ?>

			<div class="img">
				<a href="javascript:;" onclick="showWindow('<?=_getText('tech', $build['i']) ?>', '?set=infos&gid=<?=$build['i'] ?>&ajax&popup', 600)">
					<img src="<?=RPATH ?><?=DPATH ?>gebaeude/<?=$build['i'] ?>.gif" align="top" width="120" height="120" alt="" class="tooltip" data-tooltip-content='<center><?=_getText('descriptions', $build['i']) ?></center>' data-tooltip-width="150">
				</a>

				<div class="overContent">
					<?=$build['price'] ?>
				</div>
			</div>
			<div class="title">
				<a href=?set=infos&gid=<?=$build['i'] ?>><?=_getText('tech', $build['i']) ?></a>
			</div>
			<div class="actions">
				Уровень: <span class="<?=($build['count'] > 0 ? 'positive' : 'negative') ?>"><?=\Xcms\Strings::pretty_number($build['count']) ?></span><br>
				<? if ($build['access']): ?>
					Время: <?=\Xcms\Strings::pretty_time($build['time']); ?><br>
					<?=$build['add'] ?>
					<div class="startBuild"><?=$build['click'] ?></div>
				<? endif; ?>
			</div>
		</div>
	</td>
	<?= (($i % 2 == 0) ? '</tr>' : '') ?>
	<? endforeach; ?>
	<? if ($i % 2 == 1): ?>
	<th style="border-spacing:0;height:100%;width:100%">&nbsp;</th></tr>
	<? endif; ?>
</table>
<script type="text/javascript">
	$('#building').on('click', '#blc', function(e)
	{
		$(this).remove();
	});
</script>