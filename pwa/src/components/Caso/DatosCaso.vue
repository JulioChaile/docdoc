<template>
  <q-page>
    <p class="seccion-titulo">Caso</p>
    <div class="seccion-container border-teal">
      <div v-if="!loading" class="botones-editar-container">
        <q-icon
          v-if="!editar"
          class="cursor-pointer"
          color="blue-grey"
          name="fas fa-edit"
          size="sm"
          @click="habilitarEdicion()"
        >
          <q-tooltip>Editar Datos</q-tooltip>
        </q-icon>
        <q-icon
          v-else
          class="cursor-pointer"
          color="positive"
          name="done"
          size="sm"
          @click="guardarEdicion()"
        >
          <q-tooltip>Guardar Edicion</q-tooltip>
        </q-icon>
      </div>

      <Loading v-if="loading" />

      <div v-else>
        <div class="seccion-item">
          <q-item>
            <q-item-section avatar>
              <q-icon color="grey" name="description" size="50px" />
            </q-item-section>

            <q-item-section class="persona-item-subtitulo">Resumen</q-item-section>

            <q-item-section v-if="!editar && false" side top>
              <q-badge color="grey" label="Ver Archivos Adjuntos" @click="modalArchivos = true" />
            </q-item-section>
          </q-item>

          <ul v-if="!editar">
            <li>
              <span class="item-titulo text-indigo">Fecha del Hecho: </span>{{ ParametrosCaso.FechaHecho ? ParametrosCaso.FechaHecho.split('-').reverse().join('/') : 'Sin datos' }}
            </li>
            <li>
              <span class="item-titulo text-indigo">Relato del Hecho: </span>{{ ParametrosCaso.RelatoHecho ? ParametrosCaso.RelatoHecho : 'Sin datos' }}
            </li>
            <!--li v-if="ParametrosCaso.HistoriaClinica">
              <span class="item-titulo text-indigo">Estado de Historia Clinica: </span>
            </li-->
            <li v-if="ParametrosCaso.EstadoDocumentacion">
              <span class="item-titulo text-indigo">Estado de Documentacion: </span>{{ ParametrosCaso.EstadoDocumentacion ? ParametrosCaso.EstadoDocumentacion : 'Sin datos' }}
            </li>
            <li>
              <span class="item-titulo text-indigo">Total Demanda: </span> {{ totalDemanda }}
            </li>
            <li>
              <span class="item-titulo text-indigo">Total Demanda Letra: </span> {{ totalDemandaLetra }}
            </li>
          </ul>

          <div class="seccion-item-editar" v-else>
            <q-input
              v-model="ParametrosCaso.FechaHecho"
              ref="inputFechaHecho"
              label="Fecha del Hecho"
              mask="##-##-####"
              :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']"
            >
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy
                      ref="qDateProxy1"
                      transition-show="scale"
                      transition-hide="scale"
                    >
                      <q-date
                        v-model="ParametrosCaso.FechaHecho"
                        mask="DD-MM-YYYY"
                        label="Fecha del Hecho"
                        @input="() => $refs.qDateProxy1.hide()"
                      />
                    </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
            <q-input
              v-model="ParametrosCaso.RelatoHecho"
              label="Relato del Hecho"
              type="textarea"
              row="4"
            />
            <q-select
              v-model="ParametrosCaso.EstadoDocumentacion"
              :options="opcionesEstadoDoc"
              label="Estado de Documentacion"
              @input="fechaEstadoDoc()"
            />
            <!--q-checkbox
              v-model="ParametrosCaso.HistoriaClinica"
              label="Historia Clinica Requerida"
            /-->
          </div>
        </div>

        <div
          class="seccion-item"
          v-if="editar || checkCP"
        >
          <q-separator />

          <q-item>
            <q-item-section avatar>
              <q-icon color="grey" name="work" size="50px" />
            </q-item-section>

            <q-item-section class="persona-item-subtitulo">
              Causa Penal
              <q-toggle
                v-if="editar"
                v-model="checkCP"
              />
            </q-item-section>

            <q-item-section v-if="!editar && false" side top>
              <q-badge color="grey" label="Ver Archivos Adjuntos" @click="modalArchivos = true" />
            </q-item-section>
          </q-item>

          <ul v-if="!editar">
            <li>
              <span class="item-titulo text-indigo">Estado: </span>{{ CausaPenal.EstadoCausaPenal ? CausaPenal.EstadoCausaPenal : 'Sin datos' }}
            </li>
            <li>
              <span class="item-titulo text-indigo">Nro de Expediente Penal: </span>{{ CausaPenal.NroExpedienteCausaPenal ? CausaPenal.NroExpedienteCausaPenal : 'Sin datos' }}
            </li>
            <li>
              <span class="item-titulo text-indigo">Radicacion: </span>{{ CausaPenal.RadicacionCausaPenal ? CausaPenal.RadicacionCausaPenal : 'Sin datos' }}
            </li>
            <li>
              <span class="item-titulo text-indigo">Comisaria: </span>{{ CausaPenal.Comisaria ? CausaPenal.Comisaria : 'Sin datos' }}
            </li>
          </ul>

          <div v-if="!editar" class="text-caption q-pl-lg border q-mt-lg cursor-pointer" @click="verHistorialEstados">
            <b>Ver Historial de Estados</b>
          </div>

          <div class="seccion-item-editar" v-else-if="checkCP">
            <div class="row justify-start">
              <q-select
                v-model="CausaPenal.EstadoCausaPenal"
                :options="opcionesEstadoCausaPenal"
                label="Estado"
                class="col-11"
              />
              <q-btn
                color="positive"
                round
                size="sm"
                class="self-center"
                @click="editarOpciones(false, 'opcionesEstadoCausaPenal')"
              >
                +
                <q-tooltip>
                  Agregar Otra Opcion a la Lista
                </q-tooltip>
              </q-btn>
            </div>
            <q-input
              v-model="CausaPenal.NroExpedienteCausaPenal"
              label="Nro de Expediente Penal"
              type="text"
            />
            <div class="row justify-start">
              <q-select
                v-model="CausaPenal.RadicacionCausaPenal"
                :options="opcionesRadicacion"
                label="Radicacion"
                class="col-11"
              />
              <q-btn
                color="positive"
                round
                size="sm"
                class="self-center"
                @click="editarOpciones(false, 'opcionesRadicacion')"
              >
                +
                <q-tooltip>
                  Agregar Otra Opcion a la Lista
                </q-tooltip>
              </q-btn>
            </div>
            <div class="row justify-start">
              <q-select
                v-model="CausaPenal.Comisaria"
                class="col-11"
                use-input
                hide-selected
                fill-input
                input-debounce="0"
                label="Buscar Comisarias"
                :options="opcionesComisaria"
                @filter="filterFnC"
                hint="Escriba 1 letra para comenzar la busqueda. Se mostraran los primeros 10 resultados."
              >
                <template v-slot:no-option>
                    <q-item>
                      <q-item-section class="text-grey">
                          No se encontraron elementos que coincidan con la busqueda
                      </q-item-section>
                    </q-item>
                </template>
              </q-select>
              <q-btn
                color="positive"
                round
                size="sm"
                class="self-center"
                @click="modalOpcionNueva = true; tipoOpcion = 'C'"
              >
                +
                <q-tooltip>
                  Agregar Otra Opcion a la Lista
                </q-tooltip>
              </q-btn>
            </div>
          </div>
        </div>
      </div>
    </div>

    <p id="personas" class="seccion-titulo">Personas</p>
    <div class="seccion-container border-blue-grey">
      <p class="seccion-titulo">Actores</p>
      <Loading v-if="loading" />
      <ul v-else>
        <li
          v-for="persona in personas.filter(p => p.Observaciones === 'Actor')"
          :key="persona.IdPersona"
        >
          <DatosPersona
            :IdCaso="id"
            :Persona="persona"
            :FechaHecho="ParametrosCaso.FechaHecho"
            :Opciones="opcionesParametros"
            :ParametrosCaso="ParametrosCaso"
            @parEdit="setParPersona"
          />
        </li>
      </ul>

      <q-separator />

      <p class="seccion-titulo">Demandados</p>
      <Loading v-if="loading" />
      <ul v-else>
        <li
          v-for="persona in personas.filter(p => p.Observaciones === 'Demandado')"
          :key="persona.IdPersona"
        >
          <DatosPersona
            :IdCaso="id"
            :Persona="persona"
            :Opciones="opcionesParametros"
            :ParametrosCaso="ParametrosCaso"
            @parEdit="setParPersona"
          />
        </li>
      </ul>
    </div>

    <!--Modal: Opcion Nueva-->
    <q-dialog v-model="modalOpcionNueva">
      <q-card style="width: 100%; padding: 1rem">
        <q-input
          v-model="opcionNueva"
          label="Escriba la opcion que desee agregar"
          type="text"
        />
        <div style="display: flex; justify-content: center; margin-botton:2rem; margin-top: 20px">
          <q-btn
            @click="altaOpcion()"
            color="positive"
          >Agregar</q-btn>
          <q-btn
            @click="modalOpcionNueva = false; opcionNueva = ''; tipoOpcion = ''"
            color="primary"
            style="margin-left: 30px"
          >Cerrar</q-btn>
        </div>
      </q-card>
    </q-dialog>

    <!--Modal: Historial-->
    <q-dialog v-model="modalHistorialEstados" style="width: auto; height:auto;">
      <q-card style="padding:1rem;">
        <div v-if="loadingHE" class="full-width q-pa-lg">
          <Loading />
        </div>
        <div v-else class="full-width q-pa-lg">
          <ul>
            <li
              v-for="(e, i) in HistorialEstados"
              :key="e.IdCausaPenalLogEstados"
            >
              {{ e.EstadoCausaPenal }} - {{ diasEstado(i - 1, i) }}
            </li>
          </ul>
        </div>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import request from '../../request'
