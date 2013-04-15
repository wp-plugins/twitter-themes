	jQuery(document).ready(function($) {
		function twitter_themes_animation(){
			$('.tweet_list li:first-child').animate({height: 'hide', opacity: 'hide'}, 'slow', function() { 
				$(this).appendTo($(this).parent()).slideDown(); 
			});
		}
		setInterval(function(){ twitter_themes_animation(); }, 5000);
	});