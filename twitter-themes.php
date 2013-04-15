<?php
/*
	Plugin Name: Twitter themes
	Plugin URI: http://www.poselab.com/
	Description: A plugin that displays your latest tweets.
	Author: Javier Gómez Pose
	Author URI: http://www.poselab.com/
	Version: 1.3
	License: GPL2

		Copyright 2013 Javier Gómez Pose (email : javierpose@gmail.com)

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License, version 2, as
		published by the Free Software Foundation.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 *  Some functions from:
 *  Twitter Widget Pro
 *  Rotating Tweets
 */


/**
 * widget class.
 */

require_once 'lib/wptt-twitteroauth.php';

class Twitter_Themes_Widget extends WP_Widget {


	private $title = 'Twitter';

	//config
	private $screen_name = 'poselab';
	private $theme = 'twitter_ticker_theme.php';
	private $profile_image = 'normal';
	private $cache_time = 10;

	//tweets
	private $tweets_count = '10';
	private $show_retweets = 'false';
	private $show_replies = 'false';
	private $tweet_actions = 'false';

	//sending app
	private $sending_app = 'false';

	//date
	private $date = 'false';

	//links
	private $hyperlinks = 'false';
	private $target_blank = 'false';
	private $no_follow = 'false';

	//follow button
	private $follow_button = 'false';
	private $follow_show_screen_name = 'false';
	private $follow_show_count = 'false';
	private $follow_size = 'false';
	private $follow_data_align = 'false';

	//promotion
	private $poselab_link = 'false';

	//other
	private $i = 0;
	private $twitter_data;
	private $twitter_connection;
	private $current_theme_type = 'plugin';
	private $theme_type = array( 'plugin', 'custom' );


	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->_plugin_name = 'Twitter themes';
		$this->_slug = 'twitter-themes';
		$this->_file = plugin_basename( __FILE__ );

		define( 'TWITTER_THEMES_PATH', plugin_dir_path( __FILE__ ) );
		define( 'THEMES_PATH', get_template_directory( __FILE__ ).'/' );

		//localization
		load_plugin_textdomain( $this->_slug, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		add_shortcode( 'twitter_themes', array( $this, 'twitter_themes_shortcode' ) );

		//load admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_styles' ) );

		add_filter( 'plugin_action_links', array( $this, 'add_plugin_page_links' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'twitterthemes_settings_check' ) );
		add_action( 'admin_menu', array( $this, 'register_options_page' ) );
		add_action( 'admin_init', array( $this, 'twitterthemes_admin_init' ) );

		parent::__construct(
			'Twitter_Themes_Widget', // Base ID
			__( $this->_plugin_name, $this->_slug ), // Name
			array(
				'classname'  => 'twitter_themes_widget',
				'description' => __( 'A plugin that displays your latest tweets', $this->_slug )
			) // Args
		);
	}


	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		echo $this->twitter_themes_content( $instance );

		echo $after_widget;
	}


	/**
	 * Back-end widget form.
	 */
	public function form( $instance ) {

		/* Set up default widget settings. */
		$defaults = array(
			'title' => $this->title,

			//config
			'screen_name' => $this->screen_name,
			'theme' => $this->theme,
			'profile_image' => $this->profile_image,
			'cache_time' => $this->cache_time,

			//tweets
			'tweets_count' => $this->tweets_count,
			'show_retweets'=> $this->show_retweets,
			'show_replies'=> $this->show_replies,
			'tweet_actions' => $this->tweet_actions,

			//links
			'hyperlinks' => $this->hyperlinks,
			'target_blank' => $this->target_blank,
			'no_follow' => $this->no_follow,

			//sending app
			'sending_app' => $this->sending_app,

			//date
			'date' => $this->date,

			//follow button
			'follow_button' => $this->follow_button,
			'follow_show_screen_name' => $this->follow_show_screen_name,
			'follow_show_count' => $this->follow_show_count,
			'follow_size' => $this->follow_size,
			'follow_data_align' => $this->follow_data_align,

			//promotion
			'poselab_link' => $this->poselab_link,

		);

		$instance = wp_parse_args( (array) $instance, $defaults );
?>

		<script type="text/javascript">
			// js for hide unused controls
			jQuery(document).ready(function($) {

				var theme_select = '#<?php echo $this->get_field_id( 'theme' ); ?>';

				change_theme();
				$(theme_select).change(function () {
					change_theme();
				});
				function change_theme(){
					<?php
		/**
		 * check used controls in theme
		 */
		foreach ( $this->theme_type as $theme ) {
			foreach ( $this->get_themes( 'filename' , $theme ) as $filename ) {

				$theme_content = wp_remote_get( $this->get_theme_url( $filename, $theme ) );
				//print_r( $this->get_theme_url( $filename ) );
				$used_controls = $this->strpos_array( $theme_content['body'], array( '[profile_image]', '[reply_link]', '[retweet_link]', '[favorite_link]', '[follow_button]', '[poselab_link]', '[sending_app]', '[date]' ) );
?>
							if($(theme_select + ' option:selected').val() === '<?php echo $filename; ?>'){

								//console.log('<?php echo $filename; ?>');
								$('.twitter_themes_optional').hide();
								$('<?php echo $used_controls ; ?>.showcontrol').slideDown('fast');
							}
<?php
			}
		}
?>
				}
			});
		</script>

		<div class="twitter_themes">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->_slug ) ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'screen_name' ); ?>"><?php _e( 'Twitter user name', $this->_slug ) ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'screen_name' ); ?>" name="<?php echo $this->get_field_name( 'screen_name' ); ?>" value="<?php echo $instance['screen_name']; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'cache_time' ); ?>"><?php _e( 'Refresh time (in minutes)', $this->_slug ) ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'cache_time' ); ?>" name="<?php echo $this->get_field_name( 'cache_time' ); ?>" value="<?php echo $instance['cache_time']; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'theme' ); ?>"><?php _e( 'Select theme', $this->_slug ); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'theme' ); ?>" name="<?php echo $this->get_field_name( 'theme' ); ?>">
