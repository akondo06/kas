<?php
$default = array("location" => "", "status" => 1, "content" => "");
$default = (object) $default;

if($_POST) {
	$default = (object) $_POST;
}

?>
<form name="add_ad" method="post" action="<?php echo $bone->current_url(); ?>" class="form">
<?php
	$form = new Form();
	$form->input(array("name" => "location", "value" => $default->location, "placeholder" => "Location...", "label" => "Location"));
	$form->select(array("name" => "status", "label" => "Status", "values" => array(0 => "Hidden", 1 => "Active"), "selected" => $default->status));
	$form->textarea(array("name" => "content", "value" => $default->content, "label" => "Content"));
	$form->button(array("value" => "Submit"));
?>
</form>