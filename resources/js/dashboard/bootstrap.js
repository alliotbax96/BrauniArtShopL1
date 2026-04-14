import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
import $ from 'jquery';
// Гарантируем глобальную доступность jQuery
if (!window.jQuery) {
    window.$ = window.jQuery = $;
}

import moment from 'moment';
window.moment = moment;
import PerfectScrollbar from 'perfect-scrollbar';
window.PerfectScrollbar = PerfectScrollbar;
import Swal from 'sweetalert2';
window.Swal = Swal;



