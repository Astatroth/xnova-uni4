<br>
<table width="750">
	<tbody>
	<tr>
		<td class="c">ID</td>
		<td class="c">Состав флота</td>
		<td class="c">Задание</td>
		<td class="c">Владелец</td>
		<td class="c">Планета-дом</td>
		<td class="c">Время отправления</td>
		<td class="c">Игрок-цель</td>
		<td class="c">Планета-цель</td>
		<td class="c">Время на орбите</td>
		<td class="c">Время прибытия</td>
	</tr>
	<? foreach ($flt_table AS $parse): ?>
	<tr>
		<th><?=$parse['Id'] ?></th>
		<th><?=$parse['Fleet'] ?></th>
		<th><?=$parse['Mission'] ?></th>
		<th><?=$parse['St_Owner'] ?></th>
		<th><?=$parse['St_Posit'] ?></th>
		<th><?=$parse['St_Time'] ?></th>
		<th><?=$parse['En_Owner'] ?></th>
		<th><?=$parse['En_Posit'] ?></th>
		<th><?=$parse['St_Time'] ?></th>
		<th><?=$parse['En_Time'] ?></th>
	</tr>
		<? endforeach; ?>
	</tbody>
</table>
