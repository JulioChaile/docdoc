<template>
  <q-page>
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else class="casos__container">
      <TarjetaCaso
      v-for= "caso in buscarCaso" :key="caso.IdCaso"
      v-if="casos.length !== 0"
      :caso="caso"
      :clases="modo"
      @quitar = "quitarCaso($event)"
      style="margin-top: 2rem; margin-bottom: 2rem;  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);"
      />
      <span v-if="casos.length === 0" style="margin-left: auto; margin-right:auto; margin-top:3rem;font-style: italic;" class="text-h6">Usted aún no compartió ningun caso</span>
    </div>
  </q-page>
</template>

<script>
import TarjetaCaso from '../components/TarjetaCaso.vue'
import request from '../request'
import Loading from '../components/Loading'
export default {
  components: {
    TarjetaCaso,
    Loading
  },
  name: 'Casos',
  data () {
    return {
      casos: [],
      busqueda: '',
      tipoBusqueda: 't', // t (Todos) - p (Personas) - j (Juzgados) - tp (Tipo de Caso),
      estado: 'A',
      verAlta: true,
      verArchivados: false,
      modo: 'casos__listado',
      loading: true
    }
  },
  created () {
    request.Get('/casos/compartidos', {}, r => {
      if (!r.Error) {
        r.forEach(c => {
          c.PersonasCaso = JSON.parse(c.PersonasCaso)
          this.casos.push(c)
        })
        this.casos = this.casos.sort((a, b) => {
          if (a.FechaAlta < b.FechaAlta) {
            return 1
          }
          if (a.FechaAlta > b.FechaAlta) {
            return -1
          }
          return 0
        })
        this.loading = false
      }
    })
  },
  computed: {
    buscarCaso () {
      const casos = this.casos.filter(c => c.Estado === this.estado || this.estado === 'T')
      switch (this.tipoBusqueda) {
        case 't' :
          return casos.filter(caso => {
            return caso.Caratula.toLowerCase().includes(this.busqueda.toLowerCase()) ||
          caso.Juzgado.toLowerCase().includes(this.busqueda.toLowerCase()) ||
          caso.TipoCaso.toLowerCase().includes(this.busqueda.toLowerCase()) ||
          caso.PersonasCaso.filter(p =>
            (p.Nombres && p.Nombres.toLowerCase().includes(this.busqueda.toLowerCase())) ||
            (p.Apellidos && p.Apellidos.toLowerCase().includes(this.busqueda.toLowerCase())) ||
            (p.Documento && p.Documento.toLowerCase().includes(this.busqueda.toLowerCase()))
          ).length
          })
        case 'p' :
          return casos.filter(caso => {
            return caso.PersonasCaso.filter(p =>
              (p.Nombres && p.Nombres.toLowerCase().includes(this.busqueda.toLowerCase())) ||
              (p.Apellidos && p.Apellidos.toLowerCase().includes(this.busqueda.toLowerCase())) ||
              (p.Documento && p.Documento.includes(this.busqueda))
            ).length
          })
        case 'j' :
          return casos.filter(caso => {
            return caso.Juzgado.toLowerCase().includes(this.busqueda.toLowerCase())
          })
        case 'tp' :
          return casos.filter(caso => {
            return caso.TipoCaso.toLowerCase().includes(this.busqueda.toLowerCase())
          })
      }
    }
  },
  methods: {
    isMobile () {
      if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        return true
      } else {
        return false
      }
    },
    quitarCaso (caso) {
      this.casos.splice(this.casos.indexOf(caso), 1)
    },
    filtrarArchivados () {
      if (this.verAlta && this.verArchivados) {
        this.estado = 'T'
      } else if (this.verArchivados) {
        this.estado = 'R'
      } else if (this.verAlta) {
        this.estado = 'A'
      } else {
        this.estado = 'A'
      }
    }
  }
}
</script>

<style>
.casos__container {
  display: flex;
  flex-wrap: wrap;
  align-items:flex-start;
}

@media only screen
and (max-device-width: 600px){
  .q-btn-group {
    margin:.5em -.5em .5em -.5em !important
  }
}
</style>
