const { __ } = wp.i18n;

export const IsQuoteEmpty = (props) => {

    if(props.data.items.length === 0) {
        jQuery('.quote_list_page').addClass('d-none');
        return (
            <div class="text-center w-100 elex-raq-empty-list my-5">
                <EmptyImageUrl image_url = {props.empty_image_url} illustration={props.settings.illustration} />
                <QuoteEmptyText empty_quote_text={props.settings.empty_text} />
                <GoToShopPage shop_page ={props.shop_page} is_enabled ={props.settings.go_to_shop_page_button} />
            </div>)

    }
    jQuery('.quote_list_page').removeClass('d-none');
    return null;
}
export const GoToShopPage = (props) => {
  
        return(
            <a href={props.shop_page} ><button className="btn btn-sm btn-primary">{__('Go to Shop Page','elex-request-a-quote')}</button></a>
        );
   
}

export const QuoteEmptyText = (props) => {
        return (  <h6>{props.empty_quote_text.text}</h6>);
    
}

export const EmptyImageUrl = (props) => {
 
   
        return (
            <img src={props.image_url[0]} alt="" class="mb-3"/>

        );
    
}