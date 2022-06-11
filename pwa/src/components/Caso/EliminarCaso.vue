<template>
    <q-card style="padding:1rem;">
        <span class="text-h6">Eliminar Caso</span>
        <span>
            <p>
            Al eliminar un caso, este no podrá ser recuperado en el futuro.
            ¿Está seguro que desea eliminar este caso?
            </p>
            <div>
            <strong>Al eliminarse el caso se eliminarán también los siguientes elementos:</strong>
            </div>
            <div v-for="(val, info) in infoBorrar" :key="info">{{ mapBorrado[info] }} : {{ val }}</div>
        </span>

        <div style="float:right;">
            <q-btn color="primary" label="Eliminar" @click="bajaCaso()" />
            <q-btn flat label="Cancelar" @click="$emit('cerrar')" />
        </div>
    </q-card>
</template>

<script>
import request from '../../request'
import { Notify } from 'quasar'
export default {
  name: 'EliminarCaso',
  data () {
    return {
      infoBorrar: null,
      mapBorrado: {
        CantPersonas: 'Cantidad de personas',
        CantMovimientos: 'Cantidad de movimientos',
        CantUsuariosCaso: 'Cantidad de usuarios del caso',
        CantMultimedia: 'Cantidad de archivos multimedia'
      }
    }
  },
  props: [ 'IdCaso' ],
  created () {
    request.Get(`/casos/${this.IdCaso}/previsualizar-borrado`, {}, r => {
      if (r.Error) {
        Notify.create(r.Error)
      } else {
        this.infoBorrar = r
      }
    }
    )
  },
  methods: {
    bajaCaso () {
      request.Put(`/casos/${this.IdCaso}/baja`, {}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          Notify.create('El caso se ha eliminado correctamente.')
          this.$emit('eliminado')
        }
      })
    }
  }
}
</script>
