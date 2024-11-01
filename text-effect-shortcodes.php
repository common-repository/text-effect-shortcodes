<?php
/*
Plugin Name: Text Effect Shortcodes
Description: Create HTML text effects by surrounding your line by shortcodes. Blinking, scrolling text and more!
Version: 1.0.0
Author: Moki-Moki Ios
Author URI: http://mokimoki.net/
Text Domain: text-effect-shortcodes
License: GPL3
*/

/*
Copyright (C) 2017 Moki-Moki Ios http://mokimoki.net/

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Text Effect Shortcodes
 * Shortcodes for text effects
 *
 * @version 1.0.0
 */

if (!defined('ABSPATH')) return;

add_action('init', array(TextEffectShortcodes::get_instance(), 'initialize'));
add_action('admin_notices', array(TextEffectShortcodes::get_instance(), 'plugin_activation_notice'));
register_activation_hook(__FILE__, array(TextEffectShortcodes::get_instance(), 'setup_plugin_on_activation')); 

/**
 * Main class of the plugin.
 */
class TextEffectShortcodes {
	
	const PLUGIN_NAME = "Text Effect Shortcodes";
	const VERSION = '1.0.0';
	const TEXT_DOMAIN = 'text-effect-shortcodes';
	
	private static $instance;

	private function __construct() {}
		
	public static function get_instance() {
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function initialize() {
		load_plugin_textdomain(self::TEXT_DOMAIN, FALSE, basename(dirname( __FILE__ )) . '/languages');
		
		add_action('admin_menu', array($this, 'create_options_menu'));
		add_shortcode('blink', array($this, 'text_effect_shortcodes_tag_blink'));
		add_shortcode('marquee', array($this, 'text_effect_shortcodes_tag_marquee'));
		add_shortcode('code', array($this, 'text_effect_shortcodes_tag_code'));
		add_shortcode('rainbow', array($this, 'text_effect_shortcodes_tag_rainbow'));
		add_action('wp_enqueue_scripts', array($this, 'text_effect_shortcodes_styles'));
		add_action('admin_enqueue_scripts', array($this, 'text_effect_shortcodes_styles'));
	}
	
	public function setup_plugin_on_activation() {		
		set_transient('text_effect_shortcodes_activation_notice', TRUE, 5);
		add_action('admin_notices', array($this, 'plugin_activation_notice'));
	}
	
	public function plugin_activation_notice() {
		if (get_transient('text_effect_shortcodes_activation_notice')) {
			echo '<div class="notice updated"><p><strong>'.__('Text Effect Shortcodes plugin is activated. No further actions required &ndash; the plugin is now up and running!', self::TEXT_DOMAIN).'</strong></p></div>';	
		}		
	}
	
	public function text_effect_shortcodes_tag_blink($atts, $content = '') {
		return '<span class="text-effect-shortcodes-blink">' . $content . '</span>';
	}

	public function text_effect_shortcodes_tag_marquee($atts, $content = '') {
		$width_style = empty($atts['width']) ? '' : 'style="width: '.$atts['width'].'"';
		return '<div class="text-effect-shortcodes-marquee" '.$width_style.'><span class="inner-marquee">' . $content . '</span></div>';
	}

	public function text_effect_shortcodes_tag_code($atts, $content = '') {
		return '<span class="text-effect-shortcodes-code">' . $content . '</span>';
	}	
	
	public function text_effect_shortcodes_tag_rainbow($atts, $content = '') {
		return '<span class="text-effect-shortcodes-rainbow">' . $content . '</span>';
	}	
	
	public function create_options_menu() {
		add_submenu_page(
			'options-general.php',
			'Text Effect Shortcodes',
			'Text Effect Shortcodes',
			'manage_options',
			'text-effect-shortcodes',
			array($this, 'print_settings_page')
		);
	}	
	
	public function print_settings_page() {
		if (!current_user_can('manage_options')) {
			return;
		}		
		?>
		
		<h1><?php _e('Text Effect Shortcodes'); ?></h1>
							
		<div class="text-effect-shortcodes-settings">
		
			<h2><span style="color: red" class="dashicons dashicons-heart"></span> <?php _e('Recommendation'); ?></h2>		
				
			<div class="updated notice is-dismissible"><p><?php echo $this->get_recommendation(); ?></p></div>
			
			<h2><span style="color: blue" class="dashicons dashicons-info"></span> <?php _e('Instructions'); ?></h2>		
			
			<p><?php _e('Now you can place shortcodes in your content like this:'); ?></p>
			
			<div class="text-effect-shortcodes-instructions">
			<table>
			<tr>
				<th>Shortcode</th><th>Example</th><th>Result</th>
			</tr>
			<tr>
				<td>blink</td>
				<td>[blink]blinking text[/blink]</td>
				<td><span class="text-effect-shortcodes-blink">blinking text</span></td>
			</tr>
			<tr>
				<td>marquee</td>
				<td>
					[marquee]scrolling text[/marquee]<br/>
					[marquee width="128px"]scrolling text[/marquee]
				</td>
				<td class="wide"><span class="text-effect-shortcodes-marquee" width="128px"><span class="inner-marquee">scrolling text</span></span></td>
			</tr>
			<tr>
				<td>code</td>
				<td>[code]code formatting[/code]</td>
				<td><span class="text-effect-shortcodes-code">code formatting</span></td>
			</tr>	
			<tr>
				<td>rainbow</td>
				<td>[rainbow]rainbow colors[/rainbow]</td>
				<td><span class="text-effect-shortcodes-rainbow">rainbow colors</span></td>
			</tr>				
		</table>
		</div>
			
		</div>
		
		<?php
	}	
	
	
	public function get_recommendation() {
		switch (rand(0,4)) {
			default: return 'If you are using this plugin, you might also like <a target="_blank" href="https://wordpress.org/plugins/open-wp-seo/">Open WordPress SEO</a> &ndash; the absolutely free and open-source all-in-one toolbox to control SEO on your site.';		
		}
	}		
	
	public function text_effect_shortcodes_styles() {
		wp_register_style('text-effect-shortcodes', plugin_dir_url(__FILE__) . 'text-effect-shortcodes.css', array('dashicons'));
		wp_enqueue_style('text-effect-shortcodes');
	}
}
