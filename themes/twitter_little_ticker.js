jQuery(document).ready(function($) {


	var tt_little_ticker = {

		//getters
		get_profile_image_content_w: function() {
			var margin = 2; //to avoid issues with responsive themes
			return $('.profile_image_content').width() + margin;
		},
		get_twitter_little_wrapper_w: function() {
			return $('.twitter_little_wrapper').width();
		},
		get_tweet_list_all_w: function() {
			return this.get_twitter_little_wrapper_w() - this.get_profile_image_content_w();
		},
		get_li_max_h: function() {
			var max_h = Math.max.apply(null, $(".tweet_list li").map(function() {
				return $(this).height();
			}).get());
			return max_h;
		},
		get_li_num: function() {
			return $(".tweet_list li").length;
		},

		//setters

		//add width to container
		set_tweet_list_all_w: function() {
			$('.tweet_list_all').width(this.get_tweet_list_all_w());
		},

		//add width and height to ul element
		set_tweet_list_w_h: function() {
			$('.tweet_list').width('auto');
			$(".tweet_list").width(this.get_li_num() * this.get_tweet_list_all_w());
			$('.tweet_list').height(this.get_li_max_h());
		},

		//add width of li element and content
		set_tweet_w: function() {
			$(".tweet_list li").width(this.get_tweet_list_all_w());
			$(".tweet_list .tweet_content").width(this.get_tweet_list_all_w());
		},

		//call all functions to set dimensions
		set_all_w_h: function() {
			this.set_tweet_list_all_w();
			this.set_tweet_list_w_h();
			this.set_tweet_w();
		},

		//init animation
		set_animation: function() {
			$('.tweet_list li:first-child').animate({
				width: 'hide',
				opacity: 'hide'
			}, 'slow', function() {
				$(this).appendTo($(this).parent()).show();
			});
		}
	};

	tt_little_ticker.set_all_w_h();

	setInterval(function() {
		tt_little_ticker.set_animation();
	}, 5000);

	//bind setsize function to window resize event
	$(window).resize(function() {
		tt_little_ticker.set_all_w_h();
	});

});