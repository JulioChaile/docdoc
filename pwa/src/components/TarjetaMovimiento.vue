<template>
    <div class="tarjeta">
      <q-card style="background-color: white;">
        <!-- Asignado -->
        <q-item v-if="!tarjetaCaso" :class="`bg-${this.movimiento.Color}`" :style="this.movimiento.Color === 'warning' ? 'display:flex; align-items:center;' : 'display:flex; align-items:center;color:white'">
          <!--
          <q-item-side icon="lens" :class="`text-${this.movimiento.Color}`">
          </q-item-side>
          -->
          <q-item-section style="font-weigth: 900px">
            <b>{{ movimiento.Caratula }}</b>
          </q-item-section>
          <q-item-section :style="this.movimiento.Color === 'warning' ? 'color:black; text-align:right;' : 'color:white; text-align:right;'">
            {{ movimiento.UsuarioResponsable}}
          </q-item-section>
        </q-item>
        <q-separator />
        <!-- Detalle -->
        <q-card-section style="display:flex; justify-content:space-between; align-items:center;">
        <div>
          {{movimiento.Objetivo ? movimiento.Objetivo : 'Sin objetivo'}} | <b>{{ movimiento.Detalle }} {{ movimiento.Acciones.length > 0 ? ('- ' + movimiento.Acciones[0].Accion) : ''}}</b>
          <q-item style="padding:0">
            <q-item-label >
              <q-item-label caption style="display:flex; justify-content:flex-start; margin-bottom: 10px">
                <div style="margin-right:0.5rem;"> Cargado hace {{calcularAntiguedad(movimiento.FechaAlta)}} días</div>
                |
                <template v-if="movimiento.FechaEdicion">
                  <div style="margin-left:0.5rem; margin-right:0.5rem;"> Editado hace {{calcularAntiguedad(movimiento.FechaEdicion)}} días</div>
                  |
                </template>
                <div v-if="!movimiento.FechaRealizado" style="margin-left:0.5rem;">
                  <div v-if="movimiento.FechaEsperada"> A realizar hasta el {{ FechaMostrar }}</div>
                  <div v-else>Sin fecha esperada</div>
                </div>
                <div v-else style="margin-left:0.5rem;">
                  <div>Realizado el {{ parseDate(movimiento.FechaRealizado) }}</div>
                </div>
              </q-item-label>
            </q-item-label>
          </q-item>
        </div>
        <div style="display:flex; flex-direction:row;justify-content:right;align-items:center;">
          <div v-if="tarjetaCaso" :class="`bg-${this.movimiento.Color}`" style="margin-right:16px;padding:5px; border-radius:50%; color:white;">
            {{usuarioResponsable()}}
            <q-tooltip
              anchor="top middle"
              self="bottom middle"
              :offset="[10, 0]"
            >
              Responsable: {{movimiento.UsuarioResponsable ? movimiento.UsuarioResponsable : 'Sin usuario responsable.' }}
            </q-tooltip>
          </div>
          <q-avatar
            v-if="!movimiento.FechaRealizado"
            size="lg"
            :color="this.movimiento.Color"
            text-color="white"
          >
            <span style="font-weight: bold">{{ diasRestantes(movimiento.FechaEsperada, true) }}</span>
            <q-tooltip
              anchor="top middle"
              self="bottom middle"
              :offset="[10, 0]"
            >
              {{ diasRestantes(movimiento.FechaEsperada) }}
            </q-tooltip>
          </q-avatar>
          <q-btn
            icon="photo_camera"
            flat
            rounded
            outline
            size="lg"
            :color="this.movimiento.Color"
            @click="mostrandoModalMultimedia = true"
            v-if="movimiento.Multimedia"
          >
            <q-tooltip
              anchor="top middle"
              self="bottom middle"
              :offset="[10, 0]"
            >
              Ver Multimedia
            </q-tooltip>
          </q-btn>
          <q-btn
            icon="assignment"
            flat
            rounded
            outline
            size="lg"
            :color="this.movimiento.Color"
          >
            <q-tooltip
              anchor="top middle"
              self="bottom middle"
              :offset="[10, 0]"
            >
              Opciones
            </q-tooltip>
            <q-menu>
              <q-list style="width:15rem;" separator link>
                <!-- Ir Caso -->
                <q-item @click.native="abrirCaso(movimiento.IdCaso)" clickable v-close-popup style="display:flex; align-items:center;">
                  <q-icon size="xs" :color="this.movimiento.Color" name="assignment"/>
                  <span style="margin-left:1rem;text-align:left;">Ir al Caso</span>
                </q-item>
                <!-- Asociar Objetivo -->
                <q-item @click.native="cargarObjetivosAsociar()" clickable v-close-popup v-if="!movimiento.Objetivo" style="display:flex; align-items:center;">
                  <q-icon size="xs" :color="this.movimiento.Color" name="timeline"/>
                  <span style="margin-left:1rem;text-align:left;">Asociar Objetivo</span>
                </q-item>
                <!-- Desasociar Objetivo -->
                <q-item clickable v-close-popup @click.native="mostrandoModalDesasociar = true" v-if="movimiento.Objetivo" style="display:flex; align-items:center;">
                  <q-icon size="xs" :color="this.movimiento.Color" name="trending_down" />
                  <span style="margin-left:1rem;text-align:left;">Desasociar Objetivo</span>
                </q-item>
                <!-- Realizar -->
                <q-item clickable v-close-popup @click.native="mostrandoModalRealizar = true" v-if="!movimiento.FechaRealizado" style="display:flex; align-items:center;">
                  <q-icon size="xs" :color="this.movimiento.Color" name="done" />
                  <span style="margin-left:1rem;text-align:left;">Marcar como Realizado</span>
                </q-item>
                <!-- Desrealizar -->
                <q-item clickable v-close-popup @click.native="mostrandoModalDesrealizar = true" v-if="movimiento.FechaRealizado" style="display:flex; align-items:center;">
                  <q-icon size="xs" :color="this.movimiento.Color" name="cancel" />
                  <span style="margin-left:1rem;text-align:left;">Marcar como no Realizado</span>
                </q-item>
                <!-- Editar -->
                <q-item clickable v-close-popup @click.native="editarMovimiento()" style="display:flex; align-items:center;">
                  <q-icon size="xs" :color="this.movimiento.Color" name="edit" />
                  <span style="margin-left:1rem;text-align:left;">Editar</span>
                </q-item>
                <!-- Eliminar -->
                <q-item clickable v-close-popup @click.native="mostrandoModalBorrar = true" style="display:flex; align-items:center;">
                  <q-icon size="xs" :color="this.movimiento.Color" name="delete" />
                  <span style="margin-left:1rem;text-align:left;">Eliminar</span>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
        </div>
      </q-card-section>
        <q-dialog
            v-model="mostrandoModalMultimedia"
          >
            <q-card style="background-color: white; -webkit-box-shadow:none; box-shadow:none; ">
              <q-list highlight>
                <q-item-label header>Multimedia</q-item-label>
                <q-item style="display:flex; flex-direction:column; box-shadow:none;" v-for="(m, i) in multimedia" :key="m.URL" @click="descargarMultimedia(m)">
                  <q-item-label
                    :label="`Archivo ${i+1}`"
                    label-lines="1"
                    style="padding: 10px"
                  />
                  <a :href="`https://io.docdoc.com.ar/api/multimedia?file=${m.URL}&download=true`" :download="`archivo-${i+1}`">
                    <audio v-if="m.Tipo === 'A'" controls>
                      <source :src="`https://io.docdoc.com.ar/api/multimedia?file=${m.URL}`">
                    </audio>
                    <video v-if="m.Tipo === 'V'" width="320" height="240" :src="`https://io.docdoc.com.ar/api/multimedia?file=${m.URL}`" controls></video>
                    <img v-else-if="m.Tipo === 'I'" class="img--multimedia" :src="`https://io.docdoc.com.ar/api/multimedia?file=${m.URL}`">
                    <span v-else>
                      Formato desconocido, haga click para descargar
                    </span>
                  </a>
                </q-item>
              </q-list>
              <q-btn style="float:right;" flat label="Cerrar" @click="mostrandoModalMultimedia= false" />
            </q-card>
          </q-dialog>
          <q-dialog
            v-model="mostrandoModalEditar"
            no-backdrop-dismiss
            no-esc-dismiss
          >
          <q-card class="relative-position" :style="isMobile() ? 'background:white; width:100%' : 'background:white; width:50%'">
            <q-icon
              name="close"
              class="absolute-right cursor-pointer"
              color="negative"
              size="lg"
              @click="cancelarEdicion()"
            />
            <EditarMovimiento
              v-if="mostrandoModalEditar"
              :movimiento="this.movimiento"
              @cancelarEdicion="cancelarEdicion()"
              @edicionTerminada="edicionTerminada()"
            />
          </q-card>
          </q-dialog>
          <!-- Eliminar
          <q-btn
            icon="delete"
            color="primary"
            flat
            rounded
            outline
            @click="mostrandoModalBorrar = true"
          >
            <q-tooltip
              anchor="bottom middle"
              self="top middle"
              :offset="[10, 0]"
            >
              Eliminar
            </q-tooltip>
          </q-btn>
           -->
      </q-card>
      <q-dialog
        v-model="mostrandoModalBorrar"
        prevent-close
      >
        <q-card style="padding:1rem;">
          <span class="text-h6">Eliminar Movimiento</span>
          <span>
            <p>
              Al eliminar un movimiento, este no podrá ser recuperado en el futuro.
              ¿Está seguro que desea eliminar este movimiento?
            </p>
          </span>
          <div style="float:right;">
            <q-btn color="primary" label="Eliminar" @click="bajaMovimiento()" />
            <q-btn flat label="Cancelar" @click="mostrandoModalBorrar = false" />
          </div>
        </q-card>
      </q-dialog>
      <q-dialog
        v-model="mostrandoModalRealizar"
        prevent-close
      >
      <q-card style="padding:1rem">
        <span class="text-h6">Marcar como realizado</span>
        <span>
          <p>
            ¿Desea marcar el movimiento como realizado?
          </p>
        </span>
        <div style="float:right;">
          <q-btn color="primary" label="Aceptar" @click="realizarMovimiento()" />
          <q-btn flat label="Cancelar" @click="mostrandoModalRealizar = false" />
        </div>
        </q-card>
      </q-dialog>
      <q-dialog
        v-model="mostrandoModalAsociar"
        prevent-close
      >
        <q-card style="padding:1rem;">
          <span class="text-h6">Asociar Objetivo</span>
          <span>
            <p>
              <q-select
                label="Seleccione un Objetivo"
                radio
                v-model="movimiento.IdObjetivo"
                :options="opcionesObjetivos"
                v-if="movimiento.ObjetivosCaso && movimiento.ObjetivosCaso.length > 0"
              />
              <span v-if="!movimiento.ObjetivosCaso || movimiento.ObjetivosCaso.length === 0">El caso no posee objetivos</span>
            </p>
          </span>
          <div style="float:right;">
            <q-btn color="primary" label="Aceptar" @click="asociarObjetivo()" />
            <q-btn flat label="Cancelar" @click="mostrandoModalAsociar = false" />
          </div>
        </q-card>
      </q-dialog>
      <q-dialog
        v-model="mostrandoModalDesasociar"
        prevent-close
      >
      <q-card style="padding:1rem;">
        <span class="text-h6">Desasociar Objetivo</span>
        <span>
          <p>
            ¿Desea desasociar el movimiento del objetivo "{{ movimiento.Objetivo }}"?
          </p>
        </span>
        <div style="float:right;">
          <q-btn color="primary" label="Aceptar" @click="desasociarObjetivo()" />
          <q-btn flat label="Cancelar" @click="mostrandoModalDesasociar = false" />
        </div>
      </q-card>
      </q-dialog>
      <q-dialog
        v-model="mostrandoModalDesrealizar"
        prevent-close
      >
      <q-card style="padding:1rem;">
        <span class="text-h6">Marcar como no realizado</span>
        <span>
          <p>
            ¿Desea marcar el movimiento como no realizado?
          </p>
        </span>
        <div style="float:right;">
          <q-btn color="primary" label="Aceptar" @click="desrealizarMovimiento()" />
          <q-btn flat label="Cancelar" @click="mostrandoModalDesrealizar = false" />
        </div>
        </q-card>
      </q-dialog>
    </div>
