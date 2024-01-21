<template>
    <q-page class="q-px-lg">
        <input id="inputCedulas" style="visibility: hidden" type="file" @change="subirExcel" />

        <div class="full-width flex column items-center">
            <q-btn color="teal" @click="importar">Importar Datos</q-btn>

            <q-input v-if="!todasFechas" ref="inputFechaEsperada" label="Fecha" @change="getCedulas()" v-model="Fecha" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:30%;">
                <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                            <q-date mask="DD-MM-YYYY" v-model="Fecha" @input="() => $refs.qDateProxy1.hide()" @change="getCedulas()" />
                        </q-popup-proxy>
                    </q-icon>
                </template>
            </q-input>

            <q-select
                style="width:30%;"
                v-model="estado"
                :options="estados"
                label="Estado Ambito Gestion"
            />

            <q-input
                style="width:30%;"
                v-model="id"
                type="text"
                dense
                label="Id"
            />

            <q-checkbox
              v-model="ordenEstado"
              label="Ordenar Por Estado"
              @input="cambiarOrden"
              class="align-center"
            />

            <div class="flex">
              <q-checkbox
                v-model="todasFechas"
                label="Buscar todas las fechas"
                class="align-center"
              />
              <q-checkbox
                v-model="checkeado"
                label="Sin Checkear"
                class="align-center"
              />
            </div>

            

            <q-input
                style="width:30%;"
                v-model="nroExp"
                type="text"
                dense
                label="Nro Expediente"
            />
            
            <q-btn class="q-mt-lg" color="positive" @click="getCedulas">Buscar</q-btn>

            <q-btn class="q-mt-lg" color="positive" v-if="Cedulas.filter(c => c.CheckModel).length > 0" @click="finalizar">Finalizar</q-btn>
        </div>

        <div class="full-width flex justify-center" v-if="loading">
            <Loading />
        </div>

        <div class="full-width text-center text-bold q-mt-lg" v-else-if="Cedulas.length === 0">
            No existen cedulas cargadas con los filtros seleccionados
        </div>

        <div v-else>
          <div
            class="row titulos_container q-banner"
          >
            <div class="col-1 casilla_container">
                Checkeado
            </div>
            <div
              class="col-1 casilla_container"
            >
              Id
            </div>
            <div
              class="col-1 casilla_container"
            >
              Usuario
            </div>
            <div
              class="col-1 casilla_container"
            >
              Fecha
            </div>
            <div
              class="col-1 casilla_container"
            >
              Expediente
            </div>
            <div
              class="col casilla_container"
            >
                Descripcion
            </div>
            <div
              class="col casilla_container"
            >
                Tipo de Escrito
            </div>
            <div
              class="col casilla_container"
            >
              Nominacion
            </div>
            <div
              class="col casilla_container"
            >
              Caratula
            </div>
            <div
              class="col casilla_container"
            >
              Estado
            </div>
          </div>

          <div
            v-for="c in filtrarCedulas()"
            :key="c.IdCedula + 'force' + force"
            class="filas_container q-banner"
          >
            <div class="row filas">
              <div
                class="col-1"
              >
                <q-checkbox
                    v-if="c.Check === 'N'"
                    :value="c.CheckModel"
                    @input="cambiarEstado(c)"
                    class="align-center"
                />
                <q-icon
                    v-else-if="c.Check === 'S'"
                    name="r_check_circle"
                    size="sm"
                    color="positive"
                />
              </div>
              <div
                class="col-1"
              >
                {{c.IdCorrelativo}}
              </div>
              <div
                class="col-1"
              >
                {{c.Usuario || 'Sin checkear'}}
              </div>
              <div
                class="col-1"
              >
                {{ fecha(c.FechaCedula) }}
              </div>

              <div
                class="col-1"
              >
                {{c.NroExpediente}}
              </div>
              <div
                class="col"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Descripcion</q-tooltip>
                {{ c.Descripcion }}
              </div>
              <div
                class="col"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Tipo de Escrito</q-tooltip>
                {{ c.TipoEscrito }}
              </div>
              <div
                class="col column"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Nominacion</q-tooltip>
                {{ c.Nominacion }}
              </div>
              <div
                class="col column cursor-pointer"
                  @click="c.IdCaso && abrirCaso(c.IdCaso)"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Abrir Caso</q-tooltip>
                {{ c.Caratula || 'Sin caso asociado' }}
              </div>
              <div
                class="col column"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Estado de Proceso</q-tooltip>
                {{ c.EstadoAmbitoGestion || 'Sin caso asociado' }}
              </div>
            </div>
          </div>
        </div>
    </q-page>
