import { registerVueControllerComponents } from '@symfony/ux-vue';
import './bootstrap.js';
import './styles/app.css';
import "admin-lte/plugins/fontawesome-free/css/all.min.css"
import "admin-lte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css";
import "admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css"
import "admin-lte/plugins/jqvmap/jqvmap.min.css"
import "admin-lte/dist/css/adminlte.min.css"
import "admin-lte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css"
import "admin-lte/plugins/daterangepicker/daterangepicker.css"
import "admin-lte/plugins/summernote/summernote-bs4.min.css"
import "toastify-js/src/toastify.css";

import "admin-lte/plugins/jquery/jquery.min.js"
import "admin-lte/plugins/jquery-ui/jquery-ui.min.js"
import "admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js"
import "admin-lte/plugins/chart.js/Chart.min.js"
import "admin-lte/plugins/sparklines/sparkline.js"
import "admin-lte/plugins/jqvmap/jquery.vmap.min.js"
import "admin-lte/plugins/jqvmap/maps/jquery.vmap.usa.js"
import "admin-lte/plugins/jquery-knob/jquery.knob.min.js"
import "admin-lte/plugins/daterangepicker/daterangepicker.js"
import "admin-lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"
import "admin-lte/dist/js/adminlte.js"

registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));
