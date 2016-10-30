<?

use Xcms\core;
use Xcms\strings;

define('INSIDE', true);

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__.'../');

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/core.php');
core::init();
strings::setLang('ru');

include(ROOT_DIR.APP_PATH.'varsGlobal.php');

define('MAX_SLOTS', core::getConfig('maxSlotsInSim', 5));

$techList = array(109, 110, 111, 120, 121, 122);

?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; utf-8">
		<meta http-equiv="content-language" content="ru">
		<title>XNova SIM v1.0</title>
		<link rel="stylesheet" href="/<?=DEFAULT_SKINPATH ?>xnsim.css?v=<?=substr(md5(VERSION), 0, 3) ?>" type="text/css">
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	</head>
<body>
<script type="text/javascript">

	var groups = new Array(100);

	function vis_row(TAG, gID)
	{
		if (!groups[gID] == null || groups[gID] == 0)
			groups[gID] = 1;
		else
			groups[gID] = 0;

		$(TAG).each(function()
		{
			if (this.id == gID)
				this.style.display = (groups[gID] == 0) ? 'none' : '';
		});
	}

	function vis_cols(TAG, PRE, sID, gID)
	{
		var s = parseInt(sID);
		var g = parseInt(gID);

		for (var i = s; i < s + <?=core::getConfig('maxSlotsInSim', 5) ?>; i++)
		{
			if (i < s + g)
			{
				groups[PRE + i] = 0;
				vis_row(TAG, PRE + i);
			}
			else
			{
				groups[PRE + i] = 1;
				vis_row(TAG, PRE + i);
			}
		}
	}

	function opt()
	{
		var txt = "", tstr = "", tkey, tval;
		tkey = new Array();

		$('input[type=text]').each(function()
		{
			if (this.value > 0)
			{
				tstr = this.name;
				tval = tstr.split("-");

				if (tval[2] == undefined)
				{
					if ($("#" + tval[0] + "-" + tval[1] + "-l").length > 0)
					{
						tvar = tval[0];

						tval[0] = parseInt(tval[0].split('gr').join(''));

						if (tkey[tval[0]])
							tkey[tval[0]] += parseInt(tval[1]) + ',' + this.value + '!' + $("#" + tvar + "-" + tval[1] + "-l").val() + ';';
						else
							tkey[tval[0]] = parseInt(tval[1]) + ',' + this.value + '!' + $("#" + tvar + "-" + tval[1] + "-l").val() + ';';
					}
					else
					{
						tval[0] = parseInt(tval[0].split('gr').join(''));

						if (parseInt(tval[1]) < 200)
						{
							if (tkey[tval[0]])
								tkey[tval[0]] += parseInt(tval[1]) + ',' + this.value + ';';
							else
								tkey[tval[0]] = parseInt(tval[1]) + ',' + this.value + ';';
						}
						else
						{
							if (tkey[tval[0]])
								tkey[tval[0]] += parseInt(tval[1]) + ',' + this.value + '!0;';
							else
								tkey[tval[0]] = parseInt(tval[1]) + ',' + this.value + '!0;';
						}
					}
				}
			}
		});

		if (tkey != null)
		{
			if (tkey.length != null)
			{
				for (var i = 0; i < tkey.length; i++)
				{
					if (tkey[i])
						txt += tkey[i] + '|';
					else
						txt += '|';
				}
			}
		}

		$('#result input[name=r]').val(txt);
		$('#result').submit();
	}

	function gclear(gID)
	{
		var tstr = "", tval;

		$('input[type=text]').each(function()
		{
			if (this.name != "")
				tstr = this.name;
			else
				tstr = this.id;

			tval = tstr.split("-");
			tval[0] = parseInt(tval[0].charAt(2));

			if (gID == "all")
				this.value = 0;
			else if (tval[0] == gID)
				this.value = 0;
		});
	}
</script>

<form method="get" action="sim.php" id="result" target="_blank">
	<input type="hidden" name="r" value="">
</form>

<table cellspacing="0" cellpadding="0" border="0" class="maintable">
<tr valign="top" class="main">
<td class="body leftcol main">
<table cellspacing="2" cellpadding="0" align="center">
<thead>
	<tr>
		<th class="spezial"> XNova SIM </th>
		<th colspan="11" class="spezial">
			<select name="Att" SIZE="1" onchange='vis_cols("TD","gr",0,this.value);'>
			<? for ($i = 1; $i <= MAX_SLOTS; $i++): ?>
				<option value="<?=$i ?>"><?=$i ?></option>
			<? endfor; ?>
			</select>

			Исходная ситуация

			<select name="Def" SIZE="1" onchange='vis_cols("TD","gr",<?=MAX_SLOTS ?>,this.value);'>
			<? for ($i = 1; $i <= MAX_SLOTS; $i++): ?>
				<option value="<?=$i ?>"><?=$i ?></option>
			<? endfor; ?>
			</select>
		</th>
	</tr>
	<tr>
		<th align="center" class="typ leftcol_type typ_td"> Тип </th>

		<th class="angreifer leftcol_data"> Ведущий </th>
		<? for ($i = 1; $i < MAX_SLOTS; $i++): ?>
			<td class="angreifer leftcol_data" id='gr<?=$i ?>'>Атакующий&nbsp;<?=$i ?></td>
		<? endfor; ?>

		<th class="verteidiger leftcol_data "> Планета </th>
		<? for ($i = MAX_SLOTS + 1; $i < MAX_SLOTS * 2; $i++): ?>
			<td class="angreifer leftcol_data" id='gr<?=$i ?>'>Защитник&nbsp;<?=($i - MAX_SLOTS) ?></td>
		<? endfor; ?>
	</tr>