</template>

<script>
import XLSX from 'xlsx'
import moment from 'moment'
import auth from '../auth'
import request from '../request'
import Loading from '../components/Loading'

export default {
    name: 'Cedulas',
    components: { Loading },
    data () {
        return {
            Fecha: moment().format('DD-MM-YYYY'),
            Cedulas: [],
            loading: true,
            force: 1,
            id: '',
            estado: '',
            estados: [],
            ordenEstado: false,
            todasFechas: false,
            checkeado: false,
            nroExp: ''
        }
    },
    created() {
        this.getCedulas()
    },
    watch: {
        Fecha() {
            this.id = ''
            this.estado = ''
            this.getCedulas()
        }
    },
    methods: {
        subirExcel (e) {
            const file = e.target.files ? e.target.files[0] : null;

            if (file) {
                const reader = new FileReader();

                this.loading = true

                reader.onload = (e) => {
                    /* Parse data */
                    const bstr = e.target.result;
                    const wb = XLSX.read(bstr, { type: 'binary' });
                    /* Get first worksheet */
                    const wsname = wb.SheetNames[0];
                    const ws = wb.Sheets[wsname];
                    /* Convert array of arrays */
                    const data = XLSX.utils.sheet_to_json(ws, { header: 1 });

                    data.forEach(row => {
                        row[0] = this.ExcelDateToJSDate(row[0])
                    })

                    request.Post('/cedulas/alta-cedulas', { cedulas: JSON.stringify(data) }, r => {
                        this.loading = false
                        this.Cedulas = r
                        this.Cedulas.forEach(c => c.CheckModel = c.Check === 'S')

                        this.estados = [ ...new Set(this.Cedulas.map(c => c.EstadoAmbitoGestion).filter(e => {
                            if (e) {
                                return e
                            }
                        })) ]
                        this.estados.push('Todos')
                        this.estados.unshift('Sin estado')

                        this.estado = 'Todos'
                        this.id = ''
                    })
                }

                reader.readAsBinaryString(file);
            }
        },
        ExcelDateToJSDate(serial) {
            var utc_days  = Math.floor(serial - 25569);
            var utc_value = utc_days * 86400;                                        
            var date_info = new Date(utc_value * 1000);

            var fractional_day = serial - Math.floor(serial) + 0.0000001;

            var total_seconds = Math.floor(86400 * fractional_day);

            var seconds = total_seconds % 60;

            total_seconds -= seconds;

            var hours = Math.floor(total_seconds / (60 * 60));
            var minutes = Math.floor(total_seconds / 60) % 60;

            const date = new Date(date_info.getFullYear(), date_info.getMonth(), date_info.getDate(), hours, minutes, seconds);

            return moment(date).add(1, 'days').format('YYYY-MM-DD')
        },
        getCedulas () {
            const Fecha = this.todasFechas ? '' : this.Fecha.split('-').splice(0).reverse().join('-')

            this.loading = true

            request.Get('/cedulas/listar', { Fecha, nroExp: this.nroExp }, r => {
                this.loading = false
                this.Cedulas = r
                this.Cedulas.forEach(c => c.CheckModel = c.Check === 'S')

                if (this.ordenEstado) {
                  this.Cedulas = this.Cedulas.sort((a, b) => a.Orden - b.Orden)
                }

                this.estados = [ ...new Set(this.Cedulas.map(c => c.EstadoAmbitoGestion).filter(e => {
                            if (e) {
                                return e
                            }
                        })) ]
                this.estados.push('Todos')
                this.estados.unshift('Sin estado')

                this.estado = 'Todos'
            })
        },
        importar () {
            const input = document.getElementById('inputCedulas')
            input.click()
        },
        fecha (f) {
            return moment(f).format('DD/MM/YYYY')
        },
        cambiarEstado (c) {
            c.CheckModel = !c.CheckModel
            this.force++
        },
        abrirCaso (id) {
            let routeData = this.$router.resolve({ path: `/Caso?id=${id}` })
            window.open(routeData.href, '_blank')
        },
        finalizar () {
            const ids = this.Cedulas.filter(c => c.CheckModel).map(c => c.IdCedula)

            this.loading = true

            request.Post('/cedulas/finalizar', { Ids: JSON.stringify(ids) }, r => {
                this.loading = false
                this.Cedulas.forEach(c => {
                    if (c.CheckModel) {
                        c.Check = 'S'
                        c.CheckModel = false
                        c.IdUsuario = auth.UsuarioLogueado.IdUsuario
                        c.Usuario = auth.UsuarioLogueado.Usuario
                    }
                })
            })
        },
        filtrarCedulas () {
            return this.Cedulas.filter(c => {
                return  (parseInt(c.IdCorrelativo) === parseInt(this.id) || !this.id) &&
                        (c.EstadoAmbitoGestion === this.estado || !this.estado || this.estado === 'Todos' || this.estado === 'Sin estado' && !c.EstadoAmbitoGestion) &&
                        (this.checkeado && c.Check === 'N' || !this.checkeado)
            })
        },
        cambiarOrden () {
          if (this.ordenEstado) {
            this.Cedulas = this.Cedulas.sort((a, b) => a.Orden - b.Orden)
          } else {
            this.Cedulas = this.Cedulas.sort((a, b) => a.IdCorrelativo - b.IdCorrelativo)
          }
        }
    }
}
</script>

