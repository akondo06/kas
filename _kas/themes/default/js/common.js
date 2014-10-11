$(document).ready(function() {
	// More button
	$(".topmenu .categories_menu li.more a").on("click", function(e) {
		// Prevent default behavior
		e.preventDefault();

		// The more element
		var element = $("#"+$(this).attr("rel"));
		
		if(element.length > 0) {
			if(element.is(":visible")) {
				element.slideUp("fast", function() { $(this).hide(); });
			} else {
				element.slideDown("fast", function() { $(this).show(); });
			}
		}
	});


	// Tabs
	var tabs = ".tabbed .tabs";
	var selection = "selected";
	$(tabs+" a").on("click", function(e) {
		// Prevent default behavior
		e.preventDefault();
		
		// The clicked tab
		var clicked = $(this);
		
		if(clicked.attr("rel") && $("#"+clicked.attr("rel")).length > 0 && !clicked.hasClass(selection)) {
			// Get the current selected tab
			var current = $(tabs+" a."+selection);
			
			// Hide the current tab's content
			$("#"+current.attr("rel")).slideUp("fast", function() { $(this).hide(); });
			
			// Show the content of the clicked tab
			$("#"+clicked.attr("rel")).slideDown("fast", function() { $(this).show(); });
			
			// Switch the selection class
			current.removeClass(selection);
			clicked.addClass(selection);
		}
	});
});