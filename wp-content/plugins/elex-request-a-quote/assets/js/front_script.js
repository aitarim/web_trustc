jQuery(document).ready(function(){

  jQuery(".menuContainer ul").append("<ul><li>Minicart</li></ul>");

    jQuery(".elex-raq-maximize-btn").click(function(){
      jQuery(".elex-raq-quote-list-popup").removeClass("elex-raq-minimize");
      jQuery(".elex-raq-minimize-btn").removeClass("d-none");
      jQuery(this).addClass("d-none");
    });
  });


  jQuery(document).ready(function(){
    jQuery(".elex-raq-minimize-btn").click(function(){
      jQuery(".elex-raq-quote-list-popup").addClass("elex-raq-minimize");
      jQuery(".elex-raq-maximize-btn ").removeClass("d-none");
      jQuery(this).addClass("d-none");
    });

    
    jQuery(".elex-raq-quote-list-popup-container").hide();
    jQuery(".elex-raq-view-quote-list-open-btn").click(function(){
        jQuery(".elex-raq-quote-list-popup-container").show("slow");
    });
    jQuery(".elex-raq-view-quote-list-close-btn").click(function(){
        jQuery(".elex-raq-quote-list-popup-container").hide("slow");
    })
  });

  jQuery(window).on("load", function () {
    flag = false;
    attr = [];
  
    jQuery( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
  
      component_id = jQuery(this).closest('.composite_component').attr('data-item_id')
  
       jQuery(this).closest('.composite_component').find('.attribute_options select').each(function(){
  
        attr_val = jQuery(this).val();
         attr_name = jQuery(this).attr('id');
  
        item = {
          component_id: component_id,
          attribute_name:attr_name,
          attribute_value:attr_val,
          variation_id:variation.variation_id,
  
        }
       if( attr.length === 0){
          attr.push(item);
       }
    
       var existingObj = jQuery.grep(attr, function(obj) {
        return obj.component_id === item.component_id && obj.attribute_name === item.attribute_name ;
      });
    
        if (existingObj.length > 0) {
          // Object already exists, override some properties
          existingObj[0].attribute_value = item.attribute_value;
        } else {
          // Object does not exist, push the new object to the array
          attr.push(item);
        }
      });
      attr_length = jQuery(".attribute_options select ").length;
      if(jQuery(".attribute_options select ").length > 0 ){
        if(attr_length === attr.length){
          jQuery('.add_to_quote').removeClass('disabled');
          jQuery('.add_to_quote').css('opacity','1');
          jQuery('.add_to_quote').attr('disabled', false); 
  
        }
      } 
      localStorage.setItem('composite_products' , JSON.stringify(attr));
    });
    
    jQuery( ".single_variation_wrap" ).on( "hide_variation", function ( event, variation ) {
        jQuery('.add_to_quote').addClass('disabled');
        jQuery('.add_to_quote').css('opacity','0.5');
        jQuery('.add_to_quote').attr('disabled', true); 
  
    });
  
    jQuery('.reset_variations').click(function() {
      localStorage.setItem('composite_products' ,'');
  
      jQuery('.add_to_quote').addClass('disabled');
      jQuery('.add_to_quote').css('opacity','0.5');
      jQuery('.add_to_quote').attr('disabled', true); 
    });
  
    jQuery("table.variations select").each(function () {
      if (jQuery(this).val() == "" || jQuery(this).val() == undefined) {
        localStorage.removeItem("currently_selected_variation_id");
        localStorage.removeItem("selected_variation_attributes");
      }
    });
  
  });
  