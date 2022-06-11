<template>
  <q-page>
  </q-page>
</template>

<script>
import request from '../request'
import axios from 'axios'

export default {
  data () {
    return {
      casos: [],
      casosGeo: [],
      index: 0
    }
  },
  methods: {
    traerCasos () {
      request.Get('/test', {offset: this.index}, r => {
        r.forEach(c => {
          this.casos.push(c)
        })
        if (r.length) {
          this.geoLocalizar()
        } else {
          alert('LISTO CAPO')
        }
      })
    },
    geoLocalizar () {
      const c = this.casos[this.index]

      axios.get('https://maps.googleapis.com/maps/api/geocode/json', {
        params: {
          address: c.DOMICILIO + ', Tucuman',
          key: 'AIzaSyDw8NFG-Llw1Kytr1SqaJ0aT7G4lRlP38Y'
        }
      })
        .then(res => {
          let params = {}
          this.index++

          if (res.data.status === 'OK') {
            params = {
              latitud: res.data.results[0].geometry.location.lat,
              longitud: res.data.results[0].geometry.location.lng,
              id: c['ID-CASO']
            }
          } else {
            params = {
              latitud: 0,
              longitud: 0,
              id: c['ID-CASO']
            }
          }

          request.Post('/test/update', params, r => {
            this.casosGeo.push({ ...c, ...params })
            if (this.index === this.casos.length) {
              this.traerCasos()
            } else {
              setTimeout(() => {
                this.geoLocalizar()
              }, 250)
            }
          })
        })
        .catch(e => {
          console.log(e)
        })
    }
  }
}
</script>
