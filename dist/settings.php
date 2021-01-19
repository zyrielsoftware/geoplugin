<?php

function wca_comingsoon_page() {
    $pages = get_pages();
    ?>
        <select name="wca_redirection">
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
function display_wca_panel_fields() {
        add_settings_section("wca-settings-group", "Address Section", null, "wca-plugin-options");
        add_settings_field("wca_redirection", "Select Redirect Page", "wca_comingsoon_page", "wca-plugin-options", "wca-settings-group");
        register_setting("wca-options", "wca_redirection");


}

add_action("admin_init", "display_wca_panel_fields");


