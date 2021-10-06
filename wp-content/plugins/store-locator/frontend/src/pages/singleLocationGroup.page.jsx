import React , { useEffect } from 'react';
import { createStructuredSelector } from 'reselect';
import { connect } from 'react-redux';

import { selectCityStores , selectActiveLocation } from '../redux/stores/stores.selectors';

import { setActivePage } from '../redux/stores/stores.actions';

const SingleLocationGroup = ({ cityStores , setActivePage , activeLocation }) => {

    useEffect(() => {
        window.scrollTo(0, 0)
    }, []);

    return (
        <div className="container">
            <div className="allLocationsWrap singleGroupWrap">
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
                    <h2>
                        <span 
                            onClick={() => setActivePage('allLocation')}
                        >All Locations</span> {`>`} {activeLocation}
                    </h2>

                    {
                        (cityStores[activeLocation].length > 0) ?
                            <ul className="locations">
                                {
                                    cityStores[activeLocation]
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
        </div>
    )
};

const mapDispatchToProps = dispatch => ({
    setActivePage : (activePage) => dispatch(setActivePage(activePage)),
});

const mapStateToProps = createStructuredSelector({
    cityStores : selectCityStores,
    activeLocation : selectActiveLocation
});

export default connect(mapStateToProps , mapDispatchToProps)(SingleLocationGroup);