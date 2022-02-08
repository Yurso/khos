<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" >
<head>	
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="<?php echo $conf->MetaKeywords; ?>" />
	<meta name="description" content="<?php echo $conf->MetaDescription; ?>" />	

	<title><?php echo $this->tmpl_title; ?></title>

	<!-- <link href="/public/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" /> -->
	<link rel="stylesheet" href="/public/css/system.css" type="text/css" />
	<link rel="stylesheet" href="/public/css/template.css" type="text/css" />
	
	<!--[if lte IE 7]>
		<link rel="stylesheet" href="public/css/template.ie7.css" type="text/css" />
	<![endif]-->

	<!-- Add jQuery library -->
	<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>

	<!-- Add fancyBox -->
	<link rel="stylesheet" href="/public/js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
	<script type="text/javascript" src="/public/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

	<!-- fotorama.css & fotorama.js. -->
    <link href="/public/js/fotorama-4.6.4/fotorama.css" rel="stylesheet"> <!-- 3 KB -->
    <script src="/public/js/fotorama-4.6.4/fotorama.js"></script> <!-- 16 KB -->

	<script type="text/javascript">
		$(document).ready(function() {
			$(".fancybox").fancybox();
			$(".open_ajax").fancybox({type: 'ajax'});
			$(".messages .messages-inner").hide().slideDown('fast').delay(3000).slideUp('fast')			
		});
	</script>

</head>

<body>
	<div class="content">
		<?php $this->content(); ?>
	</div>
	<div class="messages">
		<?php echo Main::getMessages('html'); ?>
	</div>
</body>				

				