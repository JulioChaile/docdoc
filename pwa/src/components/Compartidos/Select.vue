<template>
    <q-select
        filled
        v-model="seleccionados"
        :multiple="multiple"
        use-input
        hide-selected
        fill-input
        input-debounce="0"
        :label="label"
        :options="opcionesSeleccion"
        @filter="filterFn"
        style="margin-top: 10px; width: 100%"
        :hint="hint"
    >
      <template v-slot:option="scope" v-if="tooltip">
        <q-item
          v-bind="scope.itemProps"
          v-on="scope.itemEvents"
        >
          <q-item-section>
            <q-item-label v-html="scope.opt.label" />
            <q-tooltip>
              {{ scope.opt.tooltip }}
            </q-tooltip>
          </q-item-section>
        </q-item>
      </template>
      <template v-slot:no-option>
          <q-item>
            <q-item-section class="text-grey">
                No se encontraron elementos que coincidan con la busqueda
            </q-item-section>
          </q-item>
      </template>
    </q-select>
</template>

<script>
export default {
  name: 'Select',
  props: [ 'multiple', 'opciones', 'label', 'hint', 'tooltip' ],
  data () {
    return {
      seleccionados: this.multiple ? [] : '',
      opcionesSeleccion: this.opciones
    }
  },
  watch: {
    seleccionados () {
      this.$emit('seleccion', this.seleccionados)
    }
  },
  methods: {
    filterFn (val, update, abort) {
      val = this.accentFold(val)

      setTimeout(() => {
        update(
          () => {
            if (val === '') {
              this.opcionesSeleccion = this.opciones
            } else {
              const needle = val.toLowerCase()
              this.opcionesSeleccion = this.opciones.filter(v => this.accentFold(v.label).toLowerCase().indexOf(needle) > -1)
            }
          },
          ref => {
            if (val !== '' && ref.options.length > 0) {
              ref.setOptionIndex(-1) // reset optionIndex in case there is something selected
              ref.moveOptionSelection(1, true) // focus the first selectable option and do not update the input-value
            }
          }
        )
      }, 300)
    },
    accentFold (s) {
      const accent = {'á': 'a', 'é': 'e', 'è': 'e', 'í': 'i', 'ó': 'o', 'ú': 'u', 'Á': 'a', 'É': 'e', 'Í': 'i', 'Ó': 'o', 'Ú': 'u'}

      if (!s) { return '' }
      var ret = ''
      for (var i = 0; i < s.length; i++) {
        ret += accent[s.charAt(i)] || s.charAt(i)
      }

      return ret
    }
  }
}
</script>
