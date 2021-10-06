import { storesActionTypes } from './stores.types';


export const setStores = stores => ({
    type : storesActionTypes.SET_STORES,
    payload : stores
});

export const setCityStores = cityStores => ({
    type : storesActionTypes.SET_CITY_STORES,
    payload : cityStores
});

export const setLongitude = longitude => ({
    type : storesActionTypes.SET_LONGITUDE,
    payload : longitude
});

export const setLatitude = latitude => ({
    type : storesActionTypes.SET_LATITUDE,
    payload : latitude
});

export const setFixtures = fixtures => ({
    type : storesActionTypes.SET_FIXTURES,
    payload : fixtures
});

export const setActivePage = activePage => ({
    type : storesActionTypes.SET_ACTIVE_PAGE,
    payload : activePage
});

export const setActiveLocation = activeLocation => ({
    type : storesActionTypes.SET_ACTIVE_LOCATION,
    payload : activeLocation
});