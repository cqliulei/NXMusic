import './assets/main.css'

import { createApp } from 'vue'

//引入状态管理类似vuex
import { createPinia } from 'pinia'
import App from './App.vue'

const app = createApp(App)

//如果使用 unplugin-element-plus 并且只使用组件 API，你需要手动导入样式。
import 'element-plus/dist/index.css'


// //引入elmentPlus--全局
// import ElementPlus from 'element-plus'
// import 'element-plus/dist/index.css'
// app.use(ElementPlus)

app.use(createPinia())



app.mount('#app')
