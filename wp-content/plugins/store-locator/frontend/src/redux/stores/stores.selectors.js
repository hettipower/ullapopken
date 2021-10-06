import { createSelector } from 'reselect';

const selectStoresAPI = state => state.stores;


export const selectStores = createSelector(
    [selectStoresAPI],
    (stores) => stores.stores
);

export const selectCityStores = createSelector(
    [selectStoresAPI],
    (stores) => stores.cityStores
);

export const selectLongitude = createSelector(
    [selectStoresAPI],
    (stores) => stores.longitude
);

export const selectLatitude = createSelector(
    [selectStoresAPI],
    (stores) => stores.latitude
);

export const selectFixtures = createSelector(
    [selectStoresAPI],
    (stores) => stores.fixtures
);