import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', redirect: '/dashboard'},
    {
      path: '/',
      component: () => import('../layouts/default.vue'),
      children: [
        {
          path: 'dashboard',
          name: 'dashboard',
          component: () => import('../pages/dashboard.vue'),
          meta:{
            middleware:"auth",
            admin: true,
          },
        },
        {
          path: 'users',
          name: 'users',
          component: () => import('../pages/users.vue'),
          meta:{
            middleware:"auth",
            admin: true,
          },
        },
        {
          path: 'account-settings',
          component: () => import('../pages/account-settings.vue'),
          meta:{
            middleware:"auth",
            admin: true,
          },
        },
        {
          path: 'typography',
          component: () => import('../pages/typography.vue'),
          meta:{
            middleware:"auth",
            admin: true,
          },
        },
        {
          path: 'icons',
          component: () => import('../pages/icons.vue'),
          meta:{
            middleware:"auth",
            admin: true,
          },
        },
        {
          path: 'cards',
          component: () => import('../pages/cards.vue'),
          meta:{
            middleware:"auth",
            admin: true,
          },
        },
        {
          path: 'tables',
          component: () => import('../pages/tables.vue'),
          meta:{
            middleware:"auth",
            admin: true,
          },
        },
        {
          path: 'form-layouts',
          component: () => import('../pages/form-layouts.vue'),
          meta:{
            middleware:"auth",
            admin: true,
          },
        },
      ],
    },
    {
      path: '/',
      component: () => import('../layouts/blank.vue'),
      children: [
        {
          path: 'login',
          name: 'login',
          component: () => import('../pages/login.vue'),
          meta:{
            middleware:"guest",
          }
        },
          /*
        {
          path: 'register',
          component: () => import('../pages/register.vue'),
          meta:{
            middleware:"guest",
          }
        },
        */
        {
          path: '/:pathMatch(.*)*',
          name: 'banned',
          component: () => import('../pages/[...all].vue'),
          meta:{
            middleware:"guest",
          }
        },
      ],
    },
  ],
})

export default router