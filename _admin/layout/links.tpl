<?php
	$section = $bone->section();
	$data = $section->list_links();
	foreach($data as $link) {
?>
<div class="row">
	<div class="content">
		<div class="status<?php if($link->status == 1) { echo " active"; } ?>"></div>
		<?php echo $link->anchor; ?>
		<span>http://<?php echo $link->url; ?></span>
		<span class="bold"><?php echo $link->contact; ?></span>
	</div>
	<div class="actions">
		<a href="<?php echo $section->url('check')."&id=".$link->id; ?>" class="refresh">Check</a>
		<a href="<?php echo $section->url('edit')."&id=".$link->id; ?>" class="edit">Edit</a>
		<a href="<?php echo $section->url('delete')."&id=".$link->id; ?>" class="delete">Delete</a>
	</div>
	<div class="clear"></div>
</div>
<?php } ?>