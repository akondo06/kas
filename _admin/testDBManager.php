<?php
	error_reporting(E_ALL);
	require_once("classes/backbone.php");

	require_once("classes/database/AdvertismentRow.php");

	$bone = Backbone::instance();


	$advertisment = new AdvertismentRow();

	$get = $advertisment->get(1);
	print_r($get);
	echo "--------------\n";
	$model = array(
		'id' => 22,
		'location' => 'sometext',
		'status' => true,
		'content' => "some content here...",
	);
	$advertisment->model($model);
	$save = $advertisment->save();
	var_dump($save);

	echo "--------------\n";
	/*$delete = $advertisment->delete();
	var_dump($save);*/
?>