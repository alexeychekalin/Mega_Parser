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
            admin: true
          },
        },
        {
          path: 'usersnew',
          name: 'usersnew',
          component: () => import('../pages/usersNew.vue'),
          meta:{
            middleware:"auth",
            admin: true
          },
        },
        {
          path: 'products',
          name: 'products',
          component: () => import('../pages/products.vue'),
          meta:{
            middleware:"auth",
            admin: true
          },
        },
        {
          path: 'monitor',
          name: 'monitor',
          component: () => import('../pages/monitor.vue'),
          meta:{
            middleware:"auth",
            admin: true
          },
        },
        {
          path: 'monitorTypes',
          name: 'monitorTypes',
          component: () => import('../pages/monitorTypes.vue'),
          meta:{
            middleware:"auth",
            admin: true
          },
        },
        {
          path: 'productsnew',
          name: 'productsnew',
          component: () => import('../pages/productNew.vue'),
          meta:{
            middleware:"auth",
            admin: true
          },
        },
        {
          path: 'types',
          name: 'types',
          component: () => import('../pages/types.vue'),
          meta:{
            middleware:"auth",
            admin: true
          },
        },
        {
          path: 'colors',
          name: 'colors',
          component: () => import('../pages/colors.vue'),
          meta:{
            middleware:"auth",
            admin: true
          },
        },
        {
          path: 'providers',
          name: 'providers',
          component: () => import('../pages/providers.vue'),
          meta:{
            middleware:"auth",
            admin: true
          },
        },
        {
          path: 'errors',
          name: 'errors',
          component: () => import('../pages/errors.vue'),
          meta:{
            middleware:"auth",
            admin: true
          },
        },
        {
          path: 'notype',
          name: 'notype',
          component: () => import('../pages/notype.vue'),
          meta:{
            middleware:"auth",
            admin: true
          },
        },
        {
          path: 'nosmm',
          name: 'nosmm',
          component: () => import('../pages/nosmm.vue'),
          meta:{
            middleware:"auth",
            admin: true
          },
        },
        {
          path: 'logs',
          name: 'logs',
          component: () => import('../pages/logs.vue'),
          meta:{
            middleware:"auth",
            admin: true
          },
        },
          /*
        {
          path: 'test',
          name: 'test',
          component: () => import('../pages/test.vue'),
          meta:{
            middleware:"auth",
            admin: true
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
           */
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
