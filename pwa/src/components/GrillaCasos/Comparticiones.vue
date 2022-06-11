<template>
    <q-card style="min-width:400px;">
        <q-item style="background-color: black;">
            <span class="q-subheading" style="color:white;">Comparticiones</span>
        </q-item>
        <div v-if="!EstudioOrigen && !EstudiosDestino" style="margin-top: 20px; margin-left: 20px; margin-right: 20px">
        El caso ha sido compartido por este mismo estudio pero no se tienen datos sobre los estudios de destino.
        </div>
        <div v-else style="margin-top: 20px; margin-left: 20px; margin-right: 20px">
        {{EstudioOrigen ? 'El caso fue compartido desde el estudio: ' + EstudioOrigen  + '. ': 'El caso ha sido compartido por este mismo estudio.'}}
        <br>
        <br>
        {{EstudiosDestino ? 'Y fue compartido desde este estudio hacia los estudios: ' + listaEstudios(EstudiosDestino) + '.' : ''}}
        </div>
        <div style="display: flex;justify-content: center; margin-botton:2rem;">
        <q-btn
            v-if="EstudiosDestino"
            @click="modalDejar = true"
            color="negative"
            style="padding-top:0px; float: center; margin-bottom:20px; margin-top: 20px;"
        >Dejar de Compartir</q-btn>
        <q-btn
            @click="$emit('cerrar')"
            color="primary"
            style="padding-top:0px; float: center; margin-bottom:20px; margin-top: 20px; margin-left: 30px"
        >Cerrar</q-btn>
        </div>

        <q-dialog v-model="modalDejar">
            <EliminarComparticion
                :estudios="EstudiosDestino"
                :IdCaso="IdCaso"
                @cerrar="modalDejar = false"
                @eliminarComparticiones="eliminarComparticiones"
            />
        </q-dialog>
    </q-card>
</template>

<script>
import auth from '../../auth'
import EliminarComparticion from '../GrillaCasos/EliminarComparticion'
export default {
  name: 'Comparticiones',
  components: {
    EliminarComparticion
  },
  data () {
    return {
      EstudioOrigen: '',
      EstudiosDestino: [],
      modalDejar: false
    }
  },
  props: [ 'comparticiones', 'IdCaso' ],
  created () {
    const IdEstudio = auth.UsuarioLogueado.IdEstudio

    this.EstudioOrigen = ''
    this.EstudiosDestino = []

    if (this.comparticiones) {
      const estudioorigen = this.comparticiones.filter(c => c.IdEstudioOrigen !== parseInt(IdEstudio))
      estudioorigen.length !== 0 ? this.EstudioOrigen = estudioorigen[0].EstudioOrigen : this.EstudioOrigen = null

      const estudiosdestino = this.comparticiones.filter(c => c.IdEstudioOrigen === parseInt(IdEstudio))
      estudiosdestino.length !== 0 ? this.EstudiosDestino = estudiosdestino[0].EstudiosDestino : this.EstudiosDestino = null
    }
  },
  methods: {
    listaEstudios (estudios) {
      let lista = []
      estudios.forEach(e => {
        lista.push(e.Estudio)
      })

      let lista2 = ''
      lista.forEach(l => {
        lista2 = lista2 + l + ', '
      })

      return lista2.slice(0, -2)
    },
    eliminarComparticiones (IdsEstudios) {
      IdsEstudios.forEach(i => {
        this.EstudiosDestino = this.EstudiosDestino.filter(e => e.IdEstudio !== i)
      })

      this.$emit('eliminarComparticiones', IdsEstudios, this.IdCaso)
    }
  }
}
</script>

<style>

</style>
