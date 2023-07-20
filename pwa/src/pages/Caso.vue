<template>
  <q-page>
    <div
      class="row justify-end q-my-sm"
    >
      <div class="col-12 col-sm flex justify-start full-height q-pl-sm" style="align-self: end;">
        <span class="text-grey text-bold">ID: {{ datos.IdCasoEstudio || '···' }}</span>

        <div class="q-mx-sm">
          <q-badge
            rounded
            :color="badgeEstado[datos.Estado || 'default'].color"
            :label="badgeEstado[datos.Estado || 'default'].label"
            text-color="white"
          />
          <q-tooltip anchor="bottom left" self="top middle" :offset="[10, 0]">
            Estado del caso
          </q-tooltip>
        </div>

        <div class="q-mx-sm">
          <q-badge
            rounded
            :color="datos.App ? 'positive' : 'negative'"
            label="APP"
            text-color="white"
          />
          <q-tooltip anchor="bottom left" self="top middle" :offset="[10, 0]">
            {{ datos.App ? 'Clientes con app descargada' : 'Aun no descarga la app ningun cliente' }}
          </q-tooltip>
        </div>
      </div>

      <div
        v-if="!editar && tab === 'caso'"
        class="col-12 col-sm-5 flex justify-center"
      >
        <!-- Editar -->
        <q-icon class="cursor-pointer" name="far fa-edit" color="dark" size="md" @click="editar = true">
          <q-tooltip>Editar datos</q-tooltip>
        </q-icon>
        <!-- Comentarios -->
        <q-icon class="cursor-pointer q-ml-md" name="o_forum" color="dark" size="md" @click="modal.comentarios = true">
          <q-tooltip>Comentarios</q-tooltip>
        </q-icon>
        <!-- Compartir -->
        <q-icon class="cursor-pointer" name="o_share" color="dark" right size="md" @click="modal.compartir = true">
          <q-tooltip>Compartir</q-tooltip>
        </q-icon>
        <!-- Comparticiones -->
        <q-icon class="cursor-pointer" name="folder_shared" color="dark" right size="md" @click="modal.comparticiones = true">
          <q-tooltip>Ver Comparticiones</q-tooltip>
        </q-icon>
        <!-- Vinculaciones -->
        <q-icon class="cursor-pointer" name="link" color="dark" right size="md" @click="modal.vinculaciones = true">
          <q-tooltip>Ver Vinculaciones</q-tooltip>
        </q-icon>
        <!-- Objetivos -->
        <q-icon class="cursor-pointer" name="o_timeline" color="dark" right size="md" @click="modal.verObjetivos = true">
          <q-tooltip>Ver Objetivos</q-tooltip>
        </q-icon>
        <!-- Movimientos -->
        <q-icon class="cursor-pointer" name="o_assignment" color="dark" right size="md" @click="modal.verMovimientos = true">
          <q-tooltip>Ver Movimientos</q-tooltip>
        </q-icon>
        <!-- Etiquetas -->
        <q-icon class="cursor-pointer" name="o_label" color="dark" right size="md" @click="modal.tags = true">
          <q-tooltip>Etiquetas del caso</q-tooltip>
        </q-icon>
        <!-- Archivar -->
        <q-icon class="cursor-pointer" name="o_archive" color="dark" right size="md" @click="modal.archivar = true">
          <q-tooltip>Cambiar Estado</q-tooltip>
        </q-icon>
        <!-- Duplicar -->
        <q-icon class="cursor-pointer" name="create_new_folder" color="dark" right size="md" @click="modal.duplicar = true">
          <q-tooltip>Duplicar Caso</q-tooltip>
        </q-icon>
        <!-- Eliminar -->
        <q-icon class="q-ml-sm cursor-pointer" name="fa fa-trash-alt" color="dark" size="md" @click="modal.eliminar = true">
          <q-tooltip>Eliminar Caso</q-tooltip>
        </q-icon>
      </div>
    </div>

    <!-- Tabs -->
    <div class="full-width row">
      <q-tabs
        v-model="tab"
        dense
        class="text-black margin-tabs col-12 col-md-7"
        active-color="dark"
        indicator-color="skyblue"
        align="left"
        narrow-indicator
      >
        <q-tab
          class="tab-class col-2"
          name="caso"
          label="Caso"
        />
        <q-tab
          class="tab-class col-3"
          name="archivos"
          label="Archivos"
        />
        <q-tab
          class="tab-class col-1"
          name="doc"
          label="Doc"
        />
        <q-tab
          class="tab-class col-2"
          name="datos"
          label="Datos"
        />
        <q-tab
          class="tab-class col-3"
          name="mediacion"
          label="Mediacion"
        />
      </q-tabs>

      <div
        class="col-12 col-md-5 row"
      >
        <div
          class="tab-class tab-datos q-tab__label q-tab col-6 col-md-3"
        >
          {{ nominacion(datos.Nominacion) }}
          <q-tooltip>Nominación</q-tooltip>
        </div>
        <div
          class="tab-class tab-datos q-tab__label q-tab col-6 col-md-3"
        >
          {{ datos.NroExpediente ? `N° ${datos.NroExpediente}` : 'Sin expediente'}}
          <q-tooltip>N° de Expediente</q-tooltip>
        </div>
      </div>
    </div>

    <q-tab-panels
      class="bg-transparent"
      v-model="tab"
      keep-alive
      animated
    >
      <q-tab-panel class="padding-top-cero" name="caso">
        <Caso
          :editar="editar"
          :modal="modal"
          @datos="setDatos"
          @personas="setPersonas"
          @cancelarEdicion="editar = false"
        />
      </q-tab-panel>
      <q-tab-panel class="padding-top-cero" name="archivos">
        <ArchivosCaso />
      </q-tab-panel>
      <q-tab-panel class="padding-cero" name="doc">
        <DocEditor />
      </q-tab-panel>
      <q-tab-panel name="datos">
        <DatosCaso />
      </q-tab-panel>
      <q-tab-panel name="mediacion">
        <Mediacion
          :IdMediacion="datos.IdMediacion"
          :IdCaso="datos.IdCaso"
          :IdChat="datos.IdChat"
          :Personas="personas"
          :CaratulaCaso="datos.Caratula"
          :VistaTab="true"
          @alta="altaMediacion"
        />
      </q-tab-panel>
    </q-tab-panels>

    <q-dialog v-model="modal.tags">
      <TagsCaso
        :IdCaso="$route.query.id"
        :EtiquetasCaso="datos.EtiquetasCaso || []"
      />
    </q-dialog>

    <q-dialog v-model="modal.comentarios">
      <ComentariosCaso
        :IdCaso="$route.query.id"
      />
    </q-dialog>

    <q-dialog v-model="modal.vinculaciones">
      <q-card style="min-width:800px;" class="q-pa-lg">
        <div v-if="casosVinculados.length > 0" class="full-width text-center">
          <b>Casos Vinculados</b>

          <q-table
            flat
            :data="casosVinculados"
            :columns="columnas"
            row-key="name"
          >
            <template v-slot:header="props">
                <q-tr :props="props">
                    <q-th
                        v-for="col in props.cols"
                        :key="col.name"
                        :props="props"
                    >
                        {{ col.label }}
                    </q-th>

                    <q-th>
                        Acciones
                    </q-th>
                </q-tr>
            </template>

            <template v-slot:body="props">
                <q-tr :props="props" :key="props.row.IdCaso">
                    <q-td
                        v-for="col in props.cols"
                        :key="col.name"
                        :props="props"
                    >
                        {{ col.value }}
                    </q-td>

                    <q-td align="center" class="flex">
                        <q-icon
                            name="delete"
                            size="sm"
                            color="red"
                            style="cursor:pointer; margin: auto"
                            @click="desvincular(props.row.IdCaso)"
                        >
                            <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                                <span class="text-body2">Desvincular</span>
                            </q-tooltip>
                        </q-icon>
                    </q-td>
                </q-tr>
            </template>
          </q-table>
        </div>
        <div class="full-width text-center">
          <b>Buscar Casos</b>

          <q-input style="font-size:12px; margin: auto; width: 100%" label="Busqueda" v-model="busqueda" />

          <q-btn
            class="q-mt-sm"
            style="font-size:12px; margin-left: auto; margin-right: auto;"
            label="Buscar"
            @click="buscarCasos"
            color="primary"
          />

          <Loading v-if="loading" />

          <q-table
            v-else
            flat
            :data="casosBuscados"
            :columns="columnas"
            row-key="name"
          >
            <template v-slot:header="props">
                <q-tr :props="props">
                    <q-th
                        v-for="col in props.cols"
                        :key="col.name"
                        :props="props"
                    >
                        {{ col.label }}
                    </q-th>

                    <q-th>
                        Acciones
                    </q-th>
                </q-tr>
            </template>

            <template v-slot:body="props">
                <q-tr :props="props" :key="props.row.IdCaso">
                    <q-td
                        v-for="col in props.cols"
                        :key="col.name"
                        :props="props"
                    >
                        {{ col.value }}
                    </q-td>

                    <q-td align="center" class="flex">
                        <q-icon
                            name="link"
                            size="sm"
                            color="green"
                            style="cursor:pointer; margin: auto"
                            @click="vincular(props.row.IdCaso)"
                        >
                            <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                                <span class="text-body2">Vincular</span>
                            </q-tooltip>
                        </q-icon>
                    </q-td>
                </q-tr>
            </template>
          </q-table>
        </div>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { QTabPanels, QTabPanel, QTab, QTabs } from 'quasar'
