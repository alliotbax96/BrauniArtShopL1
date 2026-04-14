import 'core-js/stable';
import 'regenerator-runtime/runtime';

import './bootstrap';

import '../vendor/jquery.textfill.js';
import { createPopper } from '@popperjs/core';
window.Popper = createPopper;

import '../vendor/bootstrap.min.js';
import './isotope.pkgd.min.js';
import './imagesloaded.pkgd.min.js';
import './jquery.magnific-popup.min.js';
import './owl.carousel.min.js';
import './jquery.odometer.min.js';
import './jquery.countdown.min.js';
import './jquery.appear.js';
import './slick.min.js';
import './ajax-form.js';

import WOW from 'wow.js';

// Экспортируем WOW глобально
window.WOW = WOW;

// Инициализируем после загрузки DOM
document.addEventListener('DOMContentLoaded', () => {
    new WOW().init();
});


import AOS from 'aos';
import 'aos/dist/aos.css'; // если нужны стили

// Инициализируем AOS после загрузки DOM
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 800,
        easing: 'ease-in-out-cubic',
        once: true, // анимация срабатывает только один раз
        offset: 100 // смещение от края экрана
    });
});

// Экспортируем AOS глобально (если нужно использовать в других файлах)
window.AOS = AOS;

import './plugins.js';
import '../vendor/jquery.maskedinput.min.js';
import '../vendor/table.check-vox.plugin.js';
// import '../vendor/mediaelement/mediaelement-and-player.min.js';
// import '../vendor/mediaelement/mep.js';
import './main.js';
import './cart-handler.js';
