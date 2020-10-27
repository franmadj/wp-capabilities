<?php

use Mtk\Admin\Policies;
?>
<h2>Access Policies</h1>
<div id="store_address-description">
    <p>Create, Update and Delete Access Policies</p>
</div>
<div class="rules-item rules-clonable">
    <div class="content-elements">
        <p>Hide This:</p>
        <?php echo $content_elements; ?>
        <select class="mt-sub-elements" name="mt-sub-elements[]">
            <option>All</option>   
        </select>  

    </div>
    <div class="entity-elements">
        <p>For This:</p>
        <?php echo $entity_elements; ?>
        <select class="mt-sub-entities" name="mt-sub-entities[]">
            <option>All</option>
        </select>
        <a href="#" class="substract-icon"></a>

    </div>
</div>
<form method="post" id="policies-form" action="#" enctype="multipart/form-data">
    <div class="policy-header">
        <input name="policy-title" class="inputs-value" id="policy-title" type="text" placeholder="Title" required=""> 
        <a href="#" class="button add-policy-rule">Add Rule</a>
    </div>

    <div class="rules-content">


        <div class="rules-item">
            <div class="content-elements">
                <p>Hide This:</p>
                <?php echo $content_elements; ?>
                <select class="mt-sub-elements" name="mt-sub-elements[]">
                    <option value="all">All</option>   
                </select>  

            </div>
            <div class="entity-elements">
                <p>For This:</p>
                <?php echo $entity_elements; ?>
                <select class="mt-sub-entities" name="mt-sub-entities[]">
                    <option value="all">All</option>
                </select>
                


            </div>

        </div>
    </div>

    <p class="submit">
        <button name="save-mt-policy" class="button-primary save-mt-policy" type="submit" value="save">Save changes</button>

        <button style="display: none;" name="update-mt-policy" class="button-secondary update-mt-policy inputs-value" type="submit" value="update">Update changes</button>
        <button style="display: none;" class="button-cancel cancel-update-policy" type="button" value="1">Cancel Update</button>
        <?php wp_nonce_field('save_mt_policy', 'nonce_save_mt_policy'); ?>

    </p>





</form>

<h2>Policy list</h2>



<table class="mtk-policy-list widefat mtk-table-list" cellspacing="0" aria-describedby="payment_gateways_options-description">
    <thead>
        <tr>
            <th class="name">Name</th><th class="users">Rules Count</th><th class="action">Actions</th>						
        </tr>
    </thead>
    <?php
    var_dump($policies);
    if ($policies) {


        echo '<tbody>';
        foreach ($policies as $slug => $policy) {
            ?>

            <tr>
                <td class="name" width="">
                    <?php echo $policy['title']; ?>
                </td>
                <td class="users" width="">
                    <?php echo count(Policies::get_rules_by_policy($slug)); ?>
                </td>
                

                <td class="action">

                    <a class="button alignleft policy-delete" href="<?php echo wp_nonce_url(add_query_arg('delete-policy', $slug, Mtk\get_current_location()), 'delete_policy_wpnonce'); ?>">Delete</a>
                    <a class="button alignleft policy-edit" href="#" data-data='<?php echo json_encode($policy); ?>' data-slug="<?php echo $slug; ?>">Edit</a>
                    
                </td>
            </tr>
            <?php
        }
        echo '</tbody>';
    }
    ?>

</table>