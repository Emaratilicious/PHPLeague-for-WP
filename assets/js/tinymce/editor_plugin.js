(function() {
    tinymce.create('tinymce.plugins.PHPLeague', {
        init : function(ed, url) {

            ed.addCommand('mcePHPLeague', function() {
                ed.windowManager.open({
                    file : url + '/window.php',
                    width : 500 + ed.getLang('PHPLeague.delta_width', 0),
                    height : 210 + ed.getLang('PHPLeague.delta_height', 0),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });

            // Register button
            ed.addButton('PHPLeague', {
                title : 'PHPLeague',
                cmd : 'mcePHPLeague',
                image : url + '/league.png'
            });

            // Add a node change handler, selects the button in the UI when a image is selected
            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('PHPLeague', n.nodeName == 'IMG');
            });
        },
        
        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                    longname  : 'PHPLeague',
                    author    : 'Maxime Dizerens',
                    authorurl : 'http://www.mika-web.com/',
                    infourl   : 'http://wordpress.org/extend/plugins/phpleague/',
                    version   : '1.2.1'
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('PHPLeague', tinymce.plugins.PHPLeague);
})();