<style scoped>
.acciones > i {
  cursor: pointer;
  margin: 0 auto;
}

.casilla_container, .filas div {
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  position: relative;
}

.titulos_container {
  font-family: "Avenir Next";
  font-weight: bold;
  height : 70px;
  font-size: 16px;
}

.filas {
  background : -moz-linear-gradient(50% 100% 90deg,rgba(255, 255, 255, 0.5) 0%,rgba(253, 253, 253, 0.5) 30.79%,rgba(244, 244, 244, 0.5) 49.03%,rgba(230, 230, 230, 0.5) 64%,rgba(210, 210, 210, 0.5) 77.19%,rgba(184, 184, 184, 0.5) 89.1%,rgba(153, 153, 153, 0.5) 100%);
  background : -webkit-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%);
  background : -webkit-gradient(linear,50% 100% ,50% 0% ,color-stop(0,rgba(255, 255, 255, 0.5) ),color-stop(0.3079,rgba(253, 253, 253, 0.5) ),color-stop(0.4903,rgba(244, 244, 244, 0.5) ),color-stop(0.64,rgba(230, 230, 230, 0.5) ),color-stop(0.7719,rgba(210, 210, 210, 0.5) ),color-stop(0.891,rgba(184, 184, 184, 0.5) ),color-stop(1,rgba(153, 153, 153, 0.5) ));
  background : -o-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%);
  background : -ms-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%);
  -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#999999' ,GradientType=0)";
  background : linear-gradient(0deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%);
  filter: alpha(opacity=50) progid:DXImageTransform.Microsoft.Alpha(opacity=50) progid:DXImageTransform.Microsoft.gradient(startColorstr='#999999',endColorstr='#FFFFFF' , GradientType=0);
  padding: inherit;
  border-radius : inherit;
  -moz-border-radius : inherit;
  -webkit-border-radius : inherit;
  width: inherit;
  min-height: inherit;
  height: inherit;
  font-family: "Avenir Next";
  font-weight: 600;
}

.filas_container, .titulos_container {
  background-color: #ffffff;
  width: 100%;
  color: #000 !important;
  height : 60px;
  margin-top:15px;
  padding: 0;
  border-radius : 7px;
  -moz-border-radius : 7px;
  -webkit-border-radius : 7px;
}

.filas_container {
  min-height: 60px;
  height: auto !important;
}

.casilla_container i {
  cursor: pointer;
}

.casilla_container .q-select {
  visibility: hidden;
  width: 0px;
}

@media only screen and (max-width: 1023px) {
  .filas > div:not(.cliente), .titulos_container > div:not(.cliente) {
    display: none !important;
  }

  .cliente {
    margin: 10px 0px;
  }

  .filas_container {
    height: 120px;
  }
}
</style>