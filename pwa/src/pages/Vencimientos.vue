<template>
  <q-page id="page_container_vencimientos">
    <div class="shadow-2" style="margin-bottom: 2em; border-radius: 10px; background: white;">
      <p class="titulo bg-primary">Casos</p>
      <div v-if="cargandoCasos" style="margin:auto;">
        <Loading />
      </div>
      <div v-else style="padding: 0 1rem 1rem 1rem">
        Tu estudio tiene {{Casos.length}} {{Casos.length === 1 ? 'caso cargado' : 'casos cargados'}}.
        Toca
        <a
          @click="verCasos()"
          class="text-primary"
          style="cursor:pointer; font-weight:700;"
        >aquí</a>
        para {{Casos.length === 1 ? 'verlo' : 'verlos'}}.
      </div>
    </div>
    <div class="row" style="display: flex; justify-content: space-between;">
      <div
        class="col-12 shadow-2"
        style="margin-bottom: 2em; border-radius: 10px; background: white;"
      >
        <p class="titulo bg-primary">Últimos Movimientos:</p>
        <div style="text-align: center; margin-bottom: 2rem; padding-top: 1rem">
          Ver Perentorios
          <q-toggle
            v-model="filtrar"
            @click="TipoMov = ['Todos']; EstadoAmbito = ['Todos']"
            color="primary"
            style="margin-right: 1rem; margin-left: 1rem"
          />Ver Gestión Estudio/Externa
        </div>
        <div class="contenidoTarjeta" v-if="!filtrar">
          <q-select
            v-model="TipoMov"
            stack-label
            label="Tipo de movimiento"
            multiple
            :options="opcionesTiposMov"
          />
          <q-select
            v-model="EstadoAmbito"
            stack-label
            label="Estado de Ambito de Gestión"
            multiple
            :options="opcionesEstadoAmbito"
          />
          <q-select
            v-model="Usuario"
            stack-label
            label="Usuarios"
            multiple
            :options="opcionesUsuarios"
          />
          <br>
          <div v-if="Perentorios.length === 0">
            <div v-if="cargandoMovimientos">
              <Loading />
            </div>
            <div
              v-else
              style="text-align: center; font-style: italic; margin-top: 3rem;"
            >No posee movimientos perentorios</div>
          </div>
          <div v-else>
            <TarjetaTribunales
              v-for="movimiento in filtrarPerentorios"
              :key="movimiento.IdMovimientoCaso"
              :movimiento="movimiento"
              :inicio="true"
              :vistaVenc="true"
              @mostrarObjetivos="mostrarObjetivos(movimiento)"
              @realizarMovimiento="realizarMovimiento(movimiento, null)"
            />
          </div>
        </div>
        <div class="contenidoTarjeta" v-if="filtrar">
          <q-select
            v-model="TipoMov"
            multiple
            :options="opcionesTiposMov"
          />
          <q-select
            v-model="EstadoAmbito"
            stack-label
            label="Estado de Ambito de Gestión"
            multiple
            :options="opcionesEstadoAmbito"
          />
          <q-select
            v-model="Usuario"
            stack-label
            label="Usuarios"
            multiple
            :options="opcionesUsuarios"
          />
          <br>
          <div v-if="GestionEstudio.length === 0">
            <div v-if="cargandoMovimientos">
              <Loading />
            </div>
            <div v-else>No posee movimientos a gestionar por estudio</div>
          </div>
          <div v-else>
            <TarjetaTribunales
              v-for="movimiento in filtrarMovGestion"
              :key="movimiento.IdMovimientoCaso"
              :movimiento="movimiento"
              :inicio="true"
              :vistaVenc="true"
              @mostrarObjetivos="mostrarObjetivos(movimiento)"
              @realizarMovimiento="realizarMovimiento(movimiento, juzgado)"
            />
          </div>
        </div>
      </div>
      <!--
      <div
        :class="isMobile() ? 'col-12 shadow-2' : 'col-md-4 shadow-2'"
        style="margin-bottom: 2em; border-radius: 10px; background: white;"
      >
        <p class="col-12 titulo bg-primary">Notificaciones diarias</p>
        <div class="contenidoTarjeta">
          <iframe
            src="https://notificaciones.docdoc.com.ar"
            frameborder="0"
            style="overflow:hidden;min-height: 500px;width:100%"
            height="100%"
            width="100%"
          ></iframe>
        </div>
      </div>
      -->
      <q-dialog v-model="modalAlta" v-if="modalAlta" no-backdrop-dismiss no-esc-dismiss>
        <q-card style="min-width:400px;">
          <q-item style="background-color: black;">
            <span class="q-subheading" style="color:white;">Nuevo Movimiento</span>
          </q-item>
          <q-card-separator />
          <div style="padding: 0 1rem 1rem 1rem;">
            <div
              style="display:flex; flex-direction:row;justify-content: space-between;margin-top: 20px;"
            >
              <q-field
                label="Caso:"
                stack-label
                :style="movimientoAlta.Objetivo ? 'width:45%' : 'width: 100%'"
              >{{ movimientoAlta.Caso}}</q-field>
              <q-field
                style="width: 50%"
                label="Objetivo: "
                stack-label
                v-if="!alta"
              >{{ movimientoAlta.Objetivo ? movimientoAlta.Objetivo : 'Sin Objetivo'}}</q-field>
            </div>
            <q-input
              v-model="nuevoMovimiento.Detalle"
              label="Detalle del movimiento"
              type="textarea"
              :rows="3"
            />
            <div style="display:flex; justify-content:space-between; align-items:end">
              <q-select
                style="width:47%;"
                v-model="nuevoMovimiento.colorSeleccionado"
                :options="coloresDocDoc"
                label="Estado de Gestión"
              />
              <q-input
                ref="inputFechaEsperada"
                label="Fecha Esperada"
                v-model="nuevoMovimiento.FechaEsperada"
                :rules="['date']"
                style="width:47%;"
              >
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy
                      ref="qDateProxy1"
                      transition-show="scale"
                      transition-hide="scale"
                    >
                      <q-date
                        mask="DD-MM-YYYY"
                        v-model="nuevoMovimiento.FechaEsperada"
                        @input="() => $refs.qDateProxy1.hide()"
                        @change="editarFechaEsperada()"
                      />
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:end;">
              <q-input
                ref="inputFechaAlta"
                label="Fecha de estado de gestión"
                v-model="nuevoMovimiento.FechaAlta"
                :rules="['date']"
                style="width:47%;"
              >
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy
                      ref="qDateProxy2"
                      transition-show="scale"
                      transition-hide="scale"
                    >
                      <q-date
                        mask="DD-MM-YYYY"
                        label="Fecha de estado de gestión"
                        v-model="nuevoMovimiento.FechaAlta"
                        @input="() => $refs.qDateProxy2.hide()"
                        @change="editarFechaEsperada()"
                      />
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
              <q-select
                style="width:47%;"
                v-model="nuevoMovimiento.Responsable"
                :options="opcionesResponsable"
                label="Responsable del movimiento"
              />
            </div>
            <div style="display:flex; justify-content:space-between;align-items:end">
              <q-select
                style="width:47%;"
                v-model="nuevoMovimiento.TipoMov"
                :options="opcionesTipoMov"
                label="Tipo de movimiento"
              />
              <!--q-uploader label="Multimedia" auto-upload multiple style="width:47%" /-->
            </div>
            <div style="padding-top:30px; text-align:right">
              <q-btn color="primary" @click="guardarMovimiento()">Guardar</q-btn>
              <q-btn flat @click="cancelarAlta()">Cancelar</q-btn>
            </div>
          </div>
        </q-card>
      </q-dialog>
      <q-dialog v-model="modalCasos" v-if="!cargandoCasos">
        <q-card>
          <q-item style="background:black;color:white; font-weight: 500;">
            <q-item-side left color="white" icon="description" />
            <span class="q-subheading">Casos</span>
            <q-item-side right style="margin-left:auto;">
              <q-btn flat color="white" icon="close" @click="modalCasos = false" />
            </q-item-side>
          </q-item>
          <div v-for="caso in Casos" :key="caso.IdCaso" style="padding:1rem 1rem 0 0;">
            <TarjetaCaso :caso="caso" v-if="!cargandoCasos && modalCasos" />
          </div>
          <div style="display: flex; justify-content: flex-end; margin-right: 40px">
            <q-btn flat color="primary" label="Cerrar" @click="modalCasos = false" />
          </div>
        </q-card>
      </q-dialog>
      <!-- Ver Objetivos -->
      <q-dialog v-model="modalObjetivos" v-if="modalObjetivos">
        <q-card>
          <q-list highlight>
            <q-item-label
              header
              class="q-subheading"
              style="background:black; color:white;"
            >Objetivos del caso "{{ movimientoMostrarObjetivos.Caso }}"</q-item-label>
            <q-item
              v-for="objetivo in filtrarObjetivos()"
              :key="objetivo.IdObjetivo"
              style="display:flex; align-items:center; justify-content:space-between;"
            >
              <q-item-label>{{ objetivo.Objetivo}}</q-item-label>
              <div>
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
                <q-btn
                  icon="add"
                  color="primary"
                  round
                  size="xs"
                  style="cursor: pointer"
                  @click="asociarObjetivo(objetivo)"
                  v-if="movimientoMostrarObjetivos.Objetivo !== objetivo.Objetivo"
                >
                  <q-tooltip
                    anchor="bottom middle"
                    self="top middle"
                    :offset="[10, 0]"
                  >Asociar Objetivo</q-tooltip>
                </q-btn>
                <q-btn
                  icon="clear"
                  round
                  size="xs"
                  color="primary"
                  style="cursor: pointer"
                  @click="desasociarObjetivo(objetivo)"
                  v-if="movimientoMostrarObjetivos.Objetivo === objetivo.Objetivo"
                >
                  <q-tooltip
                    anchor="bottom middle"
                    self="top middle"
                    :offset="[10, 0]"
                  >Desasociar Objetivo</q-tooltip>
                </q-btn>
              </div>
            </q-item>
          </q-list>
          <q-item
            v-if="!movimientoMostrarObjetivos.Objetivo"
            style="display:flex; justify-content:space-between; align-items:center;"
          >
            <q-item-label class="text-primary">Nuevo Objetivo</q-item-label>
            <q-btn
              icon="add"
              color="warning"
              round
              size="xs"
              style="cursor: pointer"
              @click="modalNuevoObjetivo = true"
            />
          </q-item>
          <div style="display: flex; justify-content: flex-end; margin-right: 40px">
            <q-btn
              flat
              color="accent"
              label="Ver movimientos"
              @click="modalMovimientosDeObjetivo = true"
            />
            <q-btn flat color="primary" label="Cerrar" @click="modalObjetivos = false" />
          </div>
        </q-card>
      </q-dialog>
      <q-dialog v-model="editarObjetivo" v-if="editarObjetivo">
        <q-card>
          <q-item style="background-color:black; display:flex; align-items:center; color:white;">
            <q-icon color="white" name="timeline" size="sm" style="margin-right: 1rem" />
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
      <!-- Ver Movimientos de un Objetivo -->
      <q-dialog v-model="modalMovimientosDeObjetivo" v-if="modalMovimientosDeObjetivo">
        <q-card>
          <q-list highlight>
            <q-item style="background-color: black;" v-if="movimientoMostrarObjetivos.Objetivo">
              <span
                class="q-subheading"
                style="color:white;"
              >Movimientos del objetivo "{{ movimientoMostrarObjetivos.Objetivo }}"</span>
            </q-item>
            <q-item style="background-color: black;" v-else>
              <span class="q-subheading" style="color:white;">Movimientos sin objetivo asociado</span>
            </q-item>
            <q-item
              v-for="m in movimientosObjetivo()"
              :key="m.IdMovimientoCaso"
              style="display:flex; align-items:center;"
            >
              <q-icon name="assignment" :color="m.Color" size="sm" />
              <q-item-label>{{m.Detalle}}</q-item-label>
              <div
                v-if="m.FechaRealizado"
                style="display:flex; align-items:center;flex-direction:row;margin-left:auto; justify-content:space-between;"
              >
                <q-item-label>{{diasRestantes(m, 'realizado')}}</q-item-label>
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
      <!-- Nuevo Objetivo -->
      <q-dialog v-model="modalNuevoObjetivo" v-if="modalNuevoObjetivo">
        <q-card>
          <q-item style="background-color: black; color: white; display:flex; align-items:center;">
            <q-icon color="white" name="timeline" size="sm" style="margin-right:1rem;" />
            <span class="q-subheading">Nuevo Objetivo</span>
          </q-item>
          <q-item>
            <q-input v-model="nuevoObjetivo.Objetivo" label="Nombre del objetivo" />
          </q-item>
          <q-card-actions align="end">
            <q-btn color="primary" label="Aceptar" @click="asociarANuevoObjetivo()" />
            <q-btn flat color="primary" label="Cancelar" @click="modalNuevoObjetivo = false" />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>
  </q-page>
