<? if (count($parse['list'])): ?>
	<script type="text/javascript">
		$('.planetList .list').html('<? foreach ($parse['list'] AS $i => $planet): ?><div class="planet type_<?=$planet['planet_type'] ?> <?=($parse['current'] == $planet['id'] ? 'current' : '') ?>"><a href="javascript:;" onclick="changePlanet(<?=$planet['id'] ?>)" title="<?=$planet['name'] ?>"><img src="<?=RPATH ?><?=DPATH ?>planeten/small/s_<?=$planet['image'] ?>.jpg" height="40" width="40" alt="<?=$planet['name'] ?>"></a><div><?=$planet['name'] ?><br><?=BuildPlanetAdressLink($planet) ?></div><div class="clear"></div></div><? endforeach; ?>');
	</script>
<? endif; ?>