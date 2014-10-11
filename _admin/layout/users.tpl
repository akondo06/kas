<?php
	$section = $bone->section();
	$data = $section->list_users();
	foreach($data as $user) {
?>
<div class="row thumbed">
	<div class="thumb">
		<img src="http://akondo/kas/_kas/files/img/flaming-zombooka.jpg" alt="" />
	</div>
	<div class="content">
		<?php echo $user->nickname; ?>
		<span class="email"><?php echo $user->email; ?></span>
		<span class="level"><?php echo $user->level; ?></span>
	</div>
	<div class="actions">
		<a href="<?php echo $section->url('edit')."&id=".$user->id; ?>" class="edit">Edit</a>
		<a href="<?php echo $section->url('delete')."&id=".$user->id; ?>" class="delete">Delete</a>
	</div>
	<div class="clear"></div>
</div>
<?php } ?>