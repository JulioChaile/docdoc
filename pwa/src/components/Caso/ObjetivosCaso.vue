<template>
  <q-card>
    <q-item
      style="background-color: black; color:white; display:flex;align-items:center;"
    >
      <q-icon color="white" name="timeline" size="sm" />
      <span class="q-subheading" style="margin-left:1rem;">Objetivos del Caso</span>
    </q-item>
    <q-separator />
    <q-dialog v-model="altaObjetivosModal">
      <q-card>
        <q-item
          style="background-color:black; display:flex; align-items:center; color:white;"
        >
          <q-icon color="white" name="timeline" size="sm" style="margin-right: 1rem" />
          <span class="q-subheading">Nuevo Objetivo</span>
        </q-item>
        <q-separator />
        <q-item>
          <q-input v-model="nuevoObjetivo.Objetivo" label="Nombre del objetivo" />
        </q-item>
        <q-card-actions :align="'right'">
          <q-btn flat color="primary" label="Aceptar" @click="crearNuevoObjetivo" />
          <q-btn
            flat
            color="primary"
            label="Cancelar"
            @click="altaObjetivosModal = false"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else-if="Objetivos.length">
      <q-item
        v-for="objetivo in Objetivos"
        :key="objetivo.IdObjetivo"
        style="padding: 15px;"
      >
        <q-item-section color="primary" style="margin-right: 5px;" icon="label" />
        <q-item-label header>{{ objetivo.Objetivo }}</q-item-label>
        <q-btn
          icon="edit"
          color="primary"
          flat
          rounded
          outline
          @click="edicionObjetivo(objetivo)"
        >
          <q-tooltip
            anchor="bottom middle"
            self="top middle"
            :offset="[10, 0]"
          >Editar Objetivo</q-tooltip>
        </q-btn>
        <q-dialog v-model="editarObjetivo">
          <q-card>
            <q-item
              style="background-color:black; display:flex; align-items:center; color:white;"
            >
              <q-icon
                color="white"
                name="timeline"
                size="sm"
                style="margin-right: 1rem"
              />
              <span class="q-subheading">Editar Objetivo</span>
            </q-item>
            <q-separator />
            <q-item>
              <q-input
                v-model="objetivoEditar.Objetivo"
                type="textarea"
                rows="1"
                :max-height="50"
                label="Objetivo"
                style="margin: 2rem"
              />
            </q-item>
            <q-card-actions :align="'right'">
              <q-btn flat color="primary" @click="finalizarEdicionObjetivo()">Guardar</q-btn>
              <q-btn flat @click="editarObjetivo = false">Cancelar</q-btn>
            </q-card-actions>
          </q-card>
        </q-dialog>
        <q-btn
          icon="delete"
          color="primary"
          flat
          rounded
          outline
          @click="borrarObjetivo(objetivo)"
        >
          <q-tooltip
            anchor="bottom middle"
            self="top middle"
            :offset="[10, 0]"
          >Eliminar Objetivo</q-tooltip>
        </q-btn>
        <q-dialog v-model="mostrandoModalBorrarObjetivo" prevent-close>
          <q-card style="padding:1rem;">
              <span class="text-h6">
                Eliminar Objetivo
              </span>
              <span>
                <p>
                  Al eliminar un objetivo, este no podrá ser recuperado en el futuro.
                  ¿Está seguro que desea eliminar este objetivo?
                </p>
              </span>
              <div style="float:right;">
                <q-btn
                  color="primary"
                  label="Eliminar"
                  @click="finalizarBorradoObjetivo()"
                />
                <q-btn
                  flat
                  label="Cancelar"
                  @click="mostrandoModalBorrarObjetivo = false"
                />
              </div>
          </q-card>
        </q-dialog>
      </q-item>
    </div>
    <q-item v-else>
      <q-item-label>No se asignaron objetivos al caso</q-item-label>
    </q-item>
    <div style="padding-top:20px; padding-left:20px; float: right">
      <q-btn
          rounded
          color="accent"
          icon="add"
          style="margin-top:10px; margin-bottom:20px; margin-right: 10px;"
          label="Nuevo Objetivo"
          @click="altaObjetivosModal = true"
      />
      <q-btn
          flat
          @click="$emit('cerrar')"
          style="margin-top:10px; margin-bottom:20px; margin-right: 10px;"
      >Cancelar</q-btn>
  </div>
  </q-card>
</template>

<script>
import Loading from '../../components/Loading'
import request from '../../request'
import { Notify } from 'quasar'
export default {
  name: 'ObjetivosCaso',
  components: {
    Loading
  },
  data () {
    return {
      loading: true,
      altaObjetivosModal: false,
      nuevoObjetivo: {
        Objetivo: '',
        IdCaso: this.IdCaso
      },
      Objetivos: [],
      objetivoEditar: {},
      editarObjetivo: false,
      mostrandoModalBorrarObjetivo: false,
      objetivoBorrar: {}
    }
  },
  props: [ 'IdCaso' ],
  created () {
    request.Get(`/objetivos?IdsCaso=${JSON.stringify([this.IdCaso])}`, {}, r => {
      if (!r.Error) {
        this.Objetivos = r[this.IdCaso]
        this.loading = false
      }
    }
    )
  },
  methods: {
    crearNuevoObjetivo () {
      request.Post('/objetivos', this.nuevoObjetivo, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.nuevoObjetivo.IdObjetivo = r.IdObjetivo
          this.Objetivos.push(Object.assign({}, this.nuevoObjetivo))
          this.nuevoObjetivo.Objetivo = ''
          this.nuevoObjetivo.IdObjetivo = ''
          this.altaObjetivosModal = false
        }
      })
    },
    edicionObjetivo (objetivo) {
      this.objetivoEditar = objetivo
      this.editarObjetivo = true
    },
    finalizarEdicionObjetivo () {
      let objetivo = {
        Objetivo: this.objetivoEditar.Objetivo
      }
      this.Objetivos.forEach(o => {
        if (o.IdObjetivo === this.objetivoEditar.IdObjetivo) {
          o.Objetivo = this.objetivoEditar.Objetivo
        }
      })
      request.Put(`/objetivos/${this.objetivoEditar.IdObjetivo}`, objetivo, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.editarObjetivo = false
          this.objetivoEditar = {}
        }
      }
      )
    },
    borrarObjetivo (objetivo) {
      this.mostrandoModalBorrarObjetivo = true
      this.objetivoBorrar = objetivo
    },
    finalizarBorradoObjetivo () {
      request.Delete('/objetivos/', this.objetivoBorrar.IdObjetivo, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.mostrandoModalBorrarObjetivo = false
          this.Objetivos.splice(
            this.Objetivos.indexOf(this.objetivoBorrar),
            1
          )
        }
      })
    }
  }
}
</script>
