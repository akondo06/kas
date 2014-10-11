<?php
	$section = $bone->section();
	$current = $section->game($_GET['id']);

	$list = KAS_List::instance();
?>
<form name="do" method="post" enctype="multipart/form-data" action="<?php echo $bone->current_url(); ?>" class="form">
	<div class="thumbWrapper">
		<img src="<?php echo $current->thumb; ?>" alt="" class="thumb" />
		<div class="info">
			<div class="name"><?php echo $current->name; ?></div>
			<div class="category"><?php echo $current->category->name; ?></div>
			<div class="stats">Added on <?php echo date("l, d F Y", $current->added_on); ?> and has been played <?php echo $current->plays; ?> times. Last play was on <?php echo date("l, d F Y", $current->added_on); ?></div>
		</div>
		<div class="clear"></div>
	</div>
<!-- 
	<?php if(!empty($current->description)) { ?>
	<div class="section">
		<h2>Description:</h2>
		<p><?php echo $current->description; ?></p>
	</div>
	<?php } ?>
	<?php if(!empty($current->instructions)) { ?>
	<div class="section">
		<h2>Instructions:</h2>
		<p><?php echo $current->instructions; ?></p>
	</div>
	<?php } ?> -->

	<?php 
		$list = KAS_List::instance();
		$form = new Form();

		$form->input(array("classes" => "inputWrapper halfLeftInput", "name" => "name", "value" => $current->name, "placeholder" => "Name...", "label" => "Name"));
		$form->input(array("classes" => "inputWrapper halfRightInput", "name" => "slug", "value" => $current->slug, "placeholder" => "Slug...", "label" => "Slug"));
		$form->clear();

		$form->textarea(array("name" => "description", "value" => $current->description, "label" => "Description"));
		$form->textarea(array("name" => "instructions", "value" => $current->instructions, "label" => "Instructions"));

		$categories = array();
		foreach ($list->get_categories() as $category) {
			$categories[$category->id] = $category->name;
		}
		$form->select(array("name" => "category", "label" => "Category", "values" => $categories, "selected" => $current->category->id));

		$form->input(array("name" => "tags", "value" => $current->tags, "placeholder" => "Tags...", "label" => "Tags"));
		$form->input(array("name" => "walkthrough", "value" => $current->walkthrough, "placeholder" => "Walkthrough...", "label" => "Walkthrough"));

		$form->label("Dimensions");
		$form->input(array("classes" => "inputWrapper halfLeftInput", "name" => "width", "value" => $current->width, "placeholder" => "Width..."));
		$form->input(array("classes" => "inputWrapper halfRightInput", "name" => "height", "value" => $current->height, "placeholder" => "Height..."));
		$form->clear();

		$form->input(array("type" => "file", "name" => "file", "value" => $current->file, "placeholder" => "File...", "label" => "File"));
		$form->input(array("type" => "file", "name" => "thumb", "value" => $current->thumb, "placeholder" => "Thumbnail...", "label" => "Thumbnail"));

		$form->select(array("name" => "status", "label" => "Status", "values" => array(0 => "Hidden", 1 => "Active"), "selected" => $current->status));

		$form->button(array("value" => "Save"));
	?>

	<?php 
		//print_r($current);
	?>
</form>