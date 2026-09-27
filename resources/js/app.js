import { initHeader } from './modules/header.js';
import { initDropdowns } from './modules/nav-dropdown.js';
import { initOffcanvas } from './modules/offcanvas.js';
import { initSliders } from './modules/slider.js';
import { initParallax } from './modules/parallax.js';
import { initContactForm } from './modules/contact-form.js';

document.documentElement.classList.add('js');

initHeader();
initDropdowns();
initOffcanvas();
initSliders();
initParallax();
initContactForm();
