<template>
  <q-dialog v-model="dialog" persistent>
    <q-card class="q-px-xl q-py-lg">
      <q-list highlight>
        <q-item header class="text-black q-subheading">
          <q-item-section>
            <span class="text-body">
              Nuevo Objetivo
            </span>
          </q-item-section>
        </q-item>

        <div>
          <q-input v-model="nuevo_objetivo.Objetivo" label="Nuevo objetivo" @keypress.enter.prevent="agregar()" />
          <q-select
            dense
            class="q-my-lg"
            v-model="nuevo_objetivo.IdTipoMov"
            :options="TiposMov.map(t => ({ label: t.TipoMovimiento, value: t.IdTipoMov }))"
            label="Tipo de Movimiento"
          />
          <q-select
            dense
            class="q-mb-lg"
            v-model="nuevo_objetivo.ColorMov"
            :options="[
              { value: 'negative', label: 'Perentorios' },
              { value: 'primary', label: 'Gestion Estudio' },
              { value: 'warning', label: 'Gestion Externa' },
              { value: 'positive', label: 'Finalizados' }
            ]"
            label="Estado de Gestión"
          />
        </div>

        <q-card-actions>
          <q-btn color="primary" label="Aceptar" @click="agregar()" />
          <q-btn flat color="primary" label="Cancelar" @click="close()" />
        </q-card-actions>
      </q-list>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  name: 'NuevoObjetivo',
  props: {
    dialog: {
      type: Boolean,
      default: false
    },
    TiposMov: {
      type: Array,
      default: []
    }
  },
  data () {
    return {
      nuevo_objetivo: {
        Objetivo: '',
        IdTipoMov: null,
        ColorMov: ''
      }
    }
  },
  methods: {
    close () {
      this.$emit('update:dialog', false)
      this.nuevo_objetivo = ''
    },
    agregar () {
      if (this.nuevo_objetivo.Objetivo && this.nuevo_objetivo.IdTipoMov && this.nuevo_objetivo.ColorMov) {
        this.$emit('agregarObjetivo', this.nuevo_objetivo)
        this.nuevo_objetivo = ''
        this.close()
      }
    }
  }
}
</script>
