import React from 'react';
import { Link } from 'react-router-dom';
import { createStructuredSelector } from 'reselect';
import { connect } from 'react-redux';

import { selectCityStores } from '../redux/stores/stores.selectors';

class AllLocationPage extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
        };
    
    }

    render(){

        const { cityStores } = this.props;

        return(
            <div className="allLocationsWrap">
                <div className="backToMap">
                    <Link to="/">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-arrow-left-short" viewBox="0 0 16 16">
                            <path fillRule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
                        </svg>
                        Back to Map
                    </Link>
                </div>

                <div className="allLocations">
                    <h2>All Locations</h2>
                    <ul className="locations">
                        {
                            Object.keys(cityStores)
                            .map( cityName => 
                                <li>
                                    <Link to={`/location/${cityName}`}>{cityName}</Link>
                                    ({cityStores[cityName].length})
                                </li>
                            )
                        }
                    </ul>
                </div>
            </div>
        )
    }
}

const mapStateToProps = createStructuredSelector({
    cityStores : selectCityStores,
});

export default connect(mapStateToProps)(AllLocationPage)