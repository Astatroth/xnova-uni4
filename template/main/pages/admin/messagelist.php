<form action="?set=admin&mode=messagelist" method="post">
	<input type="hidden" name="curr" value="<?=$parse['mlst_data_page'] ?>">
	<input type="hidden" name="pmax" value="<?=$parse['mlst_data_pagemax'] ?>">
	<input type="hidden" name="sele" value="<?=$parse['mlst_data_sele'] ?>">
	<table class="table">
		<tr>
			<td class="c">
				<div align="center"><input type="submit" name="prev" value="<?=_getText('mlst_hdr_prev') ?>"></div>
			</td>
			<td class="c">
				<div align="center"><?=_getText('mlst_hdr_page') ?></div>
			</td>
			<td class="c">
				<div align="center">
					<select name="page" onchange="submit();">
						<? for ($cPage = 1; $cPage <= $parse['mlst_data_pagemax']; $cPage++): ?>
							<option value="<?=$cPage ?>" <?=(($parse['mlst_data_page'] == $cPage) ? "selected" : "") ?>><?=$cPage ?>/<?=$parse['mlst_data_pagemax'] ?></option>
						<? endfor; ?>
					</select></div>
			</td>
			<td class="c">
				<div align="center"><input type="submit" name="next" value="<?=_getText('mlst_hdr_next') ?>"/></div>
			</td>
		</tr>
		<tr>
			<td class="c">owner: <input type="text" name="userid" size="7" value="<?=is($parse, 'userid') ?>"/> sender: <input type="text" name="userid_s" size="7" value="<?=is($parse, 'userid_s') ?>"/><input type="submit" name="usersearch" value="По id"/>
			</td>
			<td class="c">
				<div align="center"><?=_getText('mlst_hdr_type') ?></div>
			</td>
			<td class="c">
				<div align="center">
					<select name="type" onchange="submit();">
						<option value="1"<?=(($parse['mlst_data_sele'] == 1) ? " SELECTED" : "") ?>><?=_getText('mlst_mess_typ__1') ?></option>
						<option value="2"<?=(($parse['mlst_data_sele'] == 2) ? " SELECTED" : "") ?>><?=_getText('mlst_mess_typ__2') ?></option>
						<option value="3"<?=(($parse['mlst_data_sele'] == 3) ? " SELECTED" : "") ?>><?=_getText('mlst_mess_typ__3') ?></option>
						<option value="4"<?=(($parse['mlst_data_sele'] == 4) ? " SELECTED" : "") ?>><?=_getText('mlst_mess_typ__4') ?></option>
						<option value="5"<?=(($parse['mlst_data_sele'] == 5) ? " SELECTED" : "") ?>><?=_getText('mlst_mess_typ__5') ?></option>
						<option value="6"<?=(($parse['mlst_data_sele'] == 0) ? " SELECTED" : "") ?>><?=_getText('mlst_mess_typ__6') ?></option>
					</select></div>
			</td>
			<td class="c">&nbsp;</td>
		</tr>
		<tr>
			<td class="c">
				<div align="center"><input type="submit" name="delsel" value="<?=_getText('mlst_bt_delsel') ?>"/></div>
			</td>
			<td class="c">
				<div align="center"><?=_getText('mlst_hdr_delfrom') ?></div>
			</td>
			<td class="c">
				<div align="center"><input type="text" name="selday" size="3"/> <input type="text" name="selmonth" size="3"/> <input type="text" name="selyear" size="6"/></div>
			</td>
			<td class="c">
				<div align="center"><input type="submit" name="deldat" value="<?=_getText('mlst_bt_deldate') ?>"/></div>
			</td>
		</tr>
		<tr>
			<th colspan="4">
				<table width="100%" border="0" cellspacing="1" cellpadding="1">
					<tr align="center" valign="middle">
						<th width="30" class="c">&nbsp;</th>
						<th class="c"><?=_getText('mlst_hdr_time') ?></th>
						<th class="c"><?=_getText('mlst_hdr_from') ?></th>
						<th class="c"><?=_getText('mlst_hdr_to') ?></th>
						<th class="c" width="300"><?=_getText('mlst_hdr_text') ?></th>
					</tr>
					<? foreach ($parse['mlst_data_rows'] AS $list): ?>
					<tr>
						<th><input type="checkbox" name="sele_mes[<?=$list['mlst_id'] ?>]"/></th>
						<th><?=$list['mlst_time'] ?></th>
						<th><?=$list['mlst_from'] ?></th>
						<th><?=$list['mlst_to'] ?></th>
						<th><?=$list['mlst_text'] ?></th>
					</tr>
					<? endforeach; ?>
				</table>
			</th>
		</tr>
	</table>
</form>
