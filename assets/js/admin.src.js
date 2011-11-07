(function($) {
    var PHPLeague = {
        panel : function() {
            var title;
            
            $(".adminpanel-menu-link").each(function() {
                if ($(".adminpanel-menu-link:first").attr('href') == '#') {
                    $(".adminpanel-menu-link:first").addClass('visible');
                    $(".adminpanel-content-box:first").addClass('visible');
                } else {
                    title = $(this).attr('id').replace("adminpanel-menu-", '');
                    if ($(this).attr('href') == '#') {
                        $(this).addClass('visible');
                        $("#adminpanel-content-" + title).addClass('visible');
                    }
                }
            });

            $(".adminpanel-menu-link").click(function(event) {
                if ($(this).attr('href') == '#') {
                    event.preventDefault();
                    title = $(this).attr('id').replace("adminpanel-menu-", '');
                    $(".adminpanel-menu-link").removeClass('visible');
                    $("#adminpanel-menu-" + title).addClass('visible');
                    $(".adminpanel-content-box").removeClass('visible');
                    $(".adminpanel-content-box").hide();
                    $("#adminpanel-content-" + title).fadeIn('slow');
                    $(".adminpanel-content-box").removeClass('visible')
                }
            });
            
            $("h3.heading").each(function() {
                $(this).click(function () {
                    $(this).next().toggle('slow');
                })
            });
        },
        
        player : function() {
            $(".delete_player_team").click(function() {
                $.post(ajaxurl, {
                    action: 'delete_player_history_team',
                    id_player_team: parseInt($(this).closest('tr').attr('id'), 10)
                }, function(response) {
                    alert(response);
                });
                $(this).closest('tr').remove();
            });
        }
    };

    $.fn.toggleDisabled = function() {
        if ($(this).attr('class') == 'default')
            $(this).attr('readonly', false).attr('value', '');
        else
            $(this).attr('readonly', false);
    };
    
    jQuery(document).ready(function($) {
        PHPLeague.panel();
        PHPLeague.player();
        $('input[readonly="readonly"]').click(function() {
            $(this).toggleDisabled();
        });
        
        $("input.masked").mask("9999-99-99");
        $("input.masked-full").mask("9999-99-99 99:99:99");
    });
    
})(jQuery);