<template>
 <q-page>
   <div padding class="card-examples row items-start">
      <q-card
        v-for="u in utilidades" :key="u.titulo"
        inline class="bigger q-ma-sm tarjeta-utilidades"
        style="margin:2em; background-color: white;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);"
      >
        <q-card-section style="text-align: center; background:black; color:white;">
          {{ u.titulo }}
        </q-card-section>
        <q-card-section style="margin-top:1rem;">
          <img :src="u.foto"
            style="cursor: pointer; max-width: 262px;"
            @click="openURL(u.link)"
          >
        </q-card-section>
        <q-card-section>
          <p class="text-faded color:blue"
            style="word-break: break-all;
            cursor:pointer"
            @click="openURL(u.link)"
          >
            {{u.link}}
          </p>
        </q-card-section>
      </q-card>
   </div>
  </q-page>
</template>

<script>
import { openURL } from 'quasar'

export default {
  name: 'Utilidades',
  data () {
    return {
      utilidades: [
        {
          titulo: 'Notificaciones diarias',
          link: 'http://notificaciones.docdoc.com.ar',
          foto: 'statics/utilidades/notificaciones.PNG'
        },
        {
          titulo: 'Actualización de Importes',
          link: 'http://www.colegioabogadostuc.org.ar/herramientas/actualizacion-de-importes/',
          foto: 'statics/utilidades/actualizacion-importes.PNG'
        },
        {
          titulo: 'Consulta de Expedientes',
          link: 'https://l6.justucuman.gov.ar/',
          foto: 'statics/utilidades/cons-exp.PNG'
        },
        {
          titulo: 'Padrón de Abogados',
          link: 'http://www.colegioabogadostuc.org.ar/herramientas/padron-de-abogados/',
          foto: 'statics/utilidades/padron-abogados.PNG'
        },
        {
          titulo: 'Feriados Nacionales',
          link: 'https://www.argentina.gob.ar/interior/feriados',
          foto: 'statics/utilidades/feriados.PNG'
        },
        {
          titulo: 'Escritorio Virtual',
          link: 'http://www.colegioabogadostuc.org.ar/escritorio/',
          foto: 'statics/utilidades/esc-virtual.PNG'
        },
        {
          titulo: 'Búsqueda de Personas',
          link: 'http://www.webdedatos.com/Personas/',
          foto: 'statics/utilidades/personas.PNG'
        },
        {
          titulo: 'Consulta de Situación Crediticia',
          link: 'http://www.bcra.gob.ar/BCRAyVos/Situacion_Crediticia.asp',
          foto: 'statics/utilidades/situacion-crediticia.PNG'
        },
        {
          titulo: 'Seguimiento de Envíos',
          link: 'https://www.correoargentino.com.ar/formularios/ondnc',
          foto: 'statics/utilidades/seguimiento-envios.PNG'
        },
        {
          titulo: 'Dirección General de Rentas - Constancia de Inscripción',
          link: 'http://extranet.rentastucuman.gov.ar/nomina/rentastuc2/constancia.php',
          foto: 'statics/utilidades/constancia-dirrentas.PNG'
        },
        {
          titulo: 'AFIP - Consultar Constancia',
          link: 'https://seti.afip.gob.ar/padron-puc-constancia-internet/ConsultaConstanciaAction.do',
          foto: 'statics/utilidades/constancia-afip.PNG'
        }
      ]
    }
  },
  methods: {
    openURL
  }
}
</script>
<style lang="stylus">
.tarjeta-utilidades:hover{
    transform: scale(1.1, 1.1);
    transition: 0.3s;
}
.card-examples
  .q-card
    width 200px
    height 300px
  .bigger
    width 300px
    height 300px
    max-width 90vw
  @media (max-width $breakpoint-xs-max)
    .q-card
      width 100%,

</style>
