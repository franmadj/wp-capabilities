<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://franciscomauri.es/
 * @since      1.0.0
 *
 * @package    Wp_multi_task
 * @subpackage Wp_multi_task/includes
 */
namespace Mtk;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_multi_task
 * @subpackage Wp_multi_task/includes
 * @author     Francisco Mauri Cortina <labrest03@gmail.com>
 */
class i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp_multi_task',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
