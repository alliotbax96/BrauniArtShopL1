(function() {
    "use strict";
    try {
        if (typeof document != "undefined") {
            var t = document.createElement("style");
            t.appendChild(document.createTextNode(`.widget__input--search:not(:focus),.ydw-suggest-inactive{background-color:#f5f4f2!important;border:none!important}.ymaps-2-1-79-search__suggest,widget__popup{position:absolute!important;z-index:2!important;background:var(--color-white)!important;border-radius:var(--border-radius)!important;padding:0 16px!important;height:227px!important;overflow:auto!important}.widget__popup--search{top:100%!important;height:262px!important}.widget__popup--search .widget__popup-item{padding:14px 0!important}.widget__popup::-webkit-scrollbar{width:0!important}.ymaps-2-1-79-search__suggest,.widget__popup-list{list-style-type:none!important;padding:0!important;margin:0!important}.ymaps-2-1-79-suggest-item,.widget__popup-item{display:flex!important;align-items:center!important;justify-content:space-between!important;font-feature-settings:"pnum" on,"lnum" on!important}.ymaps-2-1-79-suggest-item:not(:last-child),.widget__popup-item:not(:last-child){border-bottom:.5px solid var(--color-line)!important;margin-left:15px;margin-right:15px;padding-left:0!important;padding-right:0!important}.ymaps-2-1-79-search__suggest-item,.widget__popup-title{margin:0!important;font-size:16px!important;line-height:17px!important;color:var(--color-text-main)!important}.ymaps-2-1-79-search__suggest-item{width:100%;padding-top:21px!important;padding-bottom:21px!important}.widget__popup-city{font-size:13px!important;line-height:15px!important;color:var(--color-text-minor)!important}.widget__popup-arrow{padding:0!important;background:none!important;border:none!important;cursor:pointer!important}.ymaps-2-1-79-i-custom-scroll ::-webkit-scrollbar,.ymaps-2-1-79-i-custom-scroll::-webkit-scrollbar{width:0!important;height:0!important}.ymaps-2-1-79-search__suggest-highlight{font-weight:400!important}.ymaps-2-1-79-search__suggest-item{padding-left:0!important;padding-right:0!important}.ymaps-2-1-79-search__suggest-item:hover{background-color:#fff}@media screen and (max-width: 750px){.ymaps-2-1-79-search__suggest{border-radius:0!important;border:none!important;box-shadow:none!important}.ymaps-2-1-79-search__suggest.ymaps-2-1-79-popup.ymaps-2-1-79-i-custom-scroll{box-shadow:none!important}.widget__label.ydw-typing-in-mobile>ymaps{display:block!important}}.ydw-widget__header-mobile-bad-keyboard{padding-bottom:150px!important}.ymaps_maps-button-icon_plus{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M12 3C11.7348 3 11.4804 3.10536 11.2929 3.29289C11.1054 3.48043 11 3.73478 11 4V11H4C3.73478 11 3.48043 11.1054 3.29289 11.2929C3.10536 11.4804 3 11.7348 3 12C3 12.2652 3.10536 12.5196 3.29289 12.7071C3.48043 12.8946 3.73478 13 4 13H11V20C11 20.2652 11.1054 20.5196 11.2929 20.7071C11.4804 20.8946 11.7348 21 12 21C12.2652 21 12.5196 20.8946 12.7071 20.7071C12.8946 20.5196 13 20.2652 13 20V13H20C20.2652 13 20.5196 12.8946 20.7071 12.7071C20.8946 12.5196 21 12.2652 21 12C21 11.7348 20.8946 11.4804 20.7071 11.2929C20.5196 11.1054 20.2652 11 20 11H13V4C13 3.73478 12.8946 3.48043 12.7071 3.29289C12.5196 3.10536 12.2652 3 12 3Z' fill='%2321201F'/%3E%3C/svg%3E")!important}#map>ymaps{border-radius:24px;overflow:hidden}.widget__map-zoom .widget__map-button:first-child{border-bottom-left-radius:0;border-bottom-right-radius:0}.widget__map-zoom .widget__map-button:last-child{border-top-left-radius:0;border-top-right-radius:0}.ymaps_maps-zoom__plus{width:40px!important;height:40px!important;display:flex!important;align-items:center!important;justify-content:center!important;background-color:var(--color-white)!important;border-radius:13px 13px 0 0!important;box-shadow:0 2px 5px #0000001a!important;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M12 3C11.7348 3 11.4804 3.10536 11.2929 3.29289C11.1054 3.48043 11 3.73478 11 4V11H4C3.73478 11 3.48043 11.1054 3.29289 11.2929C3.10536 11.4804 3 11.7348 3 12C3 12.2652 3.10536 12.5196 3.29289 12.7071C3.48043 12.8946 3.73478 13 4 13H11V20C11 20.2652 11.1054 20.5196 11.2929 20.7071C11.4804 20.8946 11.7348 21 12 21C12.2652 21 12.5196 20.8946 12.7071 20.7071C12.8946 20.5196 13 20.2652 13 20V13H20C20.2652 13 20.5196 12.8946 20.7071 12.7071C20.8946 12.5196 21 12.2652 21 12C21 11.7348 20.8946 11.4804 20.7071 11.2929C20.5196 11.1054 20.2652 11 20 11H13V4C13 3.73478 12.8946 3.48043 12.7071 3.29289C12.5196 3.10536 12.2652 3 12 3Z' fill='%2321201F'/%3E%3C/svg%3E")!important}.ymaps_maps-zoom__minus{width:40px!important;height:40px!important;display:flex!important;align-items:center!important;justify-content:center!important;background-color:var(--color-white)!important;border-radius:0 0 13px 13px!important;box-shadow:0 2px 5px #0000001a!important;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'%3E%3Crect x='3' y='11' width='18' height='2' rx='1' fill='%2321201F'/%3E%3C/svg%3E")!important}.widget__map-button{padding:0!important;width:40px!important;height:40px!important;display:flex!important;align-items:center!important;justify-content:center!important;background-color:var(--color-white)!important;border-radius:13px!important;box-shadow:0 2px 5px #0000001a!important;border:none!important;cursor:pointer!important}.widget__map-location{position:absolute;right:12px;top:calc(50% + 68px);transform:translateY(-50%)}.widget__map-zoom{position:absolute;right:12px;top:50%;transform:translateY(-50%)}.widget__map-close{position:absolute;top:16px;right:16px}.widget__filters{display:none;min-width:400px;max-width:400px;padding-right:8px;height:100%;position:absolute;left:-100%}.widget__filters.active{left:8px}.widget__filters-wrapper{background-color:var(--color-white);box-shadow:0 8px 20px #0000001f;border-radius:var(--border-radius);height:100%}.widget__filters-title{display:flex;align-items:center;padding:14.5px 16px}.widget__filters-arrow{display:flex;background:none;border:none;padding:0;cursor:pointer}.widget__filters-title-text{padding-left:8px;margin:0;font-weight:500;font-size:24px;line-height:27px;letter-spacing:-.01em;font-feature-settings:"pnum" on,"lnum" on;color:var(--color-text-main)}.widget__filters-content{padding:0 16px 23px}.widget__filters-item-title{margin:0;font-weight:500;font-size:20px;line-height:23px;letter-spacing:-.01em;font-feature-settings:"pnum" on,"lnum" on;color:var(--color-text-main);padding:16px 0 8px}.widget__filters-radio{display:flex}.widget__filters-radio:not(:last-child){border-bottom:1px solid rgba(210,208,204,.5)}.widget__filters-radio-title{margin:0;width:100%;font-size:16px;line-height:17px;font-feature-settings:"pnum" on,"lnum" on;color:var(--color-text-main);padding:15.5px 0}.widget__filters-radio .widget__filters-input{display:none;position:absolute}.widget__filters-radio .widget__filters-label{display:block;cursor:pointer;position:relative;user-select:none}.widget__filters-radio .widget__filters-label:before{content:"";display:flex;align-items:center;justify-content:center;width:24px;height:24px;position:absolute;right:0;bottom:50%;transform:translateY(50%);border-radius:6px;background-color:var(--color-control-minor);box-shadow:inset 0 2px 3px #0000000d}.widget__filters-radio .widget__filters-input:checked+.widget__filters-label:before{content:url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M8.47087 12.6505L15.3175 5.80385C15.7081 5.41332 16.3412 5.41332 16.7318 5.80385L16.7926 5.86467C17.1831 6.25519 17.1831 6.88835 16.7926 7.27888L8.47087 15.6006L4.04011 11.1698C3.64959 10.7793 3.64959 10.1461 4.04011 9.75561L4.10093 9.69479C4.49146 9.30426 5.12462 9.30426 5.51515 9.69479L8.47087 12.6505Z' fill='%2321201F'/%3E%3C/svg%3E");background-color:var(--color-yellow);box-shadow:none}.widget__filters-buttons{padding:8px;display:grid;grid-template-columns:1fr 1fr;column-gap:4px}.widget__filters-button{padding:0;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;color:var(--color-text-main);font-feature-settings:"pnum" on,"lnum" on}.widget__filters-button--show{background-color:var(--color-yellow);font-weight:500;font-size:16px;line-height:17px;letter-spacing:-.005em}.widget__filters-button--cancel{background-color:var(--color-control-minor);font-size:16px;line-height:17px}@font-face{font-family:YS Text;src:url(https://widget-pvz.dostavka.yandex.net/assets/fonts/YandexSansText-Regular.eot);src:url(https://widget-pvz.dostavka.yandex.net/assets/fonts/YandexSansText-Regular.eot?#iefix) format("embedded-opentype"),url(https://widget-pvz.dostavka.yandex.net/assets/fonts/YandexSansText-Regular.woff2) format("woff2"),url(https://widget-pvz.dostavka.yandex.net/assets/fonts/YandexSansText-Regular.woff) format("woff"),url(https://widget-pvz.dostavka.yandex.net/assets/fonts/YandexSansText-Regular.ttf) format("truetype");font-weight:400;font-style:normal;font-display:swap}@font-face{font-family:YS Text;src:url(https://widget-pvz.dostavka.yandex.net/assets/fonts/YandexSansText-Medium.eot);src:url(https://widget-pvz.dostavka.yandex.net/assets/fonts/YandexSansText-Medium.eot?#iefix) format("embedded-opentype"),url(https://widget-pvz.dostavka.yandex.net/assets/fonts/YandexSansText-Medium.woff2) format("woff2"),url(https://widget-pvz.dostavka.yandex.net/assets/fonts/YandexSansText-Medium.woff) format("woff"),url(https://widget-pvz.dostavka.yandex.net/assets/fonts/YandexSansText-Medium.ttf) format("truetype");font-weight:500;font-style:normal;font-display:swap}.widget__header{background-color:var(--color-white);border-radius:var(--border-radius);padding:15px 16px 16px}.widget__title{display:flex;align-items:center;color:var(--color-text-main);padding-bottom:15.15px}.widget__logo{padding-right:4px;width: 27px; height: 27px;}.widget__title-text{margin:0;font-weight:500;font-size:24px;line-height:27px;letter-spacing:-.01em;font-feature-settings:"pnum" on,"lnum" on}.widget__form-item{display:flex;align-items:flex-end;position:relative}.widget__form-item:not(:last-child){padding-bottom:12px}.widget__form-item.loader .widget__label-button--loader{display:block}.widget__label{width:100%;position:relative}.widget__label-text{margin:0;padding-bottom:4px;font-weight:500;font-size:13px;line-height:15px;font-feature-settings:"pnum" on,"lnum" on;color:var(--color-text-main)}.widget__input{border-radius:16px;background-color:var(--color-bg-minor);padding:15.5px 16px;width:calc(100% - 32px);font-size:16px;line-height:17px;color:var(--color-text-main);font-feature-settings:"pnum" on,"lnum" on;outline:none;border:none}.widget__input.active{background-color:var(--color-white);border:2px solid var(--color-black);width:calc(100% - 36px)}.widget__input--search{padding-right:44px;width:calc(100% - 60px);font-size:0;line-height:0}.widget__input--search.active{width:calc(100% - 64px);font-size:16px;line-height:17px}@media screen and (max-width: 750px){.widget__input--search.active{width:calc(100% - 36px)}}.widget__input--search:before{content:"\\41f\\43e\\438\\441\\43a  \\43f\\43e  \\430\\434\\440\\435\\441\\443";font-size:16px;line-height:17px;color:var(--color-text-minor);font-feature-settings:"pnum" on,"lnum" on}.widget__input--search.active:before{content:none}.widget__label-button{background:transparent;border:none;outline:none;cursor:pointer;position:absolute;padding:0;right:12px;top:50%;transform:translateY(-12%);width:24px;height:24px}.widget__label-button--loader{display:none;animation:spinner 2s linear infinite;top:46%}@keyframes spinner{0%{transform:rotate(0)}to{transform:rotate(360deg)}}.widget__label-img{width:24px;height:24px}.widget__lead{background-color:var(--color-bg-minor);width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:16px;outline:none;border:none;cursor:pointer}.widget__lead-wrapper{padding-left:8px;position:relative}.widget__lead-wrapper.count:after{content:attr(data-count);position:absolute;right:-4.5px;top:-4.5px;width:19px;height:19px;display:flex;align-items:center;justify-content:center;background-color:var(--color-red);box-shadow:0 2px 5px #d4412e4d;border-radius:50%;font-weight:500;font-size:13px;line-height:14px;color:var(--color-white)}.widget__list{position:relative;margin-top:8px;background-color:var(--color-white);border-radius:var(--border-radius);height:100%}.widget__start{text-align:center;padding-top:20px;color:var(--color-text-minor)}.widget__start-title{margin:0;padding-bottom:4px;font-weight:500;font-size:16px;line-height:17px}.widget__start-desc{margin:0;font-size:13px;line-height:14px;font-feature-settings:"pnum" on,"lnum" on}.widget__list-button{position:absolute;bottom:0;left:0;padding:0;width:100%;height:72px;box-shadow:0 -4px 20px #0000001f;border-radius:var(--border-radius);border:none;border:8px solid var(--color-white);cursor:pointer}.widget__list-button-span{margin:0;display:flex;align-items:center;justify-content:center;width:100%;height:100%;background-color:var(--color-yellow);border-radius:16px;font-weight:500;font-size:16px;line-height:17px;color:var(--color-text-main);letter-spacing:-.005em;font-feature-settings:"pnum" on,"lnum" on}.widget__locations{max-height:100%;overflow:auto;padding:0 16px 0 12px}.widget__locations::-webkit-scrollbar{width:0}.widget__location{padding:12px 0;cursor:pointer}.widget__location.active .widget__location-details{height:fit-content;padding-top:12px}.widget__location-wrapper{display:flex;align-items:center}.widget__location{border-bottom:.5px solid var(--color-line);border-bottom-color:#d2d0ccd1}.widget__location-lead{display:flex;align-items:center;width:100%}.widget__location-img{width:24px;height:24px}.widget__location-content{padding-left:8px;width:100%;max-width:268px}.widget__location-title{margin:0;font-weight:500;font-size:16px;line-height:17px;letter-spacing:-.005em;font-feature-settings:"pnum" on,"lnum" on}.widget__location-desc{margin:0;padding-top:1px;font-size:13px;line-height:14px;font-feature-settings:"pnum" on,"lnum" on;max-width:268px;overflow:hidden;text-overflow:ellipsis}.widget__location-faq{padding:0 10px;display:flex}.widget__location-checkbox-input{opacity:0;width:0;height:0;visibility:hidden;position:absolute}.widget__location-checkbox-label{cursor:pointer}.widget__location-checkbox-input~.widget__location-checkbox-label:before{content:"";display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;box-shadow:inset 0 2px 3px #0000000d;background-color:var(--color-control-minor)}.widget__location-checkbox-input:checked~.widget__location-checkbox-label:before{background-color:var(--color-yellow);box-shadow:none}.widget__location-checkbox-input:checked~.widget__location-checkbox-label:after{content:url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10.1654 15.1806L18.5229 6.8232C18.9134 6.43267 19.5465 6.43267 19.9371 6.8232L20.2929 7.17902C20.6834 7.56954 20.6834 8.20271 20.2929 8.59323L10.1654 18.7207L4.70711 13.2624C4.31658 12.8718 4.31658 12.2387 4.70711 11.8481L5.06293 11.4923C5.45345 11.1018 6.08662 11.1018 6.47714 11.4923L10.1654 15.1806Z' fill='%2321201F'/%3E%3C/svg%3E%0A");position:absolute;transform:translate(18%,-100%)}.widget__location-details{height:0;padding-top:0;overflow:hidden}.widget__location-details-wrapper{padding:16px;background-color:var(--color-cream);border-radius:16px}.widget__location-detail{margin:0;font-size:16px;line-height:17px;color:var(--color-text-main);font-feature-settings:"pnum" on,"lnum" on}.widget__location-detail:not(:last-child){padding-bottom:8px}.widget__location-detail-span{margin:0;font-size:13px;line-height:14px;color:var(--color-text-minor)}.widget__marker-count{position:absolute;width:40px;height:40px;display:flex;align-items:center;justify-content:center;font-size:20px;line-height:27px;letter-spacing:-.01em;font-feature-settings:"pnum" on,"lnum" on;color:var(--color-white);background-color:var(--color-greay);border:2px solid var(--color-white);border-radius:50%}.widget__marker-count--empty:before{content:"";width:14px;height:14px;background-color:var(--color-white);border-radius:50%}.widget__marker-extradition{position:absolute;display:flex;align-items:center;padding:9px 8px;background-color:var(--color-white);box-shadow:0 8px 20px #0000001f;border-radius:12px;cursor:pointer;top:-21px;left:-58px}.widget__marker-extradition.active{background-color:var(--color-greay)}.widget__marker-extradition.active:before{content:url("data:image/svg+xml,%3Csvg width='24' height='8' viewBox='0 0 24 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M12 8L11.5777 7.15542C11.3258 6.65156 11.1998 6.39963 11.0728 6.17114C9.04687 2.52532 5.27311 0.193012 1.10615 0.0113831C0.844995 0 0.56333 0 0 0L24 0C23.4367 0 23.155 0 22.8938 0.0113831C18.7269 0.193012 14.9531 2.52532 12.9272 6.17114C12.8002 6.39962 12.6742 6.65154 12.4223 7.15536L12.4223 7.15542L12 8Z' fill='%235C5A57'/%3E%3C/svg%3E")}.widget__marker-extradition.active .widget__marker-extradition-content{color:var(--color-white)}.widget__marker-extradition.active .widget__marker-extradition-svg--man:before{content:url("data:image/svg+xml,%3Csvg width='21' height='20' viewBox='0 0 21 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath transform='translate(-1,-3)' opacity='0.4' d='M13 13C12.4477 13 12 13.4477 12 14V21C12 21.5523 12.4477 22 13 22H20C20.5523 22 21 21.5523 21 21V14C21 13.4477 20.5523 13 20 13L13 13Z' fill='black'/%3E%3Cpath d='M9.32915 6.58493C10.9389 6.58493 12.2438 5.45604 12.2438 3.71755C12.2438 3.55792 12.2328 3.40273 12.2116 3.25251L12.6506 3.17825C13.2867 3.07063 13.7934 2.58683 13.9302 1.95635L14.0369 1.46483L11.2592 1.46951C10.7451 1.02374 10.0694 0.767578 9.32915 0.767578C7.71941 0.767578 6.41445 1.97906 6.41445 3.71755C6.41445 5.45604 7.71941 6.58493 9.32915 6.58493Z' fill='%23F5F4F2'/%3E%3Cpath d='M8.07722 7.40534C6.66167 7.07061 5.24279 7.94679 4.90807 9.36234L2.94723 17.6547L2.89123 17.718H0.931454C0.685618 17.718 0.486328 17.9173 0.486328 18.1631V18.8411C0.486328 19.0869 0.685617 19.2862 0.931454 19.2862H20.3049C20.5507 19.2862 20.75 19.0869 20.75 18.8411V18.1631C20.75 17.9173 20.5507 17.718 20.3049 17.718H8.16247L9.0314 14.6832L9.03741 14.7039C9.16859 15.1557 9.52333 15.4846 9.9488 15.6013C10.0231 15.6357 10.102 15.6634 10.1848 15.6833L16.2124 17.1335C16.5122 16.1093 15.9563 15.03 14.9485 14.6793L11.4393 13.4584L10.4332 8.90381C10.386 8.69038 10.2953 8.49838 10.1725 8.33481C9.99817 8.02666 9.70085 7.78927 9.32915 7.70137L8.07722 7.40534Z' fill='%23F5F4F2'/%3E%3C/svg%3E")}.widget__marker-extradition.active .widget__marker-extradition-svg--locker:before{content:url("data:image/svg+xml,%3Csvg width='24' height='25' viewBox='0 0 24 25' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg opacity='0.4'%3E%3Cpath d='M19.1111 3.5C19.602 3.5 20 3.90878 20 4.41304V10.8043H4V4.41304C4 3.90878 4.39797 3.5 4.88889 3.5H19.1111Z' fill='%23ffffff'/%3E%3Cpath d='M5.77778 12.6304H4V19.9348L10.1818 24.5H12V17.2131C12 16.546 11.6459 15.9319 11.0763 15.6115L5.77778 12.6304Z' fill='%23ffffff'/%3E%3C/g%3E%3Cpath d='M17.3329 6.23926H13.7773V8.06534H17.3329V6.23926Z' fill='%23ffffff'/%3E%3Cpath d='M5.77734 12.6306H19.9996V19.0219C19.9996 19.5261 19.6016 19.9349 19.1107 19.9349H11.9996V17.2132C11.9996 16.5461 11.6454 15.9321 11.0758 15.6116L5.77734 12.6306Z' fill='%23ffffff'/%3E%3C/svg%3E")}.widget__marker-extradition.active .widget__marker-extradition-svg--post-office:before{content:url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath opacity='0.4' fill-rule='evenodd' clip-rule='evenodd' d='M18 2H2V18H18V2ZM0 0V20H20V0H0Z' fill='%23E0DEDA'/%3E%3Cpath d='M7.79825 5.14247C8.11611 5.04469 8.41481 4.99677 8.68354 5.00017C9.47106 5.01012 10.0011 5.46077 10.0011 6.38845C10.0011 5.46077 10.5311 5.01012 11.3187 5.00017C11.587 4.99678 11.8853 5.04457 12.2027 5.1421C12.3286 5.19554 12.4521 5.25342 12.5731 5.31555L12.215 5.9358C11.5806 6.17965 11.1303 6.82335 11.1303 7.54363C11.1303 7.9132 11.2488 8.25508 11.45 8.53334L15.4561 5.00051C15.8055 5.7766 16 6.63753 16 7.54389C16 10.4449 14.2121 12.8807 11.5213 13.558L12.5737 15.3808C11.8025 15.7769 10.928 16.0005 10.0014 16.0005C9.07526 16.0005 8.20132 15.7772 7.43043 15.3815L8.48329 13.5579C5.79311 12.8801 4 10.4447 4 7.54407C4 6.63772 4.19444 5.77678 4.54387 5.00069L8.55555 8.53328C8.75608 8.25527 8.87423 7.9139 8.87423 7.54494C8.87423 6.82436 8.4236 6.18044 7.78878 5.9368L7.42981 5.31504C7.55018 5.25325 7.67305 5.19566 7.79825 5.14247Z' fill='white'/%3E%3C/svg%3E%0A")}.widget__marker-extradition:before{content:url("data:image/svg+xml,%3Csvg width='24' height='8' viewBox='0 0 24 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M12 8L11.5777 7.15542C11.3258 6.65156 11.1998 6.39963 11.0728 6.17114C9.04687 2.52532 5.27311 0.193012 1.10615 0.0113831C0.844995 0 0.56333 0 0 0L24 0C23.4367 0 23.155 0 22.8938 0.0113831C18.7269 0.193012 14.9531 2.52532 12.9272 6.17114C12.8002 6.39962 12.6742 6.65154 12.4223 7.15536L12.4223 7.15542L12 8Z' fill='white'/%3E%3C/svg%3E");position:absolute;right:50%;bottom:-11px;transform:translate(50%)}.widget__marker-extradition-svg--man:before{content:url("data:image/svg+xml,%3Csvg width='21' height='20' viewBox='0 0 21 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath transform='translate(-1,-3)' opacity='0.4' d='M13 13C12.4477 13 12 13.4477 12 14V21C12 21.5523 12.4477 22 13 22H20C20.5523 22 21 21.5523 21 21V14C21 13.4477 20.5523 13 20 13L13 13Z' fill='black'/%3E%3Cpath d='M9.32915 6.58493C10.9389 6.58493 12.2438 5.45604 12.2438 3.71755C12.2438 3.55792 12.2328 3.40273 12.2116 3.25251L12.6506 3.17825C13.2867 3.07063 13.7934 2.58683 13.9302 1.95635L14.0369 1.46483L11.2592 1.46951C10.7451 1.02374 10.0694 0.767578 9.32915 0.767578C7.71941 0.767578 6.41445 1.97906 6.41445 3.71755C6.41445 5.45604 7.71941 6.58493 9.32915 6.58493Z' fill='%2321201F'/%3E%3Cpath d='M8.07722 7.40534C6.66167 7.07061 5.24279 7.94679 4.90807 9.36234L2.94723 17.6547L2.89123 17.718H0.931454C0.685618 17.718 0.486328 17.9173 0.486328 18.1631V18.8411C0.486328 19.0869 0.685617 19.2862 0.931454 19.2862H20.3049C20.5507 19.2862 20.75 19.0869 20.75 18.8411V18.1631C20.75 17.9173 20.5507 17.718 20.3049 17.718H8.16247L9.0314 14.6832L9.03741 14.7039C9.16859 15.1557 9.52333 15.4846 9.9488 15.6013C10.0231 15.6357 10.102 15.6634 10.1848 15.6833L16.2124 17.1335C16.5122 16.1093 15.9563 15.03 14.9485 14.6793L11.4393 13.4584L10.4332 8.90381C10.386 8.69038 10.2953 8.49838 10.1725 8.33481C9.99817 8.02666 9.70085 7.78927 9.32915 7.70137L8.07722 7.40534Z' fill='%2321201F'/%3E%3C/svg%3E")}.widget__marker-extradition-svg--locker:before{content:url("data:image/svg+xml, %3Csvg width='24' height='25' viewBox='0 0 24 25' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg opacity='0.4'%3E%3Cpath d='M19.1111 3.5C19.602 3.5 20 3.90878 20 4.41304V10.8043H4V4.41304C4 3.90878 4.39797 3.5 4.88889 3.5H19.1111Z' fill='%2321201F'/%3E%3Cpath d='M5.77778 12.6304H4V19.9348L10.1818 24.5H12V17.2131C12 16.546 11.6459 15.9319 11.0763 15.6115L5.77778 12.6304Z' fill='%2321201F'/%3E%3C/g%3E%3Cpath d='M17.3329 6.23926H13.7773V8.06534H17.3329V6.23926Z' fill='%2321201F'/%3E%3Cpath d='M5.77734 12.6306H19.9996V19.0219C19.9996 19.5261 19.6016 19.9349 19.1107 19.9349H11.9996V17.2132C11.9996 16.5461 11.6454 15.9321 11.0758 15.6116L5.77734 12.6306Z' fill='%2321201F'/%3E%3C/svg%3E")}.widget__marker-extradition-svg--post-office:before{content:url("data:image/svg+xml,%3Csvg width='20' height='21' viewBox='0 0 20 21' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath opacity='0.4' fill-rule='evenodd' clip-rule='evenodd' d='M18 2.5H2V18.5H18V2.5ZM-3.8147e-06 0.5V20.5H20V0.5H-3.8147e-06Z' fill='%2321201F'/%3E%3Cpath d='M7.79826 5.64247C8.11611 5.54469 8.41482 5.49677 8.68354 5.50017C9.47106 5.51012 10.0011 5.96077 10.0011 6.88845C10.0011 5.96077 10.5311 5.51012 11.3187 5.50017C11.587 5.49678 11.8853 5.54457 12.2027 5.6421C12.3286 5.69554 12.4521 5.75342 12.5731 5.81555L12.215 6.4358C11.5806 6.67965 11.1303 7.32335 11.1303 8.04363C11.1303 8.4132 11.2488 8.75508 11.45 9.03334L15.4561 5.50051C15.8055 6.2766 16 7.13753 16 8.04389C16 10.9449 14.2121 13.3807 11.5213 14.058L12.5737 15.8808C11.8025 16.2769 10.928 16.5005 10.0014 16.5005C9.07526 16.5005 8.20132 16.2772 7.43044 15.8815L8.4833 14.0579C5.79311 13.3801 4 10.9447 4 8.04407C4 7.13772 4.19444 6.27678 4.54388 5.50069L8.55556 9.03328C8.75608 8.75527 8.87423 8.4139 8.87423 8.04494C8.87423 7.32436 8.4236 6.68044 7.78879 6.4368L7.42981 5.81504C7.55018 5.75325 7.67306 5.69566 7.79826 5.64247Z' fill='%2321201F'/%3E%3C/svg%3E%0A")}.widget__marker-extradition-content{color:var(--color-text-main);padding-left:4px}.widget__marker-extradition-title{font-weight:500;font-size:13px;line-height:14px;margin:0}.widget__marker-extradition-desc{font-size:11px;line-height:12px;letter-spacing:.01em;font-feature-settings:"pnum" on,"lnum" on;margin:0;max-width:156px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}/*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}main{display:block}h1{font-size:2em;margin:.67em 0}hr{box-sizing:content-box;height:0;overflow:visible}pre{font-family:monospace,monospace;font-size:1em}a{background-color:transparent}abbr[title]{border-bottom:none;text-decoration:underline;text-decoration:underline dotted}b,strong{font-weight:bolder}code,kbd,samp{font-family:monospace,monospace;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}img{border-style:none}button,input,optgroup,select,textarea{font-family:inherit;font-size:100%;line-height:1.15;margin:0}button,input{overflow:visible}button,select{text-transform:none}button,[type=button],[type=reset],[type=submit]{-webkit-appearance:button}button::-moz-focus-inner,[type=button]::-moz-focus-inner,[type=reset]::-moz-focus-inner,[type=submit]::-moz-focus-inner{border-style:none;padding:0}button:-moz-focusring,[type=button]:-moz-focusring,[type=reset]:-moz-focusring,[type=submit]:-moz-focusring{outline:1px dotted ButtonText}fieldset{padding:.35em .75em .625em}legend{box-sizing:border-box;color:inherit;display:table;max-width:100%;padding:0;white-space:normal}progress{vertical-align:baseline}textarea{overflow:auto}[type=checkbox],[type=radio]{box-sizing:border-box;padding:0}[type=number]::-webkit-inner-spin-button,[type=number]::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}[type=search]::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}details{display:block}summary{display:list-item}template{display:none}[hidden]{display:none}:root{--color-white: #ffffff;--color-black: #000000;--color-text-main: #21201F;--color-bg-minor: #F5F4F2;--color-text-minor: #9E9B98;--color-yellow: #FCE000;--color-cream: rgba(252, 228, 94, .1);--color-greay: #57595C;--color-red: #FC5230;--color-control-minor: #F1F0ED;--color-line: #D2D0CC;--border-radius: 24px}.ydw-widget{font-family:YS Text;font-style:normal;font-weight:400;width:100%;height:100%;border-radius:var(--border-radius);background-color:var(--color-bg-minor);padding:8px}.widget__wrapper{display:flex;overflow:hidden}.widget__content{min-width:400px;max-width:400px;padding-right:8px}.widget__map{position:relative;background-size:cover;background-repeat:no-repeat;width:100%;border-radius:var(--border-radius)}.transition{transition:all .3s ease}.widget__header .widget__start,.widget__map-back{display:none}@media screen and (max-width: 750px){.ydw-widget__header-mobile{height:300px}.ydw-widget__title-mobile{display:none}.ydw-widget{padding:0}.widget__wrapper{flex-direction:column}.widget{padding:0}.widget__filters,.widget__content{max-width:100%;min-width:100%}.widget__header{padding:15px 16px;box-shadow:0 8px 20px #0000001f;position:absolute;z-index:4;bottom:0;width:calc(100% - 32px);border-bottom-right-radius:0;border-bottom-left-radius:0}.widget__title-text{font-size:24.3787px;line-height:23px}.widget__title-text-second{font-size:21.3787px}.widget__input{font-weight:400;font-size:16px;line-height:17px}.widget__form-item:not(:last-child){padding-bottom:8px}.widget__form-item .widget__label-text{display:none}.widget__form-item.mobile-title .widget__label-text{display:block;position:absolute;left:16px;top:8px;font-size:13px;line-height:14px;color:var(--color-text-minor)}.widget__input.mobile-select{padding:23px 16px 8px}.widget__label-button--loader,.widget__label-button--search{top:15px}.widget__overlay{display:none;position:fixed;width:100%;height:100%;background:rgba(66,65,62,.5);z-index:2}.widget__overlay.active{display:block}.widget__header.search-mobile .widget__start.active{display:block;position:absolute;top:187px;left:50%;transform:translate(-50%);padding-top:0;width:100%}.widget__header.search-mobile{top:72px}.widget__header.search-mobile .widget__popup--search.result{display:block;padding:6px 0 0;width:100%;box-shadow:none;max-height:279px;overflow:auto;border-radius:0}.widget__header.search-mobile .widget__title,.widget__header.search-mobile .widget__form-item:first-child{display:none}.widget__map-back.active{display:block!important;position:absolute;cursor:pointer;top:16px;left:8px;z-index:3}}@media screen and (max-width: 750px){.widget__filters{left:0;z-index:3;bottom:-100%;height:0;overflow:hidden;transition:bottom .3s ease}.widget__filters.active{bottom:0;left:0;padding:0;height:auto;transition:bottom .3s ease}.widget__filters-arrow{display:none}}.widget__location-close{display:none}@media screen and (max-width: 750px){.widget__list{position:absolute;z-index:3;margin-top:0;transition:bottom .3s ease;height:0;overflow:hidden;top:100%;transform:translateY(-100%);width:100%}.widget__list-button{display:block}.widget__list.active{bottom:0;height:auto}.widget__list.active .widget__list-button{display:block}.widget__list-button-span{font-size:0}.widget__list-button-span:before{content:"\\41f\\440\\43e\\434\\43e\\43b\\436\\438\\442\\44c";letter-spacing:-.005em;font-feature-settings:"pnum" on,"lnum" on;color:#21201f;font-weight:500;font-size:16px;line-height:17px}.widget__locations{max-height:100%}.widget__location-faq,.widget__location-checkbox,.widget__location{display:none}.widget__location.active{display:block;padding-bottom:75px}.widget__location-close{display:block}}@media screen and (max-width: 750px){.widget__map{height:300px}.widget__map-close{top:16px;left:8px;display:block!important}.widget__map-button{width:48px;height:48px;border-radius:50%;box-shadow:0 8px 20px #0000001f}.widget__map-zoom,.widget__map-location{display:none}.widget__map-location--mobile{display:block;transform:none;bottom:185px;top:initial;right:8px;z-index:3}.widget__map-filter-mobile{position:absolute;right:64px;bottom:185px;cursor:pointer;z-index:3}.widget__map-filter-mobile .widget__map-button{width:121px;border-radius:100px}}.vue-recycle-scroller{position:relative}.vue-recycle-scroller.direction-vertical:not(.page-mode){overflow-y:auto}.vue-recycle-scroller.direction-horizontal:not(.page-mode){overflow-x:auto}.vue-recycle-scroller.direction-horizontal{display:flex}.vue-recycle-scroller__slot{flex:auto 0 0}.vue-recycle-scroller__item-wrapper{flex:1;box-sizing:border-box;overflow:hidden;position:relative}.vue-recycle-scroller.ready .vue-recycle-scroller__item-view{position:absolute;top:0;left:0;will-change:transform}.vue-recycle-scroller.direction-vertical .vue-recycle-scroller__item-wrapper{width:100%}.vue-recycle-scroller.direction-horizontal .vue-recycle-scroller__item-wrapper{height:100%}.vue-recycle-scroller.ready.direction-vertical .vue-recycle-scroller__item-view{width:100%}.vue-recycle-scroller.ready.direction-horizontal .vue-recycle-scroller__item-view{height:100%}.resize-observer[data-v-b329ee4c]{position:absolute;top:0;left:0;z-index:-1;width:100%;height:100%;border:none;background-color:transparent;pointer-events:none;display:block;overflow:hidden;opacity:0}.resize-observer[data-v-b329ee4c] object{display:block;position:absolute;top:0;left:0;height:100%;width:100%;overflow:hidden;pointer-events:none;z-index:-1}`)), document.head.appendChild(t)
        }
    } catch (e) {
        console.error("vite-plugin-css-injected-by-js", e)
    }
})();
(function() {
    const t = document.createElement("link").relList;
    if (t && t.supports && t.supports("modulepreload")) return;
    for (const n of document.querySelectorAll('link[rel="modulepreload"]')) s(n);
    new MutationObserver(n => {
        for (const r of n)
            if (r.type === "childList")
                for (const o of r.addedNodes) o.tagName === "LINK" && o.rel === "modulepreload" && s(o)
    }).observe(document, {
        childList: !0,
        subtree: !0
    });

    function i(n) {
        const r = {};
        return n.integrity && (r.integrity = n.integrity), n.referrerpolicy && (r.referrerPolicy = n.referrerpolicy), n.crossorigin === "use-credentials" ? r.credentials = "include" : n.crossorigin === "anonymous" ? r.credentials = "omit" : r.credentials = "same-origin", r
    }

    function s(n) {
        if (n.ep) return;
        n.ep = !0;
        const r = i(n);
        fetch(n.href, r)
    }
})();

