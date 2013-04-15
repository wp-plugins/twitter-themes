[if tag="profile_image"]
	<div class="profile_all clearfix">
		<div class="profile_image_content" title="[user_screen_name]">
			[profile_image]
		</div>
		<div class="profile_text [profile_image_size]">
			<a href="https://twitter.com/[user_screen_name]" class="screen_name" [target_blank]>@[user_screen_name]</a>
			<div class="user_description">[user_description]</div>
		</div>
	</div>
[/if]
<div class="tweet_list_all">
	<ul class="tweet_list">
		[loop]
			<li class="tweet[loop_number] [loop_even_odd]">
				<div class="tweet_content">
					<span class="tweet_text">
						[tweet_text]. 
					</span>
					[if tag="sending_app"]
						<span class="sending_app">
							[sending_app]. 
						</span>
					[/if]
					[if tag="date"]
						<span class="date">
							[date]. 
						</span>
					[/if]
					[if tag="tweet_actions"]
						<div class="tweet_actions">
							<div class="reply">
								[reply_link] 
							</div>
							<div class="retweet">
								[retweet_link] 
							</div>
							<div class="favorite">
								[favorite_link]
							</div>
						</div>
					[/if]
				</div>
			</li>
		[/loop]
	</ul>
</div>
[if tag="follow_button"]
<div class="follow_button">[follow_button]</div>
[/if]

[poselab_link]

[script name="jquery"]
[script url="twitter_ticker.js"]
[css url="twitter_ticker.css"]