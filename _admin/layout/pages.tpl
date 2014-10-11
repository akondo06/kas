<?php
	$section = $bone->section();
	$data = $section->list_pages();
	foreach($data as $page) {
?>
<div class="row">
	<div class="content">
		<?php echo $page->title; ?>
		<span><?php echo $page->slug." ("; echo ($page->status == 1) ? "Active" : "Disabled"; echo ")"; ?></span>
	</div>
	<div class="actions">
		<a href="<?php echo $section->url('edit')."&id=".$page->id; ?>" class="edit">Edit</a>
		<a href="<?php echo $section->url('delete')."&id=".$page->id; ?>" class="delete">Delete</a>
	</div>
	<div class="clear"></div>
</div>
<?php } ?>