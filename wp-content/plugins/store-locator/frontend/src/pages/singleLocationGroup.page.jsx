import React from 'react';
import { useParams , Link } from 'react-router-dom';
import { createStructuredSelector } from 'reselect';
import { connect } from 'react-redux';

import { selectCityStores } from '../redux/stores/stores.selectors';

const SingleLocationGroup = ({ cityStores }) => {

    const { locationName } = useParams();

    return (
        <div className="allLocationsWrap singleGroupWrap">
            <div className="backToMap">
                <Link to="/">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-arrow-left-short" viewBox="0 0 16 16">
                        <path fillRule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
                    </svg>
                    Back to Map
                </Link>
            </div>
            <div className="allLocations">
                <h2>
                    <Link to="/all-locations">All Locations</Link> {`>`} {locationName}
                </h2>

                {
                    (cityStores[locationName].length > 0) ?
                        <ul className="locations">
                            {
                                cityStores[locationName]
                                .map( location => 
                                    <li key={location.ID} >
                                        <span className="title">
                                            <a href={location.link}>{location.title}</a>
                                        </span>
                                        <address>{location.address.address}</address>
                                    </li>
                                )
                            }
                        </ul>
                    : ''
                }
            </div>
        </div>
    )
};

const mapStateToProps = createStructuredSelector({
    cityStores : selectCityStores,
});

export default connect(mapStateToProps)(SingleLocationGroup);