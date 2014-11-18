function radiotabs(tabs) {
    tabs += " input[type=radio]";
    $("div[id^=" + $(tabs).attr("name") + "-]").hide();
    $(tabs).click(function(e) {
        e.preventDefault();
        $("div[id^=" + $(tabs).attr("name") + "-]").hide();
        $("div[id^=" + $(tabs).attr("name") + "-] input, div[id^=" + $(tabs).attr("name") + "-] textarea").attr("value", "");
        $("#" + $(this).attr("name") + "-" + $(this).attr("value")).show();
    });

    tabs += "[checked=checked]";
    $(tabs).each(function() {
        $("#" + $(this).attr("name") + "-" + $(this).attr("value")).show();
    });
}

function headtabs(tabs, tabscontent) {
    $(tabscontent).each(function() {
        var currentRel = $(tabs + ".active").attr("rel");
        if ($(this).attr("id") != "" && $(this).attr("id") != currentRel) {
            $(this).hide();
        }
    });

    $(tabs).click(function(e) {
        e.preventDefault();
        $(tabs).removeClass("active");
        $(this).addClass("active");
        $(tabscontent).each(function() {
            if ($(this).attr("id") != "") {
                $(this).hide();
            }
        });
        $("#" + $(this).attr("rel")).fadeIn(400);
    });
}


$(document).ready(function() {
    $('.redirectOnChange').change(function() {
        window.location = $(this).val();
    });
    radiotabs("#filetype");
    radiotabs("#thumbnailtype");
    radiotabs("#thumbnail2type");
    headtabs(".tabs a", ".tabscontent > div");

    $('.nav').click(function(e) {
        var list = $('.nav .topmenu');
        if (list.length > 0 && parseInt($(this).width()) < 100) {
            list.fadeToggle('fast');
        }
    });

    $(window).click(function(e) {
		if($(e.target).closest('.nav').length > 0) {
			return;
		}
		var nav = $('.nav');
		var list = nav.find('.topmenu');
        if (list.length > 0 && parseInt(nav.width()) < 100) {
            list.fadeOut('fast');
        }
	});
});