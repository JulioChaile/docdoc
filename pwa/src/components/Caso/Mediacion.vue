<template>
  <q-card
    class="bg-white"
    :style="VistaTab ? 'background-color: transparent !important; box-shadow: none !important' : ''"
  >
    <q-item v-if="!VistaTab" style="background-color: black;">
      <span class="q-subheading" style="color:white;">Mediacion del caso</span>
    </q-item>
    <div
      class="q-my-sm q-mx-md"
    >

      <!-- Vista sin mediacion -->
      <div
        v-if="!IdMediacion && !CargarMediacion"
        :class="VistaTab ? 'column items-center text-center' : ''"
      >
        Este caso no tiene mediacion cargada
        <br>
        ¿Desea cargar datos de la mediacion?
        <br>
        <q-btn
          v-if="!VistaTab"
          @click="$emit('cerrar')"
          color="red"
          class="text-capitalize"
          style="padding-top:0px; float: right; margin-bottom:10px; margin-right: 20px; margin-top: 10px"
        >Cerrar</q-btn>
        <q-btn
          @click="CargarMediacion = true"
          color="primary"
          class="text-capitalize"
          :style="VistaTab ? 'width: 200px; margin-top: 20px' : 'padding-top:0px; float: right; margin-bottom:10px; margin-right: 20px; margin-top: 10px'"
        >Cargar Datos</q-btn>
      </div>

      <!-- Mediacion Cargada -->
      <div
        v-if="IdMediacion"
        :style="VistaTab ? 'width: 100%' : 'width: 500px'"
      >
        <!-- Tabs -->
        <q-tabs
          v-if="DatosMediacion.IdMediador"
          v-model="tab"
          dense
          class="text-black"
          active-color="primary"
          indicator-color="skyblue"
          align="left"
          narrow-indicator
          style="padding: 0; margin: 0"
          @input="changeTab"
        >
          <q-tab class="bg-transparent" name="mediacion" label="Mediacion"/>
          <q-tab class="bg-transparent" name="mediador" label="Mediador"/>
        </q-tabs>
        <div v-if="loading">
          <Loading />
        </div>

        <!-- Paneles -->
        <q-tab-panels
          v-else
          class="bg-transparent"
          v-model="tab"
          animated
          keep-alive
        >
          <!-- Tab Mediacion -->
          <q-tab-panel style="padding: 0" name="mediacion">
            <!-- Datos Mediacion -->
            <div v-if="!EditarMediacion" class="row" style="justify-content: space-between; position: relative">
              <q-icon
                right
                color="primary"
                style="position: absolute; top: 5px; right: 5px; cursor: pointer"
                name="fas fa-edit"
                size="20px"
                @click="habilitarEdicion()"
              >
                <q-tooltip>Editar datos</q-tooltip>
              </q-icon>
              <div class="col-6">
                <data-item
                  :text="DatosMediacion.IdMediador ? DatosMediador.Nombre : 'Sin Mediador Sorteado.'"
                  text-class="text-weight-light text-h6"
                  label="Mediador"
                  labelColor="accent"
                />
              </div>
              <div class="col-6 justify-center self-center column" style="display: flex">
                <q-btn
                  @click="CargarMediador = true"
                  color="accent"
                  class="text-capitalize self-center"
                  style="padding-top:0px; margin-top: 10px; width: 60%"
                >{{ DatosMediacion.IdMediador ? 'Cambiar' : 'Cargar' }} Mediador</q-btn>
                <q-btn
                  v-if="DatosMediacion.IdMediador && !crearChat"
                  @click="abrirChat()"
                  color="teal"
                  class="text-capitalize self-center"
                  style="padding-top:0px; margin-top: 10px; width: 60%"
                >Chat</q-btn>
                <q-spinner
                  v-else-if="DatosMediacion.IdMediador"
                  color="teal"
                  class="self-center"
                  style="margin-top: 10px"
                  size="3em"
                />
              </div>
              <div class="col-6 q-mt-md">
                <data-item
                  :text="DatosMediacion.Legajo || 'Sin Legajo Cargado'"
                  text-class="text-weight-light text-h6"
                  label="Legajo"
                  labelColor="accent"
                />
              </div>
              <div class="col-6 q-mt-md">
                <data-item
                  :text="DatosMediacion.Bono || 'Sin Bono Cargado'"
                  text-class="text-weight-light text-h6"
                  label="Bono"
                  labelColor="accent"
                />
              </div>
              <div class="col-6">
                <data-item
                  :text="DatosMediacion.Beneficio || 'Sin Beneficio Cargado'"
                  text-class="text-weight-light text-h6"
                  label="Beneficio"
                  labelColor="accent"
                />
              </div>
              <div class="col-6">
                <data-item
                  :text="DatosMediacion.EstadoBeneficio || 'Sin Estado de Beneficio Cargado'"
                  text-class="text-weight-light text-h6"
                  label="Estado Beneficio"
                  labelColor="accent"
                />
              </div>
              <div class="col-6">
                <data-item
                  :text="DatosMediacion.FechaBonos ? DatosMediacion.FechaBonos : 'Sin Fecha'"
                  text-class="text-weight-light text-h6"
                  label="Fecha Bonos"
                  labelColor="accent"
                />
              </div>
              <div class="col-6">
                <data-item
                  :text="DatosMediacion.FechaPresentado ? DatosMediacion.FechaPresentado : 'Sin Fecha'"
                  text-class="text-weight-light text-h6"
                  label="Fecha Presentado"
                  labelColor="accent"
                />
              </div>
              <div class="col-6">
                <data-item
                  :text="DatosMediacion.FechaProximaAudiencia ? DatosMediacion.FechaProximaAudiencia.split(' ')[0] : 'Sin Fecha Proxima Audiencia'"
                  text-class="text-weight-light text-h6"
                  label="Fecha Proxima Audiencia"
                  labelColor="accent"
                />
              </div>
              <div class="col-6">
                <data-item
                  :text="DatosMediacion.FechaProximaAudiencia ? DatosMediacion.FechaProximaAudiencia.split(' ')[1] : 'Sin Fecha Proxima Audiencia'"
                  text-class="text-weight-light text-h6"
                  label="Hora"
                  labelColor="accent"
                />
              </div>
            </div>

            <!-- Editar Mediacion -->
            <div v-else>
              <div class="row" style="justify-content: space-between;">
                <div class="col-6 flex items-center">
                  <q-select
                    v-model="BonoSeleccionado"
                    style="width: 94%; margin-top: 5px"
                    label="Bonos"
                    :options="opcionesBonos"
                    @input="cambiarFechaBonos"
                  />
                </div>
                <div class="col-6 flex justify-end">
                  <q-select
                    v-model="BeneficioSeleccionado"
                    style="width:94%; margin-top: 5px"
                    label="Beneficios"
                    :options="opcionesBeneficios"
                    @input="cambiarFechaPresentado"
                  />
                  <q-select
                    v-model="EstadoBeneficioSeleccionado"
                    style="width:94%; margin-top: 5px"
                    label="Estados Beneficio"
                    :options="opcionesEstadosBeneficio"
                    @input="cambiarFechaPresentado"
                  />
                </div>
              </div>
              <div class="row" style="justify-content: space-between;">
                <q-input label="Fecha Bonos" v-model="FechaBonos" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:47%;">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                          <q-date
                            v-model="FechaBonos"
                            mask="DD-MM-YYYY"
                            label="Fecha Bonos"
                            @input="() => $refs.qDateProxy1.hide()"
                          />
                        </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
                <q-input label="Fecha Presentado" v-model="FechaPresentado" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:47%;">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy ref="qDateProxy2" transition-show="scale" transition-hide="scale">
                          <q-date
                            v-model="FechaPresentado"
                            mask="DD-MM-YYYY"
                            label="Fecha Presentado"
                            @input="() => $refs.qDateProxy2.hide()"
                          />
                        </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
              <div v-if="checkFechaProximaAudiencia" class="row" style="justify-content: space-between;">
                <q-input label="Fecha Proxima Audiencia" v-model="FechaProximaAudiencia" mask="##-##-####" style="width:47%;" @input="fechaProximaAudienciaConMensaje()">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy ref="qDateProxy3" transition-show="scale" transition-hide="scale">
                          <q-date
                            v-model="FechaProximaAudiencia"
                            mask="DD-MM-YYYY"
                            label="Fecha Proxima Audiencia"
                            @input="fechaProximaAudienciaConMensaje()"
                          />
                        </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
                <q-input label="Hora Proxima Audiencia" v-model="HoraProximaAudiencia" mask="##:##" style="width:47%;" @input="fechaProximaAudienciaConMensaje()">
                  <template v-slot:append>
                    <q-icon name="access_time" class="cursor-pointer">
                        <q-popup-proxy ref="qTimeProxy1" transition-show="scale" transition-hide="scale">
                          <q-time
                            v-model="HoraProximaAudiencia"
                            format24h
                            mask="HH:mm"
                            @input="fechaProximaAudienciaConMensaje()"
                          />
                        </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
                <q-checkbox
                  v-if="FechaProximaAudiencia && HoraProximaAudiencia"
                  v-model="ConMensajeFechaAudiencia"
                  style="width: 47%"
                  label="Notificar nueva fecha al cliente."
                  @input="verMensajeFechaAudiencia()"
                />
                <q-checkbox
                  v-if="IdCalendario && FechaProximaAudiencia && HoraProximaAudiencia"
                  v-model="agendar"
                  style="width: 47%"
                  label="Agendar en calendario."
                />
              </div>
              <div class="row" style="justify-content: space-between;">
                <q-input
                  v-model="DatosEditar.Legajo"
                  label="Legajo"
                  style="width: 47%"
                  @input="editarMensajeCliente(DatosEditar.Legajo)"
                />
                <q-checkbox
                  v-model="checkFechaProximaAudiencia"
                  style="width: 47%"
                  label="Con Fecha de Audiencia"
                  @input="resetFecha()"
                />
              </div>
            </div>
          </q-tab-panel>

          <!-- Tab Mediador -->
          <q-tab-panel style="padding-top: 0px" name="mediador">
            <div class="row" style="justify-content: space-between">
              <div class="col-12">
                <data-item
                  :text="DatosMediador.Nombre"
                  text-class="text-weight-light text-h6"
                  label="Nombre"
                  labelColor="accent"
                />
              </div>
              <div class="col-6">
                <data-item
                  :text="DatosMediador.Registro ? DatosMediador.Registro.toString() : 'Sin Datos'"
                  text-class="text-weight-light text-h6"
                  label="Registro"
                  labelColor="accent"
                />
              </div>
              <div class="col-6">
                <data-item
                  :text="DatosMediador.MP ? DatosMediador.MP : 'Sin Datos'"
                  text-class="text-weight-light text-h6"
                  label="MP"
                  labelColor="accent"
                />
              </div>
              <div class="col-12">
                <data-item
                  :text="DatosMediador.Domicilio ? DatosMediador.Domicilio : 'Sin Domicilio Cargado'"
                  text-class="text-weight-light text-h6"
                  label="Domicilio"
                  labelColor="accent"
                />
              </div>
              <div class="col-12">
                <data-item
                  :text="DatosMediador.Telefono ? DatosMediador.Telefono : 'Sin Telefono Cargado'"
                  text-class="text-weight-light text-h6"
                  label="Telefono"
                  labelColor="accent"
                />
              </div>
              <div class="col-12">
                <data-item
                  :text="DatosMediador.Email ? DatosMediador.Email : 'Sin Email Cargado'"
                  text-class="text-weight-light text-h6"
                  label="Email"
                  labelColor="accent"
                />
              </div>
            </div>
          </q-tab-panel>
        </q-tab-panels>
        <q-btn
          v-if="!EditarMediacion && !VistaTab"
          @click="$emit('cerrar')"
          color="red"
          class="text-capitalize"
          style="padding-top:0px; margin-bottom:10px; margin-top: 10px"
        >Cerrar</q-btn>
        <div v-else-if="EditarMediacion">
          <q-btn
            @click="guardarEdicion()"
            color="primary"
            class="text-capitalize"
            style="padding-top:0px; margin-bottom:10px; margin-top: 10px; margin-right: 20px"
          >Guardar</q-btn>
          <q-btn
            @click="EditarMediacion = false"
            color="red"
            class="text-capitalize"
            style="padding-top:0px; margin-bottom:10px; margin-top: 10px"
          >Cancelar</q-btn>
        </div>
      </div>

      <!-- Cargar Mediacion -->
      <div
        v-if="CargarMediacion"
        :style="VistaTab ? 'width: 100%; justify-content: space-between' : 'width: 500px; justify-content: space-between'"
        class="column">
        <q-checkbox
          v-model="ConMediador"
          style="width: 47%"
          label="Con Mediador Sorteado"
          @input="resetMediador()"
        />
        <div v-if="ConMediador">
          <Select
            :multiple="false"
            :label="'Buscar Mediador'"
            :hint="'Seleccione un Mediador para el Caso'"
            :tooltip="false"
            :opciones="opcionesMediadores"
            @seleccion="mediadorSeleccionado"
          />
        </div>
        <div class="row" style="justify-content: space-between;">
          <div class="col-6 flex items-center">
            <q-select
              v-model="BonoSeleccionado"
              style="width: 94%; margin-top: 5px"
              label="Bonos"
              :options="opcionesBonos"
              @input="cambiarFechaBonos"
            />
          </div>
          <div class="col-6 flex justify-end">
            <q-select
              v-model="BeneficioSeleccionado"
              style="width:94%; margin-top: 5px"
              label="Beneficios"
              :options="opcionesBeneficios"
              @input="cambiarFechaPresentado"
            />
            <q-select
              v-model="EstadoBeneficioSeleccionado"
              style="width:94%; margin-top: 5px"
              label="Estados Beneficio"
              :options="opcionesEstadosBeneficio"
              @input="cambiarFechaPresentado"
            />
          </div>
        </div>
        <div class="row" style="justify-content: space-between;">
          <q-input label="Fecha Bonos" v-model="FechaBonos" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:47%;">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                    <q-date
                      v-model="FechaBonos"
                      mask="DD-MM-YYYY"
                      label="Fecha Bonos"
                      @input="() => $refs.qDateProxy1.hide()"
                    />
                  </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <q-input label="Fecha Presentado" v-model="FechaPresentado" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:47%;">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy ref="qDateProxy2" transition-show="scale" transition-hide="scale">
                    <q-date
                      v-model="FechaPresentado"
                      mask="DD-MM-YYYY"
                      label="Fecha Presentado"
                      @input="() => $refs.qDateProxy2.hide()"
                    />
                  </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
        </div>
        <div v-if="checkFechaProximaAudiencia" class="row" style="justify-content: space-between;">
          <q-input label="Fecha Proxima Audiencia" v-model="FechaProximaAudiencia" mask="##-##-####" style="width:47%;" @input="fechaProximaAudienciaConMensaje()">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy ref="qDateProxy3" transition-show="scale" transition-hide="scale">
                    <q-date
                      v-model="FechaProximaAudiencia"
                      mask="DD-MM-YYYY"
                      label="Fecha Proxima Audiencia"
                      @input="fechaProximaAudienciaConMensaje()"
                    />
                  </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <q-input label="Hora Proxima Audiencia" v-model="HoraProximaAudiencia" mask="##:##" style="width:47%;" @input="fechaProximaAudienciaConMensaje()">
            <template v-slot:append>
              <q-icon name="access_time" class="cursor-pointer">
                  <q-popup-proxy ref="qTimeProxy1" transition-show="scale" transition-hide="scale">
                    <q-time
                      v-model="HoraProximaAudiencia"
                      format24h
                      mask="HH:mm"
                      @input="fechaProximaAudienciaConMensaje()"
                    />
                  </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <q-checkbox
            v-if="FechaProximaAudiencia && HoraProximaAudiencia"
            v-model="ConMensajeFechaAudiencia"
            style="width: 47%"
            label="Notificar al cliente."
            @input="verMensajeFechaAudiencia()"
          />
          <q-checkbox
            v-if="IdCalendario && FechaProximaAudiencia && HoraProximaAudiencia"
            v-model="agendar"
            style="width: 47%"
            label="Agendar en calendario."
          />
        </div>
        <div class="row" style="justify-content: space-between; margin-bottom: 10px">
          <q-input v-model="DatosMediacion.Legajo" label="Legajo" style="width: 47%" @input="editarMensajeCliente(DatosMediacion.Legajo)" />
          <q-checkbox
            v-model="checkFechaProximaAudiencia"
            style="width: 47%"
            label="Con Fecha de Audiencia"
            @input="resetFecha()"
          />
        </div>
        <q-checkbox
          v-if="DatosMediador.IdMediador"
          v-model="ConMensaje"
          style="width: 100%"
          label="Enviar mensaje al cliente y mediador"
          @input="verMensaje()"
        />
        <div v-if="ConMensaje" style="position: relative; margin-top: 15px">
          <q-icon
            right
            color="primary"
            style="position: absolute; top: 5px; right: 5px; cursor: pointer"
            name="fas fa-edit"
            size="20px"
            @click="ModalDatosContacto = true"
          >
            <q-tooltip>Editar Datos de Contacto</q-tooltip>
          </q-icon>
          <span style="font-weight:bold">
            Datos de Contacto del Mediador
          </span>
          <ul>
            <li>
              Telefono: {{ DatosMediador.Telefono }}
            </li>
            <li>
              Email: {{ DatosMediador.Email ? DatosMediador.Email : 'Sin Email' }}
            </li>
          </ul>
          <span v-if="!validarTelefono(DatosMediador.Telefono)" style="color: red">
            Telefono Invalido.
            Por favor edite el telefono o el mensaje no podra ser enviado
          </span>
        </div>
        <div class="row" style="justify-content: center; margin-top: 15px">
          <q-btn
            @click="altaMediacion()"
            v-if="!alta"
            color="primary"
            class="text-capitalize"
            style="padding-top:0px; margin-bottom:10px; margin-right: 20px; margin-top: 10px"
          >Cargar Datos</q-btn>
          <q-spinner
            v-else
            color="primary"
            class="self-center"
            style="margin-top: 10px"
            size="3em"
          />
          <q-btn
            v-if="!VistaTab"
            @click="$emit('cerrar')"
            color="red"
            class="text-capitalize"
            style="padding-top:0px; margin-bottom:10px; margin-right: 20px; margin-top: 10px"
          >Cerrar</q-btn>
        </div>
      </div>
    </div>

    <!-- Modal Cargar / Cambiar Mediador -->
    <q-dialog v-model="CargarMediador" style="background-color: white">
      <q-card style="background-color: white; min-width:400px;">
        <q-item style="background-color: black;">
          <span class="q-subheading" style="color:white;">Seleccionar Mediador</span>
        </q-item>
        <div class="row" style="justify-content: center; margin-right: 15px; margin-left: 15px">
          <Select
            :multiple="false"
            :label="'Buscar Mediador'"
            :hint="'Seleccione un Mediador para el Caso'"
            :tooltip="false"
            :opciones="opcionesMediadores"
            @seleccion="mediadorSeleccionado"
          />
          <q-checkbox
            v-if="DatosMediador.IdMediador"
            v-model="ConMensaje"
            style="width: 100%"
            label="Enviar mensaje al cliente y mediador"
            @input="verMensaje()"
          />
          <div class="col-12" v-if="ConMensaje" style="position: relative; margin-top: 15px">
            <q-icon
              right
              color="primary"
              style="position: absolute; top: 5px; right: 5px; cursor: pointer"
              name="fas fa-edit"
              size="20px"
              @click="ModalDatosContacto = true"
            >
              <q-tooltip>Editar Datos de Contacto</q-tooltip>
            </q-icon>
            <span style="font-weight:bold">
              Datos de Contacto del Mediador
            </span>
            <ul>
              <li>
                Telefono: {{ DatosMediador.Telefono }}
              </li>
              <li>
                Email: {{ DatosMediador.Email ? DatosMediador.Email : 'Sin Email' }}
              </li>
            </ul>
            <span v-if="!validarTelefono(DatosMediador.Telefono)" style="color: red">
              Telefono Invalido.
              Por favor edite el telefono o el mensaje no podra ser enviado
            </span>
          </div>
          <q-btn
            color="primary"
            class="text-capitalize col-12"
            style="padding-top:0px; margin-bottom: 15px; margin-top: 15px; width: 100px"
            @click="guardarMediador()"
          >Guardar</q-btn>
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal Editar Datos de Contacto -->
    <q-dialog v-model="ModalDatosContacto" style="background-color: white" no-backdrop-dismiss no-esc-dismiss>
      <q-card style="background-color: white;">
        <q-item style="background-color: black;">
          <span class="q-subheading" style="color:white;">Editar Datos de Contacto</span>
        </q-item>
        <div class="row" style="justify-content: center;">
          <q-input v-model="DatosMediador.Telefono" label="Telefono" style="width: 95%" />
          <q-input v-model="DatosMediador.Email" label="Email" style="width: 95%" />
          <q-btn
            color="primary"
            class="text-capitalize col-12"
            style="padding-top:0px; margin-bottom:10px; margin-top: 10px; max-width: 100px"
            @click="editarDatosMediador()"
          >Guardar</q-btn>
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal Mensaje -->
    <q-dialog v-model="ModalMensaje" style="background-color: white">
      <q-card style="background-color: white; min-width:400px">
        <q-item style="background-color: black;">
          <span class="q-subheading" style="color:white;">Mensaje: Notificar Mediador Sorteado</span>
        </q-item>
        <div class="row" style="justify-content: center;">
          <q-input
            class="col-12"
            v-model="mensajeCliente"
            style="width:95%; margin-top: 5px"
            label="Mensaje Cliente"
            type="textarea"
            :rows="4"
          />
          <div class="row" style="justify-content: center; width: 100%">
            <q-select
              class="col-6"
              v-model="ActorSeleccionado"
              style="width:45%; margin-top: 5px; margin-right: 5px"
              label="Actor"
              :options="Actores"
              @input="verMensaje()"
            />
            <q-select
              class="col-6"
              v-model="DemandadoSeleccionado"
              style="width:45%; margin-top: 5px; margin-left: 5px"
              label="Demandado"
              :options="Demandados"
              @input="verMensaje()"
            />
          </div>
          <q-input
            class="col-12"
            v-model="mensajeMediador"
            style="width:95%; margin-top: 5px"
            label="Mensaje Mediador"
            type="textarea"
            :rows="6"
          />
          <q-btn
            @click="ModalMensaje = false"
            color="primary"
            class="text-capitalize col-12"
            style="padding-top:0px; margin-bottom:10px; margin-top: 10px; max-width: 100px"
          >Aceptar</q-btn>
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal Notificar Fecha Audiencia -->
    <q-dialog v-model="ModalFechaAudiencia" style="background-color: white">
      <q-card style="background-color: white; min-width:400px">
        <q-item style="background-color: black;">
          <span class="q-subheading" style="color:white;">Mensaje: Notificar Fecha de Audiencia</span>
        </q-item>
        <div class="row" style="justify-content: center;">
          <q-select
            class="col-12"
            v-model="ActorSeleccionado"
            style="width:95%"
            label="Actor"
            :options="Actores"
            @input="verMensajeFechaAudiencia()"
          />
          <q-input
            class="col-12"
            v-model="mensajeFechaAudiencia"
            style="width:95%; margin-top: 5px"
            label="Mensaje"
            type="textarea"
            :rows="3"
          />
          <q-btn
            @click="ModalFechaAudiencia = false"
            color="primary"
            class="text-capitalize col-12"
            style="padding-top:0px; margin-bottom:10px; margin-top: 10px; max-width: 100px"
          >Aceptar</q-btn>
        </div>
      </q-card>
    </q-dialog>
  </q-card>
