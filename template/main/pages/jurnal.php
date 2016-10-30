<br>
<center>
	<form action="?set=logs" method="post">
		<center>
			<table width="100%" border="0" cellpadding="0" cellspacing="1">
				<tr height="20">
					<td colspan="1" class="c" width=15%>
						<center>Миссия:</center>
					</td>
					<td colspan="1" class="c" width=15%>
						<center>
							<select name="journal" onChange="javascript:document.forms[1].submit()"><?=$parse['type'] ?>
							</select>
					</td>
					<td colspan="1" class="c" width=10%>
						<center>Время:</center>
					</td>
					<td colspan="1" class="c" width=10%>
						<center>
							<select name="days" onChange="javascript:document.forms[1].submit()"><?=$parse['day'] ?>
							</select>
					</td><?=$parse['count'] ?>
				</tr>
			</table>
		</center>
	</form>
</center>
<?= $parse['log'] ?>