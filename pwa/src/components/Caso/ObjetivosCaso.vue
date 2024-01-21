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
        <div class="q-pa-lg">
          <q-input v-model="nuevoObjetivo.Objetivo" label="Nombre del objetivo" />
          <q-select
            dense
            class="q-my-lg"
            v-model="nuevoObjetivo.IdTipoMov"
            :options="TiposMov.map(t => ({ label: t.TipoMovimiento, value: t.IdTipoMov }))"
            label="Tipo de Movimiento"
            emit-value
            map-options
          />
          <q-select
            dense
            class="q-mb-lg"
            v-model="nuevoObjetivo.ColorMov"
            :options="[
              { value: 'negative', label: 'Perentorios' },
              { value: 'primary', label: 'Gestion Estudio' },
              { value: 'warning', label: 'Gestion Externa' },
              { value: 'positive', label: 'Finalizados' }
            ]"
            label="Estado de Gestión"
            emit-value
            map-options
          />
        </div>
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
    <div v-else-if="Objetivos.length || Combos.length">
      <div class="flex text-center justify-center q-mt-sm">
        <span class="text-bold" v-if="Combos.length">Combos</span>
      </div>
      <q-item
        v-for="combo in Combos"
        :key="combo.IdComboObjetivos"
        style="padding: 15px;"
      >
        <q-btn
          icon="add"
          color="primary"
          flat
          rounded
          outline
          @click="ejecutarCombo(combo.Objetivos)"
        >
          <q-tooltip
            anchor="bottom middle"
            self="top middle"
            :offset="[10, 0]"
          >Agregar Movimientos del Combo</q-tooltip>
        </q-btn>
        <q-tooltip
          anchor="bottom middle"
          self="top middle"
          :offset="[10, 0]"
        >
          <ul>
            <li
              v-for="objetivo in combo.Objetivos"
              :key="objetivo.IdObjetivoEstudio"
            >
              {{ objetivo.ObjetivoEstudio }}
            </li>
          </ul>
        </q-tooltip>
        <q-item-section color="primary" style="margin-right: 5px;" icon="label" />
        <q-item-label header>{{ combo.ComboObjetivos }}</q-item-label>
      </q-item>
      <q-separator />
      <div class="flex text-center justify-center q-mt-sm">
        <span class="text-bold" v-if="Objetivos.length">Objetivos</span>
      </div>
      <q-item
        v-for="objetivo in Objetivos"
        :key="objetivo.IdObjetivo"
        style="padding: 15px;"
      >
        <q-btn
          icon="add"
          color="primary"
          flat
          rounded
          outline
          @click="nuevoMovimiento(objetivo)"
        >
          <q-tooltip
            anchor="bottom middle"
            self="top middle"
            :offset="[10, 0]"
          >Nuevo Movimiento</q-tooltip>
        </q-btn>
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
            <div class="q-pa-lg">
              <q-input
                v-model="objetivoEditar.Objetivo"
                type="textarea"
                rows="1"
                :max-height="50"
                label="Objetivo"
                style="margin: 2rem"
              />
              <q-select
                dense
                class="q-my-lg"
                v-model="objetivoEditar.IdTipoMov"
                :options="TiposMov.map(t => ({ label: t.TipoMovimiento, value: t.IdTipoMov }))"
                label="Tipo de Movimiento"
                emit-value
                map-options
              />
              <q-select
                dense
                class="q-mb-lg"
                v-model="objetivoEditar.ColorMov"
                :options="[
                  { value: 'negative', label: 'Perentorios' },
                  { value: 'primary', label: 'Gestion Estudio' },
                  { value: 'warning', label: 'Gestion Externa' },
                  { value: 'positive', label: 'Finalizados' }
                ]"
                label="Estado de Gestión"
                emit-value
                map-options
              />
            </div>
            <q-card-actions :align="'right'">
              <q-btn flat color="primary" @click="finalizarEdicionObjetivo()">Guardar</q-btn>
              <q-btn flat @click="editarObjetivo = false">Cancelar</q-btn>
            </q-card-actions>
          </q-card>
        </q-dialog>
  </q-card>
</template>

