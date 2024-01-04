<template>
  <q-page class="q-pa-lg">
    <daykeep-calendar
      :tab-labels="{
        month: 'Mes',
        week: 'Semana',
        threeDay: '3 Días',
        day: 'Día',
        agenda: 'Agenda'
      }"
      :event-array="eventos"
      event-ref="calendar"
    />

    <q-dialog v-model="ModalConfirmacionEliminar" prevent-close>
      <q-card style="padding:1rem;">
        <div class="text-h6">Eliminar Evento</div>

        <div style="float:right;">
          <q-btn color="primary" label="Eliminar" @click="eliminarEvento()" />
          <q-btn flat label="Cancelar" @click="ModalConfirmacionEliminar = false" />
        </div>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import moment from 'moment'
import { DaykeepCalendar } from '@daykeep/calendar-quasar'
import request from '../request'

const TIME_ZONE = 'America/Argentina/Buenos_Aires'
const coloresDocDoc = {
  11: 'negative',
  9: 'primary',
  5: 'warning',
  10: 'positive'
}

export default {
  components: {
    DaykeepCalendar
  },
  data () {
    return {
      eventos: [],
      interval: {
        startDate: '',
        endDate: ''
      },
      IdEventoEliminar: null,
      ModalConfirmacionEliminar: false
    }
  },
  mounted () {
    this.$watch('interval', ({ startDate, endDate }) => {
      if (!startDate && !endDate) return

      const containers = document.querySelectorAll('.calendar-header-label')
      const div = document.createElement('div')

      div.classList.add('div-loading-calendar', 'text-caption')
      div.innerHTML = 'Loading...'
      
      for (let i = 0; i < containers.length; i++) {
        containers[i].classList.add('items-center', 'flex', 'column')
        containers[i].appendChild(div)
      }

      request.Get('/estudios/eventos', { FechaInicio: startDate, FechaFin: endDate }, r => {
        const loading = document.querySelectorAll('.div-loading-calendar')

        for (let i = 0; i < loading.length; i++) {
          const element = loading[i];
          
          element.parentNode.removeChild(element)
        }

        if (r.length) {
          this.eventos = r.map(e => {
            const start = e.Comienzo.replace(' ', 'T')
            const end = e.Fin.replace(' ', 'T')
            const color = coloresDocDoc[parseInt(e.IdColor)]
            const titulo = e.Titulo.replace(' - DocDoc!', '')
            const idCaso = e.IdCaso
            const estadoDeProceso = e.EstadoAmbitoGestion

            return {
              id: e.IdEvento,
              IdEventoAPI: e.IdEventoAPI,
              summary: `${titulo}<div id="caso---${idCaso}---${estadoDeProceso}" class="link-caso" data-idEvento="${e.IdEvento}"></div>`,
              description: e.Descripcion,
              start: {
                dateTime: start, // ISO 8601 formatted
                timeZone: TIME_ZONE // Timezone listed as a separate IANA code
              },
              end: {
                dateTime: end,
                timeZone: TIME_ZONE
              },
              color: color
            }
          })

          this.eventos.sort((a, b) => {
            return new Date(a.start.dateTime) - new Date(b.start.dateTime)
          })

          setTimeout(() => {
            this.addContentToTitle('.calendar-event-summary')
            this.addContentToTitle('.ced-event-title')
            this.addContentToTitle('.calendar-agenda-event-summary')
          }, 1500);
        } else {
          this.eventos = []
        }
      })
    }, { deep: true })

    this.$root.$on("display-change-calendar", event => {
      const { startDate, endDate } = event

      if (this.interval.startDate !== startDate && this.interval.endDate !== endDate) {
        this.interval = {
          startDate,
          endDate
        }
      }
    })

    setTimeout(() => {
      this.addContentToTitle('.calendar-event-summary')
      this.addContentToTitle('.ced-event-title')
      this.addContentToTitle('.calendar-agenda-event-summary')
    }, 1500);

    document.addEventListener('click', e => {
      this.addContentToTitle('.calendar-event-summary')
      this.addContentToTitle('.ced-event-title')
      this.addContentToTitle('.calendar-agenda-event-summary')

      const cardModal = document.querySelector('.calendar-event-detail')

      if (cardModal) {
        const idEvento = cardModal.querySelector('.link-caso').dataset.idevento

        if (document.getElementById('boton-eliminar-' + idEvento)) return

        const button = document.createElement('div')
        const { IdEventoAPI } = this.eventos.find(evento => evento.id === parseInt(idEvento))

        button.id = 'boton-eliminar-' + idEvento
        button.classList.add('q-btn', 'q-btn-item', 'q-btn--rectangle', 'bg-negative', 'text-white', 'q-pa-xs', 'cursor-pointer')
        button.textContent = 'Eliminar'
        button.addEventListener('click', e => {
          this.IdEventoEliminar = idEvento
          this.IdEventoAPIEliminar = IdEventoAPI
          this.ModalConfirmacionEliminar = true
        })

        cardModal.querySelector('.ced-content').appendChild(button)
      }
    })
  },
  methods: {
    eliminarEvento () {
      this.ModalConfirmacionEliminar = false
      request.Post('/eventos/delete', { IdEvento: this.IdEventoEliminar, IdEventoAPI: this.IdEventoAPIEliminar }, r => {
          if (!r.Error) {
            this.eventos = this.eventos.filter(evento => evento.id !== parseInt(this.IdEventoEliminar))
            this.$q.notify('El evento se elimino satisfactoriamente')

            document.querySelector('.calendar-event-detail').querySelector('button').click()
          } else {
            this.$q.notify(r.Error)
          }
        })
    },
    addContentToTitle(selector) {
      const elements = document.querySelectorAll(selector)

      for (let i = 0; i < elements.length; i++) {
        const element = elements[i];
        const html = element.innerHTML.replace(/&lt;/g, '<').replace(/&gt;/g, '>')

        element.innerHTML = html
        
        const div = element.querySelector('div')
        const id = div.id.split('---')[1]
        const estadoDeProceso = div.id.split('---')[2]

        div.innerHTML = `<a style="color: blue; cursor: pointer">Ir al Caso</a> - Estado de Proceso: ${estadoDeProceso}`

        div.addEventListener('click', e => {
          let routeData = this.$router.resolve({ path: `/Caso?id=${id}` })
          window.open(routeData.href, '_blank')
        })
      }
    }
  }
}
</script>

<style>
.calendar-day-column-content > div:not([id]) {
  height: unset !important;
}
</style>
