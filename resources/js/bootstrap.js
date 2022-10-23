window._ = require('lodash');
// window.Popper = require('popper.js').default;

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

 try {
    window.$ = window.jQuery = require('jquery');

    require('jquery-ui/ui/widgets/datepicker.js');
    // require('jquery-ui/ui/widgets/sortable.js');
    require('jquery-ui/ui/i18n/datepicker-tr.js');

    require('bootstrap');
    require('bootstrap/dist/js/bootstrap.bundle');
    // require('bootstrap/dist/js/bootstrap.esm');
    

    /*Datatables*/
    require( 'datatables.net-bs5' )();
    require( 'datatables.net-responsive-bs5' )();
    
} catch (e) {}

/*Moment JS*/
window.moment = require('moment');

/* InputMask */
require('inputmask');

/*CropperJS*/
// window.Cropper = require('cropperjs/dist/cropper.js');


/* AdminLTE */
require('./adminlte/js/adminlte.js');

/* Select2 */
require('./adminlte/plugins/select2/js/select2.full');

/* daterange picker */
require('./adminlte/plugins/daterangepicker/daterangepicker');

/* jquery-knob */
require('./adminlte/plugins/jquery-knob/jquery.knob.min');

/* Chart JS */
require('chart.js');

/*Main JS*/
require('./jquery/main.js');
require('./jquery/main-ajax.js');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
