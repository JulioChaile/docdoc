<template>
  <q-layout style="padding:0em 2em 0em 2em; margin-top: 1em; margin-bottom: 3em;">
    <FiltrosCausasPenales
      :mostrar="verFiltros"
      :Grilla="Grilla"
      :Origenes="Origenes"
      :TiposCaso="TiposCaso"
      :Estados="Estados"
      :EstadosCP="EstadosCP"
      :EstadosDoc="EstadosDoc"
      :EstadosHC="EstadosHC"
      :Nominaciones="Nominaciones"
      :Juzgados="Juzgados"
      :Orden="Orden"
      @seleccionarFiltro="seleccionarFiltro"
      @ordenar="ordenar"
    />
    <q-page-container>
      <div
        style="background:white; padding:1em 1em 1em 1em"
        class="shadow-1"
      >
        <div class="row items-center">
          Opciones:
          <q-toggle
            v-model="verFiltros"
            label="Ver Filtros"
          />
          <q-toggle
            v-model="agrupar"
            label="Agrupar"
          />
          <div
            v-if="!loading"
            class="q-ml-xl cursor-pointer text-caption text-grey"
            @click="exportar"
          >
            <q-icon size="sm" name="file_download" color="positive" />
            Descargar como Excel
          </div>
        </div>

        <q-separator spaced />

        <div
          v-if="loading"
        >
          <Loading />
        </div>
        <CausasPenalesGrilla
          v-else
          :Mediaciones="ArrayMediaciones"
          :Grilla="GrillaSeleccionados"
          @altaMediacion="altaMediacion"
        />
      </div>

      <!-- Modal Excel -->
      <q-dialog v-model="ModalExcel">
        <ExportExcel
          :ArrayInicial="ArrayExcel"
          :name="'Causas Penales'"
        />
      </q-dialog>
    </q-page-container>
  </q-layout>
</template>

<script>
import moment from 'moment'
import request from '../request'
import Loading from '../components/Loading'
import CausasPenalesGrilla from '../components/CausasPenales/CausasPenalesGrilla'
import FiltrosCausasPenales from '../components/CausasPenales/FiltrosCausasPenales'
import ExportExcel from '../components/Compartidos/ExportExcel'

