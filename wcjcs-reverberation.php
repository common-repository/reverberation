<?php
/*
Plugin Name: Reverberation
Plugin URI: https://johnalarcon.com
Description: Add Reverb Nation's HTML5 widgets to your posts, pages, or templates with simple shortcodes.  Works on iDevices, too!
Version: 0.1.1
Author: John Alarcon
Author URI: https://johnalarcon.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: reverbnation, streaming music, band, music, musician, media, player, playlist
Requires at least: 3.4
Tested up to: 3.4
Stable tag: 0.1.0
*/
	// Run the plugin.
	reverberation_startup();

/*
 * A function to get the plugin fired up and ready.
 *
 */
function reverberation_startup()
{
	// Create a settings link under Dashboard > Settings > Reverberation.
	add_action('admin_menu', 'reverberation_register_settings_link');
	// Register the plugin's settings group.
	add_action('admin_init', 'reverberation_register_settings');
	// Register the shortcode button.
	add_action('admin_init', 'reverberation_register_shortcode_button');
	// Enable shortcodes in post excerpts.
	add_filter('the_excerpt', 'do_shortcode');
	//Enable shortcodes in widgets.
	add_filter('widget_text', 'do_shortcode');
	// Enqueue admin style for help-screen.
	if (strpos($_SERVER['REQUEST_URI'], 'wp-admin') || strpos($_SERVER['SCRIPT_URL'], 'wp-admin')) {
		add_action('admin_enqueue_scripts', 'reverberation_register_scripts');
		add_action('admin_enqueue_scripts', 'reverberation_register_styles');
	}
	// Register the actual shortcode.
	add_shortcode('reverb', 'reverberation_get_widget');
}

/*
 * A function to save any plugin options.
 *
 */
function reverberation_register_settings()
{
	register_setting('reverberation_settings_group', 'reverberation_settings');
}

/*
 * A function to create a menu link at Dashboard > Settings > Reverberation
 *
 */
function reverberation_register_settings_link()
{
	add_options_page('Reverberation Options', 'Reverberation', 'administrator', 'wcjcs-reverberation-display-options', 'reverberation_get_widget_options');
}

/*
 * A function to register the shortcode button functionality.
 *
 */
function reverberation_register_shortcode_button()
{
    // Ensure sufficient privilege.
    if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
        // Append divider + button.
        add_filter('mce_buttons', 'reverberation_append_shortcode_button');
        // Assign button script.
        add_filter('mce_external_plugins', 'reverberation_append_shortcode_script');
    }
}

/*
 * A function to append the shortcode button to the $buttons array.
 *
 * @param	array	$buttons	Array of TinyMCE editor buttons.
 * @return	array	$buttons	Array of buttons, including Reverberation button.
 *
 */
function reverberation_append_shortcode_button($buttons)
{
    // Append a divider and the button, then return.
    array_push($buttons, '|', 'reverberation_button');
    return $buttons;
}

/*
 * A function to append the shortcode script file to the $plugins array.
 *
 * @param	array	$plugins	Array of TinyMCE visual editor scripts.
 * @return	array	$plugins	Array of scripts, include Reverberation script.
 *
 */
function reverberation_append_shortcode_script($plugins)
{
    // Assign script to plugins array and return.
    $plugins['reverberation'] = plugin_dir_url( __FILE__ ) . 'scripts/wcjcs-reverberation-visual-editor.js';
    return $plugins;
}

/*
 * A function to enqueue the plugin's extra scripts; not the core.
 *
 */
function reverberation_register_scripts()
{
	if (is_admin()) {
		wp_enqueue_script('reverberation-jscolor', plugins_url('/scripts/jscolor/jscolor.js', __FILE__), '', true);
	}
}

/*
 * A function to enqueue the plugin's stylesheet.
 *
 */
function reverberation_register_styles()
{
	if (is_admin()) {
		wp_enqueue_style('wcjcs-reverberation', plugins_url('/styles/wcjcs-reverberation.css', __FILE__));
	}
}

/*
 * A function to return the iframe'd widget.
 *
 * @param	array 	$attr	Array of attributes for a widget.
 * @return	string			Iframe'd widet.
 */
function reverberation_get_widget($attr)
{
	// Get widget properties based on passed attributes.
	$widget = reverberation_get_widget_properties($attr);
	// Render widget.
	return '<iframe src="'.$widget['url'].'" width="'.$widget['w'].'" height="'.$widget['h'].'" class="widget_iframe" frameborder="0" scrolling="no"></iframe>'."\n";
}