</template>

<script>
import MovimientosPendientes from '../components/MovimientosPendientes.vue'
import request from '../request'
import auth from '../auth'
import Gestionometro from '../components/Gestionometro.vue'
import Loading from '../components/Loading.vue'
import TarjetaTribunales from '../components/TarjetaTribunales.vue'
import TarjetaCaso from '../components/TarjetaCaso.vue'
import barChart from '../components/UI/barChart.vue'
import { Notify, QDialog } from 'quasar'
import moment from 'moment'

export default {
  components: {
    MovimientosPendientes,
    TarjetaTribunales,
    TarjetaCaso,
    Gestionometro,
    Loading,
    QDialog,
    barChart
  },
  name: 'Vencimientos',
  data () {
    return {
      // Data home viejo
      nuevoObjetivo: {},
      modalMovimientosDeObjetivo: false,
      modalNuevoObjetivo: false,
      editarObjetivo: false,
      objetivoEditar: {},
      objetivosMostrar: [],
      movimientoMostrarObjetivos: {},
      modalObjetivos: false,
      Perentorios: [],
      GestionEstudio: [],
      Juzgados: [],
      filtrar: false,
      movimientoAlta: {},
      cargandoMovimientos: true,
      cargandoCasos: true,
      Casos: [],
      alta: false,
      nuevoMovimiento: {
        IdUsuario: 0,
        TiposMov: [],
        TipoMov: 0,
        UsuariosEstudio: [],
        IdEstudio: 0,
        Detalle: '',
        FechaEsperada: '',
        FechaAlta: new Date().toISOString(),
        colorSeleccionado: '',
        Caso: 0,
        Objetivo: ''
      },
      modalAlta: false,
      modalCasos: false,
      coloresDocDoc: [
        {
          label: 'Perentorio',
          value: 'negative'
        },
        {
          label: 'Gestión Estudio',
          value: 'primary'
        },
        {
          label: 'Gestión Externa',
          value: 'warning'
        },
        {
          label: 'Finalizado',
          value: 'positive'
        }
      ],
      TipoMov: ['Todos'],
      TiposMov: [],
      EstadoAmbito: ['Todos'],
      EstadosAmbito: [],
      Usuario: ['Todos'],
      Usuarios: []
    }
  },
  created () {
    this.Responsable = auth.UsuarioLogueado
    const r = auth.UsuarioLogueado
    this.IdResponsable = r.IdUsuario
    this.nuevoMovimiento.Responsable = {
      label: `${r.Apellidos}, ${r.Nombres}`,
      value: r.IdUsuario
    }
    this.IdEstudio = r.IdEstudio
    request.Get(`/estudios/${this.IdEstudio}/tipos-movimiento`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.TiposMov = r
      } else {
        this.$q.notify('No hay tipos de movimiento disponibles para este estudio')
      }
    })
    request.Get('/estado-ambito-gestion', {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.EstadosAmbito = r
      }
    })
    request.Get(`/estudios/${this.IdEstudio}/usuarios`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.Usuarios = r
      }
    })
    request.Get('/movimientos/vista-tribunales', {}, (r) => {
      if (!r.Error) {
        const buscarObjetivos = {}
        const IdsCasos = JSON.stringify(r.map((c) => c.IdCaso))
        request.Get(`/objetivos?IdsCaso=${IdsCasos}`, {}, (r) => {
          if (!r.Error) {
            this.Casos.forEach((c) => {
              c.Objetivos = r[c.IdCaso]
            })
          }
        })
        request.Get(`/personas/casos?IdsCasos=${IdsCasos}`, {}, (res) => {
          if (res.Error) {
            Notify.create(res.Error)
          } else {
            // ---------------------
            r.forEach((c) => {
              var personasCaso = []
              for (const param in res) {
                if (param === c.IdCaso) {
                  personasCaso = res[param]
                }
              }
              let caso = {
                PersonasCaso: personasCaso,
                Caratula: c.Caratula,
                Carpeta: c.Carpeta,
                FechaAlta: c.FechaAlta,
                FechaUltVisita: c.FechaUltVisita,
                IdCaso: c.IdCaso,
                IdEstadoCaso: c.IdEstadoCaso,
                IdJuzgado: c.IdJuzgado,
                IdNominacion: c.IdNominacion,
                IdTipoCaso: c.IdTipoCaso,
                Juzgado: c.Juzgado,
                Nominacion: c.Nominacion,
                NroExpediente: c.NroExpediente,
                Observaciones: c.Observaciones,
                Tipo: c.Tipo,
                UsuariosCaso: c.UsuariosCaso
              }

              this.Casos.push(caso)
              if (c.Estado !== 'B') {
                c.Movimientos = JSON.parse(c.Movimientos)
                buscarObjetivos[c.IdCaso] = c
                c.Movimientos.forEach((m) => {
                  m.CasoCompleto = c
                  m.IdUsuarioResponsable = this.IdResponsable
                  m.Juzgado = c.Juzgado
                  m.Nominacion = c.Nominacion
                  m.IdJuzgado = c.IdJuzgado
                  m.IdNominacion = c.IdNominacion
                  m.Detalle = m.Detalle.replace(/<\s*\/?\s*br\s*.*?>/g, '\n')
                  m.NroExpediente = c.NroExpediente
                  if (!m.FechaRealizado && c.Tipo !== 'N') {
                    if (m.Color === 'negative') {
                      this.Perentorios.push(m)
                    } else {
                      if (m.Color === 'primary' || m.Color === 'warning') {
                        if (this.Juzgados.indexOf(c.Juzgado) === -1) {
                          this.Juzgados.push(c.Juzgado)
                        }
                        m.Juzgado = c.Juzgado
                        this.GestionEstudio.push(m)
                      }
                    }
                  }
                })
              }
            })
            this.cargandoCasos = false
            this.Juzgados.sort()
            this.Perentorios.sort(function (m2, m1) {
              if (!m2.FechaEsperada) {
                return 1
              }
              if (!m1.FechaEsperada) {
                return -1
              }

              if (m1.FechaEsperada < m2.FechaEsperada) {
                return -1
              } else if (m1.FechaEsperada > m2.FechaEsperada) {
                return 1
              } else return 0
            })
            this.Perentorios.sort((a, b) => {
              if (!a.FechaEsperada) {
                return 1
              }
              if (!b.FechaEsperada) {
                return -1
              }

              let fecha = a.FechaEsperada.split(' ')[0]

              if (fecha === moment().format('YYYY-MM-DD')) return -1

              fecha = b.FechaEsperada.split(' ')[0]

              if (fecha === moment().format('YYYY-MM-DD')) return 1
            })
            this.GestionEstudio.sort(function (m2, m1) {
              if (!m1.FechaEsperada) {
                return -1
              }
              if (!m2.FechaEsperada) {
                return 1
              }

              if (m1.FechaEsperada < m2.FechaEsperada) {
                return -1
              } else if (m1.FechaEsperada > m2.FechaEsperada) {
                return 1
              } else return 1
            })
            this.GestionEstudio.sort((a, b) => {
              if (!a.FechaEsperada) {
                return 1
              }
              if (!b.FechaEsperada) {
                return -1
              }

              let fecha = a.FechaEsperada.split(' ')[0]

              if (fecha === moment().format('YYYY-MM-DD')) return -1

              fecha = b.FechaEsperada.split(' ')[0]

              if (fecha === moment().format('YYYY-MM-DD')) return 1
            })
            // Busco los objetivos
            request.Get(
              `/objetivos?IdsCaso=${JSON.stringify(
                Object.keys(buscarObjetivos)
              )}`,
              {},
              (objCaso) => {
                Object.keys(objCaso).forEach((k) => {
                  buscarObjetivos[k].Objetivos = objCaso[k]
                })
              }
            )
          }
        })
      }
      this.cargandoMovimientos = false
    })
  },
  watch: {
    'nuevoMovimiento.FechaEsperada' (val) {
      this.$nextTick(() => {
        if (!this.$refs.inputFechaEsperada.innerErrorMessage) {
          this.$refs.inputFechaEsperada.innerError = false
        }
      })
    },
    'nuevoMovimiento.FechaAlta' (val) {
      this.$nextTick(() => {
        if (!this.$refs.inputFechaAlta.innerErrorMessage) {
          this.$refs.inputFechaAlta.innerError = false
        }
      })
    }
  },
  computed: {
    opcionesTipoMov () {
      let result = []
      if (
        this.modalAlta &&
        this.nuevoMovimiento.TiposMov &&
        this.nuevoMovimiento.TiposMov.length
      ) {
        result = this.nuevoMovimiento.TiposMov.map((t) => ({
          label: t.TipoMovimiento,
          value: t.IdTipoMov
        }))
      }
      return result
    },
    opcionesResponsable () {
      let result = []
      if (
        this.nuevoMovimiento.UsuariosEstudio &&
        this.nuevoMovimiento.UsuariosEstudio.length
      ) {
        result = this.nuevoMovimiento.UsuariosEstudio.map((t) => ({
          label: `${t.Apellidos}, ${t.Nombres}`,
          value: t.IdUsuario
        }))
      }
      return result
    },
    opcionesTiposMov () {
      let result = []
      if (this.TiposMov && this.TiposMov.length) {
        result.push('Todos')
        this.TiposMov.forEach(t => {
          result.push(t.TipoMovimiento)
        })
      }
      return result
    },
    opcionesEstadoAmbito () {
      let result = []
      if (this.EstadosAmbito && this.EstadosAmbito.length) {
        result.push('Todos')
        this.EstadosAmbito.forEach(e => {
          result.push({
            label: e.EstadoAmbitoGestion,
            value: e.IdEstadoAmbitoGestion
          })
        })
      }
      return result
    },
    opcionesUsuarios () {
      let result = []
      if (this.Usuarios && this.Usuarios.length) {
        result.push('Todos')
        this.Usuarios.forEach(u => {
          result.push(u.Apellidos + ', ' + u.Nombres)
        })
      }
      return result
    },
    filtrarMovGestion () {
      let filter = this.GestionEstudio
      filter = this.filtrarPorTipoMov(filter)
      filter = this.filtrarPorEstado(filter)
      filter = this.filtrarPorUsuario(filter)
      return filter
    },
    filtrarPerentorios () {
      let filter = this.Perentorios
      filter = this.filtrarPorTipoMov(filter)
      filter = this.filtrarPorEstado(filter)
      filter = this.filtrarPorUsuario(filter)
      return filter
    }
  },
  methods: {
    filtrarGestionEstudio (juzgado) {
      return this.GestionEstudio.filter((m) => m.Juzgado === juzgado)
    },
    filtrarPorTipoMov (movs) {
      if (this.TipoMov.length === 0 || this.TipoMov[this.TipoMov.length - 1] === 'Todos') {
        this.TipoMov = ['Todos']
      }
      if (this.TipoMov.length > 1 && this.TipoMov.includes('Todos')) {
        const i = this.TipoMov.indexOf('Todos')
        this.TipoMov.splice(i, 1)
      }
      if (!this.TipoMov.includes('Todos')) {
        movs = movs.filter(m => this.TipoMov.includes(m.TipoMovimiento))
      }
      return movs
    },
    filtrarPorUsuario (movs) {
      if (this.Usuario.length === 0 || this.Usuario[this.Usuario.length - 1] === 'Todos') {
        this.Usuario = ['Todos']
      }
      if (this.Usuario.length > 1 && this.Usuario.includes('Todos')) {
        const i = this.Usuario.indexOf('Todos')
        this.Usuario.splice(i, 1)
      }
      if (!this.Usuario.includes('Todos')) {
        movs = movs.filter(m => this.Usuario.includes(m.UsuarioResponsable))
      }
      return movs
    },
    filtrarPorEstado (movs) {
      if (this.EstadoAmbito.length === 0 || this.EstadoAmbito[this.EstadoAmbito.length - 1] === 'Todos') {
        this.EstadoAmbito = ['Todos']
      }
      if (this.EstadoAmbito.length > 1 && this.EstadoAmbito.includes('Todos')) {
        const i = this.EstadoAmbito.indexOf('Todos')
        this.EstadoAmbito.splice(i, 1)
      }
      if (!this.EstadoAmbito.includes('Todos')) {
        console.log(this.EstadoAmbito)
        movs = movs.filter(m => {
          const idEstado = m.CasoCompleto.IdEstadoAmbitoGestion
          if (this.EstadoAmbito.find(e => e.value === idEstado)) {
            return true
          }
        })
      }
      return movs
    },
    formatearFecha (fecha) {
      if (fecha) {
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
      if (fecha !== null) {
        return this.parseDateTime(fecha).split(' ')[0]
      } else return null
    },
    isMobile () {
      if (
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
          navigator.userAgent
        )
      ) {
        return true
      } else {
        return false
      }
    },
    realizarMovimiento (movimiento, modo) {
      request.Put(
        `/movimientos/${movimiento.IdMovimientoCaso}/realizar`,
        {},
        (r) => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            if (movimiento.Color === 'primary') {
              this.GestionEstudio.splice(
                this.GestionEstudio.indexOf(movimiento),
                1
              )
              let movimientosRestantes = false
              this.GestionEstudio.forEach((m) => {
                if (
                  m.Juzgado === juzgado &&
                  m.IdMovimientoCaso !== movimiento.IdMovimientoCaso
                ) {
                  movimientosRestantes = true
                }
              })
              if (movimientosRestantes === false) {
                this.Juzgados.splice(this.Juzgados.indexOf(juzgado))
              }
            } else {
              this.Perentorios.splice(this.Perentorios.indexOf(movimiento), 1)
            }
            this.$q.notify({
              color: 'green',
              message: `Se marcó como realizado el movimiento '${movimiento.Detalle}'`
            })
            this.movimientoAlta = movimiento
            this.altaMovimiento()
          }
        }
      )
    },
    altaMovimiento () {
      this.modalAlta = true
      this.nuevoMovimiento.IdEstudio = auth.UsuarioLogueado.IdEstudio
      request.Get(
        `/estudios/${this.nuevoMovimiento.IdEstudio}/usuarios`,
        {},
        (r) => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else if (r.length) {
            this.nuevoMovimiento.UsuariosEstudio = r
          }
        }
      )
      request.Get(
        `/estudios/${this.nuevoMovimiento.IdEstudio}/tipos-movimiento`,
        {},
        (r) => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else if (r.length) {
            this.nuevoMovimiento.TiposMov = r
            r.forEach((tm) => {
              if (Number(tm.IdTipoMov) === this.movimientoAlta.IdTipoMov) {
                this.nuevoMovimiento.TipoMov = {
                  label: tm.TipoMovimiento,
                  value: tm.IdTipoMov
                }
              }
            })
          } else {
            this.$q.notify(
              'No hay tipos de movimiento disponibles para este estudio'
            )
          }
        }
      )
    },
    cancelarAlta () {
      this.modalAlta = false
      this.nuevoMovimiento = {
        IdUsuario: 0,
        TiposMov: [],
        TipoMov: 0,
        UsuariosEstudio: [],
        IdEstudio: 0,
        Detalle: '',
        FechaEsperada: '',
        FechaAlta: '',
        colorSeleccionado: { label: 'Gestión Estudio', value: 'primary' },
        Caso: 0,
        Objetivo: '',
        Responsable: {}
      }
    },
    guardarMovimiento () {
      if (!this.nuevoMovimiento.Detalle) {
        this.$q.notify('Debe ingresar el detalle del movimiento.')
      }
      if (!this.nuevoMovimiento.TipoMov) {
        this.$q.notify('Debe elegir un tipo de movimiento.')
      }
      if (this.nuevoMovimiento.colorSeleccionado === '') {
        this.$q.notify('Debe seleccionar un estado de gestión')
      } else {
        this.Casos.forEach((c) => {
          if (this.movimientoAlta.Caso === c.Caratula) {
            this.nuevoMovimiento.IdCaso = c.IdCaso
            this.nuevoMovimiento.NroExpediente = c.NroExpediente
          }
        })
        let fEsperada = this.nuevoMovimiento.FechaEsperada
        let fAlta = this.nuevoMovimiento.FechaAlta
        if (
          this.nuevoMovimiento.FechaEsperada &&
          this.nuevoMovimiento.FechaEsperada.split('-')[2].length === 4
        ) {
          fEsperada = `${this.nuevoMovimiento.FechaEsperada.split('-')[2]}-${
            this.nuevoMovimiento.FechaEsperada.split('-')[1]
          }-${this.nuevoMovimiento.FechaEsperada.split('-')[0]}`
        }
        if (
          this.nuevoMovimiento.FechaAlta &&
          this.nuevoMovimiento.FechaAlta.split('-')[2].length === 4
        ) {
          fAlta = `${this.nuevoMovimiento.FechaAlta.split('-')[2]}-${
            this.nuevoMovimiento.FechaAlta.split('-')[1]
          }-${this.nuevoMovimiento.FechaAlta.split('-')[0]}`
        }
        const movimiento = {
          IdResponsable: this.nuevoMovimiento.Responsable.value,
          Detalle: this.nuevoMovimiento.Detalle,
          IdCaso: this.nuevoMovimiento.IdCaso,
          FechaEsperada: this.formatearFecha(fEsperada),
          FechaAlta: this.formatearFecha(fAlta),
          FechaRealizado: null,
          IdTipoMov: this.nuevoMovimiento.TipoMov.value,
          Cuaderno: this.movimientoAlta.Cuaderno,
          Color: this.nuevoMovimiento.colorSeleccionado.value,
          Multimedia: this.Multimedia
        }
        request.Post('/movimientos', movimiento, (r) => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else {
            this.modalAlta = false
            this.$q.notify({
              color: 'green',
              message: `El movimiento "${movimiento.Detalle}" se dio de alta correctamente`
            })
            /* if (this.caso) {
              if (this.CasosPendientes.indexOf(this.caso) - -1 === 0) {
                this.CasosPendientes.push(this.caso);
              }
            } */
            movimiento.TipoMovimiento = this.nuevoMovimiento.TipoMov.label
            movimiento.Caso = this.movimientoAlta.Caso
            movimiento.CasoCompleto = this.caso
            request.Get(
              `/casos/${this.nuevoMovimiento.IdCaso}/movimientos`,
              {},
              (r) => {
                if (!r.Error) {
                  var contador = true
                  r.forEach((m) => {
                    if (m.Detalle === movimiento.Detalle) {
                      movimiento.IdMovimientoCaso = m.IdMovimientoCaso
                      if (contador === true && !movimiento.FechaRealizado) {
                        if (this.movimientoAlta.Objetivo) {
                          var objetivo = {}
                          this.movimientoAlta.CasoCompleto.Objetivos.forEach(
                            (o) => {
                              if (o.Objetivo === this.movimientoAlta.Objetivo) {
                                objetivo = o
                              }
                            }
                          )
                          request.Post(
                            `/movimientos/${movimiento.IdMovimientoCaso}/asociar-objetivo/${objetivo.IdObjetivo}`,
                            {},
                            (r) => {
                              movimiento.Objetivo = objetivo.Objetivo
                              this.MovimientosPendientes.push(movimiento)
                              this.Movimientos.push(movimiento)
                              contador = false
                            }
                          )
                        } else {
                          this.MovimientosPendientes.push(movimiento)
                          this.Movimientos.push(movimiento)
                          contador = false
                        }
                      }
                    }
                  })
                  this.altaFinalizada = true
                  this.alta = false
                  this.movimientoAlta = {}
                  this.nuevoMovimiento.IdUsuario = 0
                  this.nuevoMovimiento.TiposMov = []
                  this.nuevoMovimiento.TipoMov = 0
                  this.nuevoMovimiento.UsuariosEstudio = []
                  this.nuevoMovimiento.IdEstudio = 0
                  this.nuevoMovimiento.IdResponsable = 0
                  this.nuevoMovimiento.Detalle = ''
                  this.nuevoMovimiento.FechaAlta = new Date().toISOString()
                  this.nuevoMovimiento.FechaEsperada = null
                  this.nuevoMovimiento.colorSeleccionado = 'primary'
                  this.nuevoMovimiento.Caso = 0
                }
              }
            )
          }
        })
      }
    },
    verCasos () {
      /*
      request.Get(`/casos/${this.Casos.IdCaso}`, {}, r => {
        if (r.Error) {
          Notify.create(r.Error)
          console.log('esta verga no anda')
        } else {
          this.Casos.PersonasCaso = r.PersonasCaso
        }
      }
      */
      this.modalCasos = true
    },
    mostrarObjetivos (movimiento) {
      this.Casos.forEach((c) => {
        if (c.IdCaso === movimiento.CasoCompleto.IdCaso) {
          this.modalObjetivos = true
          this.movimientoMostrarObjetivos = movimiento
          this.objetivosMostrar = c.Objetivos
        }
      })
    },
    filtrarObjetivos () {
      let respuesta = []
      this.objetivosMostrar.forEach((objetivo) => {
        if (!this.movimientoMostrarObjetivos.Objetivo) {
          respuesta.push(objetivo)
        } else {
          if (objetivo.Objetivo === this.movimientoMostrarObjetivos.Objetivo) {
            respuesta.push(objetivo)
          }
        }
      })
      return respuesta
    },
    edicionObjetivo (objetivo) {
      this.objetivoEditar = objetivo
      this.editarObjetivo = true
    },
    asociarObjetivo (objetivo) {
      request.Post(
        `/movimientos/${this.movimientoMostrarObjetivos['IdMovimientoCaso']}/asociar-objetivo/${objetivo.IdObjetivo}`,
        {},
        (r) => {
          this.movimientoMostrarObjetivos.Objetivo = objetivo.Objetivo
          this.modalObjetivos = false
        }
      )
    },
    desasociarObjetivo (objetivo) {
      request.Put(
        `/movimientos/${this.movimientoMostrarObjetivos['IdMovimientoCaso']}/desasociar-objetivo/${objetivo.IdObjetivo}`,
        {},
        (r) => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.movimientoMostrarObjetivos.Objetivo = null
          }
          this.modalObjetivos = false
        }
      )
    },
    finalizarEdicionObjetivo () {
      let objetivo = {
        Objetivo: this.objetivoEditar.Objetivo
      }
      request.Put(
        `/objetivos/${this.objetivoEditar.IdObjetivo}`,
        objetivo,
        (r) => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            Object.defineProperty(
              this.filtrarObjetivos().filter(
                (obj) => obj.IdObjetivo === this.objetivoEditar.IdObjetivo
              ),
              'Objetivo',
              {
                value: this.objetivoEditar.Objetivo
              }
            )

            this.editarObjetivo = false
            this.objetivoEditar = {}
          }
        }
      )
    },
    asociarANuevoObjetivo () {
      this.nuevoObjetivo.IdCaso = this.movimientoMostrarObjetivos.CasoCompleto.IdCaso
      request.Post('/objetivos', this.nuevoObjetivo, (r) => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.objetivosMostrar.push(Object.assign({}, this.nuevoObjetivo))
          this.modalNuevoObjetivo = false
          request.Post(
            `/movimientos/${this.movimientoMostrarObjetivos['IdMovimientoCaso']}/asociar-objetivo/${r.IdObjetivo}`,
            {},
            (r) => {
              if (r.Error) {
                this.$q.notify(r.Error)
              } else {
                this.movimientoMostrarObjetivos.Objetivo = this.nuevoObjetivo.Objetivo
                this.nuevoObjetivo.Objetivo = ''
              }
            }
          )
        }
      })
    }
  }
}
</script>

<style>
#page_container_vencimientos {
  padding: 2em;
}
.titulo {
  padding: 1em;
  color: white;
  font-weight: 500;
  border-radius: 10px 10px 0 0;
}
.tarjeta {
  background-color: white;
  border-radius: 10px;
  /* margin-bottom:.5em */
}
.contenidoTarjeta {
  padding: 0 1em 1em 1em;
}
.perentorios_container {
  background-color: rgba(0, 0, 0, 0.1);
  width: 48% !important;
  margin: 0 auto;
  border-radius: 20px;
}
.movimiento_container {
  width: 45vw;
  margin: 10px 0;
}
.section_title {
  margin-bottom: 20px;
  transition: all 0.18s ease-in-out;
}
.section_title:hover {
  color: teal;
  border-radius: 50px;
}
@media screen and (max-width: 1000px) {
  .perentorios_container {
    background-color: rgba(0, 0, 0, 0.1);
    width: 100% !important;
    margin: 10px 0;
    border-radius: 20px;
  }
  .movimiento_container {
    width: 94vw;
    margin: 10px 0;
  }
}
@media screen and (max-width: 700px) {
  .movimiento_container {
    width: 80vw;
    margin: 10px 0;
  }
}
</style>
