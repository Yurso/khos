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

		<!-- Add jQueryUI library -->
		<link rel="stylesheet" href="/public/js/jquery-ui-1.11.4.custom/jquery-ui.min.css" />		
		<script type="text/javascript" src="/public/js/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>

		<!-- Add fancyBox -->
		<link rel="stylesheet" href="/public/js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="/public/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

		<script type="text/javascript">
			$(document).ready(function() {
				$(".fancybox").fancybox();
				$(".open_ajax").fancybox({type: 'ajax'});			
			});
		</script>
	</head>

	<body>
	
	<!-- Main Content -->
	<div class="wrapper">
		<div id="header">
			<a href="/" title="Khos Home" id="logo"></a>                
		</div>
		<div id="top-menu">
			<?php widgets::position('nav'); ?>
		</div>
		<div class="main-content">
			<?php $this->content(); ?>
			<?php widgets::position('html'); ?>
		</div>
		<div id="footer">
			<p>© 2015 KhoStudio. Все права защищены.</p>
		</div>
	</div>
	<!-- /Main Content -->
              
	</body>				
</html>