function Ki(e, t) {
    const i = Object.create(null),
        s = e.split(",");
    for (let n = 0; n < s.length; n++) i[s[n]] = !0;
    return t ? n => !!i[n.toLowerCase()] : n => !!i[n]
}
const fr = "itemscope,allowfullscreen,formnovalidate,ismap,nomodule,novalidate,readonly",
    hr = Ki(fr);

function an(e) {
    return !!e || e === ""
}

function Ke(e) {
    if (N(e)) {
        const t = {};
        for (let i = 0; i < e.length; i++) {
            const s = e[i],
                n = de(s) ? _r(s) : Ke(s);
            if (n)
                for (const r in n) t[r] = n[r]
        }
        return t
    } else {
        if (de(e)) return e;
        if (re(e)) return e
    }
}
const pr = /;(?![^(]*\))/g,
    mr = /:(.+)/;

function _r(e) {
    const t = {};
    return e.split(pr).forEach(i => {
        if (i) {
            const s = i.split(mr);
            s.length > 1 && (t[s[0].trim()] = s[1].trim())
        }
    }), t
}

function Pe(e) {
    let t = "";
    if (de(e)) t = e;
    else if (N(e))
        for (let i = 0; i < e.length; i++) {
            const s = Pe(e[i]);
            s && (t += s + " ")
        } else if (re(e))
            for (const i in e) e[i] && (t += i + " ");
    return t.trim()
}

function gr(e) {
    if (!e) return null;
    let {
        class: t,
        style: i
    } = e;
    return t && !de(t) && (e.class = Pe(t)), i && (e.style = Ke(i)), e
}

function yr(e, t) {
    if (e.length !== t.length) return !1;
    let i = !0;
    for (let s = 0; i && s < e.length; s++) i = di(e[s], t[s]);
    return i
}

function di(e, t) {
    if (e === t) return !0;
    let i = xs(e),
        s = xs(t);
    if (i || s) return i && s ? e.getTime() === t.getTime() : !1;
    if (i = kt(e), s = kt(t), i || s) return e === t;
    if (i = N(e), s = N(t), i || s) return i && s ? yr(e, t) : !1;
    if (i = re(e), s = re(t), i || s) {
        if (!i || !s) return !1;
        const n = Object.keys(e).length,
            r = Object.keys(t).length;
        if (n !== r) return !1;
        for (const o in e) {
            const l = e.hasOwnProperty(o),
                c = t.hasOwnProperty(o);
            if (l && !c || !l && c || !di(e[o], t[o])) return !1
        }
    }
    return String(e) === String(t)
}

function dn(e, t) {
    return e.findIndex(i => di(i, t))
}
const ct = e => de(e) ? e : e == null ? "" : N(e) || re(e) && (e.toString === hn || !Y(e.toString)) ? JSON.stringify(e, un, 2) : String(e),
    un = (e, t) => t && t.__v_isRef ? un(e, t.value) : Ct(t) ? {
        [`Map(${t.size})`]: [...t.entries()].reduce((i, [s, n]) => (i[`${s} =>`] = n, i), {})
    } : fi(t) ? {
        [`Set(${t.size})`]: [...t.values()]
    } : re(t) && !N(t) && !pn(t) ? String(t) : t,
    se = {},
    bt = [],
    Re = () => {},
    wr = () => !1,
    vr = /^on[^a-z]/,
    ui = e => vr.test(e),
    qi = e => e.startsWith("onUpdate:"),
    Me = Object.assign,
    Zi = (e, t) => {
        const i = e.indexOf(t);
        i > -1 && e.splice(i, 1)
    },
    br = Object.prototype.hasOwnProperty,
    U = (e, t) => br.call(e, t),
    N = Array.isArray,
    Ct = e => Nt(e) === "[object Map]",
    fi = e => Nt(e) === "[object Set]",
    xs = e => Nt(e) === "[object Date]",
    Y = e => typeof e == "function",
    de = e => typeof e == "string",
    kt = e => typeof e == "symbol",
    re = e => e !== null && typeof e == "object",
    fn = e => re(e) && Y(e.then) && Y(e.catch),
    hn = Object.prototype.toString,
    Nt = e => hn.call(e),
    Cr = e => Nt(e).slice(8, -1),
    pn = e => Nt(e) === "[object Object]",
    Ji = e => de(e) && e !== "NaN" && e[0] !== "-" && "" + parseInt(e, 10) === e,
    Kt = Ki(",key,ref,ref_for,ref_key,onVnodeBeforeMount,onVnodeMounted,onVnodeBeforeUpdate,onVnodeUpdated,onVnodeBeforeUnmount,onVnodeUnmounted"),
    hi = e => {
        const t = Object.create(null);
        return i => t[i] || (t[i] = e(i))
    },
    xr = /-(\w)/g,
    qe = hi(e => e.replace(xr, (t, i) => i ? i.toUpperCase() : "")),
    Sr = /\B([A-Z])/g,
    wt = hi(e => e.replace(Sr, "-$1").toLowerCase()),
    pi = hi(e => e.charAt(0).toUpperCase() + e.slice(1)),
    qt = hi(e => e ? `on${pi(e)}` : ""),
    ti = (e, t) => !Object.is(e, t),
    Zt = (e, t) => {
        for (let i = 0; i < e.length; i++) e[i](t)
    },
    ii = (e, t, i) => {
        Object.defineProperty(e, t, {
            configurable: !0,
            enumerable: !1,
            value: i
        })
    },
    zi = e => {
        const t = parseFloat(e);
        return isNaN(t) ? e : t
    };
let Ss;
const Mr = () => Ss || (Ss = typeof globalThis < "u" ? globalThis : typeof self < "u" ? self : typeof window < "u" ? window : typeof global < "u" ? global : {});
let je;
class $r {
    constructor(t = !1) {
        this.active = !0, this.effects = [], this.cleanups = [], !t && je && (this.parent = je, this.index = (je.scopes || (je.scopes = [])).push(this) - 1)
    }
    run(t) {
        if (this.active) {
            const i = je;
            try {
                return je = this, t()
            } finally {
                je = i
            }
        }
    }
    on() {
        je = this
    }
    off() {
        je = this.parent
    }
    stop(t) {
        if (this.active) {
            let i, s;
            for (i = 0, s = this.effects.length; i < s; i++) this.effects[i].stop();
            for (i = 0, s = this.cleanups.length; i < s; i++) this.cleanups[i]();
            if (this.scopes)
                for (i = 0, s = this.scopes.length; i < s; i++) this.scopes[i].stop(!0);
            if (this.parent && !t) {
                const n = this.parent.scopes.pop();
                n && n !== this && (this.parent.scopes[this.index] = n, n.index = this.index)
            }
            this.active = !1
        }
    }
}

function Ir(e, t = je) {
    t && t.active && t.effects.push(e)
}
const Xi = e => {
        const t = new Set(e);
        return t.w = 0, t.n = 0, t
    },
    mn = e => (e.w & nt) > 0,
    _n = e => (e.n & nt) > 0,
    Or = ({
        deps: e
    }) => {
        if (e.length)
            for (let t = 0; t < e.length; t++) e[t].w |= nt
    },
    zr = e => {
        const {
            deps: t
        } = e;
        if (t.length) {
            let i = 0;
            for (let s = 0; s < t.length; s++) {
                const n = t[s];
                mn(n) && !_n(n) ? n.delete(e) : t[i++] = n, n.w &= ~nt, n.n &= ~nt
            }
            t.length = i
        }
    },
    Pi = new WeakMap;
let Dt = 0,
    nt = 1;
const Ti = 30;
let He;
const gt = Symbol(""),
    Di = Symbol("");
class Qi {
    constructor(t, i = null, s) {
        this.fn = t, this.scheduler = i, this.active = !0, this.deps = [], this.parent = void 0, Ir(this, s)
    }
    run() {
        if (!this.active) return this.fn();
        let t = He,
            i = it;
        for (; t;) {
            if (t === this) return;
            t = t.parent
        }
        try {
            return this.parent = He, He = this, it = !0, nt = 1 << ++Dt, Dt <= Ti ? Or(this) : Ms(this), this.fn()
        } finally {
            Dt <= Ti && zr(this), nt = 1 << --Dt, He = this.parent, it = i, this.parent = void 0, this.deferStop && this.stop()
        }
    }
    stop() {
        He === this ? this.deferStop = !0 : this.active && (Ms(this), this.onStop && this.onStop(), this.active = !1)
    }
}

function Ms(e) {
    const {
        deps: t
    } = e;
    if (t.length) {
        for (let i = 0; i < t.length; i++) t[i].delete(e);
        t.length = 0
    }
}
let it = !0;
const gn = [];

function Ot() {
    gn.push(it), it = !1
}

function zt() {
    const e = gn.pop();
    it = e === void 0 ? !0 : e
}

function ze(e, t, i) {
    if (it && He) {
        let s = Pi.get(e);
        s || Pi.set(e, s = new Map);
        let n = s.get(i);
        n || s.set(i, n = Xi()), yn(n)
    }
}

function yn(e, t) {
    let i = !1;
    Dt <= Ti ? _n(e) || (e.n |= nt, i = !mn(e)) : i = !e.has(He), i && (e.add(He), He.deps.push(e))
}

function Je(e, t, i, s, n, r) {
    const o = Pi.get(e);
    if (!o) return;
    let l = [];
    if (t === "clear") l = [...o.values()];
    else if (i === "length" && N(e)) o.forEach((c, u) => {
        (u === "length" || u >= s) && l.push(c)
    });
    else switch (i !== void 0 && l.push(o.get(i)), t) {
        case "add":
            N(e) ? Ji(i) && l.push(o.get("length")) : (l.push(o.get(gt)), Ct(e) && l.push(o.get(Di)));
            break;
        case "delete":
            N(e) || (l.push(o.get(gt)), Ct(e) && l.push(o.get(Di)));
            break;
        case "set":
            Ct(e) && l.push(o.get(gt));
            break
    }
    if (l.length === 1) l[0] && Ai(l[0]);
    else {
        const c = [];
        for (const u of l) u && c.push(...u);
        Ai(Xi(c))
    }
}

function Ai(e, t) {
    const i = N(e) ? e : [...e];
    for (const s of i) s.computed && $s(s);
    for (const s of i) s.computed || $s(s)
}

function $s(e, t) {
    (e !== He || e.allowRecurse) && (e.scheduler ? e.scheduler() : e.run())
}
const Pr = Ki("__proto__,__v_isRef,__isVue"),
    wn = new Set(Object.getOwnPropertyNames(Symbol).filter(e => e !== "arguments" && e !== "caller").map(e => Symbol[e]).filter(kt)),
    Tr = Gi(),
    Dr = Gi(!1, !0),
    Ar = Gi(!0),
    Is = Er();

function Er() {
    const e = {};
    return ["includes", "indexOf", "lastIndexOf"].forEach(t => {
        e[t] = function(...i) {
            const s = G(this);
            for (let r = 0, o = this.length; r < o; r++) ze(s, "get", r + "");
            const n = s[t](...i);
            return n === -1 || n === !1 ? s[t](...i.map(G)) : n
        }
    }), ["push", "pop", "shift", "unshift", "splice"].forEach(t => {
        e[t] = function(...i) {
            Ot();
            const s = G(this)[t].apply(this, i);
            return zt(), s
        }
    }), e
}

function Gi(e = !1, t = !1) {
    return function(s, n, r) {
        if (n === "__v_isReactive") return !e;
        if (n === "__v_isReadonly") return e;
        if (n === "__v_isShallow") return t;
        if (n === "__v_raw" && r === (e ? t ? Jr : Sn : t ? xn : Cn).get(s)) return s;
        const o = N(s);
        if (!e && o && U(Is, n)) return Reflect.get(Is, n, r);
        const l = Reflect.get(s, n, r);
        return (kt(n) ? wn.has(n) : Pr(n)) || (e || ze(s, "get", n), t) ? l : xe(l) ? o && Ji(n) ? l : l.value : re(l) ? e ? $n(l) : is(l) : l
    }
}
const kr = vn(),
    Lr = vn(!0);

function vn(e = !1) {
    return function(i, s, n, r) {
        let o = i[s];
        if (Lt(o) && xe(o) && !xe(n)) return !1;
        if (!e && (!Ei(n) && !Lt(n) && (o = G(o), n = G(n)), !N(i) && xe(o) && !xe(n))) return o.value = n, !0;
        const l = N(i) && Ji(s) ? Number(s) < i.length : U(i, s),
            c = Reflect.set(i, s, n, r);
        return i === G(r) && (l ? ti(n, o) && Je(i, "set", s, n) : Je(i, "add", s, n)), c
    }
}

function Hr(e, t) {
    const i = U(e, t);
    e[t];
    const s = Reflect.deleteProperty(e, t);
    return s && i && Je(e, "delete", t, void 0), s
}

function Fr(e, t) {
    const i = Reflect.has(e, t);
    return (!kt(t) || !wn.has(t)) && ze(e, "has", t), i
}

function Rr(e) {
    return ze(e, "iterate", N(e) ? "length" : gt), Reflect.ownKeys(e)
}
const bn = {
        get: Tr,
        set: kr,
        deleteProperty: Hr,
        has: Fr,
        ownKeys: Rr
    },
    Br = {
        get: Ar,
        set(e, t) {
            return !0
        },
        deleteProperty(e, t) {
            return !0
        }
    },
    Nr = Me({}, bn, {
        get: Dr,
        set: Lr
    }),
    es = e => e,
    mi = e => Reflect.getPrototypeOf(e);

function Vt(e, t, i = !1, s = !1) {
    e = e.__v_raw;
    const n = G(e),
        r = G(t);
    i || (t !== r && ze(n, "get", t), ze(n, "get", r));
    const {
        has: o
    } = mi(n), l = s ? es : i ? os : rs;
    if (o.call(n, t)) return l(e.get(t));
    if (o.call(n, r)) return l(e.get(r));
    e !== n && e.get(t)
}

function jt(e, t = !1) {
    const i = this.__v_raw,
        s = G(i),
        n = G(e);
    return t || (e !== n && ze(s, "has", e), ze(s, "has", n)), e === n ? i.has(e) : i.has(e) || i.has(n)
}

function Yt(e, t = !1) {
    return e = e.__v_raw, !t && ze(G(e), "iterate", gt), Reflect.get(e, "size", e)
}

function Os(e) {
    e = G(e);
    const t = G(this);
    return mi(t).has.call(t, e) || (t.add(e), Je(t, "add", e, e)), this
}

function zs(e, t) {
    t = G(t);
    const i = G(this),
        {
            has: s,
            get: n
        } = mi(i);
    let r = s.call(i, e);
    r || (e = G(e), r = s.call(i, e));
    const o = n.call(i, e);
    return i.set(e, t), r ? ti(t, o) && Je(i, "set", e, t) : Je(i, "add", e, t), this
}

function Ps(e) {
    const t = G(this),
        {
            has: i,
            get: s
        } = mi(t);
    let n = i.call(t, e);
    n || (e = G(e), n = i.call(t, e)), s && s.call(t, e);
    const r = t.delete(e);
    return n && Je(t, "delete", e, void 0), r
}

function Ts() {
    const e = G(this),
        t = e.size !== 0,
        i = e.clear();
    return t && Je(e, "clear", void 0, void 0), i
}

function Ut(e, t) {
    return function(s, n) {
        const r = this,
            o = r.__v_raw,
            l = G(o),
            c = t ? es : e ? os : rs;
        return !e && ze(l, "iterate", gt), o.forEach((u, f) => s.call(n, c(u), c(f), r))
    }
}

function Wt(e, t, i) {
    return function(...s) {
        const n = this.__v_raw,
            r = G(n),
            o = Ct(r),
            l = e === "entries" || e === Symbol.iterator && o,
            c = e === "keys" && o,
            u = n[e](...s),
            f = i ? es : t ? os : rs;
        return !t && ze(r, "iterate", c ? Di : gt), {
            next() {
                const {
                    value: p,
                    done: b
                } = u.next();
                return b ? {
                    value: p,
                    done: b
                } : {
                    value: l ? [f(p[0]), f(p[1])] : f(p),
                    done: b
                }
            },
            [Symbol.iterator]() {
                return this
            }
        }
    }
}

function Ge(e) {
    return function(...t) {
        return e === "delete" ? !1 : this
    }
}

function Vr() {
    const e = {
            get(r) {
                return Vt(this, r)
            },
            get size() {
                return Yt(this)
            },
            has: jt,
            add: Os,
            set: zs,
            delete: Ps,
            clear: Ts,
            forEach: Ut(!1, !1)
        },
        t = {
            get(r) {
                return Vt(this, r, !1, !0)
            },
            get size() {
                return Yt(this)
            },
            has: jt,
            add: Os,
            set: zs,
            delete: Ps,
            clear: Ts,
            forEach: Ut(!1, !0)
        },
        i = {
            get(r) {
                return Vt(this, r, !0)
            },
            get size() {
                return Yt(this, !0)
            },
            has(r) {
                return jt.call(this, r, !0)
            },
            add: Ge("add"),
            set: Ge("set"),
            delete: Ge("delete"),
            clear: Ge("clear"),
            forEach: Ut(!0, !1)
        },
        s = {
            get(r) {
                return Vt(this, r, !0, !0)
            },
            get size() {
                return Yt(this, !0)
            },
            has(r) {
                return jt.call(this, r, !0)
            },
            add: Ge("add"),
            set: Ge("set"),
            delete: Ge("delete"),
            clear: Ge("clear"),
            forEach: Ut(!0, !0)
        };
    return ["keys", "values", "entries", Symbol.iterator].forEach(r => {
        e[r] = Wt(r, !1, !1), i[r] = Wt(r, !0, !1), t[r] = Wt(r, !1, !0), s[r] = Wt(r, !0, !0)
    }), [e, i, t, s]
}
const [jr, Yr, Ur, Wr] = Vr();

function ts(e, t) {
    const i = t ? e ? Wr : Ur : e ? Yr : jr;
    return (s, n, r) => n === "__v_isReactive" ? !e : n === "__v_isReadonly" ? e : n === "__v_raw" ? s : Reflect.get(U(i, n) && n in s ? i : s, n, r)
}
const Kr = {
        get: ts(!1, !1)
    },
    qr = {
        get: ts(!1, !0)
    },
    Zr = {
        get: ts(!0, !1)
    },
    Cn = new WeakMap,
    xn = new WeakMap,
    Sn = new WeakMap,
    Jr = new WeakMap;

function Xr(e) {
    switch (e) {
        case "Object":
        case "Array":
            return 1;
        case "Map":
        case "Set":
        case "WeakMap":
        case "WeakSet":
            return 2;
        default:
            return 0
    }
}

function Qr(e) {
    return e.__v_skip || !Object.isExtensible(e) ? 0 : Xr(Cr(e))
}

function is(e) {
    return Lt(e) ? e : ss(e, !1, bn, Kr, Cn)
}

function Mn(e) {
    return ss(e, !1, Nr, qr, xn)
}

function $n(e) {
    return ss(e, !0, Br, Zr, Sn)
}

function ss(e, t, i, s, n) {
    if (!re(e) || e.__v_raw && !(t && e.__v_isReactive)) return e;
    const r = n.get(e);
    if (r) return r;
    const o = Qr(e);
    if (o === 0) return e;
    const l = new Proxy(e, o === 2 ? s : i);
    return n.set(e, l), l
}

function xt(e) {
    return Lt(e) ? xt(e.__v_raw) : !!(e && e.__v_isReactive)
}

function Lt(e) {
    return !!(e && e.__v_isReadonly)
}

function Ei(e) {
    return !!(e && e.__v_isShallow)
}

function In(e) {
    return xt(e) || Lt(e)
}

function G(e) {
    const t = e && e.__v_raw;
    return t ? G(t) : e
}

function ns(e) {
    return ii(e, "__v_skip", !0), e
}
const rs = e => re(e) ? is(e) : e,
    os = e => re(e) ? $n(e) : e;

function Gr(e) {
    it && He && (e = G(e), yn(e.dep || (e.dep = Xi())))
}

function eo(e, t) {
    e = G(e), e.dep && Ai(e.dep)
}

function xe(e) {
    return !!(e && e.__v_isRef === !0)
}

function to(e) {
    return xe(e) ? e.value : e
}
const io = {
    get: (e, t, i) => to(Reflect.get(e, t, i)),
    set: (e, t, i, s) => {
        const n = e[t];
        return xe(n) && !xe(i) ? (n.value = i, !0) : Reflect.set(e, t, i, s)
    }
};

function On(e) {
    return xt(e) ? e : new Proxy(e, io)
}
var zn;
class so {
    constructor(t, i, s, n) {
        this._setter = i, this.dep = void 0, this.__v_isRef = !0, this[zn] = !1, this._dirty = !0, this.effect = new Qi(t, () => {
            this._dirty || (this._dirty = !0, eo(this))
        }), this.effect.computed = this, this.effect.active = this._cacheable = !n, this.__v_isReadonly = s
    }
    get value() {
        const t = G(this);
        return Gr(t), (t._dirty || !t._cacheable) && (t._dirty = !1, t._value = t.effect.run()), t._value
    }
    set value(t) {
        this._setter(t)
    }
}
zn = "__v_isReadonly";

function no(e, t, i = !1) {
    let s, n;
    const r = Y(e);
    return r ? (s = e, n = Re) : (s = e.get, n = e.set), new so(s, n, r || !n, i)
}

function st(e, t, i, s) {
    let n;
    try {
        n = s ? e(...s) : e()
    } catch (r) {
        _i(r, t, i)
    }
    return n
}

function Be(e, t, i, s) {
    if (Y(e)) {
        const r = st(e, t, i, s);
        return r && fn(r) && r.catch(o => {
            _i(o, t, i)
        }), r
    }
    const n = [];
    for (let r = 0; r < e.length; r++) n.push(Be(e[r], t, i, s));
    return n
}

function _i(e, t, i, s = !0) {
    const n = t ? t.vnode : null;
    if (t) {
        let r = t.parent;
        const o = t.proxy,
            l = i;
        for (; r;) {
            const u = r.ec;
            if (u) {
                for (let f = 0; f < u.length; f++)
                    if (u[f](e, o, l) === !1) return
            }
            r = r.parent
        }
        const c = t.appContext.config.errorHandler;
        if (c) {
            st(c, null, 10, [e, o, l]);
            return
        }
    }
    ro(e, i, n, s)
}

function ro(e, t, i, s = !0) {
    console.error(e)
}
let si = !1,
    ki = !1;
const ge = [];
let Ue = 0;
const St = [];
let Ze = null,
    ft = 0;
const Pn = Promise.resolve();
let ls = null;

function cs(e) {
    const t = ls || Pn;
    return e ? t.then(this ? e.bind(this) : e) : t
}

function oo(e) {
    let t = Ue + 1,
        i = ge.length;
    for (; t < i;) {
        const s = t + i >>> 1;
        Ht(ge[s]) < e ? t = s + 1 : i = s
    }
    return t
}

function as(e) {
    (!ge.length || !ge.includes(e, si && e.allowRecurse ? Ue + 1 : Ue)) && (e.id == null ? ge.push(e) : ge.splice(oo(e.id), 0, e), Tn())
}

function Tn() {
    !si && !ki && (ki = !0, ls = Pn.then(An))
}

function lo(e) {
    const t = ge.indexOf(e);
    t > Ue && ge.splice(t, 1)
}

function co(e) {
    N(e) ? St.push(...e) : (!Ze || !Ze.includes(e, e.allowRecurse ? ft + 1 : ft)) && St.push(e), Tn()
}

function Ds(e, t = Ue) {
    for (; t < ge.length; t++) {
        const i = ge[t];
        i && i.pre && (ge.splice(t, 1), t--, i())
    }
}

function Dn(e) {
    if (St.length) {
        const t = [...new Set(St)];
        if (St.length = 0, Ze) {
            Ze.push(...t);
            return
        }
        for (Ze = t, Ze.sort((i, s) => Ht(i) - Ht(s)), ft = 0; ft < Ze.length; ft++) Ze[ft]();
        Ze = null, ft = 0
    }
}
const Ht = e => e.id == null ? 1 / 0 : e.id,
    ao = (e, t) => {
        const i = Ht(e) - Ht(t);
        if (i === 0) {
            if (e.pre && !t.pre) return -1;
            if (t.pre && !e.pre) return 1
        }
        return i
    };

function An(e) {
    ki = !1, si = !0, ge.sort(ao);
    const t = Re;
    try {
        for (Ue = 0; Ue < ge.length; Ue++) {
            const i = ge[Ue];
            i && i.active !== !1 && st(i, null, 14)
        }
    } finally {
        Ue = 0, ge.length = 0, Dn(), si = !1, ls = null, (ge.length || St.length) && An()
    }
}

function uo(e, t, ...i) {
    if (e.isUnmounted) return;
    const s = e.vnode.props || se;
    let n = i;
    const r = t.startsWith("update:"),
        o = r && t.slice(7);
    if (o && o in s) {
        const f = `${o==="modelValue"?"model":o}Modifiers`,
            {
                number: p,
                trim: b
            } = s[f] || se;
        b && (n = i.map(T => T.trim())), p && (n = i.map(zi))
    }
    let l, c = s[l = qt(t)] || s[l = qt(qe(t))];
    !c && r && (c = s[l = qt(wt(t))]), c && Be(c, e, 6, n);
    const u = s[l + "Once"];
    if (u) {
        if (!e.emitted) e.emitted = {};
        else if (e.emitted[l]) return;
        e.emitted[l] = !0, Be(u, e, 6, n)
    }
}

function En(e, t, i = !1) {
    const s = t.emitsCache,
        n = s.get(e);
    if (n !== void 0) return n;
    const r = e.emits;
    let o = {},
        l = !1;
    if (!Y(e)) {
        const c = u => {
            const f = En(u, t, !0);
            f && (l = !0, Me(o, f))
        };
        !i && t.mixins.length && t.mixins.forEach(c), e.extends && c(e.extends), e.mixins && e.mixins.forEach(c)
    }
    return !r && !l ? (re(e) && s.set(e, null), null) : (N(r) ? r.forEach(c => o[c] = null) : Me(o, r), re(e) && s.set(e, o), o)
}

