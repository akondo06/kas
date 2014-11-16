<!DOCTYPE html>
<html>
<head>
	<title><?php echo $bone->section()->menu()['title']; ?> - KAS Administration</title>
	<meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<link rel="stylesheet" type="text/css" href="layout/css/reset.css" />
	<!-- <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Montserrat:400,700" /> -->
	<!-- <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Lato:400,300,700" /> -->
	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,300" />
	<link rel="stylesheet" type="text/css" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="layout/css/common.css" />
</head>
<body>
<div class="wrapper">
	<div class="header">
		<a href="<?php echo $bone->url(); ?>" class="logo"><img src="layout/img/logo.png" alt="" /></a>
		<div class="nav">
			<ul class="topmenu">
				<?php
					foreach($bone->menu() as $key => $section) {
						if(array_key_exists('title', $section)) {
							echo "<li>";
							echo "<a href=\"?section=".$key."\">".$section['title']."</a>\n";
							if(array_key_exists('submenu', $section) && count($section['submenu']) > 0) {
								echo "\t<ul class=\"submenu\">\n";
								foreach($section['submenu'] as $subkey => $title) {
									echo "\t\t<li><a href=\"".$bone->url($key, $subkey)."\">".$title."</a></li>\n";
								}
								echo "\t</ul>\n";
							}
							echo "</li>\n";
						}
					}
				?>
			</ul>
		</div>
		<div class="userinfo">
			<span>Hi <?php echo $bone->user_details()->nickname; ?>, </span><a href="<?php echo $bone->site_url(); ?>">View site</a> or <a href="index.php?action=logout">Logout</a>
		</div>
		<div class="clear"></div>
	</div>

	<div class="contentWrapper">
		<div class="sectionHeader">
			<?php
				$section = $bone->section();
				$subsection = null;
				if(isset($_GET['sub']) && $section->exists_in_menu($_GET['sub'])) {
					$subsection = $_GET['sub'];
				}
				$menu = $section->menu();
				$submenu = $menu['submenu'];

				echo "<h1 class=\"title\"><a href=\"".$bone->url($section->id())."\">";
				if($subsection != null) {
					echo $menu['title']." <span>(".ucfirst($subsection).")</span>";
				} else {
					echo $menu['title'];
				}
				echo "</a></h1>";

				if(is_array($submenu) && count($submenu) > 0) {
					echo "<ul class=\"menu\">";
					foreach($submenu as $subkey => $title) {
						$current = null;
						if($subsection == $subkey) {
							$current = " class=\"active\"";
						}
						echo "\t\t<li><a href=\"".$bone->url($section->id(), $subkey)."\"".$current.">".$title."</a></li>\n";
					}
					echo "</ul>";
				}
			?>
			<div class="clear"></div>
		</div>

		<?php
			$message = $bone->message();
			if(is_array($message) && array_key_exists('type', $message) && array_key_exists('message', $message)) {
				echo "<div class=\"message ".$message['type']."\">".$message['message']."</div>";
			}

			// Show the content
			$bone->content();
		?>
	</div>
	<div class="footer">
		&copy; <?php $KAS = KAS::instance(); echo date('Y').", ".$KAS->name(); ?> 
	</div>
</div>
<script type="text/javascript" src="layout/js/jquery-v1.4.4.min.js"></script>
<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/fonts/fontawesome-webfont.ttf"></script>
<script type="text/javascript" src="layout/js/common.js"></script>
</body>
</html>