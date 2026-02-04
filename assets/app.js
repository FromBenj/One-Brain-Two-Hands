import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.css';
import'leaflet.sidepanel/dist/leaflet.sidepanel.min.css';

import './app.scss';

import './stimulus_bootstrap.js';
import 'bootstrap';
import 'gsap';
import L from 'leaflet';
import 'leaflet.sidepanel';

import {enterPositionPage} from "./js/current-position.js";
import {homeElementsAppear} from "./js/home.js";
import {centerMapView, mapLoader, mapSidePanel} from "./js/map.js";
document.addEventListener('DOMContentLoaded', async () => {
    enterPositionPage();
    homeElementsAppear();
});

document.addEventListener('ux:map:connect', (event) => {
    centerMapView(event);
    mapLoader(event);
    mapSidePanel(event);
});



console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