/*
 * A function to get widget types supported by Reverberation.
 *
 */
function reverberation_get_widget_types()
{
	// Array of all possible widget types.
	return array('newmusic', 'solosong', 'allsongs', 'playlist', 'schedule', 'maillist');
}

/*
 * A function to get defaults depending upon the type of widget
 *
 * @param	string	$type	To return specific default; otherwise return all.
 *
 */
function reverberation_get_widget_defaults($type=false)
{
	// Initialization.
	$defaults = array();
	// Defaults for each widget type: single song, full playlist, show schedule, fan collector
	$defaults['newmusic'] = array('type'=>'newmusic', 'wid'=>50, 'w'=>400, 'h'=>104, 'photo'=>false);
	$defaults['solosong'] = array('type'=>'solosong', 'wid'=>50, 'w'=>400, 'h'=>104, 'photo'=>false);
	$defaults['allsongs'] = array('type'=>'allsongs', 'wid'=>50, 'w'=>400, 'h'=>370, 'photo'=>false);
	$defaults['playlist'] = array('type'=>'playlist', 'wid'=>50, 'w'=>400, 'h'=>370, 'photo'=>false);
	$defaults['schedule'] = array('type'=>'schedule', 'wid'=>52, 'w'=>500, 'h'=>550);
	$defaults['maillist'] = array('type'=>'maillist', 'wid'=>54, 'w'=>300, 'h'=>185);
	// If requested, return defaults for a particular widget type.
	if ($type && isset($defaults[$type])) {
		return $defaults[$type];
	}
	// Return widget defaults.
	return $defaults;
}

/*
 * A function to populate a widget array based on passed attributes.
 *
 * @param	array	$attr	An array of shortcode attributes.
 * @return	array	$widget	A widget array populated with shortcode attributes.
 *
 */
function reverberation_get_widget_properties($attr)
{
	// Defaults are used where any needed attributes are omitted.
	$defaults = reverberation_get_widget_defaults();
	// Initialize widget settings array.
	$widget = array();
	// Artist id. Required for all widgets, except playlist widget.
	$widget['aid'] = (isset($attr['aid']) && is_numeric($attr['aid'])) ? $attr['aid'] : false;
	// Song id. Only required when creating a solosong widget.
	$widget['sid'] = (isset($attr['sid']) && is_numeric($attr['sid'])) ? $attr['sid'] : false;
	// Playlist id. Only required when creating a playlist widget.
	$widget['pid'] = (isset($attr['pid']) && is_numeric($attr['pid'])) ? $attr['pid'] : false;
	// Widget type.
	$widget['type'] = (isset($attr['type']) && (in_array($attr['type'], reverberation_get_widget_types()))) ? $attr['type'] : 'solosong';
	// Widget id; internal ReverbNation widget id.
	$widget['wid'] = $defaults[$widget['type']]['wid'];
	// Initial size value; is reset below to 'custom' or 'fit' if non-default width or height are used.
	$widget['size'] = 'undefined';
	// Initial design value; is reset below to 'customized' if non-default background color is used.
	$widget['design'] = 'default';
	// Widget width.
	$widget['w'] = (isset($attr['w']) && is_numeric($attr['w'])) ? $attr['w'] : $defaults[$widget['type']]['w'];
	// Widget height.
	$widget['h'] = (isset($attr['h']) && is_numeric($attr['h']) && $widget['type'] != 'newmusic') ? $attr['h'] : $defaults[$widget['type']]['h'];
	// Widget background color.
	$widget['bg'] = (isset($attr['bg']) && preg_match('~^#?([0-9A-Fa-f]{6}|[0-9A-Fa-f]{3});?$~', $attr['bg'])) ? str_replace(array('#',';'), array('',''), trim($attr['bg'])) : '333333';
	// Widget stretch; stretch-to-fit horizontally.
	$widget['fit'] = (isset($attr['fit']) && $attr['fit']==1) ? true : false;
	// Widget photo; applies only to 'solosong', 'allsongs', and 'playlist' widget types.
	$widget['photo'] = (isset($attr['photo']) && $attr['photo']==1) ? '1%2C0' : '0';
	// Widget posted by.
	$widget['posted_by'] = (isset($attr['posted_by']) && is_numeric($attr['posted_by'])) ? $attr['posted_by'] : 'artist_909491';
	// Widget layout; applies only to schedule widget.
	$widget['layout'] = (isset($attr['layout']) && in_array($attr['layout'], array('detailed'))) ? $attr['layout'] : 'compact';
	// Widget map; applies only to schedule widget.
	$widget['show_map'] = (isset($attr['show_map']) && $attr['show_map']==1) ? '1%2C0' : '0';
	// Reset size if width or height differ from default values.
	if ($widget['w'] != $defaults[$widget['type']]['w'] || $widget['h'] != $defaults[$widget['type']]['h']) {
		$widget['size'] = 'custom';
	}
	// Reset size and w if $fit was passed.
	if ($widget['fit']) {
		$widget['size'] = 'fit';
		$widget['w'] = '100%';
	}
	// Reset design if non-default bg color was passed.
	if ($widget['bg'] != '333333') {
		$widget['design'] = 'customized';
	}
	// Get iframe URL for given widget.
	$widget['url'] = reverberation_get_widget_url($widget);
	// Return widget array.
	return $widget;
}

