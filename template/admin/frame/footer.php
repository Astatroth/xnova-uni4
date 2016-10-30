			</div>
			<!-- END PAGE -->
		</div>
			</div>
		<!-- END CONTAINER -->
		<!-- BEGIN FOOTER -->
			<div class="container">
		<div class="footer">
		   <div class="footer-inner">
			  <?=date("Y") ?> &copy; XGG
		   </div>
		   <div class="footer-tools">
			  <span class="go-top">
			  <i class="icon-angle-up"></i>
			  </span>
		   </div>
		</div>
				</div>
				</div>
		<!-- END FOOTER -->
		<!-- BEGIN CORE PLUGINS -->
		<script src="<?=TEMPLATE_PATH ?>/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
		<script src="<?=TEMPLATE_PATH ?>/assets/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
		<script src="<?=TEMPLATE_PATH ?>/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="<?=TEMPLATE_PATH ?>/assets/plugins/bootstrap-hover-dropdown/twitter-bootstrap-hover-dropdown.min.js" type="text/javascript" ></script>
		<script src="<?=TEMPLATE_PATH ?>/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
		<script src="<?=TEMPLATE_PATH ?>/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
		<script src="<?=TEMPLATE_PATH ?>/assets/plugins/jquery.cookie.min.js" type="text/javascript"></script>
		<script src="<?=TEMPLATE_PATH ?>/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript" ></script>
		<!-- END CORE PLUGINS -->
		<!-- BEGIN PAGE LEVEL SCRIPTS -->
		<script src="<?=TEMPLATE_PATH ?>/assets/scripts/app.js" type="text/javascript"></script>
		<script src="<?=TEMPLATE_PATH ?>/assets/scripts/index.js" type="text/javascript"></script>
		<script src="<?=TEMPLATE_PATH ?>/assets/scripts/tasks.js" type="text/javascript"></script>
		<!-- END PAGE LEVEL SCRIPTS -->
		<script>
		   jQuery(document).ready(function() {
			  App.init(); // initlayout and core plugins
			  Index.init();
		   });
		</script>
		<!-- END JAVASCRIPTS -->
	</body>
	<!-- END BODY -->
</html>