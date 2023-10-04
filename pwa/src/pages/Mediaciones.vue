<template>
  <q-layout style="padding:0em 2em 0em 2em; margin-top: 1em; margin-bottom: 3em;">
    <FiltrosMediaciones
      :mostrar="verFiltros"
      :Grilla="Grilla"
      :Bonos="Bonos"
      :Beneficios="Beneficios"
      :EstadosBeneficio="EstadosBeneficio"
      :Origenes="Origenes"
      :TiposCaso="TiposCaso"
      :Estados="Estados"
      :EstadosCP="EstadosCP"
      :EstadosDoc="EstadosDoc"
      :EstadosHC="EstadosHC"
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
          <q-toggle
            v-model="causaPenal"
            label="Causa Penal"
            @input="cambiarModo"
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
        <MediacionesGrilla
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
          :name="'Mediaciones'"
        />
      </q-dialog>
    </q-page-container>
  </q-layout>
</template>

<script>
import moment from 'moment'
import request from '../request'
import Loading from '../components/Loading'
import MediacionesGrilla from '../components/Mediaciones/MediacionesGrilla'
import FiltrosMediaciones from '../components/Mediaciones/FiltrosMediaciones'
import ExportExcel from '../components/Compartidos/ExportExcel'

export default {
  name: 'MediacionesPage',
  components: {
    MediacionesGrilla,
    Loading,
    FiltrosMediaciones,
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
      Bonos: [],
      Beneficios: [],
      EstadosBeneficio: [],
      TiposCaso: [],
      Origenes: [],
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
          label: 'Mediador',
          value: 'Mediador',
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
          check: false
        },
        {
          label: 'Origen',
          value: 'Origen',
          check: false
        },
        {
          label: 'Bono',
          value: 'Bono',
          check: true
        },
        {
          label: 'Fecha Bono',
          value: 'FechaBono',
          check: true
        },
        {
          label: 'Beneficio',
          value: 'Beneficio',
          check: true
        },
        {
          label: 'Estado de Beneficio',
          value: 'EstadoBeneficio',
          check: true
        },
        {
          label: 'Fecha Presentado',
          value: 'FechaPresentado',
          check: true
        },
        {
          label: 'Fecha Proxima Audiencia',
          value: 'FechaProximaAudiencia',
          check: true
        },
        {
          label: 'Legajo',
          value: 'Legajo',
          check: true
        },
        {
          label: 'Causa Penal',
          value: 'CausaPenal',
          check: false
        },
        {
          label: 'Documentacion',
          value: 'Doc',
          check: false
        },
        {
          label: 'Nro Exp. Causa Penal',
          value: 'NroExpCP',
          check: false
        },
        {
          label: 'Radicacion',
          value: 'Radicacion',
          check: false
        },
        {
          label: 'Comisaria',
          value: 'Comisaria',
          check: false
        },
        {
          label: 'Historia Clínica',
          value: 'HistoriaClinica',
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
      BonosSeleccionados: [],
      BeneficiosSeleccionados: [],
      EstadosBeneficioSeleccionados: [],
      OrigenesSeleccionados: [],
      TiposCasoSeleccionados: [],
      GrillaSeleccionados: [],
      ArrayExcel: [],
      ModalExcel: []
    }
  },
  created () {
    this.Grilla.forEach(g => {
      if (g.check) { this.GrillaSeleccionados.push(g.value) }
    })

    request.Get('/mediaciones/buscar', {}, r => {
      if (r.length > 0) {
        console.log(r)
        this.Mediaciones = r
        this.Mediaciones.forEach(m => {
          m.PersonasCaso = JSON.parse(m.PersonasCaso)
          m.UltimoMovimiento = JSON.parse(m.UltimoMovimiento)
          m.Parametros = JSON.parse(m.Parametros)

          let actores = []
          let demandados = []

          m.PersonasCaso && m.PersonasCaso.forEach(p => {
            if (p.Observaciones === 'Actor') actores.push(p.Apellidos + ', ' + p.Nombres)
            if (p.Observaciones === 'Demandado') demandados.push(p.Apellidos + ', ' + p.Nombres)
          })

          m.Actores = actores
          m.Demandados = demandados
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

    request.Get('/mediaciones/datos', {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        const Bonos = JSON.parse(r.Bonos)
        const Beneficios = JSON.parse(r.Beneficios)
        const EstadosBeneficio = JSON.parse(r.EstadosBeneficio) || []

        this.Bonos = Bonos.map(b => {
          return {
            label: b.Bono,
            value: b.IdBono,
            check: false
          }
        })
        this.Bonos.push({
          label: 'Sin datos',
          value: 0,
          check: false
        })

        this.Beneficios = Beneficios.map(b => {
          return {
            label: b.Beneficio,
            value: b.IdBeneficio,
            check: false
          }
        })
        this.Beneficios.push({
          label: 'Sin datos',
          value: 0,
          check: false
        })

        this.EstadosBeneficio = EstadosBeneficio.map(b => {
          return {
            label: b.EstadoBeneficio,
            value: b.IdEstadoBeneficio,
            check: false
          }
        })
        this.EstadosBeneficio.push({
          label: 'Sin datos',
          value: 0,
          check: false
        })
      }
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
              m.PersonasCaso && m.PersonasCaso.forEach(p => {
                if (p.Observaciones === 'Actor' && !p.EstadoHC) {
                  check = true
                }
              })
              return check
            }).length
            : this.Mediaciones.filter(m => {
              let check = false
              m.PersonasCaso && m.PersonasCaso.forEach(p => {
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
        const IdBono = m.IdBono || 0
        const IdBeneficio = m.IdBeneficio || 0
        const IdEstadoBeneficio = m.IdEstadoBeneficio || 0
        const IdOrigen = m.IdOrigen || 0
        const EstadoCP = m.EstadoCausaPenal || 'Sin datos'
        const EstadoDoc = m.Parametros ? (m.Parametros.EstadoDocumentacion || 'Sin datos') : 'Sin datos'

        let checkEstadoHC = false
        m.PersonasCaso && m.PersonasCaso.forEach(p => {
          if (p.Observaciones === 'Actor') {
            const EstadoHC = p.EstadoHC || 'Sin datos'

            if (this.EstadosHCSeleccionados.includes(EstadoHC)) {
              checkEstadoHC = true
            }
          }
        })

        return (this.BonosSeleccionados.includes(parseInt(IdBono)) || this.BonosSeleccionados.length === 0) &&
        (this.BeneficiosSeleccionados.includes(parseInt(IdBeneficio)) || this.BeneficiosSeleccionados.length === 0) &&
        (this.EstadosBeneficioSeleccionados.includes(parseInt(IdEstadoBeneficio)) || this.EstadosBeneficioSeleccionados.length === 0) &&
        (this.EstadosSeleccionados.includes(parseInt(m.IdEstadoAmbitoGestion)) || this.EstadosSeleccionados.length === 0) &&
        (this.OrigenesSeleccionados.includes(parseInt(IdOrigen)) || this.OrigenesSeleccionados.length === 0) &&
        (this.TiposCasoSeleccionados.includes(parseInt(m.IdTipoCaso)) || this.TiposCasoSeleccionados.length === 0) &&
        (this.EstadosCPSeleccionados.includes(EstadoCP) || this.EstadosCPSeleccionados.length === 0) &&
        (this.EstadosDocSeleccionados.includes(EstadoDoc) || this.EstadosDocSeleccionados.length === 0) &&
        (checkEstadoHC || this.EstadosHCSeleccionados.length === 0)
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
          Caratula: m.Caratula,
          Estado_de_Ambito_Gestion: m.EstadoAmbitoGestion || '',
          Fecha_Cambio_de_Estado: m.FechaEstado ? moment(m.FechaEstado).format('DD/MM/YYYY') : '',
          Dias_Estado: this.diasCambioEstado(m.FechaEstado),
          Ultimo_Movimiento: m.UltimoMovimiento ? m.UltimoMovimiento.Detalle : '',
          Tipo_de_Caso: m.TipoCaso || '',
          Ambito_de_Gestion: m.Juzgado || '',
          Bono: m.Bono || '',
          Fecha_Bono: m.FechaBonos ? moment(m.FechaBonos).format('DD/MM/YYYY') : '',
          Dias_Bono: this.diasCambioEstado(m.FechaBonos),
          Beneficio: m.Beneficio || '',
          Estado_Beneficio: m.EstadoBeneficio || '',
          Fecha_Presentado: m.FechaPresentado ? moment(m.FechaPresentado).format('DD/MM/YYYY') : '',
          Dias_Presentado: this.diasCambioEstado(m.FechaPresentado),
          Origen: m.Origen || '',
          Actores: m.Actores.length > 0 ? m.Actores.join(' - ') : '',
          Demandados: m.Demandados.length > 0 ? m.Demandados.join(' - ') : '',
          Mediador: m.NombreMediador || '',
          Legajo: m.Legajo || '',
          Fecha_Proxima_Audiencia: m.FechaProximaAudiencia ? moment(m.FechaProximaAudiencia).format('DD/MM/YYYY HH:mm') + ' hs' : ''
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
