import { createRouter, createWebHistory } from '@ionic/vue-router'; 

import Login           from '../views/Login.vue'
import Home            from '../views/Home.vue'
import Room            from '../views/Room.vue'
import Make            from '../views/Make.vue'
import Live            from '../views/Live.vue'
import Notifications   from '../views/Notifications.vue'
import Search          from '../views/Search.vue'
import Setup           from '../views/Setup.vue'
import Join        from '../views/Join.vue'
import Settings          from '../views/Settings.vue'

const routes = [
  {
    path: '/settings',
    name: 'Settings',
    component:Settings,
    meta: {
      requiresAuth: true,
      navbar:true 
    },
  },
  {
    path: '/join',
    name: 'Join',
    component: Join,
    meta: {
      requiresAuth: true,
      navbar:true 
    },
  },
  {
    path: '/setup',
    name: 'Setup',
    component: Setup,
    meta: {
      requiresAuth: true,
      navbar:false 
    },
  },
  {
    path: '/search',
    name: 'Search',
    component: Search,
    meta: {
      requiresAuth: true,
      navbar:true 
    },
  },
  {
    path: '/notifications',
    name: 'Notifications',
    component: Notifications,
    meta: {
      requiresAuth: true,
      navbar:true 
    },
  },
  {
    path: '/live',
    name: 'Live',
    component: Live,
    meta: {
      requiresAuth: true,
      navbar:false 
    },
  },

  {
    path: '/make',
    name: 'Make',
    component: Make,
    meta: {
      requiresAuth: true,
      navbar:true 
    },
  },
  {
    path: '/',
    redirect: '/home',
    meta: {
      requiresAuth: false,
      navbar:false 
    },
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: {
      requiresAuth: false,
      navbar:false 
    },
  },
  {
    path: '/home',
    name: 'Home',
    component: Home,
    meta: {
      requiresAuth: true,
      navbar:true 
    },
  },
  {
    path: '/room/:id',
    name: 'Room',
    component: Room,
    meta: {
      requiresAuth: true,
      navbar:false 
    },
    params:true
  },

]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})
 
 router.beforeEach((to, from, next) => {
      const token = JSON.parse( localStorage.getItem('user_settings') ) || null
      const localstorage = localStorage.getItem('user_settings')  
 
      /*Dont back to login page!*/
      if ( to.name == 'Login' && token != null ) { 
        next({name: 'Home'})
      }

      // If logged in, or going to the Login page. 
      if (to.meta.requiresAuth && token?.id == null) { 
        next({name: 'Login'})
      } else {  
        next()
      }
});
  


export default router
