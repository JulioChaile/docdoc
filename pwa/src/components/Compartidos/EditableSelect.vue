<template>
  <div class="row main_container q-pa-sm">
    <div class="col-12">
      <span :class="'text-weight-bolder text-body2 text-' + labelColor">{{ label }}</span>
    </div>
    <div class="col-12">
      <q-select v-model="valorSeleccionado" :options="opciones" :disable="disabled" outlined @input="seleccionoValor()" @filter="filterFn" />
    </div>
  </div>
</template>

<script>
export default {
  name: 'EditableSelect',
  props: {
    opcionesProp: {
      type: Array,
      default: () => []
    },
    label: {
      type: String,
      default: 'dato'
    },
    labelColor: {
      type: String,
      default: 'primary'
    },
    disabled: {
      type: Boolean,
      default: false
    },
    valor: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      valorSeleccionado: '',
      opciones: this.opcionesProp
    }
  },
  mounted () {
    this.valorSeleccionado = this.valor
  },
  watch: {
    valor () {
      this.valorSeleccionado = this.valor
    }
  },
  methods: {
    filterFn (val, update) {
      if (val === '') {
        update(() => {
          this.opciones = this.opciones
        })
        return
      }

      update(() => {
        const needle = val.toLowerCase()
        this.opciones = this.opciones.filter(v => v.toLowerCase().indexOf(needle) > -1)
      })
    },
    seleccionoValor () {
      // Emito el evento al padre
      this.$emit('selecciono', this.valorSeleccionado)
    }
  }
}
</script>

<style scoped>
.main_container {
  /* background-color: rgba(0, 0, 0, 0.08); */
  border-radius: 10px;
  max-width: fit-content;
}
</style>
