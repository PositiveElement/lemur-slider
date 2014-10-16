jQuery(document).ready(function($) {
	//lemur_slider - Lemur slider shortcode
	var lemur_slider_shortcode_tooltip = $('<div>').addClass('lemur_slider_shortcode_tooltip');
	var lemur_slider_shortcode_tooltip_close = $('<a>').addClass('lemur_slider_shortcode_tooltip_close nemusicon-cancel');
	var lemur_slider_shortcode_tooltip_select = $('<select>').attr('name','test').attr('data-placeholder','Select a slider').append("<option></option>");
	var lemur_ajax_called = false;

	tinymce.create('tinymce.plugins.lemur_slider_button', {
		init : function(ed, url) {  
			ed.addButton('lemur_slider', {
				title : 'Lemur Slider',
				onclick : function(e) {  
				
					if($('a#content_lemur_slider').length) {
						var offset = $('a#content_lemur_slider').offset();
					} else {
						var offset = $('.mce-i-lemur_slider').parent().offset();
					}
					
					lemur_slider_shortcode_tooltip.css('left',offset.left).css('top',offset.top);
				
					$('body').append(lemur_slider_shortcode_tooltip.append(lemur_slider_shortcode_tooltip_close));
					
					$('.lemur_slider_shortcode_tooltip').addClass('active');
				
					var data = {
						action: 'lemur_slider_load_sliders'
					};
				
					if (!lemur_ajax_called) {
				
						$.post(ajaxurl, data, function(response) {
						
							var response = jQuery.parseJSON(response);
						
							$.each( response, function( key, value ) {
								lemur_slider_shortcode_tooltip_select.append('<option value="'+value.id+'">'+value.title+'</option>');
							});
						
							$('.lemur_slider_shortcode_tooltip').prepend(lemur_slider_shortcode_tooltip_select);
						
							$(".lemur_slider_shortcode_tooltip select").change(function(e){
								
								var id = $(".lemur_slider_shortcode_tooltip select").val();
								
								ed.selection.setContent('[lemur_slider id="'+id+'"]');
								
								$(".lemur_slider_shortcode_tooltip select option").removeAttr('selected');
								
								$('.lemur_slider_shortcode_tooltip').removeClass('active');
								
							});
							
							lemur_ajax_called = true;
						});
					
					} 
					
				}  
			});  
		},  
		createControl : function(n, cm) {  
			return null;  
		},  
	});  
	tinymce.PluginManager.add('lemur_slider', tinymce.plugins.lemur_slider_button);

	lemur_slider_shortcode_tooltip_close.click(function() {
	    $('.lemur_slider_shortcode_tooltip').removeClass('active');
	});
	//lemur_slider - Lemur slider shortcode
	
});