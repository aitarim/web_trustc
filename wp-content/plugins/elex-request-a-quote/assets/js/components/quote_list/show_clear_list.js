
import {ClearList} from './clear_list';

export const HideOrShowClearList = (props) => {
    if(props.data.clear_list === true){
        return(
            <button  onClick={ClearList} className="clear_list_btn btn btn-sm btn-outline-primary px-4">Clear List</button>
        )
    }
    return '';
    
    }


