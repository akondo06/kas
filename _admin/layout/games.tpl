<?php
	$KAS = KAS::instance();
	$section = $bone->section();

	$list = KAS_List::instance();
	$base_link = $section->url(true)."&page=[PAGE]";
	$games_no = $list->get_games_no();
	$per_page = 48;
	$current_page = $section->current_url_page();

	$pattern = "
	<div class=\"game\">
		<img src=\"[THUMB]\" alt=\"\" class=\"thumb\" />
		<div class=\"details\">
			<div class=\"name\">[NAME]</div>
			<div class=\"category\">[CATEGORYNAME]</div>
			<div class=\"actions\">
				<a href=\"".$section->url('edit')."&id=[ID]\"><i class=\"fa fa-pencil\"></i></a>
				<a href=\"".$section->url('delete')."&id=[ID]\"><i class=\"fa fa-times\"></i></a>
			</div>
		</div>
		<div class=\"clear\"></div>
	</div>";

	echo $section->list_games(array("limit" => $per_page, "page" => $section->current_url_page(), "pattern" => $pattern));
?>
<div class="clear"></div>

<ul class="pagination">
	<?php
		$prev_page = $list->pagination_link("prev", $games_no, $base_link, $current_page, $per_page);
		$next_page = $list->pagination_link("next", $games_no, $base_link, $current_page, $per_page);

		if($prev_page != null) {
			echo "<li><a href=\"".$prev_page."\" class=\"button_left\">&larr; Prev</a></li>";
		}

		$args = array("total" => $games_no, "per_page" => $per_page, "current" => $current_page, "base_link" => $base_link, "display" => 18);
		echo $list->pagination($args);

		if($next_page != null) {
			echo "<li><a href=\"".$next_page."\" class=\"button_right\">Next &rarr;</a></li>";
		}
	?>
</ul>
<div class="clear"></div>