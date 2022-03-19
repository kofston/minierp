/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;
import jQuery from 'jquery'
import Buefy from 'buefy';
import Raphael from 'raphael/raphael'
global.Raphael = Raphael
import { AreaChart } from 'vue-morris'
import 'buefy/dist/buefy.css';
import VueResource from 'vue-resource'
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
Vue.use(Buefy, {
    defaultIconPack: 'fas'
});
Vue.use(VueResource);
Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('header-component', require('./components/HeaderComponent.vue').default);
Vue.component('home-component', require('./components/HomeComponent.vue').default);

Vue.component('client-component', require('./components/ClientComponent.vue').default);
Vue.component('clientadd-component', require('./components/add/ClientAddComponent.vue').default);

Vue.component('product-component', require('./components/ProductComponent.vue').default);
Vue.component('productadd-component', require('./components/add/ProductAddComponent.vue').default);

Vue.component('order-component', require('./components/OrderComponent.vue').default);
Vue.component('orderadd-component', require('./components/add/OrderAddComponent.vue').default);


Vue.component('delivery-component', require('./components/DeliveryComponent.vue').default);


Vue.component('helpdesk-component', require('./components/HelpdeskComponent.vue').default);


Vue.component('offer-component', require('./components/OfferComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
