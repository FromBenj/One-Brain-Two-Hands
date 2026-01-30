import './stimulus_bootstrap.js';
import './styles/app.css';

import {fetchCoordinates} from "./js/current-location.js";

await fetchCoordinates();


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