import Loading from '../components/Loading'
import Mediacion from '../components/Caso/Mediacion'
import Caso from '../components/Caso/Caso'
import ArchivosCaso from '../components/Caso/ArchivosCaso'
import DocEditor from '../components/Caso/DocEditor'
import DatosCaso from '../components/Caso/DatosCaso'
import ComentariosCaso from '../components/Caso/ComentariosCaso'
import TagsCaso from '../components/Compartidos/TagsCaso'
import request from '../request'

export default {
  name: 'CasoPage',
  components: {
    QTabPanels,
    QTabPanel,
    QTab,
    QTabs,
    Caso,
    ArchivosCaso,
    DocEditor,
    DatosCaso,
    Mediacion,
    TagsCaso,
    ComentariosCaso,
    Loading
  },
  data () {
    return {
      tab: this.$route.query.tab || 'caso',
      badgeEstado: {
        A: {
          color: 'positive',
          label: 'ACTIVO'
        },
        P: {
          color: 'negative',
          label: 'PENDIENTE'
        },
        R: {
          color: 'brown',
          label: 'ARCHIVADO'
        },
        E: {
          color: 'brown',
          label: 'ARCHIVO EX-ACTIVO'
        },
        F: {
          color: 'primary',
          label: 'FINALIZADO'
        },
        default: {
          color: 'black',
          label: 'Loading'
        }
      },
      datos: {},
      personas: [],
      editar: false,
      loading: false,
      busqueda: '',
      casosVinculados: [],
      casosBuscados: [],
      columnas: [
        {
          name: 'caratula',
          label: 'Caratula',
          field: 'Caratula',
          align: 'left'
        },
        {
          name: 'Juzgado',
          label: 'Tipo de Proceso',
          field: 'Juzgado',
          align: 'left'
        },
        {
          name: 'Estado',
          label: 'Estado de Tipo de Proceso',
          field: 'EstadoAmbitoGestion',
          align: 'left'
        }
      ],
      modal: {
        compartir: false,
        verObjetivos: false,
        verMovimientos: false,
        archivar: false,
        eliminar: false,
        comentarios: false,
        tags: false,
        duplicar: false,
        comparticiones: false,
        vinculaciones: false
      }
    }
  },
  created () {
    if (this.$route.query.modal === 'comentarios') {
      this.modal.comentarios = true
    }

    request.Get('/casos/casos-vinculados', { IdCaso: this.$route.query.id }, (r) => {
      this.casosVinculados = r
    })
  },
  methods: {
    setDatos (d) {
      this.editar = false
      this.datos = d
    },
    setPersonas (p) {
      this.personas = p
    },
    nominacion (nom) {
      const n = {
        0: 'Pendiente',
        1: 'I Nominación',
        2: 'II Nominación',
        3: 'III Nominación',
        4: 'IV Nominación',
        5: 'V Nominación',
        6: 'VI Nominación',
        7: 'VII Nominación',
        8: 'VIII Nominación',
        9: 'IX Nominación'
      }
      const i = nom ? nom.slice(0, 1) : '-'
      return n[i] ? n[i] : 'Sin Nominación'
    },
    altaMediacion (id) {
      this.datos.IdMediacion = id
    },
    buscarCasos () {
      if (!this.busqueda) return

      this.loading = true

      request.Get('/casos/buscar', { Cadena: this.busqueda, Tipo: 'T', Limit: 50 }, (r) => {
        this.casosBuscados = r
        this.loading = false
      })
    },
    vincular (id) {
      request.Post('/casos/vincular-caso', { IdCaso: this.datos.IdCaso, IdCasoVinculado: id }, (r) => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.$q.notify('Caso vinculado correctamente')

          const caso = this.casosBuscados.find(c => c.IdCaso === id)

          this.casosVinculados.push(caso)
        }
      })
    },
    desvincular (id) {
      request.Post('/casos/desvincular-caso', { IdCaso: this.datos.IdCaso, IdCasoVinculado: id }, (r) => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.$q.notify('Caso desvinculado correctamente')

          this.casosVinculados = this.casosVinculados.filter(c => c.IdCaso !== id)
        }
      })
    }
  }
}
</script>

