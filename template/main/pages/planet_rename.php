<form action="?set=overview&mode=renameplanet&pl=<?=$parse['planet_id'] ?>" method="POST">
	<table class="table">
		<tr>
			<td class="c" colspan=3>Переименовать или покинуть планету</td>
		</tr>
		<? if (!$isPopup): ?>
			<tr>
				<th class="hidden-xs"><?=$parse['galaxy_galaxy'] ?>:<?=$parse['galaxy_system'] ?>:<?=$parse['galaxy_planet'] ?></th>
				<th><?=$parse['planet_name'] ?></th>
				<th><input type="submit" name="action" value="Покинуть колонию" alt="Покинуть колонию"></th>
			</tr>
		<? endif; ?>
		<tr>
			<th class="hidden-xs">Сменить название</th>
			<th><input type="text" placeholder="<?=$parse['planet_name'] ?>" name="newname" maxlength=20></th>
			<th><input type="submit" name="action" value="Сменить название"></th>
		</tr>
	</table>
</form>
<? if ($parse['type'] != ''): ?>
	<div class="separator"></div>
	<form action="?set=overview&mode=renameplanet&pl=<?=$parse['planet_id'] ?>" method="POST">
		<table class="table">
			<tr>
				<td class="c">Сменить фон планеты</td>
			</tr>
			<tr>
				<th>
					<div class="separator"></div>
					<? for ($i = 0; $i <= $parse['images'][$parse['type']]; $i++): ?>
						<div class="col-xs-6 col-sm-3 col-md-2">
							<input type="radio" name="image" value="<?=$i ?>" id="image_<?=$i ?>">
							<label for="image_<?=$i ?>"><img src="<?=RPATH ?><?=DPATH ?>planeten/small/s_<?=$parse['type'] ?>planet<?=($i < 9 ? '0' : '').($i + 1) ?>.jpg" align="absmiddle" width="80"></label>
						</div>
					<? endfor; ?>
					<div class="separator"></div>
				</th>
			</tr>
			<tr>
				<th>
					<input type="submit" name="action" value="Сменить картинку (1 кредит)">
				</th>
			</tr>
		</table>
	</form>
<? endif; ?>