export default {
  name: 'CausasPenalesPage',
  components: {
    CausasPenalesGrilla,
    Loading,
    FiltrosCausasPenales,
    ExportExcel
  },
  data () {
    return {
      Mediaciones: [],
      agrupar: false,
      loading: true,
      verFiltros: false,
      causaPenal: false,
      Estados: [],
      EstadosCP: [],
      EstadosDoc: [],
      EstadosHC: [],
      TiposCaso: [],
      Origenes: [],
      Juzgados: [],
      Nominaciones: [
        {
          label: 'Pendiente',
          value: 0,
          check: false
        },
        {
          label: 'I',
          value: 1,
          check: false
        },
        {
          label: 'II',
          value: 2,
          check: false
        },
        {
          label: 'III',
          value: 3,
          check: false
        },
        {
          label: 'IV',
          value: 4,
          check: false
        },
        {
          label: 'V',
          value: 5,
          check: false
        },
        {
          label: 'VI',
          value: 6,
          check: false
        },
        {
          label: 'VII',
          value: 7,
          check: false
        },
        {
          label: 'VIII',
          value: 8,
          check: false
        },
        {
          label: 'IX',
          value: 9,
          check: false
        }
      ],
      Grilla: [
        {
          label: 'Cliente',
          value: 'Cliente',
          check: true
        },
        {
          label: 'Estado',
          value: 'Estado',
          check: true
        },
        {
          label: 'Ultimo Movimiento',
          value: 'UltMov',
          check: false
        },
        {
          label: 'Tipo Caso',
          value: 'TipoCaso',
          check: true
        },
        {
          label: 'Nominacion',
          value: 'Nominacion',
          check: false
        },
        {
          label: 'Origen',
          value: 'Origen',
          check: false
        },
        {
          label: 'Ambito de Gestion',
          value: 'Juzgado',
          check: false
        },
        {
          label: 'Causa Penal',
          value: 'CausaPenal',
          check: true
        },
        {
          label: 'Fecha del Hecho',
          value: 'FechaHecho',
          check: false
        },
        {
          label: 'Documentacion',
          value: 'Doc',
          check: true
        },
        {
          label: 'Nro Exp. Causa Penal',
          value: 'NroExpCP',
          check: true
        },
        {
          label: 'Radicacion',
          value: 'Radicacion',
          check: true
        },
        {
          label: 'Comisaria',
          value: 'Comisaria',
          check: true
        },
        {
          label: 'Historia Clínica',
          value: 'HistoriaClinica',
          check: true
        },
        {
          label: 'Actores',
          value: 'Actores',
          check: false
        },
        {
          label: 'Demandados',
          value: 'Demandados',
          check: false
        }
      ],
      Orden: [
        {
          label: 'Fecha Alta de Caso',
          value: 'FechaAlta'
        },
        {
          label: 'Fecha Bonos',
          value: 'FechaBonos'
        },
        {
          label: 'Fecha Presentado',
          value: 'FechaPresentado'
        },
        {
          label: 'Fecha Proxima Audiencia',
          value: 'FechaProximaAudiencia'
        }
      ],
      EstadosSeleccionados: [],
      EstadosCPSeleccionados: [],
      EstadosDocSeleccionados: [],
      EstadosHCSeleccionados: [],
      OrigenesSeleccionados: [],
      TiposCasoSeleccionados: [],
      NominacionesSeleccionados: [],
      GrillaSeleccionados: [],
      JuzgadosSeleccionados: [],
      ModalExcel: false,
      ArrayExcel: []
    }
  },
  created () {
    this.Grilla.forEach(g => {
      if (g.check) { this.GrillaSeleccionados.push(g.value) }
    })

    request.Get('/mediaciones/buscar', { CausaPenal: 1 }, r => {
      if (r.length > 0) {
        this.Mediaciones = r
        this.Mediaciones.forEach(m => {
          m.PersonasCaso = JSON.parse(m.PersonasCaso)
          m.UltimoMovimiento = JSON.parse(m.UltimoMovimiento)
          m.Parametros = JSON.parse(m.Parametros)

          let actores = []
          let demandados = []

          m.PersonasCaso.forEach(p => {
            if (p.Observaciones === 'Actor') actores.push(p.Apellidos + ', ' + p.Nombres)
            if (p.Observaciones === 'Demandado') demandados.push(p.Apellidos + ', ' + p.Nombres)
          })

          m.Actores = actores
          m.Demandados = demandados

          m.FechaHecho = m.Parametros.FechaHecho
            ? m.Parametros.FechaHecho.split('-').join('/')
            : ''
        })

        r.forEach(m => {
          this.Estados.push({
            label: m.EstadoAmbitoGestion,
            value: parseInt(m.IdEstadoAmbitoGestion),
            Orden: m.OrdenEstado,
            check: false,
            cantidad: 0
          })
        })
        this.Estados.sort((a, b) => a.Orden - b.Orden)

        let hash = {}
        this.Estados = this.Estados.filter(e => {
          const exists = !hash[e.value]
          hash[e.value] = true
          return exists
        })

        this.Estados.forEach(e => {
          e.cantidad = this.Mediaciones.filter(m => parseInt(m.IdEstadoAmbitoGestion) === e.value).length
        })
      }
      this.loading = false
    })

    request.Get('/casos/opciones-parametros', {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        const i = r.findIndex(o => o.Variable === 'opcionesEstadoCausaPenal')
        const opcionesEstadoCausaPenal = JSON.parse(r[i].Opciones)

        this.EstadosCP = opcionesEstadoCausaPenal.map(o => {
          return {
            label: o,
            value: o,
            check: false,
            cantidad: 0
          }
        })

        this.EstadosCP.push({
          label: 'Sin datos',
          value: 'Sin datos',
          check: false,
          cantidad: 0
        })

        const j = r.findIndex(o => o.Variable === 'opcionesEstadoDoc')
        const opcionesEstadoDoc = JSON.parse(r[j].Opciones)

        this.EstadosDoc = opcionesEstadoDoc.map(o => {
          return {
            label: o,
            value: o,
            check: false,
            cantidad: 0
          }
        })

        this.EstadosDoc.push({
          label: 'Sin datos',
          value: 'Sin datos',
          check: false,
          cantidad: 0
        })

        const k = r.findIndex(o => o.Variable === 'opcionesEstadoHC')
        const opcionesEstadoHC = JSON.parse(r[k].Opciones)

        this.EstadosHC = opcionesEstadoHC.map(o => {
          return {
            label: o,
            value: o,
            check: false,
            cantidad: 0
          }
        })

        this.EstadosHC.push({
          label: 'Sin datos',
          value: 'Sin datos',
          check: false,
          cantidad: 0
        })
      }
    })

    request.Get('/origenes', {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.Origenes = r.map(o => {
          return {
            label: o.Origen,
            value: parseInt(o.IdOrigen),
            check: false
          }
        })
        this.Origenes.push({
          label: 'Sin origen',
          value: 0,
          check: false
        })
      }
    })

    request.Get('/juzgados', {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.Juzgados = r.map(o => {
          return {
            label: o.Juzgado,
            value: parseInt(o.IdJuzgado),
            check: false
          }
        })
      }
    })

    request.Get('/tipos-caso?&IncluyeBajas=N', {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.TiposCaso = r.map(t => {
          return {
            label: t.TipoCaso,
            value: parseInt(t.IdTipoCaso),
            check: false
          }
        })
        this.TiposCaso.push({
          label: 'Sin origen',
          value: 0,
          check: false
        })
      }
    })
  },
  watch: {
    EstadosSeleccionados () {
      this.agrupar = !(this.EstadosSeleccionados.length === 0)
    },
    agrupar () {
      this.simularCarga()
    },
    Mediaciones () {
      if (this.Mediaciones.length > 0) {
        this.EstadosCP.forEach(e => {
          e.cantidad = e.value === 'Sin datos'
            ? this.Mediaciones.filter(m => !m.EstadoCausaPenal).length
            : this.Mediaciones.filter(m => m.EstadoCausaPenal === e.value).length
        })

        this.EstadosDoc.forEach(e => {
          e.cantidad = e.value === 'Sin datos'
            ? this.Mediaciones.filter(m => !m.Parametros || !m.Parametros.EstadoDocumentacion).length
            : this.Mediaciones.filter(m => {
              if (m.Parametros) {
                return m.Parametros.EstadoDocumentacion === e.value
              }
            }).length
        })

        this.EstadosHC.forEach(e => {
          e.cantidad = e.value === 'Sin datos'
            ? this.Mediaciones.filter(m => {
              let check = false
              m.PersonasCaso.forEach(p => {
                if (p.Observaciones === 'Actor' && !p.EstadoHC) {
                  check = true
                }
              })
              return check
            }).length
            : this.Mediaciones.filter(m => {
              let check = false
              m.PersonasCaso.forEach(p => {
                if (p.Observaciones === 'Actor' && p.EstadoHC === e.value) {
                  check = true
                }
              })
              return check
            }).length
        })
      }
    }
  },
  computed: {
    ArrayMediaciones () {
      let mediaciones = this.filtros(this.Mediaciones)

      if (this.agrupar) {
        let Orden = []

        mediaciones.forEach(m => {
          Orden.push({
            Orden: m.OrdenEstado,
            IdEstadoAmbitoGestion: m.IdEstadoAmbitoGestion
          })
        })

        let hash = {}
        Orden = Orden.filter(e => {
          const exists = !hash[e.IdEstadoAmbitoGestion]
          hash[e.IdEstadoAmbitoGestion] = true
          return exists
        })

        /*
        for (var i = Orden.length - 1; i >= 0; i--) {
          if (Orden.indexOf(Orden[i]) !== i) { Orden.splice(i, 1) }
        }
        */

        Orden.sort((a, b) => a.Orden - b.Orden)

        let array = []

        Orden.forEach(o => {
          let s = mediaciones.filter(m => m.IdEstadoAmbitoGestion === o.IdEstadoAmbitoGestion)
          s.sort((a, b) => { return new Date(b.FechaEstado).getTime() > new Date(a.FechaEstado).getTime() ? 1 : -1 })
          array.push(s)
        })

        return array
      } else {
        return [mediaciones]
      }
    }
  },
  methods: {
    simularCarga () {
      this.loading = true
      setTimeout(() => {
        this.loading = false
      }, 400)
    },
    cambiarModo () {
      let mod = this.causaPenal
        ? [
          'Cliente',
          'Estado',
          'CausaPenal',
          'Doc',
          'NroExpCP',
          'Radicacion',
          'Comisaria',
          'HistoriaClinica'
        ]
        : [
          'Cliente',
          'Estado',
          'Mediador',
          'Bono',
          'FechaBono',
          'Beneficio',
          'FechaPresentado',
          'FechaProximaAudiencia',
          'Legajo'
        ]
      this.Grilla.forEach(g => {
        g.check = mod.includes(g.value)
      })
      this.GrillaSeleccionados = mod.slice(0)
    },
    seleccionarFiltro (filtro, array) {
      this[array] = filtro.slice(0)
    },
    ordenar (p) {
      if (p === 'FechaProximaAudiencia') {
        const hoy = new Date().getTime()
        let proximos = []
        let pasados = []
        let sinFecha = []
        this.Mediaciones.forEach(m => {
          switch (true) {
            case new Date(m[p]).getTime() >= hoy:
              proximos.push(m)
              break

            case new Date(m[p]).getTime() < hoy:
              pasados.push(m)
              break

            default:
              sinFecha.push(m)
              break
          }
        })
        proximos.sort((a, b) => { return new Date(b[p]).getTime() < new Date(a[p]).getTime() ? 1 : -1 })
        pasados.sort((a, b) => { return new Date(b[p]).getTime() > new Date(a[p]).getTime() ? 1 : -1 })
        this.Mediaciones = proximos.concat(sinFecha).concat(pasados)
      } else {
        this.Mediaciones.sort((a, b) => { return new Date(b[p]).getTime() > new Date(a[p]).getTime() ? 1 : -1 })
      }
      this.simularCarga()
    },
    filtros (mediaciones) {
      return mediaciones.filter(m => {
        const IdOrigen = m.IdOrigen || 0
        const EstadoCP = m.EstadoCausaPenal || 'Sin datos'
        const EstadoDoc = m.Parametros ? (m.Parametros.EstadoDocumentacion || 'Sin datos') : 'Sin datos'

        let checkEstadoHC = false
        m.PersonasCaso.forEach(p => {
          if (p.Observaciones === 'Actor') {
            const EstadoHC = p.EstadoHC || 'Sin datos'

            if (this.EstadosHCSeleccionados.includes(EstadoHC)) {
              checkEstadoHC = true
            }
          }
        })

        let checkNominaciones = false

        if (((m.Nominacion || '').toLowerCase().includes('pendiente') || (m.Nominacion || '')[0] === '0') && this.NominacionesSeleccionados.includes('Pendiente')) {
          checkNominaciones = true
        }

        this.NominacionesSeleccionados.forEach(n => {
          if (n.toString() === (m.Nominacion || '')[0]) {
            checkNominaciones = true
          }
        })

        return (this.EstadosSeleccionados.includes(parseInt(m.IdEstadoAmbitoGestion)) || this.EstadosSeleccionados.length === 0) &&
        (this.OrigenesSeleccionados.includes(parseInt(IdOrigen)) || this.OrigenesSeleccionados.length === 0) &&
        (this.TiposCasoSeleccionados.includes(parseInt(m.IdTipoCaso)) || this.TiposCasoSeleccionados.length === 0) &&
        (this.JuzgadosSeleccionados.includes(parseInt(m.IdJuzgado)) || this.JuzgadosSeleccionados.length === 0) &&
        (this.EstadosCPSeleccionados.includes(EstadoCP) || this.EstadosCPSeleccionados.length === 0) &&
        (this.EstadosDocSeleccionados.includes(EstadoDoc) || this.EstadosDocSeleccionados.length === 0) &&
        (checkEstadoHC || this.EstadosHCSeleccionados.length === 0) &&
        (checkNominaciones || this.NominacionesSeleccionados.length === 0)
      })
    },
    altaMediacion (mediacion) {
      request.Get('/mediaciones', {id: mediacion.IdMediacion}, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          const i = this.Mediaciones.findIndex(m => parseInt(m.IdCaso) === parseInt(mediacion.IdCaso))

          this.Mediaciones[i].IdBono = r.IdBono
          this.Mediaciones[i].Bono = r.Bono
          this.Mediaciones[i].IdBeneficio = r.IdBeneficio
          this.Mediaciones[i].Beneficio = r.Beneficio
          this.Mediaciones[i].IdEstadoBeneficio = r.IdEstadoBeneficio
          this.Mediaciones[i].EstadoBeneficio = r.EstadoBeneficio
          this.Mediaciones[i].IdMediador = r.IdMediador
          this.Mediaciones[i].NombreMediador = r.Mediador.Nombre
          this.Mediaciones[i].FechaBonos = r.FechaBonos
          this.Mediaciones[i].FechaPresentado = r.FechaPresentado
          this.Mediaciones[i].FechaProximaAudiencia = r.FechaProximaAudiencia
          this.Mediaciones[i].Legajo = r.Legajo
        }
      })
    },
    exportar () {
      let array = []

      this.ArrayMediaciones.forEach(a => a.forEach(m => {
        array.push({
          Nro_Exp_Causa_Penal: m.NroExpedienteCausaPenal || '',
          Caratula: m.Caratula,
          Fecha_Hecho: m.FechaHecho || '',
          Actores: m.Actores.length > 0 ? m.Actores.join(' - ') : '',
          Demandados: m.Demandados.length > 0 ? m.Demandados.join(' - ') : '',
          Causa_Penal: m.EstadoCausaPenal || 'Sin estado asignado',
          Dias_Causa_Penal: this.diasCambioEstado(m.FechaEstadoCausaPenal),
          Radicacion: m.Radicacion || '',
          Comisaria: m.Comisaria || '',
          Fecha_Estado_Causa_Penal: m.FechaEstadoCausaPenal ? moment(m.FechaEstadoCausaPenal).format('DD/MM/YYYY') : '',
          Ultimo_Movimiento: m.UltimoMovimiento ? m.UltimoMovimiento.Detalle : '',
          Estado_de_Ambito_Gestion: m.EstadoAmbitoGestion || '',
          Fecha_Cambio_de_Estado: m.FechaEstado ? moment(m.FechaEstado).format('DD/MM/YYYY') : '',
          Dias_Estado: this.diasCambioEstado(m.FechaEstado),
          Ambito_de_Gestion: m.Juzgado || '',
          Tipo_de_Caso: m.TipoCaso || '',
          Nominacion: m.Nominacion || '',
          Origen: m.Origen || ''
        })
      }))

      this.ArrayExcel = array
      this.ModalExcel = true
    },
    diasCambioEstado (FechaEstado) {
      if (!FechaEstado) {
        return ''
      }

      const resultado = moment().diff(moment(FechaEstado), 'days')
      return resultado >= 0 ? resultado : resultado - 1
    }
  }
}
</script>