function gi(e, t) {
    return !e || !ui(t) ? !1 : (t = t.slice(2).replace(/Once$/, ""), U(e, t[0].toLowerCase() + t.slice(1)) || U(e, wt(t)) || U(e, t))
}
let Se = null,
    yi = null;

function ni(e) {
    const t = Se;
    return Se = e, yi = e && e.type.__scopeId || null, t
}

function fo(e) {
    yi = e
}

function ho() {
    yi = null
}
const po = e => We;

function We(e, t = Se, i) {
    if (!t || e._n) return e;
    const s = (...n) => {
        s._d && js(-1);
        const r = ni(t),
            o = e(...n);
        return ni(r), s._d && js(1), o
    };
    return s._n = !0, s._c = !0, s._d = !0, s
}

function xi(e) {
    const {
        type: t,
        vnode: i,
        proxy: s,
        withProxy: n,
        props: r,
        propsOptions: [o],
        slots: l,
        attrs: c,
        emit: u,
        render: f,
        renderCache: p,
        data: b,
        setupState: T,
        ctx: M,
        inheritAttrs: R
    } = e;
    let y, L;
    const Q = ni(e);
    try {
        if (i.shapeFlag & 4) {
            const J = n || s;
            y = Ye(f.call(J, J, p, r, T, b, M)), L = c
        } else {
            const J = t;
            y = Ye(J.length > 1 ? J(r, {
                attrs: c,
                slots: l,
                emit: u
            }) : J(r, null)), L = t.props ? c : mo(c)
        }
    } catch (J) {
        Et.length = 0, _i(J, e, 1), y = ce(rt)
    }
    let E = y;
    if (L && R !== !1) {
        const J = Object.keys(L),
            {
                shapeFlag: ye
            } = E;
        J.length && ye & 7 && (o && J.some(qi) && (L = _o(L, o)), E = Mt(E, L))
    }
    return i.dirs && (E = Mt(E), E.dirs = E.dirs ? E.dirs.concat(i.dirs) : i.dirs), i.transition && (E.transition = i.transition), y = E, ni(Q), y
}
const mo = e => {
        let t;
        for (const i in e)(i === "class" || i === "style" || ui(i)) && ((t || (t = {}))[i] = e[i]);
        return t
    },
    _o = (e, t) => {
        const i = {};
        for (const s in e)(!qi(s) || !(s.slice(9) in t)) && (i[s] = e[s]);
        return i
    };

function go(e, t, i) {
    const {
        props: s,
        children: n,
        component: r
    } = e, {
        props: o,
        children: l,
        patchFlag: c
    } = t, u = r.emitsOptions;
    if (t.dirs || t.transition) return !0;
    if (i && c >= 0) {
        if (c & 1024) return !0;
        if (c & 16) return s ? As(s, o, u) : !!o;
        if (c & 8) {
            const f = t.dynamicProps;
            for (let p = 0; p < f.length; p++) {
                const b = f[p];
                if (o[b] !== s[b] && !gi(u, b)) return !0
            }
        }
    } else return (n || l) && (!l || !l.$stable) ? !0 : s === o ? !1 : s ? o ? As(s, o, u) : !0 : !!o;
    return !1
}

function As(e, t, i) {
    const s = Object.keys(t);
    if (s.length !== Object.keys(e).length) return !0;
    for (let n = 0; n < s.length; n++) {
        const r = s[n];
        if (t[r] !== e[r] && !gi(i, r)) return !0
    }
    return !1
}

function yo({
    vnode: e,
    parent: t
}, i) {
    for (; t && t.subTree === e;)(e = t.vnode).el = i, t = t.parent
}
const wo = e => e.__isSuspense;

function vo(e, t) {
    t && t.pendingBranch ? N(e) ? t.effects.push(...e) : t.effects.push(e) : co(e)
}

function bo(e, t) {
    if (me) {
        let i = me.provides;
        const s = me.parent && me.parent.provides;
        s === i && (i = me.provides = Object.create(s)), i[e] = t
    }
}

function Si(e, t, i = !1) {
    const s = me || Se;
    if (s) {
        const n = s.parent == null ? s.vnode.appContext && s.vnode.appContext.provides : s.parent.provides;
        if (n && e in n) return n[e];
        if (arguments.length > 1) return i && Y(t) ? t.call(s.proxy) : t
    }
}
const Es = {};

function Mi(e, t, i) {
    return kn(e, t, i)
}

function kn(e, t, {
    immediate: i,
    deep: s,
    flush: n,
    onTrack: r,
    onTrigger: o
} = se) {
    const l = me;
    let c, u = !1,
        f = !1;
    if (xe(e) ? (c = () => e.value, u = Ei(e)) : xt(e) ? (c = () => e, s = !0) : N(e) ? (f = !0, u = e.some(L => xt(L) || Ei(L)), c = () => e.map(L => {
            if (xe(L)) return L.value;
            if (xt(L)) return _t(L);
            if (Y(L)) return st(L, l, 2)
        })) : Y(e) ? t ? c = () => st(e, l, 2) : c = () => {
            if (!(l && l.isUnmounted)) return p && p(), Be(e, l, 3, [b])
        } : c = Re, t && s) {
        const L = c;
        c = () => _t(L())
    }
    let p, b = L => {
        p = y.onStop = () => {
            st(L, l, 4)
        }
    };
    if (Rt) return b = Re, t ? i && Be(t, l, 3, [c(), f ? [] : void 0, b]) : c(), Re;
    let T = f ? [] : Es;
    const M = () => {
        if (!!y.active)
            if (t) {
                const L = y.run();
                (s || u || (f ? L.some((Q, E) => ti(Q, T[E])) : ti(L, T))) && (p && p(), Be(t, l, 3, [L, T === Es ? void 0 : T, b]), T = L)
            } else y.run()
    };
    M.allowRecurse = !!t;
    let R;
    n === "sync" ? R = M : n === "post" ? R = () => $e(M, l && l.suspense) : (M.pre = !0, l && (M.id = l.uid), R = () => as(M));
    const y = new Qi(c, R);
    return t ? i ? M() : T = y.run() : n === "post" ? $e(y.run.bind(y), l && l.suspense) : y.run(), () => {
        y.stop(), l && l.scope && Zi(l.scope.effects, y)
    }
}

function Co(e, t, i) {
    const s = this.proxy,
        n = de(e) ? e.includes(".") ? Ln(s, e) : () => s[e] : e.bind(s, s);
    let r;
    Y(t) ? r = t : (r = t.handler, i = t);
    const o = me;
    $t(this);
    const l = kn(n, r.bind(s), i);
    return o ? $t(o) : yt(), l
}

function Ln(e, t) {
    const i = t.split(".");
    return () => {
        let s = e;
        for (let n = 0; n < i.length && s; n++) s = s[i[n]];
        return s
    }
}

function _t(e, t) {
    if (!re(e) || e.__v_skip || (t = t || new Set, t.has(e))) return e;
    if (t.add(e), xe(e)) _t(e.value, t);
    else if (N(e))
        for (let i = 0; i < e.length; i++) _t(e[i], t);
    else if (fi(e) || Ct(e)) e.forEach(i => {
        _t(i, t)
    });
    else if (pn(e))
        for (const i in e) _t(e[i], t);
    return e
}
const At = e => !!e.type.__asyncLoader,
    Hn = e => e.type.__isKeepAlive;

function xo(e, t) {
    Fn(e, "a", t)
}

function So(e, t) {
    Fn(e, "da", t)
}

function Fn(e, t, i = me) {
    const s = e.__wdc || (e.__wdc = () => {
        let n = i;
        for (; n;) {
            if (n.isDeactivated) return;
            n = n.parent
        }
        return e()
    });
    if (wi(t, s, i), i) {
        let n = i.parent;
        for (; n && n.parent;) Hn(n.parent.vnode) && Mo(s, t, i, n), n = n.parent
    }
}

function Mo(e, t, i, s) {
    const n = wi(t, e, s, !0);
    Rn(() => {
        Zi(s[t], n)
    }, i)
}

function wi(e, t, i = me, s = !1) {
    if (i) {
        const n = i[e] || (i[e] = []),
            r = t.__weh || (t.__weh = (...o) => {
                if (i.isUnmounted) return;
                Ot(), $t(i);
                const l = Be(t, i, e, o);
                return yt(), zt(), l
            });
        return s ? n.unshift(r) : n.push(r), r
    }
}
const Xe = e => (t, i = me) => (!Rt || e === "sp") && wi(e, t, i),
    $o = Xe("bm"),
    Io = Xe("m"),
    Oo = Xe("bu"),
    zo = Xe("u"),
    Po = Xe("bum"),
    Rn = Xe("um"),
    To = Xe("sp"),
    Do = Xe("rtg"),
    Ao = Xe("rtc");

function Eo(e, t = me) {
    wi("ec", e, t)
}

function Jt(e, t) {
    const i = Se;
    if (i === null) return e;
    const s = bi(i) || i.proxy,
        n = e.dirs || (e.dirs = []);
    for (let r = 0; r < t.length; r++) {
        let [o, l, c, u = se] = t[r];
        Y(o) && (o = {
            mounted: o,
            updated: o
        }), o.deep && _t(l), n.push({
            dir: o,
            instance: s,
            value: l,
            oldValue: void 0,
            arg: c,
            modifiers: u
        })
    }
    return e
}

function at(e, t, i, s) {
    const n = e.dirs,
        r = t && t.dirs;
    for (let o = 0; o < n.length; o++) {
        const l = n[o];
        r && (l.oldValue = r[o].value);
        let c = l.dir[s];
        c && (Ot(), Be(c, i, 8, [e.el, l, e, t]), zt())
    }
}
const ds = "components",
    ko = "directives";

function _e(e, t) {
    return us(ds, e, !0, t) || e
}
const Bn = Symbol();

function ks(e) {
    return de(e) ? us(ds, e, !1) || e : e || Bn
}

function Lo(e) {
    return us(ko, e)
}

function us(e, t, i = !0, s = !1) {
    const n = Se || me;
    if (n) {
        const r = n.type;
        if (e === ds) {
            const l = ul(r, !1);
            if (l && (l === t || l === qe(t) || l === pi(qe(t)))) return r
        }
        const o = Ls(n[e] || r[e], t) || Ls(n.appContext[e], t);
        return !o && s ? r : o
    }
}

function Ls(e, t) {
    return e && (e[t] || e[qe(t)] || e[pi(qe(t))])
}

function Ho(e, t, i, s) {
    let n;
    const r = i && i[s];
    if (N(e) || de(e)) {
        n = new Array(e.length);
        for (let o = 0, l = e.length; o < l; o++) n[o] = t(e[o], o, void 0, r && r[o])
    } else if (typeof e == "number") {
        n = new Array(e);
        for (let o = 0; o < e; o++) n[o] = t(o + 1, o, void 0, r && r[o])
    } else if (re(e))
        if (e[Symbol.iterator]) n = Array.from(e, (o, l) => t(o, l, void 0, r && r[l]));
        else {
            const o = Object.keys(e);
            n = new Array(o.length);
            for (let l = 0, c = o.length; l < c; l++) {
                const u = o[l];
                n[l] = t(e[u], u, l, r && r[l])
            }
        }
    else n = [];
    return i && (i[s] = n), n
}

function tt(e, t, i = {}, s, n) {
    if (Se.isCE || Se.parent && At(Se.parent) && Se.parent.isCE) return ce("slot", t === "default" ? null : {
        name: t
    }, s && s());
    let r = e[t];
    r && r._c && (r._d = !1), V();
    const o = r && Nn(r(i)),
        l = De(Te, {
            key: i.key || o && o.key || `_${t}`
        }, o || (s ? s() : []), o && e._ === 1 ? 64 : -2);
    return !n && l.scopeId && (l.slotScopeIds = [l.scopeId + "-s"]), r && r._c && (r._d = !0), l
}

function Nn(e) {
    return e.some(t => li(t) ? !(t.type === rt || t.type === Te && !Nn(t.children)) : !0) ? e : null
}

function Fo(e, t) {
    const i = {};
    for (const s in e) i[t && /[A-Z]/.test(s) ? `on:${s}` : qt(s)] = e[s];
    return i
}
const Li = e => e ? er(e) ? bi(e) || e.proxy : Li(e.parent) : null,
    ri = Me(Object.create(null), {
        $: e => e,
        $el: e => e.vnode.el,
        $data: e => e.data,
        $props: e => e.props,
        $attrs: e => e.attrs,
        $slots: e => e.slots,
        $refs: e => e.refs,
        $parent: e => Li(e.parent),
        $root: e => Li(e.root),
        $emit: e => e.emit,
        $options: e => jn(e),
        $forceUpdate: e => e.f || (e.f = () => as(e.update)),
        $nextTick: e => e.n || (e.n = cs.bind(e.proxy)),
        $watch: e => Co.bind(e)
    }),
    Ro = {
        get({
            _: e
        }, t) {
            const {
                ctx: i,
                setupState: s,
                data: n,
                props: r,
                accessCache: o,
                type: l,
                appContext: c
            } = e;
            let u;
            if (t[0] !== "$") {
                const T = o[t];
                if (T !== void 0) switch (T) {
                    case 1:
                        return s[t];
                    case 2:
                        return n[t];
                    case 4:
                        return i[t];
                    case 3:
                        return r[t]
                } else {
                    if (s !== se && U(s, t)) return o[t] = 1, s[t];
                    if (n !== se && U(n, t)) return o[t] = 2, n[t];
                    if ((u = e.propsOptions[0]) && U(u, t)) return o[t] = 3, r[t];
                    if (i !== se && U(i, t)) return o[t] = 4, i[t];
                    Hi && (o[t] = 0)
                }
            }
            const f = ri[t];
            let p, b;
            if (f) return t === "$attrs" && ze(e, "get", t), f(e);
            if ((p = l.__cssModules) && (p = p[t])) return p;
            if (i !== se && U(i, t)) return o[t] = 4, i[t];
            if (b = c.config.globalProperties, U(b, t)) return b[t]
        },
        set({
            _: e
        }, t, i) {
            const {
                data: s,
                setupState: n,
                ctx: r
            } = e;
            return n !== se && U(n, t) ? (n[t] = i, !0) : s !== se && U(s, t) ? (s[t] = i, !0) : U(e.props, t) || t[0] === "$" && t.slice(1) in e ? !1 : (r[t] = i, !0)
        },
        has({
            _: {
                data: e,
                setupState: t,
                accessCache: i,
                ctx: s,
                appContext: n,
                propsOptions: r
            }
        }, o) {
            let l;
            return !!i[o] || e !== se && U(e, o) || t !== se && U(t, o) || (l = r[0]) && U(l, o) || U(s, o) || U(ri, o) || U(n.config.globalProperties, o)
        },
        defineProperty(e, t, i) {
            return i.get != null ? e._.accessCache[t] = 0 : U(i, "value") && this.set(e, t, i.value, null), Reflect.defineProperty(e, t, i)
        }
    };
let Hi = !0;

function Bo(e) {
    const t = jn(e),
        i = e.proxy,
        s = e.ctx;
    Hi = !1, t.beforeCreate && Hs(t.beforeCreate, e, "bc");
    const {
        data: n,
        computed: r,
        methods: o,
        watch: l,
        provide: c,
        inject: u,
        created: f,
        beforeMount: p,
        mounted: b,
        beforeUpdate: T,
        updated: M,
        activated: R,
        deactivated: y,
        beforeDestroy: L,
        beforeUnmount: Q,
        destroyed: E,
        unmounted: J,
        render: ye,
        renderTracked: ae,
        renderTriggered: he,
        errorCaptured: ue,
        serverPrefetch: q,
        expose: $,
        inheritAttrs: ee,
        components: we,
        directives: k,
        filters: I
    } = t;
    if (u && No(u, s, null, e.appContext.config.unwrapInjectedRef), o)
        for (const w in o) {
            const P = o[w];
            Y(P) && (s[w] = P.bind(i))
        }
    if (n) {
        const w = n.call(i, i);
        re(w) && (e.data = is(w))
    }
    if (Hi = !0, r)
        for (const w in r) {
            const P = r[w],
                B = Y(P) ? P.bind(i, i) : Y(P.get) ? P.get.bind(i, i) : Re,
                Z = !Y(P) && Y(P.set) ? P.set.bind(i) : Re,
                ne = hl({
                    get: B,
                    set: Z
                });
            Object.defineProperty(s, w, {
                enumerable: !0,
                configurable: !0,
                get: () => ne.value,
                set: X => ne.value = X
            })
        }
    if (l)
        for (const w in l) Vn(l[w], s, i, w);
    if (c) {
        const w = Y(c) ? c.call(i) : c;
        Reflect.ownKeys(w).forEach(P => {
            bo(P, w[P])
        })
    }
    f && Hs(f, e, "c");

    function O(w, P) {
        N(P) ? P.forEach(B => w(B.bind(i))) : P && w(P.bind(i))
    }
    if (O($o, p), O(Io, b), O(Oo, T), O(zo, M), O(xo, R), O(So, y), O(Eo, ue), O(Ao, ae), O(Do, he), O(Po, Q), O(Rn, J), O(To, q), N($))
        if ($.length) {
            const w = e.exposed || (e.exposed = {});
            $.forEach(P => {
                Object.defineProperty(w, P, {
                    get: () => i[P],
                    set: B => i[P] = B
                })
            })
        } else e.exposed || (e.exposed = {});
    ye && e.render === Re && (e.render = ye), ee != null && (e.inheritAttrs = ee), we && (e.components = we), k && (e.directives = k)
}

function No(e, t, i = Re, s = !1) {
    N(e) && (e = Fi(e));
    for (const n in e) {
        const r = e[n];
        let o;
        re(r) ? "default" in r ? o = Si(r.from || n, r.default, !0) : o = Si(r.from || n) : o = Si(r), xe(o) && s ? Object.defineProperty(t, n, {
            enumerable: !0,
            configurable: !0,
            get: () => o.value,
            set: l => o.value = l
        }) : t[n] = o
    }
}

function Hs(e, t, i) {
    Be(N(e) ? e.map(s => s.bind(t.proxy)) : e.bind(t.proxy), t, i)
}

function Vn(e, t, i, s) {
    const n = s.includes(".") ? Ln(i, s) : () => i[s];
    if (de(e)) {
        const r = t[e];
        Y(r) && Mi(n, r)
    } else if (Y(e)) Mi(n, e.bind(i));
    else if (re(e))
        if (N(e)) e.forEach(r => Vn(r, t, i, s));
        else {
            const r = Y(e.handler) ? e.handler.bind(i) : t[e.handler];
            Y(r) && Mi(n, r, e)
        }
}

function jn(e) {
    const t = e.type,
        {
            mixins: i,
            extends: s
        } = t,
        {
            mixins: n,
            optionsCache: r,
            config: {
                optionMergeStrategies: o
            }
        } = e.appContext,
        l = r.get(t);
    let c;
    return l ? c = l : !n.length && !i && !s ? c = t : (c = {}, n.length && n.forEach(u => oi(c, u, o, !0)), oi(c, t, o)), re(t) && r.set(t, c), c
}

function oi(e, t, i, s = !1) {
    const {
        mixins: n,
        extends: r
    } = t;
    r && oi(e, r, i, !0), n && n.forEach(o => oi(e, o, i, !0));
    for (const o in t)
        if (!(s && o === "expose")) {
            const l = Vo[o] || i && i[o];
            e[o] = l ? l(e[o], t[o]) : t[o]
        } return e
}
const Vo = {
    data: Fs,
    props: ut,
    emits: ut,
    methods: ut,
    computed: ut,
    beforeCreate: Ce,
    created: Ce,
    beforeMount: Ce,
    mounted: Ce,
    beforeUpdate: Ce,
    updated: Ce,
    beforeDestroy: Ce,
    beforeUnmount: Ce,
    destroyed: Ce,
    unmounted: Ce,
    activated: Ce,
    deactivated: Ce,
    errorCaptured: Ce,
    serverPrefetch: Ce,
    components: ut,
    directives: ut,
    watch: Yo,
    provide: Fs,
    inject: jo
};

function Fs(e, t) {
    return t ? e ? function() {
        return Me(Y(e) ? e.call(this, this) : e, Y(t) ? t.call(this, this) : t)
    } : t : e
}

function jo(e, t) {
    return ut(Fi(e), Fi(t))
}

function Fi(e) {
    if (N(e)) {
        const t = {};
        for (let i = 0; i < e.length; i++) t[e[i]] = e[i];
        return t
    }
    return e
}

function Ce(e, t) {
    return e ? [...new Set([].concat(e, t))] : t
}

function ut(e, t) {
    return e ? Me(Me(Object.create(null), e), t) : t
}

function Yo(e, t) {
    if (!e) return t;
    if (!t) return e;
    const i = Me(Object.create(null), e);
    for (const s in t) i[s] = Ce(e[s], t[s]);
    return i
}

function Uo(e, t, i, s = !1) {
    const n = {},
        r = {};
    ii(r, vi, 1), e.propsDefaults = Object.create(null), Yn(e, t, n, r);
    for (const o in e.propsOptions[0]) o in n || (n[o] = void 0);
    i ? e.props = s ? n : Mn(n) : e.type.props ? e.props = n : e.props = r, e.attrs = r
}

function Wo(e, t, i, s) {
    const {
        props: n,
        attrs: r,
        vnode: {
            patchFlag: o
        }
    } = e, l = G(n), [c] = e.propsOptions;
    let u = !1;
    if ((s || o > 0) && !(o & 16)) {
        if (o & 8) {
            const f = e.vnode.dynamicProps;
            for (let p = 0; p < f.length; p++) {
                let b = f[p];
                if (gi(e.emitsOptions, b)) continue;
                const T = t[b];
                if (c)
                    if (U(r, b)) T !== r[b] && (r[b] = T, u = !0);
                    else {
                        const M = qe(b);
                        n[M] = Ri(c, l, M, T, e, !1)
                    }
                else T !== r[b] && (r[b] = T, u = !0)
            }
        }
    } else {
        Yn(e, t, n, r) && (u = !0);
        let f;
        for (const p in l)(!t || !U(t, p) && ((f = wt(p)) === p || !U(t, f))) && (c ? i && (i[p] !== void 0 || i[f] !== void 0) && (n[p] = Ri(c, l, p, void 0, e, !0)) : delete n[p]);
        if (r !== l)
            for (const p in r)(!t || !U(t, p) && !0) && (delete r[p], u = !0)
    }
    u && Je(e, "set", "$attrs")
}

function Yn(e, t, i, s) {
    const [n, r] = e.propsOptions;
    let o = !1,
        l;
    if (t)
        for (let c in t) {
            if (Kt(c)) continue;
            const u = t[c];
            let f;
            n && U(n, f = qe(c)) ? !r || !r.includes(f) ? i[f] = u : (l || (l = {}))[f] = u : gi(e.emitsOptions, c) || (!(c in s) || u !== s[c]) && (s[c] = u, o = !0)
        }
    if (r) {
        const c = G(i),
            u = l || se;
        for (let f = 0; f < r.length; f++) {
            const p = r[f];
            i[p] = Ri(n, c, p, u[p], e, !U(u, p))
        }
    }
    return o
}

function Ri(e, t, i, s, n, r) {
    const o = e[i];
    if (o != null) {
        const l = U(o, "default");
        if (l && s === void 0) {
            const c = o.default;
            if (o.type !== Function && Y(c)) {
                const {
                    propsDefaults: u
                } = n;
                i in u ? s = u[i] : ($t(n), s = u[i] = c.call(null, t), yt())
            } else s = c
        }
        o[0] && (r && !l ? s = !1 : o[1] && (s === "" || s === wt(i)) && (s = !0))
    }
    return s
}

function Un(e, t, i = !1) {
    const s = t.propsCache,
        n = s.get(e);
    if (n) return n;
    const r = e.props,
        o = {},
        l = [];
    let c = !1;
    if (!Y(e)) {
        const f = p => {
            c = !0;
            const [b, T] = Un(p, t, !0);
            Me(o, b), T && l.push(...T)
        };
        !i && t.mixins.length && t.mixins.forEach(f), e.extends && f(e.extends), e.mixins && e.mixins.forEach(f)
    }
    if (!r && !c) return re(e) && s.set(e, bt), bt;
    if (N(r))
        for (let f = 0; f < r.length; f++) {
            const p = qe(r[f]);
            Rs(p) && (o[p] = se)
        } else if (r)
            for (const f in r) {
                const p = qe(f);
                if (Rs(p)) {
                    const b = r[f],
                        T = o[p] = N(b) || Y(b) ? {
                            type: b
                        } : b;
                    if (T) {
                        const M = Vs(Boolean, T.type),
                            R = Vs(String, T.type);
                        T[0] = M > -1, T[1] = R < 0 || M < R, (M > -1 || U(T, "default")) && l.push(p)
                    }
                }
            }
    const u = [o, l];
    return re(e) && s.set(e, u), u
}

function Rs(e) {
    return e[0] !== "$"
}

function Bs(e) {
    const t = e && e.toString().match(/^\s*function (\w+)/);
    return t ? t[1] : e === null ? "null" : ""
}

function Ns(e, t) {
    return Bs(e) === Bs(t)
}

function Vs(e, t) {
    return N(t) ? t.findIndex(i => Ns(i, e)) : Y(t) && Ns(t, e) ? 0 : -1
}
const Wn = e => e[0] === "_" || e === "$stable",
    fs = e => N(e) ? e.map(Ye) : [Ye(e)],
    Ko = (e, t, i) => {
        if (t._n) return t;
        const s = We((...n) => fs(t(...n)), i);
        return s._c = !1, s
    },
    Kn = (e, t, i) => {
        const s = e._ctx;
        for (const n in e) {
            if (Wn(n)) continue;
            const r = e[n];
            if (Y(r)) t[n] = Ko(n, r, s);
            else if (r != null) {
                const o = fs(r);
                t[n] = () => o
            }
        }
    },
    qn = (e, t) => {
        const i = fs(t);
        e.slots.default = () => i
    },
    qo = (e, t) => {
        if (e.vnode.shapeFlag & 32) {
            const i = t._;
            i ? (e.slots = G(t), ii(t, "_", i)) : Kn(t, e.slots = {})
        } else e.slots = {}, t && qn(e, t);
        ii(e.slots, vi, 1)
    },
    Zo = (e, t, i) => {
        const {
            vnode: s,
            slots: n
        } = e;
        let r = !0,
            o = se;
        if (s.shapeFlag & 32) {
            const l = t._;
            l ? i && l === 1 ? r = !1 : (Me(n, t), !i && l === 1 && delete n._) : (r = !t.$stable, Kn(t, n)), o = t
        } else t && (qn(e, t), o = {
            default: 1
        });
        if (r)
            for (const l in n) !Wn(l) && !(l in o) && delete n[l]
    };

function Zn() {
    return {
        app: null,
        config: {
            isNativeTag: wr,
            performance: !1,
            globalProperties: {},
            optionMergeStrategies: {},
            errorHandler: void 0,
            warnHandler: void 0,
            compilerOptions: {}
        },
        mixins: [],
        components: {},
        directives: {},
        provides: Object.create(null),
        optionsCache: new WeakMap,
        propsCache: new WeakMap,
        emitsCache: new WeakMap
    }
}
let Jo = 0;

function Xo(e, t) {
    return function(s, n = null) {
        Y(s) || (s = Object.assign({}, s)), n != null && !re(n) && (n = null);
        const r = Zn(),
            o = new Set;
        let l = !1;
        const c = r.app = {
            _uid: Jo++,
            _component: s,
            _props: n,
            _container: null,
            _context: r,
            _instance: null,
            version: ml,
            get config() {
                return r.config
            },
            set config(u) {},
            use(u, ...f) {
                return o.has(u) || (u && Y(u.install) ? (o.add(u), u.install(c, ...f)) : Y(u) && (o.add(u), u(c, ...f))), c
            },
            mixin(u) {
                return r.mixins.includes(u) || r.mixins.push(u), c
            },
            component(u, f) {
                return f ? (r.components[u] = f, c) : r.components[u]
            },
            directive(u, f) {
                return f ? (r.directives[u] = f, c) : r.directives[u]
            },
            mount(u, f, p) {
                if (!l) {
                    const b = ce(s, n);
                    return b.appContext = r, f && t ? t(b, u) : e(b, u, p), l = !0, c._container = u, u.__vue_app__ = c, bi(b.component) || b.component.proxy
                }
            },
            unmount() {
                l && (e(null, c._container), delete c._container.__vue_app__)
            },
            provide(u, f) {
                return r.provides[u] = f, c
            }
        };
        return c
    }
}

function Bi(e, t, i, s, n = !1) {
    if (N(e)) {
        e.forEach((b, T) => Bi(b, t && (N(t) ? t[T] : t), i, s, n));
        return
    }
    if (At(s) && !n) return;
    const r = s.shapeFlag & 4 ? bi(s.component) || s.component.proxy : s.el,
        o = n ? null : r,
        {
            i: l,
            r: c
        } = e,
        u = t && t.r,
        f = l.refs === se ? l.refs = {} : l.refs,
        p = l.setupState;
    if (u != null && u !== c && (de(u) ? (f[u] = null, U(p, u) && (p[u] = null)) : xe(u) && (u.value = null)), Y(c)) st(c, l, 12, [o, f]);
    else {
        const b = de(c),
            T = xe(c);
        if (b || T) {
            const M = () => {
                if (e.f) {
                    const R = b ? f[c] : c.value;
                    n ? N(R) && Zi(R, r) : N(R) ? R.includes(r) || R.push(r) : b ? (f[c] = [r], U(p, c) && (p[c] = f[c])) : (c.value = [r], e.k && (f[e.k] = c.value))
                } else b ? (f[c] = o, U(p, c) && (p[c] = o)) : T && (c.value = o, e.k && (f[e.k] = o))
            };
            o ? (M.id = -1, $e(M, i)) : M()
        }
    }
}
const $e = vo;

function Qo(e) {
    return Go(e)
}

