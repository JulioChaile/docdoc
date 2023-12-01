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
    />
  </q-page>
</template>

<script>
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
      eventos: []
    }
  },
  created () {
    request.Get('/estudios/eventos', {}, r => {
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
      }
    })
  },
  mounted () {
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
          request.Post('/eventos/delete', { IdEvento: idEvento, IdEventoAPI }, r => {
            console.log(r)
            
            if (!r.Error) {
              this.eventos = this.eventos.filter(evento => evento.id !== parseInt(idEvento))
              this.$q.notify('El evento se elimino satisfactoriamente')

              document.querySelector('.calendar-event-detail').querySelector('button').click()
            }
          })
        })

        cardModal.querySelector('.ced-content').appendChild(button)
      }
    })
  },
  methods: {
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
