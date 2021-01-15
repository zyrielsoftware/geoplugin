<?php
/**
 * Plugin Name: woocommerce Addresses
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
        add_action( 'admin_enqueue_scripts', array( $this, 'wca_admin_assets' ));
        add_action( 'init', array( $this, 'wc_addresses' ) );
        add_action( 'init', array( $this, 'wca_taxonomy'), 0 );
        add_filter( 'manage_wc-address_posts_columns', array( $this, 'wca_columns') );
        add_action( 'manage_wc-address_posts_custom_column', array( $this, 'wca_columns_data'), 10, 2);
        add_filter( 'manage_edit-wc-address_sortable_columns', array( $this,'wca_sortable_columns'));
    }
    public function wc_addresses_plugin(){
        load_theme_textdomain(WCA_TEXTDOMAIN, false, basename(dirname(__FILE__)) . '/languages');
        require WCA_PATH . 'dist/metabox.php';
    }
    public function wca_admin_assets() {
        wp_register_style( 'wca_meta_style', WCA.'src/assets/css/meta-box.css',array(),time(),'All' );
        wp_enqueue_style( 'wca_meta_style' );
        wp_register_script('wca_admin_js', WCA.'src/assets/js/common.js', array( ),time(),true );
        wp_enqueue_script( 'wca_admin_js' );
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
            'show_in_rest' => true,

        );

        // Registering your Custom Post Type
        register_post_type( 'wc-address', $args );

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
      $columns['wca_streetname'] = __( 'Street Name', WCA_TEXTDOMAIN );
      $columns['wca_city'] = __( 'City', WCA_TEXTDOMAIN );
      $columns['wca_state'] = __( 'State', WCA_TEXTDOMAIN );
      $columns['wca_zip'] = __( 'Zip Code', WCA_TEXTDOMAIN );
      $columns['wca_country'] = __( 'Country', WCA_TEXTDOMAIN );
      unset( $columns['date'] );

      return $columns;
    }


    public function wca_columns_data( $column, $post_id ) {
      if ( 'wca_streetname' === $column ) {
        echo get_post_meta( $post_id, "wca_streetname", true);
      }
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
    }


    public function wca_sortable_columns( $columns ) {
      $columns['wca_streetname'] = 'wca_streetname';
      $columns['wca_city'] = 'wca_city';
      $columns['wca_state'] = 'wca_state';
      $columns['wca_zip'] = 'wca_zip';
      $columns['wca_country'] = 'wca_country';
      return $columns;
    }

}

$wc_addresses = new WC_addresses();