/*
 * A function to build widget iframe target URL.
 *
 * @param	array	$widget	A widget array populated with shortcode attributes.
 * @return	string	$url	The target URL for the widget iframe.
 *
 */
function reverberation_get_widget_url($widget)
{
	// Base widget URL; concatenated from here.
	$url = 'http://www.reverbnation.com/widget_code/html_widget/';
	// Continue url; slight variation for playlist widget.
	$url .= ($widget['type']=='playlist') ? 'playlist_'.$widget['pid'] : 'artist_'.$widget['aid'];
	// Appending more to the URL; these first few lines apply to all widgets.
	$url .= '?widget_id='.$widget['wid'];
	$url .= '&posted_by='.$widget['posted_by'];
	$url .= '&pwc[design]='.$widget['design'];
	$url .= '&pwc[background_color]=%23'.$widget['bg'];
	$url .= '&pwc[size]='.$widget['size'];
	// All widgets except playlist widget require artist id.
	if (!$widget['aid'] && $widget['type'] != 'playlist') {
		return _e('Invalid Artist Id');
	}
	// If playlist widget, need pid or fail.
	if ($widget['type'] == 'playlist' && !$widget['pid']) {
		return _e('Invalid Playlist Id');
	}
	// If solosong widget, need sid or fail.
	if ($widget['type'] == 'solosong' && !$widget['sid']) {
		return _e('Invalid Song Id');
	}
	// Music player widgets only: which song(s) to include, photo.
	if ($widget['type'] == 'newmusic' || $widget['type'] == 'solosong' || $widget['type'] == 'allsongs' || $widget['type'] == 'playlist') {
		$url .= '&pwc[included_songs]='.(($widget['type'] == 'solosong') ? '0' : '1');
		$url .= '&pwc[photo]='.$widget['photo'];
		if ($widget['type'] == 'solosong') { // Only needed for solosong widget.
			$url .= '&pwc[song_ids]='.$widget['sid'];
		}
	}
	// Add map and set layout elements for schedule widget.
	if ($widget['type'] == 'schedule') {
		$url .= '&pwc[show_map]='.$widget['show_map'];
		$url .= '&pwc[layout]='.$widget['layout'];
	}
	// Return assembled URL.
	return $url;
}

/*
 * A function to get an Artist ID from a Song ID.
 *
 * @param	string	$sid	A ReverbNation song ID number.
 * @return	string	$aid	The ReverbNation song artist's ID number.
 *
 * @todo	Decide if worth extra HTTP requests based on user input.
 * @todo	Integrate the functionality or ditch this function.
 *
 */
function reverberation_get_aid_from_sid($sid)
{
	// No sid?  Fail.
	if (!$sid) {
		return false;
	}
	// URL to scrape.
	$url = 'http://www.reverbnation.com/play_now/song_'.$sid;
	// Get headers from target URL.
	$headers = get_headers($url, 1);
	// If not status 200 OK, fail.
	if ($headers['Status'] !== '200 OK') {
		return false;
	}
	// Scrape song page markup into array.
	$markup = file($url);
	// Loop through markup.
	foreach ($markup as $line) {
		// Find the line that has the artist id, then break.
		if (strstr($line, '"og:video"')) {
			break;
		}
	}
	// Search $line for pattern indicating artist id, or fail.
	if (!strstr($line, 'artist_') || !preg_match('/(\w{6}_\d+)/', $line, $artist_id_string)) {
		return false;
	}
	// Separate artist id string into an array.
	$aid_kv_array = explode('_', $artist_id_string[0]);
	// And finally, return the artist id.
	return (int)$aid_kv_array[1];
}

/*
 * A function to display plugin settings in the admin.
 *
 * @todo	Integrate settings or remove this and related functions.
 *
 */
function reverberation_get_widget_options()
{
	return;
}

?>