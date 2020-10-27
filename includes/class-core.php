<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://franciscomauri.es/
 * @since      1.0.0
 *
 * @package    Wp_multi_task
 * @subpackage Wp_multi_task/admin
 */

namespace Mtk;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_multi_task
 * @subpackage Wp_multi_task/admin
 * @author     Francisco Mauri Cortina <labrest03@gmail.com>
 */
use Mtk\Request;
use Mtk\Admin\Custom_Post_Types;
use Mtk\Admin\User_Roles;
use Mtk\Admin\Capabilities;
use Mtk\Admin\Policies;

class Core {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $plugin_page_hook;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        require_once plugin_dir_path(__FILE__) . 'admin/class-custom-post-types.php';
        require_once plugin_dir_path(__FILE__) . 'admin/class-user-roles.php';
        require_once plugin_dir_path(__FILE__) . 'admin/class-capabilities.php';
        require_once plugin_dir_path(__FILE__) . 'admin/class-policies.php';
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(dirname(__FILE__)) . 'assets/css/styles.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', array(), $this->version, 'all');
        wp_enqueue_style('dashicons');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery-ui-dialog');




        wp_enqueue_script($this->plugin_name, plugin_dir_url(dirname(__FILE__)) . 'assets/js/scripts.js', array('jquery'), $this->version, true);
        wp_localize_script($this->plugin_name, 'script_data', array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('my-ajax-nonce'),
            'plugin_page_hook' => $this->plugin_page_hook,
            'submenu' => get_admin_submenus()
        ));
    }

    function admin_menu() {
        if ($hook = add_management_page('WP Multi Task', 'WP Multi Task', 'install_plugins', 'wp-multi-task', array($this, 'admin_page'), '')) {
            $this->plugin_page_hook = str_replace('tools_page_', '', $hook);
        }
    }

    function admin_page() { 
        $active_cpt_nav = $active_cap_nav = $active_role_nav = $active_cpt_tab = $active_role_tab = $active_cap_tab = '';
        if ('roles' == Request::get_get('tab')) {
            $active_role_tab = 'active';
            $active_role_nav = 'nav-tab-active';
        } elseif ('cap' == Request::get_get('tab')) {
            $active_cap_tab = 'active';
            $active_cap_nav = 'nav-tab-active';
        } elseif ('policies' == Request::get_get('tab')) {
            $active_policy_tab = 'active';
            $active_policy_nav = 'nav-tab-active';
            $content_elements = Policies::get_elements_dropdown('content');
            $entity_elements = Policies::get_elements_dropdown('entity');
            $policies = Policies::get_all();
        } else {
            $active_cpt_tab = 'active';
            $active_cpt_nav = 'nav-tab-active';
        }
        $users = json_encode(User_Roles::get_users());
        $roles = User_Roles::get_roles();
        $caps = Capabilities::get_all_by_type();
        require_once(plugin_dir_path(dirname(__FILE__)) . 'partials/settings.php');
    }

    function handle_settings_submission() {       
        try {
            //CPTS
            if (Custom_Post_Types::is_store()) {
                Custom_Post_Types::store();
                wp_redirect(remove_query_arg(['tab']));
            } elseif (Custom_Post_Types::is_update()) {
                Custom_Post_Types::update();
                wp_redirect(remove_query_arg(['tab']));
            } elseif (Custom_Post_Types::is_delete()) {
                Custom_Post_Types::delete();
                wp_redirect(remove_query_arg(['delete-cpt', '_wpnonce', 'tab']));

                //ROLES
            } elseif (User_Roles::is_store()) {
                User_Roles::store();
                wp_redirect(add_query_arg(['tab' => 'roles']));
            } elseif (User_Roles::is_update()) {
                User_Roles::update();
                wp_redirect(add_query_arg(['tab' => 'roles']));
            } elseif (User_Roles::is_delete()) {
                User_Roles::delete();
                wp_redirect(add_query_arg(['tab' => 'roles'], remove_query_arg(['delete-role', '_wpnonce'])));
            } elseif (User_Roles::is_duplicate()) {
                User_Roles::duplicate();
                wp_redirect(add_query_arg(['tab' => 'roles'], remove_query_arg(['duplicate-role', 'duplicate-role-name', '_wpnonce'])));


                //POLICIES
            } elseif (Policies::is_store()) {
                Policies::store();
                wp_redirect(get_current_location());
            } elseif (Policies::is_update()) {
                Policies::update(Request::get_post('update-mt-policy'));
                wp_redirect(get_current_location());
            } elseif (Policies::is_delete()) {
                Policies::delete(Request::get_get('delete-policy'));
                wp_redirect(remove_query_arg(['delete-policy', '_wpnonce']));
            }
        } catch (Exception $e) {
            echo 'Error: ', $e->getMessage(), "\n";
        }
    }

    function create_custom_post_types() {
        Custom_Post_Types::create_custom_post_types();
    }

    function get_ajax_capabilities() {
        if (Capabilities::is_get()) {
            
        }
    }

    function get_caps_by_role_ajax() {
        check_nonce();
        $caps = Capabilities::get_all_by_role(Request::get_post('role'));
        echo json_encode($caps);
        wp_die();
    }

    function get_caps_by_user_ajax() {
        check_nonce();
        $caps = Capabilities::get_all_by_user(Request::get_post('user'));
        echo json_encode($caps);
        wp_die();
    }

    function set_caps_ajax() {
        check_nonce();
        if ('role' == Request::get_post('entity'))
            $caps = Capabilities::set_for_role(Request::get_post('id'), Request::get_post('cap'), Request::get_post('active'));
        else if ('user' == Request::get_post('entity'))
            $caps = Capabilities::set_for_user(Request::get_post('id'), Request::get_post('cap'), Request::get_post('active'));
        echo 'ok';
        wp_die();
    }

    function add_caps_ajax() {
        check_nonce();
        if (Request::has_post('cap-name'))
            $cap = Capabilities::store(Request::get_post('cap-name'));
        echo $cap ?: 'ko';
        wp_die();
    }

    function delete_caps_ajax() {
        check_nonce();
        if (Request::has_post('cap-name'))
            $caps = Capabilities::delete(Request::get_post('cap-name'));
        echo $caps ? 'ok' : 'ko';
        wp_die();
    }

    function mt_get_sub_elements_ajax() {
        check_nonce();
        if (Request::has_post('type') && Request::has_post('element')) {
            $sub_elements = call_user_func('Mtk\Admin\Policies::get_sub_elements_' . Request::get_post('type'), Request::get_post('element'));
        }
        echo json_encode($sub_elements);
        wp_die();
    }

    function restrict_content() {
        Policies::restrict_content();
    }

    function admin_head() {
        Policies::restrict_content('admin_menu');
    }

    function template_redirect() {
        Policies::restrict_content('post_type');
    }
    
    function front_end_menu() {
        Policies::restrict_content('front_end_menu');
    }
    
    function front_end_sidebar() {
        Policies::restrict_content('sidebar');
    }

    function save_admin_menu() {
        global $menu, $plugin_admin_menu;
        $plugin_admin_menu = $menu;
    }
    
    function mtk_get_all_capabilities_callback($caps){
        return Capabilities::filter_capabilities($caps); 
    }

}
