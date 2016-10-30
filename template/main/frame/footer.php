</div>

<? if (!$isAjax): ?>
	<? if (isset($pageParams['leftMenu']) && $pageParams['leftMenu'] == true): ?>
		</div>

		<div id="loadingOverlay">загрузка...<br><img src="<?=RPATH ?>images/loading.gif" alt=""></div>
		<div id="preloadOverlay"><img src="<?=RPATH ?>images/loading.gif" alt=""></div>

		<? if (\Xcms\Core::getConfig('socialIframeView', 0) == 1): ?>
			</div>
		<? endif; ?>
		</div>
		<div class="clearfix"></div>
		</div>
		</div>
		</div></div>

		<div id="siteFooter" class="hidden-xs">
			<div class="container-fluid">
				<div class="pull-left text-left">
					<a href="?set=news" title="Последние изменения"><?=VERSION ?></a>
					<? if (\Xcms\Core::getConfig('socialIframeView', 0) == 0): ?>
						<a class="hidden-sm" target="_blank" href="http://xnova.su/">© 2008 - <?=date("Y") ?> Xcms</a>
					<? endif; ?>
				</div>
				<div class="pull-right text-right">
					<? if (\Xcms\Core::getConfig('socialIframeView', 0) == 1): ?>
						<a href="http://www.odnoklassniki.ru/group/56711983595558" class="ok" target="_blank">Группа игры</a>|
					<? endif; ?>
					<a href="http://forum.xnova.su/" target="_blank">Форум</a>|
					<a href="?set=banned">Тёмные</a>|
					<? if (\Xcms\Core::getConfig('socialIframeView', 0) == 0): ?>
						<a href="wide.php" data-link="1">Большой монитор</a>|
						<a href="http://vk.com/xnova_game" target="_blank">ВК</a>|
						<a href="?set=contact">Контакты</a>|
					<? endif;?>
					<a href="?set=content&article=help">Новичкам</a>|
					<a href="?set=content&article=agb">Правила</a>|
					<a onclick="" title="Игроков в сети" style="color:green"><?=\Xcms\Core::getConfig('online') ?></a>/<a onclick="" title="Всего игроков" style="color:yellow"><?=\Xcms\Core::getConfig('users_amount') ?></a>
				</div>
				<br class="clearfloat"/></div>
		</div>
		<div class="row hidden-sm hidden-md hidden-lg footer-mobile">
			<div class="col-xs-12 text-center">
				<a href="http://forum.xnova.su/" target="_blank">Форум</a>|
				<a href="?set=banned">Тёмные</a>|
				<a href="?set=contact">Контакты</a>|
				<a href="?set=content&article=help">Новичкам</a>|
				<a href="?set=content&article=agb">Правила</a>
			</div>
			<div class="col-xs-8 text-center">
				<a href="?set=news" title="Последние изменения"><?=VERSION ?></a>
				<? if (\Xcms\Core::getConfig('socialIframeView', 0) == 0): ?>
					<a class="media_1" target="_blank" href="http://xnova.su/">© 2008 - <?=date("Y") ?> Xcms</a>
				<? endif; ?>
			</div>
			<div class="col-xs-4 text-center">
				<a onclick="" title="Игроков в сети" style="color:green"><?=\Xcms\Core::getConfig('online') ?></a>/<a onclick="" title="Всего игроков" style="color:yellow"><?=\Xcms\Core::getConfig('users_amount') ?></a>
			</div>
		</div>
	<? endif; ?>

	<? if ($_SERVER['SERVER_NAME'] == 'vk.xnova.su'): ?>
		<script src="//vk.com/js/api/xd_connection.js" type="text/javascript"></script>
		<script type="application/javascript">
			$(window).load(function()
			{
				  VK.init(function() { console.log('vk init success'); }, function() {}, '5.24');
			});
		</script>
	<? endif; ?>

	<?
		if (isset($_REQUEST['apiconnection']) && (!isset($_SESSION['OKAPI']) || !isset($_SESSION['OKAPI']['apiconnection'])))
		{
			$_SESSION['OKAPI'] = Array
			(
				'api_server' => $_REQUEST['api_server'],
				'apiconnection' => $_REQUEST['apiconnection'],
				'session_secret_key' => $_REQUEST['session_secret_key'],
				'session_key' => $_REQUEST['session_key'],
				'logged_user_id' => $_REQUEST['logged_user_id'],
				'sig' => $_REQUEST['sig']
			);
		}
	?>
	<? if (!isset($_COOKIE[COOKIE_NAME.'_full']) && isset($_SESSION['OKAPI']) && is_array($_SESSION['OKAPI'])): ?>
		<script src="<?=$_SESSION['OKAPI']['api_server'] ?>js/fapi5.js" type="text/javascript"></script>
		<script type="text/javascript">
			FAPI.init('<?=$_SESSION['OKAPI']['api_server'] ?>', '<?=$_SESSION['OKAPI']['apiconnection'] ?>',
				function()
				{
					//FAPI.UI.setWindowSize(800, 700);
				}
				, function(error)
				{
					alert("API initialization failed");
				}
			);
		</script>
	<? endif; ?>

	<div id="windowDialog"></div>
	<div id="tooltip" class="tip"></div>

	<? if ((!isset($pageParams['leftMenu']) || $pageParams['leftMenu'] == false)): ?>
		</center></div></div></div>
	<? endif; ?>

	<? if (\Xcms\Core::getConfig('DEBUG')): ?>
		<div id="profilerToolbar"><?=showProfiler() ?></div>
	<? endif; ?>

	<!-- Yandex.Metrika counter -->
	<script type="text/javascript">
	var yaParams = {userId: <?=(isset($pageParams['userId']) ? $pageParams['userId'] : 0) ?>};
	</script>

	<script type="text/javascript">
	(function (d, w, c) {
		(w[c] = w[c] || []).push(function() {
			try {
				w.yaCounter25961143 = new Ya.Metrika({id:25961143,params:window.yaParams||{ }});
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
	</body></html>

<? else: ?>

	<? if ($userId > 0 && !$isPopup && \Xcms\Core::getConfig('overviewListView', 0) == 1): ?>
		<?=$this->ShowBlock('planets', array('ajax' => $isAjax)); ?>
	<? endif; ?>

	<? if (!$isPopup): ?>
		<script>document.title = "<?=$attributes['title'] ?> :: <?=\Xcms\Core::getConfig('game_name') ?>";</script>
	<? endif; ?>

	<? if (\Xcms\Request::checkSaveState()): ?>
		<script>addHistoryState("<?=\Xcms\Request::getClearQuery() ?>")</script>
	<? endif; ?>

	<? if (!$isPopup && \Xcms\Core::getConfig('DEBUG')): ?>
		<script>$("#profilerToolbar").html(\'<?=str_replace(Array("\n", "\r", '\'', '"'), '', showProfiler()) ?>\');PTB.init();$(".show").click();</script>
	<? endif; ?>

<? endif; ?>

<? if (isset($pageParams['ajaxNavigation']) && $pageParams['ajaxNavigation']): ?>
	<script type="text/javascript">setMenuItem("<?=((isset($_GET['set'])) ? (($_GET['set'] == 'buildings' && isset($_GET['mode'])) ? $_GET['set'].$_GET['mode'] : $_GET['set']) : '') ?>");</script>
<? endif; ?>

<? if ($userId > 0 && !$isPopup): ?>
	<script type="text/javascript">UpdateGameInfo('<?=$pageParams['newMessages'] ?>', '<?=$pageParams['allyMessages'] ?>'); timestamp = <?=time() ?>;</script>
<? endif; ?>