import moment from 'moment'
import numeroALetras from '../../assets/numeroALetras'
import Loading from '../../components/Loading'
import DatosPersona from '../../components/DatosCaso/DatosPersona'

export default {
  name: 'DatosCaso',
  components: {
    Loading,
    DatosPersona
  },
  data () {
    return {
      id: 0,
      modalHistorialEstados: false,
      HistorialEstados: [],
      loadingHE: false,
      datosCaso: {},
      personas: [],
      loading: true,
      editar: false,
      varOpciones: '',
      opcionNueva: '',
      modalOpcionNueva: false,
      tipoOpcion: '',
      estadoDocEditar: '',
      opcionesParametros: [],
      opcionesEstadoDoc: [],
      opcionesEstadoCausaPenal: [],
      opcionesRadicacion: [],
      opcionesComisaria: [],
      ParametrosCaso: {
        FechaHecho: '',
        RelatoHecho: '',
        EstadoDocumentacion: '',
        FechaDocumentacion: '',
        HistoriaClinica: false
      },
      checkCP: false,
      CausaPenal: {
        IdCausaPenalCaso: null,
        EstadoCausaPenal: '',
        NroExpedienteCausaPenal: '',
        RadicacionCausaPenal: '',
        Comisaria: ''
      }
    }
  },
  created () {
    if (!this.$route.query.id) {
      this.$router.push('GrillaCasos')
      return
    }
    this.id = this.$route.query.id

    request.Get(`/casos`, { id: this.id }, (r) => {
      if (!r.Error) {
        // Datos del caso:
        this.datosCaso = {
          Caratula: r.Caratula,
          Carpeta: r.Carpeta,
          IdCaso: r.IdCaso,
          Estado: r.Estado,
          FechaAlta: r.FechaAlta,
          FechaEstado: r.FechaEstado,
          FechaUltVisita: r.FechaUltVisita,
          NroExpediente: r.NroExpediente,
          Competencia: r.Competencia,
          IdCompetencia: r.IdCompetencia,
          TipoCaso: r.TipoCaso,
          IdTipoCaso: r.IdTipoCaso,
          Juzgado: r.Juzgado,
          IdJuzgado: r.IdJuzgado,
          Nominacion: r.Nominacion,
          IdNominacion: r.IdNominacion,
          EstadoAmbitoGestion: r.EstadoAmbitoGestion,
          IdEstadoAmbitoGestion: r.IdEstadoAmbitoGestion,
          Origen: r.Origen,
          IdOrigen: r.IdOrigen,
          IdMediacion: r.IdMediacion
        }
        /*
        // Movimientos del caso:
        this.movimientos = r.MovimientosCaso
        console.log(this.movimientos)
        if (this.Objetivos.length > 0) {
          console.log(this.Objetivos)
          this.Objetivos.forEach(e => {
            const i = this.movimientos.findIndex(f => f.IdMovimientoCaso === parseInt(e.IdMovimientoCaso))
            if (i !== -1) {
              this.movimientos[i].IdObjetivo = e.IdObjetivo
              this.movimientos[i].Objetivo = e.Objetivo
            }
          })
        }
        */

        // Personas del caso:
        this.personas = r.PersonasCaso ? r.PersonasCaso : []

        if (r.Parametros) {
          this.ParametrosCaso = r.Parametros
        }

        if (r.CausaPenal.IdCausaPenalCaso) {
          this.checkCP = true
          this.CausaPenal = r.CausaPenal
        }

        request.Get('/casos/opciones-parametros', {}, r => {
          console.log(r)
          this.opcionesParametros = r
          this.opcionesParametros.forEach(o => {
            if (typeof (this[o.Variable]) !== 'undefined') {
              this[o.Variable] = JSON.parse(o.Opciones)
            }
          })
          this.loading = false
        })
      } else {
        console.log('Hubo un error al traer el caso.')
      }
    })
  },
  computed: {
    totalDemanda () {
      let total = 0

      this.personas.forEach(p => {
        if (p.Parametros !== null && p.Parametros.check && p.Observaciones === 'Actor') {
          const gc = p.Parametros.Cuantificacion.GastosCuracion
          const dm = p.Parametros.Cuantificacion.DañoMoral
          const vm = p.Parametros.Cuantificacion.FormulaVM
          const vr = p.Parametros.Vehiculo.ValorReparacion
          total = total + (parseInt(gc) || 0) + (parseInt(dm) || 0) + (parseInt(vm) || 0) + (parseInt(vr) || 0)
        }
      })

      return total || 'Sin datos'
    },
    totalDemandaLetra () {
      const total = this.totalDemanda

      return total === 'Sin datos'
        ? 'Sin datos'
        : numeroALetras(total, {
          plural: 'PESOS',
          singular: 'PESO',
          centPlural: 'CENTAVOS',
          centSingular: 'CENTAVO'
        })
    }
  },
  methods: {
    diasEstado (i, j) {
      const FechaFin = i === -1 ? moment().format('YYYY-MM-DD') : this.HistorialEstados[i].FechaEstadoCausaPenal
      const FechaInicio = this.HistorialEstados[j].FechaEstadoCausaPenal

      return moment(FechaFin).diff(FechaInicio, 'days') + ' días'
    },
    verHistorialEstados () {
      this.modalHistorialEstados = true
      this.loadingHE = true

      request.Get('/casos/historial-estados-causa-penal', { IdCaso: this.id }, r => {
        this.HistorialEstados = r
        this.loadingHE = false
      })
    },
    filterFnC (val, update, abort) {
      if (val.length >= 1) {
        request.Get(`/casos/buscar-contacto-parametros`, {offset: 0, limit: 10, cadena: val, tipo: 'C'}, r => {
          update(
            () => {
              this.opcionesComisaria = r.map(c => {
                return c.Comisaria
              })
            },
            ref => {
              if (val.length >= 4 && ref.options.length > 0) {
                ref.setOptionIndex(-1) // reset optionIndex in case there is something selected
                ref.moveOptionSelection(1, true) // focus the first selectable option and do not update the input-value
              }
            }
          )
        })
      } else {
        this.opcionesComisaria = []
      }
    },
    altaOpcion () {
      if (!this.tipoOpcion) {
        this.editarOpciones(true)
        return
      }

      if (!this.opcionNueva) {
        this.$q.notify('La nueva opcion no puede estar vacia.')
        return
      }

      switch (this.tipoOpcion) {
        case 'C':
          this.opcionesComisaria.push(this.opcionNueva)
          request.Post('/casos/alta-contacto-parametros', {tipo: 'C', opcion: this.opcionNueva}, r => {
            if (r.Error) {
              this.opcionesComisaria.pop()
              this.$q.notify(r.Error)
            } else {
              this.$q.notify('Se agrego la opcion correctamente.')
            }

            this.opcionNueva = ''
            this.modalOpcionNueva = false
            this.tipoOpcion = ''
          })
          break
      }
    },
    editarOpciones (check, varOpciones) {
      if (!check) {
        this.varOpciones = varOpciones
        this.modalOpcionNueva = true
        return
      } else if (!this.opcionNueva) {
        this.$q.notify('La nueva opcion no puede estar vacia.')
        return
      }

      const v = this.varOpciones
      const o = this.opcionNueva

      this[v].push(o)

      const i = this.opcionesParametros.findIndex(p => p.Variable === v)

      if (i < 0) {
        this.$q.notify('Hubo un error al editar las opciones, intentelo de nuevo mas tarde.')
      }

      const data = {
        id: this.opcionesParametros[i].IdOpcionesParametrosCaso,
        opciones: JSON.stringify(this[v])
      }

      request.Post('/casos/editar-opciones-parametros', data, r => {
        if (r.Error) {
          this[v].pop()
          this.$q.notify(r.Error)
        } else {
          this.$q.notify('Se agrego la opcion correctamente.')
        }

        this.varOpciones = ''
        this.opcionNueva = ''
        this.modalOpcionNueva = false
      })
    },
    fechaEstadoDoc () {
      if (this.estadoDocEditar !== this.ParametrosCaso.EstadoDocumentacion) {
        this.ParametrosCaso.FechaDocumentacion = moment().format('DD-MM-YYYY')
      }
    },
    habilitarEdicion () {
      this.estadoDocEditar = this.ParametrosCaso.EstadoDocumentacion
      this.editar = true
    },
    setParPersona (p, id) {
      const i = this.personas.findIndex(p => p.IdPersona === id)
      this.personas[i].Parametros = p
    },
    guardarEdicion () {
      if (this.ParametrosCaso.EstadoDocumentacion === 'No se requiere') {
        this.ParametrosCaso.EstadoDocumentacion = ''
      }
      this.estadoDocEditar = ''
      this.editar = false
      request.Post(`/casos/parametros`, { Parametros: this.ParametrosCaso, IdCaso: this.id }, (r) => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.$q.notify('Los datos se actualizaron correctamente.')
        }
      })

      if (this.checkCP) {
        if (!this.CausaPenal.EstadoCausaPenal) {
          this.$q.notify('Debe seleccionar un estado de causa penal.')
          return
        }
        request.Post(`/casos/causa-penal`, { CausaPenal: JSON.stringify(this.CausaPenal), IdCaso: this.id }, (r) => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else {
            if (r.IdCausaPenalCaso) {
              this.CausaPenal.IdCausaPenalCaso = r.IdCausaPenalCaso
            }
            this.$q.notify('Los datos de causa penal se actualizaron correctamente.')
          }
        })
      }
    }
  }
}
</script>

<style>
.seccion-container {
  border: 5px solid;
  border-radius: 15px;
  padding: 1em;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  margin-bottom: 3em;
  position: relative;
}

.border-teal {
  border-color: #009688;
}

.border-blue-grey {
  border-color: #607d8b;
}

.seccion-titulo {
  font-size: 2em;
  font-weight: 600;
  text-align: center;
}

.seccion-item {
  margin: 2em 0px;
}

.seccion-item .fa-edit {
  cursor: pointer;
}

.item-titulo {
  font-size: 16px;
  font-weight: 500;
}

.botones-editar-container {
  position: absolute;
  top: 25px;
  right: 25px;
}

.seccion-item-editar {
  padding: 10px;
  width: 47%
}
</style>
