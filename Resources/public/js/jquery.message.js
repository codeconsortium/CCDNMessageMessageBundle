<!--
/*
 * Plugin jQuery.Profile
 * Requires JQuery, make sure to have JQuery included in your JS to use this.
 * JQuery needs to be loaded before this script in order for it to work.
 */
$(document).ready(function() {
	$().message();
	
//	$("input[name='Profile[avatar]']").prop("disabled", true);
});
	
(function($) {
	$.fn.message = function() {
		
		$("input[name='check_all']").click(function(event) {

			this.check_all = $("input[name='check_all']");

			if (this.check_all.is(':checked')) {
				this.check_all.attr('checked', true);
				$(this).parents('form:eq(0)').find(':checkbox').attr('checked', true);
			} else {
				this.check_all.attr('checked', false);
				$(this).parents('form:eq(0)').find(':checkbox').attr('checked', false);
			}
			
//			$(this).parents('form:eq(0)').find(':checkbox').attr('checked', this.checked);
			
			return true;
		});
	}

})(jQuery);

// -->