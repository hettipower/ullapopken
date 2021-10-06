import { combineReducers } from 'redux';
import { persistReducer } from 'redux-persist';
//import storage from 'redux-persist/lib/storage';
import storageSession from 'redux-persist/lib/storage/session';

import storesReducer from './stores/stores.reducer';

const persistConfig = {
    key: 'root',
    storage : storageSession,
    whitelist : []
}

const rootReducer = combineReducers({
    stores : storesReducer,
});

export default persistReducer(persistConfig, rootReducer);