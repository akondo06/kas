<?php
	$game = $KAS->section();
	$game_size = $game->size();
?>
<div class="module">
	<div class="head">Home &rarr; <?php echo $game->category()->name; ?> &rarr; <?php echo $game->name(); ?></div>
	<div class="cont">
		<div style="background: #000; width: <?php echo $game_size[0]; ?>px; height: <?php echo $game_size[1]; ?>px; margin: 10px auto;">
			<?php if($game->type() == "swf") { ?>
			<script src="<?php echo $KAS->template_url(); ?>js/swfobject.js"></script>
			<script type="text/javascript">
				swfobject.embedSWF("<?php echo $game->file_url(); ?>", "the_game", "<?php echo $game_size[0]; ?>", "<?php echo $game_size[1]; ?>", "9.0.0", "expressInstall.swf");
			</script>
			<div id="the_game">
				<h1>Flash not available</h1>
				<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
			</div>
			<?php } else { ?>
				<?php echo $game->type(); ?>
			<?php } ?>
		</div>
	</div>
	<div class="foot"></div>
</div>

<div class="module contentbox tabbed">
	<div class="head">
		<div class="left tabs">
			<a href="#" rel="game_details" class="selected">Game Details</a>
			<a href="#" rel="game_comments">Comments</a>
			<?php if(!empty($game->walkthrough())) { ?>
			<a href="#" rel="game_walkthrough">Walkthrough</a>
			<?php } ?>
			<div class="clear"></div>
		</div>
		<div class="right">Voting here....</div>
	</div>
	<div class="cont">
		<div id="game_details">
			<span class="enhance">Description: </span>
			<p><?php echo $game->description(); ?></p>
			<span class="enhance">Instructions: </span>
			<p><?php echo $game->instructions(); ?></p>
			<span class="enhance">Tags: </span>
			<p><?php echo $game->tags(); ?></p>
			<span class="enhance">Share: </span>
			<p>
				Share Options here.. some nice icons!
			</p>
		</div>
		<div id="game_comments" style="display: none;">
			<br />
			<br />
			<br />
			SOME FORM / COMMENT CONTENT HERE...
			<br />
			<br />
			<br />
		</div>
		<?php if(!empty($game->walkthrough())) { ?>
		<div id="game_walkthrough" style="display: none;">
			<?php
				echo $game->walkthrough();
			?>
		</div>
		<?php } ?>
	</div>
	<div class="foot"></div>
</div>

<div class="module">
	<div class="head">Related Games to "<?php echo $game->name(); ?>"</div>
	<div class="cont">
		<ul class="thumbs">
			<?php
				$list = KAS_List::instance();
				$args = array("limit" => 10, "order_by" => "newest", "order" => "desc", "exclude" => $game->id(), "category" => $game->category()->id);
				echo $list->games($args);
			?>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="foot"></div>
</div>