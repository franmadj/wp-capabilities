<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mtk\Admin;

use Mtk\Request;

class User_Roles {

    private static $duplicated_name = 1;

//put your code here

    public static function get_users_count_by_role($role) {
        global $wpdb;
        //var_dump("SELECT count(ID) c FROM {$wpdb->prefix}users join wp_usermeta um on (u.ID=um.user_id) where um.meta_key='wp_capabilities' and meta_value like '%$role%'");
        $count = $wpdb->get_col("SELECT count(ID) c FROM {$wpdb->prefix}users u join wp_usermeta um on (u.ID=um.user_id) where um.meta_key='wp_capabilities' and meta_value like '%$role%'");
        return $count[0];
    }

    public static function get_users_by_role($role) {
        global $wpdb;
        //var_dump("SELECT count(ID) c FROM {$wpdb->prefix}users join wp_usermeta um on (u.ID=um.user_id) where um.meta_key='wp_capabilities' and meta_value like '%$role%'");
        $users = $wpdb->get_results("SELECT ID, display_name FROM {$wpdb->prefix}users u join wp_usermeta um on (u.ID=um.user_id) where um.meta_key='wp_capabilities' and meta_value like '%$role%'");
        return $users;
    }

    public static function get_users() {
        $roles = self::get_roles();
        $result = [];
        foreach ($roles as $slug => $role) {
            $users = get_users(['role__in' => [$slug]]);
            foreach ($users as $user) {
                if (!isset($result[$slug]))
                    $result[$slug] = [];
                $result[$slug][] = ['id' => $user->id, 'name' => $user->display_name];
            }
        }
        return $result;
    }

    public static function get_roles() {
        global $wp_roles;
        $roles = $wp_roles->roles;
        return $roles;
    }

    public static function store($caps = []) {
        $data = self::set_data();
        add_role(\Mtk\create_slug($data['name']), $data['name'], $caps);
    }

    public static function update() {
        $id = Request::get_post('update-mt-role');
        $name = Request::get_post('role-name');
        $roles=get_option('wp_user_roles');
        
        if(!empty($roles[$id])){
            $roles[$id]['name']=$name;
            
        }
        update_option('wp_user_roles', $roles);
    }

    public static function delete() {
        $role = Request::get_get('delete-role');
        remove_role($role);
    }

    public static function duplicate() {
        $role = Request::get_get('duplicate-role');
        $role_name = Request::get_get('duplicate-role-name');
        if ($role = get_role($role)) {
            $duplicated_role = self::get_duplicated_role_name($role_name);            //var_dump($duplicated_role);exit;
            return add_role(\Mtk\create_slug($duplicated_role), $duplicated_role, $role->capabilities);
        }
    }

    private static function get_duplicated_role_name($role_name) {        //var_dump($role_name);exit;
        $new_role_display_name=$role_name . ' copy' . self::$duplicated_name;
        $new_role_name = \Mtk\create_slug($new_role_display_name);
//        var_dump($new_role_name, get_role($new_role_name));
//        exit;
        if (!get_role($new_role_name)) {
            return $new_role_display_name;
        }
        self::$duplicated_name++;
        return self::get_duplicated_role_name($role_name);
    }

    public static function is_store() {
        return Request::has_post('save-mt-role') && Request::has_post('role-name') && check_admin_referer('save_mt_role', 'nonce_save_mt_role');
    }

    public static function is_update() {
        return Request::has_post('update-mt-role') && Request::has_post('role-name') && check_admin_referer('save_mt_role', 'nonce_save_mt_role');
    }

    public static function is_delete() {
        return Request::has_get('delete-role') && check_admin_referer('delete_role_wpnonce', '_wpnonce');
    }

    public static function is_duplicate() {
        return Request::has_get('duplicate-role') && Request::has_get('duplicate-role-name') && check_admin_referer('duplicate_role_wpnonce', '_wpnonce');
    }

    private static function set_data() {
        $data = [];
        $name = Request::get_post('role-name');
        if (!$name)
            throw new \Exception('Invalid Data.');
        $data['name'] = $name;

        $data['slug'] = \Mtk\create_slug(Request::get_post('role-slug', ($name)));

        return $data;
    }

    public static function format_role_capabilities($caps = NULL) {
        if (!$caps)
            return '';

//        var_dump($role);
//        return;
        $list = [];
        foreach ($caps as $cap => $bool) {
            if ($bool)
                $list[] = $cap;
        }

        return implode('  |  ', $list);

        foreach ($cpts as $cpt) {
            if ($cpt['active'])
                $name = $cpt['name'];
            $labels = array(
                'name' => _x($name, 'post type general name', 'smartparke'),
                'singular_name' => _x($name, 'post type singular name', 'smartparke'),
                'menu_name' => _x($name, 'admin menu', 'smartparke'),
                'name_admin_bar' => _x($name, 'add new on admin bar', 'smartparke'),
                'add_new' => _x('Add New', $name, 'smartparke'),
                'add_new_item' => __('Add ' . $name, 'smartparke'),
                'new_item' => __('New ' . $name, 'smartparke'),
                'edit_item' => __('Edit ' . $name, 'smartparke'),
                'view_item' => __('View ' . $name, 'smartparke'),
                'all_items' => __('All ' . $name, 'smartparke'),
                'search_items' => __('Search ' . $name, 'smartparke'),
                'parent_item_colon' => __('Parent ' . $name . ':', 'smartparke'),
                'not_found' => __('No ' . $name . ' found.', 'smartparke'),
                'not_found_in_trash' => __('No ' . $name . ' found in Trash.', 'smartparke')
            );

            $args = array(
                'labels' => $labels,
                'description' => __($cpt['description'], 'smartparke'),
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array('slug' => $cpt['slug']),
                'capability_type' => $cpt['cap'],
                'map_meta_cap' => true,
                'has_archive' => $cpt['has_archive'] ? true : false,
                'hierarchical' => false,
                'menu_position' => intVal($cpt['position']),
                'menu_icon' => 'dashicons-admin-users',
            );

            if ($cpt["supports"] && is_array($cpt["supports"])) {
                $args['supports'] = $cpt["supports"];
            }
            $post_type = register_post_type($cpt['slug'], $args);

//            var_dump($post_type);
//            exit;
        }
    }

}