<script>
import moment from 'moment'
import Loading from '../../components/Loading'
import request from '../../request'
import auth from '../../auth'
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
      Combos: [],
      objetivoEditar: {},
      editarObjetivo: false,
      mostrandoModalBorrarObjetivo: false,
      objetivoBorrar: {},
      TiposMov: []
    }
  },
  props: [ 'IdCaso' ],
  created () {
    request.Get(`/objetivos?IdsCaso=${JSON.stringify([this.IdCaso])}&combos=1`, {}, r => {
        if (!r.Error) {
          this.Objetivos = r.Objetivos[this.IdCaso].sort((a, b) => this.compararElementos(a.Objetivo, b.Objetivo))
          this.Combos = r.Combos
          this.Combos.forEach(combo => combo.Objetivos = JSON.parse(combo.Objetivos))
          this.loading = false
        }
      }
    )

    request.Get(`/estudios/${auth.UsuarioLogueado.IdEstudio}/tipos-movimiento`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.TiposMov = r
      } else {
        this.$q.notify('No hay tipos de movimiento disponibles para este estudio')
      }
    })
  },
  methods: {
    compararElementos(a, b) {
      // Utilizar una expresión regular para extraer números y letras
      const regex = /^(\d+)([A-Za-z ]+)/;
      const matchA = a[2] === '-' ? a.replace('-', '').match(regex) : a.match(regex);
      const matchB = b[2] === '-' ? b.replace('-', '').match(regex) : b.match(regex);

      if (matchA === null && matchB === null) {
        // Si ambos no coinciden con el patrón, comparar alfabéticamente
        return a.localeCompare(b);
      } else if (matchA === null) {
        // Si solo A no coincide, colocar B primero
        return 1;
      } else if (matchB === null) {
        // Si solo B no coincide, colocar A primero
        return -1;
      } else {
        // Extraer números y letras de los elementos
        const numeroA = parseInt(matchA[1]);
        const letrasA = matchA[2];
        const numeroB = parseInt(matchB[1]);
        const letrasB = matchB[2];

        // Comparar primero los números y luego las letras alfabéticamente
        if (numeroA < numeroB) {
          return -1;
        } else if (numeroA > numeroB) {
          return 1;
        } else {
          return letrasA.localeCompare(letrasB);
        }
      }
    },
    async ejecutarCombo (objetivos) {
      const movimiento = {
        IdResponsable: auth.UsuarioLogueado.IdUsuario,
        UsuarioResponsable: `${auth.UsuarioLogueado.Apellidos}, ${auth.UsuarioLogueado.Nombres}`,
        IdCaso: this.IdCaso,
        FechaEsperada: null,
        FechaAlta: moment().format('YYYY-MM-DD'),
        FechaEdicion: moment().format('YYYY-MM-DD'),
        FechaRealizado: null,
        Cuaderno: null,
      }

      const nuevoMov = (mov, o) => new Promise(resolve => {
        request.Post('/movimientos', mov, r => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else {
            this.$q.notify(`Movimiento "${o.Objetivo}" creado`)
            
            const { IdMovimientoCaso } = r

            mov.IdMovimientoCaso = IdMovimientoCaso
          }

          this.$root.$emit('nuevoMovObjetivo', mov)
          
          resolve()
        })
      })

      for (let i = 0; i < objetivos.length; i++) {
        const objetivo = this.Objetivos.find(o => o.Objetivo === objetivos[i].ObjetivoEstudio);
        const mov = { ...movimiento }

        if (objetivo) {
          mov.Detalle = objetivo.Objetivo
          mov.IdTipoMov = objetivo.IdTipoMov,
          mov.Color = objetivo.ColorMov,
          mov.TipoMovimiento = this.TiposMov.find(t => t.IdTipoMov === objetivo.IdTipoMov).TipoMovimiento
          mov.Objetivo = objetivo.Objetivo
          mov.IdObjetivo = objetivo.IdObjetivo

          await nuevoMov(mov, objetivo)
        }
      }
    },
    nuevoMovimiento (o) {
      const movimiento = {
        IdResponsable: auth.UsuarioLogueado.IdUsuario,
        UsuarioResponsable: `${auth.UsuarioLogueado.Apellidos}, ${auth.UsuarioLogueado.Nombres}`,
        Detalle: o.Objetivo,
        IdCaso: this.IdCaso,
        FechaEsperada: null,
        FechaAlta: moment().format('YYYY-MM-DD'),
        FechaEdicion: moment().format('YYYY-MM-DD'),
        FechaRealizado: null,
        IdTipoMov: o.IdTipoMov,
        TipoMovimiento: this.TiposMov.find(t => t.IdTipoMov === o.IdTipoMov).TipoMovimiento,
        Cuaderno: null,
        Color: o.ColorMov
      }
      request.Post('/movimientos', movimiento, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.$q.notify(`Movimiento "${o.Objetivo}" creado`)
          
          const { IdMovimientoCaso } = r

          request.Post(`/movimientos/${IdMovimientoCaso}/asociar-objetivo/${o.IdObjetivo}`, {}, r => {
            resolve()
            if (r.Error) {
              Notify.create(r.Error)
            } else {
              movimiento.IdMovimientoCaso = IdMovimientoCaso
              movimiento.IdObjetivo = o.IdObjetivo
              movimiento.Objetivo = o.Objetivo
            }

            this.$root.$emit('nuevoMovObjetivo', movimiento)
          })
        }
      })
    },
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
      this.Objetivos.forEach(o => {
        if (o.IdObjetivo === this.objetivoEditar.IdObjetivo) {
          o.Objetivo = this.objetivoEditar.Objetivo
          o.IdTipoMov = this.objetivoEditar.IdTipoMov
          o.ColorMov = this.objetivoEditar.ColorMov
        }
      })
      request.Put(`/objetivos/${this.objetivoEditar.IdObjetivo}`, this.objetivoEditar, r => {
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
