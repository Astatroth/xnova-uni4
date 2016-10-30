<br>
<form action="?set=admin&mode=paneladmina" method="post">
	<input type="hidden" name="result" value="usr_data">
	<table width="300">
			<tr>
				<td class="c" colspan="6"><?=_getText('adm_search_pl') ?></td>
			</tr>
			<tr>
				<th><?=_getText('adm_player_nm') ?></th>
				<th><input type="text" name="player" style="width:150px"></th>
			</tr>
			<tr>
				<th colspan="2"><input type="submit" value="<?=_getText('adm_bt_search') ?>"></th>
			</tr>
	</table>
</form>
