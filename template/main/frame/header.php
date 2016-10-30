<? if (!$isAjax): ?>
	<!DOCTYPE HTML>
	<html>
	<head>
		<? foreach ($attributes AS $name => $content): ?>
			<? if ($name == 'title'): ?>
				<title><?=$content ?> :: <?=\Xcms\Core::getConfig('game_name') ?></title>
			<? else: ?>
				<meta name="<?=$name ?>" content="<?=$content ?>">
			<? endif; ?>
		<? endforeach; ?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<link rel="image_src" href="http://<?=$_SERVER['HTTP_HOST'] ?><?=RPATH ?>images/logo.jpg" />
		<link rel="apple-touch-icon" href="http://<?=$_SERVER['HTTP_HOST'] ?><?=RPATH ?>images/apple-touch-icon.png"/>
		<?
			global $pageObj;

			$pageObj->addCss(RPATH.'template/'.\Xcms\Core::getConfig('gameTemplate').'/bootstrap.css');
			$pageObj->addCss(RPATH.DPATH.'formate.css');
			$pageObj->addCss(RPATH.'template/'.\Xcms\Core::getConfig('gameTemplate').'/style.css');
			$pageObj->addCss(RPATH.'template/'.\Xcms\Core::getConfig('gameTemplate').'/media.css');
			$pageObj->addCss(RPATH.'template/'.\Xcms\Core::getConfig('gameTemplate').'/mobile.css');
			$pageObj->addCss(RPATH.'scripts/smoothness/jquery-ui-1.10.2.custom.css');

			$pageObj->addJs('https://yastatic.net/jquery/1.11.1/jquery.min.js');
			$pageObj->addJs('https://yastatic.net/jquery-ui/1.11.2/jquery-ui.min.js');
			$pageObj->addJs(RPATH.'template/'.\Xcms\Core::getConfig('gameTemplate').'/script.js');
			$pageObj->addJs(RPATH.'scripts/jquery.form.min.js');
			$pageObj->addJs(RPATH.'scripts/game.js');
			$pageObj->addJs(RPATH.'scripts/universe.js');
			$pageObj->addJs(RPATH.'scripts/flotten.js');
			$pageObj->addJs(RPATH.'scripts/smiles.js');
			$pageObj->addJs(RPATH.'scripts/ed.js');
			$pageObj->addJs(RPATH.'scripts/jquery.touchSwipe.min.js');

			$pageObj->showScripts('all');
		?>
		<!--
		<link rel="stylesheet" type="text/css" href="<?=RPATH ?>template/<?=\Xcms\Core::getConfig('gameTemplate') ?>/bootstrap.css?v=<?=substr(md5(VERSION), 0, 3) ?>">
		<link rel="stylesheet" type="text/css" href="<?=RPATH ?><?=DPATH ?>formate.css?v=<?=substr(md5(VERSION), 0, 3) ?>">
		<link rel="stylesheet" type="text/css" href="<?=RPATH ?>template/<?=\Xcms\Core::getConfig('gameTemplate') ?>/style.css?v=<?=substr(md5(VERSION), 0, 3) ?>">
		<link rel="stylesheet" type="text/css" href="<?=RPATH ?>template/<?=\Xcms\Core::getConfig('gameTemplate') ?>/media.css?v=<?=substr(md5(VERSION), 0, 3) ?>">
		<link rel="stylesheet" type="text/css" href="<?=RPATH ?>scripts/smoothness/jquery-ui-1.10.2.custom.css">

		<script type="text/javascript" src="//yastatic.net/jquery/1.11.1/jquery.min.js"></script>
		<script type="text/javascript" src="//yastatic.net/jquery-ui/1.11.2/jquery-ui.min.js"></script>
		<script type="text/javascript" src="<?=RPATH ?>template/<?=\Xcms\Core::getConfig('gameTemplate') ?>/script.js?v=<?=substr(md5(VERSION), 0, 3) ?>"></script>
		-->
		<? if (!function_exists('allowMobileVersion') || !allowMobileVersion()): ?>
			<meta name="viewport" content="width=810">
		<? else: ?>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<script type="text/javascript">
				$(document).ready(function()
				{
					$("body").swipe(
					{
						swipeLeft: function()
						{
							if ($('.menu-sidebar').hasClass('opened'))
								$('.menu-toggle').click();
							else
								$('.planet-toggle').click();
						},
						swipeRight: function()
						{
							if ($('.planet-sidebar').hasClass('opened'))
								$('.planet-toggle').click();
							else
								$('.menu-toggle').click();
						},
						threshold: 100,
						excludedElements: ".table-responsive",
						fallbackToMouseEvents: false,
						allowPageScroll: "auto"
					});
				});
			</script>
		<? endif; ?>
		<!--
		<script type="text/javascript" src="<?=RPATH ?>scripts/jquery.form.min.js"></script>
		<script type="text/javascript" src="<?=RPATH ?>scripts/game.js?v=<?=substr(md5(VERSION), 0, 3) ?>"></script>
		<script type="text/javascript" src="<?=RPATH ?>scripts/universe.js?v=<?=substr(md5(VERSION), 0, 3) ?>"></script>
		<script type="text/javascript" src="<?=RPATH ?>scripts/flotten.js?v=<?=substr(md5(VERSION), 0, 3) ?>"></script>
		<script language="JavaScript" src="<?=RPATH ?>scripts/smiles.js?v=<?=substr(md5(VERSION), 0, 3) ?>"></script>
		<script src="<?=RPATH ?>scripts/ed.js?v=<?=substr(md5(VERSION), 0, 3) ?>" type="text/javascript"></script>
		-->
		<? if (\Xcms\Core::getConfig('DEBUG')): ?>
			<link rel="stylesheet" type="text/css" href="<?=RPATH ?>scripts/profiler.css">
			<script type="text/javascript" src="<?=RPATH ?>scripts/profiler.js"></script>
		<? endif; ?>
	</head>
	<body class="<? if (\Xcms\Core::getConfig('socialIframeView', 0) == 1): ?>iframe<? else: ?>window<? endif; ?>">
	<script type="text/javascript">
		XNova.path = '<?=RPATH ?>';
		var timestamp = <?=time() ?>;
		var timezone = <?=$pageParams['timezone'] ?>;
		var ajax_nav = <?=$pageParams['ajaxNavigation'] ?>;
		var addToUrl = '<? if (!isset($_COOKIE[COOKIE_NAME.'_full']) && isset($_SESSION['OKAPI'])): ?><?=http_build_query($_SESSION['OKAPI']) ?><? endif; ?>';

		<? if (isset($pageParams['userId'])): ?>
			XNova.fleetSpeed 	= <?=\GetGameSpeedFactor() ?>;
			XNova.gameSpeed 	= <?=round(\Xcms\core::getConfig('game_speed', 1) / 2500, 1) ?>;
			XNova.resSpeed 		= <?=\Xcms\core::getConfig('resource_multiplier', 1) ?>;
		<? endif; ?>
	</script>
	<? if (isset($pageParams['leftMenu']) && $pageParams['leftMenu'] == true): ?>
		<?=$this->ShowBlock('menu'); ?>
	<? endif; ?>

	<? if (isset($pageParams['topPanel']) && $pageParams['topPanel'] == true): ?>
		<?=$this->ShowBlock('top_panel'); ?>
	<? endif; ?>

	<? if (!isset($pageParams['leftMenu']) || $pageParams['leftMenu'] == false): ?>
		<div class="contentBox"><div><div id="box"><center>
	<? endif; ?>

