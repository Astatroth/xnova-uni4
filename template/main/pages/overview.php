<? if ($parse['bonus']): ?>
	<table class="table">
		<tr>
			<td class="c">Ежедневный бонус</td>
		</tr>
		<tr>
			<th>
				Сейчас вы можете получить по <b><?=($parse['bonus_multi'] * 500 * \Xnova\system::getResourceSpeed()) ?></b> Металла, Кристаллов и Дейтерия.<br>
				Каждый день размер бонуса будет увеличиваться.<br>
				<br>
				<a href="?set=overview&mode=bonus">ПОЛУЧИТЬ БОНУС</a><br><br>

				Помоги проекту, поделись им с друзьями!
				<script type="text/javascript" src="https://yandex.st/share/share.js" charset="utf-8"></script>
				<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareTitle="<?=\Xcms\Core::getConfig('game_name') ?>" data-yashareLink="http://uni<?=UNIVERSE ?>.xnova.su/" data-yashareQuickServices="vkontakte,facebook,twitter,odnoklassniki,moimir,gplus" data-yashareTheme="counter" data-yashareType="small"></div>
			</th>
		</tr>
	</table>
	<div class="separator"></div>
<? endif; ?>

<div class="block">
	<div class="title">
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<?=_getText('type_planet', $parse['planet_type']) ?> "<?=$parse['planet_name'] ?>"
				<a href="?set=galaxy&r=0&galaxy=<?=$parse['galaxy_galaxy'] ?>&system=<?=$parse['galaxy_system'] ?>">[<?=$parse['galaxy_galaxy'] ?>:<?=$parse['galaxy_system'] ?>:<?=$parse['galaxy_planet'] ?>]</a>
				<a href="?set=overview&mode=renameplanet" title="Редактирование планеты">(изменить)</a>
			</div>
			<div class="separator visible-xs"></div>
			<div class="col-xs-12 col-sm-6">
				<div id="clock" class="pull-right"><?=datezone("d-m-Y H:i:s", time()) ?></div>
				<script type="text/javascript">UpdateClock();</script>
				<div class="clearfix visible-xs"></div>
			</div>
		</div>
	</div>
	<div class="content">
		<? if (count($parse['fleet_list']) > 0): ?>
			<table class="table">
				<? foreach ($parse['fleet_list'] as $id => $list): ?>
					<tr class="<?=$list['fleet_status'] ?>">
					<th width="80">
						<div id="bxx<?=$list['fleet_order'] ?>" class="z"><?=$list['fleet_count_time'] ?></div>
						<font color="lime"><?=$list['fleet_time'] ?></font>
					</th>
					<th class="text-left" colspan="3">
						<span class="<?=$list['fleet_status'] ?> <?=$list['fleet_prefix'] ?><?=$list['fleet_style'] ?>"><?=$list['fleet_descr'] ?></span>
					</th>
					<?= $list['fleet_javas'] ?>
					</tr>
				<? endforeach; ?>
			</table>
			<div class="separator"></div>
		<? endif; ?>
		<? $m = \Xcms\Core::getConfig('newsMessage', ''); ?>
		<? if ($m != ''): ?>
			<div class="row">
				<div class="col-xs-3"><img src="<?=RPATH ?><?=DPATH ?>img/warning.png" align="absmiddle" alt=""></div>
				<div class="col-xs-9">
					<?=$m ?>
				</div>
			</div>
			<div class="separator"></div>
		<? endif; ?>
		<div class="row overview">
			<div class="col-sm-4">
				<div class="row">
					<div class="col-sm-10 col-xs-5">
						<div class="planet-image">
							<a href="?set=overview&mode=renameplanet">
								<img src="<?=RPATH ?><?=DPATH ?>planeten/<?=$parse['planet_image'] ?>.jpg" alt="">
							</a>
							<? if ($parse['moon_img'] != ''): ?>
								<div class="moon-image"><?=$parse['moon_img'] ?></div>
							<? endif; ?>
						</div>

						<div class="separator"></div>

						<div style="border: 1px solid rgb(153, 153, 255); width: 100%; margin: 0 auto;">
							<div id="CaseBarre" style="background-color: #<?=($parse['case_pourcentage'] > 80 ? 'C00000' : ($parse['case_pourcentage'] > 60 ? 'C0C000' : '00C000')) ?>; width: <?=$parse['case_pourcentage'] ?>%;  margin: 0 auto; text-align:center;">
								<font color="#000000"><b><?=$parse['case_pourcentage'] ?>%</b></font></div>
						</div>

						<br><center><a href="javascript:" onclick="snow()">Добавить снега</a></center>

						<? if (\Xcms\Core::getConfig('noob', 0) == 1): ?>
							<div class="separator"></div>
							<img src="<?=RPATH ?><?=DPATH ?>img/warning.png" align="absmiddle" alt="">
							<span style="font-weight:normal;"><span class="positive">Активен режим ускорения новичков.</span><br>Режим будет деактивирован после достижения 1000 очков.</span>
						<? endif; ?>
					</div>
					<div class="col-sm-2 col-xs-7">
						<div class="row">
							<? foreach ($parse['officiers'] AS $oId => $oTime): ?>

									<div class="col-xs-3 col-sm-12">
										<a href="?set=officier" class="tooltip" data-tooltip-content="<?=_getText('tech', $oId) ?><br><? if ($oTime > time()): ?>Нанят до <font color=lime><?=datezone("d.m.Y H:i", $oTime) ?></font><? else: ?><font color=lime>Не нанят</font><? endif; ?>"><span class="officier of<?=$oId ?><?=($oTime > time() ? '_ikon' : '') ?>"></span></a>
									</div>

							<? endforeach; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="separator hidden-sm hidden-md hidden-lg"></div>
				<table class="table">
					<tr>
						<td class="c" colspan="2">Диаметр</td>
					</tr>
					<tr>
						<th colspan="2"><?=\Xcms\Strings::pretty_number($parse['planet_diameter']) ?> км</th>
					</tr>
					<tr>
						<td class="c" colspan="2">Занятость</td>
					</tr>
					<tr>
						<th colspan="2"><a title="Занятость полей"><?=$parse['planet_field_current'] ?></a> / <a title="Максимальное количество полей"><?=$parse['planet_field_max'] ?></a> поля</th>
					</tr>
					<tr>
						<td class="c" colspan="2">Температура</td>
					</tr>
					<tr>
						<th colspan="2">от. <?=$parse['planet_temp_min'] ?>&deg;C до <?=$parse['planet_temp_max'] ?>&deg;C</th>
					</tr>
					<tr>
						<td class="c" colspan="2">
							Обломки
							<? if ($parse['get_link']): ?>
								(<a href="#" onclick="QuickFleet(8, <?=$parse['galaxy_galaxy'] ?>, <?=$parse['galaxy_system'] ?>, <?=$parse['galaxy_planet'] ?>, 2)">переработать</a>)
							<? endif; ?>
						</td>
					</tr>
					<tr>
						<th colspan="2" class="doubleth">
							<img src="/skins/default/images/s_metall.png" alt="" align="absmiddle" class="tooltip" data-tooltip-content="Металл">
							<?=\Xcms\Strings::pretty_number($parse['metal_debris']) ?>
							/
							<img src="/skins/default/images/s_kristall.png" alt="" align="absmiddle" class="tooltip" data-tooltip-content="Кристалл">
							<?=\Xcms\Strings::pretty_number($parse['crystal_debris']) ?>
						</th>
					</tr>
					<tr>
						<td class="c" colspan="2">Бои</td>
					</tr>
					<tr>
						<th colspan="2">
							<img src="<?=RPATH ?>images/wins.gif" alt="" align="absmiddle" class="tooltip" data-tooltip-content="Победы">
							<?=$parse['raids_win'] ?>
							&nbsp;&nbsp;
							<img src="<?=RPATH ?>images/losses.gif" alt="" align="absmiddle" class="tooltip" data-tooltip-content="Поражения">
							<?=$parse['raids_lose'] ?>
						</th>
					</tr>
					<tr>
						<th colspan="2">Фракция: <a href="?set=race"><?=$parse['race'] ?></a></th>
					</tr>
					<tr>
						<th colspan="2"><a href="?set=refers"><? if (\Xcms\Core::getConfig('socialIframeView', 0)): ?>Рефералы<? else: ?>http://<?=$_SERVER['HTTP_HOST'] ?>/?<?=$parse['user_id'] ?><? endif; ?></a> [<?=$parse['links'] ?>]</th>
					</tr>
				</table>
			</div>
			<div class="col-sm-4">
				<div class="separator hidden-sm hidden-md hidden-lg"></div>
				<table class="table">
					<tr>
						<td class="c col-sm-5 col-xs-6">Игрок:</td>
						<td class="c col-sm-7 col-xs-6" style="word-break: break-all;"><a href="?set=players&id=<?=$parse['user_id'] ?>" class="window popup-user"><?=$parse['user_username'] ?></a></td>
					</tr>
					<tr>
						<th>Постройки:</th>
						<th><span class="positive"><?=\Xcms\Strings::pretty_number($parse['user_points']) ?></span></th>
					</tr>
					<tr>
						<th>Флот:</th>
						<th><span class="positive"><?=\Xcms\Strings::pretty_number($parse['user_fleet']) ?></span></th>
					</tr>
					<tr>
						<th>Оборона:</th>
						<th><span class="positive"><?=\Xcms\Strings::pretty_number($parse['user_defs']) ?></span></th>
					</tr>
					<tr>
						<th>Наука:</th>
						<th><span class="positive"><?=\Xcms\Strings::pretty_number($parse['player_points_tech']) ?></span></th>
					</tr>
					<tr>
						<th>Всего:</th>
						<th><span class="positive"><?=\Xcms\Strings::pretty_number($parse['total_points']) ?></span></th>
					</tr>
					<tr>
						<th>Место:</th>
						<th><a href="?set=stat&range=<?=$parse['user_rank'] ?>"><?=$parse['user_rank'] ?></a> <span title="Изменение места в рейтинге">(<?=($parse['ile'] >= 1 ? '<font color="lime">+'.$parse['ile'].'</font>' : ($parse['ile'] < 0 ? '<font color="red">'.$parse['ile'].'</font>' : '<font color="lightblue">'.$parse['ile'].'</font>')) ?>)</span></th>
					</tr>
					<tr>
						<td class="c" colspan="2">Промышленный уровень</td>
					</tr>
					<tr>
						<th colspan="2"><?=$parse['lvl_minier'] ?> из 100</th>
					</tr>
					<tr>
						<th colspan="2"><?=\Xcms\Strings::pretty_number($parse['xpminier']) ?> / <?=\Xcms\Strings::pretty_number($parse['lvl_up_minier']) ?> exp</th>
					</tr>
					<tr>
						<td class="c" colspan="2">Военный уровень</td>
					</tr>
					<tr>
						<th colspan="2"><?=$parse['lvl_raid'] ?> из 100</th>
					</tr>
					<tr>
						<th colspan="2"><?=\Xcms\Strings::pretty_number($parse['xpraid']) ?> / <?=\Xcms\Strings::pretty_number($parse['lvl_up_raid']) ?> exp</th>
					</tr>
				</table>
			</div>
		</div>
		<div class="clearfix"></div>
		<? if (isset($parse['build_list']) && is_array($parse['build_list']) && count($parse['build_list']) > 0): ?>
			<div class="separator"></div>
			<table class="table">
				<? foreach ($parse['build_list'] as $id => $list): ?>
					<tr class="flight">
						<th class="col-xs-4 col-sm-1" <?=($id > 0 ? 'style="border-top:0;"' : '')?>>
							<div id="build<?=$id ?>" class="z"><?=($list[0] - time()) ?></div>
							<script type="text/javascript">FlotenTime('build<?=$id ?>', <?=($list[0] - time()) ?>);</script>
							<font color="lime" class="visible-xs"><?=datezone("d.m H:i:s", $list[0]) ?></font>
						</th>
						<th class="col-sm-11 col-xs-8 text-left" style="<?=($id > 0 ? 'border-top:0;' : '')?>">
							<span class="pull-left"><span class="flight owndeploy"><?=$list[1] ?></span></span>
							<font color="lime" class="pull-right hidden-xs"><?=datezone("d.m H:i:s", $list[0]) ?></font>
						</th>
					</tr>
				<? endforeach; ?>
			</table>
		<? endif; ?>
	</div>
