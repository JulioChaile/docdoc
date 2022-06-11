<template>
  <div style="width:100%">
    <q-expansion-item class="whitebg shadow-1" icon="adjust" label="Civil y comercial">
      <q-expansion-item icon="filter_1" label="Primera nominación (3 mov)">
        <q-banner
          type="negative"
          avatar="../statics/img/pipo-avatar.png"
          style="margin-top:.2em"
        >
        <div class="row float-left">
          <div class="col-12">
            10 caracteres de la caratula - <strong> Expte: 3545/17 </strong>
          </div>
          <div class="col-12">
            Este movimiento es perentorio.. es URGENTE
          </div>
        </div>
        <div class="float-right" >
          1 día.
        </div>
        </q-banner>
        <q-banner
          color="primary"
          avatar="../statics/img/jorge-avatar.png"
          style="margin-top:.2em"
        >
        <div class="row float-left">
          <div class="col-12">
            10 caracteres de la caratula - <strong> Expte: 3545/17 </strong>
          </div>
          <div class="col-12">
            Aca va el movimiento. Esta azul porque mi estudio tiene que gestionar algo.
          </div>
        </div>
        <div class="float-right" >
          5 días.
        </div>
        </q-banner>
        <q-banner
          type="warning"
          avatar="../statics/img/guido-avatar.png"
          style="margin-top:.2em"
        >
        <div class="row col-8 float-left">
          <div class="col-12">
            10 caracteres de la caratula - <strong> Expte: 3545/17 </strong>
          </div>
          <div class="col-8">
            Aca va el movimiento. Está amarillo porque el que tiene que hacer algo es el juzgado o alguien ajeno a mi estudio. Debería poder tener un movimiento super largo aca por eso estoy llenando esto con caracteres para ver como se ve, no es mas que texto basura....
          </div>
        </div>
        <div class="col-4 " >
          Vence hoy.
        </div>
        </q-banner>
        <q-banner
          type="positive"
          style="margin-top:.2em"
          detail="18/10/18 - Este fue el último movimiento y su fecha de realizado"
        >
          Carátula de un juicio sin movimientos pendientes - <strong> Expte: 3545/17 </strong> <span style="float:right"> Sin movimiento hace 5 días. </span>
        </q-banner>
        <q-banner
          type="positive"
          style="margin-top:.2em"
          detail="18/10/18 - Este fue el último movimiento y su fecha de realizado"
        >
          Carátula de OTRO juicio sin movimientos pendientes - <strong> Expte: 3545/17 </strong> <span style="float:right"> Sin movimiento hace 85 días. </span>
        </q-banner>
      </q-expansion-item>
      <q-expansion-item icon="filter_2" label="Segunda nominación (0 mov)">
        <q-banner
          type="positive"
          style="margin-top:.2em"
        >
          <strong> Expte: 3545/17 </strong> - Carátula de un juicio sin movimientos pendientes.
        </q-banner>
        <q-banner
          type="positive"
          style="margin-top:.2em"
        >
          <strong> Expte: 3545/17 </strong> - Carátula de otro juicio sin movimientos pendientes.
        </q-banner>
      </q-expansion-item>
    </q-expansion-item>
  </div>
</template>
<script>
export default {
}
</script>
<style>
  .whitebg{
    margin-top:1em;
    background-color: white
  }
  q-banner{
    margin-top:.2em
  }
  @keyframes destaco {
    0%{
      transform: scale(1,1,1)
    }
    100%{
      transform: scale(1.009,1.009,1.009)
    }
  }
  .q-banner:hover{
    animation: destaco 100ms ease-in;
    transform: scale(1.009, 1.009);
  }
  .q-banner-detail{
    float:right
  }
</style>
