<template>
    <q-card style="min-width:400px;">
        <q-item style="background-color: black;">
            <span class="q-subheading" style="color:white;">Dejar de Compartir</span>
        </q-item>
        <ul>
            <li
              v-for="(Estudio, index) in EstudiosDestino"
              :key="Estudio.IdEstudio"
            >
              <q-checkbox
                v-model="arrayCheck[index]"
                :label="Estudio.Estudio"
              />
            </li>
        </ul>
        <div style="display: flex;justify-content: center; margin-botton:2rem;">
            <q-btn
                @click="eliminarComparticiones()"
                color="negative"
                style="padding-top:0px; float: center; margin-bottom:20px; margin-top: 20px;"
            >Dejar de Compartir</q-btn>
            <q-btn
                @click="$emit('cerrar')"
                color="primary"
                style="padding-top:0px; float: center; margin-bottom:20px; margin-top: 20px; margin-left: 30px"
            >Cerrar</q-btn>
        </div>
    </q-card>
</template>

<script>
import request from '../../request'
export default {
  name: 'EliminarComparticion',
  data () {
    return {
      EstudiosDestino: [],
      arrayCheck: []
    }
  },
  props: [ 'estudios', 'IdCaso' ],
  created () {
    this.EstudiosDestino = this.estudios

    // Hacer esto es estupido porque el q-checkbox deberia modificar
    // el objeto si yo le pusiera la propiedad model a EstudiosDestino
    // pero por alguna razon no lo hace y esta es la mejor forma que encontre
    // de hacer que esta verga funcione, solo espero que los array no se
    // desordenen por azares del destino
    for (let i = 0; i < this.EstudiosDestino.length; i++) {
      this.arrayCheck[i] = false
    }
  },
  methods: {
    eliminarComparticiones () {
      let IdsEstudios = []

      if (this.arrayCheck.length === 0) {
        this.$q.notify({
          color: 'red',
          message: 'Debe seleccionar al menos un estudio para continuar.'
        })

        return
      }

      for (let i = 0; i < this.arrayCheck.length; i++) {
        if (this.arrayCheck[i]) {
          IdsEstudios.push(this.EstudiosDestino[i].IdEstudio)
        }
      }

      request.Post(`/casos/eliminar-comparticion/${this.IdCaso}`, { IdsEstudios: JSON.stringify(IdsEstudios) }, r => {
        if (r.length !== 0) {
          let lista = []
          this.EstudiosDestino.forEach(e => {
            if (r[e.IdEstudio]) {
              lista.push(e.Estudio)
              IdsEstudios = IdsEstudios.filter(f => f !== e.IdEstudio)
            }
          })

          let lista2 = ''
          lista.forEach(l => {
            lista2 = lista2 + l + ', '
          })

          this.$q.notify({
            color: 'red',
            message: `Ha ocurrido un error al dejar de compartir el caso con los siguientes estudios: ` + lista2.slice(0, -2) + '.'
          })
          this.$emit('eliminarComparticiones', IdsEstudios)
          this.Estudios.forEach(e => {
            if (r[e.IdEstudio]) {
              this.$q.notify({
                color: 'red',
                message: 'Orurrio un error con el estudio ' + e.Estudio + ' : ' + r[e.IdEstudio] + '.'
              })
            }
          })
        } else {
          this.$emit('eliminarComparticiones', IdsEstudios)
          this.$q.notify({
            color: 'green',
            message: `El caso se dejo de compartir a los estudios seleccionados.`
          })
        }
      })
    }
  }
}
</script>
