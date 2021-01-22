<?php
/**
 * Plugin Name: Zyriel GEO.
 * Plugin URI: http://www.expinator.com
 * Description: Manage woocommerce addresses.
 * Version: 1.1.0
 * Author: Expinator
 * Author URI: http://www.expinator.com
 * Text Domain: wc-addresses
 * */
global $wc_addresses;
defined('ABSPATH') or die();
defined('WCA')  OR define('WCA', plugin_dir_url(__FILE__));
defined('WCA_PATH')  OR define('WCA_PATH', plugin_dir_path(__FILE__));
defined('WCA_TEXTDOMAIN')  OR define('WCA_TEXTDOMAIN', 'wc-addresses');




class WC_addresses {

    public function __construct() {
        add_action( 'init', array( $this, 'wc_addresses_plugin'));
        register_activation_hook(__FILE__, array(__CLASS__, 'wc_addresses_activated'));
        register_deactivation_hook(__FILE__, array(__CLASS__, 'wc_addresses_deactivated'));
        add_action( 'admin_enqueue_scripts', array( $this, 'wca_admin_assets' ));
        add_action( 'wp_enqueue_scripts', array($this, 'wca_frontend_assets'));
        add_action( 'init', array( $this, 'wc_addresses' ) );
        add_filter( 'enter_title_here', array($this,'wca_change_title_text' ));
        add_action( 'init', array( $this, 'wca_taxonomy'), 0 );
        add_filter( 'manage_wc-address_posts_columns', array( $this, 'wca_columns') );
        add_action( 'manage_wc-address_posts_custom_column', array( $this, 'wca_columns_data'), 10, 2);
        add_filter( 'manage_edit-wc-address_sortable_columns', array( $this,'wca_sortable_columns'));
        add_action( 'restrict_manage_posts', array($this,'wca_filterable_column'));
        add_filter( 'parse_query', array($this,'wca_filter_query'));
        add_action( 'admin_menu', array($this, 'wca_add_menu_pages'));
        add_action( 'woocommerce_after_checkout_validation', array( $this, 'wca_validate' ) );
        add_action( 'woocommerce_before_main_content', array( $this, 'wca_check_address'), 10 );
        add_action( 'wp_ajax_nopriv_wca_address_validater', array( $this, 'wca_address_validater'));
        add_action( 'wp_ajax_wca_address_validater', array( $this, 'wca_address_validater'));
        add_action( 'wp_footer', array( $this, 'popup_html_content'));
        add_filter( 'woocommerce_checkout_fields', array( $this, 'checkout_street_address_update'));
    }
    public function wc_addresses_plugin(){
        load_theme_textdomain(WCA_TEXTDOMAIN, false, basename(dirname(__FILE__)) . '/languages');
        require WCA_PATH . 'dist/metabox.php';
        require WCA_PATH . 'dist/settings.php';
    }
    public function wc_addresses_activated()
    {
      update_option( 'wca_error_message', 'We are sorry but we are not delivering to this address' );
      update_option( 'wca_dialog_header', 'Welcome To Zyriel !' );
      update_option( 'wca_dialog_error', 'Please enter valid street address.' );
      update_option( 'wca_dialog_content', 'Currently we are delivering to certain Sub divisions. To assure we are delivering in your area please provide your street address.' );
      update_option( 'wca_success_dialog_header', 'Perfect !' );
      update_option( 'wca_success_dialog_content', 'We deliver to your area!. Please browse our currrent inventory and let us know what you would like us to deliver to you! Thanks!' );
    }
    public function wc_addresses_deactivated()
    {
      delete_option( 'wca_error_message' );
      delete_option( 'wca_dialog_header' );
      delete_option( 'wca_dialog_error' );
      delete_option( 'wca_dialog_content' );
      delete_option( 'wca_success_dialog_header' );
      delete_option( 'wca_success_dialog_content' );
    }
    public function wca_admin_assets() {
        wp_register_style( 'wca_meta_style', WCA.'src/assets/css/meta-box.css',array(),time(),'All' );
        wp_enqueue_style( 'wca_meta_style' );
        wp_register_script('wca_admin_js', WCA.'src/assets/js/admin.js', array( ),time(),true );
        wp_enqueue_script( 'wca_admin_js' );
    }
    public function wca_frontend_assets() {
        wp_register_style('wca_popup_css',  WCA . 'src/assets/css/popup-modal.css', array(), time(), 'All');
        wp_enqueue_style('wca_popup_css');
        wp_register_style('wca_swal2_css',  WCA . 'src/assets/css/sweetalert2.min.css', array(), time(), 'All');
        wp_enqueue_style('wca_swal2_css');
        wp_register_script('wca_swal2_js',  WCA . 'src/assets/js/sweetalert2.min.js', array('jquery'), time(), false);
        wp_enqueue_script('wca_swal2_js');
        wp_register_script('wca_frontend_js', WCA . 'src/assets/js/frontend.js', array(), time(), false);
        wp_enqueue_script('wca_frontend_js');
        wp_localize_script('wca_frontend_js', 'wca_ajax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }

    public function wc_addresses() {
        $labels = array(
            'name'                => _x( 'WC Addresses', 'Post Type General Name', WCA_TEXTDOMAIN ),
            'singular_name'       => _x( 'WC Address', 'Post Type Singular Name', WCA_TEXTDOMAIN ),
            'menu_name'           => __( 'WC Addresses', WCA_TEXTDOMAIN ),
            'parent_item_colon'   => __( 'Parent Address', WCA_TEXTDOMAIN ),
            'all_items'           => __( 'All Addresses', WCA_TEXTDOMAIN ),
            'view_item'           => __( 'View Address', WCA_TEXTDOMAIN ),
            'add_new_item'        => __( 'Add New Address', WCA_TEXTDOMAIN ),
            'add_new'             => __( 'Add New', WCA_TEXTDOMAIN ),
            'edit_item'           => __( 'Edit Address', WCA_TEXTDOMAIN ),
            'update_item'         => __( 'Update Address', WCA_TEXTDOMAIN ),
            'search_items'        => __( 'Search Address', WCA_TEXTDOMAIN ),
            'not_found'           => __( 'Not Found', WCA_TEXTDOMAIN ),
            'not_found_in_trash'  => __( 'Not found in Trash', WCA_TEXTDOMAIN ),
        );

        $args = array(
            'label'               => __( 'wc_address', WCA_TEXTDOMAIN ),
            'description'         => __( 'woocommerce addresses', WCA_TEXTDOMAIN ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'revisions' ),
            'taxonomies'          => array( 'wca-category' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-email',
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest'        => true,

        );

        register_post_type( 'wc-address', $args );

    }

    public function wca_change_title_text( $title ){
     $screen = get_current_screen();

         if  ( 'wc-address' == $screen->post_type ) {
              $title = 'Add Street Name';
         }

         return $title;
    }

    public function wca_taxonomy() {

    // Labels part for the GUI

      $labels = array(
        'name' => _x( 'Category', 'taxonomy general name' ),
        'singular_name' => _x( 'Category', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Category' ),
        'popular_items' => __( 'Popular Category' ),
        'all_items' => __( 'All Categories' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Category' ),
        'update_item' => __( 'Update Category' ),
        'add_new_item' => __( 'Add New Category' ),
        'new_item_name' => __( 'New Topic Name' ),
        'separate_items_with_commas' => __( 'Separate categories with commas' ),
        'add_or_remove_items' => __( 'Add or remove categories' ),
        'choose_from_most_used' => __( 'Choose from the most used categories' ),
        'menu_name' => __( 'Category' ),
      );

    // Now register the non-hierarchical taxonomy like tag

      register_taxonomy('wca-category','wc_address',array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'wca-taxonomy' ),
      ));
    }


    public function wca_columns( $columns ) {
      $columns['title'] = __( 'Street Name', WCA_TEXTDOMAIN );
      $columns['wca_city'] = __( 'City', WCA_TEXTDOMAIN );
      $columns['wca_state'] = __( 'State', WCA_TEXTDOMAIN );
      $columns['wca_zip'] = __( 'Zip Code', WCA_TEXTDOMAIN );
      $columns['wca_country'] = __( 'Country', WCA_TEXTDOMAIN );
      $columns['wca_status'] = __( 'Status', WCA_TEXTDOMAIN );
      unset( $columns['date'] );

      return $columns;
    }


    public function wca_columns_data( $column, $post_id ) {
      if ( 'wca_city' === $column ) {
        echo get_post_meta( $post_id, "wca_city", true);
      }
      if ( 'wca_state' === $column ) {
        echo get_post_meta( $post_id, "wca_state", true);
      }
      if ( 'wca_zip' === $column ) {
        echo get_post_meta( $post_id, "wca_zip", true);
      }
      if ( 'wca_country' === $column ) {
        echo get_post_meta( $post_id, "wca_country", true);
      }
      if ( 'wca_status' === $column ) {
        echo ucwords(get_post_meta( $post_id, "wca_status", true));
      }
    }


    public function wca_sortable_columns( $columns ) {
      $columns['wca_city'] = 'wca_city';
      $columns['wca_state'] = 'wca_state';
      $columns['wca_zip'] = 'wca_zip';
      $columns['wca_country'] = 'wca_country';
      $columns['wca_status'] = 'wca_status';
      return $columns;
    }

    public function wca_filterable_column(){
      global $typenow;
      $post_type = 'wc-address'; // change to your post type
      $taxonomy  = 'wca-category'; // change to your taxonomy
      $wca_city = isset($_GET['wca_city'])? $_GET['wca_city']:'';
      $wca_state = isset($_GET['wca_state'])? $_GET['wca_state']:'';
      $wca_streetname = isset($_GET['wca_streetname'])? $_GET['wca_streetname']:'';
      $wca_zip = isset($_GET['wca_zip'])? $_GET['wca_zip']:'';
      $wca_status = isset($_GET['wca_status'])? $_GET['wca_status']:'';
      if ($typenow == $post_type) {
        $selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
        $info_taxonomy = get_taxonomy($taxonomy);
        wp_dropdown_categories(array(
          'show_option_all' => sprintf( __( 'Show all %s', WCA_TEXTDOMAIN ), $info_taxonomy->label ),
          'taxonomy'        => $taxonomy,
          'name'            => $taxonomy,
          'orderby'         => 'name',
          'selected'        => $selected,
          'show_count'      => true,
          'hide_empty'      => true,
        ));
        echo "<input type='text' name='wca_streetname' value='".$wca_streetname."' placeholder='Street Name'/>";
        echo "<input type='text' name='wca_city' value='".$wca_city."' placeholder='City'/>";
        echo "<input type='text' name='wca_state' value='".$wca_state."' placeholder='State'/>";
        echo "<input type='text' name='wca_zip' value='".$wca_zip."' placeholder='Zip'/>";
        $statuses = ['Enable','Disable'];
        ?>
          <select name="wca_status">
            <option value=""><?php _e('Filter By Status', WCA_TEXTDOMAIN); ?></option>
            <?php
                foreach ($statuses as $status) {
                    printf
                        (
                            '<option value="%s"%s>%s</option>',
                            strtolower($status),
                            strtolower($status) == $wca_status? ' selected="selected"':'',
                            $status
                        );

                }
            ?>
            </select>
        <?php
      };
    }


    public function wca_filter_query($query) {
      global $pagenow;
      global $typenow;
      $post_type = 'wc-address';
      $taxonomy  = 'wca-category';
      $q_vars    = &$query->query_vars;
      if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
        $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
        $q_vars[$taxonomy] = $term->slug;
      }
      $current_page = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
      if ( is_admin() && $post_type == $typenow && 'edit.php' == $pagenow){
        $queryParamsCounter = 0;
        if (isset( $_GET['wca_city'] ) && $_GET['wca_city'] != '')
        {
          $wca_city = $_GET['wca_city'];
          $queryParamsCounter++;
        }
        if (isset( $_GET['wca_state'] ) && $_GET['wca_state'] != '')
        {
          $queryParamsCounter++;
          $wca_state = $_GET['wca_state'];
        }
        if (isset( $_GET['wca_streetname'] ) && $_GET['wca_streetname'] != '')
        {
          $queryParamsCounter++;
          $wca_streetname = $_GET['wca_streetname'];
        }
        if (isset( $_GET['wca_zip'] ) && $_GET['wca_zip'] != '')
        {
          $queryParamsCounter++;
          $wca_zip = $_GET['wca_zip'];
        }
        if (isset( $_GET['wca_status'] ) && $_GET['wca_status'] != '')
        {
          $queryParamsCounter++;
          $wca_status = $_GET['wca_status'];
        }

        $meta_query = array();

        if ($queryParamsCounter > 1) {
          $meta_query['relation'] = 'AND';
        }

        if (isset($wca_city)) {
          $meta_query[] =       array(
            'key'     => 'wca_city',
            'value'   => $wca_city,
            'compare' => 'LIKE',
          );
        }
        if (isset($wca_state)) {
          $meta_query[] = array(
            'key'     => 'wca_state',
            'value'   => $wca_state,
            'compare' => 'LIKE',
          );
        }
        if (isset($wca_streetname)) {
          $q_vars['s'] = $wca_streetname;
        }
        if (isset($wca_zip)) {
          $meta_query[] = array(
            'key'     => 'wca_zip',
            'value'   => $wca_zip,
            'compare' => 'LIKE',
          );
        }
        if (isset($wca_status)) {
          $meta_query[] = array(
            'key'     => 'wca_status',
            'value'   => $wca_status,
            'compare' => 'LIKE',
          );
        }
        $query->set( 'meta_query', $meta_query);
      }
    }
    public function wca_add_menu_pages() {
        add_submenu_page('edit.php?post_type=wc-address', 'Settings', 'WCA Settings', 'manage_options', 'wca_settings_page', array($this, 'wca_settings_page_fn'));
    }
    public function wca_settings_page_fn() {
        ?>
          <style type="text/css">
             .wca-shadow .sec-title {
                  border: 1px solid #ebebeb;
                  background: #fff;
                  color: #d54e21;
                  padding: 2px 4px;
              }
              .wca-shadow{
                  border:1px solid #ebebeb; padding:5px 20px; background:#fff; margin-bottom:40px;
                  -webkit-box-shadow: 4px 4px 10px 0px rgba(50, 50, 50, 0.1);
                  -moz-box-shadow:    4px 4px 10px 0px rgba(50, 50, 50, 0.1);
                  box-shadow:         4px 4px 10px 0px rgba(50, 50, 50, 0.1);
              }
          </style>
          <div class="wrap">
            <h1><?php _e('Woocommerce address Settings'); ?></h1>
            <form method="post" action="options.php" class="wca-shadow">
                <?php
                  settings_fields("wca-options");
                  do_settings_sections("wca-plugin-options");
                  submit_button();
                ?>
            </form>
          </div>
        <?php
    }
    public function wca_validate($posted)
    {
        global $wpdb;
        extract($posted);
        $wca = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_title = '%s'", $billing_address_1) );
        if(empty($wca)){
          //$string = ;
          wc_add_notice( get_option('wca_error_message'), 'error' );
        }
    }

    public function wca_check_address(){
      if(!isset($_COOKIE['address_validator'])){
        $wca_heading = get_option('wca_dialog_header');
        $wca_content = get_option('wca_dialog_content');
        $wca_perfect_heading = get_option('wca_success_dialog_header');
        $wca_perfect_content = get_option('wca_success_dialog_content');
        $wca_dialog_error = get_option('wca_dialog_error');
        ?>
        <script type="text/javascript">
            jQuery(function(){
              var html = '<h3 class="address-heading"><?php echo $wca_heading; ?></h3> <p class="address-disc"><?php echo $wca_content; ?></p><p class="input-address" id="address-input">Street Name</p><input id="address-inpup-box" class="address-box" placeholder="" type="text"><p class="address-error"></p><p class="button-content"><button id="submit-address" class="address-submit">SUBMIT</button></p>';
              jQuery("#modal-popup").html(html);
              jQuery("#modal-main").show();
              jQuery(document).on('click','#submit-address', function () {
                var address = jQuery(document).find("#address-inpup-box").val();
                if (address!='') {
                  gf_data={
                      'action': 'wca_address_validater',
                      'address': address,
                  };
                  jQuery.ajax({
                      url: "<?php echo admin_url('admin-ajax.php'); ?>",
                      type: "POST",
                      data: gf_data,
                      success: function (resp) {
                        var data = JSON.parse(resp);
                        if(data.status == 'invalid'){
                          setCookie('address_validator','',-1);
                          window.location.replace(data.url);
                        }else{
                          setCookie('address_validator',address,1);
                          html ='<h3 class="address-heading"><?php echo $wca_perfect_heading; ?></h3><p class="address-disc-success"><?php echo $wca_perfect_content; ?></p><p class="button-content"><button id="ok-address" class="address-submit">OK</button></p>';
                          jQuery("#modal-popup").html(html);
                        }
                      }
                  });
                }else{
                  jQuery(".address-error").html('<?php echo $wca_dialog_error; ?>');
                }
              });
              jQuery(document).on('click','#ok-address', function () {
                jQuery("#modal-main").hide();
              });

              /*swal({
                title: '<?php echo $wca_heading; ?>',
                text: "<?php echo $wca_content; ?>",
                input: 'text',
                allowOutsideClick: false,
                allowEscapeKey: false,
                //showCancelButton: true,
                inputValidator: function(value) {
                  return new Promise(function(resolve, reject) {
                    if (value) {
                      gf_data={
                          'action': 'wca_address_validater',
                          'address': value,
                      };
                      jQuery.ajax({
                          url: "<?php echo admin_url('admin-ajax.php'); ?>",
                          type: "POST",
                          data: gf_data,
                          success: function (resp) {
                            var data = JSON.parse(resp);
                            if(data.status == 'invalid'){
                              setCookie('address_validator','',-1);
                              window.location.replace(data.url);
                            }else{
                              setCookie('address_validator',value,1);
                              swal({
                                title: '<?php echo $wca_perfect_heading; ?>',
                                text: '<?php echo $wca_perfect_content; ?>',
                                //timer: 2000
                              });
                            }
                          }
                      });
                    } else {
                      reject('<?php echo $wca_dialog_error; ?>');
                    }
                  });
                }
              });*/
            });
        </script>
      <?php
      }
    }
    public function wca_address_validater()
    {
        global $wpdb;
        extract($_POST);
        $response = [];
        $wca = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_title = '%s'", $address) );
        if(empty($wca)){
          $page_id = get_option('wca_redirection');
          $response['url'] = get_permalink($page_id);
          $response['status'] = 'invalid';
          setcookie('address_validator', '', -1, "/");
        }else{
          $response['url'] = '';
          $response['status'] = 'valid';
          setcookie('address_validator', $address, '', "/");
        }
        echo json_encode($response);
        die();
    }

    public function popup_html_content(){
      ?>
      <div id="modal-main" class="popup-modal">
        <div id="modal-popup" class="modal-content">
          <p></p>
        </div>
      </div>
      <?php
    }
    function checkout_street_address_update( $fields = array() ) {
      if(isset($_COOKIE['address_validator'])) {
        $fields['billing']['billing_address_1']['default']= $_COOKIE['address_validator'];
        $fields['shipping']['billing_address_1']['default'] = $_COOKIE['address_validator'];
      }else{
        $fields['billing']['billing_address_1']['default']= '';
        $fields['shipping']['billing_address_1']['default'] = '';
      }
      return $fields;
    }
}

$wc_addresses = new WC_addresses();

