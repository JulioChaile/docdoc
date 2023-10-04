<template>
  <q-dialog v-model="dialog" persistent>
    <q-card class="q-px-xl q-py-lg">
      <q-list highlight>
        <q-item header class="text-black q-subheading">
          <q-item-section>
            <span class="text-body">
              Editar Objetivo
            </span>
          </q-item-section>
        </q-item>

        <div>
          <q-input v-model="objetivo.Objetivo" @keypress.enter.prevent="guardar()" />
          <q-select
            dense
            class="q-my-lg"
            v-model="objetivo.IdTipoMov"
            :options="TiposMov.map(t => ({ label: t.TipoMovimiento, value: t.IdTipoMov }))"
            label="Tipo de Movimiento"
            emit-value
            map-options
          />
          <q-select
            dense
            class="q-mb-lg"
            v-model="objetivo.ColorMov"
            :options="[
              { value: 'negative', label: 'Perentorios' },
              { value: 'primary', label: 'Gestion Estudio' },
              { value: 'warning', label: 'Gestion Externa' },
              { value: 'positive', label: 'Finalizados' }
            ]"
            label="Estado de Gestión"
            emit-value
            map-options
          />
        </div>

        <q-card-actions>
          <q-btn color="primary" label="Guardar" @click="guardar()" />
          <q-btn flat color="primary" label="Cancelar" @click="close()" />
        </q-card-actions>
      </q-list>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  name: 'EditarObjetivo',
  props: {
    dialog: {
      type: Boolean,
      default: false
    },
    objetivo: {
      type: Object,
      default: () => {}
    },
    TiposMov: {
      type: Array,
      default: []
    }
  },
  data () {
    return {
      nuevo_objetivo: {
        IdTipoMov: null,
        ColorMov: ''
      }
    }
  },
  methods: {
    close () {
      this.$emit('update:dialog', false)
    },
    guardar () {
      this.$emit('guardarObjetivo',{
        ...this.objetivo
      })
      this.close()
    }
  }
}
</script>
