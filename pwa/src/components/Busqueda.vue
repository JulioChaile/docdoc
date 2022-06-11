<template>
  <div style="background:white; padding:1em" class="shadow-1">
    <q-search
      v-model="textoBusqueda"
      @input="ingresoBusqueda"
      :debounce="600"
      :placeholder="placeholder || 'Buscar'"
    />
    <q-expansion-item
      :opened="true"
      :popup="true"
      :icon-toggle="true"
      label="Seleccionar tipo de busqueda"
      v-if="displayOptions"
    >
      <div
        v-for="t in tiposBusqueda"
        class="tipo-busqueda"
        :key="t.TipoBusqueda"
        @click="seleccionTipo(t)"
      >
        <q-icon class="on-left" :name="t.icon" /> {{ t.TipoBusqueda }}
      </div>
    </q-expansion-item>
    <q-chip
      v-for="(s, i) in Selecciones"
      :key="i"
      :icon="s.icon"
      color="primary"
      style="margin: .4em .2em .2em 0;"
      closable
      small
      @hide="quitarSeleccion(i)"
    >
      {{ s.Texto }}
    </q-chip>
  </div>
</template>
<script>
export default {
  props: ['placeholder'],
  data () {
    return {
      displayOptions: false,
      Selecciones: [],
      textoBusqueda: '',
      tiposBusqueda: [
        {
          TipoBusqueda: 'Persona',
          icon: 'face'
        },
        {
          TipoBusqueda: 'Juzgado',
          icon: 'account_balance'
        }
      ]
    }
  },
  methods: {
    ingresoBusqueda () {
      this.displayOptions = true
    },
    seleccionTipo (tipo) {
      const seleccion = Object.assign({}, tipo)
      seleccion.Texto = this.textoBusqueda
      this.textoBusqueda = ''
      this.displayOptions = false
      this.Selecciones.push(seleccion)
    },
    quitarSeleccion (i) {
      this.Selecciones.splice(i, 1)
    }
  }
}
</script>
<style>
.tipo-busqueda {
  padding-bottom: 10px;
  padding-top: 10px;
  border-top: 1px #c8c8c8 solid;
  cursor: pointer;
}
</style>
