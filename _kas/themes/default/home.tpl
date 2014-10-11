<div class="column1 left">
			
	<div class="module">
		<div class="head">Games Played Now</div>
		<div class="cont">
			<ul class="thumbs thumbs2perrow">
				<?php
					$list = KAS_List::instance();
					$args = array("limit" => 4, "order_by" => "playing", "order" => "desc");
					echo $list->games($args);
				?>
			</ul>
			<div class="clear"></div>
		</div>
		<div class="foot"></div>
	</div>
	
</div>
<div class="column2 right">
	
	<div class="module">
		<div class="head">Featured Games</div>
		<div class="cont">
		
			<div class="slider">
				<?php
					//$list = KAS_List::instance();
					//$args = array("limit" => 4, "order_by" => "newest", "order" => "desc", "featured" => true);
					//echo $list->games($args);
				?>
				<div class="frame show"><a href="#"><img src="<?php echo $KAS->template_url(); ?>games_big/angry-birds.jpg" alt="" /></a></div>
				<div class="frame"><a href="#"><img src="<?php echo $KAS->template_url(); ?>games_big/perry-widgets.jpg" alt="" /></a></div>
				<div class="frame"><a href="#"><img src="<?php echo $KAS->template_url(); ?>games_big/project-gotham-racing-4.jpg" alt="" /></a></div>
				<div class="frame"><a href="#"><img src="<?php echo $KAS->template_url(); ?>games_big/wario.jpg" alt="" /></a></div>
			</div>
			
		</div>
		<div class="foot"></div>
	</div>

</div>
<div class="clear"></div>

<div class="module">
	<div class="head">Most Popular Games</div>
	<div class="cont">
		<ul class="thumbs">
			<?php
				$list = KAS_List::instance();
				$args = array("limit" => 10, "order_by" => "plays", "order" => "desc");
				echo $list->games($args);
			?>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="foot"></div>
</div>

<div class="module">
	<div class="head">New Games</div>
	<div class="cont">
		<ul class="thumbs">
			<?php
				$list = KAS_List::instance();
				$args = array("limit" => 10, "order_by" => "id", "order" => "desc");
				echo $list->games($args);
			?>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="foot"></div>
</div>

<div class="module">
	<div class="head">Top Rated Games</div>
	<div class="cont">
		<ul class="thumbs">
			<?php
				$list = KAS_List::instance();
				$args = array("limit" => 10, "order_by" => "rating", "order" => "desc");
				echo $list->games($args);
			?>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="foot"></div>
</div>

<div class="module">
	<div class="head">Random Games</div>
	<div class="cont">
		<ul class="thumbs">
			<?php
				$list = KAS_List::instance();
				$args = array("limit" => 10, "order_by" => "random", "order" => "desc");
				echo $list->games($args);
			?>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="foot"></div>
</div>
