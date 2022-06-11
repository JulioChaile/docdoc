<template>
  <div class="edicion">
    <q-input label="Caratula" v-model="casoRecibido.Caratula"></q-input>
    <q-select
      v-model="Juzgado"
      label="Juzgado"
      :options="opcionesJuzgados"
      @blur="obtenerNominaciones()"
    />
    <q-select v-model="Nominacion" label="Nominación" :options="opcionesNominaciones" />
    <q-select v-model="EstadoCaso" label="Estado del Caso" :options="opcionesEstadoCaso" />
    <q-input label="Número de Expediente" type="tel" v-model="casoRecibido.NroExpediente"></q-input>
    <q-input label="Carpeta" v-model="casoRecibido.Carpeta"></q-input>
    <q-input label="Observaciones" v-model="casoRecibido.Observaciones"></q-input>
    <div style="padding-top:30px; text-align:right">
      <q-btn color="primary" @click="editarCaso()">Guardar</q-btn>
      <q-btn flat @click="$emit('cancelar-edicion')">Cancelar</q-btn>
    </div>
  </div>
</template>

<script>
import request from '../request'
import auth from '../auth'
import { Notify } from 'quasar'
export default {
  data () {
    return {
      EstadosCaso: [],
      Nominaciones: [],
      Juzgados: [],
      Juzgado: {
        label: this.casoRecibido.Juzgado,
        value: this.casoRecibido.IdJuzgado
      },
      Nominacion: {
        label: this.casoRecibido.Nominacion,
        value: this.casoRecibido.IdNominacion
      },
      EstadoCaso: {
        label: this.casoRecibido.EstadoCaso,
        value: this.casoRecibido.IdEstadoCaso
      }
    }
  },
  props: ['casoRecibido'],
  computed: {
    opcionesEstadoCaso () {
      let result = []
      if (this.EstadosCaso && this.EstadosCaso.length) {
        result = this.EstadosCaso.map((e) => ({
          label: e.EstadoCaso,
          value: e.IdEstadoCaso
        }))
      }
      return result
    },
    opcionesJuzgados () {
      let result = []
      if (this.Juzgados && this.Juzgados.length) {
        result = this.Juzgados.map((e) => ({
          label: e.Juzgado,
          value: e.IdJuzgado
        }))
      }
      return result
    },
    opcionesNominaciones () {
      let result = []
      if (this.Nominaciones && this.Nominaciones.length) {
        result = this.Nominaciones.map((e) => ({
          label: e.Nominacion,
          value: e.IdNominacion
        }))
      }
      return result
    }
  },
  created () {
    request.Get(
      `/estudios/${auth.UsuarioLogueado.IdEstudio}/estados-caso`,
      {},
      (t) => {
        if (!t.Error) {
          this.EstadosCaso = t
        }
      }
    )
    request.Get('/juzgados?IncluyeBajas=N', {}, (r) => {
      if (r.Error) {
        Notify.create(r.Error)
      } else {
        this.Juzgados = r
      }
    })
    this.obtenerNominaciones()
  },
  methods: {
    editarCaso () {
      var caso = {
        IdNominacion: this.Nominacion.value,
        IdJuzgado: this.Juzgado.value,
        IdEstadoCaso: this.EstadoCaso.value,
        Caratula: this.casoRecibido.Caratula,
        NroExpediente: this.casoRecibido.NroExpediente,
        Carpeta: this.casoRecibido.Carpeta,
        Observaciones: this.casoRecibido.Observaciones,
        IdCompetencia: this.casoRecibido.IdCompetencia,
        IdEstadoAmbitoGestion: this.casoRecibido.IdEstadoAmbitoGestion,
        IdCaso: this.casoRecibido.IdCaso,
        IdOrigen: this.casoRecibido.IdOrigen,
        IdTipoCaso: this.casoRecibido.IdTipoCaso
      }
      this.casoRecibido.Juzgado = this.opcionesJuzgados.filter(
        (o) => o.value === this.Juzgado.value
      )[0].label
      if (
        this.opcionesEstadoCaso.filter(
          (o) => o.value === this.casoRecibido.IdEstadoCaso
        )[0]
      ) {
        this.casoRecibido.EstadoCaso = this.opcionesEstadoCaso.filter(
          (o) => o.value === this.EstadoCaso.value
        )[0].label
      }
      if (this.Nominaciones && this.Nominaciones.length) {
        this.casoRecibido.Nominacion = this.opcionesNominaciones.filter(
          (o) => o.value === this.Nominacion.value
        )[0].label
      } else {
        caso.IdNominacion = ''
      }
      request.Put(`/casos/${this.casoRecibido.IdCaso}`, caso, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.$emit('edicionTerminada')
        }
      })
    },
    obtenerNominaciones () {
      this.Nominaciones = []
      request.Get(
        `/nominaciones?IdsJuzgado=${JSON.stringify([
          String(this.Juzgado.value)
        ])}&IncluyeBajas=N`,
        {},
        (n) => {
          if (n.Error) {
            Notify.create(n.Error)
          } else {
            this.Nominaciones = n[[String(this.Juzgado.value)]]
          }
        }
      )
    }
  }
}
</script>

<style lang="stylus">
.q-subheading {
  font-size: 18px;
}

.edicion {
  margin: 2rem;
  width: 300px;
}

.docdoc-title {
  font-size: 15px;
  text-align: left;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: flex-start;
}

span.q-title {
  color: $primary;
}
</style>
