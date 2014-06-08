(function($){
	simpleRelatedPostsOption = 
	{
		init:function()
		{
			if ( !$('#sirp_original_css').is(':checked') ) {
				$("#sirp_original_css_content").css('display', 'none');
			}

			$('#sirp_original_css').click(function() {
				$("#sirp_original_css_content").slideToggle(this.checked);
			});
		}
	},
	$(document).ready(function ()	
    {
        simpleRelatedPostsOption.init();
    })
})(jQuery);