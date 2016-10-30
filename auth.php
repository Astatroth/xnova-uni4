<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.2/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="/scripts/smoothness/jquery-ui-1.10.2.custom.css">
<script type="text/javascript" src="/scripts/universe.js"></script>
<script language="JavaScript" src="/scripts/smiles.js"></script>
<script src="/scripts/ed.js" type="text/javascript"></script>
<script src="<?=$_GET['api_server'] ?>js/fapi.js" type="text/javascript"></script>

<script type="text/javascript">
function LoadGame ()
{
	document.getElementById('formiframe').submit();
}

var timezone = 0;
var ajax_nav = 0;

window.onload = (function()
{
	FAPI.init('<?=$_GET['api_server'] ?>', '<?=$_GET['apiconnection'] ?>',
		function() 
		{
			FAPI.UI.setWindowSize(760, 700);
			LoadGame();
		}
		, function(error)
		{
			alert("API initialization failed");
		}
	);
});
</script>
</head>
<body>
<iframe src="" name="iframe" style="visibility:hidden" frameborder="0"></iframe>
<form method="POST" target="iframe" name="formiframe" id="formiframe" action="/?set=login">
	<? foreach ($_GET AS $key => $value): ?>
		<input type="hidden" name="<?=$key ?>" value="<?=$value ?>">
	<? endforeach; ?>
</form>
<center>Загрузка...<br><img src="/images/loading.gif"></center>
</body>
</html>