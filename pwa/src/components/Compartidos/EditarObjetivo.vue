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

        <q-item>
          <q-input v-model="objetivo.Objetivo" @keypress.enter.prevent="guardar()" />
        </q-item>

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
    }
  },
  methods: {
    close () {
      this.$emit('update:dialog', false)
    },
    guardar () {
      this.$emit('guardarObjetivo', this.objetivo)
      this.close()
    }
  }
}
</script>
