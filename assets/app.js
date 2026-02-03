import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.css';
import './app.scss';

import './stimulus_bootstrap.js';
import 'bootstrap';
import 'gsap';

import {enterPositionPage} from "./js/current-position.js";
import {homeElementsAppear} from "./js/home.js";
document.addEventListener('DOMContentLoaded', async () => {
    enterPositionPage();
    homeElementsAppear();
});



console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
