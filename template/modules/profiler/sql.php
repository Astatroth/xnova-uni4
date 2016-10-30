<div id="ptb_data_cont_sql" class="ptb_data_cont" style="display: none;">
	<?php if (empty(\Xcms\profiler::$DATA_SQL)): ?>
	<ul class="ptb_tabs">
		<li id="ptb_tab_sql_default">default <span>(0)</span></li>
	</ul>
	<div id="ptb_tab_cont_sql_default" class="ptb_tab_cont">
		<table>
			<tr>
				<td colspan="5" class="empty">—</td>
			</tr>
		</table>
	</div>
	<?php else: ?>
	<ul class="ptb_tabs">
		<?php foreach (\Xcms\profiler::$DATA_SQL as $k => $v): ?>
		<li id="ptb_tab_sql<?php echo $k;?>"><?php echo $k;?> <span>(<?php echo $v['total']['count'];?>)</span></li>
		<?php endforeach;?>
	</ul>
	<?php foreach (\Xcms\profiler::$DATA_SQL as $k => $group): ?>
		<div id="ptb_tab_cont_sql<?php echo $k;?>" class="ptb_tab_cont">
			<table>
				<thead>
				<tr>
					<th>№</th>
					<th>query</th>
					<th style="width:50px;">rows</th>
					<th style="width:80px;">time</th>
					<th style="width:70px;">memory</th>
				</tr>
				</thead>
				<tbody>
					<?php foreach ($group['data'] as $i => $v): ?>
				<tr class="<?=(($i % 2) ? 'odd' : 'even')?>">
					<td class="num"><?php echo $i + 1;?></td>
					<td>
						<?php echo $v['sql'];?>
					</td>
					<td class="tCenter"><?php echo $v['rows'];?></td>
					<td class="tRight graph">
						<div class="val"><?php echo \Xcms\profiler::formatTime($v['time']);?></div>
						<div class="line" style="width:<?php echo round($v['time'] / $group['total']['time'] * 100);?>%;"></div>
					</td>
					<td class="tRight graph">
						<div class="val"><?php echo \Xcms\profiler::formatMemory($v['memory']);?></div>
						<div class="line" style="width:<?php echo round($v['memory'] / $group['total']['memory'] * 100);?>%;"></div>
					</td>
				</tr>
					<?php endforeach;?>
				<tr class="total">
					<td></td>
					<td>всего <?php echo $group['total']['count'];?> запросов</td>
					<td></td>
					<td class="tRight"><?php echo \Xcms\profiler::formatTime($group['total']['time']);?></td>
					<td class="tRight"><?php echo \Xcms\profiler::formatMemory($group['total']['memory']);?></td>
				</tr>
				</tbody>
			</table>
		</div>
		<?php endforeach; ?>
	<?php endif;?>
</div>