</template>

<script>
import { QTabPanels, QTabPanel, QTab, QTabs, QSpinner, Notify } from 'quasar'
import request from '../../request'
import auth from '../../auth'
import Loading from '../Loading'
import DataItem from '../Compartidos/DataItem'
import Select from '../../components/Compartidos/Select'
import moment from 'moment'
export default {
  name: 'Mediacion',
  components: {
    Loading,
    Select,
    QTabPanels,
    QTabPanel,
    QTabs,
    QTab,
    QSpinner,
    DataItem
  },
  props: [ 'IdMediacion', 'IdCaso', 'IdChat', 'Personas', 'CaratulaCaso', 'VistaTab' ],
  data () {
    return {
      tab: 'mediacion',
      loading: true,
      crearChat: false,
      alta: false,
      agendar: false,
      CargarMediacion: false,
      CargarMediador: false,
      EditarMediacion: false,
      ConMediador: false,
      ConMensaje: false,
      ConMensajeFechaAudiencia: false,
      DatosMediacion: {},
      DatosMediador: {},
      DatosEditar: {},
      opcionesBonos: [],
      opcionesBeneficios: [],
      opcionesEstadosBeneficio: [],
      opcionesMediadores: [],
      BonoSeleccionado: {},
      BeneficioSeleccionado: {},
      EstadoBeneficioSeleccionado: {},
      FechaBonos: '',
      FechaPresentado: '',
      FechaProximaAudiencia: '',
      HoraProximaAudiencia: '',
      checkFechaProximaAudiencia: false,
      ModalFechaAudiencia: false,
      mensajeCliente: '',
      mensajeMediador: '',
      mensajeFechaAudiencia: '',
      ModalDatosContacto: false,
      ModalMensaje: false,
      Actores: [],
      Demandados: [],
      ActorSeleccionado: '',
      DemandadoSeleccionado: '',
      IdCalendario: 0,
      IdCalendarioAPI: ''
    }
  },
  created () {
    if (this.IdMediacion) {
      request.Get('/mediaciones', {id: this.IdMediacion}, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.DatosMediacion = r
          this.DatosMediador = r.Mediador
          this.loading = false
        }
      })
    }

    request.Get(`/estudios/${auth.UsuarioLogueado.IdEstudio}/calendario`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r) {
        this.IdCalendario = r.IdCalendario
        this.IdCalendarioAPI = r.IdCalendarioAPI
      }
    })

    request.Get('/mediaciones/datos', {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        const Bonos = JSON.parse(r.Bonos)
        const Beneficios = JSON.parse(r.Beneficios)
        const EstadosBeneficio = JSON.parse(r.EstadosBeneficio) || []

        this.opcionesBonos = Bonos.map(b => {
          return {
            label: b.Bono,
            value: b.IdBono
          }
        })
        this.opcionesBonos.unshift({
          label: 'Sin datos',
          value: null
        })

        this.BonoSeleccionado = this.opcionesBonos[0]

        this.opcionesBeneficios = Beneficios.map(b => {
          return {
            label: b.Beneficio,
            value: b.IdBeneficio
          }
        })
        this.opcionesBeneficios.unshift({
          label: 'Sin datos',
          value: null
        })

        this.BeneficioSeleccionado = this.opcionesBeneficios[0]

        this.opcionesEstadosBeneficio = EstadosBeneficio.map(b => {
          return {
            label: b.EstadoBeneficio,
            value: b.IdEstadoBeneficio
          }
        })
        this.opcionesEstadosBeneficio.unshift({
          label: 'Sin datos',
          value: null
        })

        this.EstadoBeneficioSeleccionado = this.opcionesEstadosBeneficio[0]
      }
    })

    request.Get('/mediaciones/mediadores', {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.Mediadores = r
        this.opcionesMediadores = r.map(m => {
          return {
            label: m.Nombre,
            value: m.IdMediador
          }
        })
      }
    })

    this.Personas.forEach(p => {
      if (p.Rol === 'Demandado') {
        this.Demandados.push(p.Apellido + ', ' + p.Nombre)
      } else {
        this.Actores.push(p.Apellido + ', ' + p.Nombre)
      }
    })

    this.ActorSeleccionado = this.Actores[0]
    this.DemandadoSeleccionado = this.Demandados.length === 0 ? '' : this.Demandados[0]
  },
  methods: {
    fechaProximaAudienciaConMensaje () {
      this.$refs.qTimeProxy1.hide()
      this.$refs.qDateProxy3.hide()

      if (this.FechaProximaAudiencia && this.HoraProximaAudiencia.length === 5) {
        this.agendar = true
        this.ConMensajeFechaAudiencia = true
        this.verMensajeFechaAudiencia()
      }
    },
    resetFecha  () {
      if (!this.checkFechaProximaAudiencia) {
        this.FechaProximaAudiencia = ''
        this.HoraProximaAudiencia = ''
      }
    },
    resetMediador () {
      if (!this.ConMediador) {
        this.DatosMediador = {}
        this.DatosMediacion.IdMediador = ''
        this.ConMensaje = false
      }
    },
    mediadorSeleccionado (mediador) {
      const i = this.Mediadores.findIndex(m => m.IdMediador === mediador.value)

      this.DatosMediador = this.Mediadores[i]
      this.DatosMediacion.IdMediador = this.Mediadores[i].IdMediador

      this.ConMensaje = true

      this.verMensaje()
    },
    altaMediacion () {
      this.alta = true
      if (!this.ConMediador) {
        this.DatosMediacion.IdMediador = null
      }
      this.DatosMediacion.IdCaso = this.IdCaso
      this.DatosMediacion.IdBono = this.BonoSeleccionado.value
      this.DatosMediacion.Bono = this.BonoSeleccionado.label
      this.DatosMediacion.IdBeneficio = this.BeneficioSeleccionado.value
      this.DatosMediacion.Beneficio = this.BeneficioSeleccionado.label
      this.DatosMediacion.IdEstadoBeneficio = this.EstadoBeneficioSeleccionado.value
      this.DatosMediacion.EstadoBeneficio = this.EstadoBeneficioSeleccionado.label
      this.DatosMediacion.FechaBonos = this.FechaBonos
      this.DatosMediacion.FechaPresentado = this.FechaPresentado

      if (this.checkFechaProximaAudiencia) {
        if (this.FechaProximaAudiencia && this.HoraProximaAudiencia) {
          const fecha = this.FechaProximaAudiencia
          const hora = this.HoraProximaAudiencia

          this.DatosMediacion.FechaProximaAudiencia = `${fecha} ${hora}`
        } else {
          this.$q.notify('Debe ingresar una fecha para proxima audiencia o destildar la opcion.')
          return
        }
      } else {
        this.DatosMediacion.FechaProximaAudiencia = null
      }

      if (this.ConMensaje && !this.validarTelefono(this.DatosMediador.Telefono)) {
        this.$q.notify('No es posible enviar el mensaje, por favor edite el numero o destilde la opcion.')
        return
      }

      request.Post('/mediaciones/alta', this.DatosMediacion, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          if (this.ConMensaje) {
            this.enviarMensaje(this.mensajeCliente)
            this.enviarMensajeMediador()
          }

          if (this.ConMensajeFechaAudiencia) {
            this.enviarMensaje(this.mensajeFechaAudiencia)
          }

          this.DatosMediacion.IdMediacion = r.IdMediacion
          this.IdMediacion = r.IdMediacion

          if (this.checkFechaProximaAudiencia) {
            this.altaMovimiento(this.agendar, this.ConMensajeFechaAudiencia)
          }
          this.CargarMediacion = false
          this.ConMensaje = false
          this.loading = false
          this.agendar = false
          this.checkFechaProximaAudiencia = false

          this.$emit('alta', this.DatosMediacion.IdMediacion)

          this.$q.notify('Los datos se dieron de alta con exito.')
        }
      })
    },
    changeTab (tab) {
      this.checkFechaProximaAudiencia = false
      this.EditarMediacion = false
    },
    habilitarEdicion () {
      this.FechaPresentado = this.DatosMediacion.FechaPresentado
      this.FechaBonos = this.DatosMediacion.FechaBonos
      this.BonoSeleccionado = {
        label: this.DatosMediacion.Bono,
        value: this.DatosMediacion.IdBono
      }
      this.BeneficioSeleccionado = {
        label: this.DatosMediacion.Beneficio,
        value: this.DatosMediacion.IdBeneficio
      }
      this.EstadoBeneficioSeleccionado = {
        label: this.DatosMediacion.EstadoBeneficio,
        value: this.DatosMediacion.IdEstadoBeneficio
      }
      if (this.DatosMediacion.FechaProximaAudiencia) {
        this.FechaProximaAudiencia = this.DatosMediacion.FechaProximaAudiencia.split(' ')[0]
        this.HoraProximaAudiencia = this.DatosMediacion.FechaProximaAudiencia.split(' ')[1]
        this.checkFechaProximaAudiencia = true
      }
      this.DatosEditar.Legajo = this.DatosMediacion.Legajo
      this.EditarMediacion = true
    },
    cambiarFechaBonos () {
      this.FechaBonos = moment().format('DD-MM-YYYY')
    },
    cambiarFechaPresentado () {
      this.FechaPresentado = moment().format('DD-MM-YYYY')
    },
    guardarEdicion () {
      this.DatosEditar.FechaBonos = this.FechaBonos
      this.DatosEditar.FechaPresentado = this.FechaPresentado
      this.DatosEditar.IdBono = this.BonoSeleccionado.value
      this.DatosEditar.Bono = this.BonoSeleccionado.label
      this.DatosEditar.IdBeneficio = this.BeneficioSeleccionado.value
      this.DatosEditar.Beneficio = this.BeneficioSeleccionado.label
      this.DatosEditar.IdEstadoBeneficio = this.EstadoBeneficioSeleccionado.value
      this.DatosEditar.EstadoBeneficio = this.EstadoBeneficioSeleccionado.label
      this.DatosEditar.IdMediacion = this.IdMediacion
      this.DatosEditar.IdMediador = this.DatosMediacion.IdMediador
      this.DatosEditar.IdCaso = this.DatosMediacion.IdCaso

      let movAudiencia = false

      if (this.checkFechaProximaAudiencia) {
        if (this.FechaProximaAudiencia && this.HoraProximaAudiencia) {
          const fecha = this.FechaProximaAudiencia
          const hora = this.HoraProximaAudiencia
          const fechaAntigua = this.DatosMediacion.FechaProximaAudiencia ? this.DatosMediacion.FechaProximaAudiencia.split(' ')[0].split(/[-/]/).join('-') : null
          if (this.FechaProximaAudiencia.split(/[-/]/).join('-') !== fechaAntigua) {
            movAudiencia = true
          }

          this.DatosEditar.FechaProximaAudiencia = `${fecha} ${hora}`
        } else {
          this.$q.notify('Debe ingresar una fecha para proxima audiencia o destildar la opcion.')
          return
        }
      } else {
        this.DatosEditar.FechaProximaAudiencia = null
      }

      request.Post('/mediaciones/editar', this.DatosEditar, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.EditarMediacion = false
          this.DatosMediacion = this.DatosEditar
          this.DatosEditar = {}

          if (this.ConMensajeFechaAudiencia) {
            this.enviarMensaje(this.mensajeFechaAudiencia)
          }

          if (movAudiencia) {
            this.altaMovimiento(this.agendar, this.ConMensajeFechaAudiencia)
          }

          this.ConMensajeFechaAudiencia = false
          this.checkFechaProximaAudiencia = false
          this.agendar = false

          this.$q.notify('Los datos se editaron con exito.')
        }
      })
    },
    verMensaje () {
      if (this.ConMensaje) {
        const mediador = this.DatosMediador.Nombre
        const legajo = this.DatosMediacion.Legajo

        this.ModalMensaje = true
        this.mensajeCliente = `Estimado ${this.ActorSeleccionado} te comentamos que el mediador que ha sido sorteado es ${mediador}, el proximo paso es que fije la fecha de la audiencia, lo que te vamos a estar informando en aproximadamente 7 dias. Saludos!`
        this.mensajeMediador = `Estimado mediador/a ${mediador} te informamos que has sido sorteado en el legajo Nro ${legajo}, requirente ${this.ActorSeleccionado} con ${this.DemandadoSeleccionado}. Te pedimos por favor fijar la fecha con 20 dias de margen para efectuar la notificacion.`
      }
    },
    verMensajeFechaAudiencia () {
      if (this.ConMensajeFechaAudiencia) {
        this.ModalFechaAudiencia = true
        this.mensajeFechaAudiencia = `Estimado ${this.ActorSeleccionado} te comentamos que la proxima fecha de audiencia de mediacion sera el dia ${this.FechaProximaAudiencia} a las ${this.HoraProximaAudiencia}. Se realizara en forma virtual, A este numero te llamaran desde el centro de mediación. yo estaré en la audiencia desde mi oficina. procura tener una buena conexión de wifi. Saludos!`
      }
    },
    validarTelefono (telefono) {
      if (!telefono) { return false }

      telefono = telefono.replace(/[- +]/g, '')

      switch (true) {
        case telefono.startsWith('54') && telefono.length === 12:
          return true

        case !telefono.startsWith('54') && telefono.length === 10:
          return true

        default:
          return false
      }
    },
    editarDatosMediador () {
      const datos = {
        Telefono: this.DatosMediador.Telefono,
        Email: this.DatosMediador.Email,
        id: this.DatosMediador.IdMediador
      }

      request.Post('/mediadores/editar-contacto', datos, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.ModalDatosContacto = false

          this.$q.notify('Los datos se editaron con exito.')
        }
      })
    },
    enviarMensaje (mensaje) {
      const Mensaje = {
        IdChat: this.IdChat ? this.IdChat : null,
        Contenido: mensaje
      }
      if (!Mensaje.IdChat) {
        const NuevoChat = {
          IdCaso: this.IdCaso,
          IdPersona: this.personaPrincipal().Id,
          Telefono: this.telefonoPrincipal(this.personaPrincipal().Telefonos)
        }
        if (NuevoChat.Telefono) {
          request.Post(`/chats/crear`, NuevoChat, r => {
            if (!r.Error) {
              Notify.create('Nuevo chat creado!')
              Mensaje.IdChat = r.IdChat
              request.Post(`/mensajes/enviar`, Mensaje, q => {
                if (!q.Error) {
                  Notify.create('Se ha notificado al cliente con exito.')
                  const UltMsjLeido = q.IdMensaje
                  request.Post(`/chats/${Mensaje.IdChat}/actualizar`, { IdUltimoLeido: UltMsjLeido }, p => {
                    if (p.Error) {
                      Notify.create('Falló al actualizar el ultimo mensaje leído. Razon:' + p.Error)
                    }
                  })
                } else {
                  Notify.create('Falló al notificar al cliente. Razon: ' + q.Error)
                }
              })
            } else {
              Notify.create('Falló al notificar al cliente. Razon: ' + r.Error)
            }
          })
        } else {
          Notify.create('Falló al notificar al cliente. Razon: no existe un telefono asociado')
        }
      } else {
        request.Post(`/mensajes/enviar`, Mensaje, r => {
          if (!r.Error) {
            Notify.create('Se ha notificado al cliente con exito.')
            const UltMsjLeido = r.IdMensaje
            request.Post(`/chats/${Mensaje.IdChat}/actualizar`, { IdUltimoLeido: UltMsjLeido }, p => {
              if (p.Error) {
                Notify.create('Falló al actualizar el ultimo mensaje leído. Razon:' + p.Error)
              }
            })
          } else {
            Notify.create('Falló al notificar al cliente. Razon: ' + r.Error)
          }
        })
      }
    },
    enviarMensajeMediador () {
      const Mensaje = {
        IdChat: this.DatosMediador.IdChatMediador ? this.DatosMediador.IdChatMediador : null,
        mediador: this.DatosMediador.IdChatMediador ? this.DatosMediador.IdChatMediador : null,
        Contenido: this.mensajeMediador
      }
      if (!Mensaje.IdChat) {
        const NuevoChat = {
          Telefono: this.DatosMediador.Telefono,
          IdMediador: this.DatosMediador.IdMediador
        }
        if (NuevoChat.Telefono) {
          request.Post(`/chats/crear`, NuevoChat, r => {
            if (!r.Error) {
              Notify.create('Nuevo chat con mediador creado!')
              Mensaje.IdChat = r.IdChat
              Mensaje.mediador = r.IdChat
              request.Post(`/mensajes/enviar`, Mensaje, q => {
                if (!q.Error) {
                  Notify.create('Se ha notificado al mediador con exito.')
                  const UltMsjLeido = q.IdMensaje
                  request.Post(`/chats/${Mensaje.IdChat}/actualizar`, { IdUltimoLeido: UltMsjLeido, mediador: Mensaje.mediador }, p => {
                    if (p.Error) {
                      Notify.create('Falló al actualizar el ultimo mensaje leído. Razon:' + p.Error)
                    }
                  })
                } else {
                  Notify.create('Falló al notificar al mediador. Razon: ' + q.Error)
                }
              })
            } else {
              Notify.create('Falló al notificar al mediador. Razon: ' + r.Error)
            }
          })
        } else {
          Notify.create('Falló al notificar al mediador. Razon: no existe un telefono asociado')
        }
      } else {
        request.Post(`/mensajes/enviar`, Mensaje, r => {
          if (!r.Error) {
            Notify.create('Se ha notificado al mediador con exito.')
            const UltMsjLeido = r.IdMensaje
            request.Post(`/chats/${Mensaje.IdChat}/actualizar`, { IdUltimoLeido: UltMsjLeido, mediador: Mensaje.mediador }, p => {
              if (p.Error) {
                Notify.create('Falló al actualizar el ultimo mensaje leído. Razon:' + p.Error)
              }
            })
          } else {
            Notify.create('Falló al notificar al mediador. Razon: ' + r.Error)
          }
        })
      }
    },
    personaPrincipal () {
      if (this.Personas.length) {
        return this.Personas.filter(persona => persona.Principal)[0]
      }
    },
    telefonoPrincipal (telefonos) {
      if (telefonos) {
        const TelefonosPrincipales = (telefonos || []).filter(
          (telefono) => telefono.EsPrincipal === 'S'
        )
        if (!TelefonosPrincipales.length) {
          return telefonos.length ? telefonos[0].Telefono : 'Sin telefono'
        } else {
          return TelefonosPrincipales[0].Telefono
        }
      } else {
        return ''
      }
    },
    guardarMediador () {
      if (this.ConMensaje && !this.validarTelefono(this.DatosMediador.Telefono)) {
        this.$q.notify('No es posible enviar el mensaje, por favor edite el numero o destilde la opcion.')
        return
      }

      request.Post('/mediaciones/editar', this.DatosMediacion, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.CargarMediador = false

          if (this.ConMensaje) {
            this.enviarMensaje(this.mensajeCliente)
            this.enviarMensajeMediador()
          }

          this.$q.notify('Se cargo el mediador con exito.')
        }
      })
    },
    agendarEvento (IdMovimientoCaso) {
      const fecha = this.FechaProximaAudiencia.split(/[-/]/).reverse().join('-')
      const hora = this.HoraProximaAudiencia

      let fechadb = `${fecha}T${hora}`

      const datetimeCalendar = `${fecha}T${hora}:00-03:00`

      const datosEvento = {
        IdCalendario: this.IdCalendario,
        IdCalendarioAPI: this.IdCalendarioAPI,
        Titulo: `${this.CaratulaCaso} - DocDoc! - Audiencia Mediacion`,
        Descripcion: 'Proxima audiencia',
        Comienzo: datetimeCalendar,
        Fin: datetimeCalendar,
        IdColor: 10,
        ComienzoDb: fechadb,
        FinDb: fechadb,
        IdMediacion: this.IdMediacion,
        IdMovimientoCaso: IdMovimientoCaso || ''
      }

      request.Post('/mediaciones/evento', datosEvento, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.$q.notify({
            color: 'green',
            message: `Se añadio un evento al calendario del estudio.`
          })
        }
      })
    },
    altaMovimiento (agendar = false, cliente = false) {
      request.Get(`/estudios/${auth.UsuarioLogueado.IdEstudio}/tipos-movimiento`, {}, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          const IdTipoMov = r.filter(t => t.TipoMovimiento === 'Gestión oficina')[0].IdTipoMov
          const movimiento = {
            IdResponsable: auth.UsuarioLogueado.IdUsuario,
            Detalle: 'Audiencia - Mediacion',
            IdCaso: this.IdCaso,
            FechaEsperada: this.FechaProximaAudiencia,
            FechaAlta: new Date(),
            FechaRealizado: null,
            IdTipoMov: IdTipoMov,
            Cuaderno: '',
            Color: 'negative',
            Multimedia: null,
            Cliente: cliente ? 'S' : ''
          }
          request.Post('/movimientos', movimiento, r => {
            if (r.Error) {
              this.$q.notify(r.Error)
            } else {
              this.$q.notify('Se dio de alta un nuevo movimiento.')
            }
            if (agendar) {
              this.agendarEvento(r.IdMovimientoCaso)
            }
          })
        }
      })
    },
    editarMensajeCliente (legajo) {
      const mediador = this.DatosMediador.Nombre
      this.mensajeMediador = `Estimado mediador/a ${mediador} te informamos que has sido sorteado en el legajo Nro ${legajo}, requirente ${this.ActorSeleccionado} con ${this.DemandadoSeleccionado}. Te pedimos por favor fijar la fecha con 20 dias de margen para efectuar la notificacion.`
    },
    abrirChat () {
      if (this.DatosMediacion.IdChatMediador) {
        const routeData = this.$router.resolve({
          name: 'Chat',
          query: {
            id: this.DatosMediacion.IdChatMediador,
            caratula: this.DatosMediador.Nombre,
            telefono: this.DatosMediador.Telefono,
            idMediacion: this.DatosMediacion.IdMediacion
          }
        })
        window.open(routeData.href, '_blank')
      } else {
        const nuevoChat = {
          Telefono: this.DatosMediador.Telefono,
          IdMediador: this.DatosMediador.IdMediador
        }
        this.crearChat = true
        request.Post(`/chats/crear`, nuevoChat, r => {
          if (!r.Error) {
            Notify.create('Nuevo chat con mediador creado!')
            this.DatosMediacion.IdChatMediador = r.IdChat
            const routeData = this.$router.resolve({
              name: 'Chat',
              query: {
                id: r.IdChat,
                caratula: this.DatosMediador.Nombre,
                telefono: this.DatosMediador.Telefono,
                idMediacion: this.DatosMediacion.IdMediacion
              }
            })
            window.open(routeData.href, '_blank')
            this.crearChat = false
          } else {
            Notify.create('Falló al iniciar un nuevo chat. Razon: ' + r.Error)
          }
        })
      }
    }
  }
}
</script>
