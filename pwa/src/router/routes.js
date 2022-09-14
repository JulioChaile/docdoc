
const routes = [
  {
    path: '/',
    component: () => import('layouts/DocdocLayout.vue'),
    children: [
      {
        path: '',
        name: 'Inicio',
        component: () => import('pages/Index.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'Login',
        name: 'Login',
        component: () => import('pages/Login.vue'),
        meta: {
          requiresAuth: false,
          withoutToolbar: true
        }
      },
      {
        path: 'Vencimientos',
        name: 'Vencimientos',
        component: () => import('pages/Vencimientos.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'Tribunales',
        name: 'Tribunales',
        component: () => import('pages/Tribunales.vue'),
        meta: {
          requiresAuth: true
        },
        props: (route) => ({ preview: route.query.p })
      },
      {
        path: 'Casos',
        name: 'Casos',
        component: () => import('pages/Casos.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'Test',
        name: 'Test',
        component: () => import('pages/Test.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'Caso',
        name: 'Caso',
        component: () => import('pages/Caso.vue'),
        meta: {
          requiresAuth: true
        },
        props: (route) => ({ id: route.query.id })
      },
      {
        path: 'Compartidos',
        name: 'Compartidos',
        component: () => import('pages/Compartidos.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'Movimientos/:idCaso',
        name: 'Movimientos',
        component: () => import('pages/Movimientos.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'Utilidades',
        name: 'Utilidades',
        component: () => import('pages/Utilidades.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'AltaCaso',
        name: 'Nuevo Caso',
        component: () => import('pages/AltaCasos.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'GrillaCasos',
        name: 'Grilla Casos',
        component: () => import('pages/GrillaCasos.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'CasosPendientes',
        name: 'Casos Pendientes',
        component: () => import('pages/CasosPendientes.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'AltaMovimiento/:idCaso',
        name: 'Nuevo Movimiento',
        component: () => import('pages/AltaMovimientos.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'registrar/:token',
        name: 'Nuevo Usuario',
        component: () => import('pages/RegistrarUsuario.vue'),
        props: true,
        meta: {
          requiresAuth: false,
          withoutToolbar: true
        }
      },
      {
        path: '/Chat',
        name: 'Chat',
        component: () => import('pages/Chat.vue'),
        props: true,
        meta: {
          requiresAuth: true
        }
      },
      {
        path: '/ChatNotificaciones',
        name: 'Chat Notificaciones',
        component: () => import('pages/ChatNotificaciones.vue'),
        props: true,
        meta: {
          requiresAuth: true
        }
      },
      {
        path: '/Mediaciones',
        name: 'Mediaciones',
        component: () => import('pages/Mediaciones.vue'),
        props: true,
        meta: {
          requiresAuth: true
        }
      },
      {
        path: '/CausasPenales',
        name: 'CausasPenales',
        component: () => import('pages/CausasPenales.vue'),
        props: true,
        meta: {
          requiresAuth: true
        }
      },
      {
        path: '/Contactos',
        name: 'Contactos del Estudio',
        component: () => import('pages/ContactosEstudio.vue'),
        props: true,
        meta: {
          requiresAuth: true
        }
      },
      {
        path: '/Whatsapp',
        name: 'Whatsapp',
        component: () => import('pages/Whatsapp.vue'),
        props: true,
        meta: {
          requiresAuth: true
        }
      },
      {
        path: '/Judiciales',
        name: 'Judiciales',
        component: () => import('pages/Judiciales.vue'),
        props: true,
        meta: {
          requiresAuth: true
        }
      },
      {
        path: '/ArchivosCaso',
        name: 'Archivos del Caso',
        component: () => import('pages/ArchivosCaso.vue'),
        props: true,
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'Calendario',
        name: 'Calendario',
        component: () => import('pages/Calendario.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: '/Maps',
        name: 'Maps',
        component: () => import('pages/Maps.vue'),
        props: true,
        meta: {
          withoutToolbar: true
        }
      }
    ]
  }
  /* {
    path: '/Chat',
    component: () => import('layouts/ChatLayout.vue'),
    children: [
      {
        path: '/',
        name: 'Chat',
        component: () => import('pages/Chat.vue'),
        props: true,
        meta: { requiresAuth: true }
      }
    ]
  } */
]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue')
  })
}

export default routes
