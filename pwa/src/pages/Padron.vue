<template>
    <q-page class="q-px-lg">
        <div class="row q-mt-sm">
            <q-input
                v-model="busqueda"
                dense
                label="Buscar por Nombre, DNI o Domicilio"
                style="padding: 0 1rem 1rem 1rem; width: 500px"
            />
            <q-btn
                @click="buscar()"
                color="primary"
                style="padding-top:0px; margin-left: 20px;"
                dense
            >Buscar</q-btn>
        </div>

        <Loading v-if="loading" />

        <q-table
            v-else
            flat
            :data="Padron"
            :columns="columnas"
            row-key="name"
        />
    </q-page>
</template>

<script>
import { Notify } from 'quasar'
import request from '../request'
import Loading from '../components/Loading'

export default {
    components: { Loading },
  data () {
    return {
      Padron: [],
      busqueda: '',
      loading: false,
      columnas: [
        {
          name: 'persona',
          label: 'Persona',
          field: 'PERSONA',
          align: 'left'
        },
        {
          name: 'dni',
          label: 'DNI',
          field: 'DNI',
          align: 'left'
        },
        {
          name: 'domicilio',
          label: 'Domicilio',
          field: 'DOMICILIO',
          align: 'left'
        },
        {
          name: 'localidad',
          label: 'Localidad',
          field: 'LOCALIDAD',
          align: 'left'
        }
      ]
    }
  },
  methods: {
    buscar () {
        if (!this.busqueda) {
            Notify.create('El campo de busqueda no puede estar vacio')
            return
        }

        this.loading = true

        request.Get('/empresa/padron', { cadena: this.busqueda }, r => {
            this.Padron = r
            this.loading = false
        })
    }
  }
}
</script>