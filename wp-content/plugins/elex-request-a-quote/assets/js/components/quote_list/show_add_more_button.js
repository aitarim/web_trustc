export const HideOrShowAddMoreButton = (props) => {

    if(props.data.add_more_items_button === true){
        return(
            <a href={props.data.add_more_items_button_redirection}><button className="add_more_items_btn btn btn-sm btn-primary px-4">{props.data.add_more_items_button_label}</button></a>
        )
    }
    return '';
    
}