</thead>
<tr>
	<td colspan="12" class="spezial" id="tech_td"><b>Исследования и офицеры</b></td>
</tr>
<? foreach ($techList AS $techId): ?>
	<tr align=center>
		<td><b><?=_getText('tech', $techId) ?></b></td>
		<? for ($i = 0; $i < MAX_SLOTS * 2; $i++): ?>
			<td id="gr<?=$i ?>"><input class="number" value="0" type="text" name="gr<?=$i ?>-<?=$techId ?>" maxlength="2"></td>
		<? endfor; ?>
	</tr>
<? endforeach; ?>

<tr>
	<td colspan="12" class="spezial" id="fleet_td"><b>Флот</b></td>
</tr>

<? foreach ($reslist['fleet'] AS $fleetId): ?>
	<tr align=center>
		<td><b><?=_getText('tech', $fleetId) ?></b></td>
		<? for ($i = 0; $i < MAX_SLOTS * 2; $i++): ?>
			<td id="gr<?=$i ?>">
				<? if ($fleetId == 212 && $i < MAX_SLOTS): ?>
					-
				<? else: ?>
					<input class="number" value="0" type="text" name="gr<?=$i ?>-<?=$fleetId ?>" maxlength="7">
				<? endif; ?>
				<? if (in_array($fleetId + 100, $reslist['tech_f'])): ?>
					<input class="lvl" value="0" type="text" id="gr<?=$i ?>-<?=$fleetId ?>-l" maxlength="2">
				<? endif; ?>
			</td>
		<? endfor; ?>
	</tr>
<? endforeach; ?>

<tr>
	<td colspan="12" class="spezial" id="def_td"><b>Защита</b></td>
</tr>
	<? foreach ($reslist['defense'] AS $fleetId): ?>
		<tr align=center>
			<td><b><?=_getText('tech', $fleetId) ?></b></td>
			<? for ($i = 0; $i < MAX_SLOTS * 2; $i++): ?>
				<td id="gr<?=$i ?>">
					<? if ($i < MAX_SLOTS): ?>
						-
					<? else: ?>
						<input class="number" value="0" type="text" name="gr<?=$i ?>-<?=$fleetId ?>" maxlength="7">
						<? if (in_array($fleetId - 50, $reslist['tech_f'])): ?>
							<input class="lvl" value="0" type="text" id="gr<?=$i ?>-<?=$fleetId ?>-l" maxlength="2">
						<? endif; ?>
					<? endif; ?>
				</td>
			<? endfor; ?>
		</tr>
	<? endforeach; ?>


	<tr align="center">
		<td>&nbsp;</td>
		<? for ($i = 0; $i < MAX_SLOTS * 2; $i++): ?>
			<td id='gr<?=$i ?>'><a href='javascript:;' onClick='gclear("<?=$i ?>");'>Очистить</a></td>
		<? endfor; ?>
  	</tr>
    <tr> 
     	<td colspan="12" align="center">
    	<input name="SendBtn" type="submit" id="SendBtn" value="Расчитать!" onclick="opt()">
    </tr>
</table>

<script type="text/javascript">
	vis_cols("TD","gr",0,1);
	vis_cols("TD","gr",<?=MAX_SLOTS ?>,1);
	vis_row("TR","ts");
	vis_row("TR","sp");
	vis_row("TR","gb");
	vis_row("TR","of");
</script>

<center>Made by AlexPro for <a href="http://xnova.su/" target="_blank">XNova Game</a></center>

	<!-- Yandex.Metrika counter -->
	<script type="text/javascript">
	(function (d, w, c) {
		(w[c] = w[c] || []).push(function() {
			try {
				w.yaCounter25961143 = new Ya.Metrika({id:25961143});
			} catch(e) { }
		});

	    var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); };
	    s.type = "text/javascript";
	    s.async = true;
	    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

	    if (w.opera == "[object Opera]") {
	        d.addEventListener("DOMContentLoaded", f, false);
	    } else { f(); }
	})(document, window, "yandex_metrika_callbacks");
	</script>
	<!-- /Yandex.Metrika counter -->

</body>
</html>