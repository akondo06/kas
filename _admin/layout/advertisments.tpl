<?php
	$section = $bone->section();
	$data = $section->list_ads();
	foreach($data as $ad) {
?>
<div class="row">
	<div class="content">
		<?php echo $ad->location; ?>
		<span><?php echo ($ad->status == 1) ? "Active" : "Disabled"; ?></span>
	</div>
	<div class="actions">
		<a href="<?php echo $section->url('edit')."&id=".$ad->id; ?>" class="edit">Edit</a>
		<a href="<?php echo $section->url('delete')."&id=".$ad->id; ?>" class="delete">Delete</a>
	</div>
	<div class="clear"></div>
</div>
<?php } ?>