</div>

<? if (is_array($parse['anothers_planets']) && count($parse['anothers_planets'])): ?>
	<div class="separator"></div>
	<table class="table anotherPlanets">
		<tr>
			<? foreach ($parse['anothers_planets'] AS $i => $UserPlanet): ?>
				<th width="20%" valign="top">
					<a href="?set=overview&amp;cp=<?= $UserPlanet['id'] ?>&amp;re=0" title="<?= $UserPlanet['name'] ?>"><img src="<?=RPATH ?><?=DPATH ?>planeten/small/s_<?= $UserPlanet['image'] ?>.jpg" height="50" width="50" alt=""></a>
					<br><?=$UserPlanet['name'] ?>
				</th>
				<? if ($i > 0 && $i%5 == 0): ?></tr><tr><? endif; ?>
			<? endforeach; ?>
		</tr>
	</table>
<? endif; ?>

<? if (isset($parse['activity'])): ?>
	<div class="separator"></div>

	<div id="tabs" class="ui-tabs ui-widget ui-widget-content" style="max-width: 100%">
		<div class="head">
			<ul class="ui-tabs-nav ui-widget-header">
				<li><a href="#tabs-0">Чат</a></li>
				<li><a href="#tabs-1">Форум</a></li>
			</ul>
		</div>
		<div id="tabs-0" class="ui-tabs-panel ui-widget-content">
			<table class="table" style="max-width: 100%">
				<tr>
					<th class="left">
						<div style="max-height: 150px;overflow-y: auto;overflow-x: hidden;">
						<? foreach ($parse['activity']['chat'] AS $activity): ?>
							<div class="activity"><div class="date1" style="display: inline-block;padding-right:5px;"><?=datezone("H:i", $activity['TIME']) ?></div><div style="display: inline;white-space:pre-wrap"><?=$activity['MESS'] ?></div></div>
							<div class="clear"></div>
						<? endforeach; ?>
						</div>
					</th>
				</tr>
			</table>
		</div>
		<div id="tabs-1" class="ui-tabs-panel ui-widget-content" style="display: none">
			<table class="table">
				<tr>
					<th class="left">
						<div style="max-height: 150px;overflow-y: auto;overflow-x: hidden;">
						<? foreach ($parse['activity']['forum'] AS $activity): ?>
							<div class="activity"><div class="date1" style="display: inline-block;padding-right:5px;"><?=datezone("H:i", $activity['TIME']) ?></div><div style="display: inline;white-space:pre-wrap"><?=$activity['MESS'] ?></div></div>
							<div class="clear"></div>
						<? endforeach; ?>
						</div>
					</th>
				</tr>
			</table>
		</div>
	</div>
	<script type="text/javascript">
		$("#tabs").tabs();
	</script>
<? endif; ?>