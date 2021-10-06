import React from 'react';
import axios from "axios";
import { connect } from 'react-redux';
import { createStructuredSelector } from 'reselect';

import { 
  setStores , 
  setCityStores , 
  setLatitude , 
  setLongitude , 
  setFixtures 
} from './redux/stores/stores.actions';

import { selectActivePage } from './redux/stores/stores.selectors';

import './App.css';
import './styles/storefinder.scss';

import AllLocationPage from './pages/allLocation.page';
import MapPage from './pages/map.page';
import SingleLocationGroup from './pages/singleLocationGroup.page';

class App extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
    };
  }

  componentDidMount(){

    let thisEle = this;

    const { 
      setStores , 
      setCityStores , 
      setLatitude , 
      setLongitude , 
      setFixtures 
    } = thisEle.props;

    if (navigator.geolocation) {
      navigator.geolocation.watchPosition(function(position) {
        setLongitude(position.coords.longitude);
        setLatitude(position.coords.latitude);
      });
    }

    let STORES_API_URL = 'http://localhost/ullapopken/wp-json/ullapopken/v1/stores';
    let CITY_API_URL = 'http://localhost/ullapopken/wp-json/ullapopken/v1/stores-cities';

    if( process.env.NODE_ENV === 'production' ) {
      STORES_API_URL = `${window.CUSTOM_PARAMS.base_url}/wp-json/ullapopken/v1/stores`;
      CITY_API_URL = `${window.CUSTOM_PARAMS.base_url}/wp-json/ullapopken/v1/stores-cities`;
    }

    axios.get(STORES_API_URL)
    .then(function (response) {
      if( response.data.length > 0 ){
        
        var fixtures = [];

        response.data.map( 
          store => {

            const item = {
              label : store.title+' - '+store.address.address,
              location : {
                lat: store.address.lat,
                lng: store.address.lng
              }
            }

            fixtures.push(item);

            return false;
          }
        );

        setStores(response.data);
        setFixtures(fixtures);

      }

    });

    axios.get(CITY_API_URL)
    .then(function (response) {
      setCityStores(response.data);
    });

    window.scrollTo(0, 0);

  }

  render(){

    const { activePage } = this.props;

    if (activePage === 'map') {
      return ( <MapPage /> );
    } else if (activePage === 'allLocation') {
      return ( <AllLocationPage /> );
    } else if (activePage === 'singleLocation') {
      return ( <SingleLocationGroup /> );
    }

  }


}

const mapDispatchToProps = dispatch => ({
  setStores : (stores) => dispatch(setStores(stores)),    
  setCityStores : (cityStores) => dispatch(setCityStores(cityStores)),
  setLatitude : (longitude) => dispatch(setLatitude(longitude)),
  setLongitude : (latitude) => dispatch(setLongitude(latitude)),
  setFixtures : (fixtures) => dispatch(setFixtures(fixtures)),
});

const mapStateToProps = createStructuredSelector({
  activePage : selectActivePage,
});

export default connect(mapStateToProps, mapDispatchToProps)(App);
