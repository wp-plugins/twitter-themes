=== Twitter Themes ===
Contributors: javitxu123
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=M88VD9UUWQFGW&lc=GB&item_name=Poselab&item_number=twitter%20themes&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Tags: widget, Twitter, Themes, tweet, tweets,animation, shortcode
Requires at least: 3.5.1
Tested up to: 3.5.1
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show an animation of the timeline of a Twitter account. Add a system template that allows you to customize the plugin with your own theme easily.


== Description == 

Twitter themes is a plugin that allows you to show the timeline of a Twitter account. The main difference from other plugins is that Twitter themes incorporates a system template that allows you to create your own theme for the plugin easily. If you know HTML and CSS you can create your own theme for the plugin and integrate it into your Wordpress theme. You can create your own HTML, CSS or JavaScript. The plugin includes several themes that you can use as reference to create your own theme and I hope to offer new themes.


= Demos: =
You can see a demo of the plugin in the following URLs:

* [Widget Demo](http://poselab.com/en/twitter-themes/) | [ES](http://poselab.com/twitter-themes/)
* [Demo with ‘Twitter ticker’ theme as shortcode](http://poselab.com/en/twitter-themes-twitter-ticker-theme-as-shortcode/) | [ES](http://poselab.com/twitter-themes-tema-twitter-ticker-como-shortcode)
* [Demo with ‘Twitter little ticker’ theme as shortcode](http://poselab.com/en/twitter-themes-twitter-little-ticker-theme-as-shortcode/) | [ES](http://poselab.com/twitter-themes-tema-twitter-little-ticker-como-shortcode)


= Features: =
* You can create your own themes, based on shortcodes, for the plugin to which you can assign CSS, JavaScript or images resources and customize completely.
* You can use the plugin as a widget where you can configure the options available for each theme.
* You can use the plugin as shortcode, to integrate it from the editor or from the php of your WordPresss theme.
* Using version 1.1 of the Twitter API.
* Using font icons so you can change the color of them from CSS.


= Widget fields: =
I will describe every field of the widget but keep in mind that not all fields appear in each theme.

* **Title:** the title of the widget. Shortcode attribute: title, value: text (optional).  
* **Twitter user name:** Twitter user name. Shortcode attribute: screen_name, value: text (required).
* **Refresh time (in minutes):** number of minutes that the information gathered from Twitter is stored in cache. Shortcode attribute: cache_time, value: number (optional).
* **Select theme:** field to select the theme that we will use with the plugin. Custom themes created by the user will also appear in this select. Shortcode attribute: theme, value: name of the PHP file used in the theme. The themes that come with the plugin are twitter_ticker_theme.php and twitter_little_ticker_theme.php, but I hope to offer new themes occasionally. The user can also create their own themes (optional).
* **Display profile image:** to select the image size of the user profile. Shortcode attribute: profile_image, value: false, mini, normal, bigger, original (optional).
* **Number of tweets:** number of tweets to be displayed in the widget. The total number includes the retweets and responses, so if you configure to not display them the total number will be less than the value set. The maximum number of tweets that can be requested to Twitter is 200 tweets. Shortcode attribute: tweets_count, value: number (optional).
* **Show Tweet Actions:** this option will show the tweet actions links on each tweet, reply, retweet and favorite. Shortcode attribute: tweet_actions, value: true or false (default), (optional).
* **Include retweets:** this option will show the retweets in the timeline. Shortcode attribute: show_retweets, value: true or false (default), (optional).
* **Include replies:** this option will show the replies in the timeline. Shortcode attribute: show_replies, value: true or false (default), (optional).
* **Show sending application:** this will show the application from which the tweet was sent. Shortcode attribute: sending_app, value: true or false (default), (optional).
* **Show date:** this option will show the creation date of the tweet with the format used by Twitter. Shortcode attribute: date, value: true or false (default), (optional).
* **Show links:** this option will display the tweet links. Shortcode attribute: hyperlinks, value: true or false (default) (optional).
* **Open links in new window:** The links will open in a new window. Shortcode attribute: target_blank, value: true or false (default), (optional).
* **Add "nofollow" attributte to links:** with this option selected rel="nofollow" will be added to links code to tell search engines to not follow this links. Shortcode attribute: no_follow, value: true or false (default), (optional).
* **Show Follow Button:** This option will display the Twitter follow button. Shortcode attribute: follow_button, value: true or false (default), (optional).
* **Show Screen Name:** the user name will be shown in the Follow button. Shortcode attribute: follow_show_screen_name, value: true or false (default), (optional).
* **Followers count display:** followers counter will be shown in the Follow button. Shortcode attribute: follow_show_count, value: true or false (default), (optional).
* **Large Follow Button:** This will display the big follow button. Shortcode attribute: follow_size, value: true or false (default), (optional).
* **Button alignment left:** this option aligns to the left the follow button. Shortcode attribute: follow_data_align, value: true or false (default), (optional).
* **Show Link to PoseLab:** It is a small link to my website. If you have installed this plugin on your website and want to thank me you can show this link to my website. Shortcode attribute: poselab_link, value: true or false (default), (optional).

== Installation ==

1. Use automatic installer to install and active the plugin.
1. You should see a notice appear in your admin that links you to the settings page.
1. Follow the instructions to setup your Twitter app and authenticate your account (an unfortunate step made necessary by Twitter's API changes).
1. In WordPress admin go to 'Appearance' -> 'Widgets' and add "Twitter themes" to one of your widget-ready areas of your site


== Other Notes ==
Some functions of this plugin and inspiration correspond to the following plugins:

* [Rotating Tweets](http://wordpress.org/extend/plugins/rotatingtweets/) by [Martin Tod](http://profiles.wordpress.org/mpntod/)
* [Twitter Widget Pro](http://wordpress.org/extend/plugins/rotatingtweets/) by [Range](http://profiles.wordpress.org/range/)


== Frequently Asked Questions ==

= How can I create themes for the plugin? =

Soon a tutorial will be ready.


== Screenshots ==

1. To use the widget, go to Appearance -> Widgets and Add "Twitter Themes" widget.
2. Each widget has settings that need to be set, so the next step is to click the down arrow on the right of the newly added widget and adjust all the settings.  When you're done click "Save"
3. This is what the widget looks like in the Twitter ticker theme.
4. This is what the widget looks like in the Twitter little ticker theme.


== Upgrade Notice ==

= 1.3 =
* First release

== Changelog ==

= 1.3 =
* First release
