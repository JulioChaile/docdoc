<template>
  <q-page class="flex">
    <q-stepper
      ref="stepper"
      vertical
      contractable
      style="width: 100vw, justify-content: center; background:none;"
      no-header-navigation
      v-model="stepActual"
      animated
    >
      <!-- Step 1: Seleccionar Competencia del Caso-->
      <q-step
        default
        name="competencia"
        title="Competencia"
        caption="Seleccione la competencia del caso"
        style="width: 100vw;"
      >
        <div v-if="Competencias === []">
          <Loading />
        </div>
        <div v-else style="display: flex; flex-flow: row wrap; justify-content: space-evenly;">
          <q-btn
            v-for="(Competencia, index) in Competencias"
            :key="Competencia.IdCompetencia"
            rounded
            :icon="(ParamsJuzgados[Competencia.Competencia] || {icon:''}).icon"
            style="background: #FF0000"
            :color="decisiones.competencia === Competencia.Competencia ? 'warning' : 'primary'"
            size="lg"
            :label="Competencia.Competencia"
            @click="seleccionar_competencia(Competencia), $refs.stepper.next()"
            :style="index+1 < Competencias.length ? 'margin: 10px;': ''"
          />
        </div>
        <q-stepper-navigation>
          <q-btn
            @click="$refs.stepper.next()"
            label="Siguiente"
            v-bind:disabled="!decisiones.competencia"
            color="primary"
            style="display:none"
          />
        </q-stepper-navigation>
      </q-step>
      <!-- Step 2: Selección del Tipo de Caso -->
      <q-step
        name="tipo_caso"
        active-icon
        title="Tipo de Caso"
        caption="Seleccione el Tipo de Caso"
      >
        <!--TiposCaso
          @set-tipoCaso="seleccionar_subcompetencia($event), $refs.stepper.next(), step = 3"
          :tipoCaso="this.decisiones.subcompetencia"
        /-->
        <div v-if="TiposCaso.length === 0">
          <Loading />
        </div>
        <div
          v-else
          style="display: flex; flex-flow: row wrap; justify-content: space-evenly; margin-bottom: 100px;"
        >
          <q-btn
            v-for="tc in TiposCaso"
            :key="tc.IdTipoCaso"
            rounded
            :icon="ParamsTipoCaso[tc.TipoCaso] ? ParamsTipoCaso[tc.TipoCaso].icon : ''"
            v-bind:color="tipoCaso === tc.TipoCaso ? 'warning' : 'primary'"
            size="lg"
            :label="tc.TipoCaso"
            @click="seleccionar_subcompetencia(tc), $refs.stepper.next(), step = 3"
            style="margin: 10px;"
          />
        </div>
        <q-stepper-navigation>
          <q-btn
            @click="$refs.stepper.previous()"
            label="Atrás"
            style="margin-right: 10px;"
            icon="arrow_back_ios"
            color="warning"
          />
        </q-stepper-navigation>
      </q-step>
      <!-- Step 3: Seleccion del modo de gestion -->
      <q-step
        name="modogestion"
        active-icon
        title="Modo de Gestión"
        caption="Seleccione el modo de gestión del caso"
      >
        <div
          style="display: flex;flex-flow: row wrap; justify-content: space-evenly; margin-bottom: 50px;"
        >
          <q-btn
            rounded
            color="primary"
            icon="mdi-gavel"
            size="lg"
            label="Judicial"
            style="margin: 10px; width:300px;"
            @click="seleccionarModoGestion('J'), $refs.stepper.next(), step = 4"
          />
          <q-btn
            rounded
            color="primary"
            icon="fas fa-user-tie"
            size="lg"
            label="Extrajudicial"
            style="margin: 10px; width:300px;"
            @click="seleccionarModoGestion('E'), $refs.stepper.next(), step = 4"
          />
        </div>
        <div>
          <q-stepper-navigation>
            <q-btn
              @click="$refs.stepper.previous()"
              label="Atrás"
              style="margin-right: 10px;"
              icon="arrow_back_ios"
              color="warning"
            />
          </q-stepper-navigation>
        </div>
      </q-step>
      <!-- Step 4: Seleccion del ambito de gestion -->
      <q-step
        name="ambitogestion"
        active-icon
        title="Ámbito de Gestión"
        caption="Seleccione el ámbito de gestión del caso"
      >
        <div v-if="juzgados.length === 0">
          <Loading />
        </div>
        <div
          v-else
          style="display: flex; flex-flow: row wrap; justify-content: space-evenly; margin-bottom: 100px;"
        >
          <q-btn
            v-for="j in juzgados"
            :key="j.IdJuzgado"
            rounded
            v-bind:color="'primary'"
            size="lg"
            :label="j.Juzgado"
            @click="seleccionarJuzgados(j), $refs.stepper.next(), step = 5"
            style="margin: 10px;"
          />
        </div>
        <div>
          <q-stepper-navigation>
            <q-btn
              @click="$refs.stepper.previous()"
              label="Atrás"
              style="margin-right: 10px;"
              icon="arrow_back_ios"
              color="warning"
            />
          </q-stepper-navigation>
        </div>
      </q-step>
      <!-- Step 5: Seleccion del estado de ambito de gestion -->
      <q-step
        name="estadoambitogestion"
        active-icon
        title="Estado de Ámbito de Gestión"
        caption="Seleccione el estado de ámbito de gestión del caso"
      >
        <div v-if="!estados">
          <Loading />
        </div>
        <div
          v-else-if="estados === 'Sin estados'"
        >
          No hay estados cargados en el sistema
          <q-btn
            v-bind:color="'primary'"
            size="lg"
            :label="'Continuar'"
            @click="guardarEstado(''), $refs.stepper.next(), step = 6"
            style="margin: 10px;"
          />
        </div>
        <div
          v-else
          style="display: flex; flex-flow: row wrap; justify-content: space-evenly; margin-bottom: 100px;"
        >
          <q-btn
            v-for="e in estados"
            :key="e.IdEstadoAmbitoGestion"
            rounded
            v-bind:color="'primary'"
            size="lg"
            :label="e.EstadoAmbitoGestion"
            @click="guardarEstado(e), $refs.stepper.next(), step = 6"
            style="margin: 10px;"
          />
        </div>
        <div>
          <q-stepper-navigation>
            <q-btn
              @click="$refs.stepper.previous()"
              label="Atrás"
              style="margin-right: 10px;"
              icon="arrow_back_ios"
              color="warning"
            />
          </q-stepper-navigation>
        </div>
      </q-step>
      <!-- Step 6: Indicar el origen del caso -->
      <q-step name="origen" active-icon title="Origen" caption="Indique el origen del caso">
        <div>
          <q-select v-model="origenesModel" :options="origenes" label="Origenes" />
        </div>
        <q-stepper-navigation>
          <q-btn
            @click="$refs.stepper.previous()"
            label="Atrás"
            style="margin-right: 10px;"
            icon="arrow_back_ios"
            color="warning"
          />
          <q-btn
            label="Siguiente"
            color="primary"
            @click="guardarOrigen(), $refs.stepper.next(), step = 7"
          />
        </q-stepper-navigation>
      </q-step>
      <!-- Step 7: Ingreso de datos de los Actores -->
      <q-step
        name="involucrados"
        active-icon="person"
        title="Involucrados"
        caption="Ingrese datos de las personas relacionadas al caso"
      >
        <div
          style="display: flex;flex-flow: row wrap; justify-content: space-evenly; margin-bottom: 50px;"
        >
          <div>
            <q-btn
              rounded
              color="primary"
              icon="person_add"
              size="md"
              v-bind:label="`Actores (${decisiones.actores.length})`"
              @click="form_actores = true, rolPrimario = 'actor', $refs.stepper.next()"
              style="margin: 10px;"
            />
            <div v-for="actor in this.decisiones.actores" :key="actor.Documento">
              <q-expansion-item
                icon="perm_identity"
                :label="actor.EsJuridica === 'N' ? actor.Nombres + ' ' + (actor.Apellidos || '') : actor.Nombres"
              >
                <div v-if="actor.EsJuridica === 'S'">
                  <div v-for="Prop in FormActores.Juridica" :key="Prop">
                    <span class="propiedad_nombre">{{ Prop }}:</span>
                    <span class="propiedad_valor">{{ actor[Prop] || '-' }}</span>
                  </div>
                </div>
                <div v-if="actor.EsJuridica === 'N'">
                  <div v-for="Prop in FormActores.NoJuridica" :key="Prop">
                    <span class="propiedad_nombre">{{ Prop }}:</span>
                    <span class="propiedad_valor">{{ actor[Prop] || '-' }}</span>
                  </div>
                </div>
                <q-expansion-item label="Más Datos Personales">
                  <div v-for="Prop in FormActores.MasDatos" :key="Prop">
                    <span class="propiedad_nombre">{{ Prop }}:</span>
                    <span class="propiedad_valor">{{ actor[Prop] || '-'}}</span>
                  </div>
                </q-expansion-item>
                <div
                  v-for="(parametros, categoria) in actor.Categorias"
                  :key="categoria.Parametros"
                >
                  <q-expansion-item :label="categoria" style="margin-top:2vw">
                    <div v-for="parametro in parametros" :key="parametro">
                      <span class="propiedad_nombre">{{ parametro.Descripcion }}:</span>
                      <span class="propiedad_valor">{{ parametro.Valor || '-'}}</span>
                    </div>
                  </q-expansion-item>
                </div>
                <q-btn
                  style="margin-top: 2vw; margin-bottom:2vw"
                  color="primary"
                  rounded
                  size="sm"
                  @click="editarPersona(decisiones.actores.indexOf(actor), actor, 'a'), $refs.stepper.goTo('datos_persona')"
                  icon="edit"
                  label="Editar"
                />
                <q-btn
                  style="margin-top: 2vw; margin-bottom:2vw"
                  color="warning"
                  rounded
                  size="sm"
                  @click="borrarPersona(decisiones.actores.indexOf(actor), 'a')"
                  icon="delete"
                  label="Eliminar"
                />
              </q-expansion-item>
            </div>
          </div>
          <div>
            <q-btn
              rounded
              color="primary"
              icon="person_add"
              size="md"
              v-bind:label="`Demandados (${decisiones.demandados.length})`"
              @click="form_actores = true, rolPrimario = 'demandado', $refs.stepper.next()"
              style="margin: 10px;"
            />
            <div v-for="demandado in this.decisiones.demandados" :key="demandado.Documento">
              <q-expansion-item
                icon="perm_identity"
                :label="demandado.EsJuridica === 'N' ? demandado.Nombres + ' ' + (demandado.Apellidos || '') : demandado.Nombres"
              >
                <div v-if="demandado.EsJuridica === 'S'">
                  <div v-for="Prop in FormActores.Juridica" :key="Prop">
                    <span class="propiedad_nombre">{{ Prop }}:</span>
                    <span class="propiedad_valor">{{ demandado[Prop] || '-' }}</span>
                  </div>
                </div>
                <div v-if="demandado.EsJuridica === 'N'">
                  <div v-for="Prop in FormActores.NoJuridica" :key="Prop">
                    <span class="propiedad_nombre">{{ Prop }}:</span>
                    <span class="propiedad_valor">{{ demandado[Prop] || '-' }}</span>
                  </div>
                </div>
                <q-expansion-item label="Más Datos Personales">
                  <div v-for="Prop in FormActores.MasDatos" :key="Prop">
                    <span class="propiedad_nombre">{{ Prop }}:</span>
                    <span class="propiedad_valor">{{ demandado[Prop] || '-'}}</span>
                  </div>
                </q-expansion-item>
                <div
                  v-for="(parametros, categoria) in demandado.Categorias"
                  :key="categoria.Parametros"
                >
                  <q-expansion-item :label="categoria" style="margin-top:2vw">
                    <div v-for="parametro in parametros" :key="parametro">
                      <span class="propiedad_nombre">{{ parametro.Descripcion }}:</span>
                      <span class="propiedad_valor">{{ parametro.Valor || '-'}}</span>
                    </div>
                  </q-expansion-item>
                </div>
                <q-btn
                  style="margin-top: 2vw; margin-bottom:2vw"
                  color="primary"
                  rounded
                  size="sm"
                  @click="editarPersona(decisiones.demandados.indexOf(demandado), demandado, 'd'), $refs.stepper.goTo('datos_persona')"
                  icon="edit"
                  label="Editar"
                />
                <q-btn
                  style="margin-top: 2vw; margin-bottom:2vw"
                  color="warning"
                  rounded
                  size="sm"
                  @click="borrarPersona(decisiones.actores.indexOf(demandado), 'd')"
                  icon="delete"
                  label="Eliminar"
                />
              </q-expansion-item>
            </div>
          </div>
        </div>
        <q-stepper-navigation>
          <q-btn
            @click="$refs.stepper.previous()"
            label="Atrás"
            style="margin-right: 10px;"
            icon="arrow_back_ios"
            color="warning"
          />
          <q-btn
            @click="$refs.stepper.goTo('resumen')"
            label="Siguiente"
            v-bind:disabled="siguiente_deshabilitado || decisiones.actores.length === 0"
            color="primary"
            icon-right="arrow_forward_ios"
          />
        </q-stepper-navigation>
      </q-step>
      <!-- Step 8: Formulario de ingreso de datos de los Actores -->
      <q-step
        name="datos_persona"
        active-icon
        title="Formulario de ingreso de personas"
        caption="Ingrese los datos de la persona"
      >
        <StepPersona
          v-bind:personaeditar="persona"
          v-bind:editarPersona="editar"
          v-if="form_actores === true"
          @agregar-persona="set_persona($event), $refs.stepper.previous()"
          v-on:volver="$refs.stepper.previous()"
          :idTipoCaso="tipoCaso.IdTipoCaso"
          :rol="rolPrimario"
        />
      </q-step>
      <!-- Step 9: Resumen de datos ingresados -->
      <q-step
        name="resumen"
        active-icon
        title="Nominación"
        caption="Seleccione la nominación a la que pertenecerá el caso (Esto es opcional)"
        style="margin-bottom:2.5em"
      >
        <!--
        <ResumenCaso
          v-bind:actores="decisiones.actores"
          v-bind:demandados="decisiones.demandados"
        />
        -->
        <div
          style="display: flex; flex-flow: row wrap; justify-content: space-evenly; margin-top: 2rem;"
        >
          <q-btn
            v-for="(Nominacion) in Nominaciones"
            :key="Nominacion.IdNominacion"
            rounded
            style="background: #FF0000; margin-bottom: 2rem;"
            :color="decisiones.idnominacion === Nominacion.IdNominacion ? 'warning' : 'primary'"
            size="lg"
            :label="Nominacion.Nominacion"
            @click="seleccionar_nominacion(Nominacion.IdNominacion)"
          />
        </div>
        <q-stepper-navigation>
          <q-btn
            @click="$refs.stepper.goTo('involucrados')"
            label="Atrás"
            style="margin-right: 10px;"
            color="warning"
            icon="arrow_back_ios"
          />
          <q-btn
            label="Guardar"
            v-bind:disabled="siguiente_deshabilitado"
            color="primary"
            @click="enviar_datos(true)"
            icon-right="save"
          />
        </q-stepper-navigation>
      </q-step>
    </q-stepper>
  </q-page>
