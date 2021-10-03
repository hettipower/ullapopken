import { compose, withProps , withStateHandlers } from "recompose";
import { 
    GoogleMap,
    withGoogleMap , 
    withScriptjs
} from "react-google-maps";

import MarkerWithInfoWindow from './makerWithInfoWindow.component';

const GoogleMapComp = compose(
    withProps({
        googleMapURL: "https://maps.googleapis.com/maps/api/js?key=AIzaSyC0_cw3dkaXJvqhZ6KHIj7_RF_Cf5Tj5p4&v=3.exp&libraries=geometry,drawing,places",
        loadingElement: <div style={{ height: `100%` }} />,
        containerElement: <div style={{ height: `600px` }} />,
        mapElement: <div style={{ height: `100%` }} />,
    }),
    withStateHandlers(() => ({
        isOpen: false,
      }), {
        onToggleOpen: ({ isOpen }) => () => ({
          isOpen: !isOpen,
        })
    }),
    withScriptjs,
    withGoogleMap
    )((props) =>
    <GoogleMap
      defaultZoom={5}
      defaultCenter={{ lat: props.centerLocation.latitude, lng: props.centerLocation.longitude }}
    >
        {
            (props.allLocations.length > 0) ?
            props.allLocations
            .map((location , idx) => 
                <MarkerWithInfoWindow
                    key={idx} 
                    position={{ lat: location.address.lat, lng: location.address.lng }}
                    {...location}
                    longitude={props.longitude}
                    latitude={props.latitude}
                />
            )
            : ''
        }
    </GoogleMap>
);

export default GoogleMapComp;