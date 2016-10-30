<br>
<form action="?set=admin&mode=paneladmina" method="post">
	<table width="300">
			<tr>
				<td class="c" colspan="6"><?=_getText('adm_mod_level') ?></td>
			</tr>
			<tr>
				<th><?=_getText('adm_player_nm') ?></th>
				<th><input type="text" name="player" style="width:150px"></th>
			</tr>
			<tr>
				<th colspan="2"><select name="authlvl"><?=$parse['adm_level_lst'] ?></select></th>
			</tr>
			<tr>
				<th colspan="2"><input type="submit" value="<?=_getText('adm_bt_change') ?>"></th>
			</tr>
	</table>
	<input type="hidden" name="result" value="usr_level">
</form>