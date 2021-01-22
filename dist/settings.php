<?php

function wca_error_message(){
    ?>
    <input type="text" name="wca_error_message" value="<?= get_option('wca_error_message'); ?>" style="width: 50%;">
    <?php
}

function wca_comingsoon_page() {
    $pages = get_pages();
    ?>
        <select name="wca_redirection" style="width: 50%;">
            <option value="">Select Redirect page</option>
        <?php
        if (!empty($pages) ):
            foreach ($pages as $key => $page):
        ?>
            <option value="<?php echo $page->ID; ?>" <?php echo ($page->ID == get_option("wca_redirection"))  ? 'selected' : ''; ?>><?php echo $page->post_title; ?></option>
        <?php
            endforeach;
        endif;
    ?>
    </select>
    <?php
}
function wca_dialog_error(){
    ?>
    <input type="text" name="wca_dialog_error" value="<?= get_option('wca_dialog_error'); ?>" style="width: 50%;">
    <?php
}
function wca_dialog_header(){
    ?>
    <input type="text" name="wca_dialog_header" value="<?= get_option('wca_dialog_header'); ?>" style="width: 50%;">
    <?php
}
function wca_dialog_content(){
    ?>
    <textarea name="wca_dialog_content" style="width: 50%;"><?= get_option('wca_dialog_content'); ?></textarea>
    <?php
}

function wca_success_dialog_header(){
    ?>
    <input type="text" name="wca_success_dialog_header" value="<?= get_option('wca_success_dialog_header'); ?>" style="width: 50%;">
    <?php
}
function wca_success_dialog_content(){
    ?>
    <textarea name="wca_success_dialog_content" style="width: 50%;"><?= get_option('wca_success_dialog_content'); ?></textarea>
    <?php
}
function display_wca_panel_fields() {
        add_settings_section("wca-settings-group", "Redirect Section", null, "wca-plugin-options");
        add_settings_section("wca-messages-group", "Dialog Section", null, "wca-plugin-options");
        add_settings_section("wca-success-group", "Address Found Dialog", null, "wca-plugin-options");
        add_settings_field("wca_redirection", "Select Redirect Page", "wca_comingsoon_page", "wca-plugin-options", "wca-settings-group");
        add_settings_field("wca_error_message", "Checkout Error Message", "wca_error_message", "wca-plugin-options", "wca-settings-group");
        add_settings_field("wca_dialog_header", "Dialog Heading", "wca_dialog_header", "wca-plugin-options", "wca-messages-group");
        add_settings_field("wca_dialog_error", "Invalid street address", "wca_dialog_error", "wca-plugin-options", "wca-messages-group");
        add_settings_field("wca_dialog_content", "Dialog Content", "wca_dialog_content", "wca-plugin-options", "wca-messages-group");
        add_settings_field("wca_success_dialog_header", "Dialog Heading", "wca_success_dialog_header", "wca-plugin-options", "wca-success-group");
        add_settings_field("wca_success_dialog_content", "Dialog Content", "wca_success_dialog_content", "wca-plugin-options", "wca-success-group");
        register_setting("wca-options", "wca_redirection");
        register_setting("wca-options", "wca_error_message");
        register_setting("wca-options", "wca_dialog_error");
        register_setting("wca-options", "wca_dialog_header");
        register_setting("wca-options", "wca_dialog_content");
        register_setting("wca-options", "wca_success_dialog_header");
        register_setting("wca-options", "wca_success_dialog_content");


}

add_action("admin_init", "display_wca_panel_fields");


