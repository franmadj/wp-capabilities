<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mtk;

function create_slug($str, $delimiter = '-') {
    $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
    return $slug;
}

function create_name($str) {
    $slug = trim(ucwords(preg_replace('/[\-_]/', ' ', $str)));
    return $slug;
}

function get_current_location() {
    if (isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }
    return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function get_admin_submenus() {
    global $submenu;
    $filtered_submenu = [];
    foreach ($submenu as $key => $items) {
        if (!$key)
            continue;

        $filtered_submenu[$key] = [];
        foreach ($items as $item) {
            if ('tools.php' != $item[2])
                $filtered_submenu[$key][] = ['key' => $item[2], 'label' => strip_tags($item[0])];
        }
    }
    return $filtered_submenu;
}

function check_nonce() {
    $nonce = sanitize_text_field($_POST['nonce']);
    if (!wp_verify_nonce($nonce, 'my-ajax-nonce')) {
        die('Busted!');
    }
}

function show_404() {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);
    exit();
}
