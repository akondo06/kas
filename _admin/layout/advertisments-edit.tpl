<?php
	$section = $bone->section();
	$current = $section->ad($_GET['id']);
?>
<form name="edit_ad" method="post" action="<?php echo $bone->current_url(); ?>" class="form">
<?php
	$form = new Form();
	$form->input(array("name" => "location", "value" => $current->location, "placeholder" => "Location...", "label" => "Location"));
	$form->select(array("name" => "status", "label" => "Status", "values" => array(0 => "Hidden", 1 => "Active"), "selected" => $current->status));
	$form->textarea(array("name" => "content", "value" => $current->content, "label" => "Content"));
	$form->button(array("value" => "Save"));
?>
</form>