<?php
		foreach ( $this->theme_type as $theme ) {
			foreach ( $this->get_themes( 'filename' , $theme ) as $filename ) {
?>
					<option value="<?php echo $filename; ?>"<?php selected( $instance['theme'], $filename ); ?>><?php _e( $this->get_theme_title( $filename ) , $this->_slug ); ?></option>
<?php
			}
		}
?>
				</select>
			</p>

			<p class="<?php echo $this->_slug; ?>-profile_image twitter_themes_optional">
				<label for="<?php echo $this->get_field_id( 'profile_image' ); ?>"><?php _e( 'Display profile image', $this->_slug ); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'profile_image' ); ?>" name="<?php echo $this->get_field_name( 'profile_image' ); ?>">
					<option value="false"<?php selected( $instance['profile_image'], 'false' ) ?>><?php _e( 'Do not show', $this->_slug ); ?></option>
					<option value="mini"<?php selected( $instance['profile_image'], 'mini' ) ?>><?php _e( 'Mini - 24px by 24px', $this->_slug ); ?></option>
					<option value="normal"<?php selected( $instance['profile_image'], 'normal' ) ?>><?php _e( 'Normal - 48px by 48px', $this->_slug ); ?></option>
					<option value="bigger"<?php selected( $instance['profile_image'], 'bigger' ) ?>><?php _e( 'Bigger - 73px by 73px', $this->_slug ); ?></option>
					<option value="original"<?php selected( $instance['profile_image'], 'original' ) ?>><?php _e( 'Original', $this->_slug ); ?></option>
				</select>
			</p>


		<!-- Tweet content -->
		<fieldset>
			<legend>
				<?php _e( 'Tweet content', $this->_slug ) ?>
			</legend>

			<p>
				<label for="<?php echo $this->get_field_id( 'tweets_count' ); ?>"><?php _e( 'Number of tweets (max 200, included retweets and replies)', $this->_slug ) ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'tweets_count' ); ?>" name="<?php echo $this->get_field_name( 'tweets_count' ); ?>" value="<?php echo $instance['tweets_count']; ?>" />
			</p>

			<p class="<?php echo $this->_slug; ?>-reply_link <?php echo $this->_slug; ?>-retweet_link <?php echo $this->_slug; ?>-favorite_link twitter_themes_optional">
				<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['tweet_actions'], 'true' ) ?> id="<?php echo $this->get_field_id( 'tweet_actions' ); ?>" name="<?php echo $this->get_field_name( 'tweet_actions' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'tweet_actions' ); ?>"><?php _e( 'Show Tweet Actions', $this->_slug ); ?></label>
			</p>

			<p>
				<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['show_retweets'], 'true' ) ?> id="<?php echo $this->get_field_id( 'show_retweets' ); ?>" name="<?php echo $this->get_field_name( 'show_retweets' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_retweets' ); ?>"><?php _e( 'Include retweets', $this->_slug ); ?></label>
			</p>

			<p>
				<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['show_replies'], 'true' ) ?> id="<?php echo $this->get_field_id( 'show_replies' ); ?>" name="<?php echo $this->get_field_name( 'show_replies' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_replies' ); ?>"><?php _e( 'Include replies', $this->_slug ); ?></label>
			</p>

			<p class="<?php echo $this->_slug; ?>-sending_app twitter_themes_optional">
				<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['sending_app'], 'true' ) ?> id="<?php echo $this->get_field_id( 'sending_app' ); ?>" name="<?php echo $this->get_field_name( 'sending_app' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'sending_app' ); ?>"><?php _e( 'Show sending application', $this->_slug ); ?></label>
			</p>

			<p class="<?php echo $this->_slug; ?>-date twitter_themes_optional">
				<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['date'], 'true' ) ?> id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e( 'Show date', $this->_slug ); ?></label>
			</p>

			<fieldset>

				<legend>
					<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['hyperlinks'], 'true' ) ?> id="<?php echo $this->get_field_id( 'hyperlinks' ); ?>" name="<?php echo $this->get_field_name( 'hyperlinks' ); ?>" />
					<label for="<?php echo $this->get_field_id( 'hyperlinks' ); ?>"><?php _e( 'Show links', $this->_slug ); ?></label>
				</legend>

				<p>
					<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['target_blank'], 'true' ) ?> id="<?php echo $this->get_field_id( 'target_blank' ); ?>" name="<?php echo $this->get_field_name( 'target_blank' ); ?>" />
					<label for="<?php echo $this->get_field_id( 'target_blank' ); ?>"><?php _e( 'Open links in a new window', $this->_slug ); ?></label>
				</p>

				<p>
					<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['no_follow'], 'true' ) ?> id="<?php echo $this->get_field_id( 'no_follow' ); ?>" name="<?php echo $this->get_field_name( 'no_follow' ); ?>" />
					<label for="<?php echo $this->get_field_id( 'no_follow' ); ?>"><?php _e( 'Add "nofollow" attributte to links', $this->_slug ); ?></label>
				</p>

			</fieldset>

		</fieldset>


		<!-- Follow Button -->
		<fieldset class="<?php echo $this->_slug; ?>-follow_button twitter_themes_optional">

			<legend>
				<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['follow_button'], 'true' ) ?> id="<?php echo $this->get_field_id( 'follow_button' ); ?>" name="<?php echo $this->get_field_name( 'follow_button' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'follow_button' ); ?>"><?php _e( 'Show Follow Button', $this->_slug ); ?></label>
			</legend>

			<p>
				<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['follow_show_screen_name'], 'true' ) ?> id="<?php echo $this->get_field_id( 'follow_show_screen_name' ); ?>" name="<?php echo $this->get_field_name( 'follow_show_screen_name' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'follow_show_screen_name' ); ?>"><?php _e( 'Show Screen Name', $this->_slug ); ?></label>
			</p>

			<p>
				<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['follow_show_count'], 'true' ) ?> id="<?php echo $this->get_field_id( 'follow_show_count' ); ?>" name="<?php echo $this->get_field_name( 'follow_show_count' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'follow_show_count' ); ?>"><?php _e( 'Followers count display', $this->_slug ); ?></label>
			</p>

			<p>
				<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['follow_size'], 'true' ) ?> id="<?php echo $this->get_field_id( 'follow_size' ); ?>" name="<?php echo $this->get_field_name( 'follow_size' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'follow_size' ); ?>"><?php _e( 'Large Follow Button', $this->_slug ); ?></label>
			</p>

			<p>
				<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['follow_data_align'], 'true' ) ?> id="<?php echo $this->get_field_id( 'follow_data_align' ); ?>" name="<?php echo $this->get_field_name( 'follow_data_align' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'follow_data_align' ); ?>"><?php _e( 'Button alignment left', $this->_slug ); ?></label>
			</p>

		</fieldset>

			<p class="<?php echo $this->_slug; ?>-poselab_link twitter_themes_optional">
				<input class="checkbox" type="checkbox" value="true" <?php checked( $instance['poselab_link'], 'true' ) ?> id="<?php echo $this->get_field_id( 'poselab_link' ); ?>" name="<?php echo $this->get_field_name( 'poselab_link' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'poselab_link' ); ?>"><?php _e( ' Show Link to PoseLab', $this->_slug ); ?></label>
			</p>
		</div>
