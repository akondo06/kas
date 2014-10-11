<?php
	$section = $bone->section();
	$current = $section->privilege($_GET['id']);
?>
<form name="edit_privilege" method="post" action="<?php echo $bone->current_url(); ?>" class="form">
	

	<div class="buttonWrapper">
		<input type="submit" name="submit" value="Save" />
		<div class="clear"></div>
	</div>
</form>