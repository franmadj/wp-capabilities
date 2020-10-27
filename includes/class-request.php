<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mtk;

/**
 * Description of class-request
 *
 * @author USER
 */
class Request {

//put your code here
    public static function has($key) {
        return self::has_value($key, $_REQUEST);
    }

    public static function has_get($key) {
        return self::has_value($key, $_GET);
    }

    public static function has_post($key) {
        return self::has_value($key, $_POST);
    }

    public static function get($key, $default = '') {
        return self::get_value($key, $_REQUEST, $default);
    }

    public static function get_get($key, $default = '') {
        return self::get_value($key, $_GET, $default);
    }

    public static function get_post($key, $default = '') {
        return self::get_value($key, $_POST, $default);
    }

    private static function has_value($key, $global) {
        if (!empty($global[$key]) && !is_array($global[$key]))
            $global[$key] = trim($global[$key]);
        return !empty($global[$key]);
    }

    private static function get_value($key, $global, $default = '') {
        if (self::has_value($key, $global)) {
            return $global[$key];
        }
        return $default;
    }

    public static function has_all_posts($posts) {
        foreach (array_map('trim', explode(',', $posts)) as $post) {
            if (!self::has_post($post))
                return false;
        }
        return true;
    }

}
