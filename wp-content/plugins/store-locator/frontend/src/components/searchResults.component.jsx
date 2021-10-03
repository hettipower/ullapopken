import React from 'react';

import SearchItem from './searchItem.component';

const SearchResults = ({stores , longitude , latitude}) => {

    return(
        <div className="searchResultsWrap">
            
            {
                (stores.length > 0) ?
                stores
                .map( store => 
                    <SearchItem 
                        key={store.ID} 
                        {...store} 
                        longitude={longitude} 
                        latitude={latitude}
                    /> 
                )
                : ''
            }

        </div>
    )
}

export default SearchResults;