function Go(e, t) {
    const i = Mr();
    i.__VUE__ = !0;
    const {
        insert: s,
        remove: n,
        patchProp: r,
        createElement: o,
        createText: l,
        createComment: c,
        setText: u,
        setElementText: f,
        parentNode: p,
        nextSibling: b,
        setScopeId: T = Re,
        cloneNode: M,
        insertStaticContent: R
    } = e, y = (a, d, h, _ = null, m = null, C = null, z = !1, v = null, S = !!d.dynamicChildren) => {
        if (a === d) return;
        a && !Tt(a, d) && (_ = ke(a), oe(a, m, C, !0), a = null), d.patchFlag === -2 && (S = !1, d.dynamicChildren = null);
        const {
            type: g,
            ref: H,
            shapeFlag: D
        } = d;
        switch (g) {
            case hs:
                L(a, d, h, _);
                break;
            case rt:
                Q(a, d, h, _);
                break;
            case Xt:
                a == null && E(d, h, _, z);
                break;
            case Te:
                k(a, d, h, _, m, C, z, v, S);
                break;
            default:
                D & 1 ? ae(a, d, h, _, m, C, z, v, S) : D & 6 ? I(a, d, h, _, m, C, z, v, S) : (D & 64 || D & 128) && g.process(a, d, h, _, m, C, z, v, S, be)
        }
        H != null && m && Bi(H, a && a.ref, C, d || a, !d)
    }, L = (a, d, h, _) => {
        if (a == null) s(d.el = l(d.children), h, _);
        else {
            const m = d.el = a.el;
            d.children !== a.children && u(m, d.children)
        }
    }, Q = (a, d, h, _) => {
        a == null ? s(d.el = c(d.children || ""), h, _) : d.el = a.el
    }, E = (a, d, h, _) => {
        [a.el, a.anchor] = R(a.children, d, h, _, a.el, a.anchor)
    }, J = ({
        el: a,
        anchor: d
    }, h, _) => {
        let m;
        for (; a && a !== d;) m = b(a), s(a, h, _), a = m;
        s(d, h, _)
    }, ye = ({
        el: a,
        anchor: d
    }) => {
        let h;
        for (; a && a !== d;) h = b(a), n(a), a = h;
        n(d)
    }, ae = (a, d, h, _, m, C, z, v, S) => {
        z = z || d.type === "svg", a == null ? he(d, h, _, m, C, z, v, S) : $(a, d, m, C, z, v, S)
    }, he = (a, d, h, _, m, C, z, v) => {
        let S, g;
        const {
            type: H,
            props: D,
            shapeFlag: F,
            transition: j,
            patchFlag: W,
            dirs: te
        } = a;
        if (a.el && M !== void 0 && W === -1) S = a.el = M(a.el);
        else {
            if (S = a.el = o(a.type, C, D && D.is, D), F & 8 ? f(S, a.children) : F & 16 && q(a.children, S, null, _, m, C && H !== "foreignObject", z, v), te && at(a, null, _, "created"), D) {
                for (const le in D) le !== "value" && !Kt(le) && r(S, le, null, D[le], C, a.children, _, m, ve);
                "value" in D && r(S, "value", null, D.value), (g = D.onVnodeBeforeMount) && Ve(g, _, a)
            }
            ue(S, a, a.scopeId, z, _)
        }
        te && at(a, null, _, "beforeMount");
        const ie = (!m || m && !m.pendingBranch) && j && !j.persisted;
        ie && j.beforeEnter(S), s(S, d, h), ((g = D && D.onVnodeMounted) || ie || te) && $e(() => {
            g && Ve(g, _, a), ie && j.enter(S), te && at(a, null, _, "mounted")
        }, m)
    }, ue = (a, d, h, _, m) => {
        if (h && T(a, h), _)
            for (let C = 0; C < _.length; C++) T(a, _[C]);
        if (m) {
            let C = m.subTree;
            if (d === C) {
                const z = m.vnode;
                ue(a, z, z.scopeId, z.slotScopeIds, m.parent)
            }
        }
    }, q = (a, d, h, _, m, C, z, v, S = 0) => {
        for (let g = S; g < a.length; g++) {
            const H = a[g] = v ? et(a[g]) : Ye(a[g]);
            y(null, H, d, h, _, m, C, z, v)
        }
    }, $ = (a, d, h, _, m, C, z) => {
        const v = d.el = a.el;
        let {
            patchFlag: S,
            dynamicChildren: g,
            dirs: H
        } = d;
        S |= a.patchFlag & 16;
        const D = a.props || se,
            F = d.props || se;
        let j;
        h && dt(h, !1), (j = F.onVnodeBeforeUpdate) && Ve(j, h, d, a), H && at(d, a, h, "beforeUpdate"), h && dt(h, !0);
        const W = m && d.type !== "foreignObject";
        if (g ? ee(a.dynamicChildren, g, v, h, _, W, C) : z || B(a, d, v, null, h, _, W, C, !1), S > 0) {
            if (S & 16) we(v, d, D, F, h, _, m);
            else if (S & 2 && D.class !== F.class && r(v, "class", null, F.class, m), S & 4 && r(v, "style", D.style, F.style, m), S & 8) {
                const te = d.dynamicProps;
                for (let ie = 0; ie < te.length; ie++) {
                    const le = te[ie],
                        Le = D[le],
                        vt = F[le];
                    (vt !== Le || le === "value") && r(v, le, Le, vt, m, a.children, h, _, ve)
                }
            }
            S & 1 && a.children !== d.children && f(v, d.children)
        } else !z && g == null && we(v, d, D, F, h, _, m);
        ((j = F.onVnodeUpdated) || H) && $e(() => {
            j && Ve(j, h, d, a), H && at(d, a, h, "updated")
        }, _)
    }, ee = (a, d, h, _, m, C, z) => {
        for (let v = 0; v < d.length; v++) {
            const S = a[v],
                g = d[v],
                H = S.el && (S.type === Te || !Tt(S, g) || S.shapeFlag & 70) ? p(S.el) : h;
            y(S, g, H, null, _, m, C, z, !0)
        }
    }, we = (a, d, h, _, m, C, z) => {
        if (h !== _) {
            for (const v in _) {
                if (Kt(v)) continue;
                const S = _[v],
                    g = h[v];
                S !== g && v !== "value" && r(a, v, g, S, z, d.children, m, C, ve)
            }
            if (h !== se)
                for (const v in h) !Kt(v) && !(v in _) && r(a, v, h[v], null, z, d.children, m, C, ve);
            "value" in _ && r(a, "value", h.value, _.value)
        }
    }, k = (a, d, h, _, m, C, z, v, S) => {
        const g = d.el = a ? a.el : l(""),
            H = d.anchor = a ? a.anchor : l("");
        let {
            patchFlag: D,
            dynamicChildren: F,
            slotScopeIds: j
        } = d;
        j && (v = v ? v.concat(j) : j), a == null ? (s(g, h, _), s(H, h, _), q(d.children, h, H, m, C, z, v, S)) : D > 0 && D & 64 && F && a.dynamicChildren ? (ee(a.dynamicChildren, F, h, m, C, z, v), (d.key != null || m && d === m.subTree) && Jn(a, d, !0)) : B(a, d, h, H, m, C, z, v, S)
    }, I = (a, d, h, _, m, C, z, v, S) => {
        d.slotScopeIds = v, a == null ? d.shapeFlag & 512 ? m.ctx.activate(d, h, _, z, S) : x(d, h, _, m, C, z, S) : O(a, d, S)
    }, x = (a, d, h, _, m, C, z) => {
        const v = a.component = ol(a, _, m);
        if (Hn(a) && (v.ctx.renderer = be), ll(v), v.asyncDep) {
            if (m && m.registerDep(v, w), !a.el) {
                const S = v.subTree = ce(rt);
                Q(null, S, d, h)
            }
            return
        }
        w(v, a, d, h, m, C, z)
    }, O = (a, d, h) => {
        const _ = d.component = a.component;
        if (go(a, d, h))
            if (_.asyncDep && !_.asyncResolved) {
                P(_, d, h);
                return
            } else _.next = d, lo(_.update), _.update();
        else d.el = a.el, _.vnode = d
    }, w = (a, d, h, _, m, C, z) => {
        const v = () => {
                if (a.isMounted) {
                    let {
                        next: H,
                        bu: D,
                        u: F,
                        parent: j,
                        vnode: W
                    } = a, te = H, ie;
                    dt(a, !1), H ? (H.el = W.el, P(a, H, z)) : H = W, D && Zt(D), (ie = H.props && H.props.onVnodeBeforeUpdate) && Ve(ie, j, H, W), dt(a, !0);
                    const le = xi(a),
                        Le = a.subTree;
                    a.subTree = le, y(Le, le, p(Le.el), ke(Le), a, m, C), H.el = le.el, te === null && yo(a, le.el), F && $e(F, m), (ie = H.props && H.props.onVnodeUpdated) && $e(() => Ve(ie, j, H, W), m)
                } else {
                    let H;
                    const {
                        el: D,
                        props: F
                    } = d, {
                        bm: j,
                        m: W,
                        parent: te
                    } = a, ie = At(d);
                    if (dt(a, !1), j && Zt(j), !ie && (H = F && F.onVnodeBeforeMount) && Ve(H, te, d), dt(a, !0), D && Qe) {
                        const le = () => {
                            a.subTree = xi(a), Qe(D, a.subTree, a, m, null)
                        };
                        ie ? d.type.__asyncLoader().then(() => !a.isUnmounted && le()) : le()
                    } else {
                        const le = a.subTree = xi(a);
                        y(null, le, h, _, a, m, C), d.el = le.el
                    }
                    if (W && $e(W, m), !ie && (H = F && F.onVnodeMounted)) {
                        const le = d;
                        $e(() => Ve(H, te, le), m)
                    }(d.shapeFlag & 256 || te && At(te.vnode) && te.vnode.shapeFlag & 256) && a.a && $e(a.a, m), a.isMounted = !0, d = h = _ = null
                }
            },
            S = a.effect = new Qi(v, () => as(g), a.scope),
            g = a.update = () => S.run();
        g.id = a.uid, dt(a, !0), g()
    }, P = (a, d, h) => {
        d.component = a;
        const _ = a.vnode.props;
        a.vnode = d, a.next = null, Wo(a, d.props, _, h), Zo(a, d.children, h), Ot(), Ds(), zt()
    }, B = (a, d, h, _, m, C, z, v, S = !1) => {
        const g = a && a.children,
            H = a ? a.shapeFlag : 0,
            D = d.children,
            {
                patchFlag: F,
                shapeFlag: j
            } = d;
        if (F > 0) {
            if (F & 128) {
                ne(g, D, h, _, m, C, z, v, S);
                return
            } else if (F & 256) {
                Z(g, D, h, _, m, C, z, v, S);
                return
            }
        }
        j & 8 ? (H & 16 && ve(g, m, C), D !== g && f(h, D)) : H & 16 ? j & 16 ? ne(g, D, h, _, m, C, z, v, S) : ve(g, m, C, !0) : (H & 8 && f(h, ""), j & 16 && q(D, h, _, m, C, z, v, S))
    }, Z = (a, d, h, _, m, C, z, v, S) => {
        a = a || bt, d = d || bt;
        const g = a.length,
            H = d.length,
            D = Math.min(g, H);
        let F;
        for (F = 0; F < D; F++) {
            const j = d[F] = S ? et(d[F]) : Ye(d[F]);
            y(a[F], j, h, null, m, C, z, v, S)
        }
        g > H ? ve(a, m, C, !0, !1, D) : q(d, h, _, m, C, z, v, S, D)
    }, ne = (a, d, h, _, m, C, z, v, S) => {
        let g = 0;
        const H = d.length;
        let D = a.length - 1,
            F = H - 1;
        for (; g <= D && g <= F;) {
            const j = a[g],
                W = d[g] = S ? et(d[g]) : Ye(d[g]);
            if (Tt(j, W)) y(j, W, h, null, m, C, z, v, S);
            else break;
            g++
        }
        for (; g <= D && g <= F;) {
            const j = a[D],
                W = d[F] = S ? et(d[F]) : Ye(d[F]);
            if (Tt(j, W)) y(j, W, h, null, m, C, z, v, S);
            else break;
            D--, F--
        }
        if (g > D) {
            if (g <= F) {
                const j = F + 1,
                    W = j < H ? d[j].el : _;
                for (; g <= F;) y(null, d[g] = S ? et(d[g]) : Ye(d[g]), h, W, m, C, z, v, S), g++
            }
        } else if (g > F)
            for (; g <= D;) oe(a[g], m, C, !0), g++;
        else {
            const j = g,
                W = g,
                te = new Map;
            for (g = W; g <= F; g++) {
                const Oe = d[g] = S ? et(d[g]) : Ye(d[g]);
                Oe.key != null && te.set(Oe.key, g)
            }
            let ie, le = 0;
            const Le = F - W + 1;
            let vt = !1,
                vs = 0;
            const Pt = new Array(Le);
            for (g = 0; g < Le; g++) Pt[g] = 0;
            for (g = j; g <= D; g++) {
                const Oe = a[g];
                if (le >= Le) {
                    oe(Oe, m, C, !0);
                    continue
                }
                let Ne;
                if (Oe.key != null) Ne = te.get(Oe.key);
                else
                    for (ie = W; ie <= F; ie++)
                        if (Pt[ie - W] === 0 && Tt(Oe, d[ie])) {
                            Ne = ie;
                            break
                        } Ne === void 0 ? oe(Oe, m, C, !0) : (Pt[Ne - W] = g + 1, Ne >= vs ? vs = Ne : vt = !0, y(Oe, d[Ne], h, null, m, C, z, v, S), le++)
            }
            const bs = vt ? el(Pt) : bt;
            for (ie = bs.length - 1, g = Le - 1; g >= 0; g--) {
                const Oe = W + g,
                    Ne = d[Oe],
                    Cs = Oe + 1 < H ? d[Oe + 1].el : _;
                Pt[g] === 0 ? y(null, Ne, h, Cs, m, C, z, v, S) : vt && (ie < 0 || g !== bs[ie] ? X(Ne, h, Cs, 2) : ie--)
            }
        }
    }, X = (a, d, h, _, m = null) => {
        const {
            el: C,
            type: z,
            transition: v,
            children: S,
            shapeFlag: g
        } = a;
        if (g & 6) {
            X(a.component.subTree, d, h, _);
            return
        }
        if (g & 128) {
            a.suspense.move(d, h, _);
            return
        }
        if (g & 64) {
            z.move(a, d, h, be);
            return
        }
        if (z === Te) {
            s(C, d, h);
            for (let D = 0; D < S.length; D++) X(S[D], d, h, _);
            s(a.anchor, d, h);
            return
        }
        if (z === Xt) {
            J(a, d, h);
            return
        }
        if (_ !== 2 && g & 1 && v)
            if (_ === 0) v.beforeEnter(C), s(C, d, h), $e(() => v.enter(C), m);
            else {
                const {
                    leave: D,
                    delayLeave: F,
                    afterLeave: j
                } = v, W = () => s(C, d, h), te = () => {
                    D(C, () => {
                        W(), j && j()
                    })
                };
                F ? F(C, W, te) : te()
            }
        else s(C, d, h)
    }, oe = (a, d, h, _ = !1, m = !1) => {
        const {
            type: C,
            props: z,
            ref: v,
            children: S,
            dynamicChildren: g,
            shapeFlag: H,
            patchFlag: D,
            dirs: F
        } = a;
        if (v != null && Bi(v, null, h, a, !0), H & 256) {
            d.ctx.deactivate(a);
            return
        }
        const j = H & 1 && F,
            W = !At(a);
        let te;
        if (W && (te = z && z.onVnodeBeforeUnmount) && Ve(te, d, a), H & 6) ot(a.component, h, _);
        else {
            if (H & 128) {
                a.suspense.unmount(h, _);
                return
            }
            j && at(a, null, d, "beforeUnmount"), H & 64 ? a.type.remove(a, d, h, m, be, _) : g && (C !== Te || D > 0 && D & 64) ? ve(g, d, h, !1, !0) : (C === Te && D & 384 || !m && H & 16) && ve(S, d, h), _ && Ae(a)
        }(W && (te = z && z.onVnodeUnmounted) || j) && $e(() => {
            te && Ve(te, d, a), j && at(a, null, d, "unmounted")
        }, h)
    }, Ae = a => {
        const {
            type: d,
            el: h,
            anchor: _,
            transition: m
        } = a;
        if (d === Te) {
            Ee(h, _);
            return
        }
        if (d === Xt) {
            ye(a);
            return
        }
        const C = () => {
            n(h), m && !m.persisted && m.afterLeave && m.afterLeave()
        };
        if (a.shapeFlag & 1 && m && !m.persisted) {
            const {
                leave: z,
                delayLeave: v
            } = m, S = () => z(h, C);
            v ? v(a.el, C, S) : S()
        } else C()
    }, Ee = (a, d) => {
        let h;
        for (; a !== d;) h = b(a), n(a), a = h;
        n(d)
    }, ot = (a, d, h) => {
        const {
            bum: _,
            scope: m,
            update: C,
            subTree: z,
            um: v
        } = a;
        _ && Zt(_), m.stop(), C && (C.active = !1, oe(z, a, d, h)), v && $e(v, d), $e(() => {
            a.isUnmounted = !0
        }, d), d && d.pendingBranch && !d.isUnmounted && a.asyncDep && !a.asyncResolved && a.suspenseId === d.pendingId && (d.deps--, d.deps === 0 && d.resolve())
    }, ve = (a, d, h, _ = !1, m = !1, C = 0) => {
        for (let z = C; z < a.length; z++) oe(a[z], d, h, _, m)
    }, ke = a => a.shapeFlag & 6 ? ke(a.component.subTree) : a.shapeFlag & 128 ? a.suspense.next() : b(a.anchor || a.el), pe = (a, d, h) => {
        a == null ? d._vnode && oe(d._vnode, null, null, !0) : y(d._vnode || null, a, d, null, null, null, h), Ds(), Dn(), d._vnode = a
    }, be = {
        p: y,
        um: oe,
        m: X,
        r: Ae,
        mt: x,
        mc: q,
        pc: B,
        pbc: ee,
        n: ke,
        o: e
    };
    let lt, Qe;
    return t && ([lt, Qe] = t(be)), {
        render: pe,
        hydrate: lt,
        createApp: Xo(pe, lt)
    }
}

function dt({
    effect: e,
    update: t
}, i) {
    e.allowRecurse = t.allowRecurse = i
}

function Jn(e, t, i = !1) {
    const s = e.children,
        n = t.children;
    if (N(s) && N(n))
        for (let r = 0; r < s.length; r++) {
            const o = s[r];
            let l = n[r];
            l.shapeFlag & 1 && !l.dynamicChildren && ((l.patchFlag <= 0 || l.patchFlag === 32) && (l = n[r] = et(n[r]), l.el = o.el), i || Jn(o, l))
        }
}

function el(e) {
    const t = e.slice(),
        i = [0];
    let s, n, r, o, l;
    const c = e.length;
    for (s = 0; s < c; s++) {
        const u = e[s];
        if (u !== 0) {
            if (n = i[i.length - 1], e[n] < u) {
                t[s] = n, i.push(s);
                continue
            }
            for (r = 0, o = i.length - 1; r < o;) l = r + o >> 1, e[i[l]] < u ? r = l + 1 : o = l;
            u < e[i[r]] && (r > 0 && (t[s] = i[r - 1]), i[r] = s)
        }
    }
    for (r = i.length, o = i[r - 1]; r-- > 0;) i[r] = o, o = t[o];
    return i
}
const tl = e => e.__isTeleport,
    Te = Symbol(void 0),
    hs = Symbol(void 0),
    rt = Symbol(void 0),
    Xt = Symbol(void 0),
    Et = [];
let Fe = null;

function V(e = !1) {
    Et.push(Fe = e ? null : [])
}

function il() {
    Et.pop(), Fe = Et[Et.length - 1] || null
}
let Ft = 1;

function js(e) {
    Ft += e
}

function Xn(e) {
    return e.dynamicChildren = Ft > 0 ? Fe || bt : null, il(), Ft > 0 && Fe && Fe.push(e), e
}

function K(e, t, i, s, n, r) {
    return Xn(A(e, t, i, s, n, r, !0))
}

function De(e, t, i, s, n) {
    return Xn(ce(e, t, i, s, n, !0))
}

function li(e) {
    return e ? e.__v_isVNode === !0 : !1
}

function Tt(e, t) {
    return e.type === t.type && e.key === t.key
}
const vi = "__vInternal",
    Qn = ({
        key: e
    }) => e != null ? e : null,
    Qt = ({
        ref: e,
        ref_key: t,
        ref_for: i
    }) => e != null ? de(e) || xe(e) || Y(e) ? {
        i: Se,
        r: e,
        k: t,
        f: !!i
    } : e : null;

function A(e, t = null, i = null, s = 0, n = null, r = e === Te ? 0 : 1, o = !1, l = !1) {
    const c = {
        __v_isVNode: !0,
        __v_skip: !0,
        type: e,
        props: t,
        key: t && Qn(t),
        ref: t && Qt(t),
        scopeId: yi,
        slotScopeIds: null,
        children: i,
        component: null,
        suspense: null,
        ssContent: null,
        ssFallback: null,
        dirs: null,
        transition: null,
        el: null,
        anchor: null,
        target: null,
        targetAnchor: null,
        staticCount: 0,
        shapeFlag: r,
        patchFlag: s,
        dynamicProps: n,
        dynamicChildren: null,
        appContext: null
    };
    return l ? (ms(c, i), r & 128 && e.normalize(c)) : i && (c.shapeFlag |= de(i) ? 8 : 16), Ft > 0 && !o && Fe && (c.patchFlag > 0 || r & 6) && c.patchFlag !== 32 && Fe.push(c), c
}
const ce = sl;

function sl(e, t = null, i = null, s = 0, n = null, r = !1) {
    if ((!e || e === Bn) && (e = rt), li(e)) {
        const l = Mt(e, t, !0);
        return i && ms(l, i), Ft > 0 && !r && Fe && (l.shapeFlag & 6 ? Fe[Fe.indexOf(e)] = l : Fe.push(l)), l.patchFlag |= -2, l
    }
    if (fl(e) && (e = e.__vccOpts), t) {
        t = Gn(t);
        let {
            class: l,
            style: c
        } = t;
        l && !de(l) && (t.class = Pe(l)), re(c) && (In(c) && !N(c) && (c = Me({}, c)), t.style = Ke(c))
    }
    const o = de(e) ? 1 : wo(e) ? 128 : tl(e) ? 64 : re(e) ? 4 : Y(e) ? 2 : 0;
    return A(e, t, i, s, n, o, r, !0)
}

function Gn(e) {
    return e ? In(e) || vi in e ? Me({}, e) : e : null
}

function Mt(e, t, i = !1) {
    const {
        props: s,
        ref: n,
        patchFlag: r,
        children: o
    } = e, l = t ? _s(s || {}, t) : s;
    return {
        __v_isVNode: !0,
        __v_skip: !0,
        type: e.type,
        props: l,
        key: l && Qn(l),
        ref: t && t.ref ? i && n ? N(n) ? n.concat(Qt(t)) : [n, Qt(t)] : Qt(t) : n,
        scopeId: e.scopeId,
        slotScopeIds: e.slotScopeIds,
        children: o,
        target: e.target,
        targetAnchor: e.targetAnchor,
        staticCount: e.staticCount,
        shapeFlag: e.shapeFlag,
        patchFlag: t && e.type !== Te ? r === -1 ? 16 : r | 16 : r,
        dynamicProps: e.dynamicProps,
        dynamicChildren: e.dynamicChildren,
        appContext: e.appContext,
        dirs: e.dirs,
        transition: e.transition,
        component: e.component,
        suspense: e.suspense,
        ssContent: e.ssContent && Mt(e.ssContent),
        ssFallback: e.ssFallback && Mt(e.ssFallback),
        el: e.el,
        anchor: e.anchor
    }
}

function ht(e = " ", t = 0) {
    return ce(hs, null, e, t)
}

function ps(e, t) {
    const i = ce(Xt, null, e);
    return i.staticCount = t, i
}

function fe(e = "", t = !1) {
    return t ? (V(), De(rt, null, e)) : ce(rt, null, e)
}

function Ye(e) {
    return e == null || typeof e == "boolean" ? ce(rt) : N(e) ? ce(Te, null, e.slice()) : typeof e == "object" ? et(e) : ce(hs, null, String(e))
}

function et(e) {
    return e.el === null || e.memo ? e : Mt(e)
}

function ms(e, t) {
    let i = 0;
    const {
        shapeFlag: s
    } = e;
    if (t == null) t = null;
    else if (N(t)) i = 16;
    else if (typeof t == "object")
        if (s & 65) {
            const n = t.default;
            n && (n._c && (n._d = !1), ms(e, n()), n._c && (n._d = !0));
            return
        } else {
            i = 32;
            const n = t._;
            !n && !(vi in t) ? t._ctx = Se : n === 3 && Se && (Se.slots._ === 1 ? t._ = 1 : (t._ = 2, e.patchFlag |= 1024))
        }
    else Y(t) ? (t = {
        default: t,
        _ctx: Se
    }, i = 32) : (t = String(t), s & 64 ? (i = 16, t = [ht(t)]) : i = 8);
    e.children = t, e.shapeFlag |= i
}

function _s(...e) {
    const t = {};
    for (let i = 0; i < e.length; i++) {
        const s = e[i];
        for (const n in s)
            if (n === "class") t.class !== s.class && (t.class = Pe([t.class, s.class]));
            else if (n === "style") t.style = Ke([t.style, s.style]);
        else if (ui(n)) {
            const r = t[n],
                o = s[n];
            o && r !== o && !(N(r) && r.includes(o)) && (t[n] = r ? [].concat(r, o) : o)
        } else n !== "" && (t[n] = s[n])
    }
    return t
}

function Ve(e, t, i, s = null) {
    Be(e, t, 7, [i, s])
}
const nl = Zn();
let rl = 0;

function ol(e, t, i) {
    const s = e.type,
        n = (t ? t.appContext : e.appContext) || nl,
        r = {
            uid: rl++,
            vnode: e,
            type: s,
            parent: t,
            appContext: n,
            root: null,
            next: null,
            subTree: null,
            effect: null,
            update: null,
            scope: new $r(!0),
            render: null,
            proxy: null,
            exposed: null,
            exposeProxy: null,
            withProxy: null,
            provides: t ? t.provides : Object.create(n.provides),
            accessCache: null,
            renderCache: [],
            components: null,
            directives: null,
            propsOptions: Un(s, n),
            emitsOptions: En(s, n),
            emit: null,
            emitted: null,
            propsDefaults: se,
            inheritAttrs: s.inheritAttrs,
            ctx: se,
            data: se,
            props: se,
            attrs: se,
            slots: se,
            refs: se,
            setupState: se,
            setupContext: null,
            suspense: i,
            suspenseId: i ? i.pendingId : 0,
            asyncDep: null,
            asyncResolved: !1,
            isMounted: !1,
            isUnmounted: !1,
            isDeactivated: !1,
            bc: null,
            c: null,
            bm: null,
            m: null,
            bu: null,
            u: null,
            um: null,
            bum: null,
            da: null,
            a: null,
            rtg: null,
            rtc: null,
            ec: null,
            sp: null
        };
    return r.ctx = {
        _: r
    }, r.root = t ? t.root : r, r.emit = uo.bind(null, r), e.ce && e.ce(r), r
}
let me = null;
const $t = e => {
        me = e, e.scope.on()
    },
    yt = () => {
        me && me.scope.off(), me = null
    };

function er(e) {
    return e.vnode.shapeFlag & 4
}
let Rt = !1;

function ll(e, t = !1) {
    Rt = t;
    const {
        props: i,
        children: s
    } = e.vnode, n = er(e);
    Uo(e, i, n, t), qo(e, s);
    const r = n ? cl(e, t) : void 0;
    return Rt = !1, r
}

function cl(e, t) {
    const i = e.type;
    e.accessCache = Object.create(null), e.proxy = ns(new Proxy(e.ctx, Ro));
    const {
        setup: s
    } = i;
    if (s) {
        const n = e.setupContext = s.length > 1 ? dl(e) : null;
        $t(e), Ot();
        const r = st(s, e, 0, [e.props, n]);
        if (zt(), yt(), fn(r)) {
            if (r.then(yt, yt), t) return r.then(o => {
                Ys(e, o, t)
            }).catch(o => {
                _i(o, e, 0)
            });
            e.asyncDep = r
        } else Ys(e, r, t)
    } else tr(e, t)
}

function Ys(e, t, i) {
    Y(t) ? e.type.__ssrInlineRender ? e.ssrRender = t : e.render = t : re(t) && (e.setupState = On(t)), tr(e, i)
}
let Us;

function tr(e, t, i) {
    const s = e.type;
    if (!e.render) {
        if (!t && Us && !s.render) {
            const n = s.template;
            if (n) {
                const {
                    isCustomElement: r,
                    compilerOptions: o
                } = e.appContext.config, {
                    delimiters: l,
                    compilerOptions: c
                } = s, u = Me(Me({
                    isCustomElement: r,
                    delimiters: l
                }, o), c);
                s.render = Us(n, u)
            }
        }
        e.render = s.render || Re
    }
    $t(e), Ot(), Bo(e), zt(), yt()
}

function al(e) {
    return new Proxy(e.attrs, {
        get(t, i) {
            return ze(e, "get", "$attrs"), t[i]
        }
    })
}

function dl(e) {
    const t = s => {
        e.exposed = s || {}
    };
    let i;
    return {
        get attrs() {
            return i || (i = al(e))
        },
        slots: e.slots,
        emit: e.emit,
        expose: t
    }
}

function bi(e) {
    if (e.exposed) return e.exposeProxy || (e.exposeProxy = new Proxy(On(ns(e.exposed)), {
        get(t, i) {
            if (i in t) return t[i];
            if (i in ri) return ri[i](e)
        }
    }))
}

function ul(e, t = !0) {
    return Y(e) ? e.displayName || e.name : e.name || t && e.__name
}

function fl(e) {
    return Y(e) && "__vccOpts" in e
}
const hl = (e, t) => no(e, t, Rt);

function pl(e, t, i) {
    const s = arguments.length;
    return s === 2 ? re(t) && !N(t) ? li(t) ? ce(e, null, [t]) : ce(e, t) : ce(e, null, t) : (s > 3 ? i = Array.prototype.slice.call(arguments, 2) : s === 3 && li(i) && (i = [i]), ce(e, t, i))
}
const ml = "3.2.38",
    _l = "http://www.w3.org/2000/svg",
    pt = typeof document < "u" ? document : null,
    Ws = pt && pt.createElement("template"),
    gl = {
        insert: (e, t, i) => {
            t.insertBefore(e, i || null)
        },
        remove: e => {
            const t = e.parentNode;
            t && t.removeChild(e)
        },
        createElement: (e, t, i, s) => {
            const n = t ? pt.createElementNS(_l, e) : pt.createElement(e, i ? {
                is: i
            } : void 0);
            return e === "select" && s && s.multiple != null && n.setAttribute("multiple", s.multiple), n
        },
        createText: e => pt.createTextNode(e),
        createComment: e => pt.createComment(e),
        setText: (e, t) => {
            e.nodeValue = t
        },
        setElementText: (e, t) => {
            e.textContent = t
        },
        parentNode: e => e.parentNode,
        nextSibling: e => e.nextSibling,
        querySelector: e => pt.querySelector(e),
        setScopeId(e, t) {
            e.setAttribute(t, "")
        },
        cloneNode(e) {
            const t = e.cloneNode(!0);
            return "_value" in e && (t._value = e._value), t
        },
        insertStaticContent(e, t, i, s, n, r) {
            const o = i ? i.previousSibling : t.lastChild;
            if (n && (n === r || n.nextSibling))
                for (; t.insertBefore(n.cloneNode(!0), i), !(n === r || !(n = n.nextSibling)););
            else {
                Ws.innerHTML = s ? `<svg>${e}</svg>` : e;
                const l = Ws.content;
                if (s) {
                    const c = l.firstChild;
                    for (; c.firstChild;) l.appendChild(c.firstChild);
                    l.removeChild(c)
                }
                t.insertBefore(l, i)
            }
            return [o ? o.nextSibling : t.firstChild, i ? i.previousSibling : t.lastChild]
        }
    };

function yl(e, t, i) {
    const s = e._vtc;
    s && (t = (t ? [t, ...s] : [...s]).join(" ")), t == null ? e.removeAttribute("class") : i ? e.setAttribute("class", t) : e.className = t
}

function wl(e, t, i) {
    const s = e.style,
        n = de(i);
    if (i && !n) {
        for (const r in i) Ni(s, r, i[r]);
        if (t && !de(t))
            for (const r in t) i[r] == null && Ni(s, r, "")
    } else {
        const r = s.display;
        n ? t !== i && (s.cssText = i) : t && e.removeAttribute("style"), "_vod" in e && (s.display = r)
    }
}
const Ks = /\s*!important$/;

function Ni(e, t, i) {
    if (N(i)) i.forEach(s => Ni(e, t, s));
    else if (i == null && (i = ""), t.startsWith("--")) e.setProperty(t, i);
    else {
        const s = vl(e, t);
        Ks.test(i) ? e.setProperty(wt(s), i.replace(Ks, ""), "important") : e[s] = i
    }
}
const qs = ["Webkit", "Moz", "ms"],
    $i = {};

function vl(e, t) {
    const i = $i[t];
    if (i) return i;
    let s = qe(t);
    if (s !== "filter" && s in e) return $i[t] = s;
    s = pi(s);
    for (let n = 0; n < qs.length; n++) {
        const r = qs[n] + s;
        if (r in e) return $i[t] = r
    }
    return t
}
const Zs = "http://www.w3.org/1999/xlink";

