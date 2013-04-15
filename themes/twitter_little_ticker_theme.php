
<div class="twitter_little_wrapper clearfix">
	[if tag="profile_image"]
		<div class="profile_image_content [profile_image_size]">
			[profile_image]
		</div>
	[/if]
	<div class="tweet_list_all">
		<ul class="tweet_list">
			[loop]
				<li class="tweet[loop_number]">
					<div class="tweet_content">
						<span class="tweet_text">
							[tweet_text]. 
						</span>
						[if tag="date"]
							<span class="date">
								[date]. 
							</span>
						[/if]
					</div>
				</li>
			[/loop]
		</ul>
	</div>
</div>

[poselab_link]

[script name="jquery"]
[script url="twitter_little_ticker.js"]
[css url="twitter_little_ticker.css"]