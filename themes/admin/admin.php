<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" >
<head>	
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<?php echo $this->tmpl_head_meta; ?>

	<title><?php echo $this->tmpl_title; ?></title>
	
	<link rel="stylesheet" href="/public/css/admin.css" type="text/css" />

	<!-- Add jQuery library -->
	<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>	

	<!-- Add jQueryUI library -->
	<link rel="stylesheet" href="/public/js/jquery-ui-1.11.4.custom/jquery-ui.min.css" />		
	<script type="text/javascript" src="/public/js/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>

	<!-- Add TinyMCE WYSYWIG library -->
	<script type="text/javascript" src="/public/js/tinymce/tinymce.min.js"></script>

	<!-- Add fancyBox -->
	<link rel="stylesheet" href="/public/js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
	<script type="text/javascript" src="/public/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

	<!-- Add Site library -->
	<script type="text/javascript" src="/public/js/admin.js"></script>

	<!-- Put this script tag to the <head> of your page -->
	<script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script>

	<!-- Font Awesome -->
	<!-- <link rel="stylesheet" href="/public/font-awesome-4.7.0/css/font-awesome.min.css"> -->
	<script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>

	<!-- fotorama.css & fotorama.js. -->
    <link href="/public/js/fotorama-4.6.4/fotorama.css" rel="stylesheet"> <!-- 3 KB -->
    <script src="/public/js/fotorama-4.6.4/fotorama.js"></script> <!-- 16 KB -->

    <?php echo $this->tmpl_head_additions; ?>

</head>
<body>
<?php if (isset($_GET['theme_type']) && $_GET['theme_type'] == 'clean') : ?>
	<?php $this->content(); ?>
<?php else : ?>
	<div id="main">
		<div class="mainmenu">			
			<div style="text-align: center;margin-top: 10px;"><img src="/public/images/logo.png" alt="Beescom" width="180"></div>
			<?php widgets::position('admin_menu'); ?>
			<!-- <ul class="menu-vertical" style="border-top:1px solid #fff;">				
				<li><a href="/" target="_blank">Перейти на сайт</a></li>
				<li><a href="/system/user/logout">Выход</a></li>
			</ul> -->
			
		</div>
		<div class="maincontent">
			
			<?php if (widgets::position_count('pathway')) : ?>
				<div class="pathway">
					<?php widgets::position('pathway'); ?>
				</div>
			<?php endif; ?>

			<?php $this->content(); ?>

			<div class="clr"></div>
			
			<div class="messages">
				<?php echo Main::getMessages('html'); ?>
			</div>
			
		</div>		
	</div>
<?php endif; ?>
</body>					
					