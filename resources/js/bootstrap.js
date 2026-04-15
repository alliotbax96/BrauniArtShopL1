import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import $ from 'jquery';
import 'jquery-mask-plugin';
import select2 from "select2"
select2();
// Гарантируем глобальную доступность jQuery
if (!window.jQuery) {
    window.$ = window.jQuery = $;
}
import './echo';
