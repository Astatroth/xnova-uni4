<div id="ptb_data_cont_route" class="ptb_data_cont" style="display: none;">
	<ul class="ptb_tabs">
		<li id="ptb_tab_route">route <span>(<?php echo \Xcms\profiler::$DATA_ROUTES['total']['count'];?>)</span></li>
	</ul>

	<div id="ptb_tab_cont_route" class="ptb_tab_cont">
		<table>
			<thead>
			<tr>
				<th>№</th>
				<th style="width: 150px;">name</th>
				<th style="width: 300px;">action</th>
				<th>params</th>
			</tr>
			</thead>
			<tbody>
			<?php $i = 0; foreach (\Xcms\profiler::$DATA_ROUTES['data'] as $name => $route): ?>
			<tr class="<?=(($i % 2) ? 'odd' : 'even')?>">
				<td class="num"><?php echo ++$i;?></td>
				<td><?php echo $route[0];?></td>
				<td><?php echo $route[1];?></td>
				<td>
					<? if (is_array($route[2])): ?>
					<?php foreach ($route[2] as $k => $v): ?>
						<?php echo $k; ?>: <?php echo $v;?><br/>
						<?php endforeach; ?>
					<? else: ?>
					<?= $route[2]
					; ?>
					<? endif; ?>
				</td>
			</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>
</div>