<template>
  <q-page>
    <div
      style="background:white;
    padding:1em 1em 1em 1em;
    margin-top:-.5em;"
      class="shadow-1"
    >
      <q-input
        v-model="busqueda"
        :debounce="600"
        placeholder="Buscar Casos"
        @change="buscarCaso"
        style="margin: 0 .6em 0 .6em"
      />
      <!--div style="margin:.5em 0 0 .9em">
        <q-chip
      icon="face"
      color="primary"
      style="margin:.4em .2em .2em 0"
      closable
      small
    >
      Raul Moreno
    </q-chip>
    <q-chip
      icon="location_on"
      color="primary"
      style="margin:.4em .2em .2em 0"
      closable
      small
    >
      Civ y Com
    </q-chip>
      </div-->
      <q-btn-group flat push>
        <q-btn
          style="font-size:12px"
          push
          label="Todos"
          @click="tipoBusqueda = 't'"
          v-bind:outline="tipoBusqueda === 't'"
          color="primary"
        />
        <q-btn
          style="font-size:12px"
          push
          label="Personas"
          @click="tipoBusqueda = 'p'"
          v-bind:outline="tipoBusqueda === 'p'"
          color="primary"
        />
        <q-btn
          style="font-size:12px"
          push
          label="Juzgados"
          @click="tipoBusqueda = 'j'"
          v-bind:outline="tipoBusqueda === 'j'"
          color="primary"
        />
        <q-btn
          style="font-size:12px"
          push
          label="Tipos de Casos"
          @click="tipoBusqueda = 'c'"
          v-bind:outline="tipoBusqueda === 'c'"
          color="primary"
        />
        <q-btn
          style="font-size:12px"
          push
          label="N° Expediente"
          @click="tipoBusqueda = 'e'"
          v-bind:outline="tipoBusqueda === 'e'"
          color="primary"
        />
      </q-btn-group>
      <q-checkbox v-model="verAlta" label="No archivados" @input="filtrarArchivados()" />
      <q-checkbox
        v-model="verArchivados"
        label="Archivados"
        @input="filtrarArchivados()"
        style="margin-left: 10px"
      />
      <q-btn-group style="margin-left: 20px" outline v-if="!isMobile()">
        <q-btn outline icon="list" @click="modo = 'casos__listado'" />
        <q-btn outline icon="grid_on" @click="modo = 'casos__grilla'" />
      </q-btn-group>
    </div>
    <div v-if="casos.length === 0 && loading">
      <Loading />
    </div>
    <div v-else>
      <q-infinite-scroll
        :key="busqueda"
        :disable="noHayMasCasos"
        @load="onLoad"
        style="width: 100%; padding: 0px; margin: 0px"
        class="casos__container"
        :offset="500"
      >
        <TarjetaCaso
          v-for="caso in buscarCaso"
          :key="caso.IdCaso"
          :caso="caso"
          :clases="modo"
          :perentorioHome="true"
          @quitar="quitarCaso($event)"
          style="margin-top: 2rem; margin-bottom: 2rem; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);"
        />
        <template v-slot:loading>
          <div class="row justify-center q-my-md">
            <q-spinner-dots
              color="primary"
              size="100px"
              style="position: fixed; bottom: 10px; left: 50%"
            />
          </div>
        </template>
      </q-infinite-scroll>
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
      tipoBusqueda: 't', // t (Todos) - p (Personas) - j (Juzgados) - c (Tipo de Caso),
      estado: 'A',
      verAlta: true,
      verArchivados: false,
      noHayMasCasos: false,
      modo: 'casos__grilla',
      loading: true,
      objetivos: [],
      idCasos: []
    }
  },
  created () {
    request.Get('/casos', { Estado: 'T' }, (r) => {
      if (!r.Error) {
        r.forEach((c) => {
          this.idCasos.push(c.IdCaso)
          try {
            c.PersonasCaso = JSON.parse(c.PersonasCaso)
          } catch (error) {
            console.log(error)
            c.PersonasCaso = []
          }
          this.casos.push(c)
        })
        request.Get(`/objetivos?IdsCaso=[${this.idCasos}]`, {}, (r) => {
          if (!r.Error) {
            this.casos.map((c) => {
              c.Objetivos = r[c.IdCaso]
            })
          }
          this.loading = false
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
      }
    })
  },
  computed: {
    buscarCaso () {
      const casos = this.casos.filter(
        (c) => c.Estado === this.estado || this.estado === 'T'
      )
      switch (this.tipoBusqueda) {
        case 't':
          return casos.filter((caso) => {
            return (
              caso.Caratula.toLowerCase().includes(
                this.busqueda.toLowerCase()
              ) ||
              caso.Juzgado.toLowerCase().includes(
                this.busqueda.toLowerCase()
              ) ||
              caso.TipoCaso.toLowerCase().includes(
                this.busqueda.toLowerCase()
              ) ||
              caso.NroExpediente.includes(this.busqueda) ||
              caso.PersonasCaso.filter(
                (p) =>
                  (p.Nombres &&
                    p.Nombres.toLowerCase().includes(
                      this.busqueda.toLowerCase()
                    )) ||
                  (p.Apellidos &&
                    p.Apellidos.toLowerCase().includes(
                      this.busqueda.toLowerCase()
                    )) ||
                  (p.Documento &&
                    p.Documento.toLowerCase().includes(
                      this.busqueda.toLowerCase()
                    ))
              ).length
            )
          })
        case 'p':
          return casos.filter((caso) => {
            return caso.PersonasCaso.filter(
              (p) =>
                (p.Nombres &&
                  p.Nombres.toLowerCase().includes(
                    this.busqueda.toLowerCase()
                  )) ||
                (p.Apellidos &&
                  p.Apellidos.toLowerCase().includes(
                    this.busqueda.toLowerCase()
                  )) ||
                (p.Documento && p.Documento.includes(this.busqueda))
            ).length
          })
        case 'j':
          return casos.filter((caso) => {
            return caso.Juzgado.toLowerCase().includes(
              this.busqueda.toLowerCase()
            )
          })
        case 'c':
          return casos.filter((caso) => {
            return caso.TipoCaso.toLowerCase().includes(
              this.busqueda.toLowerCase()
            )
          })
        case 'e':
          return casos.filter((caso) => {
            return caso.NroExpediente.includes(this.busqueda)
          })
      }
    }
  },
  watch: {
    busqueda () {
      this.casos = []
      this.loading = true
      this.onLoad(0, () => {})
    }
  },
  methods: {
    onLoad (index, done) {
      request.Get(
        '/casos',
        {
          Estado: 'T',
          Offset: this.casos.length,
          Cadena: this.busqueda,
          Tipo: this.tipoBusqueda.toUpperCase()
        },
        (r) => {
          if (!r.Error) {
            r.forEach((c) => {
              try {
                c.PersonasCaso = JSON.parse(c.PersonasCaso)
              } catch (error) {
                console.log(error)
                c.PersonasCaso = []
              }
              this.casos.push(c)
            })
            request.Get(`/objetivos?IdsCaso=[${this.idCasos}]`, {}, (r) => {
              if (!r.Error) {
                this.casos.map((c) => {
                  c.Objetivos = r[c.IdCaso]
                })
              }
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
            this.noHayMasCasos = r.length === 0
          }
          this.loading = false
          done()
        }
      )
    },
    isMobile () {
      if (
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
          navigator.userAgent
        )
      ) {
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
  align-items: flex-start;
}

@media only screen and (max-device-width: 600px) {
  .q-btn-group {
    margin: 0.5em -0.5em 0.5em -0.5em !important;
  }
}
</style>
