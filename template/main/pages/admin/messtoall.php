<form action="?set=admin&mode=messall&modes=change" method="post">
	<table class="table">
		<tr>
			<td class="c" colspan="2">Отправить сообщение всем игрокам</td>
		</tr>
		<tr>
			<th width="150">Тема сообщения</th>
			<th><input name="temat" maxlength="100" style="width: 99%" value="Администрация" type="text"></th>
		</tr>
		<tr>
			<th colspan="2" class="nopadding">
				<div id="editor"></div>
				<textarea name="tresc" id="text" rows="10"></textarea>

				<div id="showpanel" style="display:none">
					<table class="table">
						<tr>
							<td class="c"><b>Предварительный просмотр</b></td>
						</tr>
						<tr>
							<td class="b"><span id="showbox"></span></td>
						</tr>
					</table>
				</div>
				<script type="text/javascript">edToolbar('text');</script>
			</th>
		</tr>
		<tr>
			<th colspan="2"><input value="Отправить" type="submit"></th>
		</tr>
	</table>
</form>
