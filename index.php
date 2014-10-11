<?php
	require_once("_kas/classes/db_config.php");
	require_once("_kas/classes/cache.php");
	require_once("_kas/classes/database.php");
	require_once("_kas/classes/session.php");
	require_once("_kas/classes/router.php");
	require_once("_kas/classes/kas.php");
	
	$KAS = KAS::instance();
	require_once("_kas/classes/list.php");
	
	//print_r($KAS->settings());
	
	$KAS->router()->section();
	
	require_once($KAS->template_path()."".$KAS->template_layout());
	
	echo "\n<br />----------------\n<br /> Session statistics: \n<br /> ID: ".$KAS->session()->session_id()."\n<br />----------------\n<br /> Database statistics: \n<br /> Total Queries: ".$KAS->db()->no_of_queries()." (".$KAS->db()->no_of_cached_queries()." cached).\n<br /> Connection type: ".$KAS->db()->type();

	//if($KAS->session()->is_expired()) {
	//	$KAS->session()->renew();
	//	echo "\n<br />----------------\n<br />Session renewed.";
	//}

?>