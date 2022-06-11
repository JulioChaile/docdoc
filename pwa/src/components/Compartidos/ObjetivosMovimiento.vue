<template>
  <q-dialog v-model="dialog" persistent>
    <q-card>

      <q-list highlight>
        <q-item header class="bg-black text-white q-subheading">
          <q-item-section>
            <span class="text-body">
              Objetivo del movimiento
            </span>
          </q-item-section>
          <q-item-section side>
            <q-btn round icon="mdi-close" @click="close()" />
          </q-item-section>
        </q-item>

        <q-item>
          <q-item-section>
            <span>
              {{ objetivo ? objetivo.Objetivo : 'Sin datos' }}
            </span>
          </q-item-section>

          <q-item-section side>
            <q-btn round flat icon="mdi-pencil" color="primary">
              <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                Editar objetivo
              </q-tooltip>
            </q-btn>
          </q-item-section>

          <q-item-section side>
            <q-btn round flat icon="mdi-close-circle" color="red" @click="desasociarObjetivo()">
              <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                Desasociar objetivo
              </q-tooltip>
            </q-btn>
          </q-item-section>
        </q-item>
        <div style="display: flex; justify-content: flex-end; margin-right: 40px">
          <q-btn
            flat
            color="accent"
            label="Ver movimientos"
            @click="modalMovimientosDeObjetivo = true"
          />
          <q-btn flat color="primary" label="Cerrar" @click="close()" />
        </div>

      </q-list>
      <!-- Ver Movimientos de un Objetivo -->
      <q-dialog v-model="modalMovimientosDeObjetivo" v-if="modalMovimientosDeObjetivo">
        <q-card>
          <q-list highlight>
            <q-item style="background-color: black;">
              <span
                class="q-subheading"
                style="color:white;"
              >Movimientos del objetivo "{{ objetivo.Objetivo }}"</span>
            </q-item>
            <q-item
              v-for="m in movimientosObjetivo"
              :key="m.IdMovimientoCaso"
              style="display:flex; align-items:center;"
            >
              <q-icon name="assignment" :color="m.Color" size="sm" />
              <q-item-label>{{m.Detalle}}</q-item-label>
              <div
                v-if="m.FechaRealizado"
                style="display:flex; align-items:center;flex-direction:row;margin-left:auto; justify-content:space-between;"
              >
                <q-item-label>{{diasRestantes(m)}}</q-item-label>
                <q-icon name="check_circle" size="sm" color="positive" style="margin-left:0.5rem;" />
              </div>
              <div
                v-else-if="m.FechaEsperada"
                style="display:flex; align-items:center;flex-direction:row;margin-left:auto; justify-content:space-between;"
              >{{parseDate(m.FechaEsperada)}}</div>
            </q-item>
          </q-list>
          <div style="display: flex; justify-content: flex-end; margin-right: 40px">
            <q-btn flat color="primary" label="Cerrar" @click="modalMovimientosDeObjetivo = false" />
          </div>
        </q-card>
      </q-dialog>
    </q-card>
  </q-dialog>
</template>

<script>
import request from '../../request'
export default {
  name: 'ObjetivosMovimiento',
  props: {
    dialog: {
      type: Boolean,
      default: false
    },
    objetivo: {
      type: Object,
      default: () => {}
    },
    id: {
      type: String || Number,
      default: null
    }
  },
  data () {
    return {
      movimientos: [],
      movimientosObjetivo: [],
      modalMovimientosDeObjetivo: false
    }
  },
  watch: {
    objetivo () {
      this.movimientosObjetivo = []
      this.movimientos.forEach((m) => {
        if (m.Objetivo === this.objetivo.Objetivo) {
          this.movimientosObjetivo.push(m)
        }
      })
      this.movimientosObjetivo.sort(function (m1, m2) {
        if (m1.FechaRealizado > m2.FechaRealizado) {
          return -1
        } else if (m1.FechaRealizado < m2.FechaRealizado) {
          return 1
        } else return 0
      })
    }
  },
  created () {
    request.Get(`/casos/${this.id}/movimientos-realizados`, {}, (r) => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.movimientos = r
      }
    })
  },
  methods: {
    diasRestantes (movimiento) {
      const fecha2 = new Date()
      const year = movimiento.FechaRealizado.split('-')[0]
      const month = movimiento.FechaRealizado.split('-')[1] - 1
      const day = movimiento.FechaRealizado.split('-')[2].split(' ')[0] - 1

      fecha2.setMonth(month)
      fecha2.setFullYear(year)
      fecha2.setDate(day)
      const hoy = new Date()

      var resultado =
        Math.ceil(
          (fecha2.getTime() - hoy.getTime()) / (1000 * 60 * 60 * 24)
        ) + 1
      return resultado * -1
    },
    close () {
      this.$emit('update:dialog', false)
    },
    desasociarObjetivo () {
      this.$emit('desasociarObjetivo', { idMov: this.objetivo.IdMovimientoCaso, idObj: this.objetivo.IdObjetivo, obj: this.objetivo.Objetivo })
      this.close()
    }
  }
}
</script>
