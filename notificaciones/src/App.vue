<template>
  <v-app>
    <v-toolbar
      app
    >
      <v-toolbar-title>
        <img src="static/img/icons/docdoc-home.svg" height="40px">
      </v-toolbar-title>
    </v-toolbar>
    <v-content>
      <Notificaciones :abrirDialogo="abrirDialogo"/>
    </v-content>
    <v-btn fixed bottom right fab dark color="orange" @click="buscar">
      <v-icon>search</v-icon>
    </v-btn>
  </v-app>
</template>

<script>
import Notificaciones from './components/Notificaciones'

export default {
  name: 'AppNotificaciones',
  components: {
    Notificaciones
  },
  data () {
    return {
      abrirDialogo: false
    }
  },
  methods: {
    buscar () {
      this.abrirDialogo = !this.abrirDialogo
    }
  }
}
</script>
