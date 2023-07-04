(function($) {
  "use strict";

	jQuery(document).ready( function($){ 
		$(".call_to_action").change( function(){
			var checked = this.checked;
			 
			if(checked){
				$(".call_to_action_form").slideDown();
			} else {
				$(".call_to_action_form").slideUp();
			}
		});
	});
})(jQuery);