function bl(e, t, i, s, n) {
    if (s && t.startsWith("xlink:")) i == null ? e.removeAttributeNS(Zs, t.slice(6, t.length)) : e.setAttributeNS(Zs, t, i);
    else {
        const r = hr(t);
        i == null || r && !an(i) ? e.removeAttribute(t) : e.setAttribute(t, r ? "" : i)
    }
}

function Cl(e, t, i, s, n, r, o) {
    if (t === "innerHTML" || t === "textContent") {
        s && o(s, n, r), e[t] = i == null ? "" : i;
        return
    }
    if (t === "value" && e.tagName !== "PROGRESS" && !e.tagName.includes("-")) {
        e._value = i;
        const c = i == null ? "" : i;
        (e.value !== c || e.tagName === "OPTION") && (e.value = c), i == null && e.removeAttribute(t);
        return
    }
    let l = !1;
    if (i === "" || i == null) {
        const c = typeof e[t];
        c === "boolean" ? i = an(i) : i == null && c === "string" ? (i = "", l = !0) : c === "number" && (i = 0, l = !0)
    }
    try {
        e[t] = i
    } catch {}
    l && e.removeAttribute(t)
}
const [ir, xl] = (() => {
    let e = Date.now,
        t = !1;
    if (typeof window < "u") {
        Date.now() > document.createEvent("Event").timeStamp && (e = performance.now.bind(performance));
        const i = navigator.userAgent.match(/firefox\/(\d+)/i);
        t = !!(i && Number(i[1]) <= 53)
    }
    return [e, t]
})();
let Vi = 0;
const Sl = Promise.resolve(),
    Ml = () => {
        Vi = 0
    },
    $l = () => Vi || (Sl.then(Ml), Vi = ir());

function mt(e, t, i, s) {
    e.addEventListener(t, i, s)
}

function Il(e, t, i, s) {
    e.removeEventListener(t, i, s)
}

function Ol(e, t, i, s, n = null) {
    const r = e._vei || (e._vei = {}),
        o = r[t];
    if (s && o) o.value = s;
    else {
        const [l, c] = zl(t);
        if (s) {
            const u = r[t] = Pl(s, n);
            mt(e, l, u, c)
        } else o && (Il(e, l, o, c), r[t] = void 0)
    }
}
const Js = /(?:Once|Passive|Capture)$/;

function zl(e) {
    let t;
    if (Js.test(e)) {
        t = {};
        let s;
        for (; s = e.match(Js);) e = e.slice(0, e.length - s[0].length), t[s[0].toLowerCase()] = !0
    }
    return [e[2] === ":" ? e.slice(3) : wt(e.slice(2)), t]
}

function Pl(e, t) {
    const i = s => {
        const n = s.timeStamp || ir();
        (xl || n >= i.attached - 1) && Be(Tl(s, i.value), t, 5, [s])
    };
    return i.value = e, i.attached = $l(), i
}

function Tl(e, t) {
    if (N(t)) {
        const i = e.stopImmediatePropagation;
        return e.stopImmediatePropagation = () => {
            i.call(e), e._stopped = !0
        }, t.map(s => n => !n._stopped && s && s(n))
    } else return t
}
const Xs = /^on[a-z]/,
    Dl = (e, t, i, s, n = !1, r, o, l, c) => {
        t === "class" ? yl(e, s, n) : t === "style" ? wl(e, i, s) : ui(t) ? qi(t) || Ol(e, t, i, s, o) : (t[0] === "." ? (t = t.slice(1), !0) : t[0] === "^" ? (t = t.slice(1), !1) : Al(e, t, s, n)) ? Cl(e, t, s, r, o, l, c) : (t === "true-value" ? e._trueValue = s : t === "false-value" && (e._falseValue = s), bl(e, t, s, n))
    };

function Al(e, t, i, s) {
    return s ? !!(t === "innerHTML" || t === "textContent" || t in e && Xs.test(t) && Y(i)) : t === "spellcheck" || t === "draggable" || t === "translate" || t === "form" || t === "list" && e.tagName === "INPUT" || t === "type" && e.tagName === "TEXTAREA" || Xs.test(t) && de(i) ? !1 : t in e
}
const ci = e => {
    const t = e.props["onUpdate:modelValue"] || !1;
    return N(t) ? i => Zt(t, i) : t
};

function El(e) {
    e.target.composing = !0
}

function Qs(e) {
    const t = e.target;
    t.composing && (t.composing = !1, t.dispatchEvent(new Event("input")))
}
const Gs = {
        created(e, {
            modifiers: {
                lazy: t,
                trim: i,
                number: s
            }
        }, n) {
            e._assign = ci(n);
            const r = s || n.props && n.props.type === "number";
            mt(e, t ? "change" : "input", o => {
                if (o.target.composing) return;
                let l = e.value;
                i && (l = l.trim()), r && (l = zi(l)), e._assign(l)
            }), i && mt(e, "change", () => {
                e.value = e.value.trim()
            }), t || (mt(e, "compositionstart", El), mt(e, "compositionend", Qs), mt(e, "change", Qs))
        },
        mounted(e, {
            value: t
        }) {
            e.value = t == null ? "" : t
        },
        beforeUpdate(e, {
            value: t,
            modifiers: {
                lazy: i,
                trim: s,
                number: n
            }
        }, r) {
            if (e._assign = ci(r), e.composing || document.activeElement === e && e.type !== "range" && (i || s && e.value.trim() === t || (n || e.type === "number") && zi(e.value) === t)) return;
            const o = t == null ? "" : t;
            e.value !== o && (e.value = o)
        }
    },
    kl = {
        deep: !0,
        created(e, t, i) {
            e._assign = ci(i), mt(e, "change", () => {
                const s = e._modelValue,
                    n = Ll(e),
                    r = e.checked,
                    o = e._assign;
                if (N(s)) {
                    const l = dn(s, n),
                        c = l !== -1;
                    if (r && !c) o(s.concat(n));
                    else if (!r && c) {
                        const u = [...s];
                        u.splice(l, 1), o(u)
                    }
                } else if (fi(s)) {
                    const l = new Set(s);
                    r ? l.add(n) : l.delete(n), o(l)
                } else o(sr(e, r))
            })
        },
        mounted: en,
        beforeUpdate(e, t, i) {
            e._assign = ci(i), en(e, t, i)
        }
    };

function en(e, {
    value: t,
    oldValue: i
}, s) {
    e._modelValue = t, N(t) ? e.checked = dn(t, s.props.value) > -1 : fi(t) ? e.checked = t.has(s.props.value) : t !== i && (e.checked = di(t, sr(e, !0)))
}

function Ll(e) {
    return "_value" in e ? e._value : e.value
}

function sr(e, t) {
    const i = t ? "_trueValue" : "_falseValue";
    return i in e ? e[i] : t
}
const Hl = {
        esc: "escape",
        space: " ",
        up: "arrow-up",
        left: "arrow-left",
        right: "arrow-right",
        down: "arrow-down",
        delete: "backspace"
    },
    tn = (e, t) => i => {
        if (!("key" in i)) return;
        const s = wt(i.key);
        if (t.some(n => n === s || Hl[n] === s)) return e(i)
    },
    Fl = Me({
        patchProp: Dl
    }, gl);
let sn;

function Rl() {
    return sn || (sn = Qo(Fl))
}
const Bl = (...e) => {
    const t = Rl().createApp(...e),
        {
            mount: i
        } = t;
    return t.mount = s => {
        const n = Nl(s);
        if (!n) return;
        const r = t._component;
        !Y(r) && !r.render && !r.template && (r.template = n.innerHTML), n.innerHTML = "";
        const o = i(n, !1, n instanceof SVGElement);
        return n instanceof Element && (n.removeAttribute("v-cloak"), n.setAttribute("data-v-app", "")), o
    }, t
};

function Nl(e) {
    return de(e) ? document.querySelector(e) : e
}
const Ie = (e, t) => {
        const i = e.__vccOpts || e;
        for (const [s, n] of t) i[s] = n;
        return i
    },
    Vl = {},
    jl = {
        width: "24",
        height: "24",
        viewBox: "0 0 24 24",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg"
    },
    Yl = A("path", {
        "fill-rule": "evenodd",
        "clip-rule": "evenodd",
        d: "M18.6464 6.75355C18.8417 6.55829 18.8417 6.24171 18.6464 6.04645L17.9536 5.35355C17.7583 5.15829 17.4417 5.15829 17.2464 5.35355L12 10.6L6.75355 5.35355C6.55829 5.15829 6.24171 5.15829 6.04645 5.35355L5.35355 6.04645C5.15829 6.24171 5.15829 6.55829 5.35355 6.75355L10.6 12L5.35355 17.2464C5.15829 17.4417 5.15829 17.7583 5.35355 17.9536L6.04645 18.6464C6.24171 18.8417 6.55829 18.8417 6.75355 18.6464L12 13.4L17.2464 18.6464C17.4417 18.8417 17.7583 18.8417 17.9536 18.6464L18.6464 17.9536C18.8417 17.7583 18.8417 17.4417 18.6464 17.2464L13.4 12L18.6464 6.75355Z",
        fill: "#21201F"
    }, null, -1),
    Ul = [Yl];

function Wl(e, t) {
    return V(), K("svg", jl, Ul)
}
const Kl = Ie(Vl, [
        ["render", Wl]
    ]),
    ql = {},
    Zl = {
        width: "27",
        height: "27",
        viewBox: "0 0 27 27",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg"
    },
    Jl = ps('<mask id="mask0_1489_60603" style="mask-type:alpha;" maskUnits="userSpaceOnUse" x="0" y="0" width="27" height="27" style="width: 27px; height: 27px;"><circle cx="13.3532" cy="13.5" r="13.3532" fill="white"></circle></mask><g mask="url(#mask0_1489_60603)"><rect width="26.7064" height="26.7064" transform="translate(0 0.146774)" fill="#FC3F1D"></rect><path d="M15.3217 7.62497H13.9702C11.6535 7.62497 10.4952 8.78332 10.4952 10.5208C10.4952 12.4514 11.2674 13.4167 13.005 14.5751L14.3564 15.5404L10.4952 21.5252H7.40625L11.0744 16.1195C8.95072 14.5751 7.79237 13.2237 7.79237 10.7139C7.79237 7.62497 9.91601 5.50132 13.9702 5.50132H18.0245V21.5252H15.3217V7.62497Z" fill="white"></path></g>', 2),
    Xl = [Jl];

function Ql(e, t) {
    return V(), K("svg", Zl, Xl)
}
const Gl = Ie(ql, [
        ["render", Ql]
    ]),
    ec = {},
    tc = {
        width: "24",
        height: "24",
        viewBox: "0 0 24 24",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg"
    },
    ic = A("path", {
        "fill-rule": "evenodd",
        "clip-rule": "evenodd",
        d: "M4 11C4 7.141 7.141 4 11 4C14.859 4 18 7.141 18 11C18 14.859 14.859 18 11 18C7.141 18 4 14.859 4 11ZM22 22C22.3905 21.6095 22.3905 20.9765 22 20.586L18.025 16.611C19.258 15.071 20 13.122 20 11C20 6.037 15.963 2 11 2C6.037 2 2 6.037 2 11C2 15.963 6.037 20 11 20C13.122 20 15.071 19.258 16.611 18.025L20.586 22C20.9765 22.3905 21.6095 22.3905 22 22Z",
        fill: "#9E9B98"
    }, null, -1),
    sc = [ic];

function nc(e, t) {
    return V(), K("svg", tc, sc)
}
const rc = Ie(ec, [
        ["render", nc]
    ]),
    oc = {},
    lc = {
        width: "24",
        height: "24",
        viewBox: "0 0 24 24",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg"
    },
    cc = A("path", {
        d: "M23 12C23 18.0751 18.0751 23 12 23C5.92487 23 1 18.0751 1 12C1 5.92487 5.92487 1 12 1",
        stroke: "#9E9B98",
        "stroke-width": "2",
        "stroke-linecap": "round"
    }, null, -1),
    ac = [cc];

function dc(e, t) {
    return V(), K("svg", lc, ac)
}
const uc = Ie(oc, [
        ["render", dc]
    ]),
    fc = {},
    hc = {
        width: "24",
        height: "24",
        viewBox: "0 0 24 24",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg"
    },
    pc = A("path", {
        "fill-rule": "evenodd",
        "clip-rule": "evenodd",
        d: "M15 10.5C15.7538 10.4999 16.4874 10.2565 17.0919 9.80612C17.6963 9.35571 18.1393 8.72229 18.355 8H21C21.2652 8 21.5196 7.89464 21.7071 7.70711C21.8946 7.51957 22 7.26522 22 7C22 6.73479 21.8946 6.48043 21.7071 6.2929C21.5196 6.10536 21.2652 6 21 6H18.355C18.139 5.27808 17.6958 4.6451 17.0914 4.19507C16.487 3.74505 15.7536 3.50198 15 3.50198C14.2464 3.50198 13.513 3.74505 12.9086 4.19507C12.3042 4.6451 11.861 5.27808 11.645 6H3C2.73478 6 2.48043 6.10536 2.29289 6.2929C2.10536 6.48043 2 6.73479 2 7C2 7.26522 2.10536 7.51957 2.29289 7.70711C2.48043 7.89464 2.73478 8 3 8H11.645C11.8607 8.72229 12.3037 9.35571 12.9081 9.80612C13.5126 10.2565 14.2462 10.4999 15 10.5ZM3 16C2.73478 16 2.48043 16.1054 2.29289 16.2929C2.10536 16.4804 2 16.7348 2 17C2 17.2652 2.10536 17.5196 2.29289 17.7071C2.48043 17.8946 2.73478 18 3 18H5.145C5.36103 18.7219 5.80417 19.3549 6.40858 19.8049C7.013 20.255 7.74645 20.498 8.5 20.498C9.25355 20.498 9.987 20.255 10.5914 19.8049C11.1958 19.3549 11.639 18.7219 11.855 18H21C21.2652 18 21.5196 17.8946 21.7071 17.7071C21.8946 17.5196 22 17.2652 22 17C22 16.7348 21.8946 16.4804 21.7071 16.2929C21.5196 16.1054 21.2652 16 21 16H11.855C11.639 15.2781 11.1958 14.6451 10.5914 14.1951C9.987 13.745 9.25355 13.502 8.5 13.502C7.74645 13.502 7.013 13.745 6.40858 14.1951C5.80417 14.6451 5.36103 15.2781 5.145 16H3Z",
        fill: "#21201F"
    }, null, -1),
    mc = [pc];

function _c(e, t) {
    return V(), K("svg", hc, mc)
}
const gc = Ie(fc, [
        ["render", _c]
    ]),
    yc = {},
    wc = {
        width: "21",
        height: "20",
        viewBox: "0 0 21 20",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg"
    },
    vc = A("path", {
        transform: "translate(-1,-3)",
        opacity: "0.4",
        d: "M13 13C12.4477 13 12 13.4477 12 14V21C12 21.5523 12.4477 22 13 22H20C20.5523 22 21 21.5523 21 21V14C21 13.4477 20.5523 13 20 13L13 13Z",
        fill: "black"
    }, null, -1),
    bc = A("path", {
        d: "M9.32915 6.58493C10.9389 6.58493 12.2438 5.45604 12.2438 3.71755C12.2438 3.55792 12.2328 3.40273 12.2116 3.25251L12.6506 3.17825C13.2867 3.07063 13.7934 2.58683 13.9302 1.95635L14.0369 1.46483L11.2592 1.46951C10.7451 1.02374 10.0694 0.767578 9.32915 0.767578C7.71941 0.767578 6.41445 1.97906 6.41445 3.71755C6.41445 5.45604 7.71941 6.58493 9.32915 6.58493Z",
        fill: "#21201F"
    }, null, -1),
    Cc = A("path", {
        d: "M8.07722 7.40534C6.66167 7.07061 5.24279 7.94679 4.90807 9.36234L2.94723 17.6547L2.89123 17.718H0.931454C0.685618 17.718 0.486328 17.9173 0.486328 18.1631V18.8411C0.486328 19.0869 0.685617 19.2862 0.931454 19.2862H20.3049C20.5507 19.2862 20.75 19.0869 20.75 18.8411V18.1631C20.75 17.9173 20.5507 17.718 20.3049 17.718H8.16247L9.0314 14.6832L9.03741 14.7039C9.16859 15.1557 9.52333 15.4846 9.9488 15.6013C10.0231 15.6357 10.102 15.6634 10.1848 15.6833L16.2124 17.1335C16.5122 16.1093 15.9563 15.03 14.9485 14.6793L11.4393 13.4584L10.4332 8.90381C10.386 8.69038 10.2953 8.49838 10.1725 8.33481C9.99817 8.02666 9.70085 7.78927 9.32915 7.70137L8.07722 7.40534Z",
        fill: "#21201F"
    }, null, -1),
    xc = [vc, bc, Cc];

function Sc(e, t) {
    return V(), K("svg", wc, xc)
}
const Mc = Ie(yc, [
        ["render", Sc]
    ]),
    $c = {},
    Ic = {
        width: "24",
        height: "25",
        viewBox: "0 0 24 25",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg"
    },
    Oc = ps('<g opacity="0.4"><path d="M19.1111 3.5C19.602 3.5 20 3.90878 20 4.41304V10.8043H4V4.41304C4 3.90878 4.39797 3.5 4.88889 3.5H19.1111Z" fill="#21201F"></path><path d="M5.77778 12.6304H4V19.9348L10.1818 24.5H12V17.2131C12 16.546 11.6459 15.9319 11.0763 15.6115L5.77778 12.6304Z" fill="#21201F"></path></g><path d="M17.3329 6.23926H13.7773V8.06534H17.3329V6.23926Z" fill="#21201F"></path><path d="M5.77734 12.6306H19.9996V19.0219C19.9996 19.5261 19.6016 19.9349 19.1107 19.9349H11.9996V17.2132C11.9996 16.5461 11.6454 15.9321 11.0758 15.6116L5.77734 12.6306Z" fill="#21201F"></path>', 3),
    zc = [Oc];

function Pc(e, t) {
    return V(), K("svg", Ic, zc)
}
const Tc = Ie($c, [
        ["render", Pc]
    ]),
    Dc = {},
    Ac = {
        width: "20",
        height: "21",
        viewBox: "0 0 20 21",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg"
    },
    Ec = A("path", {
        opacity: "0.4",
        "fill-rule": "evenodd",
        "clip-rule": "evenodd",
        d: "M18 2.5H2V18.5H18V2.5ZM-3.8147e-06 0.5V20.5H20V0.5H-3.8147e-06Z",
        fill: "#21201F"
    }, null, -1),
    kc = A("path", {
        d: "M7.79826 5.64247C8.11611 5.54469 8.41482 5.49677 8.68354 5.50017C9.47106 5.51012 10.0011 5.96077 10.0011 6.88845C10.0011 5.96077 10.5311 5.51012 11.3187 5.50017C11.587 5.49678 11.8853 5.54457 12.2027 5.6421C12.3286 5.69554 12.4521 5.75342 12.5731 5.81555L12.215 6.4358C11.5806 6.67965 11.1303 7.32335 11.1303 8.04363C11.1303 8.4132 11.2488 8.75508 11.45 9.03334L15.4561 5.50051C15.8055 6.2766 16 7.13753 16 8.04389C16 10.9449 14.2121 13.3807 11.5213 14.058L12.5737 15.8808C11.8025 16.2769 10.928 16.5005 10.0014 16.5005C9.07526 16.5005 8.20132 16.2772 7.43043 15.8815L8.4833 14.0579C5.79311 13.3801 4 10.9447 4 8.04407C4 7.13772 4.19444 6.27678 4.54388 5.50069L8.55556 9.03328C8.75608 8.75527 8.87423 8.4139 8.87423 8.04494C8.87423 7.32436 8.4236 6.68044 7.78879 6.4368L7.42981 5.81504C7.55018 5.75325 7.67306 5.69566 7.79826 5.64247Z",
        fill: "#21201F"
    }, null, -1),
    Lc = [Ec, kc];

function Hc(e, t) {
    return V(), K("svg", Ac, Lc)
}
const Fc = Ie(Dc, [
        ["render", Hc]
    ]),
    Rc = {},
    Bc = {
        width: "24",
        height: "24",
        viewBox: "0 0 24 24",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg"
    },
    Nc = A("circle", {
        cx: "12",
        cy: "12",
        r: "9",
        stroke: "#9E9B98",
        "stroke-width": "2"
    }, null, -1),
    Vc = A("path", {
        "fill-rule": "evenodd",
        "clip-rule": "evenodd",
        d: "M13 8C13 8.55228 12.5523 9 12 9C11.4477 9 11 8.55228 11 8C11 7.44772 11.4477 7 12 7C12.5523 7 13 7.44772 13 8ZM13.5 15C13.2239 15 13 14.7761 13 14.5V10C13 9.72386 12.7761 9.5 12.5 9.5H10V10.5H10.5C10.7761 10.5 11 10.7239 11 11V14.5C11 14.7761 10.7761 15 10.5 15H10V16H14V15H13.5Z",
        fill: "#9E9B98"
    }, null, -1),
    jc = [Nc, Vc];

function Yc(e, t) {
    return V(), K("svg", Bc, jc)
}
const Uc = Ie(Rc, [
    ["render", Yc]
]);

function Wc() {
    var e = window.navigator.userAgent,
        t = e.indexOf("MSIE ");
    if (t > 0) return parseInt(e.substring(t + 5, e.indexOf(".", t)), 10);
    var i = e.indexOf("Trident/");
    if (i > 0) {
        var s = e.indexOf("rv:");
        return parseInt(e.substring(s + 3, e.indexOf(".", s)), 10)
    }
    var n = e.indexOf("Edge/");
    return n > 0 ? parseInt(e.substring(n + 5, e.indexOf(".", n)), 10) : -1
}
let Gt;

function ji() {
    ji.init || (ji.init = !0, Gt = Wc() !== -1)
}
var Ci = {
    name: "ResizeObserver",
    props: {
        emitOnMount: {
            type: Boolean,
            default: !1
        },
        ignoreWidth: {
            type: Boolean,
            default: !1
        },
        ignoreHeight: {
            type: Boolean,
            default: !1
        }
    },
    emits: ["notify"],
    mounted() {
        ji(), cs(() => {
            this._w = this.$el.offsetWidth, this._h = this.$el.offsetHeight, this.emitOnMount && this.emitSize()
        });
        const e = document.createElement("object");
        this._resizeObject = e, e.setAttribute("aria-hidden", "true"), e.setAttribute("tabindex", -1), e.onload = this.addResizeHandlers, e.type = "text/html", Gt && this.$el.appendChild(e), e.data = "about:blank", Gt || this.$el.appendChild(e)
    },
    beforeUnmount() {
        this.removeResizeHandlers()
    },
    methods: {
        compareAndNotify() {
            (!this.ignoreWidth && this._w !== this.$el.offsetWidth || !this.ignoreHeight && this._h !== this.$el.offsetHeight) && (this._w = this.$el.offsetWidth, this._h = this.$el.offsetHeight, this.emitSize())
        },
        emitSize() {
            this.$emit("notify", {
                width: this._w,
                height: this._h
            })
        },
        addResizeHandlers() {
            this._resizeObject.contentDocument.defaultView.addEventListener("resize", this.compareAndNotify), this.compareAndNotify()
        },
        removeResizeHandlers() {
            this._resizeObject && this._resizeObject.onload && (!Gt && this._resizeObject.contentDocument && this._resizeObject.contentDocument.defaultView.removeEventListener("resize", this.compareAndNotify), this.$el.removeChild(this._resizeObject), this._resizeObject.onload = null, this._resizeObject = null)
        }
    }
};
const Kc = po();
fo("data-v-b329ee4c");
const qc = {
    class: "resize-observer",
    tabindex: "-1"
};
ho();
const Zc = Kc((e, t, i, s, n, r) => (V(), De("div", qc)));
Ci.render = Zc;
Ci.__scopeId = "data-v-b329ee4c";
Ci.__file = "src/components/ResizeObserver.vue";

function ei(e) {
    return typeof Symbol == "function" && typeof Symbol.iterator == "symbol" ? ei = function(t) {
        return typeof t
    } : ei = function(t) {
        return t && typeof Symbol == "function" && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t
    }, ei(e)
}

function Jc(e, t) {
    if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
}

function nn(e, t) {
    for (var i = 0; i < t.length; i++) {
        var s = t[i];
        s.enumerable = s.enumerable || !1, s.configurable = !0, "value" in s && (s.writable = !0), Object.defineProperty(e, s.key, s)
    }
}

function Xc(e, t, i) {
    return t && nn(e.prototype, t), i && nn(e, i), e
}

function rn(e) {
    return Qc(e) || Gc(e) || ea(e) || ta()
}

function Qc(e) {
    if (Array.isArray(e)) return Yi(e)
}

function Gc(e) {
    if (typeof Symbol < "u" && Symbol.iterator in Object(e)) return Array.from(e)
}

function ea(e, t) {
    if (!!e) {
        if (typeof e == "string") return Yi(e, t);
        var i = Object.prototype.toString.call(e).slice(8, -1);
        if (i === "Object" && e.constructor && (i = e.constructor.name), i === "Map" || i === "Set") return Array.from(e);
        if (i === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(i)) return Yi(e, t)
    }
}

function Yi(e, t) {
    (t == null || t > e.length) && (t = e.length);
    for (var i = 0, s = new Array(t); i < t; i++) s[i] = e[i];
    return s
}

function ta() {
    throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)
}

function ia(e) {
    var t;
    return typeof e == "function" ? t = {
        callback: e
    } : t = e, t
}

function sa(e, t) {
    var i = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {},
        s, n, r, o = function(c) {
            for (var u = arguments.length, f = new Array(u > 1 ? u - 1 : 0), p = 1; p < u; p++) f[p - 1] = arguments[p];
            if (r = f, !(s && c === n)) {
                var b = i.leading;
                typeof b == "function" && (b = b(c, n)), (!s || c !== n) && b && e.apply(void 0, [c].concat(rn(r))), n = c, clearTimeout(s), s = setTimeout(function() {
                    e.apply(void 0, [c].concat(rn(r))), s = 0
                }, t)
            }
        };
    return o._clear = function() {
        clearTimeout(s), s = null
    }, o
}

function nr(e, t) {
    if (e === t) return !0;
    if (ei(e) === "object") {
        for (var i in e)
            if (!nr(e[i], t[i])) return !1;
        return !0
    }
    return !1
}
var na = function() {
    function e(t, i, s) {
        Jc(this, e), this.el = t, this.observer = null, this.frozen = !1, this.createObserver(i, s)
    }
    return Xc(e, [{
        key: "createObserver",
        value: function(i, s) {
            var n = this;
            if (this.observer && this.destroyObserver(), !this.frozen) {
                if (this.options = ia(i), this.callback = function(l, c) {
                        n.options.callback(l, c), l && n.options.once && (n.frozen = !0, n.destroyObserver())
                    }, this.callback && this.options.throttle) {
                    var r = this.options.throttleOptions || {},
                        o = r.leading;
                    this.callback = sa(this.callback, this.options.throttle, {
                        leading: function(c) {
                            return o === "both" || o === "visible" && c || o === "hidden" && !c
                        }
                    })
                }
                this.oldResult = void 0, this.observer = new IntersectionObserver(function(l) {
                    var c = l[0];
                    if (l.length > 1) {
                        var u = l.find(function(p) {
                            return p.isIntersecting
                        });
                        u && (c = u)
                    }
                    if (n.callback) {
                        var f = c.isIntersecting && c.intersectionRatio >= n.threshold;
                        if (f === n.oldResult) return;
                        n.oldResult = f, n.callback(f, c)
                    }
                }, this.options.intersection), cs(function() {
                    n.observer && n.observer.observe(n.el)
                })
            }
        }
    }, {
        key: "destroyObserver",
        value: function() {
            this.observer && (this.observer.disconnect(), this.observer = null), this.callback && this.callback._clear && (this.callback._clear(), this.callback = null)
        }
    }, {
        key: "threshold",
        get: function() {
            return this.options.intersection && typeof this.options.intersection.threshold == "number" ? this.options.intersection.threshold : 0
        }
    }]), e
}();

function rr(e, t, i) {
    var s = t.value;
    if (!!s)
        if (typeof IntersectionObserver > "u") console.warn("[vue-observe-visibility] IntersectionObserver API is not available in your browser. Please install this polyfill: https://github.com/w3c/IntersectionObserver/tree/master/polyfill");
        else {
            var n = new na(e, s, i);
            e._vue_visibilityState = n
        }
}

function ra(e, t, i) {
    var s = t.value,
        n = t.oldValue;
    if (!nr(s, n)) {
        var r = e._vue_visibilityState;
        if (!s) {
            or(e);
            return
        }
        r ? r.createObserver(s, i) : rr(e, {
            value: s
        }, i)
    }
}

function or(e) {
    var t = e._vue_visibilityState;
    t && (t.destroyObserver(), delete e._vue_visibilityState)
}
var oa = {
    beforeMount: rr,
    updated: ra,
    unmounted: or
};

function la(e) {
    return {
        all: e = e || new Map,
        on: function(t, i) {
            var s = e.get(t);
            s && s.push(i) || e.set(t, [i])
        },
        off: function(t, i) {
            var s = e.get(t);
            s && s.splice(s.indexOf(i) >>> 0, 1)
        },
        emit: function(t, i) {
            (e.get(t) || []).slice().map(function(s) {
                s(i)
            }), (e.get("*") || []).slice().map(function(s) {
                s(t, i)
            })
        }
    }
}
var lr = {
        itemsLimit: 1e3
    },
    ca = /(auto|scroll)/;

function cr(e, t) {
    return e.parentNode === null ? t : cr(e.parentNode, t.concat([e]))
}
var Ii = function(t, i) {
        return getComputedStyle(t, null).getPropertyValue(i)
    },
    aa = function(t) {
        return Ii(t, "overflow") + Ii(t, "overflow-y") + Ii(t, "overflow-x")
    },
    da = function(t) {
        return ca.test(aa(t))
    };

function on(e) {
    if (e instanceof HTMLElement || e instanceof SVGElement) {
        for (var t = cr(e.parentNode, []), i = 0; i < t.length; i += 1)
            if (da(t[i])) return t[i];
        return document.scrollingElement || document.documentElement
    }
}

function Ui(e) {
    return Ui = typeof Symbol == "function" && typeof Symbol.iterator == "symbol" ? function(t) {
        return typeof t
    } : function(t) {
        return t && typeof Symbol == "function" && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t
    }, Ui(e)
}
var ar = {
    items: {
        type: Array,
        required: !0
    },
    keyField: {
        type: String,
        default: "id"
    },
    direction: {
        type: String,
        default: "vertical",
        validator: function(t) {
            return ["vertical", "horizontal"].includes(t)
        }
    },
    listTag: {
        type: String,
        default: "div"
    },
    itemTag: {
        type: String,
        default: "div"
    }
};

