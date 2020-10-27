<?php

use Mtk\Admin\Capabilities;
use Mtk\Admin\User_Roles;
?>
<h2>Capabilities</h1>

<div id="store_address-description"><p>Create, Update and Delete Capabilities</p>
</div>

<form method="post" id="cap-form" action="#" enctype="multipart/form-data">



    <table class="form-table">





        <tr valign="top">
            <td scope="row">
                <select id="cap-roles">
                    <option>Select Role</option>
                    <?php
                    if ($roles) {
                        foreach ($roles as $slug => $role) {
                            echo '<option value="' . $slug . '">' . $role['name'] . '</option>';
                        }
                    }
                    ?>

                </select>
            </td>
            <td>
                <select id="cap-users" data-users='<?php echo $users; ?>'>
                    <option>Select User</option>
                </select>						
            </td>
            <td>
                <button name="new-mt-cap" class="button-primary new-mt-cap" type="button" value="save" id="new-cap">New Capability</button>
            </td>

        </tr>




    </table>

    <p class="submit">



        <?php wp_nonce_field('save_mt_role', 'nonce_save_mt_role'); ?>

    </p>


</form>

<h2>Capabilities list</h2>
<div class="filter-nav">
    <?php
    $core = count($caps['core']);
    $post = count($caps['post']);
    $plugin = count((array) $caps['plugin']);
    $all = $core + $post + $plugin;
    ?>
    <a class="active" href="all">All(<?php echo $all; ?>)</a> 
    <a href="core">Core(<?php echo $core; ?>)</a> 
    <a href="post">Posts(<?php echo $post; ?>)</a> 
    <a href="plugin">Custom(<?php echo $plugin; ?>)</a>
    
    <a href="delete">Delete(<span></span>)</a> 
    <a href="edit">Edit(<span></span>)</a> 
    <a href="read">Read(<span></span>)</a>
    <a href="publish">Publish(<span></span>)</a>
</div>
<div id="capabilities-content">


    <?php
    if ($caps) {
        echo '<ul class="caps-filter">';
        if ($caps['core'])
            foreach ($caps['core'] as $cap=>$obj_cap) {
                echo '<li class="core-type '.$obj_cap['subtype'].'"><input type="checkbox" id="' . $cap . '" value="' . $cap . '" class="capabilities">' . $cap . '</li>';
            }
        if ($caps['post'])
            foreach ($caps['post'] as $cap=>$obj_cap) {
                echo '<li class="post-type '.$obj_cap['subtype'].'"><input type="checkbox" id="' . $cap . '" value="' . $cap . '" class="capabilities">' . $cap . '</li>';
            }
        if ($caps['plugin'])
            foreach ($caps['plugin'] as $cap=>$obj_cap) {
                echo '<li class="plugin-type '.$obj_cap['subtype'].'"><input type="checkbox" id="' . $cap . '" value="' . $cap . '" class="capabilities">' . $cap . ' <a class="delete-cap" data-cap="' . $cap . '" href="#"><span class="dashicons dashicons-trash"></span></a></li>';
            }
        echo '</ul>';
    }
    ?>




</div>



<div id="dialog-form" title="Create new Capablity">

    <form>
        <fieldset>

            <input type="text" name="cap-name" id="cap-name" value="" class="text ui-widget-content ui-corner-all" placeholder="Name">


            <!-- Allow form submission with keyboard without duplicating the dialog button -->
            <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
        </fieldset>
    </form>
</div>