var TiposCaso = {};

TiposCaso.AltaRol = {
  vue: null,
  init: function (params) {
    this.vue = new Vue({
      el: "#rolestipocaso-form",
      data: {
        Descripcion: "",
        Obligatorio: false,
        Parametro: "",
        TipoDato: "",
        ArrayParametros: params ? params : [],
      },
      methods: {
        agregarParametro: function () {
          var par = {
            Descripcion: this.Descripcion,
            Obligatorio: this.Obligatorio,
            Parametro: this.Parametro,
            TipoDato: this.TipoDato,
          };
          this.ArrayParametros.push(par);
        },
        quitarParametro: function (k) {
          this.ArrayParametros.splice(k, 1);
        },
      },
    });
  },
};

TiposCaso.Juzgados = {
  init: function (IdTipoCaso, juzgados, listadoJuzgados, listadoCompetencias) {
    console.log(IdTipoCaso, juzgados, listadoJuzgados, listadoCompetencias);
    var vm = new Vue({
      el: "#juzgados",
      data: function () {
        return {
          IdTipoCaso: IdTipoCaso,
          juzgados: juzgados,
          listadoJuzgados: listadoJuzgados,
          listadoCompetencias: listadoCompetencias,
          selectCompetencias: {},
          selectJuzgados: {},
        };
      },
      mounted: function () {
        if (this.competenciasOpciones.length === 1) {
          this.selectCompetencias = this.competenciasOpciones[0];
        }
      },
      computed: {
        mostrarHeader: function () {
          return this.listadoJuzgados.length > 0;
        },
        competenciasOpciones: function () {
          let Competencias = [];
          this.listadoCompetencias.forEach((c) => {
            if (c.TiposCaso[this.IdTipoCaso]) {
              Competencias.push(c);
            }
          });
          return Competencias;
        },
        juzgadosOpciones: function () {
          let juzgados = [];
          if (!this.selectCompetencias.IdCompetencia) {
            return juzgados;
          }
          this.juzgados.forEach((j) => {
            const ix = this.listadoJuzgados.find(
              (j2) =>
                j2.IdJuzgado == j.IdJuzgado &&
                j2.IdCompetencia == this.selectCompetencias.IdCompetencia
            );
            if (!ix && ix != 0) {
              juzgados.push(j);
            }
          });
          return juzgados;
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
        quitarJuzgado: function (ix, IdJuzgado, IdCompetencia) {
          $.post(
            "/tipos-caso/quitar-juzgado/" +
              this.IdTipoCaso +
              "?idJuzgado=" +
              IdJuzgado +
              "&idCompetencia=" +
              IdCompetencia
          )
            .done(function (r) {
              if (r.error) {
                vm.showError(r.error, "danger");
              } else {
                Vue.delete(vm.listadoJuzgados, ix);
              }
            })
            .fail(function () {
              vm.showError(
                "Ocurrió un error en la comunicación con el servidor. Intente nuevamente más tarde.",
                "danger"
              );
            });
        },
        agregarJuzgado: function () {
          var selectCompetencias = Object.assign({}, vm.selectCompetencias);
          var selectJuzgados = Object.assign({}, vm.selectJuzgados);
          $.post(
            "/tipos-caso/agregar-juzgado/" +
              this.IdTipoCaso +
              "?idJuzgado=" +
              selectJuzgados.IdJuzgado +
              "&idCompetencia=" +
              selectCompetencias.IdCompetencia
          )
            .done(function (r) {
              if (r.error) {
                vm.showError(r.error, "danger");
              } else {
                vm.listadoJuzgados.push({
                  IdJuzgado: selectJuzgados.IdJuzgado,
                  Juzgado: selectJuzgados.Juzgado,
                  ModoGestion: selectJuzgados.ModoGestion,
                  EstadoJuzgado: "A",
                  IdCompetencia: selectCompetencias.IdCompetencia,
                  Competencia: selectCompetencias.Competencia,
                  EstadoCompetencia: "A",
                });
                Vue.set(vm, "selectJuzgados", {});
                Vue.set(vm, "selectCompetencias", {});
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
