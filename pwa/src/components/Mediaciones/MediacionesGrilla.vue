<template>
  <div>
    <q-banner
      inline="true"
      class="bg-grey-3 text-black inner-shadow"
      :style="'margin-top:.2em; margin-bottom:.2em; padding: 5px; width: 100%'"
    >
      <div class="row text-center">
        <span v-if="Ver.Cliente" class="col self-center">Cliente</span>
        <span v-if="Ver.Estado" class="col self-center">Estado de Ambito de Gestion</span>
        <span v-if="Ver.Mediador" class="col self-center">Mediador</span>
        <span v-if="Ver.UltMov" class="col self-center">Ultimo Movimiento</span>
        <span v-if="Ver.TipoCaso" class="col self-center">Tipo de Caso</span>
        <span v-if="Ver.Origen" class="col self-center">Origen</span>
        <span v-if="Ver.Bono" class="col self-center">Bono</span>
        <span v-if="Ver.FechaBono" class="col self-center">Fecha Bono</span>
        <span v-if="Ver.Beneficio" class="col self-center">Beneficio</span>
        <span v-if="Ver.EstadoBeneficio" class="col self-center">Estado de Beneficio</span>
        <span v-if="Ver.FechaPresentado" class="col self-center">Fecha Presentado</span>
        <span v-if="Ver.FechaProximaAudiencia" class="col self-center">Fecha Proxima Audiencia</span>
        <span v-if="Ver.Legajo" class="col self-center">Legajo</span>
        <span v-if="Ver.CausaPenal" class="col self-center">Causa Penal</span>
        <span v-if="Ver.Doc" class="col self-center">Documentacion</span>
        <span v-if="Ver.NroExpCP" class="col self-center">Nro Exp. Causa Penal</span>
        <span v-if="Ver.Radicacion" class="col self-center">Radicacion</span>
        <span v-if="Ver.Comisaria" class="col self-center">Comisaria</span>
        <span v-if="Ver.HistoriaClinica" class="col self-center">Historia Clínica</span>
      </div>
    </q-banner>

    <div
      v-for="(GrupoMediaciones, i) in Mediaciones"
      :key="i + 'g'"
    >
      <h6 v-if="GrupoMediaciones.length === 0" class="flex flex-center" style="text-align: center;">
        No existen casos que coincidan con el criterio de busqueda
      </h6>
      <div
        v-else
        v-for="m in GrupoMediaciones"
        :key="m.IdMediacion"
      >
        <q-banner
          class="bg-secondary text-white inner-shadow"
          :style="'margin-top:.2em; padding: 5px; width: 100%'"
        >
          <div
            class="row text-center bg-secondary text-white inner-shadow"
            style="cursor: pointer; margin-top:.2em; padding: 0; width: 100%"
            @click="abrirMediacion(m)"
          >
            <div
              class="col self-center text-center"
              v-if="Ver.Cliente"
              @click="abrirCaso(m.IdCaso)"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ver Caso</q-tooltip>
              {{ m.Caratula ? m.Caratula : 'Sin caratula' }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.Estado"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Estado de la Mediacion</q-tooltip>
              {{ m.EstadoAmbitoGestion ? m.EstadoAmbitoGestion : 'Sin estado asignado' }}
              <br>
              Hace {{ diasCambioEstado(m.FechaEstado) }} días
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.Mediador"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Mediador</q-tooltip>
              {{ m.NombreMediador ? m.NombreMediador : 'Sin mediador sorteado' }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.UltMov"
              @click="editarMovimiento(m.UltimoMovimiento, m.Caratula)"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Editar Movimiento</q-tooltip>
              {{ m.UltimoMovimiento ? m.UltimoMovimiento.Detalle : 'Sin movimientos cargados' }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.TipoCaso"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Tipo de Caso</q-tooltip>
              {{ m.TipoCaso }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.Origen"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Origen</q-tooltip>
              {{ m.Origen || 'Sin origen asignado' }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.Bono"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Bono</q-tooltip>
              <span
                :class="m.ColorBono ? `text-${m.ColorBono}` : ''"
                style="font-weight: bold"
              >
                {{ m.Bono ? m.Bono : 'Sin datos' }}
              </span>
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.FechaBono"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Fecha Bono</q-tooltip>
              {{ m.FechaBonos ? m.FechaBonos.split(' ')[0] : 'Sin fecha asignada' }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.Beneficio"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Beneficio</q-tooltip>
              {{ m.Beneficio ? m.Beneficio : 'Sin datos'}}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.EstadoBeneficio"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Estado de Beneficio</q-tooltip>
              {{ m.EstadoBeneficio || 'Sin datos'}}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.FechaPresentado"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Fecha Presentado</q-tooltip>
              {{ m.FechaPresentado ? m.FechaPresentado.split(' ')[0] : 'Sin fecha asignada' }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.FechaProximaAudiencia"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Fecha Proxima Audiencia: {{ m.FechaProximaAudiencia ? m.FechaProximaAudiencia.split(' ')[0] : 'Sin fecha asignada' }}</q-tooltip>
              {{ m.FechaProximaAudiencia ? diasCambioEstado(m.FechaProximaAudiencia.split(' ')[0]) : 'Sin fecha asignada' }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.Legajo"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Legajo</q-tooltip>
              {{ m.Legajo ? m.Legajo : 'Sin legajo asignado' }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.CausaPenal"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Estado de la Causa Penal</q-tooltip>
              {{ m.EstadoCausaPenal || 'Sin estado asignado' }}
              <br>
              {{ m.FechaEstadoCausaPenal ? `Hace ${diasCambioEstado(m.FechaEstadoCausaPenal)} días` : '' }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.Doc"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Documentacion del Caso</q-tooltip>
              {{ m.Parametros ? (m.Parametros.EstadoDocumentacion || 'Sin estado asignado') : 'Sin estado asignado' }}
              <br>
              {{ m.Parametros ? (m.Parametros.FechaEstadoDocumentacion ? `Hace ${diasCambioEstado(m.FechaEstadoDocumentacion)} días` : '') : '' }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.NroExpCP"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Nro de Expediente de la Causa Penal</q-tooltip>
              {{ m.NroExpedienteCausaPenal || 'Sin exp asignado' }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.Radicacion"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Radicación</q-tooltip>
              {{ m.Radicacion || 'Sin datos' }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.Comisaria"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Comisaria</q-tooltip>
              {{ m.Comisaria || 'Sin datos' }}
            </div>
            <div
              class="col self-center text-center"
              v-if="Ver.HistoriaClinica"
              @click="verInfoHc(m)"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ver más</q-tooltip>
              <ul style="margin: 0; padding: 0">
                <li
                  v-for="h in arrayHC(m.PersonasCaso)"
                  :key="h"
                  style="list-style: none"
                >
                  {{ h }}
                </li>
              </ul>
            </div>
          </div>
        </q-banner>
      </div>
      <q-separator color="black" size="5px" style="margin-bottom: -.2em" />
    </div>

    <!-- Modal Historia Clinica -->
    <q-dialog v-model="ModalHC">
      <q-card style="max-width: 100% !important">
        <q-item style="background-color: black;">
          <span class="q-subheading" style="color:white;">Historia Clínica</span>
        </q-item>
        <q-item>
          <q-table
            hide-pagination
            flat
            :data="personas"
            :columns="columnas"
            row-key="name"
          />
        </q-item>
        <q-item class="justify-center">
          <span
            class="q-subheading cursor-pointer"
            @click="abrirCaso(IdCaso, true)"
          >
            Ver Datos de las Personas
          </span>
        </q-item>
      </q-card>
    </q-dialog>

    <!-- Modal Mediacion -->
    <q-dialog v-model="ModalMediacion" style="background-color: white">
      <Mediacion
        :IdMediacion="Mediacion.IdMediacion"
        :IdCaso="Mediacion.IdCaso"
        :IdChat="Mediacion.IdChat"
        :CaratulaCaso="Mediacion.CaratulaCaso"
        :Personas="Mediacion.Personas"
        @alta="altaMediacion"
        @cerrar="ModalMediacion = false"
      />
    </q-dialog>

    <!-- Modal Editar Movimiento -->
    <q-dialog v-model="ModalMovimiento" no-backdrop-dismiss no-esc-dismiss>
      <q-card>
        <q-item class="relative-position" style="background-color: black;">
          <span class="q-subheading" style="color:white;">Editar Movimiento</span>
          <q-icon
            name="close"
            class="absolute-right cursor-pointer"
            color="negative"
            size="lg"
            @click="ModalMovimiento = false"
          />
        </q-item>
        <EditarMovimiento
          v-if="ModalMovimiento"
          :movimiento="this.movimientoEditar"
          @cancelarEdicion="ModalMovimiento = false"
          @edicionTerminada="ModalMovimiento = false"
        />
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import moment from 'moment'
import Mediacion from '../../components/Caso/Mediacion'
import EditarMovimiento from '../../components/EditarMovimiento'

export default {
  name: 'MediacionesGrilla',
  props: ['Mediaciones', 'Grilla'],
  components: { Mediacion, EditarMovimiento },
  data () {
    return {
      Mediacion: {},
      ModalMediacion: false,
      ModalMovimiento: false,
      ModalHC: false,
      movimientoEditar: {},
      personas: [],
      columnas: [
        {
          name: 'persona',
          label: 'Persona',
          field: 'persona',
          align: 'left'
        },
        {
          name: 'estado',
          label: 'Estado de Historia Clínica',
          field: 'estado',
          align: 'center'
        },
        {
          name: 'cambioEstado',
          label: 'Días desde cambio de estado',
          field: 'cambioEstado',
          align: 'center'
        },
        {
          name: 'centro',
          label: 'Centro Médico',
          field: 'centro',
          align: 'center'
        },
        {
          name: 'numero',
          label: 'Número',
          field: 'numero',
          align: 'right'
        }
      ],
      IdCaso: 0
    }
  },
  computed: {
    Ver () {
      return {
        Cliente: this.Grilla.includes('Cliente'),
        Estado: this.Grilla.includes('Estado'),
        Mediador: this.Grilla.includes('Mediador'),
        Bono: this.Grilla.includes('Bono'),
        UltMov: this.Grilla.includes('UltMov'),
        TipoCaso: this.Grilla.includes('TipoCaso'),
        Origen: this.Grilla.includes('Origen'),
        FechaBono: this.Grilla.includes('FechaBono'),
        Beneficio: this.Grilla.includes('Beneficio'),
        EstadoBeneficio: this.Grilla.includes('EstadoBeneficio'),
        FechaPresentado: this.Grilla.includes('FechaPresentado'),
        FechaProximaAudiencia: this.Grilla.includes('FechaProximaAudiencia'),
        Legajo: this.Grilla.includes('Legajo'),
        CausaPenal: this.Grilla.includes('CausaPenal'),
        Doc: this.Grilla.includes('Doc'),
        NroExpCP: this.Grilla.includes('NroExpCP'),
        Radicacion: this.Grilla.includes('Radicacion'),
        Comisaria: this.Grilla.includes('Comisaria'),
        HistoriaClinica: this.Grilla.includes('HistoriaClinica')
      }
    }
  },
  methods: {
    abrirMediacion (m) {
      this.Mediacion = {
        IdMediacion: m.IdMediacion,
        IdCaso: m.IdCaso,
        IdChat: m.IdChat,
        CaratulaCaso: m.Caratula,
        Personas: this.dataPersonas(m.PersonasCaso)
      }

      this.ModalMediacion = true
    },
    altaMediacion (id) {
      this.Mediacion.IdMediacion = id
      this.$emit('altaMediacion', this.Mediacion)
    },
    dataPersonas (personas) {
      const temp = []
      if (personas.length) {
        personas.forEach((persona) => {
          const data = {
            Id: persona.IdPersona,
            Apellido: persona.Apellidos,
            Nombre: persona.Nombres,
            Email: persona.Email,
            Principal: persona.EsPrincipal === 'S',
            Domicilio: persona.Domicilio,
            Documento: persona.Documento,
            Tipo: persona.Tipo,
            Rol: persona.RolTipoCaso ? persona.RolTipoCaso : persona.Observaciones,
            Telefonos: this.personaTelefonos(persona.Telefonos),
            TelefonoActivo: this.telefonoPrincipal(persona.Telefonos)
          }
          temp.push(data)
        })
      }
      return temp
    },
    personaTelefonos (telefonos) {
      const temp = []
      if (telefonos) {
        telefonos.forEach((item) => {
          const ob = {
            Detalle: item.Detalle,
            EsPrincipal: item.EsPrincipal === 'S',
            FechaAlta: item.FechaAlta,
            Telefono: item.Telefono
          }
          temp.push(ob)
        })
      }
      return temp
    },
    telefonoPrincipal (telefonos) {
      if (telefonos) {
        const TelefonosPrincipales = (telefonos || []).filter(
          (telefono) => telefono.EsPrincipal === 'S'
        )
        if (!TelefonosPrincipales.length) {
          return telefonos.length ? telefonos[0].Telefono : 'Sin telefono'
        } else {
          return TelefonosPrincipales[0].Telefono
        }
      } else {
        return ''
      }
    },
    abrirCaso (id, persona) {
      event.stopPropagation()
      const ruta = persona
        ? `/Caso?id=${id}&tab=datos#personas`
        : `/Caso?id=${id}`
      let routeData = this.$router.resolve({ path: ruta })
      window.open(routeData.href, '_blank')
    },
    editarMovimiento (movimiento, caratula) {
      event.stopPropagation()
      if (!movimiento) { return }
      movimiento.Caratula = caratula
      this.movimientoEditar = movimiento
      this.ModalMovimiento = true
    },
    diasCambioEstado (FechaEstado) {
      if (!FechaEstado) {
        return 'No hay un estado asignado.'
      }
      /*
      var fecha = new Date()
      var hoy = new Date()
      var year = FechaEstado.split('-')[0]
      var month = FechaEstado.split('-')[1] - 1
      var day = FechaEstado.split('-')[2].split(' ')[0] - 1
      fecha.setMonth(month)
      fecha.setFullYear(year)
      fecha.setDate(day)
      var resultado =
        Math.ceil(
          (fecha.getTime() - hoy.getTime()) / (1000 * 60 * 60 * 24)
        ) + 1
      return resultado * -1
      */
      const resultado = moment().diff(moment(FechaEstado), 'days')
      return resultado >= 0 ? resultado : resultado - 1
    },
    arrayHC (personas) {
      let array = []
      let totalHC = 0

      personas.forEach(p => {
        if (p.IdHistoriaClinica) {
          totalHC++
          if (array.length === 0) {
            array.push({
              Estado: p.EstadoHC,
              Cant: 1
            })
          } else {
            const i = array.findIndex(a => a.Estado === p.EstadoHC)
            i < 0
              ? array.push({
                Estado: p.EstadoHC,
                Cant: 1
              })
              : array[i].Cant++
          }
        }
      })

      let arrayFinal = array.map(a => {
        return `${a.Estado} (${a.Cant}/${totalHC})`
      })

      if (arrayFinal.length >= 2) {
        arrayFinal = arrayFinal.slice(0, 1)
        arrayFinal.push('...')
      }

      return arrayFinal.length === 0
        ? ['Sin datos']
        : arrayFinal
    },
    verInfoHc (m) {
      event.stopPropagation()

      this.ModalHC = true
      this.IdCaso = m.IdCaso

      const personas = m.PersonasCaso

      this.personas = personas
        .filter(p => p.Observaciones === 'Actor')
        .map(p => {
          return {
            persona: p.Apellidos + ', ' + p.Nombres,
            estado: p.EstadoHC || 'Sin datos',
            cambioEstado: p.FechaEstadoHC ? `Hace ${this.diasCambioEstado(p.FechaEstadoHC)} días` : 'Sin datos',
            centro: p.CentroMedicoHC || 'Sin datos',
            numero: p.NumeroHC || 'Sin datos'
          }
        })
    }
  }
}
</script>