</template>

<script>
import ParamsTipoCaso from '../parameters/TiposCaso'
import ParamsJuzgados from '../parameters/Juzgados'
import request from '../request'
import TiposCaso from '../components/TiposCaso.vue'
import StepPersona from '../components/StepPersona.vue'
import ResumenCaso from '../components/ResumenCaso.vue'
import Loading from '../components/Loading.vue'
import auth from '../auth'
import { Notify } from 'quasar'

export default {
  name: 'AltaCasos',
  props: [
    'JuzgadoProp',
    'TipoCasoProp',
    'ActoresProp',
    'DemandadosProp',
    'editarCaso',
    'IdCaso'
  ],
  components: {
    TiposCaso,
    StepPersona,
    ResumenCaso,
    Loading
  },
  data () {
    return {
      IdEstudio: '',
      checkEnviarDatos: false,
      stepActual: 'competencia',
      form_actores: false,
      rolPrimario: '',
      decisiones: {
        competencia: '',
        idcompetencia: 0,
        idnominacion: 0,
        subcompetencia: '',
        actores: [],
        demandados: []
      },
      juzgados: [],
      TiposCaso: [],
      categorias: {},
      ParamsJuzgados,
      ParamsTipoCaso,
      siguiente_deshabilitado: true,
      editar: false,
      IdOrigen: '',
      origenes: [],
      origenesRespaldo: [],
      origenesModel: '',
      persona: {},
      index: 0,
      Competencias: [],
      Nominaciones: [],
      FormActores: {
        NoJuridica: [
          'Apellidos',
          'Nombres',
          'Documento',
          'Telefonos',
          'Domicilio'
        ],
        Juridica: ['Nombres', 'CUIT', 'Telefonos', 'Domicilio'],
        MasDatos: ['FechaNacimiento', 'Sexo', 'EstadoCivil', 'Oficio']
      },
      tiposCaso: [],
      tipoCaso: {},
      estados: []
    }
  },
  created () {
    this.IdEstudio = auth.UsuarioLogueado.IdEstudio
    request.Get('/competencias', {}, (r) => {
      if (r.Error) {
        Notify.create(r.Error)
      } else {
        this.Competencias = r
      }
    })
  },
  mounted () {
    if (this.editarCaso) {
      this.decisiones.competencia = this.JuzgadoProp
      this.decisiones.subcompetencia = this.TipoCasoProp
    }
  },
  methods: {
    seleccionarJuzgados (j) {
      this.nullEstados = false
      this.decisiones.idJuzgado = j.IdJuzgado
      this.Nominaciones = []
      let IdsJuzgado = []
      IdsJuzgado.push(this.decisiones.idJuzgado)
      request.Get(
        `/nominaciones?IdsJuzgado=${JSON.stringify(IdsJuzgado)}&IncluyeBajas=N`,
        {},
        (n) => {
          if (n.Error) {
            Notify.create(n.Error)
          } else {
            this.Nominaciones = n[this.decisiones.idJuzgado]
          }
        }
      )
      request.Get(`/estado-ambito-gestion/estados-juzgado?id=${j.IdJuzgado}`, {}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.estados = r.EstadoAmbitoGestion ? r.EstadoAmbitoGestion : 'Sin estados'
        }
      })
      // Traigo origenes para el proximo paso del stepper
      request.Get(`/origenes`, {}, (r) => {
        if (!r.Error) {
          Object.keys(r).forEach(k => {
            this.origenes.push(r[k].Origen)
            this.origenesRespaldo.push(r[k])
          })
        } else {
          Notify.create(r.Error)
        }
      })
    },
    seleccionarModoGestion (mg) {
      const IdTipoCaso = this.tipoCaso.IdTipoCaso
      this.juzgados = []
      request.Get(`/tipos-caso/${IdTipoCaso}/juzgados`, {}, (r) => {
        if (!r.Error) {
          r.Juzgados.forEach((j) => {
            if (j.ModoGestion === mg.toString()) {
              this.juzgados.push(j)
            }
          })
        }
      })
    },
    guardarEstado (e) {
      this.decisiones.EstadoAmbitoGestion = e
    },
    guardarOrigen () {
      // Identifico el IdOrigen segun la opcion seleccionada por el usuario y lo guardo en IdOrigen.
      this.origenesRespaldo.forEach(o => {
        if (o.Origen === this.origenesModel) {
          this.IdOrigen = o.IdOrigen
        }
      })
    },
    cargarNominaciones () {
      this.Competencias.forEach((j) => {
        /*
        request.Get(`/nominaciones?IdJuzgado=${j.IdJuzgado}&IncluyeBajas=N`, {}, n => {
          if (n.Error) {
            Notify.create(n.Error)
          } else {
            if (n.length > 0) {
              n.forEach(nominacion => {
                this.Nominaciones.push(nominacion)
              })
            }
          }
        })
        */
      })
    },
    seleccionar_competencia (Competencia) {
      this.decisiones = {
        competencia: '',
        idcompetencia: 0,
        idnominacion: 0,
        subcompetencia: '',
        actores: [],
        demandados: []
      }
      this.TiposCaso = []
      this.decisiones.competencia = Competencia.Competencia
      this.decisiones.idcompetencia = Competencia.IdCompetencia

      Object.entries(Competencia.TiposCaso).forEach(
        ([IdTipoCaso, TipoCaso]) => {
          if (TipoCaso.Estado === 'A') {
            let tipocaso = {
              IdTipoCaso: IdTipoCaso,
              TipoCaso: TipoCaso.TipoCaso
            }
            this.TiposCaso.push(tipocaso)
          }
        }
      )
      this.deberia_estar_deshabilitado()
    },
    seleccionar_nominacion (id) {
      this.decisiones.idnominacion = id
    },
    deberia_estar_deshabilitado () {
      this.siguiente_deshabilitado = this.decisiones.competencia === ''
    },
    seleccionar_subcompetencia (subcompetencia) {
      this.decisiones.subcompetencia = subcompetencia.TipoCaso
      this.tipoCaso = subcompetencia
    },
    set_persona (persona) {
      if (this.editar) {
        if (this.rolPrimario === 'actor') {
          this.decisiones.actores[this.index] = persona
        } else {
          this.decisiones.demandados[this.index] = persona
        }
      } else {
        persona.Parametros.forEach((p) => {
          if (!this.categorias[p.Categoria]) {
            this.categorias[p.Categoria] = []
          }
          this.categorias[p.Categoria].push(p)
        })
        persona.Categorias = this.categorias
        this.categorias = {}
        if (this.rolPrimario === 'actor') {
          persona.Observaciones = 'Actor'
          console.log(persona)
          this.decisiones.actores.push(persona)
        } else {
          persona.Observaciones = 'Demandado'
          this.decisiones.demandados.push(persona)
        }
      }
      this.persona = {}
      this.editar = false
    },
    editarPersona (indice, personaeditar, rol) {
      if (rol === 'a') {
        this.rolPrimario = 'actor'
      } else {
        this.rolPrimario = 'demandado'
      }
      this.index = indice
      this.editar = true
      this.persona = personaeditar
    },
    borrarPersona (indice, rol) {
      if (rol === 'a') {
        this.decisiones.actores.splice(indice, 1)
      } else {
        this.decisiones.demandados.splice(indice, 1)
      }
    },
    enviar_datos (check) {
      if (this.checkEnviarDatos) {
        return
      }
      this.checkEnviarDatos = check
      let personas = []
      let personasCaso = []
      let principal = true
      let personaprincipal = ''
      personasCaso = this.decisiones.actores.concat(this.decisiones.demandados)
      personasCaso.forEach((p) => {
        let parametrosPersona = []
        p.Parametros.forEach((par) => {
          let param = {
            Descripcion: par.Descripcion,
            TipoDato: par.TipoDato,
            Parametro: par.Parametro,
            Obligatorio: par.Obligatorio,
            Valor: par.Valor
          }
          parametrosPersona.push(param)
        })
        let persona = {
          Nombres: p.Nombres,
          Apellidos: p.Apellidos,
          Domicilio: p.Domicilio,
          Email: p.Email,
          TipoPersona: p.EsJuridica === 'S' ? 'J' : 'F',
          // IdRTC: p.IdRTC,
          Documento: p.Documento,
          CUIT: p.CUIT,
          Observaciones: p.Observaciones,
          Telefonos: p.Telefonos,
          EsPrincipal: principal === true ? 'S' : 'N',
          Parametros: parametrosPersona
        }
        if (principal) {
          personaprincipal =
            (persona.Apellidos ? persona.Apellidos + ', ' : '') +
            persona.Nombres
          principal = false
        }
        personas.push(persona)
      })
      let caso = {
        IdCompetencia: this.decisiones.idcompetencia,
        IdNominacion: this.decisiones.idnominacion,
        IdTipoCaso: this.tipoCaso.IdTipoCaso,
        IdJuzgado: this.decisiones.idJuzgado,
        IdEstadoAmbitoGestion: this.decisiones.EstadoAmbitoGestion.IdEstadoAmbitoGestion ? this.decisiones.EstadoAmbitoGestion.IdEstadoAmbitoGestion : null,
        IdEstadoCaso: '',
        IdOrigen: this.IdOrigen,
        Caratula: personaprincipal,
        NroExpediente: '',
        Carpeta: '',
        Observaciones: '',
        PersonasCaso: personas
      }
      Notify.create(this.editarCaso)
      request.Post('/casos', caso, (r) => {
        if (r.Error) {
          this.checkEnviarDatos = false
          Notify.create(r.Error)
        } else {
          /*
          if (this.decisiones.idnominacion) {
            this.$router.push({
              name: 'Tribunales',
              params: {
                idCompetencia: this.decisiones.idcompetencia,
                idNominacion: this.decisiones.idnominacion,
                Caratula: personaprincipal
              }
            })
          } else {
            this.$router.push('Casos')
          }
          */
          this.$router.push(`/Caso?id=${r.IdCaso}`)
        }
      })
    }
  }
}
</script>

<style>
</style>
