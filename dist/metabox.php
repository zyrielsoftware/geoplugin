<?php
function extended_wca_meta_box() {

    $screens = array( 'wc-address' );

    foreach ( $screens as $screen ) {
        add_meta_box(
            'custom_availability_meta_options',
            __( 'Address', WCA_TEXTDOMAIN ),
            'extended_wca_meta_box_callback',
            $screen,'normal', 'high',
		    array(
		        '__block_editor_compatible_meta_box' => true,
		    )
        );
    }
}

add_action( 'add_meta_boxes', 'extended_wca_meta_box' );

function extended_wca_meta_box_callback($object)
{
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");

    ?>
        <div class="wca_custom_meta_boxes">
        	<div class="wca_meta_inputs">
                <div class="wca_meta_inputs_half">
    	            <label for="wca_streetname"><?php _e('Street name'); ?></label>
    	            <input name="wca_streetname" id="wca_streetname" class="wca_address_fields" type="text" value="<?php echo get_post_meta($object->ID, "wca_streetname", true); ?>">
                </div>
                <div class="wca_meta_inputs_half">
                    <label for="wca_city"><?php _e('City'); ?></label>
                    <input name="wca_city" id="wca_city" type="text" class="wca_address_fields" value="<?php echo get_post_meta($object->ID, "wca_city", true); ?>">
                </div>
        	</div>
            <div class="wca_meta_inputs ">
                <div class="wca_meta_inputs_half">
    	            <label for="wca_state"><?php _e('State'); ?></label>
    	            <input name="wca_state" id="wca_state" type="text" class="wca_address_fields" value="<?php echo get_post_meta($object->ID, "wca_state", true); ?>">
                </div>
                <div class="wca_meta_inputs_half ">
                    <label for="wca_zip"><?php _e('Zip'); ?></label>
                    <input name="wca_zip" id="wca_zip" type="text" class="wca_address_fields" value="<?php echo get_post_meta($object->ID, "wca_zip", true); ?>">
                </div>
            </div>
            <div class="wca_meta_inputs">
                <label for="wca_country"><?php _e('Country'); ?></label>
                <input name="wca_country" id="wca_country" type="text" class="wca_address_fields" value="<?php echo get_post_meta($object->ID, "wca_country", true); ?>">
            </div>
            <div class="wca_meta_inputs">
	            <label for="wca_status"><?php _e('Status'); ?></label>
                <select name="wca_status" id="wca_status">
                    <option value=""><?php _e('Set Address Status'); ?></option>
                    <option value="enable" <?php echo (get_post_meta($object->ID, "wca_status", true) == "enable")?'selected':''; ?>><?php _e('Enable'); ?></option>
                    <option value="disable" <?php echo (get_post_meta($object->ID, "wca_status", true) == "disable")?'selected':''; ?>><?php _e('Disable'); ?></option>
                </select>
            </div>
            <div class="wca_meta_inputs">
                <label for="wca_notes"><?php _e('Notes'); ?></label>
                <textarea name="wca_notes" id="wca_notes"><?php echo get_post_meta($object->ID, "wca_notes", true); ?></textarea>
            </div>
            <div class="wca_meta_inputs">
                <label for="wca_alerts"><?php _e('Alerts'); ?></label>
                <textarea name="wca_alerts" id="wca_alerts"><?php echo get_post_meta($object->ID, "wca_alerts", true); ?></textarea>
            </div>

        </div>
    <?php
}

function save_extended_wca_meta_box($post_id, $post, $update)
{
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    $slug = array( 'wc-address' );

    if(!in_array($post->post_type, $slug))
        return $post_id;

    $wca_streetname = "";
    $wca_city = "";
    $wca_state = "";
	$wca_zip = "";
    $wca_country = "";
 	$wca_status = "";
    $wca_notes = "";
    $wca_alerts = "";

    if(isset($_POST["wca_streetname"]))
	    {
	        $wca_streetname = $_POST["wca_streetname"];
	    }
    update_post_meta($post_id, "wca_streetname", $wca_streetname);
    if(isset($_POST["wca_city"]))
        {
            $wca_city = $_POST["wca_city"];
        }
    update_post_meta($post_id, "wca_city", $wca_city);
    if(isset($_POST["wca_state"]))
	    {
	        $wca_state = $_POST["wca_state"];
	    }
    update_post_meta($post_id, "wca_state", $wca_state);
    if(isset($_POST["wca_zip"]))
        {
            $wca_zip = $_POST["wca_zip"];
        }
    update_post_meta($post_id, "wca_zip", $wca_zip);
    if(isset($_POST["wca_status"]))
	    {
	        $wca_status = $_POST["wca_status"];
	    }
    update_post_meta($post_id, "wca_zip", $wca_zip);
    if(isset($_POST["wca_country"]))
        {
            $wca_country = $_POST["wca_country"];
        }
    update_post_meta($post_id, "wca_country", $wca_country);
    if(isset($_POST["wca_notes"]))
	    {
	        $wca_notes = $_POST["wca_notes"];
	    }
    update_post_meta($post_id, "wca_notes", $wca_notes);
    if(isset($_POST["wca_alerts"]))
	    {
	        $wca_alerts = $_POST["wca_alerts"];
	    }
    update_post_meta($post_id, "wca_alerts", $wca_alerts);

}

add_action("save_post", "save_extended_wca_meta_box", 10, 3);

?>