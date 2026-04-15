// Полифиллы — оставляем как есть
import 'core-js/stable';
import 'regenerator-runtime/runtime';
// Затем загружаем зависимости, которые могут требовать jQuery
import './bootstrap';
// Popper.js — оставляем как есть (ESM)
import { createPopper } from '@popperjs/core';
window.Popper = createPopper;
// Заменяем прямые импорты минифицированных файлов на npm‑пакеты
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// 2. Затем Isotope — важно: после jQuery
import Isotope from 'isotope-layout';
// Инициализируем Isotope как jQuery‑плагин
if (typeof $.fn !== 'undefined') {
    $.fn.isotope = function(options) {
        return this.each(function() {
            new Isotope(this, options);
        });
    };
}

import imagesLoaded from 'imagesloaded';
window.imagesLoaded = imagesLoaded;

import magnificPopup from 'magnific-popup';
$.fn.magnificPopup = magnificPopup;
import './owl.carousel.min.js';
import odometer from 'odometer';
window.odometer = odometer;
import './jquery.countdown.min.js';
import './jquery.appear.js';
import Slick from 'slick-carousel';
window.Slick = Slick;
// WOW.js — оставляем импорт, инициализируем после DOM
import WOW from 'wow.js';
window.WOW = WOW;
document.addEventListener('DOMContentLoaded', () => {
    new WOW().init();
});
// AOS — оставляем как есть
import AOS from 'aos';
import 'aos/dist/aos.css';
window.AOS = AOS;
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 800,
        easing: 'ease-in-out-cubic',
        once: true,
        offset: 100
    });
});
// Кастомные плагины, которые не имеют npm‑версий
// import '../vendor/jquery.textfill.js'; // зависит от jQuery
import textfill from 'textfill';
window.textfill = textfill;
import '../vendor/table.check-vox.plugin.js'; // зависит от jQuery

// Остальные файлы проекта
import './plugins.js';
import './ajax-form.js';
import './main.js';
import './cart-handler.js';
