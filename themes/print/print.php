<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" >
<head>	
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="<?php echo $conf->MetaKeywords; ?>" />
	<meta name="description" content="<?php echo $conf->MetaDescription; ?>" />	

	<title><?php echo $this->tmpl_title; ?></title>
		
	<link rel="stylesheet" href="/public/css/print.css" type="text/css" />
	
	<!--[if lte IE 7]>
		<link rel="stylesheet" href="public/css/template.ie7.css" type="text/css" />
	<![endif]-->	
</head>

<body>
	<div class="content">
		<?php $this->content(); ?>
	</div>
</body>					
				