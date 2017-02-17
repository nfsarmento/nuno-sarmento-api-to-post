jQuery( document ).ready( function($) {


	$(document).ready(function() {
		$('#logo_image_url').click(function() {

			var logo_imag = wp.media({
				title:'Select Logo Image',
				library: {type: 'image'},
				multiple: false,
				button: { text: 'Insert'}
			});

			logo_imag.on('select', function() {
				var logo_selection = logo_imag.state().get('selection').first().toJSON();
				for(prop in logo_selection){
					console.log(prop);
				}
				$('#logo_image').val(logo_selection.url);
			});

			logo_imag.open();
		});
	});
	
});
