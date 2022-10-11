require('./bootstrap');


import Vue from 'vue';

/*VueI18n*/
import VueI18n from 'vue-i18n'
Vue.use(VueI18n);

const i18n = new VueI18n({
  locale: document.documentElement.lang, // set locale
  messages: require('../lang/translate.js'), // set locale messages
});

/*Vuex*/
import Vuex from 'vuex';
Vue.use(Vuex);

import storeObj from './store';
const store = new Vuex.Store(storeObj(i18n));

/*Global Components Start*/
import templateComponent from './components/admin/template/TemplateComponent.vue';

import succeedMsgComponent from './components/messages/SucceedMsgComponent';
import errorMsgListComponent from './components/messages/ErrorMsgListComponent';
import errorMsgComponent from './components/messages/ErrorMsgComponent';
import errorMsg2Component from './components/messages/ErrorMsg2Component';
import formComponent from './components/form/FormComponent';

Vue.component('template-component', templateComponent);

Vue.component('succeed-msg-component', succeedMsgComponent);
Vue.component('error-msg-list-component', errorMsgListComponent);
Vue.component('error-msg-component', errorMsgComponent);
Vue.component('error-msg2-component', errorMsg2Component);
Vue.component('form-form-component', formComponent);
/*Global Components End*/

/*Components*/
import components from './components';

/*Global Mixin*/
import globalMixin from './globalMixin';
Vue.mixin(globalMixin);

/*VTooltip*/
/* import VTooltip from 'v-tooltip'
Vue.use(VTooltip) */

/*SharedState*/
const sharedState = {
	currentLocallang: document.documentElement.lang,
}

export const app = new Vue({
	el: "#app",
	store,
	sharedState,
	components,
	i18n,
})
