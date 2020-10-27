<?php 
use Mtk\Admin\Custom_Post_Types;
?>


<h2>Custom post types</h1>

<div id="store_address-description"><p>Create, Update and Delete Custom Post Types</p>
</div>

<form method="post" id="cpt-form" action="#" enctype="multipart/form-data">



    <table class="form-table">


        <tr valign="top" class="">
            <th scope="row" class="">Enabled</th>
            <td>
                <fieldset>

                    <label for="cpt-has-archive">
                        <input name="cpt-active" id="cpt-active" type="checkbox" class="" value="1">						
                    </label> 

                </fieldset>

            </td>
        </tr>


        <tr valign="top">
            <th scope="row" class=>
                <label for="cpt-name">Name <span ></span></label>
            </th>
            <td>
                <input name="cpt-name" id="cpt-name" class="inputs-value" type="text" placeholder="" required=""> 							
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="">
                <label for="cpt-slug">Slug <span ></span></label>
            </th>
            <td>
                <input name="cpt-slug" class="inputs-value" id="cpt-slug" type="text" placeholder="" required=""> 							
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="">
                <label for="cpt-description">Desciption <span ></span></label>
            </th>
            <td>
                <textarea name="cpt-description" id="cpt-description" class="inputs-value"></textarea> 							
            </td>
        </tr>
        <tr valign="top" class="">
            <th scope="row" class="">Custom Capability</th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span>Custom Capability</span></legend>
                    <label for="cpt-custom-cap">
                        <input name="cpt-custom-cap" id="cpt-custom-cap" type="checkbox" class="" value="1">						
                    </label> 
                    <p class="description">Used to build the read, edit, and delete capabilities.</p>																
                </fieldset>

            </td>
        </tr>
        <tr valign="top" class="capability-type">
            <th scope="row" class="">
                <label for="cpt-cap">Capability Type <span ></span></label>
            </th>
            <td>
                <input name="cpt-cap" id="cpt-cap" type="text" class="inputs-value" placeholder=""> 
                <p class="description">Type the sigular and plural way separated by a vertical bar. Ex: <i>Movie|Movies</i></p>
            </td>
        </tr>
        <tr valign="top" class="">
            <th scope="row" class="">Has Archive</th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span>Has Archive</span></legend>
                    <label for="cpt-has-archive">
                        <input name="cpt-has-archive" id="cpt-has-archive" type="checkbox" class="" value="1">						
                    </label> 
                    <p class="description">Whether or not there should be post type archives.</p>																
                </fieldset>

            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="">
                <label for="cpt-position">Menu position<span ></span></label>
            </th>
            <td>
                <input name="cpt-position" id="cpt-position" class="inputs-value" type="number" placeholder=""> 							
            </td>
        </tr>



        <tr valign="top" class="">
            <th scope="row" class="">Theme Support</th>
            <td class="supports-checkbox">
                <fieldset>
                    <?php
                    $supports = ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom fields', 'comments', 'pevisions', 'page Attributes', 'post Formats'];
                    foreach ($supports as $support) {
                        $slug = Mtk\create_slug($support);
                        ?>

                        <label for="cpt-sp-<?php echo $slug; ?>">
                            <input name="cpt-supports[]" id="cpt-sp-<?php echo $slug; ?>" type="checkbox" class="" value="<?php echo $slug; ?>"> <?php echo ucwords($support); ?>						
                        </label> 

                        <?php
                    }
                    ?>
                </fieldset>
            </td>
        </tr>
    </table>

    <p class="submit">
        <button name="save-mt-cpt" class="button-primary save-mt-cpt" type="submit" value="save">Save changes</button>

        <button style="display: none;" name="update-mt-cpt" class="button-secondary update-mt-cpt inputs-value" type="submit" value="update">Update changes</button>
        <button style="display: none;" class="button-cancel cancel-update" type="button" value="1">Cancel Update</button>
        <?php wp_nonce_field('save_mt_cpt', 'nonce_save_mt_cpt'); ?>

    </p>


</form>

<h2>Custom post types list</h2>



<table class="mtk-cpt-list widefat" cellspacing="0" aria-describedby="payment_gateways_options-description">
    <thead>
        <tr>
            <th class="name">Name</th><th class="status">Status</th><th class="description">Description</th><th class="action"></th>						
        </tr>
    </thead>
    <?php
    
    
    $cpts = Custom_Post_Types::get_all();
    var_dump($cpts);
    if ($cpts) {


        echo '<tbody>';
        foreach ($cpts as $cpt) {
            ?>

            <tr>
                <td class="name" width="">
                    <?php echo $cpt['name']; ?>
                </td>
                <td class="status" width="2%">
                    <?php if ($cpt["active"]) { ?>Enabled <?php
                    } else {
                        echo 'Disabled';
                    }
                    ?>
                </td>
                <td class="description" width=""><?php echo $cpt['description']; ?></td>
                <td class="action" width="130">




                    <a class="button alignleft cpt-delete" href="<?php echo wp_nonce_url(add_query_arg('delete-cpt', $cpt['id'], Mtk\get_current_location()), 'delete_cpt_wpnonce'); ?>">Delete</a>
                    <a class="button alignright cpt-edit" href="#" data-data='<?php echo json_encode($cpt); ?>'>Edit</a>
                </td>
            </tr>
            <?php
        }
        echo '</tbody>';
    }
    ?>

</table>