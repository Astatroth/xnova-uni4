<script type="text/javascript" src="<?=RPATH ?>scripts/universe_full.js"></script>
<script type="text/javascript">

	var planetX = <?=$planetX ?>;
	var planetY = <?=$planetY ?>;

	var galaxySize = 500;
	var mapsizeX = 19;
	var mapsizeY = 19;
	var minimapX = 0;
	var minimapY = 0;

	function buildMap(data) {
		var temp = data.split("|");
		var conf = temp[0];
		var map = temp[1];
		conf = conf.split(":");
		map = map.split(":");

		var x_size = conf[0];
		var y_size = conf[1];
		var x_start = conf[2];
		var y_start = conf[3];

		var own = "<div class='f_own'>%<img src='image/map/fleets/own.png' alt='.'></div>";
		var other = "<div class='f_other'>%<img src='image/map/fleets/no.png' alt='.'></div>";
		var enem = "<div class='f_enem'><img src='image/map/fleets/enemy.png' alt='.'></div>";
		var all = "<div class='f_all'><img src='image/map/fleets/ally.png' alt='.'></div>";
		var battle = "<div style='m_bat'><img src='image/map/battles/0.png' alt='.'></div>";//FIXME
		var output = "<table cellspacing=0 cellpadding=0 id='tablemap'>";
		var p = 0;
		var coordX = 0;
		var coordY = 0;
		var cell = '';
		var back = '';
		var turn = '';
		var s = '';
		var params = '';
		var fleets = '';

		for (var y = 0; y <= y_size; y++) {
			output += "<tr>";

			for (var x = 0; x <= x_size; x++) {
				coordX = (parseInt(x_start) + x);
				coordY = (parseInt(y_start) + y);

				if (coordX > galaxySize) coordX -= galaxySize;
				if (coordX < 1) coordX += galaxySize;
				if (coordY > galaxySize) coordY -= galaxySize;
				if (coordY < 1) coordY += galaxySize;

				if (x == 0 && y == 0) {
					output += "<td>XY</td>";
				}
				if (x > 0 && y == 0) {
					output += "<td class=coord-b>" + coordX + "</td>";
				}
				if (x == 0 && y > 0) {
					output += "<td class=coord-r>" + coordY + "</td>";
				}

				if (x > 0 && y > 0) {
					cell = map[p];
					p++;
					cell = cell.split(",");
					back = cell[0];
					turn = cell[2];
					params = parseInt(cell[1]);
					params = params.toString(2);

					while (params.length < 8) {
						params = '0' + params;
					}

					//alert(params);

					if (back == '0') back = '';
					if (back == '1') back = 'no';
					if (back == '2') back = 'own';
					if (back == '3') back = 'enemy';
					if (back == '4') back = 'ally';
					if (back == '5') back = 'geo_no';
					if (back == '6') back = 'geo_good';
					if (back == '7') back = 'geo_bad';
					if (back == '8') back = 'never';
					if (back == '9') back = '';


					s = '';
					if (params.charAt(0) == '1') s = 'style="border-color: yellow;"';

					fleets = '';

					if (params.charAt(3) == '1') {
						if (turn > 0) {
							fleets = fleets + own.replace('%', '<div class="turn">' + turn + '</div>');
						} else {
							fleets = fleets + own.replace('%', '');
						}
					}
					if (params.charAt(4) == '1') {
						if (turn > 0) {
							fleets = fleets + other.replace('%', '<div class="turn">' + turn + '</div>');
						} else {
							fleets = fleets + other.replace('%', '');
						}
					}
					if (params.charAt(5) == '1') fleets = fleets + all;
					if (params.charAt(6) == '1') fleets = fleets + enem;
					if (params.charAt(7) == '1') fleets = fleets + battle;
					//output=output+"<td class='map $l$r'> <div class='map_div $b' style='$col' id='map_$i.$j'> &nbsp; $own $other $ally $enemy $battle</div></td>";

					output = output + "<td class='map l" + params.charAt(1) + "r" + params.charAt(2) + "'> <div turn=" + turn + " class='map_div " + back + "' " + s + " id='map_" + coordX + "." + coordY + "'> " + fleets + "</div></td>";
				}
			}
			output += "</tr>";
		}

		output += "</table>";

		return output;
	}

	$(document).ready(function () {
		$('#galaxySelector').html(PrintSelector([]));
		$('#galaxyMap').html(buildMap('15:15:<?=$planetX - 8 ?>:<?=$planetY - 8 ?>|<?=$planets ?>'));
	});
</script>

<div style="margin: 10px auto;" id="galaxySelector"></div>
<div style="padding:10px 0 10px 0;background:url(/images/galaxy/map.jpg)" id="galaxyMap"></div>

<style>
div.popup2 {
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border: 1px solid grey;
	background-color: #10477d;
	font-size: 11px;
	padding: 5px;
}

div.popup {
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border: 1px solid grey;
	background-color: dodgerblue;
	opacity: 0.6;
	filter: alpha(opacity = 60);
	width: 250px;
	font-size: 11px;
	padding: 5px;
}

div.popup input {
	border: 1px solid grey;
	background: white;
	color: black;
	font-size: 10px;
	margin-left: 5px;
	margin-bottom: 5px;
}

.turn {
	position: absolute;
	margin-top: 2px;
	margin-left: 4px;
	font-size: 8px;
	font-weight: bold;
	color: white;
}

