import React from 'react';
import { getCenterOfBounds } from 'geolib';
import { createStructuredSelector } from 'reselect';
import { connect } from 'react-redux';

import GeoSearch from '../components/search.components';
import SearchResults from '../components/searchResults.component';
import GoogleMapComp from '../components/googleMap.component';

import { 
    selectStores , 
    selectFixtures , 
    selectLatitude , 
    selectLongitude 
} from '../redux/stores/stores.selectors';

import { setActivePage } from '../redux/stores/stores.actions';

class MapPage extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            allLocations: [],
            showClear : false,
        };

        this.onSuggestChange = this.onSuggestChange.bind(this);
        this.onSuggestSelect = this.onSuggestSelect.bind(this);
        this.onHideClear = this.onHideClear.bind(this);
    }

    componentDidMount() {

        const { stores } = this.props;

        if( stores.length > 0 ) {

            var allLocations = [];
            
            stores
            .map( store => {

                const location = {
                    latitude: store.address.lat, 
                    longitude: store.address.lng
                }
      
                allLocations.push(location);
      
                return false;

            });

            this.setState({
                allLocations : allLocations
            })
        }

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

        const { allLocations , showClear } = this.state;

        const { fixtures , stores , longitude , latitude , setActivePage } = this.props;

        return(
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

                <div className="showAll">
                  <span
                    onClick={() => setActivePage('allLocation')}
                  >Show all locations</span>
                </div>
            
            </div>
        )
    }
}

const mapDispatchToProps = dispatch => ({
  setActivePage : (activePage) => dispatch(setActivePage(activePage)),
});

const mapStateToProps = createStructuredSelector({
    stores : selectStores,
    fixtures : selectFixtures , 
    latitude : selectLatitude,
    longitude : selectLongitude , 
});

export default connect(mapStateToProps , mapDispatchToProps)(MapPage);