(function($){
	simpleRelatedPosts = 
	{
		init:function()
		{
			this.keyup();
			this.sortable();
			this.remove();
			this.submit();
			this.reset();
		},
		
		sortable: function()
		{
			$( '.sirp_relationship .relationship_right .relationship_list' ).sortable();
		},
		
		keyup:function()
		{
			$('.sirp_relationship .relationship_left .relationship_search').keyup(function(e){
				var param = {
					action: 'sirp_search_posts',
					s: $(this).val()
				}
				$.post(ajaxurl, param, function(ret) {
					if ( ret == '' ) {
						return false;
					}

					$('.sirp_relationship .relationship_left .relationship_list').html("");
					$.each(ret, function(index, item) {
						$('.sirp_relationship .relationship_left .relationship_list').html("");
						var itemList = $("<li>");
						var itemLink = $("<a>");
						itemLink.attr("data-post_id", item.ID);
						itemLink.attr("href", item.permalink);
						var span1 = $("<span>");
						span1.attr("class", "title")
						var span2 = $("<span>");
						span2.attr("class", "sirp-button");
						var itemDiv = $("<div>");
						itemDiv.attr("class", "result-thumbnail")
						itemDiv.text(item.post_thumbnail);
						itemDiv.appendTo(span1);
						var titleSpan = $("<span>");
						titleSpan.text(item.post_title);
						titleSpan.appendTo(span1);
						span1.appendTo(itemLink);
						span2.appendTo(itemLink);
						itemLink.appendTo(itemList);
						itemList.appendTo($('.sirp_relationship .relationship_left .relationship_list'));
					});
					simpleRelatedPosts.add();
					$('.sirp_relationship .relationship_right .relationship_list li').each(function(index, item) {
      					$('.sirp_relationship .relationship_left .relationship_list li').each(function(index_2, item_2) {
      						if($(item).children('a').attr('data-post_id') == $(item_2).children('a').attr('data-post_id')) {
	      						$(item_2).addClass('hide');
		  					}
		  				});
		  			});
				},'json');
			});
		},
		
		add: function()
		{
			$('.sirp_relationship .relationship_left .relationship_list li a').on( 'click', function(e){
			 	e.preventDefault();
			 	
			 	var post_id = $(this).attr('data-post_id'), flg = true;

      			$('.sirp_relationship .relationship_right .relationship_list li').each(function(index, item) {
      				if ( (index+1) >= sirp_display_num ) {
	      				alert(objectL10n.alert.replace('%d', sirp_display_num));
	      				flg = false;
      				}
      			
      				if($(item).children('a').attr('data-post_id') == post_id) {
      					flg = false;
		  			}
      			});
	  			
	  			if (flg) {
					$(this).closest('li').clone(false).prependTo('.sirp_relationship .relationship_right .relationship_list').css('background-color', '#EAF2FA').animate({
						backgroundColor: "#FFFFF",
					}, 1200);
					$(this).closest('li').addClass('hide');
					simpleRelatedPosts.remove();
      			}
			});
		},
		
		remove: function()
		{

			$('.sirp_relationship .relationship_right .relationship_list li a .sirp-button').on( 'click', function(e){
			 	e.preventDefault();
			 	$(this).closest('li').fadeOut("slow").queue(function () {
					$(this).remove();
      			});
      			
      			var post_id = $(this).closest('a').attr('data-post_id');
      			$('.sirp_relationship .relationship_left .relationship_list li').each(function(index, item) {
      				if($(item).children('a').attr('data-post_id') == post_id) {
	      				$(item).removeClass('hide');
      				}
      			});
      			
			});
		},
		
		submit: function()
		{
			$('#post').submit(function(){
				$('.sirp_relationship .relationship_right .relationship_list li').each(function(index, item) {
					$('<input />').attr('type', 'hidden')
					.attr('name', 'simple_related_posts[]')
					.attr('value', $(item).children('a').attr('data-post_id'))
					.appendTo('#post');
				});
			});
		},
		
		reset: function()
		{
			$('.sirp_relationship .relationship_right #sirp-reset').on('click', function(e){
				var param = {
					action: 'sirp_reset_related_posts',
					post_id: sirp_post_id 
				}
				$.post(ajaxurl, param, function(ret) {
					if ( ret == '' ) {
						return false;
					}

					$('.sirp_relationship .relationship_left .relationship_list').html("");
					$.each(ret, function(index, item) {
						$('.sirp_relationship .relationship_left .relationship_list').html("");
						var itemList = $("<li>");
						var itemLink = $("<a>");
						itemLink.attr("data-post_id", item.ID);
						itemLink.attr("href", item.permalink);
						var span1 = $("<span>");
						span1.attr("class", "title")
						var span2 = $("<span>");
						span2.attr("class", "sirp-button");
						var itemDiv = $("<div>");
						itemDiv.attr("class", "result-thumbnail")
						itemDiv.text(item.post_thumbnail);
						itemDiv.appendTo(span1);
						var titleSpan = $("<span>");
						titleSpan.text(item.post_title);
						titleSpan.appendTo(span1);
						span1.appendTo(itemLink);
						span2.appendTo(itemLink);
						itemLink.appendTo(itemList);
						itemList.appendTo($('.sirp_relationship .relationship_left .relationship_list'));
					});
					simpleRelatedPosts.remove();
					$('.sirp_relationship .relationship_right .relationship_list').css('background-color', '#EAF2FA').animate({
						backgroundColor: "#FFFFF",
					}, 1200);
				},'json');
			});
		}
		
	},
	$(document).ready(function ()	
    {
        simpleRelatedPosts.init();
    })
})(jQuery);