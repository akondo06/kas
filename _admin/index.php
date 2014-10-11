<?php
	error_reporting(E_ALL);
	ini_set('upload_max_filesize', '40M');
	ini_set('post_max_size', '40M');
	ini_set('max_input_time', 600);
	ini_set('max_execution_time', 600);
	require_once("_backbone.php");

	$bone = Backbone::instance();

	if($bone->check_for_user()) {
		$bone->current_section();
		$section = $bone->section();
		$section->template();
		require_once("layout/layout.tpl");
	} else {
		require_once("layout/login.tpl");
	}
?>