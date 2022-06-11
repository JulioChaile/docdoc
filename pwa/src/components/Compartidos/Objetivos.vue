<template>
  <q-dialog v-model="dialog" persistent>
    <q-card class="card_container">
      <q-list highlight>
        <q-item header class="bg-black text-white q-subheading">
          <q-item-section>
            <span class="text-body">
              Objetivos
            </span>
          </q-item-section>
          <q-item-section side>
            <q-btn round icon="mdi-close" @click="close()" />
          </q-item-section>
        </q-item>

        <div v-if="objetivos.length" class="list_container">
          <q-item v-for="(objetivo, index) in objetivos" :key="index">
          <q-item-section>
            <span class="text-body">
              {{ objetivo.Objetivo ? objetivo.Objetivo : 'Sin nombre' }}
            </span>
          </q-item-section>

          <q-item-section side>
            <q-btn round flat icon="mdi-plus-circle" color="primary" @click="vincularObjetivo(objetivo)">
              <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                <span>
                  Asociar objetivo
                </span>
              </q-tooltip>
            </q-btn>
          </q-item-section>
          <q-item-section side>
            <q-btn round flat icon="mdi-pencil" color="secondary" @click="editarObjetivo(objetivo)">
              <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                <span>
                  Editar objetivo
                </span>
              </q-tooltip>
            </q-btn>
          </q-item-section>
          <!--<q-item-section side>
            <q-btn round flat icon="mdi-delete" color="red" @click="eliminarObjetivo(objetivo)" />
          </q-item-section>-->
        </q-item>
        </div>

        <div v-else>
          <q-item class="q-py-lg">
            <span>
              No hay objetivos sin movimientos actualmente
            </span>
          </q-item>
        </div>

        <div>
          <q-item>
            <q-item-section class="text-accent">
              Nuevo objetivo
            </q-item-section>

            <q-item-section side>
              <q-btn round flat icon="mdi-plus-circle" color="warning" @click="nuevoObjetivo()" />
            </q-item-section>
          </q-item>
        </div>
      </q-list>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  name: 'Objetivos',
  props: {
    dialog: {
      type: Boolean,
      default: false
    },
    objetivos: {
      type: Array,
      default: () => []
    }
  },
  methods: {
    close () {
      this.$emit('update:dialog', false)
    },
    vincularObjetivo (objetivo) {
      this.$emit('vincularObjetivo', objetivo)
    },
    eliminarObjetivo (objetivo) {
      this.$emit('eliminarObjetivo', objetivo)
    },
    nuevoObjetivo () {
      this.$emit('nuevoObjetivo')
    },
    editarObjetivo (objetivo) {
      this.$emit('editarObjetivo', objetivo)
    }
  }
}
</script>

<style scoped>
.card_container {
  min-width: 300px;
}
.list_container {
  max-height: 350px;
  overflow-x: hidden;
  overflow-y: auto;
}
</style>
