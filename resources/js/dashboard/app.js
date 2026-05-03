import './bootstrap';
import moment from 'moment';
window.moment = moment;
import PerfectScrollbar from 'perfect-scrollbar';
window.PerfectScrollbar = PerfectScrollbar;
import Swal from 'sweetalert2';
window.Swal = Swal;

// vendor.min.js
import '../../vendor/dashboard/js/nxlNavigation.min.js';
import '../../vendor/dashboard/js/jquery-ui.min.js';
import '../../vendor/dashboard/js/pace.min.js';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import '../../vendor/dashboard/js/full-screen-helper.min.js';
import './theme-customizer-init.min.js';
// vendor.min.js

import './common-init.min.js';
import '../dashboard/scripts/notifications.js';
