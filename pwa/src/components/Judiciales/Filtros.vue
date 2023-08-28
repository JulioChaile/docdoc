<template>
    <q-expansion-item ref="expansionFiltros" label="Filtros">
        <q-checkbox
            v-for="j in Juzgados"
            v-model="JuzgadosSeleccionados"
            :val="j.value"
            :label="j.label"
            :key="j.value"
            style="margin-left: 10px"
            @input="$emit('JuzgadosSeleccionados', JuzgadosSeleccionados)"
        />
    </q-expansion-item>
  </template>
  
  <script>
  export default {
    props: ['Juzgados'],
    data () {
      return {
        JuzgadosSeleccionados: []
      }
    }
  }
  </script>
  