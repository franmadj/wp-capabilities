<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mtk\Admin;

use Mtk\Request;

class Policies {

    private static $content_to_restrict = ['Post Type', 'Admin Menu', 'Front End Menu', 'Sidebar'];
    private static $entities_restricted = ['Roles', 'Custom Capabilities'];

    public static function restrict_content($element_checked) {
        //return;
        if ($policies = Policies::get_all()) {
            foreach ($policies as $policy) {
                if ($policy['data']) {
                    foreach ($policy['data'] as $rule) {
                        $content_element = explode('@', $rule['element']);
                        $element_type = $content_element[0];
                        //only checking elements for the current hook
                        if ($element_type != $element_checked)
                            continue;
                        $element = $content_element[1];
                        $sub_element = $rule['sub_element'];

                        $content_entity = explode('@', $rule['entities']);
                        $entity_type = $content_entity[0];
                        $entity = $content_entity[1];
                        $sub_entity = $rule['sub_entities'];
                        if (self::check_entity($entity_type, $entity, $sub_entity))
                            switch ($element_type) {
                                case 'post_type':
                                    if ($sub_element == get_the_ID() || ($element == get_post_type() && $sub_element == 'all')) {
                                        \Mtk\show_404();
                                    }
                                    break;
                                case 'admin_menu':
                                    global $menu, $submenu;
                                    foreach ($submenu as $_menu => $_submenu) {
                                        if ($sub_element == 'all') {
                                            if (strpos($_menu, $element) !== false) {
                                                remove_menu_page($element);
                                            }
                                        } else {
                                            foreach ($_submenu as $item) {
                                                $link_submenu = $item[2];
                                                if (strpos($link_submenu, $sub_element) !== false) {
                                                    remove_submenu_page($_menu, $link_submenu);
                                                }
                                            }
                                        }
                                    }
                                    $request_uri = str_replace('/wp-admin/', '', $_SERVER['REQUEST_URI']);
                                    if (strpos($request_uri, $sub_element) !== false || (strpos($request_uri, $element) !== false && $sub_element == 'all')) {
                                        exit;
                                    }

                                    break;
                                case 'front_end_menu':
                                    if (is_admin())
                                        return;
                                    $menu_id = $element;
                                    $submenu_id = $sub_element;
                                    if ($submenu_id == 'all') {
//                                        add_filter('has_nav_menu', function($has_nav_menu, $location)use($menu_id) {
//                                            $locations = get_nav_menu_locations();
//                                            if ($menu_id == $locations[$location]) {
//
//                                                return false;
//                                            }
//                                            return $has_nav_menu;
//                                        }, 1000, 2);

                                        add_filter('wp_get_nav_menu_object', function($menu_obj, $menu)use($menu_id) {
                                            return $menu_id == $menu ? false : $menu_obj;
                                        }, 10, 2);
                                    } else {
                                        //apply_filters('wp_get_nav_menu_items', $items, $menu, $args);
                                        add_filter('wp_get_nav_menu_items', function($items, $menu, $args)use($menu_id, $submenu_id) {
                                            if ($menu_id == $menu->term_id)
                                                return array_filter($items, function($item)use($submenu_id) {
                                                    return $submenu_id != $item->ID;
                                                });
                                        }, 10, 3);
                                    }
                                    break;
                                case 'sidebar':
                                    if (is_admin())
                                        return;
                                    $sidebar_id = $element;
                                    $widget_id = $sub_element;

                                    if ($widget_id == 'all') {
                                        //apply_filters( 'is_active_sidebar', $is_active_sidebar, $index );
                                        add_filter('is_active_sidebar', function($is_active_sidebar, $index)use($sidebar_id) {
                                            return $index == $sidebar_id ? false : $is_active_sidebar;
                                        }, 10, 2);
                                    } else {
                                        //apply_filters( 'widget_display_callback', $instance, $this, $args );
                                        add_filter('widget_display_callback', function($instance, $_this, $args)use($sidebar_id, $widget_id) {
                                            return $args['id'] == $sidebar_id && $args['widget_id'] == $widget_id ? false : $instance;
                                        }, 10, 3);
                                    }

                                    break;
                            }
                    }
                }
            }
        }
    }

    private static function check_entity($entity_type, $entity, $sub_entity) {
        $user = wp_get_current_user();
        switch ($entity_type) {
            case 'roles':
                if ($sub_entity == $user->ID)
                    return true;
                $roles = (array) $user->roles;
                return in_array($entity, $roles) && $sub_entity == 'all';
            case 'plugin_capabilities':
                return $user->has_cap($entity);
        }
    }

    public static function get_all() {
        return get_option(RULES_OPTION_META, []) ?: [];
    }

    public static function get_elements_dropdown($type) {
        if ('content' == $type) {
            $elements_to_restrict = self::$content_to_restrict;
            $html = '<select class="mt-elements-content" name="mt-elements-content[]"><option value="">Select Element</option>';
        } else {
            $elements_to_restrict = self::$entities_restricted;
            $html = '<select class="mt-elements-entities" name="mt-elements-entities[]"><option value="">Select Entity</option>';
        }
        foreach ($elements_to_restrict as $element) {
            $slug_element = \Mtk\create_slug($element, '_');
            if ($elements = call_user_func('self::get_' . $slug_element)) {
                $html .= '<optgroup label="' . $element . '">';
                foreach ($elements as $el) {
                    $html .= '<option value="' . $slug_element . '@' . $el['name'] . '">' . $el['label'] . '</option>';
                }
                $html .= '</optgroup>';
            }
        }
        $html .= '</select>';
        return $html;
    }

