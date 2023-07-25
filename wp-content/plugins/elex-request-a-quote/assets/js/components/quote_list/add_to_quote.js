// Function that runs when product is added to quote in product page (for simple and external products)
jQuery('.add_view_quote_btn').on('click','.add_to_quote' ,function(e){
  const id = jQuery(this).attr('data-product-id');

  const btn = jQuery(this);
  const productType = jQuery(this).attr('data-product-type');
  let selected_variation_id = null;

  if('variable' === productType ){
    if (localStorage.getItem("currently_selected_variation_id") === null) {
      e.preventDefault();
      return;
    } else {
      selected_variation_id = localStorage.getItem(
        "currently_selected_variation_id"
      );
    }
  }
  let quantity;
  quantity = jQuery('input[name="quantity"]').val();

  let storeddata = localStorage.getItem('composite_products');
  let item = {
    id: id,
    quantity: quantity ? quantity:1,
    variation_id: selected_variation_id,
    composite_data:storeddata
  };
 

  if('grouped' === productType ){
  const child_ids = jQuery(this).attr('data-child-ids');
  jQuery.each(JSON.parse(child_ids ), function(key,value) {

    let quantity;
    quantity = jQuery('input[name="quantity['+value+']"]').val();

    if(quantity == 0 || quantity == "" || quantity ==undefined){
    
      quantity_check++;
      return;
    }

    var item = {
      id: value,
      quantity: quantity
        ? quantity
        : 1,
      variation_id: null
    };
    // grouped_items.push(item);
    add_to_quote(item,btn);
   
  });

  }
  else{
    add_to_quote(item,btn);
  }
})

function add_to_quote(item,btn){

   jQuery.ajax({
    type: "post",
    url: request_a_quote_ajax_obj.ajax_url,
    data: {
      action: 'elex_raq_add_to_quote',
      ajax_raq_nonce: request_a_quote_ajax_obj.nonce,
      data: item,
    },
    success: function(data) {
      const container = jQuery(data.data.html).html();

      btn.parents('.add_view_quote_btn').html(container);

      jQuery('#elex-raq-add-sucess-toast').addClass('show');
      setTimeout(function() {
      jQuery('#elex-raq-add-sucess-toast').removeClass('show');

      }, 3000);
      jQuery(window).trigger("add_to_quote_event", data.data.quote_list_data); 

    }
  })
}

  
    
  
   
  