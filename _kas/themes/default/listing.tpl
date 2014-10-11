<?php
	$is_category = $KAS->router()->current_route() == "category" || $KAS->router()->current_route() == "category_page";
	$is_tag = $KAS->router()->current_route() == "tag" || $KAS->router()->current_route() == "tag_page";
	$site_args = $KAS->router()->args();
?>
<?php if($is_category) { ?>
<div class="module">
	<div class="head">Most Popular <?php echo $KAS->section()->name(); ?> Games</div>
	<div class="cont">
		<ul class="thumbs thumbs8perrow">
			<?php
				$list = KAS_List::instance();
				$args = array("limit" => 8, "order_by" => "plays", "order" => "desc");
				if($is_category) {
					$args['category'] = $KAS->section()->id();
				}
				echo $list->games($args);
			?>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="foot"></div>
</div>
<?php } ?>
<div class="module">
	<div class="head"><?php echo $KAS->section()->name(); ?> Games</div>
	<div class="cont">
		<ul class="thumbs">
			<?php
				$list = KAS_List::instance();
				$args = array("limit" => $KAS->setting("per_page"), "page" => $KAS->section()->page(), "order_by" => "id", "order" => "desc");
				if($is_category) {
					$args['category'] = $KAS->section()->id();
				}
				if($is_tag) {
					$args['search'] = $site_args['slug'];
				}
				echo $list->games($args);
			?>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="foot"></div>
</div>

<div class="pagination">
	<?php
		$games_no = $KAS->section()->games_no();
		$prev_page = $list->pagination_link("prev", $games_no);
		$next_page = $list->pagination_link("next", $games_no);
		if($prev_page != null) {
			echo "<a href=\"".$prev_page."\" class=\"button_left\">&larr; Prev</a>";
		}
		if($next_page != null) {
			echo "<a href=\"".$next_page."\" class=\"button_right\">Next &rarr;</a>";
		}
	?>
	<ul class="buttons">
		<?php
			$args = array("total" => $games_no);
			if($is_category) {
				$args['section'] = "category_page";
			} else {
				$args['section'] = "tag_page";
			}
			echo $list->pagination($args);
		?>
	</ul>
</div>