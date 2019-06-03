'use strict';

import Vue from 'vue'
import HelloWorld from './components/HelloWorld.vue'


!(function(){

  Vue.config.productionTip = false

  /* eslint-disable no-new */
  new Vue({
    el: '#app',
    components: { HelloWorld },
  })

})();