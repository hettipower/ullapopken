import React from 'react';
import { Route } from 'react-router-dom';

import AllLocationPage from './pages/allLocation.page';
import MapPage from './pages/map.page';
import SingleLocationGroup from './pages/singleLocationGroup.page';

const routes = (
    <React.Fragment>
        <Route exact path="/" component={ MapPage } />
        <Route exact path="/all-locations" component={ AllLocationPage } />
        <Route path="/location/:locationName" component={ SingleLocationGroup } />
    </React.Fragment>
);

export default routes;