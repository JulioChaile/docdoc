import Vue from 'vue'
import VueRouter from 'vue-router'

import routes from './routes'
import auth from '../auth'
Vue.use(VueRouter)

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation
 */

export default function (/* { store, ssrContext } */) {
  const Router = new VueRouter({
    scrollBehavior: () => ({ y: 0 }),
    routes,

    // Leave these as is and change from quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    mode: "history",
    // mode: process.env.VUE_ROUTER_MODE,
    base: process.env.VUE_ROUTER_BASE
  })

  Router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
      if (localStorage.getItem('toRoute')) {
        sessionStorage.setItem('DOCDOCTOKENPWA', localStorage.getItem('DOCDOCTOKENPWA'))
        sessionStorage.setItem('DOCDOCDEBECAMBIARPASS', localStorage.getItem('DOCDOCDEBECAMBIARPASS'))
        sessionStorage.setItem('DOCDOCUSUARIOLOGUEADO', localStorage.getItem('DOCDOCUSUARIOLOGUEADO'))
        sessionStorage.setItem('mensajes', localStorage.getItem('mensajes'))

        localStorage.removeItem('DOCDOCTOKENPWA')
        localStorage.removeItem('DOCDOCDEBECAMBIARPASS')
        localStorage.removeItem('DOCDOCUSUARIOLOGUEADO')
        localStorage.removeItem('mensajes')
        localStorage.removeItem('toRoute')
      }

      if (!auth.isLoggedIn) {
        next({
          path: '/Login',
          params: { redirect: to.fullPath }
        })
      } else {
        if (typeof auth.UsuarioLogueado.IdRol !== 'undefined') {
          next({
            path: '/Maps'
          })
        } else if (!to.path.includes('Login')) {
          next()
        }
      }
    } else {
      next()
    }
  })

  return Router
}