function dr() {
    return this.items.length && Ui(this.items[0]) !== "object"
}
var Wi = !1;
if (typeof window < "u") {
    Wi = !1;
    try {
        var ua = Object.defineProperty({}, "passive", {
            get: function() {
                Wi = !0
            }
        });
        window.addEventListener("test", null, ua)
    } catch {}
}
let fa = 0;
var It = {
    name: "RecycleScroller",
    components: {
        ResizeObserver: Ci
    },
    directives: {
        ObserveVisibility: oa
    },
    props: {
        ...ar,
        itemSize: {
            type: Number,
            default: null
        },
        gridItems: {
            type: Number,
            default: void 0
        },
        itemSecondarySize: {
            type: Number,
            default: void 0
        },
        minItemSize: {
            type: [Number, String],
            default: null
        },
        sizeField: {
            type: String,
            default: "size"
        },
        typeField: {
            type: String,
            default: "type"
        },
        buffer: {
            type: Number,
            default: 200
        },
        pageMode: {
            type: Boolean,
            default: !1
        },
        prerender: {
            type: Number,
            default: 0
        },
        emitUpdate: {
            type: Boolean,
            default: !1
        },
        skipHover: {
            type: Boolean,
            default: !1
        },
        listTag: {
            type: String,
            default: "div"
        },
        itemTag: {
            type: String,
            default: "div"
        },
        listClass: {
            type: [String, Object, Array],
            default: ""
        },
        itemClass: {
            type: [String, Object, Array],
            default: ""
        }
    },
    emits: ["resize", "visible", "hidden", "update", "scroll-start", "scroll-end"],
    data() {
        return {
            pool: [],
            totalSize: 0,
            ready: !1,
            hoverKey: null
        }
    },
    computed: {
        sizes() {
            if (this.itemSize === null) {
                const e = {
                        "-1": {
                            accumulator: 0
                        }
                    },
                    t = this.items,
                    i = this.sizeField,
                    s = this.minItemSize;
                let n = 1e4,
                    r = 0,
                    o;
                for (let l = 0, c = t.length; l < c; l++) o = t[l][i] || s, o < n && (n = o), r += o, e[l] = {
                    accumulator: r,
                    size: o
                };
                return this.$_computedMinItemSize = n, e
            }
            return []
        },
        simpleArray: dr
    },
    watch: {
        items() {
            this.updateVisibleItems(!0)
        },
        pageMode() {
            this.applyPageMode(), this.updateVisibleItems(!1)
        },
        sizes: {
            handler() {
                this.updateVisibleItems(!1)
            },
            deep: !0
        },
        gridItems() {
            this.updateVisibleItems(!0)
        },
        itemSecondarySize() {
            this.updateVisibleItems(!0)
        }
    },
    created() {
        this.$_startIndex = 0, this.$_endIndex = 0, this.$_views = new Map, this.$_unusedViews = new Map, this.$_scrollDirty = !1, this.$_lastUpdateScrollPosition = 0, this.prerender && (this.$_prerender = !0, this.updateVisibleItems(!1)), this.gridItems && !this.itemSize && console.error("[vue-recycle-scroller] You must provide an itemSize when using gridItems")
    },
    mounted() {
        this.applyPageMode(), this.$nextTick(() => {
            this.$_prerender = !1, this.updateVisibleItems(!0), this.ready = !0
        })
    },
    activated() {
        const e = this.$_lastUpdateScrollPosition;
        typeof e == "number" && this.$nextTick(() => {
            this.scrollToPosition(e)
        })
    },
    beforeUnmount() {
        this.removeListeners()
    },
    methods: {
        addView(e, t, i, s, n) {
            const r = ns({
                    id: fa++,
                    index: t,
                    used: !0,
                    key: s,
                    type: n
                }),
                o = Mn({
                    item: i,
                    position: 0,
                    nr: r
                });
            return e.push(o), o
        },
        unuseView(e, t = !1) {
            const i = this.$_unusedViews,
                s = e.nr.type;
            let n = i.get(s);
            n || (n = [], i.set(s, n)), n.push(e), t || (e.nr.used = !1, e.position = -9999, this.$_views.delete(e.nr.key))
        },
        handleResize() {
            this.$emit("resize"), this.ready && this.updateVisibleItems(!1)
        },
        handleScroll(e) {
            this.$_scrollDirty || (this.$_scrollDirty = !0, requestAnimationFrame(() => {
                this.$_scrollDirty = !1;
                const {
                    continuous: t
                } = this.updateVisibleItems(!1, !0);
                t || (clearTimeout(this.$_refreshTimout), this.$_refreshTimout = setTimeout(this.handleScroll, 100))
            }))
        },
        handleVisibilityChange(e, t) {
            this.ready && (e || t.boundingClientRect.width !== 0 || t.boundingClientRect.height !== 0 ? (this.$emit("visible"), requestAnimationFrame(() => {
                this.updateVisibleItems(!1)
            })) : this.$emit("hidden"))
        },
        updateVisibleItems(e, t = !1) {
            const i = this.itemSize,
                s = this.gridItems || 1,
                n = this.itemSecondarySize || i,
                r = this.$_computedMinItemSize,
                o = this.typeField,
                l = this.simpleArray ? null : this.keyField,
                c = this.items,
                u = c.length,
                f = this.sizes,
                p = this.$_views,
                b = this.$_unusedViews,
                T = this.pool;
            let M, R, y, L, Q;
            if (!u) M = R = L = Q = y = 0;
            else if (this.$_prerender) M = L = 0, R = Q = Math.min(this.prerender, c.length), y = null;
            else {
                const $ = this.getScroll();
                if (t) {
                    let k = $.start - this.$_lastUpdateScrollPosition;
                    if (k < 0 && (k = -k), i === null && k < r || k < i) return {
                        continuous: !0
                    }
                }
                this.$_lastUpdateScrollPosition = $.start;
                const ee = this.buffer;
                $.start -= ee, $.end += ee;
                let we = 0;
                if (this.$refs.before && (we = this.$refs.before.scrollHeight, $.start -= we), this.$refs.after) {
                    const k = this.$refs.after.scrollHeight;
                    $.end += k
                }
                if (i === null) {
                    let k, I = 0,
                        x = u - 1,
                        O = ~~(u / 2),
                        w;
                    do w = O, k = f[O].accumulator, k < $.start ? I = O : O < u - 1 && f[O + 1].accumulator > $.start && (x = O), O = ~~((I + x) / 2); while (O !== w);
                    for (O < 0 && (O = 0), M = O, y = f[u - 1].accumulator, R = O; R < u && f[R].accumulator < $.end; R++);
                    for (R === -1 ? R = c.length - 1 : (R++, R > u && (R = u)), L = M; L < u && we + f[L].accumulator < $.start; L++);
                    for (Q = L; Q < u && we + f[Q].accumulator < $.end; Q++);
                } else M = ~~($.start / i * s), M -= M % s, R = Math.ceil($.end / i * s), L = Math.max(0, Math.floor(($.start - we) / i * s)), Q = Math.floor(($.end - we) / i * s), M < 0 && (M = 0), R > u && (R = u), L < 0 && (L = 0), Q > u && (Q = u), y = Math.ceil(u / s) * i
            }
            R - M > lr.itemsLimit && this.itemsLimitError(), this.totalSize = y;
            let E;
            const J = M <= this.$_endIndex && R >= this.$_startIndex;
            if (this.$_continuous !== J) {
                if (J) {
                    p.clear(), b.clear();
                    for (let $ = 0, ee = T.length; $ < ee; $++) E = T[$], this.unuseView(E)
                }
                this.$_continuous = J
            } else if (J)
                for (let $ = 0, ee = T.length; $ < ee; $++) E = T[$], E.nr.used && (e && (E.nr.index = c.indexOf(E.item)), (E.nr.index === -1 || E.nr.index < M || E.nr.index >= R) && this.unuseView(E));
            const ye = J ? null : new Map;
            let ae, he, ue, q;
            for (let $ = M; $ < R; $++) {
                ae = c[$];
                const ee = l ? ae[l] : ae;
                if (ee == null) throw new Error(`Key is ${ee} on item (keyField is '${l}')`);
                if (E = p.get(ee), !i && !f[$].size) {
                    E && this.unuseView(E);
                    continue
                }
                E ? (E.nr.used = !0, E.item = ae) : ($ === c.length - 1 && this.$emit("scroll-end"), $ === 0 && this.$emit("scroll-start"), he = ae[o], ue = b.get(he), J ? ue && ue.length ? (E = ue.pop(), E.item = ae, E.nr.used = !0, E.nr.index = $, E.nr.key = ee, E.nr.type = he) : E = this.addView(T, $, ae, ee, he) : (q = ye.get(he) || 0, (!ue || q >= ue.length) && (E = this.addView(T, $, ae, ee, he), this.unuseView(E, !0), ue = b.get(he)), E = ue[q], E.item = ae, E.nr.used = !0, E.nr.index = $, E.nr.key = ee, E.nr.type = he, ye.set(he, q + 1), q++), p.set(ee, E)), i === null ? (E.position = f[$ - 1].accumulator, E.offset = 0) : (E.position = Math.floor($ / s) * i, E.offset = $ % s * n)
            }
            return this.$_startIndex = M, this.$_endIndex = R, this.emitUpdate && this.$emit("update", M, R, L, Q), clearTimeout(this.$_sortTimer), this.$_sortTimer = setTimeout(this.sortViews, 300), {
                continuous: J
            }
        },
        getListenerTarget() {
            let e = on(this.$el);
            return window.document && (e === window.document.documentElement || e === window.document.body) && (e = window), e
        },
        getScroll() {
            const {
                $el: e,
                direction: t
            } = this, i = t === "vertical";
            let s;
            if (this.pageMode) {
                const n = e.getBoundingClientRect(),
                    r = i ? n.height : n.width;
                let o = -(i ? n.top : n.left),
                    l = i ? window.innerHeight : window.innerWidth;
                o < 0 && (l += o, o = 0), o + l > r && (l = r - o), s = {
                    start: o,
                    end: o + l
                }
            } else i ? s = {
                start: e.scrollTop,
                end: e.scrollTop + e.clientHeight
            } : s = {
                start: e.scrollLeft,
                end: e.scrollLeft + e.clientWidth
            };
            return s
        },
        applyPageMode() {
            this.pageMode ? this.addListeners() : this.removeListeners()
        },
        addListeners() {
            this.listenerTarget = this.getListenerTarget(), this.listenerTarget.addEventListener("scroll", this.handleScroll, Wi ? {
                passive: !0
            } : !1), this.listenerTarget.addEventListener("resize", this.handleResize)
        },
        removeListeners() {
            !this.listenerTarget || (this.listenerTarget.removeEventListener("scroll", this.handleScroll), this.listenerTarget.removeEventListener("resize", this.handleResize), this.listenerTarget = null)
        },
        scrollToItem(e) {
            let t;
            this.itemSize === null ? t = e > 0 ? this.sizes[e - 1].accumulator : 0 : t = Math.floor(e / this.gridItems) * this.itemSize, this.scrollToPosition(t)
        },
        scrollToPosition(e) {
            const t = this.direction === "vertical" ? {
                scroll: "scrollTop",
                start: "top"
            } : {
                scroll: "scrollLeft",
                start: "left"
            };
            let i, s, n;
            if (this.pageMode) {
                const r = on(this.$el),
                    o = r.tagName === "HTML" ? 0 : r[t.scroll],
                    l = r.getBoundingClientRect(),
                    u = this.$el.getBoundingClientRect()[t.start] - l[t.start];
                i = r, s = t.scroll, n = e + o + u
            } else i = this.$el, s = t.scroll, n = e;
            i[s] = n
        },
        itemsLimitError() {
            throw setTimeout(() => {
                console.log("It seems the scroller element isn't scrolling, so it tries to render all the items at once.", "Scroller:", this.$el), console.log("Make sure the scroller has a fixed height (or width) and 'overflow-y' (or 'overflow-x') set to 'auto' so it can scroll correctly and only render the items visible in the scroll viewport.")
            }), new Error("Rendered items limit reached")
        },
        sortViews() {
            this.pool.sort((e, t) => e.index - t.index)
        }
    }
};
const ha = {
        key: 0,
        ref: "before",
        class: "vue-recycle-scroller__slot"
    },
    pa = {
        key: 1,
        ref: "after",
        class: "vue-recycle-scroller__slot"
    };

function ma(e, t, i, s, n, r) {
    const o = _e("ResizeObserver"),
        l = Lo("observe-visibility");
    return Jt((V(), K("div", {
        class: Pe(["vue-recycle-scroller", {
            ready: n.ready,
            "page-mode": i.pageMode,
            [`direction-${e.direction}`]: !0
        }]),
        onScrollPassive: t[0] || (t[0] = (...c) => r.handleScroll && r.handleScroll(...c))
    }, [e.$slots.before ? (V(), K("div", ha, [tt(e.$slots, "before")], 512)) : fe("v-if", !0), (V(), De(ks(i.listTag), {
        ref: "wrapper",
        style: Ke({
            [e.direction === "vertical" ? "minHeight" : "minWidth"]: n.totalSize + "px"
        }),
        class: Pe(["vue-recycle-scroller__item-wrapper", i.listClass])
    }, {
        default: We(() => [(V(!0), K(Te, null, Ho(n.pool, c => (V(), De(ks(i.itemTag), _s({
            key: c.nr.id,
            style: n.ready ? {
                transform: `translate${e.direction==="vertical"?"Y":"X"}(${c.position}px) translate${e.direction==="vertical"?"X":"Y"}(${c.offset}px)`,
                width: i.gridItems ? `${e.direction==="vertical"&&i.itemSecondarySize||i.itemSize}px` : void 0,
                height: i.gridItems ? `${e.direction==="horizontal"&&i.itemSecondarySize||i.itemSize}px` : void 0
            } : null,
            class: ["vue-recycle-scroller__item-view", [i.itemClass, {
                hover: !i.skipHover && n.hoverKey === c.nr.key
            }]]
        }, Fo(i.skipHover ? {} : {
            mouseenter: () => {
                n.hoverKey = c.nr.key
            },
            mouseleave: () => {
                n.hoverKey = null
            }
        })), {
            default: We(() => [tt(e.$slots, "default", {
                item: c.item,
                index: c.nr.index,
                active: c.nr.used
            })]),
            _: 2
        }, 1040, ["style", "class"]))), 128)), tt(e.$slots, "empty")]),
        _: 3
    }, 8, ["style", "class"])), e.$slots.after ? (V(), K("div", pa, [tt(e.$slots, "after")], 512)) : fe("v-if", !0), ce(o, {
        onNotify: r.handleResize
    }, null, 8, ["onNotify"])], 34)), [
        [l, r.handleVisibilityChange]
    ])
}
It.render = ma;
It.__file = "src/components/RecycleScroller.vue";
var Bt = {
    name: "DynamicScroller",
    components: {
        RecycleScroller: It
    },
    provide() {
        return typeof ResizeObserver < "u" && (this.$_resizeObserver = new ResizeObserver(e => {
            requestAnimationFrame(() => {
                if (!!Array.isArray(e)) {
                    for (const t of e)
                        if (t.target) {
                            const i = new CustomEvent("resize", {
                                detail: {
                                    contentRect: t.contentRect
                                }
                            });
                            t.target.dispatchEvent(i)
                        }
                }
            })
        })), {
            vscrollData: this.vscrollData,
            vscrollParent: this,
            vscrollResizeObserver: this.$_resizeObserver
        }
    },
    inheritAttrs: !1,
    props: {
        ...ar,
        minItemSize: {
            type: [Number, String],
            required: !0
        }
    },
    emits: ["resize", "visible"],
    data() {
        return {
            vscrollData: {
                active: !0,
                sizes: {},
                validSizes: {},
                keyField: this.keyField,
                simpleArray: !1
            }
        }
    },
    computed: {
        simpleArray: dr,
        itemsWithSize() {
            const e = [],
                {
                    items: t,
                    keyField: i,
                    simpleArray: s
                } = this,
                n = this.vscrollData.sizes,
                r = t.length;
            for (let o = 0; o < r; o++) {
                const l = t[o],
                    c = s ? o : l[i];
                let u = n[c];
                typeof u > "u" && !this.$_undefinedMap[c] && (u = 0), e.push({
                    item: l,
                    id: c,
                    size: u
                })
            }
            return e
        }
    },
    watch: {
        items() {
            this.forceUpdate(!1)
        },
        simpleArray: {
            handler(e) {
                this.vscrollData.simpleArray = e
            },
            immediate: !0
        },
        direction(e) {
            this.forceUpdate(!0)
        },
        itemsWithSize(e, t) {
            const i = this.$el.scrollTop;
            let s = 0,
                n = 0;
            const r = Math.min(e.length, t.length);
            for (let l = 0; l < r && !(s >= i); l++) s += t[l].size || this.minItemSize, n += e[l].size || this.minItemSize;
            const o = n - s;
            o !== 0 && (this.$el.scrollTop += o)
        }
    },
    beforeCreate() {
        this.$_updates = [], this.$_undefinedSizes = 0, this.$_undefinedMap = {}, this.$_events = la()
    },
    activated() {
        this.vscrollData.active = !0
    },
    deactivated() {
        this.vscrollData.active = !1
    },
    unmounted() {
        this.$_events.all.clear()
    },
    methods: {
        onScrollerResize() {
            this.$refs.scroller && this.forceUpdate(), this.$emit("resize")
        },
        onScrollerVisible() {
            this.$_events.emit("vscroll:update", {
                force: !1
            }), this.$emit("visible")
        },
        forceUpdate(e = !0) {
            (e || this.simpleArray) && (this.vscrollData.validSizes = {}), this.$_events.emit("vscroll:update", {
                force: !0
            })
        },
        scrollToItem(e) {
            const t = this.$refs.scroller;
            t && t.scrollToItem(e)
        },
        getItemSize(e, t = void 0) {
            const i = this.simpleArray ? t != null ? t : this.items.indexOf(e) : e[this.keyField];
            return this.vscrollData.sizes[i] || 0
        },
        scrollToBottom() {
            if (this.$_scrollingToBottom) return;
            this.$_scrollingToBottom = !0;
            const e = this.$el;
            this.$nextTick(() => {
                e.scrollTop = e.scrollHeight + 5e3;
                const t = () => {
                    e.scrollTop = e.scrollHeight + 5e3, requestAnimationFrame(() => {
                        e.scrollTop = e.scrollHeight + 5e3, this.$_undefinedSizes === 0 ? this.$_scrollingToBottom = !1 : requestAnimationFrame(t)
                    })
                };
                requestAnimationFrame(t)
            })
        }
    }
};

function _a(e, t, i, s, n, r) {
    const o = _e("RecycleScroller");
    return V(), De(o, _s({
        ref: "scroller",
        items: r.itemsWithSize,
        "min-item-size": i.minItemSize,
        direction: e.direction,
        "key-field": "id",
        "list-tag": e.listTag,
        "item-tag": e.itemTag
    }, e.$attrs, {
        onResize: r.onScrollerResize,
        onVisible: r.onScrollerVisible
    }), {
        default: We(({
            item: l,
            index: c,
            active: u
        }) => [tt(e.$slots, "default", gr(Gn({
            item: l.item,
            index: c,
            active: u,
            itemWithSize: l
        })))]),
        before: We(() => [tt(e.$slots, "before")]),
        after: We(() => [tt(e.$slots, "after")]),
        empty: We(() => [tt(e.$slots, "empty")]),
        _: 3
    }, 16, ["items", "min-item-size", "direction", "list-tag", "item-tag", "onResize", "onVisible"])
}
Bt.render = _a;
Bt.__file = "src/components/DynamicScroller.vue";
var ai = {
    name: "DynamicScrollerItem",
    inject: ["vscrollData", "vscrollParent", "vscrollResizeObserver"],
    props: {
        item: {
            required: !0
        },
        watchData: {
            type: Boolean,
            default: !1
        },
        active: {
            type: Boolean,
            required: !0
        },
        index: {
            type: Number,
            default: void 0
        },
        sizeDependencies: {
            type: [Array, Object],
            default: null
        },
        emitResize: {
            type: Boolean,
            default: !1
        },
        tag: {
            type: String,
            default: "div"
        }
    },
    emits: ["resize"],
    computed: {
        id() {
            if (this.vscrollData.simpleArray) return this.index;
            if (this.item.hasOwnProperty(this.vscrollData.keyField)) return this.item[this.vscrollData.keyField];
            throw new Error(`keyField '${this.vscrollData.keyField}' not found in your item. You should set a valid keyField prop on your Scroller`)
        },
        size() {
            return this.vscrollData.validSizes[this.id] && this.vscrollData.sizes[this.id] || 0
        },
        finalActive() {
            return this.active && this.vscrollData.active
        }
    },
    watch: {
        watchData: "updateWatchData",
        id() {
            this.size || this.onDataUpdate()
        },
        finalActive(e) {
            this.size || (e ? this.vscrollParent.$_undefinedMap[this.id] || (this.vscrollParent.$_undefinedSizes++, this.vscrollParent.$_undefinedMap[this.id] = !0) : this.vscrollParent.$_undefinedMap[this.id] && (this.vscrollParent.$_undefinedSizes--, this.vscrollParent.$_undefinedMap[this.id] = !1)), this.vscrollResizeObserver ? e ? this.observeSize() : this.unobserveSize() : e && this.$_pendingVScrollUpdate === this.id && this.updateSize()
        }
    },
    created() {
        if (!this.$isServer && (this.$_forceNextVScrollUpdate = null, this.updateWatchData(), !this.vscrollResizeObserver)) {
            for (const e in this.sizeDependencies) this.$watch(() => this.sizeDependencies[e], this.onDataUpdate);
            this.vscrollParent.$_events.on("vscroll:update", this.onVscrollUpdate)
        }
    },
    mounted() {
        this.vscrollData.active && (this.updateSize(), this.observeSize())
    },
    beforeUnmount() {
        this.vscrollParent.$_events.off("vscroll:update", this.onVscrollUpdate), this.unobserveSize()
    },
    methods: {
        updateSize() {
            this.finalActive ? this.$_pendingSizeUpdate !== this.id && (this.$_pendingSizeUpdate = this.id, this.$_forceNextVScrollUpdate = null, this.$_pendingVScrollUpdate = null, this.computeSize(this.id)) : this.$_forceNextVScrollUpdate = this.id
        },
        updateWatchData() {
            this.watchData && !this.vscrollResizeObserver ? this.$_watchData = this.$watch("item", () => {
                this.onDataUpdate()
            }, {
                deep: !0
            }) : this.$_watchData && (this.$_watchData(), this.$_watchData = null)
        },
        onVscrollUpdate({
            force: e
        }) {
            !this.finalActive && e && (this.$_pendingVScrollUpdate = this.id), (this.$_forceNextVScrollUpdate === this.id || e || !this.size) && this.updateSize()
        },
        onDataUpdate() {
            this.updateSize()
        },
        computeSize(e) {
            this.$nextTick(() => {
                if (this.id === e) {
                    const t = this.$el.offsetWidth,
                        i = this.$el.offsetHeight;
                    this.applySize(t, i)
                }
                this.$_pendingSizeUpdate = null
            })
        },
        applySize(e, t) {
            const i = ~~(this.vscrollParent.direction === "vertical" ? t : e);
            i && this.size !== i && (this.vscrollParent.$_undefinedMap[this.id] && (this.vscrollParent.$_undefinedSizes--, this.vscrollParent.$_undefinedMap[this.id] = void 0), this.vscrollData.sizes[this.id] = i, this.vscrollData.validSizes[this.id] = !0, this.emitResize && this.$emit("resize", this.id))
        },
        observeSize() {
            !this.vscrollResizeObserver || !this.$el.parentNode || (this.vscrollResizeObserver.observe(this.$el.parentNode), this.$el.parentNode.addEventListener("resize", this.onResize))
        },
        unobserveSize() {
            !this.vscrollResizeObserver || (this.vscrollResizeObserver.unobserve(this.$el.parentNode), this.$el.parentNode.removeEventListener("resize", this.onResize))
        },
        onResize(e) {
            const {
                width: t,
                height: i
            } = e.detail.contentRect;
            this.applySize(t, i)
        }
    },
    render() {
        return pl(this.tag, this.$slots.default())
    }
};
ai.__file = "src/components/DynamicScrollerItem.vue";

function ga(e, t) {
    e.component("".concat(t, "recycle-scroller"), It), e.component("".concat(t, "RecycleScroller"), It), e.component("".concat(t, "dynamic-scroller"), Bt), e.component("".concat(t, "DynamicScroller"), Bt), e.component("".concat(t, "dynamic-scroller-item"), ai), e.component("".concat(t, "DynamicScrollerItem"), ai)
}
var ya = {
    version: "2.0.0-beta.3",
    install: function(t, i) {
        var s = Object.assign({}, {
            installComponents: !0,
            componentsPrefix: ""
        }, i);
        for (var n in s) typeof s[n] < "u" && (lr[n] = s[n]);
        s.installComponents && ga(t, s.componentsPrefix)
    }
};
const wa = {
        name: "WidgetController",
        components: {
            CloseIcon: Kl,
            LogoIcon: Gl,
            SearchIcon: rc,
            SpinnerIcon: uc,
            LeadIcon: gc,
            ManIcon: Mc,
            LockerIcon: Tc,
            PostOfficeIcon: Fc,
            FaqIcon: Uc,
            RecycleScroller: It,
            DynamicScroller: Bt,
            DynamicScrollerItem: ai
        },
        props: {
            foundPoints: {
                type: Array,
                default: () => []
            },
            isYaMapSdkLoaded: {
                type: Boolean,
                required: !0
            },
            selectedPointId: {
                type: String,
                default: () => ""
            },
            defaultCity: {
                type: String,
                default: () => ""
            },
            pointCustomDesc: {
                type: String,
                default: () => ""
            },
            deliveryPrice: {
                default: () => ""
            },
            showSelectButton: {
                type: Boolean,
                default: () => !1
            },
            waitingPeriod: {
                type: Object,
                default: () => {}
            },
            height: {
                type: Number
            },
            isMobile: {
                type: Boolean,
                default: () => !1
            },
            isKeyboardOverlaps: {
                type: Boolean,
                default: () => !1
            }
        },
        watch: {
            isYaMapSdkLoaded: function(e, t) {
                this.initSuggestView()
            },
            selectedPointId: function(e, t) {
                setTimeout(() => {
                    const i = this.foundPoints.find(s => s.id === e);
                    i && (i.expanded = !0), this.scrollUpPointInfo()
                }, 500)
            }
        },
        data() {
            return {
                selected: "",
                selectedList: [],
                previouslySelected: "",
                isSearching: !1,
                isTyping: !1,
                searchedAddress: "",
                interpretations: {
                    already_paid: "\u041A\u0430\u0440\u0442\u043E\u0439 \u043E\u043D\u043B\u0430\u0439\u043D",
                    cash_on_receipt: "\u041D\u0430\u043B\u0438\u0447\u043D\u044B\u043C\u0438 \u043F\u0440\u0438 \u043F\u043E\u043B\u0443\u0447\u0435\u043D\u0438\u0438",
                    card_on_receipt: "\u041A\u0430\u0440\u0442\u043E\u0439 \u043F\u0440\u0438 \u043F\u043E\u043B\u0443\u0447\u0435\u043D\u0438\u0438",
                    cash_on_receipt__card_on_receipt: "\u041A\u0430\u0440\u0442\u043E\u0439 \u0438 \u043D\u0430\u043B\u0438\u0447\u043D\u044B\u043C\u0438 \u043F\u0440\u0438 \u043F\u043E\u043B\u0443\u0447\u0435\u043D\u0438\u0438"
                },
                days: {
                    d1: "\u041F\u043D",
                    d2: "\u0412\u0442",
                    d3: "\u0421\u0440",
                    d4: "\u0427\u0442",
                    d5: "\u041F\u0442",
                    d6: "\u0421\u0431",
                    d7: "\u0412\u0441"
                }
            }
        },
        updated() {},
        created: function() {
            this.defaultCity && (this.searchedAddress = this.defaultCity)
        },
        computed: {
            getPointCustomDesc: function() {
                return typeof this.deliveryPrice == "function" ? this.pointCustomDesc + " | " + this.deliveryPrice() : this.pointCustomDesc + " | " + this.deliveryPrice
            },
            isTypingInMobile: function() {
                return this.isTyping && this.isMobile
            },
            isTypingInBadKeyboadMobile: function() {
                return this.isTyping && this.isMobile && this.isKeyboardOverlaps
            }
        },
        methods: {
            clearSearchedAddress: function() {
                this.searchedAddress = ""
            },
            visibilityChanged: function(e) {},
            selectButtonClicked: function() {
                this.$emit("selectButtonClicked")
            },
            scrollUpPointInfo: function() {
                const e = this.$refs.pointList.getBoundingClientRect().top,
                    t = document.getElementById("p_" + this.selectedPointId);
                if (t) {
                    const i = t.getBoundingClientRect().top;
                    this.$refs.pointList.scrollBy({
                        top: i - e,
                        behavior: "smooth"
                    })
                }
            },
            unselectPoint: function() {
                this.selected = !1, this.selectedList = [], this.$emit("pointUnselected"), document.querySelectorAll(".widget__marker-extradition.active").forEach(function(e) {
                    e.classList.remove("active")
                })
            },
            pointSelectClicked: function(e) {
                this.selected === e.id ? (this.selected = !1, this.selectedList = [], this.$emit("pointUnselected")) : this.$emit("pointSelected", e.id, e.position.latitude, e.position.longitude)
            },
            uncheck: function(e) {
                this.selected === e && (this.selected = !1, this.selectedList = [])
            },
            interpretSchedule: function(e) {
                let t = [],
                    i = {};
                if (e.restrictions.forEach(n => {
                        var f, p, b, T;
                        let r = (f = n.time_from) == null ? void 0 : f.hours,
                            o = (p = n.time_from) == null ? void 0 : p.minutes,
                            l = (b = n.time_to) == null ? void 0 : b.hours,
                            c = (T = n.time_to) == null ? void 0 : T.minutes;
                        r = r < 10 ? "0" + r : r, o = o < 10 ? "0" + o : o, l = l < 10 ? "0" + l : l, c = c < 10 ? "0" + c : c;
                        let u = r + ":" + o + "\u2013" + l + ":" + c;
                        n.days.forEach(M => {
                            i[u] ? i[u].push(M) : i[u] = [M]
                        })
                    }), Object.keys(i).length === 1) {
                    const n = Object.keys(i)[0];
                    if (i[n].length === 7) return "\u0415\u0436\u0435\u0434\u043D\u0435\u0432\u043D\u043E " + n
                }
                return Object.keys(i).forEach(n => {
                    let r = "",
                        o = "";
                    i[n].forEach((l, c) => {
                        if (r || (r = this.days["d" + l]), !i[n][c + 1]) {
                            o = this.days["d" + l], r === o ? t.push(r + " " + n) : t.push(r + "\u2013" + o + " " + n);
                            return
                        }
                        if (i[n][c + 1] !== l + 1) {
                            o = this.days["d" + l], r === o ? t.push(r + " " + n) : t.push(r + "\u2013" + o + " " + n), r = "", o = "";
                            return
                        }
                    })
                }), t.join("; ")
            },
            interpretPaymentMethods: function(e) {
                if (!e) return "";
                if (e.length === 1) return this.interpretations[e[0]] || "";
                let t = e.indexOf("cash_on_receipt"),
                    i = e.indexOf("card_on_receipt");
                t !== -1 && i !== -1 && (t > i ? (e.splice(t, 1), e.splice(i, 1)) : (e.splice(i, 1), e.splice(t, 1)), e.push("cash_on_receipt__card_on_receipt"));
                let s = [];
                return e.forEach(n => {
                    s.push(this.interpretations[n])
                }), s.join(", ")
            },
            initSuggestView: function() {
                window.YaDelivery.suggestView = new window.ydwmaps.SuggestView("ydw-suggest-adr", {
                    offset: [0, 3]
                }), this.defaultCity && this.searchByAddress(), window.YaDelivery.suggestView.events.add("select", e => {
                    this.searchedAddress = e.originalEvent.item.value, document.activeElement.blur(), this.searchByAddress()
                })
            },
            searchByAddress: function() {
                let e = this.searchedAddress;
                ydwmaps.geocode(e).then(t => {
                    let s = t.geoObjects.get(0).properties.get("boundedBy");
                    const n = document.getElementById("ydw-map"),
                        r = ydwmaps.util.bounds.getCenterAndZoom(s, [n.offsetWidth, n.offsetHeight]),
                        o = r.center[0],
                        l = r.center[1],
                        c = r.zoom;
                    this.$emit("areaSelected", o, l, c)
                })
            }
        }
    },
    va = {
        class: "widget__content transition"
    },
    ba = A("button", {
        class: "widget__map-button"
    }, [A("svg", {
        class: "widget__map-button-img",
        width: "24",
        height: "24",
        viewBox: "0 0 24 24",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg"
    }, [A("path", {
        "fill-rule": "evenodd",
        "clip-rule": "evenodd",
        d: "M7.8 11L13.0464 5.75355C13.2417 5.55829 13.2417 5.24171 13.0464 5.04645L12.3536 4.35355C12.1583 4.15829 11.8417 4.15829 11.6464 4.35355L4 12L11.6464 19.6464C11.8417 19.8417 12.1583 19.8417 12.3536 19.6464L13.0464 18.9536C13.2417 18.7583 13.2417 18.4417 13.0464 18.2464L7.8 13H19.5C19.7761 13 20 12.7761 20 12.5V11.5C20 11.2239 19.7761 11 19.5 11H7.8Z",
        fill: "#21201F"
    })])], -1),
    Ca = [ba],
    xa = A("p", {
        class: "widget__title-text"
    }, "\u0414\u043E\u0441\u0442\u0430\u0432\u043A\u0430", -1),
    Sa = A("p", {
        class: "widget__title-text widget__title-text-second"
    }, "\u30FB\u041F\u0443\u043D\u043A\u0442\u044B \u0432\u044B\u0434\u0430\u0447\u0438", -1),
    Ma = {
        class: "widget__form"
    },
    $a = {
        class: "widget__form-item mobile-title"
    },
    Ia = A("p", {
        class: "widget__label-text"
    }, "\u0410\u0434\u0440\u0435\u0441", -1),
    Oa = {
        class: "widget__lead-wrapper count",
        "data-count": "2",
        hidden: ""
    },
    za = {
        class: "widget__lead"
    },
    Pa = ["data-xxx-ismobile", "data-xxx-selectedList-length"],
    Ta = {
        key: 0,
        class: "widget__start"
    },
    Da = A("p", {
        class: "widget__start-title"
    }, "\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u0430\u0434\u0440\u0435\u0441", -1),
    Aa = A("p", {
        class: "widget__start-desc"
    }, " \u041F\u043E\u043A\u0430\u0436\u0435\u043C \u0431\u043B\u0438\u0436\u0430\u0439\u0448\u0438\u0435 \u043F\u0443\u043D\u043A\u0442\u044B \u0432\u044B\u0434\u0430\u0447\u0438 \u0438 \u043F\u043E\u0441\u0442\u0430\u043C\u0430\u0442\u044B ", -1),
    Ea = [Da, Aa],
    ka = {
        key: 1,
        class: "widget__start"
    },
    La = A("p", {
        class: "widget__start-title"
    }, "\u041D\u0438\u0447\u0435\u0433\u043E \u043D\u0435 \u043D\u0430\u0439\u0434\u0435\u043D\u043E", -1),
    Ha = A("p", {
        class: "widget__start-desc"
    }, " \u041F\u043E\u043F\u0440\u043E\u0431\u0443\u0439\u0442\u0435 \u0438\u0437\u043C\u0435\u043D\u0438\u0442\u044C \u0437\u0430\u043F\u0440\u043E\u0441 \u0438\u043B\u0438 \u043C\u0430\u0441\u0448\u0442\u0430\u0431 ", -1),
    Fa = [La, Ha],
    Ra = {
        key: 2,
        class: "widget__locations",
        ref: "pointList",
        id: "ydw-pointList"
    },
    Ba = ["onClick", "id"],
    Na = {
        class: "widget__location-wrapper"
    },
    Va = {
        class: "widget__location-lead"
    },
    ja = {
        class: "widget__location-content"
    },
    Ya = {
        class: "widget__location-title"
    },
    Ua = {
        class: "widget__location-desc"
    },
    Wa = {
        key: 0
    },
    Ka = {
        key: 1
    },
    qa = {
        key: 2
    },
    Za = ht("\u30FB "),
    Ja = ["data-pickpoint-id", "data-payment_method"],
    Xa = {
        class: "widget__location-faq"
    },
    Qa = {
        class: "widget__location-checkbox"
    },
    Ga = ["onClick", "value", "id"],
    ed = ["for"],
    td = {
        class: "widget__location-details transition"
    },
    id = {
        class: "widget__location-details-wrapper"
    },
    sd = {
        key: 0,
        class: "widget__location-detail"
    },
    nd = A("span", {
        class: "widget__location-detail-span"
    }, " \xB7 \u0433\u0440\u0430\u0444\u0438\u043A \u0440\u0430\u0431\u043E\u0442\u044B", -1),
    rd = {
        key: 1,
        class: "widget__location-detail"
    },
    od = A("span", {
        class: "widget__location-detail-span"
    }, " \xB7 \u0441\u0440\u043E\u043A \u0445\u0440\u0430\u043D\u0435\u043D\u0438\u044F ", -1),
    ld = {
        key: 2,
        class: "widget__location-detail"
    },
    cd = A("span", {
        class: "widget__location-detail-span"
    }, " \xB7 \u0442\u0435\u043B\u0435\u0444\u043E\u043D", -1),
    ad = {
        key: 3,
        class: "widget__location-detail"
    },
    dd = A("span", {
        class: "widget__location-detail-span"
    }, " \xB7 \u043E\u043F\u043B\u0430\u0442\u0430", -1),
    ud = {
        key: 4,
        class: "widget__location-detail"
    },
    fd = A("span", {
        class: "widget__location-detail-span"
    }, " \xB7 \u043A\u0430\u043A \u0434\u043E\u0431\u0440\u0430\u0442\u044C\u0441\u044F", -1),
    hd = {
        key: 0,
        style: {
            height: "72px"
        }
    },
    pd = A("p", {
        class: "widget__list-button-span"
    }, "\u041F\u0440\u043E\u0434\u043E\u043B\u0436\u0438\u0442\u044C", -1),
    md = [pd],
    _d = A("p", {
        class: "widget__list-button-span"
    }, "\u041F\u0440\u043E\u0434\u043E\u043B\u0436\u0438\u0442\u044C", -1),
    gd = [_d];

