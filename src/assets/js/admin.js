(function($) {
    "use strict";
    
      $('.ekshop-sync-single').click(function(event){
         console.log( $(this).attr('id') );
         console.log( $(this).attr('data-ekprodid') );
         event.preventDefault();
         productSyncById($(this).attr('data-ekprodid'));
         //jQuery(".ur-submit-button").click();
         
      });
  
      function productSyncById(id){
          console.log('from single sync function: '+ id);
          jQuery("#eksid-"+id).addClass("rotating");
          
          jQuery.ajax({
            url: ajax_script_product_sync_by_id.ajax_url,
            type: "post",
            data: {
              action: "product_sync_by_id",
              id: id,
              //hidden_last_name: hidden_last_name,
              //hidden_email_address: hidden_email_address,
              //leaders_emails: leaders_emails,
              //phone_logged_in: phone_logged_in,
              //preferred_contact_method: preferred_contact_method,
              //entrytitle: entrytitle
            },
            success: function(response) {
              response = JSON.parse(response); 
              console.log(response.message);
              if(response.status==2){
                  jQuery("#eksid-"+id).removeClass("rotating");
                  jQuery("body").append("<div class='tempnotice red-bg'>"+ response.message +"</div>");
  
                  setTimeout(function() {
                      jQuery('.tempnotice').fadeOut(5000);
                  }, 10000);
                  //jQuery( ".tempnotice" ).remove();
              }else{
                  if(response.status==1 && (response.invalid_products.length > 0)){
                      jQuery("#eksid-"+id).removeClass("rotating");
                      jQuery("body").append("<div class='tempnotice red-bg'>"+ response.invalid_products[0].message +"</div>");
      
                      setTimeout(function() {
                          jQuery('.tempnotice').fadeOut(5000);
                      }, 10000);
  
                  }else if(response.status==1 && (response.invalid_products.length < 1)){
                      jQuery("#eksid-"+id).removeClass("rotating");
                  jQuery("#eksid-"+id).removeClass("dashicons-update");
                  jQuery("#eksid-"+id).removeClass("orange");
                  jQuery("#eksid-"+id).addClass("dashicons-yes-alt");
                  jQuery("#eksid-"+id).addClass("green");
                  jQuery("body").append("<div class='tempnotice green-bg'>Successfully synced</div>");
  
                  setTimeout(function() {
                      jQuery('.tempnotice').fadeOut(5000);
                  }, 10000);
                  }else{
                      jQuery("#response").html(response.message);
                      jQuery("body").append("<div class='tempnotice green-bg'>"+response.message+"</div>");
  
                  setTimeout(function() {
                      jQuery('.tempnotice').fadeOut(5000);
                  }, 10000);
                  }
                  
              }
              
              //jQuery(".ajaxsaving").removeClass("progress");
              //prop_comp_site_url = jQuery("#prop_comp_site_url").val();
              //jQuery("#append_first_name_reg").html(response);
              // if (response != "notitle") {
              // } else if (response == "notitle") {
              //   jQuery("#titleerror").html("Enter title");
              // }
            },
                    error: function(xhr, status, error) {
                     console.log(error);
                    }
          });
  
          
      
          return false;
      }
  
      $("#ekshop_product_sync").click(function(event) {
      event.preventDefault();
      
      console.log('sync button clicked');
      jQuery("#ekshop_product_sync").html("Syncing <i class='ekshop-sync-single  dashicons dashicons-update rotating' style='margin-top:5px;'></i>");
      productSync('all');
      //jQuery(".ur-submit-button").click();
      
      });
      function productSync(type){
          jQuery.ajax({
              url: ajax_script_product_sync_func.ajax_url,
              type: "post",
              data: {
                action: "product_sync_func",
                type: type,
              },
              success: function(response) {
              response = JSON.parse(response);
              console.log(response);
              if(response.status==2){
                  jQuery("#ekshop_product_sync").removeClass("rotating");
                  jQuery("#ekshop_product_sync").html("Sync failed!");
                  jQuery("#response").html(response.message);
          
              }else if(response.status==1){
                  var output = '';
                  var object = response.invalid_products;
                  for (var property in object) {
                  output += '<div style="display:block"><b>Product id:</b> <a target="_blank" href="'+ajax_script_product_sync_func.siteurl+'/wp-admin/post.php?post=' + object[property].product_reference_id+'&action=edit">' + object[property].product_reference_id+'</a>, <b>Message:</b><span style="color:red"> ' + object[property].message +'</span></div>';
                  }
                  jQuery("#ekshop_product_sync").html("Sync done <i class='ekshop-sync-single  dashicons dashicons-yes-alt' style='margin-top:5px;'></i>");
                  
                  jQuery("#response").html('<b>Message:</b> '+response.message+'<br><b>Invalid products:</b> '+output+'<br><b>Product reference ids:</b> '+response.product_reference_ids);
              }else{
                  jQuery("#response").html(response.message);
                  jQuery("#ekshop_product_sync").removeClass("rotating");
                  jQuery("#ekshop_product_sync").html("Sync Products");
              }
                  //jQuery("#ekshop_token").val(response);
                  //jQuery("#ekshop_product_sync").empty();
                  //jQuery("#ekshop_product_sync").html("Sync done <i class='ekshop-sync-single  dashicons dashicons-yes-alt' style='margin-top:5px;'></i>");
                //jQuery(".ajaxsaving").removeClass("progress");
                //prop_comp_site_url = jQuery("#prop_comp_site_url").val();
                //jQuery("#response").html(response);
                // if (response != "notitle") {
                // } else if (response == "notitle") {
                //   jQuery("#titleerror").html("Enter title");
                // }
              },
                      error: function(xhr, status, error) {
                       console.log(error);
                      }
            });
            return false;
  
      }
  
     $("#wca-import").click(function(event) {
              event.preventDefault();
              var csvimportfileid;
              jQuery("#wca-import").html("Importing <i class='dashicons dashicons-update rotating' style='margin-top:5px;'></i>");
              csvimportfileid = $(this).attr('data-wcaimportfileid');
              console.log(csvimportfileid);
              sendRegFormData(csvimportfileid);
              console.log('import button clicked');
              
          });
          
      function sendRegFormData(csvimportfileid){
        
              console.log(csvimportfileid);
              jQuery.ajax({
                url: wca_admin_js_object.ajax_url,
                type: "post",
                data: {
                  action: "wca_import_fn",
                  hidden_first_name: csvimportfileid,
                
                },
                success: function(response) {
                    console.log(response);
                    if(response){
                        jQuery("#wca-import").html("Done");
                        //jQuery("#wca-import-response").html('done');
                    }
                    
                },
                        error: function(xhr, status, error) {
                         console.log(error);
                         jQuery("#wca-import").html("Something wrong!");
                        }
              });
          
              return false;
          }
  
    
  })(jQuery);