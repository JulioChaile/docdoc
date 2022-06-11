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

        <q-item>
          <q-input v-model="nuevo_objetivo" label="Nuevo objetivo" @keypress.enter.prevent="agregar()" />
        </q-item>

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
    }
  },
  data () {
    return {
      nuevo_objetivo: ''
    }
  },
  methods: {
    close () {
      this.$emit('update:dialog', false)
      this.nuevo_objetivo = ''
    },
    agregar () {
      if (this.nuevo_objetivo !== '') {
        this.$emit('agregarObjetivo', this.nuevo_objetivo)
        this.nuevo_objetivo = ''
        this.close()
      }
    }
  }
}
</script>
