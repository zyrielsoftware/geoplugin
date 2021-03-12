<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//function setup() {

    // Define the CSV Path
    //$csv_path = plugin_dir_path(__FILE__) . '/csv.csv';
    //echo $csv_path.'<br>';
    // $allcsv = get_attached_media('text');
    // var_dump($allcsv); 
    // if ( wp_mkdir_p( $csv_path.'/sub/directory' ) ) {
    //     echo __( 'It worked! Now look for a directory named "a".', 'textdomain' );
    //   }
    //$rows   = array_map( 'str_getcsv', file( $csv_path ) );
    //echo '<pre>';
    // Loop over the data and add every row to the queue
    //foreach ( $rows as $row ) {
            // $proptech_company_info = array(
            //     'post_title' => esc_attr(strip_tags($row[0])),
            //     'post_type' => 'wc-address',
            //     'post_status' => 'publish'
            // );
            //$proptech_company_id = wp_insert_post($proptech_company_info);
            //wp_set_object_terms( $proptech_company_id, $row[1], 'wca-category' );
        // $row_data = array(
        //     'first_name' => $row[0], // First name
        //     'last_name'  => $row[1] // Last name

        // );
        
        //var_dump($row);
        //$unique_id  = md5( $row[2] );
        //$this->push( new WP_Batch_Item( $unique_id, $row_data ) );
    //}
    //echo '</pre>';
//}
//var_dump('hiiiiiiiiiii');

//add_shortcode('b2importform','b2importform_function');

//function b2importform_function(){
 //ob_start(); 

//  $file = fopen("p.csv","r");
//  while(! feof($file))
//    {
//    print_r(fgetcsv($file));
//    }
//  fclose($file);
// $file = fopen(plugin_dir_path(__FILE__)."csv.csv","r");

// var_dump($file);
// $i=0;
//  while ((($column = fgetcsv($file, 9999, ",")) !== FALSE) && ($i < 10000)) 
//    {

//     $data = fgetcsv($file);

//     echo '<pre>';
//     print_r($data[0]);
//     print_r($data[1]);
//     echo '<pre>';
   
//     $proptech_company_info = array(
//         'post_title' => esc_attr(strip_tags($data[0])),
//         'post_type' => 'wc-address',
//         'post_status' => 'publish'
//     );
    //$proptech_company_id = wp_insert_post($proptech_company_info);

    // update_field( 'what_does_company_do', $data[8], $proptech_company_id );
    // update_field( 'proptech_company_website', $data[3], $proptech_company_id );
    // update_field( 'proptech_company_facebook', $data[4], $proptech_company_id );
    // update_field( 'proptech_company_linkedin', $data[6], $proptech_company_id );
    // update_field( 'proptech_company_twitter', $data[5], $proptech_company_id );
    // update_field( 'proptech_company_email', $data[11], $proptech_company_id );
    // update_field( 'proptech_company_phone', $data[12], $proptech_company_id );
    // update_field( 'proptech_company_address', $data[10], $proptech_company_id );

    // $real_str = $data[1];
    // if($real_str){
    //     $real_arr = explode(', ', $real_str);
    // }else{
    //     $real_arr = array('real-estate-cat');
    // }
    
    //wp_set_object_terms( $proptech_company_id, $data[1], 'wca-category' );
    // wp_set_object_terms( $proptech_company_id, array($data[0]), 'operational' );
    // wp_set_object_terms( $proptech_company_id, array('1-20'), 'team_size' );
    // wp_set_object_terms( $proptech_company_id, array('1-to-50'), 'number_clients' ); 
//     echo $i. "th row inserted";
// $i++;

//    }
// fclose($file);
// var_dump('End');

 //return ob_get_clean();
//}


// Check that the nonce is valid, and the user can edit this post.
if (isset( $_POST['my_image_upload_nonce'] ) 
    //&& wp_verify_nonce( $_POST['my_image_upload_nonce'], 'my_image_upload' )
){
    // Input type file name
    $image_input_name = 'my_image_upload';
    // Allowed image types
    $allowed_image_types = array('text/csv');
    // Maximum size in bytes
    //$max_image_size = (1000 * 1000)/4; // 250 KB (approx)
    // Check if there's an image
    if (isset($_FILES[$image_input_name]['size']) && $_FILES[$image_input_name]['size'] > 0){
        // Check conditions
        if(1==1){
            // You shall pass
            // These files need to be included as dependencies when on the front end.
            // require_once( ABSPATH . 'wp-admin/includes/image.php' );
            // require_once( ABSPATH . 'wp-admin/includes/file.php' );
            // require_once( ABSPATH . 'wp-admin/includes/media.php' );
            // Let WordPress handle the upload.
            // Remember, 'my_image_upload' is the name of our file input in our form above.
            $attachment_id = media_handle_upload($image_input_name , 0 );
            update_option('wca_import_file_id', $attachment_id, true);
            if ( is_wp_error( $attachment_id ) ) {
                // There was an error uploading the image.
            } 
        } else {
            // You shall not pass
            echo 'this is not a csv file';
        }
    }
} else {
    // The security check failed, maybe show the user an error.
}
$csvfileid = get_option('wca_import_file_id');
$fullsize_path = get_attached_file( $csvfileid );
//var_dump($fullsize_path);
//$csvfileid = $csvfileid ? $attachment_id : $csvfileid;
?>
<div class="wca-shadow"> 
<form id="featured_upload" method="post" action="#" enctype="multipart/form-data">
	<input type="file" name="my_image_upload" id="my_image_upload"  multiple="false" />
	<input type="hidden" name="post_id" id="post_id" value="55" />
	<?php wp_nonce_field( 'my_image_upload', 'my_image_upload_nonce' ); ?>
	<input id="submit_my_image_upload" name="submit_my_image_upload" type="submit" value="Upload" />
</form>
<?php if($csvfileid){ ?>
<div id="wca-import" class="button button-primary" data-wcaimportfileid="<?php echo $csvfileid; ?>">Import</div>
<div id="wca-import-response"></div>
<?php } ?>
</div>


