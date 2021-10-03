import React from 'react';
import axios from "axios";
import { getCenterOfBounds } from 'geolib';

import GeoSearch from './components/search.components';
import SearchResults from './components/searchResults.component';
import GoogleMapComp from './components/googleMap.component';

import './App.css';
import './styles/storefinder.scss';

class App extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
      longitude: false,
      latitude : false,
      stores: false,
      fixtures : [],
      allLocations: [],
      showClear : false,
      allStores: false,
      allFixtures : [],
    };

    this.onSuggestChange = this.onSuggestChange.bind(this);
    this.onSuggestSelect = this.onSuggestSelect.bind(this);
    this.onHideClear = this.onHideClear.bind(this);
  }

  componentDidMount(){

    let thisEle = this;

    if (navigator.geolocation) {
      navigator.geolocation.watchPosition(function(position) {
        thisEle.setState({
          longitude : position.coords.longitude,
          latitude : position.coords.latitude
        });
      });
    }

    //axios.get(`${window.CUSTOM_PARAMS.base_url}/wp-json/ullapopken/v1/stores`)
    axios.get(`http://localhost/ullapopken/wp-json/ullapopken/v1/stores`)
    .then(function (response) {
      if( response.data.length > 0 ){
        
        var fixtures = [];
        var allLocations = [];

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

            const location = {
              latitude: store.address.lat, 
              longitude: store.address.lng
            }

            allLocations.push(location);

            return false;
          }
        );

        thisEle.setState({
          fixtures : fixtures,
          stores : response.data,
          allLocations : allLocations,
          allStores : response.data,
          allFixtures : fixtures,
        });

      }

    });

  }

  onSuggestSelect(suggest){

    const { stores , allLocations } = this.state;
    let result = [];
    var valArr = suggest.label.toLowerCase().split(" - ");
    var value = valArr[0];
    var newLocations = [];

    //Stores Search
    if( stores.length > 0 ) {
      result = stores.filter((data) => {
        return data.title.toLowerCase().indexOf(value) !== -1;
      });
    }

    if( result.length > 0 ){
      result
      .map((store) => {
        const location = {
          latitude: store.address.lat, 
          longitude: store.address.lng
        }

        newLocations.push(location);

        return false;
      });
    }

    this.setState({
      stores : result,
      allLocations : (newLocations.length > 0) ? newLocations : allLocations,
      showClear: true
    });
  }

  onSuggestChange(text) {

    const { stores , fixtures , allLocations } = this.state;
    let result = [];
    let fixturesResults = [];
    var newLocations = [];
    var value = text.toLowerCase();

    //Stores Search
    if( stores.length > 0 ) {
      result = stores.filter((data) => {
        return data.title.toLowerCase().indexOf(value) !== -1;
      });
    }

    //Fixtures Search
    if( fixtures.length > 0 ) {
      fixturesResults = fixtures.filter((data) => {
        return data.label.toLowerCase().indexOf(value) !== -1;
      });
    }

    if( result.length > 0 ){
      result
      .map((store) => {
        const location = {
          latitude: store.address.lat, 
          longitude: store.address.lng
        }

        newLocations.push(location);

        return false;
      });
    }

    this.setState({
      stores : result,
      fixtures : fixturesResults,
      allLocations : (newLocations.length > 0) ? newLocations : allLocations,
      showClear: true
    });
    
  }

  onHideClear(){

    const { allStores } = this.state;

    this.setState({
      showClear: false,
      stores : allStores
    });
  }

  render(){

    const { fixtures , stores , longitude , latitude , allLocations , showClear } = this.state;

    return (
      <div id="storeFinderWrapper" className="container">
    
        <div className="maplistWrap">
          <GeoSearch
            onSuggestSelect={this.onSuggestSelect} 
            fixtures={fixtures}
            onSuggestChange={this.onSuggestChange}
            showClear={showClear}
            onHideClear={this.onHideClear}
          />
          <SearchResults
            stores={stores} 
            longitude={longitude}
            latitude={latitude}
          />
  
        </div>
        <div className="mapWrapper">
          <GoogleMapComp 
            centerLocation={(allLocations.length) ? getCenterOfBounds(allLocations) : { latitude: 49.0154, longitude: 15.6446 } }
            allLocations={stores}
            longitude={longitude}
            latitude={latitude}
          />
        </div>
    
      </div>
    );
  }


}

export default App;
