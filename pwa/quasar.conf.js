// Configuration for your app

module.exports = function (ctx) {
  return {
    // app boot (/src/boot)
    boot: [
      'axios'
    ],
    css: [
      'app.scss'
    ],
    extras: [
      // 'roboto-font',
      'material-icons',
      'material-icons-round',
      'material-icons-outlined',
      'ionicons-v4',
      'mdi-v3',
      'fontawesome-v5'
    ],
    supportIE: true,
    build: {
      scopeHoisting: true,
      vueRouterMode: 'history',
      vueCompiler: true,
      gzip: true,
      // analyze: true,
      // extractCSS: false,
      extendWebpack (cfg) {
        /* cfg.module.rules.push({
          enforce: 'pre',
          test: /\.(js|vue)$/,
          loader: 'eslint-loader',
          exclude: /(node_modules|quasar)/
        }) */
        /*
        cfg.module.rules.push({
          test: /\.ttf$/,
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: 'fonts/'
          },
          exclude: /(node_modules|quasar)/
        })
        */
      }
    },
    devServer: {
      // https: true,
      // port: 8080,
      open: true // opens browser window automatically
    },
    // framework: 'all' --- includes everything; for dev only!
    framework: {
      components: [
        'QAvatar',
        'QBadge',
        'QBanner',
        'QLayout',
        'QHeader',
        'QDrawer',
        'QPageContainer',
        'QPage',
        'QToolbar',
        'QToolbarTitle',
        'QBtn',
        'QIcon',
        'QList',
        'QItemLabel',
        'QItem',
        'QItemSection',
        'QFab',
        'QFabAction',
        'QTooltip',
        'QField',
        'QInput',
        'QCard',
        'QCardSection',
        'QSeparator',
        'QCardActions',
        'QSpace',
        'QStepper',
        'QStep',
        'QStepperNavigation',
        'QBtnDropdown',
        'QDate',
        'QTime',
        'QOptionGroup',
        'QToggle',
        'QExpansionItem',
        'QEditor',
        'QSelect',
        'QDialog',
        'QBtnToggle',
        'QBtnGroup',
        'QCheckbox',
        'QColor',
        'QChip',
        'QTable',
        'QTh',
        'QTr',
        'QTd',
        'QMenu',
        'QUploader',
        'QPopupProxy',
        'QPopupEdit',
        'QInfiniteScroll',
        'QSpinnerDots',
        'QChatMessage'
      ],
      directives: [
        'Ripple',
        'ClosePopup'
      ],
      // Quasar plugins
      plugins: [
        'Notify',
        'Dialog'
      ],
      // iconSet: ctx.theme.mat ? 'material-icons' : 'ionicons'
      lang: 'es' // Quasar language
    },
    // animations: 'all' --- includes all animations
    animations: [],
    ssr: {
      pwa: true
    },
    pwa: {
      workboxPluginMode: 'InjectManifest',
      workboxOptions: {},
      manifest: {
        name: 'DocdocApp',
        short_name: 'Docdoc Abogados',
        description: 'Docdoc! Abogados Online',
        display: 'standalone',
        background_color: '#ffffff',
        theme_color: '#027be3',
        icons: [
          {
            'src': 'statics/icons/icon-128x128.png',
            'sizes': '128x128',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-192x192.png',
            'sizes': '192x192',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-256x256.png',
            'sizes': '256x256',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-384x384.png',
            'sizes': '384x384',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-512x512.png',
            'sizes': '512x512',
            'type': 'image/png'
          }
        ],
        'start_url': '/index.html'
      }
    },
    cordova: {
      // id: 'org.cordova.quasar.app'
    },
    electron: {
      // bundler: 'builder', // or 'packager'
      extendWebpack (cfg) {
        // do something with Electron process Webpack cfg
      },
      packager: {
        // https://github.com/electron-userland/electron-packager/blob/master/docs/api.md#options

        // OS X / Mac App Store
        // appBundleId: '',
        // appCategoryType: '',
        // osxSign: '',
        // protocol: 'myapp://path',

        // Window only
        // win32metadata: { ... }
      },
      builder: {
        // https://www.electron.build/configuration/configuration

        // appId: 'quasar-app'
      }
    }
  }
}
