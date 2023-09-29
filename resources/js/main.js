/* eslint-disable import/order */
import '@/@iconify/icons-bundle'
import App from '@/App.vue'
import vuetify from '@/plugins/vuetify'
import { loadFonts } from '@/plugins/webfontloader'
import router from '@/router'
import '@core-scss/template/index.scss'
import '@layouts/styles/index.scss'
import '@styles/styles.scss'
import store from '../js/store'
import { createApp } from 'vue'
import axios from "axios";

import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

loadFonts()

// Create vue app
const app = createApp(App)

const axiosInstance = axios.create({
    withCredentials: true,
})

app.config.globalProperties.$axios = { ...axiosInstance }


router.beforeEach((to, from, next) => {
    //document.title = `${to.meta.title} - ${process.env.MIX_APP_NAME}`
    store.dispatch('auth/checkUser')

    if(to.meta.middleware==="guest"){
        if(store.state.auth.authenticated && store.state.auth.accessWeb){
            next({name:"dashboard"})
        }else{
            if(!store.state.auth.accessWeb)
                next()
            else
                next({name:"banned"})
        }
    }else{
        if(store.state.auth.authenticated && store.state.auth.accessWeb){
            next()
        }else{
            if(!store.state.auth.authenticated && !store.state.auth.accessWeb)
                next({name:"login"})
            else
                next({name:"banned"})
        }
    }
})

// Use plugins
app.use(vuetify)
app.use(router)
app.use(store)

app.use(Toast, {
    transition: "Vue-Toastification__bounce",
    maxToasts: 2,
    newestOnTop: true,
    filterToasts: toasts => {
        // Keep track of existing types
        const types = {};
        return toasts.reduce((aggToasts, toast) => {
            // Check if type was not seen before
            if (!types[toast.type]) {
                aggToasts.push(toast);
                types[toast.type] = true;
            }
            return aggToasts;
        }, []);
    }
});

// Mount vue app
app.mount('#app')
