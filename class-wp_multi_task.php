<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://franciscomauri.es/
 * @since      1.0.0
 *
 * @package    Wp_multi_task
 * @subpackage Wp_multi_task/includes
 */

use Mtk\Loader;
use Mtk\i18n;
use Mtk\Core;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_multi_task
 * @subpackage Wp_multi_task/includes
 * @author     Francisco Mauri Cortina <labrest03@gmail.com>
 */
class Wp_multi_task {

    protected static $_instance;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wp_multi_task_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    public static function get_instance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new Wp_multi_task();
        }
        return self::$_instance;
    }

    protected function __construct() {
        if (defined('WP_MULTI_TASK_VERSION')) {
            $this->version = WP_MULTI_TASK_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'wp_multi_task';

        $this->constants();
        $this->load_dependencies();
        $this->set_locale();
        $this->plugin_hooks();
    }

    private function constants() {
        define('CPT_OPTION_META', 'cpts_multi_task');
        define('CAPS_OPTION_META', 'caps_multi_task');
        define('RULES_OPTION_META', 'rules_multi_task');
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Wp_multi_task_Loader. Orchestrates the hooks of the plugin.
     * - Wp_multi_task_i18n. Defines internationalization functionality.
     * - Wp_multi_task_Core. Defines all hooks for the admin area.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {       

        /**
         * The class responsible for orchestrating requests of the
         * core plugin.
         */
        require_once plugin_dir_path(__FILE__) . 'includes/class-request.php';
        /**
         * The custom fucntions for the
         * core plugin.
         */
        require_once plugin_dir_path(__FILE__) . 'includes/functions.php';

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(__FILE__) . 'includes/class-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(__FILE__) . 'includes/class-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(__FILE__) . 'includes/class-core.php';

        $this->loader = new Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Wp_multi_task_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function plugin_hooks() {

        $class = new Core($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $class, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $class, 'enqueue_scripts');

        $this->loader->add_action('admin_menu', $class, 'admin_menu');
        $this->loader->add_action('init', $class, 'handle_settings_submission', 10);
        $this->loader->add_action('init', $class, 'create_custom_post_types', 11);

        $this->loader->add_action('wp_ajax_get_caps_by_role', $class, 'get_caps_by_role_ajax');
        $this->loader->add_action('wp_ajax_get_caps_by_user', $class, 'get_caps_by_user_ajax');
        $this->loader->add_action('wp_ajax_set_caps', $class, 'set_caps_ajax');
        $this->loader->add_action('wp_ajax_add_caps', $class, 'add_caps_ajax');
        $this->loader->add_action('wp_ajax_delete_caps', $class, 'delete_caps_ajax');
        $this->loader->add_action('wp_ajax_mt_get_sub_elements', $class, 'mt_get_sub_elements_ajax');


        $this->loader->add_action('template_redirect', $class, 'template_redirect');
        $this->loader->add_action('admin_head', $class, 'admin_head');
        $this->loader->add_action('init', $class, 'front_end_menu');
        $this->loader->add_action('init', $class, 'front_end_sidebar');


        $this->loader->add_action('_network_admin_menu', $class, 'save_admin_menu');
        $this->loader->add_action('_user_admin_menu', $class, 'save_admin_menu');
        $this->loader->add_action('_admin_menu', $class, 'save_admin_menu');
        
        $this->loader->add_filter('mtk_get_all_capabilities', $class, 'mtk_get_all_capabilities_callback',1,1);

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Wp_multi_task_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
