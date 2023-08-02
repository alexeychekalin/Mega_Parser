import axios from 'axios'
import router from '../router'

export default {
    namespaced: true,
    state:{
        authenticated:false,
        user:{},
        role: '',
        accessWeb:false
    },
    getters:{
        authenticated(state){
            return state.authenticated
        },
        user(state){
            return state.user
        },
        role(state){
            return state.role
        },
        accessWeb(state){
            return state.accessWeb
        },

    },
    mutations:{
        SET_AUTHENTICATED (state, value) {
            state.authenticated = value
        },
        SET_USER (state, value) {
            state.user = value
        },
        SET_ROLE (state, value) {
            state.role = value
        },
        SET_ACCESSWEB (state, value) {
            state.accessWeb = value
        },
    },
    actions:{
        login({commit}){
            return axios.get('/api/user').then(({data})=>{
                commit('SET_USER',data)
                commit('SET_AUTHENTICATED',true)
                commit('SET_ROLE', data.role)
                commit('SET_ACCESSWEB', data.AccessWeb === 1)
                router.push('dashboard')
            }).catch(({response:{data}})=>{
                commit('SET_USER',{})
                commit('SET_AUTHENTICATED',false)
                commit('SET_ROLE', '')
                commit('SET_ACCESSWEB', '')
            })
        },
        logout({commit}){
            commit('SET_USER',{})
            commit('SET_AUTHENTICATED',false)
            commit('SET_ROLE', '')
            commit('SET_ACCESSWEB', false)
        }
    }
}