function yd(e, t, i, s, n, r) {
    const o = _e("LogoIcon"),
        l = _e("SearchIcon"),
        c = _e("CloseIcon"),
        u = _e("LeadIcon"),
        f = _e("ManIcon"),
        p = _e("LockerIcon"),
        b = _e("PostOfficeIcon"),
        T = _e("FaqIcon"),
        M = _e("DynamicScrollerItem"),
        R = _e("DynamicScroller");
    return V(), K("div", va, [A("div", {
        class: Pe(["widget__map-back", {
            active: r.isTypingInMobile
        }])
    }, Ca, 2), A("div", {
        class: Pe(["widget__header", {
            "ydw-widget__header-mobile": r.isTypingInMobile,
            "ydw-widget__header-mobile-bad-keyboard": r.isTypingInBadKeyboadMobile
        }])
    }, [A("div", {
        class: Pe(["widget__title", {
            "ydw-widget__title-mobile": r.isTypingInMobile
        }])
    }, [ce(o, {
        class: "widget__logo"
    }), xa, Sa], 2), A("div", Ma, [A("div", $a, [A("label", {
        class: Pe(["widget__label", {
            "ydw-typing-in-mobile": r.isTypingInMobile
        }])
    }, [Ia, Jt(A("input", {
        onKeyup: t[0] || (t[0] = tn((...y) => r.searchByAddress && r.searchByAddress(...y), ["enter"])),
        "onUpdate:modelValue": t[1] || (t[1] = y => n.searchedAddress = y),
        style: {
            display: "none"
        },
        id: "ydw-suggest",
        type: "text",
        class: "widget__input widget__input--search active mobile-select"
    }, null, 544), [
        [Gs, n.searchedAddress]
    ]), Jt(A("input", {
        onKeyup: t[2] || (t[2] = tn((...y) => r.searchByAddress && r.searchByAddress(...y), ["enter"])),
        "onUpdate:modelValue": t[3] || (t[3] = y => n.searchedAddress = y),
        id: "ydw-suggest-adr",
        type: "text",
        onFocus: t[4] || (t[4] = y => n.isTyping = !0),
        onBlur: t[5] || (t[5] = y => n.isTyping = !1),
        class: Pe(["widget__input widget__input--search active mobile-select", {
            "ydw-suggest-inactive": !n.searchedAddress
        }])
    }, null, 34), [
        [Gs, n.searchedAddress]
    ]), A("button", {
        onClick: t[6] || (t[6] = (...y) => r.searchByAddress && r.searchByAddress(...y)),
        class: "widget__label-button widget__label-button--search"
    }, [n.searchedAddress ? (V(), De(c, {
        key: 1,
        onMousedown: r.clearSearchedAddress,
        class: "widget__label-img",
        style: Ke({
            "margin-top": r.isTypingInMobile ? "3px" : "0"
        })
    }, null, 8, ["onMousedown", "style"])) : (V(), De(l, {
        key: 0,
        class: "widget__label-img"
    }))])], 2), A("div", Oa, [A("button", za, [ce(u, {
        class: "widget__lead-img"
    })])])])])], 2), !i.isMobile || n.selectedList.length > 0 ? (V(), K("div", {
        key: 0,
        "data-xxx-ismobile": i.isMobile,
        "data-xxx-selectedList-length": n.selectedList.length,
        class: "widget__list",
        style: Ke("height:" + (i.isMobile ? 340 : i.height - 155) + "px;")
    }, [n.searchedAddress === "" && i.foundPoints.length === 0 ? (V(), K("div", Ta, Ea)) : n.searchedAddress !== "" && i.foundPoints.length === 0 ? (V(), K("div", ka, Fa)) : (V(), K("div", Ra, [ce(R, {
        "page-mode": "",
        items: i.foundPoints,
        "min-item-size": 1,
        class: "scroller"
    }, {
        default: We(({
            item: y,
            index: L,
            active: Q
        }) => [ce(M, {
            item: y,
            active: Q,
            "size-dependencies": [y.message],
            "data-index": L
        }, {
            default: We(() => {
                var E;
                return [A("div", {
                    onClick: J => i.isMobile ? "" : y.expanded = !y.expanded,
                    class: Pe(["widget__location", {
                        active: y.expanded
                    }]),
                    id: "p_" + y.id
                }, [A("div", Na, [A("div", Va, [y.type == "pickup_point" ? (V(), De(f, {
                    key: 0,
                    class: "widget__location-img"
                })) : fe("", !0), y.type == "terminal" ? (V(), De(p, {
                    key: 1,
                    class: "widget__location-img"
                })) : fe("", !0), y.is_post_office ? (V(), De(b, {
                    key: 2,
                    class: "widget__location-img"
                })) : fe("", !0), A("div", ja, [A("p", Ya, ct((E = y.address) == null ? void 0 : E.full_address), 1), A("p", Ua, [y.type == "pickup_point" ? (V(), K("span", Wa, "\u041F\u0443\u043D\u043A\u0442 \u0432\u044B\u0434\u0430\u0447\u0438")) : fe("", !0), y.type == "terminal" ? (V(), K("span", Ka, "\u041F\u043E\u0441\u0442\u0430\u043C\u0430\u0442")) : fe("", !0), y.is_post_office ? (V(), K("span", qa, "\u041F\u043E\u0447\u0442\u0430 \u0420\u043E\u0441\u0441\u0438\u0438")) : fe("", !0), Za, A("span", {
                    class: "ydw-point-desc not-actual",
                    "data-pickpoint-id": y.id,
                    "data-payment_method": y.payment_methods && y.payment_methods.length > 0 ? y.payment_methods[0] : ""
                }, ct(i.pointCustomDesc), 9, Ja)])])]), A("div", Xa, [ce(T, {
                    class: "widget__location-faq-img"
                })]), i.isMobile ? (V(), De(c, {
                    key: 0,
                    onClick: r.unselectPoint
                }, null, 8, ["onClick"])) : fe("", !0), A("div", Qa, [Jt(A("input", {
                    "onUpdate:modelValue": t[7] || (t[7] = J => n.selectedList = J),
                    onClick: J => r.pointSelectClicked(y),
                    type: "checkbox",
                    value: y.id,
                    name: "pointSelect",
                    class: "widget__location-checkbox-input",
                    id: "ps_" + y.id
                }, null, 8, Ga), [
                    [kl, n.selectedList]
                ]), A("label", {
                    class: "widget__location-checkbox-label",
                    for: "ps_" + y.id
                }, null, 8, ed)])]), A("div", td, [A("div", id, [y.schedule ? (V(), K("p", sd, [ht(ct(r.interpretSchedule(y.schedule)) + " ", 1), nd])) : fe("", !0), i.waitingPeriod[y.type] ? (V(), K("p", rd, [ht(ct(i.waitingPeriod[y.type]) + " ", 1), od])) : fe("", !0), y.contact ? (V(), K("p", ld, [ht(ct(y.contact.phone) + " ", 1), cd])) : fe("", !0), y.payment_methods && y.payment_methods.length > 0 ? (V(), K("p", ad, [ht(ct(r.interpretPaymentMethods(y.payment_methods)) + " ", 1), dd])) : fe("", !0), y.address && y.address.comment ? (V(), K("p", ud, [ht(ct(y.address.comment) + " ", 1), fd])) : fe("", !0)])])], 10, Ba)]
            }),
            _: 2
        }, 1032, ["item", "active", "size-dependencies", "data-index"])]),
        _: 1
    }, 8, ["items"]), i.foundPoints.length ? (V(), K("div", hd)) : fe("", !0)], 512)), i.showSelectButton && i.selectedPointId && n.selected ? (V(), K("button", {
        key: 3,
        onClick: t[8] || (t[8] = (...y) => r.selectButtonClicked && r.selectButtonClicked(...y)),
        class: "widget__list-button"
    }, md)) : fe("", !0)], 12, Pa)) : fe("", !0), i.isMobile && i.showSelectButton && i.selectedPointId && n.selected ? (V(), K("button", {
        key: 1,
        onClick: t[9] || (t[9] = (...y) => r.selectButtonClicked && r.selectButtonClicked(...y)),
        class: "widget__list-button",
        style: {
            "z-index": "55"
        }
    }, gd)) : fe("", !0)])
}
const wd = Ie(wa, [
        ["render", yd]
    ]),
    vd = {},
    bd = {
        width: "24",
        height: "24",
        viewBox: "0 0 24 24",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg"
    },
    Cd = A("path", {
        "fill-rule": "evenodd",
        "clip-rule": "evenodd",
        d: "M7.8 11L13.0464 5.75355C13.2417 5.55829 13.2417 5.24171 13.0464 5.04645L12.3536 4.35355C12.1583 4.15829 11.8417 4.15829 11.6464 4.35355L4 12L11.6464 19.6464C11.8417 19.8417 12.1583 19.8417 12.3536 19.6464L13.0464 18.9536C13.2417 18.7583 13.2417 18.4417 13.0464 18.2464L7.8 13H19.5C19.7761 13 20 12.7761 20 12.5V11.5C20 11.2239 19.7761 11 19.5 11H7.8Z",
        fill: "#21201F"
    }, null, -1),
    xd = [Cd];

function Sd(e, t) {
    return V(), K("svg", bd, xd)
}
const Md = Ie(vd, [
        ["render", Sd]
    ]),
    $d = {
        name: "WindgetFilter",
        components: {
            ArrowIcon: Md
        }
    },
    Id = {
        class: "widget__filters transition"
    },
    Od = {
        class: "widget__filters-wrapper"
    },
    zd = {
        class: "widget__filters-title"
    },
    Pd = {
        class: "widget__filters-arrow"
    },
    Td = A("p", {
        class: "widget__filters-title-text"
    }, "\u0424\u0438\u043B\u044C\u0442\u0440\u044B", -1),
    Dd = ps('<div class="widget__filters-content"><div class="widget__filters-item"><p class="widget__filters-item-title">\u041E\u043F\u043B\u0430\u0442\u0430</p><div class="widget__filters-radio"><p class="widget__filters-radio-title">\u0411\u0430\u043D\u043A\u043E\u0432\u0441\u043A\u043E\u0439 \u043A\u0430\u0440\u0442\u043E\u0439</p><input class="widget__filters-input" id="radio-1" type="radio" name="radio" value="1" checked><label for="radio-1" class="widget__filters-label"></label></div><div class="widget__filters-radio"><p class="widget__filters-radio-title">\u041D\u0430\u043B\u0438\u0447\u043D\u044B\u043C\u0438</p><input class="widget__filters-input" id="radio-2" type="radio" name="radio" value="2"><label for="radio-2" class="widget__filters-label"></label></div></div><div class="widget__filters-item"><p class="widget__filters-item-title">\u0422\u0438\u043F</p><div class="widget__filters-radio"><p class="widget__filters-radio-title">\u041F\u0443\u043D\u043A\u0442 \u0432\u044B\u0434\u0430\u0447\u0438</p><input class="widget__filters-input" id="type-1" type="radio" name="type" value="1" checked><label for="type-1" class="widget__filters-label"></label></div><div class="widget__filters-radio"><p class="widget__filters-radio-title">\u041F\u043E\u0441\u0442\u0430\u043C\u0430\u0442</p><input class="widget__filters-input" id="type-2" type="radio" name="type" value="2"><label for="type-2" class="widget__filters-label"></label></div><div class="widget__filters-radio"><p class="widget__filters-radio-title"> \u041F\u043E\u0447\u0442\u043E\u0432\u043E\u0435 \u043E\u0442\u0434\u0435\u043B\u0435\u043D\u0438\u0435 </p><input class="widget__filters-input" id="type-3" type="radio" name="type" value="3"><label for="type-3" class="widget__filters-label"></label></div></div></div><div class="widget__filters-buttons"><button class="widget__filters-button widget__filters-button--cancel"> \u041E\u0442\u043C\u0435\u043D\u0430 </button><button class="widget__filters-button widget__filters-button--show"> \u041F\u043E\u043A\u0430\u0437\u0430\u0442\u044C\u30FB12 </button></div>', 2);

function Ad(e, t, i, s, n, r) {
    const o = _e("ArrowIcon");
    return V(), K("div", Id, [A("div", Od, [A("div", zd, [A("button", Pd, [ce(o, {
        class: "widget__filters-arrow-img"
    })]), Td]), Dd])])
}
const Ed = Ie($d, [
    ["render", Ad]
]);
const ln = "https://widget-pvz.dostavka.yandex.net",
    kd = {
        name: "WidgetMap",
        props: {
            apiKey: {
                type: String,
                required: !0
            },
            lang: {
                center: String,
                default: () => "ru_RU",
                required: !1
            },
            lat: {
                type: Number,
                default: () => 55.751574,
                required: !1
            },
            lon: {
                type: Number,
                default: () => 37.573856,
                required: !1
            },
            zoom: {
                type: Number,
                default: () => 9,
                required: !1
            },
            height: {
                type: String,
                required: !0
            },
            selectedPointId: {
                type: String,
                default: () => ""
            },
            pointCustomDesc: {
                type: String,
                default: () => ""
            },
            gridSize: {
                type: Number,
                default: () => 64
            },
            minifyOnZoom: {
                type: Number,
                default: () => 12
            },
            isMobile: {
                type: Boolean,
                default: () => !1
            }
        },
        watch: {
            center: function(e, t) {
                setTimeout(() => {
                    this.setCenter()
                }, 400)
            }
        },
        data() {
            return {
                map: null,
                points: []
            }
        },
        computed: {
            yandexMapScriptUrl: function() {
                return `https://api-maps.yandex.ru/2.1/?apikey=${this.apiKey}&lang=${this.lang}&ns=ydwmaps&suggest_apikey=5ff2b7f2-2564-4338-8d03-749707d56d65`
            },
            center: function() {
                return [this.lat, this.lon]
            }
        },
        mounted: function() {
            this.initMap()
        },
        methods: {
            unselectPoint: function() {},
            setPointToCenter: function(e) {
                if (!e) {
                    return;
                };
                const t = this.points.find(i => i.id === e);
                window.YaDelivery.map.setCenter(t.coords)
            },
            setCenter: function() {
                window.YaDelivery.map.setCenter(this.center, this.zoom)
            },
            isPointInCluster: function(e) {
                let t = !1;
                return Array.isArray(window.YaDelivery.clusterer.getClusters()) ? (window.YaDelivery.clusterer.getClusters().forEach(i => {
                    i.getGeoObjects().forEach(n => {
                        e === n.options.get("pointId") && (t = !0)
                    })
                }), t) : !1
            },
            setPoints: function(e) {
                this.points = e, window.YaDelivery.map.geoObjects.removeAll();
                const t = window.YaDelivery.clusterer = new window.ydwmaps.Clusterer({
                        groupByCoordinates: !1,
                        clusterDisableClickZoom: !0,
                        clusterHideIconOnBalloonOpen: !1,
                        geoObjectHideIconOnBalloonOpen: !1,
                        clusterIconLayout: ydwmaps.templateLayoutFactory.createClass('<div class="widget__marker-count"><span>{% if properties.geoObjects.length < 100 %}{{ properties.geoObjects.length }}{% else %}99+{% endif %}</span></div>'),
                        clusterIconShape: {
                            type: "Rectangle",
                            coordinates: [
                                [0, 0],
                                [45, 45]
                            ]
                        }
                    }),
                    i = function(r) {
                        let o = "",
                            l = "man";
                        r.pointType === "pickup_point" ? (o = "\u041F\u0443\u043D\u043A\u0442 \u0432\u044B\u0434\u0430\u0447\u0438", l = "man") : r.pointType === "terminal" ? (o = "\u041F\u043E\u0441\u0442\u0430\u043C\u0430\u0442", l = "locker") : r.is_post_office && (o = "\u041F\u043E\u0447\u0442\u0430 \u0420\u043E\u0441\u0441\u0438\u0438", l = "post-office");
                        const u = window.YaDelivery.map.getZoom() >= r.minifyOnZoom ? `<div class="widget__marker-extradition ${r.isActive?"active":""}" data-point-id="${r.pointId}"><div class="widget__marker-extradition-svg widget__marker-extradition-svg--${l}"></div><div class="widget__marker-extradition-content" style="text-align: left;"><p class="widget__marker-extradition-title">${o}</p><p class="widget__marker-extradition-desc"><span class="ydw-point-desc not-actual" data-pickpoint-id="${r.pointId}" data-payment_method="${r.payment_methods&&r.payment_methods.length>0?r.payment_methods:""}">${r.desc}</span></p></div></div>` : '<div class="widget__marker-count"><span>1</span></div>';
                        return {
                            arguments: r,
                            iconContent: u
                        }
                    },
                    s = function(r) {
                        return {
                            pointId: r.pointId,
                            iconLayout: "default#imageWithContent",
                            iconImageHref: "",
                            iconShape: {
                                type: "Rectangle",
                                coordinates: [
                                    [-60, -25],
                                    [75, 30]
                                ]
                            }
                        }
                    };
                let n = [];
                for (let r = 0, o = e.length; r < o; r++) {
                    let l = new window.ydwmaps.Placemark(e[r].coords, i({
                        pointId: e[r].id,
                        pointType: e[r].type,
                        is_post_office: e[r].is_post_office,
                        desc: this.pointCustomDesc,
                        isActive: this.selectedPointId === e[r].id,
                        minifyOnZoom: this.minifyOnZoom
                    }), s({
                        pointId: e[r].id
                    }));
                    l.events.add("click", c => {
                        let u = c.get("target"),
                            f = u.properties.get("arguments"),
                            p = u.properties.get("iconContent").includes("active");
                        document.querySelectorAll(".widget__marker-extradition.active").forEach(function(b) {
                            b.getAttribute("data-point-id") !== f.pointId && b.classList.remove("active")
                        }), f.isActive = !p, u.properties.set("iconContent", i(f).iconContent), this.$emit("pointSelected", f.isActive ? f.pointId : "")
                    }), n[r] = l
                }
                t.options.set({
                    gridSize: this.gridSize,
                    clusterDisableClickZoom: !1
                }), t.add(n), window.YaDelivery.map.geoObjects.add(t)
            },
            unselectAll: function() {},
            initMap: function() {
                if (document.querySelectorAll('[src="' + this.yandexMapScriptUrl + '"]').length > 0) {
                    this.loadModule(), this.$emit("yaMapSdkLoaded");
                    return
                }
                let e = document.createElement("script");
                e.src = this.yandexMapScriptUrl, e.onload = () => {
                    window.ydwmaps.ready(() => {
                        this.loadModule(), this.$emit("yaMapSdkLoaded")
                    })
                }, document.head.appendChild(e), this.runYandexCounter()
            },
            runYandexCounter: function() {
                (function(e, t, i, s, n, r, o) {
                    e[n] = e[n] || function() {
                        (e[n].a = e[n].a || []).push(arguments)
                    }, e[n].l = 1 * new Date;
                    for (var l = 0; l < document.scripts.length; l++)
                        if (document.scripts[l].src === s) return;
                    r = t.createElement(i), o = t.getElementsByTagName(i)[0], r.async = 1, r.src = s, o.parentNode.insertBefore(r, o)
                })(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym"), ym(90834265, "init", {
                    clickmap: !0,
                    trackLinks: !0,
                    accurateTrackBounce: !0,
                    webvisor: !0
                })
            },
            setUpMap: function() {
                const e = parseInt(this.height);
                let t = parseInt(e * .52);
                this.isMobile && (t = e - 60);
                const i = t - 85;
                let n = [new window.ydwmaps.control.GeolocationControl({
                        data: {},
                        options: {
                            suppressMapOpenBlock: !0,
                            layout: "round#buttonLayout",
                            maxWidth: 40,
                            float: "none",
                            position: {
                                top: t + "px",
                                right: "12px"
                            }
                        }
                    })],
                    r = new window.ydwmaps.control.ZoomControl({
                        options: {
                            layout: "round#zoomLayout",
                            size: "small",
                            float: "none",
                            position: {
                                top: i + "px",
                                right: "16px"
                            }
                        }
                    });
                r.state.set("zoomRange", [12, 21]), n.push(r), window.YaDelivery.map = this.map = new window.ydwmaps.Map("ydw-map", {
                    center: this.center,
                    zoom: this.zoom,
                    behaviors: ["default", "scrollZoom"],
                    controls: n
                }, {
                    minZoom: 5,
                    searchControlProvider: "yandex#search",
                    suppressMapOpenBlock: !0,
                    yandexMapDisablePoiInteractivity: !0
                }), this.$emit("boundsChanged", window.YaDelivery.map.getBounds()), window.YaDelivery.map.events.add("boundschange", o => {
                    let l = o.get("newBounds");
                    this.$emit("boundsChanged", l)
                })
            },
            loadModule: function() {
                let e = document.createElement("script");
                this.isMobile ? e.src = ln + "/module-mobile.js?v=" + Math.random() : e.src = ln + "/module.js?v=2", e.onload = () => {
                    this.setUpMap()
                }, document.head.appendChild(e)
            }
        }
    };

function Ld(e, t, i, s, n, r) {
    return V(), K("div", {
        class: "widget__map",
        id: "ydw-map",
        style: Ke("height: " + i.height)
    }, null, 4)
}
const Hd = Ie(kd, [
    ["render", Ld]
]);
var gs = typeof globalThis < "u" ? globalThis : typeof window < "u" ? window : typeof global < "u" ? global : typeof self < "u" ? self : {},
    ys = {
        exports: {}
    };