    private static function get_roles() {
        $roles = User_Roles::get_roles();
        $output_roles = [];
        foreach ($roles as $key => $role) {
            $output_roles[] = ['name' => $key, 'label' => $role['name']];
        }
        return $output_roles;
    }

    private static function get_custom_capabilities() {
        $caps = Capabilities::get_custom_capabilities();
        $output_caps = [];
        foreach ($caps as $cap) {
            $output_caps[] = ['name' => $cap, 'label' => $cap];
        }
        return $output_caps;
    }

    private static function get_post_type() {
        $post_types = get_post_types(array(), 'objects');
        $valid_post_types = [];
        foreach ($post_types as $pt) {
            if ($pt->public && $pt->name != 'attachment') {
                $valid_post_types[] = ['name' => $pt->name, 'label' => $pt->label];
            }
        }
        return $valid_post_types;
    }

    private static function get_admin_menu() {
        global $plugin_admin_menu;
        $items = [];
        foreach ($plugin_admin_menu as $item) {
            if ($item[0])
                $items[] = ['name' => $item[2], 'label' => $item[0]];
        }
        return $items;
    }

    private static function get_front_end_menu() {
        $items = [];
        $menus = wp_get_nav_menus();
        foreach ($menus as $menu) {
            $items[] = ['name' => $menu->term_id, 'label' => $menu->name];
        }
        return $items;
    }

    private static function get_sidebar() {
        global $wp_registered_sidebars;
        $items = [];
        foreach ($wp_registered_sidebars as $key => $sidebar) {
            $items[] = ['name' => $key, 'label' => $sidebar['name']];
        }
        return $items;
    }

    public static function get_sub_elements_post_type($post_type) {
        $posts = get_posts([
            'post_type' => $post_type,
            'numberposts' => -1,
            'post_status' => 'publish'
        ]);
        $filtered_posts = [];
        foreach ($posts as $post) {
            $filtered_posts[] = ['key' => $post->ID, 'label' => $post->post_title];
        }
        return $filtered_posts;
    }

    public static function get_sub_elements_admin_menu($menu) {
        $submenu = json_decode(str_replace('\\', '', Request::get_post('submenu')), true);
        return $submenu[$menu];
    }

    public static function get_sub_elements_front_end_menu($menu) {
        $submenu = wp_get_nav_menu_items($menu);
        $filtered_submenu = [];
        foreach ($submenu as $post) {
            $filtered_submenu[] = ['key' => $post->ID, 'label' => $post->title];
        }
        return $filtered_submenu;
    }

    public static function get_sub_elements_sidebar($sidebar) {
        $widgets = wp_get_sidebars_widgets();
        //var_dump($widgets);exit;
        $filtered_widgets = [];
        foreach ($widgets[$sidebar] as $widget) {
            $filtered_widgets[] = ['key' => $widget, 'label' => \Mtk\create_name(preg_replace('/[0-9]/', '', $widget))];
        }
        return $filtered_widgets;
    }

    public static function get_sub_elements_roles($role) {
        $users = User_Roles::get_users_by_role($role);
        $filtered_users = [];
        foreach ($users as $user) {
            $filtered_users[] = ['key' => $user->ID, 'label' => $user->display_name];
        }
        return $filtered_users;
    }

    public static function get_rules_by_policy($policy) {
        $plugin_rules = self::get_all();
        if ($plugin_rules[$policy]) {
            return $plugin_rules[$policy]['data'];
        }
        return [];
    }

    public static function store() {
        $id = uniqid();
        $plugin_rules = self::get_all();
        $plugin_rules[$id] = self::get_data();

        if (update_option(RULES_OPTION_META, $plugin_rules))
            return $rules;
        return false;
    }

    public static function update($policy) {
        $plugin_rules = self::get_all();
        if (!empty($plugin_rules[$policy])) {
            $plugin_rules[$policy] = self::get_data();
            if (update_option(RULES_OPTION_META, $plugin_rules))
                return $rules;
            return false;
        }
        return false;
    }

    public static function delete($policy) {
        $plugin_rules = self::get_all();
        unset($plugin_rules[$policy]);
        return update_option(RULES_OPTION_META, $plugin_rules);
    }

    public static function is_store() {
        return Request::has_all_posts('save-mt-policy, policy-title, mt-elements-content, mt-sub-elements, mt-elements-entities, mt-sub-entities') &&
                check_admin_referer('save_mt_policy', 'nonce_save_mt_policy');
    }

    public static function is_update() {
        return Request::has_all_posts('update-mt-policy, policy-title, mt-elements-content, mt-sub-elements, mt-elements-entities, mt-sub-entities') &&
                check_admin_referer('save_mt_policy', 'nonce_save_mt_policy');
    }

    public static function is_delete() {
        return Request::has_get('delete-policy') && check_admin_referer('delete_policy_wpnonce', '_wpnonce');
    }

    private static function get_data() {
        $title = Request::get_post('policy-title');
        $elements = Request::get_post('mt-elements-content');
        $sub_elements = Request::get_post('mt-sub-elements');
        $entities = Request::get_post('mt-elements-entities');
        $sub_entities = Request::get_post('mt-sub-entities');

        $rules = [];
        $rules['title'] = $title;
        $rules['data'] = [];
        foreach ((array) $elements as $key => $element) {
            if (!$element || !$sub_elements[$key] || !$entities[$key] || !$sub_entities[$key])
                continue;
            $rules['data'][] = ['element' => $element, 'sub_element' => $sub_elements[$key], 'entities' => $entities[$key], 'sub_entities' => $sub_entities[$key]];
        }

        return $rules;

        return array('title' => $title, 'elements' => $elements, 'sub_elements' => $sub_elements, 'entities' => $entities, 'sub_entities' => $sub_entities);
    }

}
