<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mtk\Admin;

use Mtk\Request;

class Custom_Post_Types {

//put your code here

    public static function get_all() {
        return get_option(CPT_OPTION_META, []) ?: [];
    }

    public static function store() {
        $data = self::set_data();
        $data['id'] = uniqid();
        $cpts = self::get_all();
        $cpts[$data['id']] = $data;
        update_option(CPT_OPTION_META, $cpts);
    }

    public static function update() {
        $id = Request::get_post('update-mt-cpt');
        $data = self::set_data();
        $data['id'] = $id;
        $cpts = self::get_all();
        if (!empty($cpts[$id])) {
            $cpts[$id] = $data;
            update_option(CPT_OPTION_META, $cpts);
        }
    }

    public static function delete() {
        $id = Request::get_get('delete-cpt');
        $cpts = self::get_all();
        unset($cpts[$id]);
        //$cpts=[];
        update_option(CPT_OPTION_META, $cpts);
    }

    public static function is_store() {
        return Request::has_post('save-mt-cpt') && Request::has_post('cpt-name') && check_admin_referer('save_mt_cpt', 'nonce_save_mt_cpt');
    }

    public static function is_update() {
        return Request::has_post('update-mt-cpt') && Request::has_post('cpt-name') && check_admin_referer('save_mt_cpt', 'nonce_save_mt_cpt');
    }

    public static function is_delete() {
        return Request::has_get('delete-cpt') && check_admin_referer('delete_cpt_wpnonce', '_wpnonce');
    }

    private static function set_data() {
        $data = [];
        $name = Request::get_post('cpt-name');
        if (!$name)
            throw new \Exception('Invalid Data.');
        $data['name'] = $name;
        $data['active'] = intVal(Request::get_post('cpt-active', 0));
        $data['slug'] = \Mtk\create_slug(Request::get_post('cpt-slug', ($name)));
        $data['description'] = Request::get_post('cpt-description');
        $data['custom_cap'] = Request::has_post('cpt-custom-cap', 0) ? 1 : 0;
        $custom_cap = $data['custom_cap'] != 0 ? Request::get_post('cpt-cap', 'post') : 'post';
        $custom_cap = array_map('\Mtk\create_slug', explode('|', $custom_cap));
        $custom_cap = count($custom_cap) != 2 ? [$custom_cap[0], $custom_cap[0] . 's'] : $custom_cap;
        $data['cap'] = $custom_cap;
        $data['has_archive'] = Request::has_post('cpt-has-archive', 0) ? 1 : 0;
        $data['position'] = intVal(Request::get_post('cpt-position', 1));
        $data['supports'] = Request::get_post('cpt-supports', []);
        return $data;
    }

    public static function create_custom_post_types() {
        $cpts = self::get_all();
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

        }
    }

}
