import { storesActionTypes } from './stores.types';

const INITIAL_STATE = {
    stores : [],
    cityStores : [],
    longitude: false,
    latitude : false,
    fixtures : [],
}

const storesReducer = ( state = INITIAL_STATE , action ) => {
    switch (action.type) {
        case storesActionTypes.SET_STORES:
            return{
                ...state , 
                stores : action.payload
            }
        case storesActionTypes.SET_CITY_STORES:
            return{
                ...state,
                cityStores : action.payload
            }
        case storesActionTypes.SET_LONGITUDE:
            return{
                ...state,
                longitude : action.payload
            }
        case storesActionTypes.SET_LATITUDE:
            return{
                ...state,
                latitude : action.payload
            }
        case storesActionTypes.SET_FIXTUREs:
            return{
                ...state,
                fixtures : action.payload
            }
        default:
            return state;
    }
}

export default storesReducer;