<template>
    <q-card style="padding:1rem;">
      <span class="text-h6">Cambiar Estado</span>
      <span>
        <p>
          Al cambiar el estado de un caso, este se ocultará de la lista.
        </p>
      </span>
      <div class="q-gutter-sm column">
        <q-radio v-model="estado" val="R" label="Archivado" />
        <q-radio v-model="estado" val="E" label="Archivo ex-activo" />
        <q-radio v-model="estado" val="F" label="Finalizado" />
      </div>
      <div style="float:right;">
        <q-btn v-if="!loading" color="primary" label="Archivar" @click="archivarCaso()" />
        <q-spinner
          v-else
          color="teal"
          class="self-center"
          size="2em"
        />
        <q-btn flat label="Cancelar" @click="$emit('cerrar')" />
      </div>
    </q-card>
</template>

<script>
import request from '../../request'
import { Notify, QRadio, QSpinner } from 'quasar'
import axios from 'axios'
export default {
  name: 'ArchivarCaso',
  props: [ 'IdCaso' ],
  components: {
    QRadio,
    QSpinner
  },
  data () {
    return {
      estado: 'R',
      coords: {},
      persona: {},
      loading: true
    }
  },
  created () {
    request.Get(`/casos`, { id: this.IdCaso }, (r) => {
      if (!r.Error) {
        const personas = r.PersonasCaso ? r.PersonasCaso : []

        this.persona = personas.filter(p => p.EsPrincipal === 'S')[0]

        if (this.persona.Domicilio) {
          axios.get('https://maps.googleapis.com/maps/api/geocode/json', {
            params: {
              address: this.persona.Domicilio + ', Tucuman',
              key: 'AIzaSyDw8NFG-Llw1Kytr1SqaJ0aT7G4lRlP38Y'
            }
          })
            .then(res => {
              if (res.data.status === 'OK') {
                this.coords = {
                  Latitud: res.data.results[0].geometry.location.lat,
                  Longitud: res.data.results[0].geometry.location.lng
                }
              }
              this.loading = false
            })
        } else {
          this.loading = false
        }
      } else {
        console.log('Hubo un error al traer el caso.')
      }
    })
  },
  methods: {
    archivarCaso () {
      this.loading = true
      if (this.coords.Latitud) {
        const casoP = [{
          Apellidos: this.persona.Apellidos,
          Nombres: this.persona.Nombres,
          Documento: this.persona.Documento,
          Telefono: this.persona.Telefonos.filter(t => t.EsPrincipal === 'S').length
            ? this.persona.Telefonos.filter(t => t.EsPrincipal === 'S')[0].Telefono
            : this.persona.Telefonos[0].Telefono,
          Domicilio: this.persona.Domicilio,
          Prioridad: 3,
          IdOrigen: 2,
          IdEstadoCasoPendiente: 36,
          Lesion: '',
          ...this.coords
        }]

        request.Post('/casos-pendientes/alta', {casos: JSON.stringify(casoP)}, r => {
          if (r.length > 0 || r.Error) {
            r.length > 0
              ? r.forEach(e => {
                Notify.create(`Hubo un error al subir los datos de ${e.persona}: ${e.error}`)
              })
              : Notify.create(r.Error)
          }
        })
      }

      request.Put(`/casos/${this.IdCaso}/archivar`, { Estado: this.estado }, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
          this.$emit('cerrar')
        } else {
          Notify.create('El caso ha sido archivado con exito.')
          this.loading = false
          this.$emit('archivado', this.estado)
        }
      })
    }
  }
}
</script>