div.f_own {
	float: left;
}

div.f_other {
	float: right;
}

div.f_enem {
	float: left;
}

div.f_all {
	float: right;
}

div.m_bat {
	float: left;
}

div.map_div {
	border: 1px solid black;
	width: 32px;
	height: 32px;
	display: block;
	position: static;
	text-align: left;
	vertical-align: top;
}

div.map_div:hover {
	width: 32px;
	height: 32px;
	border: 1px solid blue;
}

div.map_div.own {
	background-image: url('/images/galaxy/own.png');
}

div.map_div.enemy {
	background-image: url('/images/galaxy/enemy.png');
}

div.map_div.ally {
	background-image: url('/images/galaxy/ally.png');
}

div.map_div.no {
	background-image: url('/images/galaxy/no.png');
}

div.map_div.geo_no {
	background-image: url('/images/galaxy/geo_no.png');
}

div.map_div.geo_good {
	background-image: url('/images/galaxy/geo_good.png');
}

div.map_div.geo_bad {
	background-image: url('/images/galaxy/geo_bad.png');
}

div.map_div.never {
	background-image: url('/images/galaxy/never.png');
}

div.map_div.nnew {
	background-image: url('/images/galaxy/geo_no.png');
}

td.map.l0r0 {
	border-right-color: #555555;
	border-right-width: 1px;
	border-right-style: solid;
	border-bottom-color: #555555;
	border-bottom-width: 1px;
	border-bottom-style: solid;
}

td.map.l1r0 {
	border-right-color: #555555;
	border-right-width: 1px;
	border-right-style: dotted;
	border-bottom-color: #555555;
	border-bottom-width: 1px;
	border-bottom-style: solid;
}

td.map.l0r1 {
	border-right-color: #555555;
	border-right-width: 1px;
	border-right-style: solid;
	border-bottom-color: #555555;
	border-bottom-width: 1px;
	border-bottom-style: dotted;
}

td.map.l1r1 {
	border-right-color: #555555;
	border-right-width: 1px;
	border-right-style: dotted;
	border-bottom-color: #555555;
	border-bottom-width: 1px;
	border-bottom-style: dotted;
}

div.map_div div img {
	width: 12px;
}

td.map {
	background-color: transparent;
	margin: 0px;
	padding: 0px;
	width: 32px;
	height: 32px;
	font-family: Verdana;
	font-size: 6px;
	opacity: .8;
	filter: alpha(opacity = 80);
	zoom: 1;
	cursor: pointer;
}

table.map_main {
	background-color: transparent;
	margin: 0px;
	padding: 0px;
	border: 1px double #e5e5e5;
}

td.map_menu {
	background-color: black;
	margin: 0px;
	padding: 0px;
	border: 2px double #333333;
	vertical-align: top;
	text-align: left;
	height: 620px;
	opacity: 0.8;
	filter: alpha(opacity = 80);
	zoom: 1;

}

.menu_item_big {
	font-size: 12px;
	font-weight: bold;
	cursor: pointer;
}

.menu_item {
	font-size: 11px;
	font-weight: bold;
	cursor: pointer;
}

.menu_item_medium {
	font-size: 11px;
	font-weight: bold;
	cursor: pointer;
}

.menu_item_small {
	font-size: 10px;
	font-weight: bold;
	cursor: pointer;
}

.menu_div {
	width: 100%;
	border-bottom: 1px solid #333333;
	padding: 2px;
	padding-bottom: 5px;
}

.unit {
	text-align: center;
	border-bottom: 1px solid gray;
}

.coord-b {
	font-size: 10px;
	color: grey;
	text-align: center;
	vertical-align: middle;
	border-bottom: 1px solid #333333;
}

.coord-r {
	font-size: 10px;
	color: grey;
	text-align: center;
	vertical-align: middle;
	border-right: 1px solid #333333;
}

.m_link {
	font-size: 12px;
	cursor: pointer;
}

.m_link:hover {
	font-size: 12px;
	color: white;
	cursor: pointer;
}

.m_link:visited {
	font-size: 12px;
	cursor: pointer;
}

.corr {
	background: transparent url(../image/korr.gif) repeat scroll 0 2px;
	margin-top: 27px;
	margin-left: -1px;
	height: 2px;
	position: absolute;
}

.rob {
	background: transparent url(../image/korr.gif) repeat scroll 0 3px;
	margin-top: 30px;
	margin-left: -1px;
	height: 2px;
	position: absolute;
}

.lvl5 {
	width: 2px;
}

.lvl10 {
	width: 3px;
}

.lvl15 {
	width: 4px;
}

.lvl20 {
	width: 5px;
}

.lvl25 {
	width: 6px;
}

.lvl30 {
	width: 7px;
}

.lvl35 {
	width: 8px;
}

.lvl40 {
	width: 10px;
}

.lvl45 {
	width: 12px;
}

.lvl50 {
	width: 14px;
}

.lvl55 {
	width: 16px;
}

.lvl60 {
	width: 18px;
}

.lvl65 {
	width: 20px;
}

.lvl70 {
	width: 22px;
}

.lvl75 {
	width: 24px;
}

.lvl80 {
	width: 26px;
}

.lvl85 {
	width: 28px;
}

.lvl90 {
	width: 30px;
}

.lvl95 {
	width: 32px;
}

.lvl100 {
	width: 34px;
}
</style>