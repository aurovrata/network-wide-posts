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
		if($("#form_result input.option_order:checked").val() ==  "manual" && $("#sortable-list li").length >1) enableSortable();
		
		// Au clic sur les boutons radio on enrehistre les prŽfŽrences //1,9,11,7,14
		$("#form_result input.option_order").change(function (){
			$('#spinner-ajax-radio').show();
			
			if($("#form_result input.option_order:checked").val() ==  "manual" && $("#sortable-list li").length >1){
				enableSortable();
			}else{
				if(sortable) $("#sortable-list").sortable("disable");
				$("#sortable-list").addClass("sorting-disabled");
			}
			
			$("#form_result input.option_order").attr('disabled', 'disabled');
			
			data = {
				action				: 'network_wide_post_ordering',
				nwp_order_type: $("#form_result input.option_order:checked").val(),
				security			: $("input#nwp_ordering_nonce").val()
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
		$("#sortable-list").removeClass("sorting-disabled");
		$("#sortable-list").sortable(
			{
				 update: function( event, ui ) {
					
					$('#spinner-ajax-order').show();
					
					data = {
						action				: 'network_wide_post_ordering',
						nwp_list_order: $(this).sortable("toArray").toString(),
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