<? else: ?>

	<? if (isset($pageParams['topPanel']) && $pageParams['topPanel'] == true): ?>
		<?=$this->ShowBlock('top_panel'); ?>
	<? endif; ?>

<? endif; ?>

<? if (isset($pageParams['deleteUserTimer']) && $pageParams['deleteUserTimer'] > 0): ?>
	<table class="table"><tr><td class="c" align="center">Включен режим удаления профиля!<br>Ваш аккаунт будет удалён после <?=datezone("d.m.Y", $pageParams['deleteUserTimer']) ?> в <?=datezone("H:i:s", $pageParams['deleteUserTimer']) ?>. Выключить режим удаления можно в настройках игры.</td></tr></table><div class="separator"></div>
<? endif; ?>

<? if (isset($pageParams['vocationTimer']) && $pageParams['vocationTimer'] > 0): ?>
   <table class="table"><tr><td class="c" align="center"><font color="red">Включен режим отпуска! Функциональность игры ограничена.</font></td></tr></table><div class="separator"></div>
<? endif; ?>

<? if (isset($pageParams['message']) && $pageParams['message'] != ''): ?>
   <table class="table"><tr><td class="c" align="center"><?=$pageParams['message'] ?></td></tr></table><div class="separator"></div>
<? endif; ?>

<div class="row">