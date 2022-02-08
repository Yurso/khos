<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" >
<head>	
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?php echo $this->tmpl_title; ?></title>
	
	<link rel="stylesheet" href="/public/css/mobile.css" type="text/css" />

	<!-- Add jQuery library -->
	<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>	

	<!-- Add jQueryUI library -->
	<link rel="stylesheet" href="/public/js/jquery-ui-1.11.4.custom/jquery-ui.min.css" />		
	<script type="text/javascript" src="/public/js/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
	
	<!-- Add Site library -->
	<script type="text/javascript" src="/public/js/mobile.js"></script>

	<!-- Put this script tag to the <head> of your page -->
	<script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script>

	<!-- Font Awesome script -->
	<script src="https://use.fontawesome.com/9cda35d99d.js"></script>

	<!-- fotorama.css & fotorama.js. -->
    <link href="/public/js/fotorama-4.6.4/fotorama.css" rel="stylesheet"> <!-- 3 KB -->
    <script src="/public/js/fotorama-4.6.4/fotorama.js"></script> <!-- 16 KB -->
</head>
	<div id="main">
		<?php $route = Registry::get('route'); ?>
		<?php if (widgets::position_count('admin_menu') && $route->subpath == 'admin/') : ?>
			<div class="mainmenu">						
				<?php widgets::position('admin_menu'); ?>			
				<a href="#"	id="menu-button-close"><i class="fa fa-times" aria-hidden="true"></i></a>
			</div>
		<?php endif; ?>
		<div class="maincontent">
			<?php if (widgets::position_count('admin_menu') && $route->subpath == 'admin/') : ?>
				<a href="#"	id="menu-button"><i class="fa fa-bars" aria-hidden="true"></i></a>
			<?php endif; ?>
			<?php if (widgets::position_count('pathway')) : ?>
				<div class="pathway">
					<?php widgets::position('pathway'); ?>
				</div>
			<?php endif; ?>

			<?php $this->content(); ?>

			<div class="clr"></div>

			<p style="text-align: center;padding: 20px 0;">
				<a href="?fullview=1">Перейти на полную версию сайта</a>
			</p>
			
			<div class="messages">
				<?php echo Main::getMessages('html'); ?>
			</div>
			
		</div>		
	</div>
</body>					
					