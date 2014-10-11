<ul class="tabs">
	<li><a href="#" rel="generalTab" class="active">General</a></li>
	<li><a href="#" rel="componentsTab">Components</a></li>
	<li><a href="#" rel="seoTab">SEO</a></li>
	<li><a href="#" rel="gameTab">Game</a></li>
</ul>
<div class="clear"></div>

<div class="tabscontent">
	<div id="generalTab">
		General
	</div>
	<div id="componentsTab">
		Components
	</div>
	<div id="seoTab">
		SEOOOO
	</div>
	<div id="gameTab">
		Game related...
	</div>
</div>


<?php

	$KAS = KAS::instance();
	$settings = (array) $KAS->settings();

	foreach ($settings as $key => $value) {
		echo $key." = ".$value."<br />\n";
	}

?>