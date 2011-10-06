var admin_panel;
(function ($) {
    admin_panel = {
        init: function () {
            var title;            
            $(".adminpanel-menu-link").each(function() {
                if ($(".adminpanel-menu-link:first").attr("href") == "#") {
                    $(".adminpanel-menu-link:first").addClass("visible");
                    $(".adminpanel-content-box:first").addClass("visible");
                } else {
                    title = $(this).attr("id").replace("adminpanel-menu-", "");
                    if ($(this).attr("href") == "#") {
                        $(this).addClass("visible");
                        $("#adminpanel-content-" + title).addClass("visible");
                    }
                }
            })
            
            $(".adminpanel-menu-link").click(function (event) {
                if ($(this).attr("href") == "#") {
                    event.preventDefault();
                    title = $(this).attr("id").replace("adminpanel-menu-", "");
                    $(".adminpanel-menu-link").removeClass("visible");
                    $("#adminpanel-menu-" + title).addClass("visible");
                    $(".adminpanel-content-box").removeClass("visible");
                    $(".adminpanel-content-box").hide();
                    $("#adminpanel-content-" + title).fadeIn("fast");
                    $(".adminpanel-content-box").removeClass("visible")
                }
            })
            
            $("h3.heading").each(function() {
                $(this).click(function (event) {
                    $(this).next().toggle('slow');
                })
            })
        }
    };
    $(document).ready(function () {
        admin_panel.init()
    })
})(jQuery);