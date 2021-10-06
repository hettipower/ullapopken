import React from 'react';
import { createStructuredSelector } from 'reselect';
import { connect } from 'react-redux';

import { selectCityStores } from '../redux/stores/stores.selectors';

import { setActivePage , setActiveLocation } from '../redux/stores/stores.actions';

class AllLocationPage extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
        };
    
    }

    componentDidMount() {
        window.scrollTo(0, 0);
    }

    render(){

        const { cityStores , setActivePage , setActiveLocation } = this.props;

        return(
            <div className="container">
                <div className="allLocationsWrap">
                    <div className="backToMap">
                        <span 
                            onClick={() => setActivePage('map')}
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                <path fillRule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
                            </svg>
                            Back to Map
                        </span>
                    </div>

                    <div className="allLocations">
                        <h2>All Locations</h2>
                        <ul className="locations">
                            {
                                Object.keys(cityStores)
                                .map( cityName => 
                                    <li>
                                        <span
                                            onClick={() => {
                                                setActivePage('singleLocation');
                                                setActiveLocation(`${cityName}`);
                                            }}
                                        >{cityName}</span>
                                        ({cityStores[cityName].length})
                                    </li>
                                )
                            }
                        </ul>
                    </div>
                </div>
            </div>
        )
    }
}

const mapDispatchToProps = dispatch => ({
    setActivePage : (activePage) => dispatch(setActivePage(activePage)),
    setActiveLocation : (activeLocation) => dispatch(setActiveLocation(activeLocation)),
});

const mapStateToProps = createStructuredSelector({
    cityStores : selectCityStores,
});

export default connect(mapStateToProps , mapDispatchToProps)(AllLocationPage)