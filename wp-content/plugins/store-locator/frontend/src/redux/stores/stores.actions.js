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
    type : storesActionTypes.SET_FIXTUREs,
    payload : fixtures
});