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

          return {
            id: e.IdEvento,
            summary: titulo,
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
      }
    })
  }
}
</script>
