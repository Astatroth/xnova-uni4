<!DOCTYPE HTML>
<html>
<head>
	<? foreach ($attributes AS $name => $content): ?>
		<? if ($name == 'title'): ?>
			<title><?=$content ?></title>
		<? else: ?>
			<meta name="<?=$name ?>" content="<?=$content ?>">
		<? endif; ?>
	<? endforeach; ?>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width">

	<link rel="image_src" href="http://<?=$_SERVER['HTTP_HOST'] ?>/images/logo.jpg" />
	<link rel="apple-touch-icon" href="http://<?=$_SERVER['HTTP_HOST'] ?>/images/apple-touch-icon.png"/>

	<link rel="stylesheet" type="text/css" href="/scripts/login.css">
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/scripts/jquery.form.min.js"></script>
	<script type="text/javascript" src="/scripts/jquery.validate.js"></script>
	<link rel="stylesheet" type="text/css" href="/scripts/smoothness/jquery-ui-1.10.2.custom.css">
	<script type="text/javascript" src="/scripts/game.js?2"></script>
</head>
<body>

<script type="text/javascript">
	var ajax_nav = 1;
	var addToUrl = '';
</script>