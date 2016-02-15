(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	var data='';
	var sortable = false;
	
	$(document).ready(function() {
		
		$('ul#tabs li h3 a').on('click', function(e)  {
        var currentAttrValue = jQuery(this).attr('href');
 
        // Show/Hide Tabs
				$('ul.order-list').removeClass('active');
        $('ul' + currentAttrValue).addClass('active');
 
        // Change/remove current tab to active
        $(this).closest('li').addClass('active').siblings().removeClass('active');
 
        e.preventDefault();
    });
		
		if($('#form_result input.option_order:checked').val() ==  'manual' ) enableSortable();
		
		// Au clic sur les boutons radio on enrehistre les préférences //1,9,11,7,14
		$('#form_result input.option_order').change(function (){
			$('#spinner-ajax-radio').show();
			
			if($('#form_result input.option_order:checked').val() ==  'manual' ){
				if(sortable){
					$('ul.order-list').sortable('enable');
					$("ul.order-list").removeClass("sorting-disabled");
				}else enableSortable();
			}else{
				if(sortable) $('ul.order-list').sortable('disable');
				$('ul.order-list').addClass('sorting-disabled');
			}
			
			$("#form_result input.option_order").attr('disabled', 'disabled');
			
			data = {
				action				: 'network_wide_post_ordering',
				nwp_order_type: $('#form_result input.option_order:checked').val(),
				security			: $('input#nwp_ordering_nonce').val()
			}
			
			$.post(ajaxurl, data, function (response){
				$('#debug').html(response);
				$('#spinner-ajax-radio').hide();
				$("#form_result input.option_order").attr('disabled', false);
			});
			
			return false;
		})
	});
	
	function enableSortable(){
		sortable = true;
		$("ul.order-list").removeClass("sorting-disabled");
		$("ul.order-list").sortable(
			{
				 update: function( event, ui ) {
					
					$('#spinner-ajax-order').show();
					var ordered = [];
					$("ul.order-list li").each(function(){
						ordered.push($(this).attr('id'));
					});
					//$(this).sortable("toArray").toString()
					data = {
						action				: 'network_wide_post_ordering',
						nwp_list_order: ordered.toString(),
						security			: $("input[name=nwp_ordering_nonce]").val()
					}
					$.post(ajaxurl, data, function (response){
						//alert(response);
						$('#spinner-ajax-order').hide();
					});
				 }
			}
		);
	}

})( jQuery );