<style>
.tab-class {
  background : -moz-linear-gradient(50% 100% 90deg,rgba(255, 255, 255, 1) 0%,rgba(253, 253, 253, 1) 30.7%,rgba(244, 244, 244, 1) 48.88%,rgba(230, 230, 230, 1) 63.81%,rgba(210, 210, 210, 1) 76.96%,rgba(184, 184, 184, 1) 88.83%,rgba(153, 153, 153, 1) 99.83%,rgba(152, 152, 152, 1) 100%);
  background : -webkit-linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(253, 253, 253, 1) 30.7%, rgba(244, 244, 244, 1) 48.88%, rgba(230, 230, 230, 1) 63.81%, rgba(210, 210, 210, 1) 76.96%, rgba(184, 184, 184, 1) 88.83%, rgba(153, 153, 153, 1) 99.83%, rgba(152, 152, 152, 1) 100%);
  background : -webkit-gradient(linear,50% 100% ,50% 0% ,color-stop(0,rgba(255, 255, 255, 1) ),color-stop(0.307,rgba(253, 253, 253, 1) ),color-stop(0.4888,rgba(244, 244, 244, 1) ),color-stop(0.6381,rgba(230, 230, 230, 1) ),color-stop(0.7696,rgba(210, 210, 210, 1) ),color-stop(0.8883,rgba(184, 184, 184, 1) ),color-stop(0.9983,rgba(153, 153, 153, 1) ),color-stop(1,rgba(152, 152, 152, 1) ));
  background : -o-linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(253, 253, 253, 1) 30.7%, rgba(244, 244, 244, 1) 48.88%, rgba(230, 230, 230, 1) 63.81%, rgba(210, 210, 210, 1) 76.96%, rgba(184, 184, 184, 1) 88.83%, rgba(153, 153, 153, 1) 99.83%, rgba(152, 152, 152, 1) 100%);
  background : -ms-linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(253, 253, 253, 1) 30.7%, rgba(244, 244, 244, 1) 48.88%, rgba(230, 230, 230, 1) 63.81%, rgba(210, 210, 210, 1) 76.96%, rgba(184, 184, 184, 1) 88.83%, rgba(153, 153, 153, 1) 99.83%, rgba(152, 152, 152, 1) 100%);
  -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#989898' ,GradientType=0)";
  background : linear-gradient(0deg, rgba(255, 255, 255, 1) 0%, rgba(253, 253, 253, 1) 30.7%, rgba(244, 244, 244, 1) 48.88%, rgba(230, 230, 230, 1) 63.81%, rgba(210, 210, 210, 1) 76.96%, rgba(184, 184, 184, 1) 88.83%, rgba(153, 153, 153, 1) 99.83%, rgba(152, 152, 152, 1) 100%);
  opacity: 0.5;
  margin: auto;
  border-radius : 5px 5px 0 0;
  filter: alpha(opacity=50) progid:DXImageTransform.Microsoft.Alpha(opacity=50) progid:DXImageTransform.Microsoft.gradient(startColorstr='#989898',endColorstr='#FFFFFF' , GradientType=0);
}

.tab-datos {
  min-height: 36px;
  text-align: center;
  padding: 4px 16px;
}

.padding-top-cero {
  padding-top: 0px;
}

.padding-cero {
  padding: 0px;
}

.margin-tabs {
  margin-top: 5px;
}
</style>
