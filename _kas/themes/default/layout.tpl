<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php echo $KAS->section()->title(); ?></title>
	<meta name="keywords" content="<?php echo $KAS->section()->tags(); ?>" />
	<meta name="description" content="<?php echo $KAS->section()->description(); ?>" />
	<link href="http://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="<?php echo $KAS->template_url(); ?>common.css" />
	<!--[if IE]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="<?php echo $KAS->template_url(); ?>js/common.js"></script>
</head>
<body>
<div class="header">
	<div class="wrapper">
		<a href="<?php echo $KAS->url(); ?>" class="logo left"><img src="<?php echo $KAS->template_url(); ?>img/logo.png" alt="" /></a>
		<h1 class="pagetitle left"><?php echo $KAS->section()->title(); ?></h1>
		<form name="search" method="post" action="<?php echo $KAS->url(); ?>" class="search right">
			<input type="text" name="search" class="input left" placeholder="Search games..." />
			<input type="submit" name="submit" class="button right" value="" />
			<div class="clear"></div>
		</form>
		<div class="clear"></div>
		
		
		
		<!-- TOP MENU START -->
		<div class="topmenu">
			<ul class="categories_menu left">
				<?php
					$list = KAS_List::instance();
					$args = array("limit" => 12, "order_by" => "name", "order" => "asc", "pattern" => "<li><a href=\"[LINK]\">[NAME]</a></li>\n");
					echo $list->categories($args);
				?>
				<li class="more"><a href="#" rel="more">More</a></li>
			</ul>
			<div class="members_menu right">
				<!-- <a href="#register" class="button left">Register</a>
				<a href="#log-in" class="button right">Log in</a> -->
				<a href="#log-in" class="button right">Random Pick!</a>
			</div>
			<div class="clear"></div>
			
			<div id="more">
				<ul>
					<?php
						$list = KAS_List::instance();
						$args = array("limit" => 9999, "order_by" => "name", "order" => "asc");
						echo $list->categories($args);
					?>
				</ul>
				<!--EXTRA TOP MENU -->
			</div>
		</div>
		
	</div>
</div>
<div class="content">
	<div class="wrapper">
		<?php include_once($KAS->section()->file()); ?>
	</div>
</div>
<div class="footer">
	<div class="wrapper">
		<div class="copyright left">&copy; 2013 - <?php echo date('Y'); ?> <a href="#">Akondo Designs</a></div>
		<ul class="menu right">
			<li><a href="#">Home</a></li>
			<li><a href="#">News</a></li>
			<li><a href="#">Pictures</a></li>
			<li><a href="#">Videos</a></li>
			<li><a href="#">Contact</a></li>
		</ul>
		<div class="clear"></div>
		<div class="disclaimer left">All games, animations, images, and sprites are copyright to their respective owners.</div>
		<a href="#" class="akdesign right" target="_blank">AKONDO DESIGNS</a>
		<div class="clear"></div>
		<div class="partners">
			<span>Partners:</span> <a href="#">Free Online Games 24/7</a>, <a href="#">Dark Games</a>, <a href="#">Jeux Online</a>, <a href="#">Naruto Shippuden</a>
		</div>
	</div>
	<div class="clear"></div>
</div>
</body>
</html>