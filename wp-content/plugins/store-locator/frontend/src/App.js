import React from 'react';
import axios from "axios";
import { connect } from 'react-redux';

import { 
  setStores , 
  setCityStores , 
  setLatitude , 
  setLongitude , 
  setFixtures 
} from './redux/stores/stores.actions';

import './App.css';
import './styles/storefinder.scss';

import routes from './routes';

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

  }

  render(){

    return (
      <div>
        {routes}
      </div>
    );
  }


}

const mapDispatchToProps = dispatch => ({
  setStores : (stores) => dispatch(setStores(stores)),    
  setCityStores : (cityStores) => dispatch(setCityStores(cityStores)),
  setLatitude : (longitude) => dispatch(setLatitude(longitude)),
  setLongitude : (latitude) => dispatch(setLongitude(latitude)),
  setFixtures : (fixtures) => dispatch(setFixtures(fixtures)),
});

export default connect(null, mapDispatchToProps)(App);
