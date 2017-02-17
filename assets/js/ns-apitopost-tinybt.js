(function() {
     /* Register the buttons */
     tinymce.create('tinymce.plugins.MyButtons', {
          init : function(ed, url) {
               /**
               * Inserts shortcode content
               */
               ed.addButton( 'nsatp_button_eek', {
                    title : 'Insert NS API TO POST shortcode',
                    icon  : 'icon dashicons-before dashicons-external',
                    onclick : function() {
                         ed.selection.setContent('[ns_api_to_post]');
                    }
               });

          },
          createControl : function(n, cm) {
               return null;
          },
     });
     /* Start the buttons */
     tinymce.PluginManager.add( 'nsatp_button_script', tinymce.plugins.MyButtons );
})();
