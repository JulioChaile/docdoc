<template>
  <!--q-card
    inline
    style="width: 600px; max-width: 90vw; background: rgba(255,255,255, 0.7);"
  -->
  <div id="InicioSesion">
    <!--q-card-section style="display:flex; flex-direction:column; justify-content:center; align-items:center;">
      <object
        type="image/svg+xml"
        data="statics/docdoc-home.svg"
        alt="Docdoc! Gestión"
        width="240"
        height="100">
        DocDoc!
      </object>
      <span class="text-h5">{{ titulo }}</span>
      <span class="text-subtitle2" style="opacity: 0.4">{{ subtitle }}</span>
    </q-card-section-->
    <q-card-section class="flex titulo-login">
      <span>Orientación Legal de Bolsillo</span>
    </q-card-section>
    <div v-if="codigo" class="text-subtitle2" style="padding-left: 16px; opacity: 0.4">{{ subtitle }}</div>
    <q-card-section class="field-login flex">
      <q-field
        outlined
        dense
        label-color="dark"
        class="max-width-cajas"
        v-bind:hint="helper"
        :error="!!error()"
        :error-label="error()"
      >
        <slot></slot>
      </q-field>
    </q-card-section>
    <q-card-section class="column botones-login">
      <a
        v-if="recuperar"
        @click="recuperar"
        class="cursor-pointer max-width-cajas --italic recuperar-cuenta"
      >
        Recuperar cuenta
      </a>
      <a
        v-if="inicio"
        @click="inicio"
        class="cursor-pointer max-width-cajas --italic recuperar-cuenta"
      >
        Usar otra cuenta
      </a>
      <q-btn
        dense
        outline
        class="boton-login max-width-cajas avenir-next"
        v-if="execute"
        @click="execute"
      >
      {{ continuar }}
      </q-btn>
    </q-card-section>
  </div>
  <!--/q-card-->
</template>

<script>
export default {
  props: ['value', 'titulo', 'label', 'continuar', 'error', 'loading', 'execute', 'inicio', 'helper', 'subtitle', 'recuperar', 'codigo'],
  methods: {
    recibirInput (newValue) {
      this.$emit('input', newValue)
    }
  }
}
</script>

<style>
.titulo-login {
  font-size : 3.5rem;
  line-height : 3.5rem;
  margin-bottom: 10%;
}

.q-card-section {
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  justify-content: space-around;
  font-size: 26px;
}

.q-card-subtitle {
  align-self: center;
}

.volver-inicio {
  padding-left: 25px;
  padding-top: 5px;
  text-align: left;
  text-decoration: none;
}

#InisioSesion {
  width: 75%;
}

#InicioSesion .q-field__control {
  padding: 0;
}

#InicioSesion .q-field__label, .boton-login {
  font-size: 16px;
  font-weight: 500;
}

.max-width-cajas {
  width: 100%;
  max-width: 300px;
}

#InicioSesion .q-field__control-container {
  flex-direction: column;
}

.boton-login {
  text-transform: none;
}

.recuperar-cuenta {
  text-align: right;
  text-decoration: none;
  color: #1E4B74;
}

#InicioSesion .q-field__control::before, #InicioSesion .q-btn__wrapper::before {
  content: '';
  background: -moz-linear-gradient(50% 100% 90deg,rgba(255, 255, 255, 1) 0%,rgba(253, 253, 253, 1) 44.8%,rgba(246, 246, 246, 1) 60.93%,rgba(235, 235, 235, 1) 72.43%,rgba(218, 218, 218, 1) 81.73%,rgba(196, 196, 196, 1) 89.7%,rgba(169, 169, 169, 1) 96.62%,rgba(153, 153, 153, 1) 100%);
  background: -webkit-linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(253, 253, 253, 1) 44.8%, rgba(246, 246, 246, 1) 60.93%, rgba(235, 235, 235, 1) 72.43%, rgba(218, 218, 218, 1) 81.73%, rgba(196, 196, 196, 1) 89.7%, rgba(169, 169, 169, 1) 96.62%, rgba(153, 153, 153, 1) 100%);
  background: -webkit-gradient(linear,50% 100% ,50% 0% ,color-stop(0,rgba(255, 255, 255, 1) ),color-stop(0.448,rgba(253, 253, 253, 1) ),color-stop(0.6093,rgba(246, 246, 246, 1) ),color-stop(0.7243,rgba(235, 235, 235, 1) ),color-stop(0.8173,rgba(218, 218, 218, 1) ),color-stop(0.897,rgba(196, 196, 196, 1) ),color-stop(0.9662,rgba(169, 169, 169, 1) ),color-stop(1,rgba(153, 153, 153, 1) ));
  background: -o-linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(253, 253, 253, 1) 44.8%, rgba(246, 246, 246, 1) 60.93%, rgba(235, 235, 235, 1) 72.43%, rgba(218, 218, 218, 1) 81.73%, rgba(196, 196, 196, 1) 89.7%, rgba(169, 169, 169, 1) 96.62%, rgba(153, 153, 153, 1) 100%);
  background: -ms-linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(253, 253, 253, 1) 44.8%, rgba(246, 246, 246, 1) 60.93%, rgba(235, 235, 235, 1) 72.43%, rgba(218, 218, 218, 1) 81.73%, rgba(196, 196, 196, 1) 89.7%, rgba(169, 169, 169, 1) 96.62%, rgba(153, 153, 153, 1) 100%);
  -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#999999' ,GradientType=0)";
  background: linear-gradient(0deg, rgba(255, 255, 255, 1) 0%, rgba(253, 253, 253, 1) 44.8%, rgba(246, 246, 246, 1) 60.93%, rgba(235, 235, 235, 1) 72.43%, rgba(218, 218, 218, 1) 81.73%, rgba(196, 196, 196, 1) 89.7%, rgba(169, 169, 169, 1) 96.62%, rgba(153, 153, 153, 1) 100%);
  opacity : 0.5;
  filter: alpha(opacity=50) progid:DXImageTransform.Microsoft.Alpha(opacity=50) progid:DXImageTransform.Microsoft.gradient(startColorstr='#999999',endColorstr='#FFFFFF' , GradientType=0);
}

.botones-login {
  justify-content: flex-start;
  padding: 16px;
  padding-top: 0;
}

.field-login {
  margin-left: auto;
  margin-right: auto;
  padding-bottom: 0;
}

@media screen and (max-width: 1023px) {
  .titulo-login {
    font-size : 1.75rem;
    line-height : 2.75rem;
    margin-bottom: 5%;
    text-align: center;
  }

  #InisioSesion {
    width: 100%;
  }

  .botones-login{
    align-content: center;
  }

  .field-login {
    justify-content: center;
  }
}
</style>
