// Полифиллы — оставляем как есть
import 'core-js/stable';
import 'regenerator-runtime/runtime';
// Затем загружаем зависимости, которые могут требовать jQuery
import './bootstrap';
// Заменяем прямые импорты минифицированных файлов на npm‑пакеты
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
// Popper.js — оставляем как есть (ESM)
import { createPopper } from '@popperjs/core';
window.Popper = createPopper;

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

// import magnificPopup from 'magnific-popup';
// $.fn.magnificPopup = magnificPopup;
import 'magnific-popup';
import './owl.carousel.min.js';
import odometer from 'odometer';
window.odometer = odometer;
import './jquery.countdown.min.js';
import './jquery.appear.js';

// import Slick from 'slick-carousel';
// window.Slick = Slick;
import 'slick-carousel/slick/slick.js';

// WOW.js — оставляем импорт, инициализируем после DOM
import WOW from 'wow.js';
window.WOW = WOW;
// AOS — оставляем как есть
import AOS from 'aos';
import 'aos/dist/aos.css';
window.AOS = AOS;

// Кастомные плагины, которые не имеют npm‑версий
// import '../vendor/jquery.textfill.js'; // зависит от jQuery
import textfill from 'textfill';
window.textfill = textfill;
import '../vendor/table.check-vox.plugin.js'; // зависит от jQuery

import feather from 'feather-icons';
window.feather = feather;
document.addEventListener('DOMContentLoaded', () => {
    feather.replace();
});

// Остальные файлы проекта
import './plugins.js';
import './ajax-form.js';
import './main.js';
import './cart-handler.js';
