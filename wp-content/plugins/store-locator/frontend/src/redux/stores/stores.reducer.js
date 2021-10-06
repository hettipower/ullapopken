import { storesActionTypes } from './stores.types';

const INITIAL_STATE = {
    stores : [],
    cityStores : [],
    longitude: false,
    latitude : false,
    fixtures : [],
    activePage : 'map',
    activeLocation : false
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
        case storesActionTypes.SET_FIXTURES:
            return{
                ...state,
                fixtures : action.payload
            }
        case storesActionTypes.SET_ACTIVE_PAGE:
            return{
                ...state,
                activePage : action.payload
            }
        case storesActionTypes.SET_ACTIVE_LOCATION:
            return{
                ...state,
                activeLocation : action.payload
            }
        default:
            return state;
    }
}

export default storesReducer;