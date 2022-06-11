"use strict";
var Competencias = {};
Competencias.TiposCaso = {
  init: function (IdCompetencia, tiposCaso, listadoTipos) {
    var vm = new Vue({
      el: "#competencias",
      data: function () {
        return {
          IdCompetencia: IdCompetencia,
          tiposCaso: Object.assign({}, tiposCaso),
          listadoTipos: listadoTipos,
          selectTipoCaso: {},
        };
      },
      computed: {
        mostrarHeader: function () {
          return Object.keys(this.tiposCaso).length > 0;
        },
        tiposCasoOpciones: function () {
          var vm = this;
          return this.listadoTipos.filter(function (t) {
            return !vm.tiposCaso[t.IdTipoCaso];
          });
        },
      },
      methods: {
        showError: function (mensaje, tipo) {
          //Agregando mensaje de error al diálogo modal
          var html =
            '<div id="mensaje-modal" class="alert alert-' +
            tipo +
            ' alert-dismissable">' +
            '<i class="fa fa-ban"></i> ' +
            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
            '<b class="texto" >' +
            mensaje +
            "</b>" +
            "</div>";
          $("#errores-modal").html(html);
        },
        quitarTipoCaso: function (id) {
          $.post(
            "/competencias/quitar-tipo-caso/" +
              this.IdCompetencia +
              "?idTipoCaso=" +
              id,
            {}
          )
            .done(function (r) {
              if (r.error) {
                vm.showError(r.error, "danger");
              } else {
                Vue.delete(vm.tiposCaso, id);
              }
            })
            .fail(function () {
              vm.showError(
                "Ocurrió un error en la comunicación con el servidor. Intente nuevamente más tarde.",
                "danger"
              );
            });
        },
        agregarTipoCaso: function () {
          var selectTipoCaso = Object.assign({}, vm.selectTipoCaso);
          $.post(
            "/competencias/agregar-tipo-caso/" +
              this.IdCompetencia +
              "?idTipoCaso=" +
              selectTipoCaso.IdTipoCaso,
            {}
          )
            .done(function (r) {
              if (r.error) {
                vm.showError(r.error, "danger");
              } else {
                Vue.set(
                  vm.tiposCaso,
                  selectTipoCaso.IdTipoCaso,
                  selectTipoCaso
                );
                Vue.set(vm, "selectTipoCaso", {});
              }
            })
            .fail(function () {
              vm.showError(
                "Ocurrió un error en la comunicación con el servidor. Intente nuevamente más tarde.",
                "danger"
              );
            });
        },
      },
    });
  },
};
