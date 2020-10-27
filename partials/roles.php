<?php

use Mtk\Admin\User_Roles;
?>
<h2>Roles</h1>

<div id="store_address-description"><p>Create, Update and Delete Roles</p>
</div>

<form method="post" id="role-form" action="#" enctype="multipart/form-data">



    <table class="form-table">





        <tr valign="top">
            <th scope="row" class=>
                <label for="cpt-name">Name <span ></span></label>
            </th>
            <td >
                <input name="role-name" id="role-name" class="inputs-value" type="text" placeholder="" required=""> 							
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="">
                <label for="role-slug">Slug <span ></span></label>
            </th>
            <td >
                <input name="role-slug" class="inputs-value" id="role-slug" type="text" placeholder="" required=""> 							
            </td>
        </tr>



    </table>

    <p class="submit">
        <button name="save-mt-role" class="button-primary save-mt-role" type="submit" value="save">Save changes</button>

        <button style="display: none;" name="update-mt-role" class="button-secondary update-mt-role inputs-value" type="submit" value="update">Update changes</button>
        <button style="display: none;" class="button-cancel cancel-update-role" type="button" value="1">Cancel Update</button>
        <?php wp_nonce_field('save_mt_role', 'nonce_save_mt_role'); ?>

    </p>


</form>

<h2>Roles list</h2>



<table class="mtk-role-list widefat mtk-table-list" cellspacing="0" aria-describedby="payment_gateways_options-description">
    <thead>
        <tr>
            <th class="name">Name</th><th class="users">User Count</th><th class="cap">Capabilities</th><th class="action">actions</th>						
        </tr>
    </thead>
    <?php
    //var_dump($roles);exit;
    if ($roles) {


        echo '<tbody>';
        foreach ($roles as $slug => $role) {
            ?>

            <tr>
                <td class="name" width="">
                    <?php echo $role['name']; ?>
                </td>
                <td class="users" width="">
                    <?php echo User_Roles::get_users_count_by_role($slug); ?>
                </td>
                <td class="cap" width="">

                    <a class="button alignright role-cap-edit" href="<?php echo $slug; ?>" title="<?php echo User_Roles::format_role_capabilities($role['capabilities']); ?>">Edit</a>
                </td>

                <td class="action">

                    <a class="button alignleft role-delete" href="<?php echo wp_nonce_url(add_query_arg('delete-role', $slug, Mtk\get_current_location()), 'delete_role_wpnonce'); ?>">Delete</a>
                    <a class="button alignleft role-edit" href="#" data-data='<?php echo json_encode($role); ?>' data-slug="<?php echo $slug; ?>">Edit</a>
                    <a class="button alignleft role-duplicate" href="<?php echo wp_nonce_url(add_query_arg(['duplicate-role' => $slug, 'duplicate-role-name' => $role['name']], Mtk\get_current_location()), 'duplicate_role_wpnonce'); ?>">Duplicate</a>
                </td>
            </tr>
            <?php
        }
        echo '</tbody>';
    }
    ?>

</table>