(function(e, t) {
    (function(i, s) {
        e.exports = s()
    })(gs, function() {
        var i = 1e3,
            s = 6e4,
            n = 36e5,
            r = "millisecond",
            o = "second",
            l = "minute",
            c = "hour",
            u = "day",
            f = "week",
            p = "month",
            b = "quarter",
            T = "year",
            M = "date",
            R = "Invalid Date",
            y = /^(\d{4})[-/]?(\d{1,2})?[-/]?(\d{0,2})[Tt\s]*(\d{1,2})?:?(\d{1,2})?:?(\d{1,2})?[.:]?(\d+)?$/,
            L = /\[([^\]]+)]|Y{1,4}|M{1,4}|D{1,2}|d{1,4}|H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|Z{1,2}|SSS/g,
            Q = {
                name: "en",
                weekdays: "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),
                months: "January_February_March_April_May_June_July_August_September_October_November_December".split("_"),
                ordinal: function(k) {
                    var I = ["th", "st", "nd", "rd"],
                        x = k % 100;
                    return "[" + k + (I[(x - 20) % 10] || I[x] || I[0]) + "]"
                }
            },
            E = function(k, I, x) {
                var O = String(k);
                return !O || O.length >= I ? k : "" + Array(I + 1 - O.length).join(x) + k
            },
            J = {
                s: E,
                z: function(k) {
                    var I = -k.utcOffset(),
                        x = Math.abs(I),
                        O = Math.floor(x / 60),
                        w = x % 60;
                    return (I <= 0 ? "+" : "-") + E(O, 2, "0") + ":" + E(w, 2, "0")
                },
                m: function k(I, x) {
                    if (I.date() < x.date()) return -k(x, I);
                    var O = 12 * (x.year() - I.year()) + (x.month() - I.month()),
                        w = I.clone().add(O, p),
                        P = x - w < 0,
                        B = I.clone().add(O + (P ? -1 : 1), p);
                    return +(-(O + (x - w) / (P ? w - B : B - w)) || 0)
                },
                a: function(k) {
                    return k < 0 ? Math.ceil(k) || 0 : Math.floor(k)
                },
                p: function(k) {
                    return {
                        M: p,
                        y: T,
                        w: f,
                        d: u,
                        D: M,
                        h: c,
                        m: l,
                        s: o,
                        ms: r,
                        Q: b
                    } [k] || String(k || "").toLowerCase().replace(/s$/, "")
                },
                u: function(k) {
                    return k === void 0
                }
            },
            ye = "en",
            ae = {};
        ae[ye] = Q;
        var he = function(k) {
                return k instanceof ee
            },
            ue = function k(I, x, O) {
                var w;
                if (!I) return ye;
                if (typeof I == "string") {
                    var P = I.toLowerCase();
                    ae[P] && (w = P), x && (ae[P] = x, w = P);
                    var B = I.split("-");
                    if (!w && B.length > 1) return k(B[0])
                } else {
                    var Z = I.name;
                    ae[Z] = I, w = Z
                }
                return !O && w && (ye = w), w || !O && ye
            },
            q = function(k, I) {
                if (he(k)) return k.clone();
                var x = typeof I == "object" ? I : {};
                return x.date = k, x.args = arguments, new ee(x)
            },
            $ = J;
        $.l = ue, $.i = he, $.w = function(k, I) {
            return q(k, {
                locale: I.$L,
                utc: I.$u,
                x: I.$x,
                $offset: I.$offset
            })
        };
        var ee = function() {
                function k(x) {
                    this.$L = ue(x.locale, null, !0), this.parse(x)
                }
                var I = k.prototype;
                return I.parse = function(x) {
                    this.$d = function(O) {
                        var w = O.date,
                            P = O.utc;
                        if (w === null) return new Date(NaN);
                        if ($.u(w)) return new Date;
                        if (w instanceof Date) return new Date(w);
                        if (typeof w == "string" && !/Z$/i.test(w)) {
                            var B = w.match(y);
                            if (B) {
                                var Z = B[2] - 1 || 0,
                                    ne = (B[7] || "0").substring(0, 3);
                                return P ? new Date(Date.UTC(B[1], Z, B[3] || 1, B[4] || 0, B[5] || 0, B[6] || 0, ne)) : new Date(B[1], Z, B[3] || 1, B[4] || 0, B[5] || 0, B[6] || 0, ne)
                            }
                        }
                        return new Date(w)
                    }(x), this.$x = x.x || {}, this.init()
                }, I.init = function() {
                    var x = this.$d;
                    this.$y = x.getFullYear(), this.$M = x.getMonth(), this.$D = x.getDate(), this.$W = x.getDay(), this.$H = x.getHours(), this.$m = x.getMinutes(), this.$s = x.getSeconds(), this.$ms = x.getMilliseconds()
                }, I.$utils = function() {
                    return $
                }, I.isValid = function() {
                    return this.$d.toString() !== R
                }, I.isSame = function(x, O) {
                    var w = q(x);
                    return this.startOf(O) <= w && w <= this.endOf(O)
                }, I.isAfter = function(x, O) {
                    return q(x) < this.startOf(O)
                }, I.isBefore = function(x, O) {
                    return this.endOf(O) < q(x)
                }, I.$g = function(x, O, w) {
                    return $.u(x) ? this[O] : this.set(w, x)
                }, I.unix = function() {
                    return Math.floor(this.valueOf() / 1e3)
                }, I.valueOf = function() {
                    return this.$d.getTime()
                }, I.startOf = function(x, O) {
                    var w = this,
                        P = !!$.u(O) || O,
                        B = $.p(x),
                        Z = function(ke, pe) {
                            var be = $.w(w.$u ? Date.UTC(w.$y, pe, ke) : new Date(w.$y, pe, ke), w);
                            return P ? be : be.endOf(u)
                        },
                        ne = function(ke, pe) {
                            return $.w(w.toDate()[ke].apply(w.toDate("s"), (P ? [0, 0, 0, 0] : [23, 59, 59, 999]).slice(pe)), w)
                        },
                        X = this.$W,
                        oe = this.$M,
                        Ae = this.$D,
                        Ee = "set" + (this.$u ? "UTC" : "");
                    switch (B) {
                        case T:
                            return P ? Z(1, 0) : Z(31, 11);
                        case p:
                            return P ? Z(1, oe) : Z(0, oe + 1);
                        case f:
                            var ot = this.$locale().weekStart || 0,
                                ve = (X < ot ? X + 7 : X) - ot;
                            return Z(P ? Ae - ve : Ae + (6 - ve), oe);
                        case u:
                        case M:
                            return ne(Ee + "Hours", 0);
                        case c:
                            return ne(Ee + "Minutes", 1);
                        case l:
                            return ne(Ee + "Seconds", 2);
                        case o:
                            return ne(Ee + "Milliseconds", 3);
                        default:
                            return this.clone()
                    }
                }, I.endOf = function(x) {
                    return this.startOf(x, !1)
                }, I.$set = function(x, O) {
                    var w, P = $.p(x),
                        B = "set" + (this.$u ? "UTC" : ""),
                        Z = (w = {}, w[u] = B + "Date", w[M] = B + "Date", w[p] = B + "Month", w[T] = B + "FullYear", w[c] = B + "Hours", w[l] = B + "Minutes", w[o] = B + "Seconds", w[r] = B + "Milliseconds", w)[P],
                        ne = P === u ? this.$D + (O - this.$W) : O;
                    if (P === p || P === T) {
                        var X = this.clone().set(M, 1);
                        X.$d[Z](ne), X.init(), this.$d = X.set(M, Math.min(this.$D, X.daysInMonth())).$d
                    } else Z && this.$d[Z](ne);
                    return this.init(), this
                }, I.set = function(x, O) {
                    return this.clone().$set(x, O)
                }, I.get = function(x) {
                    return this[$.p(x)]()
                }, I.add = function(x, O) {
                    var w, P = this;
                    x = Number(x);
                    var B = $.p(O),
                        Z = function(oe) {
                            var Ae = q(P);
                            return $.w(Ae.date(Ae.date() + Math.round(oe * x)), P)
                        };
                    if (B === p) return this.set(p, this.$M + x);
                    if (B === T) return this.set(T, this.$y + x);
                    if (B === u) return Z(1);
                    if (B === f) return Z(7);
                    var ne = (w = {}, w[l] = s, w[c] = n, w[o] = i, w)[B] || 1,
                        X = this.$d.getTime() + x * ne;
                    return $.w(X, this)
                }, I.subtract = function(x, O) {
                    return this.add(-1 * x, O)
                }, I.format = function(x) {
                    var O = this,
                        w = this.$locale();
                    if (!this.isValid()) return w.invalidDate || R;
                    var P = x || "YYYY-MM-DDTHH:mm:ssZ",
                        B = $.z(this),
                        Z = this.$H,
                        ne = this.$m,
                        X = this.$M,
                        oe = w.weekdays,
                        Ae = w.months,
                        Ee = function(pe, be, lt, Qe) {
                            return pe && (pe[be] || pe(O, P)) || lt[be].slice(0, Qe)
                        },
                        ot = function(pe) {
                            return $.s(Z % 12 || 12, pe, "0")
                        },
                        ve = w.meridiem || function(pe, be, lt) {
                            var Qe = pe < 12 ? "AM" : "PM";
                            return lt ? Qe.toLowerCase() : Qe
                        },
                        ke = {
                            YY: String(this.$y).slice(-2),
                            YYYY: this.$y,
                            M: X + 1,
                            MM: $.s(X + 1, 2, "0"),
                            MMM: Ee(w.monthsShort, X, Ae, 3),
                            MMMM: Ee(Ae, X),
                            D: this.$D,
                            DD: $.s(this.$D, 2, "0"),
                            d: String(this.$W),
                            dd: Ee(w.weekdaysMin, this.$W, oe, 2),
                            ddd: Ee(w.weekdaysShort, this.$W, oe, 3),
                            dddd: oe[this.$W],
                            H: String(Z),
                            HH: $.s(Z, 2, "0"),
                            h: ot(1),
                            hh: ot(2),
                            a: ve(Z, ne, !0),
                            A: ve(Z, ne, !1),
                            m: String(ne),
                            mm: $.s(ne, 2, "0"),
                            s: String(this.$s),
                            ss: $.s(this.$s, 2, "0"),
                            SSS: $.s(this.$ms, 3, "0"),
                            Z: B
                        };
                    return P.replace(L, function(pe, be) {
                        return be || ke[pe] || B.replace(":", "")
                    })
                }, I.utcOffset = function() {
                    return 15 * -Math.round(this.$d.getTimezoneOffset() / 15)
                }, I.diff = function(x, O, w) {
                    var P, B = $.p(O),
                        Z = q(x),
                        ne = (Z.utcOffset() - this.utcOffset()) * s,
                        X = this - Z,
                        oe = $.m(this, Z);
                    return oe = (P = {}, P[T] = oe / 12, P[p] = oe, P[b] = oe / 3, P[f] = (X - ne) / 6048e5, P[u] = (X - ne) / 864e5, P[c] = X / n, P[l] = X / s, P[o] = X / i, P)[B] || X, w ? oe : $.a(oe)
                }, I.daysInMonth = function() {
                    return this.endOf(p).$D
                }, I.$locale = function() {
                    return ae[this.$L]
                }, I.locale = function(x, O) {
                    if (!x) return this.$L;
                    var w = this.clone(),
                        P = ue(x, O, !0);
                    return P && (w.$L = P), w
                }, I.clone = function() {
                    return $.w(this.$d, this)
                }, I.toDate = function() {
                    return new Date(this.valueOf())
                }, I.toJSON = function() {
                    return this.isValid() ? this.toISOString() : null
                }, I.toISOString = function() {
                    return this.$d.toISOString()
                }, I.toString = function() {
                    return this.$d.toUTCString()
                }, k
            }(),
            we = ee.prototype;
        return q.prototype = we, [
            ["$ms", r],
            ["$s", o],
            ["$m", l],
            ["$H", c],
            ["$W", u],
            ["$M", p],
            ["$y", T],
            ["$D", M]
        ].forEach(function(k) {
            we[k[1]] = function(I) {
                return this.$g(I, k[0], k[1])
            }
        }), q.extend = function(k, I) {
            return k.$i || (k(I, ee, q), k.$i = !0), q
        }, q.locale = ue, q.isDayjs = he, q.unix = function(k) {
            return q(1e3 * k)
        }, q.en = ae[ye], q.Ls = ae, q.p = {}, q
    })
})(ys);
const ws = ys.exports,
    cn = 750,
    Oi = "https://widget-pvz.dostavka.yandex.net",
    Fd = {
        components: {
            WidgetController: wd,
            WidgetFilter: Ed,
            WidgetMap: Hd
        },
        data() {
            var e, t, i, s, n, r, o, l, c, u, f, p, b;
            return {
                screenWidth: Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0),
                screenHeight: Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0),
                foundPoints: [],
                isYaMapSdkLoaded: !1,
                mapArgs: {
                    apiKey: "daeadf5f-c4ec-4f05-a5ce-3f577f5c7c18",
                    lat: 0,
                    lon: 0,
                    zoom: 9
                },
                lastAbortController: null,
                params: {
                    city: (e = window.YaDelivery.params.city) != null ? e : "\u041C\u043E\u0441\u043A\u0432\u0430",
                    size: {
                        height: window.screen.width <= cn ? window.innerHeight - 120 + "px" : window.YaDelivery.params.size.height,
                        width: window.YaDelivery.params.size.width
                    },
                    source_platform_station: window.YaDelivery.params.source_platform_station,
                    physical_dims_weight_gross: window.YaDelivery.params.physical_dims_weight_gross,
                    physical_dims_dx: (t = window.YaDelivery.params.physical_dims_dx) != null ? t : 10,
                    physical_dims_dy: (i = window.YaDelivery.params.physical_dims_dy) != null ? i : 10,
                    physical_dims_dz: (s = window.YaDelivery.params.physical_dims_dz) != null ? s : 10,
                    delivery_price: typeof window.YaDelivery.params.delivery_price == "function" ? window.YaDelivery.params.delivery_price : T => typeof window.YaDelivery.params.delivery_price > "u" ? T : window.YaDelivery.params.delivery_price,
                    delivery_term: window.YaDelivery.params.delivery_term,
                    show_select_button: window.YaDelivery.params.show_select_button,
                    filter: {
                        type: (n = window.YaDelivery.params.filter.type) != null ? n : [],
                        is_yandex_branded: window.YaDelivery.params.filter.is_yandex_branded,
                        show_post_office: window.YaDelivery.params.filter.show_post_office,
                        payment_methods: (r = window.YaDelivery.params.filter.payment_methods) != null ? r : [],
                        payment_methods_filter: (o = window.YaDelivery.params.filter.payment_methods_filter) != null ? o : "or"
                    },
                    waiting_period: {
                        pickup_point: (c = (l = window.YaDelivery.params.waiting_period) == null ? void 0 : l.pickup_point) != null ? c : "7 \u0434\u043D\u0435\u0439",
                        terminal: (f = (u = window.YaDelivery.params.waiting_period) == null ? void 0 : u.terminal) != null ? f : "3 \u0434\u043D\u044F"
                    },
                    grid_size: (p = window.YaDelivery.params.grid_size) != null ? p : 64,
                    minify_on_zoom: (b = window.YaDelivery.params.minify_on_zoom) != null ? b : 14
                },
                selectedPointId: "",
                fewFoundPoints: [],
                pickupPointsExtras: {},
                weightRules: {
                    warehouse: 19e3,
                    pickup_point: 3e4,
                    terminal: 2e4
                },
                isKeyboardOverlaps: navigator.userAgent.indexOf("Mi") != -1
            }
        },
        mounted() {
            this.params.filter.show_post_office === !0 && this.params.filter.type.push("warehouse"), this.initPickupPointsExtras(), this.isNeedToFetchPointDescription && setTimeout(() => {
                let e = null,
                    t = document.getElementById("ydw-pointList");
                !t || t.addEventListener("scroll", () => {
                    e !== null && clearTimeout(e), e = setTimeout(() => {
                        this.fetchAndSetPointDescription()
                    }, 200)
                }, !1)
            }, 6e3), window.addEventListener("resize", () => {
                this.screenWidth = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0), this.screenHeight = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0)
            }), window.YaDelivery.setParams = e => {
                e.filter && (e.filter.type && (this.params.filter.type = e.filter.type), e.filter.is_yandex_branded && (this.params.filter.is_yandex_branded = e.filter.is_yandex_branded), e.filter.payment_methods && (this.params.filter.payment_methods = e.filter.payment_methods)), typeof e.show_select_button < "u" && (this.params.show_select_button = e.show_select_button), e.delivery_term && (this.params.delivery_term = e.delivery_term, setTimeout(() => {
                    this.$refs.mapComponent.setPoints(this.$refs.mapComponent.points)
                }, 500)), e.city && (this.$refs.controllerComponent.searchedAddress = e.city, this.$refs.controllerComponent.searchByAddress()), e.source_platform_station && (this.params.source_platform_station = e.source_platform_station), e.physical_dims_weight_gross && (this.params.physical_dims_weight_gross = e.physical_dims_weight_gross), e.physical_dims_dx && (this.params.physical_dims_dx = e.physical_dims_dx), e.physical_dims_dy && (this.params.physical_dims_dy = e.physical_dims_dy), e.physical_dims_dz && (this.params.physical_dims_dz = e.physical_dims_dz)
            }
        },
        computed: {
            isNeedToFetchPointDescription: function() {
                return this.params.source_platform_station && this.params.physical_dims_weight_gross
            },
            isMobile: function() {
                return this.screenWidth <= cn
            },
            heightNumber: function() {
                return Number.parseInt(this.params.size.height)
            },
            pointCustomDesc: function() {
                const e = [];
                return this.params.delivery_term && e.push(this.params.delivery_term), this.params.delivery_price && e.push(this.params.delivery_price(0)), e.join("\u30FB")
            }
        },
        methods: {
            initPickupPointsExtras: function() {
                fetch(Oi + "/pickup-points/list").then(e => e.json()).then(e => {
                    e.forEach(t => {
                        this.pickupPointsExtras[t.stationId] = {
                            maxWeightGross: t.maxWeightGross
                        }
                    })
                })
            },
            isInViewport: function(e) {
                const t = e.getBoundingClientRect();
                return t.top >= 0 && t.left >= 0 && t.bottom <= (window.innerHeight || document.documentElement.clientHeight) && t.right <= (window.innerWidth || document.documentElement.clientWidth)
            },
            buildOffersCreateBody: function(e, t) {
                return {
                    billing_info: {
                        payment_method: t
                    },
                    destination: {
                        type: "platform_station",
                        platform_station: {
                            platform_id: e
                        }
                    },
                    info: {
                        operator_request_id: "1"
                    },
                    items: [{
                        article: "1",
                        billing_details: {
                            assessed_unit_price: 1,
                            unit_price: 1
                        },
                        count: 1,
                        name: "1",
                        place_barcode: "1"
                    }],
                    last_mile_policy: "self_pickup",
                    places: [{
                        barcode: "1",
                        physical_dims: {
                            dx: this.params.physical_dims_dx,
                            dy: this.params.physical_dims_dy,
                            dz: this.params.physical_dims_dz,
                            weight_gross: this.params.physical_dims_weight_gross
                        }
                    }],
                    recipient_info: {
                        email: "dummy@inbox.ru",
                        first_name: "Dummy",
                        phone: "+79999999999"
                    },
                    source: {
                        platform_station: {
                            platform_id: this.params.source_platform_station
                        }
                    }
                }
            },
            getVisiblePoints: function() {
                const e = [];
                return document.querySelectorAll(".ydw-point-desc").forEach(t => {
                    this.isInViewport(t) && e.push({
                        id: t.attributes["data-pickpoint-id"].value,
                        payment_method: t.attributes["data-payment_method"].value
                    })
                }), e
            },
            fetchPointClosestOffer: function(e) {
                return new Promise(t => {
                    if (window.YaDelivery.pointOfferMap[e]) t(window.YaDelivery.pointOfferMap[e]);
                    else {
                        let i = new Headers;
                        i.append("Content-Type", "application/json");
                        let s = JSON.stringify(this.buildOffersCreateBody(e, "already_paid")),
                            n = {
                                method: "POST",
                                headers: i,
                                body: s
                            };
                        fetch(Oi + "/api/b2b/platform/offers/create", n).then(r => r.json()).then(r => {
                            r.offers && r.offers[0] ? window.YaDelivery.pointOfferMap[e] = {
                                pricing_total: r.offers[0].offer_details.pricing_total,
                                delivery_interval_min: r.offers[0].offer_details.delivery_interval.min
                            } : window.YaDelivery.pointOfferMap[e] = {
                                error: "\u041D\u0435 \u0443\u0434\u0430\u043B\u043E\u0441\u044C \u0440\u0430\u0441\u0441\u0447\u0438\u0442\u0430\u0442\u044C \u0441\u0440\u043E\u043A\u0438 \u0438 \u0441\u0442\u043E\u0438\u043C\u043E\u0441\u0442\u044C"
                            }, t(window.YaDelivery.pointOfferMap[e])
                        })
                    }
                })
            },
            fetchAndSetPointDescription: function() {
                this.getVisiblePoints().forEach(async t => {
                    let i = await this.fetchPointClosestOffer(t.id, t.payment_method);
                    this.setPointDescription(t.id, i.pricing_total, i.delivery_interval_min, i.error)
                })
            },
            setPointDescription: function(e, t, i, s) {
                if (!s) {
                    let n = this.params.delivery_term;
                    if (typeof this.params.delivery_term > "u" && (this.params.delivery_term = 0), Number.isInteger(this.params.delivery_term)) {
                        let o = this.params.delivery_term,
                            l = ws(i).add(o, "day");
                        n = l.format("D MMMM"), l.isTomorrow() && (n = "\u0417\u0430\u0432\u0442\u0440\u0430")
                    }
                    let r = parseFloat(t);
                    r = this.params.delivery_price(r), s = n + "\u30FB" + r
                }
                document.querySelectorAll('.ydw-point-desc.not-actual[data-pickpoint-id="' + e + '"]').forEach(n => {
                    n.innerText = s
                })
            },
            dispatchSelectedPoint: function() {
                var i;
                const e = this.foundPoints.find(s => s.id === this.selectedPointId),
                    t = {
                        id: this.selectedPointId,
                        address: (i = JSON.parse(JSON.stringify(e))) == null ? void 0 : i.address
                    };
                document.dispatchEvent(new CustomEvent("YaNddWidgetPointSelected", {
                    detail: t
                })), ym(90834265, "reachGoal", "yaPvzSelect")
            },
            selectButtonClicked: function() {
                this.dispatchSelectedPoint()
            },
            pointUnselected: function() {
                this.$refs.mapComponent.unselectPoint()
            },
            pointSelected: function(e, t, i) {
                this.selectedPointId = e, this.$refs.mapComponent.setPointToCenter(e), this.$refs.controllerComponent.selected = e, this.$refs.controllerComponent.selectedList = [e], this.$refs.mapComponent.isPointInCluster(e) ? window.YaDelivery.map.setZoom(16) : window.YaDelivery.map.getZoom() < 14 && window.YaDelivery.map.setZoom(14), this.params.show_select_button || this.dispatchSelectedPoint()
            },
            getPoints: function(e) {
                return new Promise((t, i) => {
                    this.lastAbortController && this.lastAbortController.abort(), this.lastAbortController = new AbortController;
                    let s = new Headers;
                    s.append("Content-Type", "application/json");
                    let n = JSON.stringify(e),
                        r = {
                            method: "POST",
                            signal: this.lastAbortController.signal,
                            headers: s,
                            body: n
                        };
                    fetch(Oi, r).then(o => o.json()).then(o => {
                        t(o.points || [])
                    }).catch(o => {})
                })
            },
            buildFilter: function() {
                const e = {};
                return typeof this.params.filter.is_yandex_branded == "boolean" && (e.is_yandex_branded = this.params.filter.is_yandex_branded), this.params.filter.show_post_office !== !0 && (e.is_post_office = !1), this.params.filter.type.length === 1 && (e.type = this.params.filter.type[0]), this.params.filter.payment_methods.length === 1 && (e.payment_method = this.params.filter.payment_methods[0]), e
            },
            boundsChanged: async function(e) {
                const t = this.buildFilter();
                t.latitude = {
                    from: e[0][0],
                    to: e[1][0]
                }, t.longitude = {
                    from: e[0][1],
                    to: e[1][1]
                };
                let i = await this.getPoints(t);
                this.foundPoints = i;
                let s = [],
                    n = [];
                if (i.length > 0) {
                    const r = this.params.filter.type.length > 1,
                        o = this.params.filter.payment_methods.length > 1;
                    i.forEach((l, c) => {
                        let u = 0;
                        if (this.pickupPointsExtras[l.id] ? u = this.pickupPointsExtras[l.id].maxWeightGross : this.weightRules[l.type] && (u = this.weightRules[l.type]), !(this.params.physical_dims_weight_gross > u) && !(r && !this.params.filter.type.includes(l.type)) && !(l.type === "warehouse" && l.is_post_office !== !0)) {
                            if (o) {
                                if (this.params.filter.payment_methods_filter === "or") {
                                    let f = !1;
                                    for (let p = 0; p < l.payment_methods.length; p++)
                                        if (this.params.filter.payment_methods.includes(l.payment_methods[p])) {
                                            f = !0;
                                            continue
                                        } if (!f) return
                                } else if (this.params.filter.payment_methods_filter === "and") {
                                    for (let f = 0; f < this.params.filter.payment_methods.length; f++)
                                        if (!l.payment_methods.includes(this.params.filter.payment_methods[f])) return
                                }
                            }
                            l.expanded = !1, l.id === this.selectedPointId && (l.expanded = !0), s.push({
                                id: l.id,
                                type: l.type,
                                is_post_office: l.is_post_office,
                                coords: [l.position.latitude, l.position.longitude]
                            }), this.isMobile ? l.expanded && n.push(l) : n.push(l)
                        }
                    }), this.isNeedToFetchPointDescription && setTimeout(this.fetchAndSetPointDescription, 500)
                }
                this.$refs.mapComponent.setPoints(s), this.fewFoundPoints = n
            },
            yaMapSdkLoaded: function() {
                this.isYaMapSdkLoaded = !0
            },
            areaSelected: function(e, t, i) {
                this.mapArgs.lat = e, this.mapArgs.lon = t, this.mapArgs.zoom = i
            }
        }
    };

function Rd(e, t, i, s, n, r) {
    const o = _e("WidgetController"),
        l = _e("WidgetFilter"),
        c = _e("WidgetMap");
    return V(), K("section", {
        id: "ydw-widget-section",
        class: "ydw-widget",
        style: Ke("box-sizing: border-box;overflow: scroll;width:" + r.isMobile ? n.screenWidth : n.params.size.width + ";")
    }, [A("div", {
        class: "widget__wrapper",
        style: Ke("height: " + n.params.size.height)
    }, [ce(o, {
        ref: "controllerComponent",
        onAreaSelected: r.areaSelected,
        onPointSelected: r.pointSelected,
        onSelectButtonClicked: r.selectButtonClicked,
        onPointUnselected: r.pointUnselected,
        selectedPointId: n.selectedPointId,
        isYaMapSdkLoaded: n.isYaMapSdkLoaded,
        foundPoints: n.fewFoundPoints,
        defaultCity: n.params.city,
        pointCustomDesc: r.pointCustomDesc,
        deliveryPrice: n.params.delivery_price,
        showSelectButton: n.params.show_select_button,
        waitingPeriod: n.params.waiting_period,
        height: r.heightNumber,
        isMobile: r.isMobile,
        isKeyboardOverlaps: n.isKeyboardOverlaps
    }, null, 8, ["onAreaSelected", "onPointSelected", "onSelectButtonClicked", "onPointUnselected", "selectedPointId", "isYaMapSdkLoaded", "foundPoints", "defaultCity", "pointCustomDesc", "deliveryPrice", "showSelectButton", "waitingPeriod", "height", "isMobile", "isKeyboardOverlaps"]), ce(l), ce(c, {
        ref: "mapComponent",
        onYaMapSdkLoaded: r.yaMapSdkLoaded,
        onBoundsChanged: r.boundsChanged,
        onPointSelected: r.pointSelected,
        apiKey: n.mapArgs.apiKey,
        lat: n.mapArgs.lat,
        lon: n.mapArgs.lon,
        zoom: n.mapArgs.zoom,
        height: n.params.size.height,
        selectedPointId: n.selectedPointId,
        pointCustomDesc: r.pointCustomDesc,
        deliveryPrice: n.params.delivery_price,
        gridSize: n.params.grid_size,
        minifyOnZoom: n.params.minify_on_zoom,
        isMobile: r.isMobile
    }, null, 8, ["onYaMapSdkLoaded", "onBoundsChanged", "onPointSelected", "apiKey", "lat", "lon", "zoom", "height", "selectedPointId", "pointCustomDesc", "deliveryPrice", "gridSize", "minifyOnZoom", "isMobile"])], 4)], 4)
}
const Bd = Ie(Fd, [
    ["render", Rd]
]);
var Nd = {
    exports: {}
};
(function(e, t) {
    (function(i, s) {
        e.exports = s(ys.exports)
    })(gs, function(i) {
        function s(M) {
            return M && typeof M == "object" && "default" in M ? M : {
                default: M
            }
        }
        var n = s(i),
            r = "\u044F\u043D\u0432\u0430\u0440\u044F_\u0444\u0435\u0432\u0440\u0430\u043B\u044F_\u043C\u0430\u0440\u0442\u0430_\u0430\u043F\u0440\u0435\u043B\u044F_\u043C\u0430\u044F_\u0438\u044E\u043D\u044F_\u0438\u044E\u043B\u044F_\u0430\u0432\u0433\u0443\u0441\u0442\u0430_\u0441\u0435\u043D\u0442\u044F\u0431\u0440\u044F_\u043E\u043A\u0442\u044F\u0431\u0440\u044F_\u043D\u043E\u044F\u0431\u0440\u044F_\u0434\u0435\u043A\u0430\u0431\u0440\u044F".split("_"),
            o = "\u044F\u043D\u0432\u0430\u0440\u044C_\u0444\u0435\u0432\u0440\u0430\u043B\u044C_\u043C\u0430\u0440\u0442_\u0430\u043F\u0440\u0435\u043B\u044C_\u043C\u0430\u0439_\u0438\u044E\u043D\u044C_\u0438\u044E\u043B\u044C_\u0430\u0432\u0433\u0443\u0441\u0442_\u0441\u0435\u043D\u0442\u044F\u0431\u0440\u044C_\u043E\u043A\u0442\u044F\u0431\u0440\u044C_\u043D\u043E\u044F\u0431\u0440\u044C_\u0434\u0435\u043A\u0430\u0431\u0440\u044C".split("_"),
            l = "\u044F\u043D\u0432._\u0444\u0435\u0432\u0440._\u043C\u0430\u0440._\u0430\u043F\u0440._\u043C\u0430\u044F_\u0438\u044E\u043D\u044F_\u0438\u044E\u043B\u044F_\u0430\u0432\u0433._\u0441\u0435\u043D\u0442._\u043E\u043A\u0442._\u043D\u043E\u044F\u0431._\u0434\u0435\u043A.".split("_"),
            c = "\u044F\u043D\u0432._\u0444\u0435\u0432\u0440._\u043C\u0430\u0440\u0442_\u0430\u043F\u0440._\u043C\u0430\u0439_\u0438\u044E\u043D\u044C_\u0438\u044E\u043B\u044C_\u0430\u0432\u0433._\u0441\u0435\u043D\u0442._\u043E\u043A\u0442._\u043D\u043E\u044F\u0431._\u0434\u0435\u043A.".split("_"),
            u = /D[oD]?(\[[^[\]]*\]|\s)+MMMM?/;

        function f(M, R, y) {
            var L, Q;
            return y === "m" ? R ? "\u043C\u0438\u043D\u0443\u0442\u0430" : "\u043C\u0438\u043D\u0443\u0442\u0443" : M + " " + (L = +M, Q = {
                mm: R ? "\u043C\u0438\u043D\u0443\u0442\u0430_\u043C\u0438\u043D\u0443\u0442\u044B_\u043C\u0438\u043D\u0443\u0442" : "\u043C\u0438\u043D\u0443\u0442\u0443_\u043C\u0438\u043D\u0443\u0442\u044B_\u043C\u0438\u043D\u0443\u0442",
                hh: "\u0447\u0430\u0441_\u0447\u0430\u0441\u0430_\u0447\u0430\u0441\u043E\u0432",
                dd: "\u0434\u0435\u043D\u044C_\u0434\u043D\u044F_\u0434\u043D\u0435\u0439",
                MM: "\u043C\u0435\u0441\u044F\u0446_\u043C\u0435\u0441\u044F\u0446\u0430_\u043C\u0435\u0441\u044F\u0446\u0435\u0432",
                yy: "\u0433\u043E\u0434_\u0433\u043E\u0434\u0430_\u043B\u0435\u0442"
            } [y].split("_"), L % 10 == 1 && L % 100 != 11 ? Q[0] : L % 10 >= 2 && L % 10 <= 4 && (L % 100 < 10 || L % 100 >= 20) ? Q[1] : Q[2])
        }
        var p = function(M, R) {
            return u.test(R) ? r[M.month()] : o[M.month()]
        };
        p.s = o, p.f = r;
        var b = function(M, R) {
            return u.test(R) ? l[M.month()] : c[M.month()]
        };
        b.s = c, b.f = l;
        var T = {
            name: "ru",
            weekdays: "\u0432\u043E\u0441\u043A\u0440\u0435\u0441\u0435\u043D\u044C\u0435_\u043F\u043E\u043D\u0435\u0434\u0435\u043B\u044C\u043D\u0438\u043A_\u0432\u0442\u043E\u0440\u043D\u0438\u043A_\u0441\u0440\u0435\u0434\u0430_\u0447\u0435\u0442\u0432\u0435\u0440\u0433_\u043F\u044F\u0442\u043D\u0438\u0446\u0430_\u0441\u0443\u0431\u0431\u043E\u0442\u0430".split("_"),
            weekdaysShort: "\u0432\u0441\u043A_\u043F\u043D\u0434_\u0432\u0442\u0440_\u0441\u0440\u0434_\u0447\u0442\u0432_\u043F\u0442\u043D_\u0441\u0431\u0442".split("_"),
            weekdaysMin: "\u0432\u0441_\u043F\u043D_\u0432\u0442_\u0441\u0440_\u0447\u0442_\u043F\u0442_\u0441\u0431".split("_"),
            months: p,
            monthsShort: b,
            weekStart: 1,
            yearStart: 4,
            formats: {
                LT: "H:mm",
                LTS: "H:mm:ss",
                L: "DD.MM.YYYY",
                LL: "D MMMM YYYY \u0433.",
                LLL: "D MMMM YYYY \u0433., H:mm",
                LLLL: "dddd, D MMMM YYYY \u0433., H:mm"
            },
            relativeTime: {
                future: "\u0447\u0435\u0440\u0435\u0437 %s",
                past: "%s \u043D\u0430\u0437\u0430\u0434",
                s: "\u043D\u0435\u0441\u043A\u043E\u043B\u044C\u043A\u043E \u0441\u0435\u043A\u0443\u043D\u0434",
                m: f,
                mm: f,
                h: "\u0447\u0430\u0441",
                hh: f,
                d: "\u0434\u0435\u043D\u044C",
                dd: f,
                M: "\u043C\u0435\u0441\u044F\u0446",
                MM: f,
                y: "\u0433\u043E\u0434",
                yy: f
            },
            ordinal: function(M) {
                return M
            },
            meridiem: function(M) {
                return M < 4 ? "\u043D\u043E\u0447\u0438" : M < 12 ? "\u0443\u0442\u0440\u0430" : M < 17 ? "\u0434\u043D\u044F" : "\u0432\u0435\u0447\u0435\u0440\u0430"
            }
        };
        return n.default.locale(T, null, !0), T
    })
})(Nd);
var ur = {
    exports: {}
};
(function(e, t) {
    (function(i, s) {
        e.exports = s()
    })(gs, function() {
        return function(i, s, n) {
            s.prototype.isTomorrow = function() {
                var r = "YYYY-MM-DD",
                    o = n().add(1, "day");
                return this.format(r) === o.format(r)
            }
        }
    })
})(ur);
const Vd = ur.exports;
ws.extend(Vd);
ws.locale("ru");
window.YaDelivery = {
    createWidget: function(e) {
        window.YaDelivery.params = e.params, Bl(Bd).use(ya).mount("#" + e.containerId)
    },
    pointOfferMap: {}
};
document.dispatchEvent(new Event("YaNddWidgetLoad"));