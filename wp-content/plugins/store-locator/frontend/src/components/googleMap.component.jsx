import { compose, withProps , withHandlers } from "recompose";
import { 
    GoogleMap,
    withGoogleMap , 
    withScriptjs
} from "react-google-maps";
import { MarkerClusterer } from 'react-google-maps/lib/components/addons/MarkerClusterer';

import MarkerWithInfoWindow from './makerWithInfoWindow.component';

import MarkerClustererImg from '../assets/images/MarkerClusterer.png';

const GoogleMapComp = compose(
    withProps({
        googleMapURL: "https://maps.googleapis.com/maps/api/js?key=AIzaSyC0_cw3dkaXJvqhZ6KHIj7_RF_Cf5Tj5p4&v=3.exp&libraries=geometry,drawing,places",
        loadingElement: <div style={{ height: `100%` }} />,
        containerElement: <div style={{ height: `600px` }} />,
        mapElement: <div style={{ height: `100%` }} />,
    }),
    withHandlers({
        onMarkerClustererClick: () => (markerClusterer) => {
            // eslint-disable-next-line
            const clickedMarkers = markerClusterer.getMarkers();
        },
    }),
    withScriptjs,
    withGoogleMap
    )((props) =>
    <GoogleMap
      defaultZoom={5}
      defaultCenter={{ lat: props.centerLocation.latitude, lng: props.centerLocation.longitude }}
    >
        <MarkerClusterer
            onClick={props.onMarkerClustererClick}
            averageCenter
            enableRetinaIcons
            gridSize={60}
            styles={[
                {
                    url: MarkerClustererImg,
                    height: 40,
                    width: 40,
                    textColor:"#FFF",
                }
            ]}
        >   
            {
                (props.allLocations.length > 0) ?
                props.allLocations
                .map(location => 
                    <MarkerWithInfoWindow
                        key={location.ID} 
                        position={{ lat: location.address.lat, lng: location.address.lng }}
                        {...location}
                        longitude={props.longitude}
                        latitude={props.latitude}
                    />
                )
                : ''
            }
        </MarkerClusterer>
    </GoogleMap>
);

export default GoogleMapComp;