</template>

<script>
import request from '../request'
import moment from 'moment'
import EditarMovimiento from '../components/EditarMovimiento.vue'
import { Notify, QDialog } from 'quasar'

export default {
  components: {
    QDialog,
    EditarMovimiento
  },
  props: ['movimiento', 'edicion', 'tarjetaCaso', 'tarea'],
  data () {
    return {
      mostrandoModalMultimedia: false,
      mostrandoModalBorrar: false,
      mostrandoModalRealizar: false,
      mostrandoModalDesrealizar: false,
      mostrandoModalEditar: false,
      mostrandoModalCancelarEdicion: false,
      mostrandoModalAsociar: false,
      mostrandoModalDesasociar: false,
      TiposMov: [],
      TipoMov: null,
      popover: false,
      FechaRealizado: this.movimiento.FechaRealizado,
      FechaEsperada: this.movimiento.FechaEsperada,
      FechaMostrar: '',
      UsuariosEstudio: [],
      editado: false,
      objetivoSeleccionado: 0,
      Color: '',
      coloresDocDoc: [
        {
          label: 'Rojo',
          value: 'negative'
        },
        {
          label: 'Azul',
          value: 'primary'
        },
        {
          label: 'Naranja',
          value: 'warning'
        },
        {
          label: 'Verde',
          value: 'positive'
        }
      ]
    }
  },
  computed: {
    opcionesObjetivos () {
      let result = []
      if (this.movimiento.ObjetivosCaso && this.movimiento.ObjetivosCaso.length) {
        this.movimiento.ObjetivosCaso.forEach(o => {
          if (o) {
            result.push({ label: o.Objetivo, value: o.IdObjetivo })
          }
        })
      }
      return result
    },
    multimedia () {
      if (typeof this.movimiento.Multimedia === 'string') {
        return JSON.parse(this.movimiento.Multimedia)
      }
      return this.movimiento.Multimedia
    }
  },
  created () {
    this.hoy = new Date()
    var dd = this.hoy.getDate()
    var mm = this.hoy.getMonth() + 1
    var yyyy = this.hoy.getFullYear()
    if (dd < 10) {
      dd = '0' + dd
    }
    if (mm < 10) {
      mm = '0' + mm
    }
    this.hoy = mm + '/' + dd + '/' + yyyy
    this.FechaRealizado = dd + '/' + mm + '/' + yyyy
    this.FechaMostrar = this.parseDate(this.movimiento.FechaEsperada)
    switch (this.movimiento.Color) {
      case 'primary':
        this.movimiento.InicialGestion = 'G'
        this.Color = 'primary'
        break
      case 'warning':
        this.movimiento.InicialGestion = 'Ex'
        this.Color = 'warning'
        break
      case 'positive':
        this.movimiento.InicialGestion = 'F'
        this.Color = 'positive'
        break
      default:
        this.Color = 'negative'
        this.movimiento.InicialGestion = 'P'
    }
  },
  methods: {
    formatearFecha (fecha) {
      if (fecha !== null) {
        return fecha.split('T')[0]
      } else return null
    },
    parseDateTime (fecha) {
      if (fecha !== null) {
        const fh = fecha.split(' ')
        const amd = fh[0].split('-')
        const hms = fh[1].split(':')
        return `${amd[2]}/${amd[1]}/${amd[0]} ${hms[0]}:${hms[1]}`
      } else return null
    },
    parseDate (fecha) {
      if (fecha === this.hoy || this.editado) {
        return fecha
      } else if (fecha !== null) {
        return this.parseDateTime(fecha).split(' ')[0]
      } else return null
    },
    isMobile () {
      if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        return true
      } else {
        return false
      }
    },
    abrirCaso (id) {
      let routeData = this.$router.resolve({ path: `/Caso?id=${id}` })
      window.open(routeData.href, '_blank')
    },
    calcularAntiguedad (fecha) {
      if (!fecha) { return '-' }
      const fecha2 = new Date()
      var year = fecha.split('-')[0]
      var month = fecha.split('-')[1] - 1
      var day = fecha.split('-')[2].split(' ')[0] - 1
      fecha2.setMonth(month)
      fecha2.setFullYear(year)
      fecha2.setDate(day)
      const hoy = new Date()
      var resultado = Math.ceil((fecha2.getTime() - hoy.getTime()) / (1000 * 60 * 60 * 24)) + 1
      return (resultado * -1)
    },
    editarMovimiento () {
      this.mostrandoModalEditar = true
    },
    usuarioResponsable () {
      if (!this.movimiento.UsuarioResponsable) { return 'XX' }
      let ap = this.movimiento.UsuarioResponsable[0]
      let nom = ''
      for (let i = 0; i < this.movimiento.UsuarioResponsable.length; i++) {
        if (this.movimiento.UsuarioResponsable[i] === ',') {
          nom = this.movimiento.UsuarioResponsable[i + 2]
        }
      }
      return `${nom}${ap}`
    },
    cargarObjetivosAsociar () {
      this.mostrandoModalAsociar = true
    },
    cancelarEdicion () {
      this.mostrandoModalEditar = false
    },
    asociarObjetivo () {
      if (this.movimiento.ObjetivosCaso && this.movimiento.ObjetivosCaso.length > 0) {
        request.Post(`/movimientos/${this.movimiento['IdMovimientoCaso']}/asociar-objetivo/${this.movimiento.IdObjetivo.value}`, {}, r => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.movimiento.IdObjetivo = this.movimiento.IdObjetivo
            this.movimiento.Objetivo = this.opcionesObjetivos.filter(o => o.value === this.movimiento.IdObjetivo.value)[0].label
          }
          this.mostrandoModalAsociar = false
        })
      }
      this.mostrandoModalAsociar = false
    },
    desasociarObjetivo () {
      request.Put(`/movimientos/${this.movimiento['IdMovimientoCaso']}/desasociar-objetivo/${this.movimiento.IdObjetivo}`, {}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.movimiento.IdObjetivo = null
          this.movimiento.Objetivo = null
        }
        this.mostrandoModalDesasociar = false
      })
    },
    bajaMovimiento: function () {
      if (this.movimiento.Multimedia) {
        this.$q.notify('No se puede eliminar movimientos con multimedia')
      } else {
        request.Delete(`/movimientos/`, this.movimiento.IdMovimientoCaso, r => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.$emit('borrar', this.movimiento)
          }
        })
      }
      this.mostrandoModalBorrar = false
    },
    realizarMovimiento () {
      request.Put(`/movimientos/${this.movimiento['IdMovimientoCaso']}/realizar`, {}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.editado = true
          this.movimiento.FechaRealizado = this.FechaRealizado
          this.mostrandoModalRealizar = false
        }
      })
    },
    desrealizarMovimiento () {
      request.Put(`/movimientos/${this.movimiento['IdMovimientoCaso']}/desrealizar`, {}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.editado = false
          this.movimiento.FechaRealizado = null
          this.mostrandoModalDesrealizar = false
        }
      })
    },
    edicionTerminada () {
      this.editado = true
      this.mostrandoModalEditar = false
      this.FechaMostrar = new Date(this.movimiento.FechaEsperada)
      var dd = this.FechaMostrar.getDate() + 1
      var mm = this.FechaMostrar.getMonth() + 1
      var yyyy = this.FechaMostrar.getFullYear()
      if (dd < 10) {
        dd = '0' + dd
      }
      if (mm < 10) {
        mm = '0' + mm
      }
      this.FechaMostrar = dd + '/' + mm + '/' + yyyy
    },
    diasRestantes (fecha, soloNumeros) {
      if (!fecha) { return '-' }
      /*
      this.fecha2 = new Date()
      var year = fecha.split('-')[0]
      var month = fecha.split('-')[1] - 1
      var day = fecha.split('-')[2].split(' ')[0] - 1
      this.fecha2.setMonth(month)
      this.fecha2.setFullYear(year)
      this.fecha2.setDate(day)
      this.hoy = new Date()
      var resultado =
        Math.ceil(
          (this.fecha2.getTime() - this.hoy.getTime()) / (1000 * 60 * 60 * 24)
        ) + 1
      */
      fecha = fecha.split(' ')[0]
      const resultado = moment(moment(fecha).format('YYYY-MM-DD')).diff(moment().format('YYYY-MM-DD'), 'days')
      if (resultado > 0) {
        return soloNumeros ? (resultado) * -1 : 'Vence en ' + resultado + (resultado === 1 ? ' día' : ' días')
      } else if (resultado < 0) {
        return soloNumeros ? resultado * -1 : 'Vencido hace ' + resultado * -1 + (resultado === 1 ? ' día' : ' días')
      } else {
        return soloNumeros ? 0 : 'Vence hoy'
      }
    }
  }
}
</script>

<style lang="stylus">
.q-subheading {
  font-size: 18px;
}
.tarjeta {
  margin: 1rem;
  width: 90vw;
}
.edicion {
  margin: 2rem;
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
.img--multimedia {
  height: auto;
  width: auto;
  max-width: 320px;
  max-height: 240px;
}
</style>
