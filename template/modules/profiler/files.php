<div id="ptb_data_cont_files" class="ptb_data_cont" style="display: none;">
	<ul class="ptb_tabs">
		<li id="ptb_tab_files">files <span>(<?php echo \Xcms\profiler::$DATA_INC_FILES['total']['count'];?>)</span></li>
	</ul>
	<div id="ptb_tab_cont_files" class="ptb_tab_cont">
		<table>
			<thead>
			<tr>
				<th>№</th>
				<th>file</th>
				<th style="width:60px;">size</th>
				<th style="width:50px;">lines</th>
				<th style="width:130px;">last modified</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach (\Xcms\profiler::$DATA_INC_FILES['data'] as $i => $file): ?>
			<tr class="<?=(($i % 2) ? 'odd' : 'even')?>">
				<td class="num"><?php echo $i + 1;?></td>
				<td>
					<span style="color: #4a4a4a;"><?=$file['name']?></span>
				</td>
				<td class="tRight"><?php echo \Xcms\profiler::formatMemory($file['size']);?></td>
				<td class="tRight"><?php echo number_format($file['lines']);?></td>
				<td class="tCenter" style="color: #4a4a4a;">
					<?php echo date("Y.m.d", $file['lastModified']);?>&nbsp;&nbsp;<?php echo date("H:i:s", $file['lastModified']);?>
				</td>
			</tr>
				<?php endforeach;?>
			<tr class="total">
				<th></th>
				<th>total <?php echo \Xcms\profiler::$DATA_INC_FILES['total']['count'];?> files</th>
				<th class="tRight"><?php echo \Xcms\profiler::formatMemory(\Xcms\profiler::$DATA_INC_FILES['total']['size']);?></th>
				<th class="tRight"><?php echo number_format(\Xcms\profiler::$DATA_INC_FILES['total']['lines']);?></th>
				<th></th>
			</tr>
			</tbody>
		</table>
	</div>
</div>