<?php
	}


	/*--------------------------------------------------*/
	/* Private Functions
	/*--------------------------------------------------*/

	private function twitter_themes_content( $instance ) {

		/* Our variables from the widget settings. */
		$this->title = apply_filters( 'widget_title', $instance['title'] );

		//config
		$this->screen_name = $instance['screen_name'];
		$this->theme = $instance['theme'];
		$this->profile_image = $instance['profile_image'];
		$this->cache_time = $instance['cache_time'];

		//tweets
		$this->tweets_count = $instance['tweets_count'];
		$this->show_retweets = isset( $instance['show_retweets'] ) ? $instance['show_retweets'] : 'false';
		$this->show_replies =  isset( $instance['show_replies'] ) ? $instance['show_replies'] = false : true; //'cos I ask for include replies and twitter checks exclude_replies
		$this->tweet_actions = isset( $instance['tweet_actions'] ) ? $instance['tweet_actions'] : 'false';

		//links
		$this->hyperlinks = isset( $instance['hyperlinks'] ) ? $instance['hyperlinks'] : 'false';
		$this->target_blank = isset( $instance['target_blank'] ) ? $instance['target_blank'] : 'false';
		$this->no_follow = isset( $instance['no_follow'] ) ? $instance['no_follow'] : 'false';

		//sending app
		$this->sending_app = isset( $instance['sending_app'] ) ? $instance['sending_app'] : 'false';

		//date
		$this->date = isset( $instance['date'] ) ? $instance['date'] : 'false';

		//follow button
		$this->follow_button = isset( $instance['follow_button'] ) ? $instance['follow_button'] : 'false';
		$this->follow_show_screen_name = isset( $instance['follow_show_screen_name'] ) ? $instance['follow_show_screen_name'] : 'false';
		$this->follow_show_count = isset( $instance['follow_show_count'] ) ? $instance['follow_show_count'] : 'false';
		$this->follow_size = isset( $instance['follow_size'] ) ? 'large' : 'medium';
		$this->follow_data_align = isset( $instance['follow_data_align'] ) ? 'left' : 'right';

		//promotion
		$this->poselab_link = isset( $instance['poselab_link'] ) ? $instance['poselab_link'] : 'false';

		//other

		$this->i=0;
		$trans_name = 'twitter_themes_transient4' . $this->screen_name;
		$this->current_theme_type = $this->get_current_theme_type( $this->theme );


		/**
		 * Content
		 * --------------------------
		 */
		$content = '';

		$this->twitter_data = null;

		if ( !get_transient( $trans_name ) ) {

			$api = get_option( 'twitterthemes-api-settings' );

			$this->get_twitter_connection( $api );

			
			//print_r( $this->twitter_data );

			// Save our new transient.
			if ( $this->twitter_connection->http_code == 200 ) {
				set_transient( $trans_name, $this->twitter_data, $this->cache_time ); //* MINUTE_IN_SECONDS
			}

		}
		$this->twitter_data = get_transient( $trans_name );


		//Shortcodes for use in plugin themes
		//-----------------------------------

		//[if tag="follow_button/profile_image/date/tweet_actions/sending_app/date/poselab_link"]
		add_shortcode( 'if', array( $this, 'get_if_shortcode' ) );

		//[follow_button]
		add_shortcode( 'follow_button', array( $this, 'get_follow_button_shortcode' ) );
		//[follow_data_align]
		add_shortcode( 'follow_data_align', array( $this, 'get_follow_data_align_shortcode' ) );

		//[target_blank]
		add_shortcode( 'target_blank', array( $this, 'get_target_blank_shortcode' ) );

		//[profile_image]
		add_shortcode( 'profile_image', array( $this, 'get_profile_image_shortcode' ) );
		//[profile_image_size]
		add_shortcode( 'profile_image_size', array( $this, 'get_profile_image_size_shortcode' ) );
		//[screen_name]
		add_shortcode( 'user_screen_name', array( $this, 'get_user_screen_name_shortcode' ) );
		//[name]
		add_shortcode( 'user_name', array( $this, 'get_user_name_shortcode' ) );
		//[user_description]
		add_shortcode( 'user_description', array( $this, 'get_user_description_shortcode' ) );
		//[user_location]
		add_shortcode( 'user_location', array( $this, 'get_user_location_shortcode' ) );
		//[user_url]
		add_shortcode( 'user_url', array( $this, 'get_user_url_shortcode' ) );
		//[user_url]
		add_shortcode( 'user_followers_count', array( $this, 'get_user_followers_count' ) );
		//[user_friends_count]
		add_shortcode( 'user_friends_count', array( $this, 'get_user_friends_count' ) );

		// Loop of tweets
		//---------------
		//[loop]
		add_shortcode( 'loop', array( $this, 'get_tweets_shortcode' ) );
		//[loop_number]
		add_shortcode( 'loop_number', array( $this, 'get_loop_number_shortcode' ) );
		//[loop_number]
		add_shortcode( 'loop_even_odd', array( $this, 'get_loop_even_odd_shortcode' ) );
		//[tweet_text]
		add_shortcode( 'tweet_text', array( $this, 'get_tweet_text_shortcode' ) );
		//[date]
		add_shortcode( 'date', array( $this, 'get_date_shortcode' ) );

		// intents
		//---------------
		//[reply_link]
		add_shortcode( 'reply_link', array( $this, 'get_reply_link_shortcode' ) );
		//[retweet_link]
		add_shortcode( 'retweet_link', array( $this, 'get_retweet_link_shortcode' ) );
		//[favorite_link]
		add_shortcode( 'favorite_link', array( $this, 'get_favorite_link_shortcode' ) );

		//[sending_app]
		add_shortcode( 'sending_app', array( $this, 'get_sending_app_shortcode' ) );

		//[poselab_link]
		add_shortcode( 'poselab_link', array( $this, 'get_poselab_link_shortcode' ) );

		//[theme_url]
		add_shortcode( 'theme_url', array( $this, 'get_theme_url_shortcode' ) );

		//[once]
		add_shortcode( 'once', array( $this, 'get_once_shortcode' ) );

		//[script url="" name="jquery"]
		add_shortcode( 'script', array( $this, 'get_script' ) );

		//[css url=""]
		add_shortcode( 'css', array( $this, 'get_css' ) );

		//end shortcodes
		//-----------------------------------


		//get theme
		$theme_content = wp_remote_get( $this->get_theme_url( $this->theme, $this->current_theme_type ) );

		//get content
		$content .= '<div class="twitter_themes">';
		$content .= do_shortcode( $theme_content['body'] );
		$content .= '</div>';

		return $content;
	}


	/**
	 * Shortcodes to use in plugin themes
	 * -----------------------------------
	 */
	//[if tag="follow_button/profile_image/date/tweet_actions/sending_app/date/poselab_link"]
	function get_if_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
					'tag' => '',
				), $atts ) );
		//print_r($tag . ': ' . $this->$tag . '<br>');
		if ( $this->$tag != 'false' ) {
			return do_shortcode( $content );
		}
	}

	//[profile_image]
	function get_profile_image_shortcode( $atts ) {
		return $this->get_profile_image( $this->twitter_data[0]->user->profile_image_url );
	}

	//[profile_image_size]
	function get_profile_image_size_shortcode( $atts ) {
		return $this->profile_image;
	}

	//[user_screen_name]
	function get_user_screen_name_shortcode( $atts ) {
		return $this->twitter_data[0]->user->screen_name;
	}

	//[user_name]
	function get_user_name_shortcode( $atts ) {
		return $this->twitter_data[0]->user->name;
	}

	//[user_description]
	function get_user_description_shortcode( $atts ) {
		return $this->twitter_data[0]->user->description;
	}

	//[user_location]
	function get_user_location_shortcode( $atts ) {
		return $this->twitter_data[0]->user->location;
	}

	//[user_url]
	function get_user_url_shortcode( $atts ) {
		return $this->twitter_data[0]->user->url;
	}

	//[user_followers_count]
	function get_user_followers_count( $atts ) {
		return $this->twitter_data[0]->user->followers_count;
	}

	//[user_friends_count]
	function get_user_friends_count( $atts ) {
		return $this->twitter_data[0]->user->friends_count;
	}

	//[poselab_link]
	function get_poselab_link_shortcode( $atts ) {
		return $this->get_poselab_link();
	}

	//[follow_button]
	function get_follow_button_shortcode( $atts ) {
		return $this->get_follow_button();
	}
	//[follow_data_align]
	function get_follow_data_align_shortcode( $atts ) {
		return $this->follow_data_align;
	}

	//[target_blank]
	function get_target_blank_shortcode( $atts ) {
		return $this->get_target_blank();
	}

	/**
	 * Shortcodes for the loop
	 */
	//[loop]
	function get_tweets_shortcode( $atts, $tweets="" ) {
		$tweets2 = '';
		foreach ( $this->twitter_data as $tweet ) {
			$tweets2 .= do_shortcode( $tweets );
			$this->i++;
		}
		return $tweets2;
	}

	//[loop_number]
	function get_loop_number_shortcode( $atts ) {
		return $this->i + 1;
	}

	//[loop_even_odd]
	function get_loop_even_odd_shortcode( $atts ) {
		if ( $this->i % 2 == 0 ) {
			return 'odd';
		} else {
			return 'even';
		}
	}

	//[tweet_text]
	function get_tweet_text_shortcode( $atts ) {
		return $this->get_hyperlinks( $this->twitter_data[$this->i]->text );
	}
	//[date]
	function get_date_shortcode( $atts ) {
		return $this->get_date( $this->twitter_data[$this->i] );
	}

	//Shortcodes for web intents
	//[reply_link]
	function get_reply_link_shortcode( $atts ) {
		return $this->get_tweet_actions( $this->twitter_data[$this->i]->id_str, 'reply' );
	}
	//[retweet_link]
	function get_retweet_link_shortcode( $atts ) {
		return $this->get_tweet_actions( $this->twitter_data[$this->i]->id_str, 'retweet' );
	}
	//[favorite_link]
	function get_favorite_link_shortcode( $atts ) {
		return $this->get_tweet_actions( $this->twitter_data[$this->i]->id_str, 'favorite' );
	}

	//[sending_app]
	function get_sending_app_shortcode( $atts ) {
		return $this->get_sending_app( $this->twitter_data[$this->i]->source );
	}

	//[theme_url]
	function get_theme_url_shortcode( $atts ) {
		return $this->get_theme_url( '', $this->current_theme_type );
	}

	//[once]
	function get_once_shortcode( $atts, $content = null ) {
		static $once = 1;
		if ( $once == 1 ) {
			return $content;
		}
		$once++;
	}

	//[script url="" name=""]
	function get_script( $atts, $content = null ) {
		extract( shortcode_atts( array(
					'url' => 'empty',
					'name' => 'jquery',
				), $atts ) );
		if ( $url != 'empty' ) {
			wp_enqueue_script( $this->_slug, $this->get_theme_url( $url, $this->current_theme_type ), false, false, true );
			//$js_values = array( 'speed' => '5000' );
			//wp_localize_script( $this->_slug, 'js_values', $js_values );
		}
		if ( $name == 'jquery' ) {
			wp_enqueue_script( 'jquery' );
		}
	}

	//[css url=""]
	function get_css( $atts, $content = null ) {
		extract( shortcode_atts( array(
					'url' => 'empty'
				), $atts ) );
		if ( $url != 'empty' ) {
			wp_enqueue_style( $this->_slug, $this->get_theme_url( $url, $this->current_theme_type ), false, false, 'all' );
		}
	}


	/*
	*/
	/**
	 * Find links and create the hyperlinks
	 */
	private function get_hyperlinks( $text ) {
		if ( $this->hyperlinks == 'true' ) {
			$text = preg_replace( '/\b([a-zA-Z]+:\/\/[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i', '<a href="$1" class="twitter-link" '. $this->get_target_blank() . $this->get_no_follow(). '>$1</a>', $text );
			$text = preg_replace( '/\b(?<!:\/\/)(www\.[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i', "<a href=\"http://$1\" class=\"twitter-link\">$1</a>", $text );
			// match name@address
			$text = preg_replace( "/\b([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})\b/i", '<a href="mailto://$1" class="twitter-mails" '. $this->get_target_blank() . $this->get_no_follow(). '>$1</a>', $text );
			//mach #trendingtopics. Props to Michael Voigt
			$text = preg_replace( '/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)#{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', '$1<a href="http://twitter.com/#search?q=$2" class="twitter-hastag" '. $this->get_target_blank() . $this->get_no_follow(). '>#$2</a>$3 ', $text );

			//Find twitter screen_names and link to them
			$text = preg_replace( '/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', '$1<a href="http://twitter.com/$2" class="twitter-user" '. $this->get_target_blank() . $this->get_no_follow(). '>@$2</a>$3 ', $text );
		}
		return $text;
	}

	/**
	 * twitter time
	 */
	private function get_date( $tweet ) {
		$string = '<a ' . $this->get_target_blank() . ' href="https://twitter.com/twitterapi/status/'.$tweet->id_str.'">';
		$tweettimestamp = strtotime( $tweet->created_at );
		$string .= $this->get_date_format( $tweettimestamp );
		$string .= '</a>';
		return $string;
	}

	// Converts Tweet timestamp into a short time description - as specified by Twitter
	private function get_date_format( $small_ts, $large_ts=false ) {
		if ( !$large_ts ) $large_ts = time();
		$n = $large_ts - $small_ts;
		if ( $n < ( 60 ) ) return sprintf( _x( '%ds', 'abbreviated timestamp in seconds', $this->_slug ), $n );
		if ( $n < ( 60*60 ) ) { $minutes = round( $n/60 ); return sprintf( _x( '%dm', 'abbreviated timestamp in minutes', $this->_slug ), $minutes ); }
		if ( $n < ( 60*60*24 ) ) { $hours = round( $n/( 60*60 ) ); return sprintf( _x( '%dh', 'abbreviated timestamp in hours', $this->_slug ), $hours ); }
		if ( $n < ( 60*60*24*364 ) ) return date( _x( 'j M', 'short date format as per http://uk.php.net/manual/en/function.date.php', $this->_slug ), $small_ts );
		return date( _x( 'j M Y', 'slightly longer date format as per http://uk.php.net/manual/en/function.date.php', $this->_slug ), $small_ts );
	}


	// get profile image
	private function get_profile_image( $profile_image_url ) {
		if ( $this->profile_image != 'false' ) {
			if ( $this->profile_image == 'original' ) {
				$size = '';
			}else {
				$size = '_' . $this->profile_image;
			}

			$profile_url_normal = $profile_image_url;

			$profile_image_url = str_replace( '_normal', $size, $profile_url_normal );
			return '<img src="'. $profile_image_url .'" title="'. $this->screen_name .'" class="profile_image">';
		}
	}

	// get sending app
	private function get_sending_app( $tweet_source ) {
		if ( $this->sending_app == 'true' ) {
			$sending_app = sprintf( __( 'from %s', $this->_slug ), str_replace( '&', '&amp;', $tweet_source ) );
			return $sending_app;
		}
	}


	// html for target
	private function get_target_blank() {
		if ( $this->target_blank == 'true' ) {
			$target = 'target="_blank"';
			return $target;
		}
	}


	// html for target
	private function get_no_follow() {
		if ( $this->no_follow == 'true' ) {
			$nofollow = 'rel="nofollow"';
			return $nofollow;
		}
		return;
	}


	// follow link
	private function get_follow_button() {
		if ( $this->follow_button == 'true' ) {

			$follow = '<a href="https://twitter.com/'. $this->screen_name .'" class="twitter-follow-button" data-show-screen-name="'. $this->follow_show_screen_name .'"  data-show-count="'. $this->follow_show_count .'" data-size="'. $this->follow_size .'" data-width="100%" data-align="'. $this->follow_data_align .'" data-language="'. $this->get_language_code() .'"></a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
			return $follow;
		}
		return;
	}


	// language
	private function get_language_code() {
		$valid_langs = array(
			'en', // English
			'it', // Italian
			'es', // Spanish
			'fr', // French
			'ko', // Korean
			'ja', // Japanese
		);
		$language = strtolower( substr( get_locale(), 0, 2 ) );
		if ( in_array( $language, $valid_langs ) ) {
			return $language;
		} else {
			return 'en';
		}
	}


	// poselab link
	private function get_poselab_link() {
		if ( $this->poselab_link == 'true' ) {
			$poselab = '<a href="http://poselab.com/" class="poselab" target="_blank">Powered by PoseLab</a>';
			return $poselab;
		}
		return;
	}

	// Web intents
	private function get_tweet_actions( $id, $intent_to_show ) {
		if ( $this->tweet_actions == 'true' ) {
			//$this->enqueue_twitter_js();

			if ( $intent_to_show == 'reply' ) {
				$reply_text = __( 'Reply', $this->_slug );
				$reply = '<a href="https://twitter.com/intent/tweet?in_reply_to='. $id .'" class="icon_reply" title="'. $reply_text .'">'. $reply_text .'</a>';
				return $reply;
			} elseif ( $intent_to_show == 'retweet' ) {
				$retweet_text = __( 'Retweet', $this->_slug );
				$retweet = '<a href="https://twitter.com/intent/retweet?tweet_id='. $id .'" class="icon_retweet" title="'. $retweet_text .'">'. $retweet_text .'</a>';
				return $retweet;
			} elseif ( $intent_to_show == 'favorite' ) {
				$favorite_text = __( 'Favorite', $this->_slug );
				$favorite = '<a href="https://twitter.com/intent/favorite?tweet_id='. $id .'" class="icon_favorite" title="'. $favorite_text .'">'. $favorite_text .'</a>';
				return $favorite;
			}
		}
	}


	// enqueue widgets.js
	private function enqueue_twitter_js() {
		//$script = 'http://platform.twitter.com/widgets.js';
		if ( is_ssl() ) {
			$script = str_replace( 'http://', 'https://', $script );
		}
		wp_enqueue_script( 'twitter-widgets', $script, array(), '1.0.0', true );
	}


	//register admin styles
	public function register_admin_styles( $hook ) {
		if ( 'widgets.php' != $hook )
			return;
		wp_enqueue_style( $this->_slug, plugins_url( '/admin-styles.css', __FILE__ ) );
	}


	//get all themes. Themes names must end in _theme.php.
	private function get_themes( $path, $type ) {
		if ( $type == 'custom' ) {
			$all_themes = glob( THEMES_PATH . '/plugins/' . $this->_slug . '/*_custom.{php,htm*}', GLOB_BRACE );
		} else {
			$all_themes = glob( TWITTER_THEMES_PATH . '/themes/*_theme.{php,htm*}', GLOB_BRACE );
		}
		if ( $path == 'filename' ) {
			$all_themes = array_map( 'basename', $all_themes );
			return $all_themes;
		}
		if ( $path == 'file_system_directory' ) {
			return $all_themes;
		}
	}

	//theme url
	private function get_theme_url( $filename, $type = 'plugin' ) {
		//if ( strpos( $filename, '_custom.' ) !== false ) {
		if ( $type == 'custom' ) {
			$url = get_template_directory_uri() . '/plugins/' . $this->_slug . '/' . $filename;
		} else {
			$url = plugins_url( '/themes/'. $filename , __FILE__ );
		}
		return $url;
	}

	//theme current type
	private function get_current_theme_type( $filename ) {
		if ( strpos( $filename, '_custom.' ) !== false ) {
			$theme_type = 'custom';
		} else {
			$theme_type = 'plugin';
		}
		return $theme_type;
	}

	//theme title
	private function get_theme_title( $filename ) {
		$info = pathinfo( $filename );
		if ( strpos( $filename, '_custom.' ) !== false ) {
			$filename = str_replace( '_custom', '', $filename );
		} else {
			$filename = str_replace( '_theme', '', $filename );
		}
		$filename = basename( $filename, '.'.$info['extension'] );
		return ucfirst( str_replace( '_', ' ', $filename ) );
	}

	//replace function to get used controls
	function strpos_array( $haystack, $needles ) {
		if ( is_array( $needles ) ) {
			$class='';
			foreach ( $needles as $str ) {
				$pos = strpos( $haystack, $str );
				if ( $pos !== FALSE ) {
					$class .= str_replace( array( '[', '[ ', ']' ),  array( '.'.$this->_slug.'-', '.'.$this->_slug.'-', ', ' ), $str );
				}
			}
			return $class;
		}
	}


	/**
	 * Management page for the Twitter API options is adapted from the excelent plugin:
	 * Rotating Tweets by Martin Tod. http://www.martintod.org.uk
	 */

	public function twitterthemes_settings_check() {
		$api = get_option( 'twitterthemes-api-settings' );
		$error = get_option( 'twitterthemes_api_error' );
		$this->get_twitter_connection( $api );

		if ( !empty( $api ) ) {
			$apistring = implode( '', $api );
		}
		$optionslink = 'options-general.php?page=' . $this->_slug;
		if ( empty( $apistring ) ) {
			$msgString = __( 'Please update <a href="%1$s">your settings for ' .$this->_plugin_name . '</a>.', $this->_slug );
			echo "<div class='error'><p><strong>".sprintf( $msgString, $optionslink )."</strong></p></div>";
		} elseif ( $this->twitter_connection->http_code == 401 ) {
			echo "<div class='error'><p><strong>".sprintf( __( 'Please update <a href="%1$s">your settings for ' .$this->_plugin_name . '</a>. Currently ' .$this->_plugin_name . ' cannot authenticate you with Twitter using the details you have given.', $this->_slug ), $optionslink )."</strong></p></div>";
		}
	}

	public function get_options_url() {
		return admin_url( 'options-general.php?page=' . $this->_slug );
	}

	public function register_options_page() {
		add_options_page( __( $this->_plugin_name . ': Twitter API settings', $this->_slug ), $this->_plugin_name, 'manage_options', $this->_slug, array( $this, 'twitterthemes_call_twitter_API_options' ) );
	}

	public function twitterthemes_call_twitter_API_options() {
		echo '<div class="wrap">';
		screen_icon();
		echo '<h2>'.__( $this->_plugin_name . ': Twitter API settings', $this->_slug ).'</h2>';
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', $this->_slug ) );
		}

		echo '<div style="display: table-cell;vertical-align: top;">';
		echo '<form method="post" action="options.php">';
		settings_fields( 'twitterthemes_options' );
		do_settings_sections( 'twitterthemes_api_settings' );
		submit_button( __( 'Save', $this->_slug ) );
		echo '</form>';
		echo '</div>';

		echo '<div style="display: table-cell;vertical-align: top;padding: 40px 0 0 50px;">';
		do_settings_sections( 'twitterthemes_api_instructions' );
		echo '</div>';

		echo '</div>';
	}

	public function twitterthemes_admin_init() {
		register_setting( 'twitterthemes_options', 'twitterthemes-api-settings', array( $this, 'twitterthemes_api_validate' ) );

		add_settings_section( 'twitterthemes_api_main', __( 'Twitter API Settings', $this->_slug ), array( $this, 'twitterthemes_api_explanation' ), 'twitterthemes_api_settings' );
		add_settings_field( 'twitterthemes_key', __( 'Consumer Key', $this->_slug ), array( $this, 'twitterthemes_option_show_key' ), 'twitterthemes_api_settings', 'twitterthemes_api_main' );
		add_settings_field( 'twitterthemes_secret', __( 'Consumer Secret', $this->_slug ), array( $this, 'twitterthemes_option_show_secret' ), 'twitterthemes_api_settings', 'twitterthemes_api_main' );
		add_settings_field( 'twitterthemes_token', __( 'Access Token', $this->_slug ), array( $this, 'twitterthemes_option_show_token' ), 'twitterthemes_api_settings', 'twitterthemes_api_main' );
		add_settings_field( 'twitterthemes_token_secret', __( 'Access Token Secret', $this->_slug ), array( $this, 'twitterthemes_option_show_token_secret' ), 'twitterthemes_api_settings', 'twitterthemes_api_main' );

		add_settings_section( 'twitterthemes_instructions', __( 'How to get your Twitter API Settings', $this->_slug ), array( $this, 'twitterthemes_option_show_instructions' ), 'twitterthemes_api_instructions' );
	}
	public function twitterthemes_option_show_key() {
		$options = get_option( 'twitterthemes-api-settings' );
		echo "<input id='twitterthemes_api_key_input' name='twitterthemes-api-settings[key]' size='70' type='text' value='{$options['key']}' />";
	}

	public function twitterthemes_option_show_secret() {
		$options = get_option( 'twitterthemes-api-settings' );
		echo "<input id='twitterthemes_api_secret_input' name='twitterthemes-api-settings[secret]' size='70' type='text' value='{$options['secret']}' />";
	}

	public function twitterthemes_option_show_token() {
		$options = get_option( 'twitterthemes-api-settings' );
		echo "<input id='twitterthemes_api_token_input' name='twitterthemes-api-settings[token]' size='70' type='text' value='{$options['token']}' />";
	}

	public function twitterthemes_option_show_token_secret() {
		$options = get_option( 'twitterthemes-api-settings' );
		echo "<input id='twitterthemes_api_token_secret_input' name='twitterthemes-api-settings[token_secret]' size='70' type='text' value='{$options['token_secret']}' />";
	}

	// Explanatory text
	public function twitterthemes_api_explanation() {
	}

	// instructions
	public function twitterthemes_option_show_instructions() {
		echo '<div>';
		echo '<ol><li><a href="https://dev.twitter.com/apps/new">';
		_e( 'Create a new Twitter application', $this->_slug );
		echo '</a></li><li>';
		_e( "Fill in Name, Description, Website" );
		echo '</li><li>';
		_e( "Agree to rules, fill out captcha, and submit your application" );
		echo '</li><li>';
		_e( "Copy the Consumer key, Consumer secret, Access Token and Access Token Secret into the left fields" );
		echo '</li><li>';
		_e( "Click the Save button" );
		echo '</li></ol></div>';


	}

	// validate our options
	function twitterthemes_api_validate( $input ) {
		$options = get_option( 'twitterthemes-api-settings' );
		$error = 0;
		// Check 'key'
		$options['key'] = trim( $input['key'] );
		if ( !preg_match( '/^[a-z0-9]+$/i', $options['key'] ) ) {
			$options['key'] = '';
			$error = 1;
			add_settings_error( $this->_slug, esc_attr( 'twitterthemes-api-key' ), __( 'Error: Twitter API Consumer Key not correctly formatted.', $this->_slug ) );
		}
		// Check 'secret'
		$options['secret'] = trim( $input['secret'] );
		if ( !preg_match( '/^[a-z0-9]+$/i', $options['secret'] ) ) {
			$options['secret'] = '';
			$error = 1;
			add_settings_error( $this->_slug, esc_attr( 'twitterthemes-api-secret' ), __( 'Error: Twitter API Consumer Secret not correctly formatted.', $this->_slug ) );
		}
		// Check 'token'
		$options['token'] = trim( $input['token'] );
		if ( !preg_match( '/^[a-z0-9]+\-[a-z0-9]+$/i', $options['token'] ) ) {
			$options['token'] = '';
			$error = 1;
			add_settings_error( $this->_slug, esc_attr( 'twitterthemes-api-token' ), __( 'Error: Twitter API Access Token not correctly formatted.', $this->_slug ) );
		}
		// Check 'token_secret'
		$options['token_secret'] = trim( $input['token_secret'] );
		if ( !preg_match( '/^[a-z0-9]+$/i', $options['token_secret'] ) ) {
			$options['token_secret'] = '';
			$error = 1;
			add_settings_error( $this->_slug, esc_attr( 'twitterthemes-api-token-secret' ), __( 'Error: Twitter API Access Token Secret not correctly formatted.', $this->_slug ) );
		}

		return $options;
	}

	/**
	 * Plugins section links
	 */

	public function add_plugin_page_links( $links, $file ) {
		if ( $file == $this->_file ) {
			// Add Widget Page link to our plugin
			$link = $this->get_options_link();
			array_unshift( $links, $link );

			// Add Support Forum link to our plugin
			$link = $this->get_support_forum_link();
			array_unshift( $links, $link );
		}
		return $links;
	}

	public function get_support_forum_link( $linkText = '' ) {
		if ( empty( $linkText ) ) {
			$linkText = __( 'Support', $this->_slug );
		}
		return '<a href="http://wordpress.org/support/plugin/' . $this->_slug . '">' . $linkText . '</a>';
	}

	public function get_options_link( $linkText = '' ) {
		if ( empty( $linkText ) ) {
			$linkText = __( 'Settings', $this->_slug );
		}
		$options_url = admin_url( 'options-general.php?page=' . $this->_slug );
		return '<a href="' . $options_url . '">' . $linkText . '</a>';
	}


	/**
	 * Shortcode
	 */
	function get_twitter_connection( $api ) {

		$this->twitter_connection = new WPTwTh_TwitterOAuth( $api['key'], $api['secret'], $api['token'], $api['token_secret'] );

		//user_timeline
		$this->twitter_data = $this->twitter_connection->get(
			'statuses/user_timeline',
			array(
				'screen_name' => $this->screen_name,
				'count' => $this->tweets_count,
				'include_rts' => $this->show_retweets,
				'exclude_replies' => $this->show_replies,
				'include_entities' => 1
			)
		);

		
	}


	/**
	 * Shortcode
	 */
	function twitter_themes_shortcode( $attr, $content = '' ) {

		$defaults = array(
			'title' => '',

			//config
			'screen_name' =>'',
			'theme' => $this->theme,
			'profile_image' => 'false',
			'cache_time' => '',

			//tweets
			'tweets_count' => '',
			'show_retweets'=> '',
			'show_replies'=> '',
			'tweet_actions' => '',

			//links
			'hyperlinks' => '',
			'target_blank' => '',
			'no_follow' => '',

			//sending app
			'sending_app' => '',

			//date
			'date' => 'false',

			//follow button
			'follow_button' => '',
			'follow_show_screen_name' => '',
			'follow_show_count' => '',
			'follow_size' => '',
			'follow_data_align' => '',

			//promotion
			'poselab_link' => ''

		);
		$attr = shortcode_atts( $defaults, $attr );

		return $this->twitter_themes_content( $attr );
	}
}// end Twitter_Themes_Widget

add_action( 'widgets_init', create_function( '', 'register_widget("Twitter_Themes_Widget");' ) );
