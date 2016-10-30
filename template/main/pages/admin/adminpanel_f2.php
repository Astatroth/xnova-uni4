<br>
<form action="?set=admin&mode=paneladmina" method="post">
	<table width="300">
			<tr>
				<td class="c" colspan="6"><?=_getText('adm_search_ip') ?></td>
			</tr>
			<tr>
				<th><?=_getText('adm_ip') ?></th>
				<th><input type="text" name="ip" style="width:150px"></th>
			</tr>
			<tr>
				<th colspan="2"><input type="submit" value="<?=_getText('adm_bt_search') ?>"></th>
			</tr>
	</table>
	<input type="hidden" name="result" value="ip_search">
</form>
