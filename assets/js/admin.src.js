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
        
        league_settings : function() {
            var getNumber = 0;
            
            $(".remove_position").live('click', function() {
        		$(this).closest('tr').remove();
        	});

        	$("#add_position").live('click', function() {
        		if ($('#positions-table tbody tr:last').length > 0)
        		    var getNumber = parseInt($('#positions-table tbody tr:last').attr('id').replace('position-', ''), 10);

        		var addNumber = getNumber + 1;
        		var nextNumber = parseInt($('#positions-table tbody tr:last').find('input:first').val(), 10) + 1;

        		$('#positions-table > tbody:first').append('<tr id="position-' + addNumber + '"><td><input type="text" readonly="readonly" name="positions[' + addNumber + '][id]" value="' + nextNumber + '" size="4" /></td><td><input type="text" name="positions[' + addNumber + '][name]" value="" /></td><td><input type="text" name="positions[' + addNumber + '][order]" value="0" size="4" /></td><td><input type="button" name="remove_position" value="Remove" class="button remove_position" /></td></tr>');
        	});

        	$(".remove_event").live('click', function() {
        		$(this).closest('tr').remove();
        	});

        	$("#add_event").live('click', function() {
        		if ($('#events-table tbody tr:last').length > 0)
        		    var getNumber = parseInt($('#events-table tbody tr:last').attr('id').replace('event-', ''), 10);

        		var addNumber  = getNumber + 1;
        		var nextNumber = parseInt($('#events-table tbody tr:last').find('input:first').val(), 10) + 1;

        		$('#events-table > tbody:first').append('<tr id="event-' + addNumber + '"><td><input type="text" readonly="readonly" name="events[' + addNumber + '][id]" value="' + nextNumber + '" size="4" /></td><td><input type="text" name="events[' + addNumber + '][full_name]" value="" /></td><td><input type="text" name="events[' + addNumber + '][mini_name]" value="" /></td><td><input type="button" name="remove_event" value="Remove" class="button remove_event" /></td></tr>');
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
        $(this).attr('readonly', false);
    };
    
    jQuery(document).ready(function($) {
        PHPLeague.panel();
        PHPLeague.league_settings();
        PHPLeague.player();
        $('input[readonly="readonly"]').click(function() {
            $(this).toggleDisabled();
        });
    });
    
})(jQuery);