<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mtk\Admin;

use Mtk\Request;

class Capabilities {

//put your code here

    public static function set_for_role($role, $cap, $active) {
        $role = get_role($role);
        if ($active)
            $role->add_cap($cap);
        else
            $role->remove_cap($cap);
        return true;
    }

    public static function set_for_user($user, $cap, $active) {
        $user = get_user_by('ID', $user);        
        if ($user)
            if ($active)
                $user->add_cap($cap);
            else
                $user->remove_cap($cap);
        return true;
    }

    public static function get_all_by_role($role) {
        $all_caps = User_Roles::get_roles();
        return !empty($all_caps[$role]['capabilities']) ? $all_caps[$role]['capabilities'] : [];
    }

    public static function get_all_by_user($user) {
        if ($user = get_userdata($user))
            return $user->allcaps;
        return [];
    }

    public static function get_custom_capabilities() {
        return get_option(CAPS_OPTION_META, []);
    }

    public static function get_all() {
        $caps = [];
        $admin_caps = self::get_all_by_role('administrator');
        foreach ($admin_caps as $cap => $bool) {
            $caps[$cap] = self::add_cap_data_array($cap, 'core');
        }

        $plugin_caps = self::get_custom_capabilities();
        foreach ($plugin_caps as $cap) {
            $caps[$cap] = self::add_cap_data_array($cap, 'plugin'); 
        }

        $post_types = get_post_types(array(), 'objects');
        foreach ($post_types as $pt) {
            foreach ($pt->cap as $cap) {
                $caps[$cap] = self::add_cap_data_array($cap, 'post'); 
            }
        }



        return apply_filters('mtk_get_all_capabilities', $caps);
    }

    private static function add_cap_data_array($cap, $type) {
        $subtype = '';
        if (strpos($cap, 'delete_') !== false) {
            $subtype = 'delete-type';
        } elseif (strpos($cap, 'edit') !== false) {
            $subtype = 'edit-type';
        } elseif (strpos($cap, 'read_') !== false || $cap=='read') {
            $subtype = 'read-type';
        } elseif (strpos($cap, 'publish_') !== false) {
            $subtype = 'publish-type';
        }
        return ['cap' => $cap, 'type' => $type, 'subtype' => $subtype];
    }

    public static function filter_capabilities($caps) {
        $filter_caps = [];
        foreach ($caps as $key => $cap) {
            if (
                    !(preg_match('/^level_[0-9]{1,2}/', $key) ||
                    $key == 'edit_files' ||
                    preg_match('/^[a-z]+(_post|_page){1}$/', $key))
            ) {
                $filter_caps[$key] = $cap;
            }
        }
        return $filter_caps;
    }

    public static function get_all_by_type() {
        $caps = self::get_all();
        $caps_by_type = [];
        foreach ($caps as $cap) {
            if (!isset($caps_by_type[$cap['type']]))
                $caps_by_type[$cap['type']] = [];
            $caps_by_type[$cap['type']][$cap['cap']] = $cap;
        }
        return $caps_by_type;
    }

    public static function store($cap_name) {
        $cap_name = \Mtk\create_slug($cap_name, '_');
        if (!in_array($cap_name, self::get_all())) {
            $plugin_caps = get_option(CAPS_OPTION_META, []);
            $plugin_caps[$cap_name] = $cap_name;
            if (update_option(CAPS_OPTION_META, $plugin_caps))
                return $cap_name;
        }
        return false;
    }

    public static function delete($cap) {
        $plugin_caps = get_option(CAPS_OPTION_META, []);
        unset($plugin_caps[$cap]);
        return update_option(CAPS_OPTION_META, $plugin_caps);
    }

}
