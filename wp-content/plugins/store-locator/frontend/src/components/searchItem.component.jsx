import React from 'react';
import { getPreciseDistance } from 'geolib';

const SearchItem = ({ title , address , image , telephone , longitude , latitude , link }) => {

    const currentLocation = ( longitude && latitude ) ? { latitude: latitude, longitude: longitude } : false;

    var distance = getPreciseDistance(currentLocation , { latitude: address.lat, longitude: address.lng });

    const numberWithCommas = (x) => {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    return (
        <div className="searchItem">
            <a href={link}>{title}</a>
            <div className="titleWrap">
                <div className="title">
                    <h3>{title}</h3>
                    <address>{address.address}</address>
                </div>
                <div className="img">
                    {
                        (image) ? 
                            <img src={image} alt="" />
                        : ''
                    }
                </div>
            </div>
            <div className="details">
                {
                    (distance) ?
                    <div className="distance">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16"><path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/><path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg> {numberWithCommas(distance/1000)}  km <span>from your current location</span>
                    </div>
                    : ''
                }
                
            </div>
            
        </div>
    )
}

export default SearchItem;