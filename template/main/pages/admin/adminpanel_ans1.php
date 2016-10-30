<br>
<table width=650>
	<tr>
		<td width=50% style="vertical-align:top">
			<table width=100% align="center">
				<tbody>
				<tr>
					<td class="c" colspan="2"><?=_getText('adm_panel_mnu') ?></td>
				</tr>
				<tr>
					<th align="center" class=""><?=_getText('adm_frm1_id') ?></th>
					<th align="center" class=""><?=$parse['answer1'] ?></th>
				</tr>
				<tr>
					<th align="center" class=""><?=_getText('adm_frm1_name') ?></th>
					<th align="center" class=""><?=$parse['answer2'] ?></th>
				</tr>
				<tr>
					<th align="center" class=""><?=_getText('adm_frm1_ip') ?></th>
					<th align="center" class=""><?=$parse['answer3'] ?></th>
				</tr>
				<tr>
					<th align="center" class=""><?=_getText('adm_frm1_mail') ?></th>
					<th align="center" class=""><?=$parse['answer4'] ?></th>
				</tr>
				<tr>
					<th align="center" class=""><?=_getText('adm_frm1_acc') ?></th>
					<th align="center" class=""><font color="red"><?=$parse['answer5'] ?></font></th>
				</tr>
				<tr>
					<th align="center" class=""><?=_getText('adm_frm1_gen') ?></th>
					<th align="center" class=""><?=$parse['answer6'] ?></th>
				</tr>
				</tbody>
			</table>

			<?=$parse['adm_sub_form2'] ?>
			<?=$parse['adm_sub_form3'] ?>
		</td>
		<td style="vertical-align:top">
			<?=$parse['adm_sub_form4'] ?>
		</td>
	</tr>
	<tr>
		<td colspan=2><?=$parse['adm_sub_form5'] ?></td>
	</tr>
</table>
