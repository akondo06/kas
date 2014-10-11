<?php
	$section = $bone->section();
	$data = $section->list_levels();
	foreach($data as $level) {
?>
<div class="row">
	<div class="content">
		<?php echo $level->name; ?>
		<span>
		<?php
			if(is_array($level->privileges) && count($level->privileges) > 0) {
				$first = true;
				foreach ($level->privileges as $id => $privilege) {
					if(!$first) { echo ", "; }
					echo $privilege->name;
					$first = false;
				}
			} else {
				echo "No privileges!";
			}
		?>
		</span>
	</div>
	<div class="actions">
		<a href="<?php echo $section->url('edit_level')."&id=".$level->id; ?>" class="edit">Edit</a>
		<a href="<?php echo $section->url('delete_level')."&id=".$level->id; ?>" class="delete">Delete</a>
	</div>
	<div class="clear"></div>
</div>
<?php } ?>