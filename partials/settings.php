<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://franciscomauri.es/
 * @since      1.0.0
 *
 * @package    Wp_multi_task
 * @subpackage Wp_multi_task/admin/partials
 */
global $wp;
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div id="wpbody-content" class="mt-admin-settings">
    <div class="wrap">
        <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
            <a href="#" class="cpt-tab nav-tab <?php echo $active_cpt_nav; ?>" data-tab="cpt-tab">CPT</a>
            <a href="#" class="roles-tab nav-tab <?php echo $active_role_nav; ?>" data-tab="roles-tab">Roles</a>
            <a href="#" class="caps-tab nav-tab <?php echo $active_cap_nav; ?>" data-tab="capabilities-tab">Capabilities</a>
            <a href="#" class="policies-tab nav-tab <?php echo $active_policy_nav; ?>" data-tab="policies-tab">Access Policies</a>
        </nav>
        
        <div id="cpt-tab" class="<?php echo $active_cpt_tab; ?> in-tabs">
            <?php 
            
            require_once (plugin_dir_path(__FILE__) . 'custom-post-types.php'); 
            
            ?>
        </div>
        <div id="roles-tab" class="in-tabs <?php echo $active_role_tab; ?>">
            <?php require_once (plugin_dir_path(__FILE__) . 'roles.php'); ?>
        </div>
        <div id="capabilities-tab" class="in-tabs <?php echo $active_cap_tab; ?>">
            <?php 
            require_once (plugin_dir_path(__FILE__) . 'capabilities.php'); 
            ?>
        </div>
        
        
        
        <div id="policies-tab" class="in-tabs <?php echo $active_policy_tab; ?>">
            <?php 
            require_once (plugin_dir_path(__FILE__) . 'policies.php'); 
            ?>
        </div>
    </div>
    <div class="clear"></div>
</div>
