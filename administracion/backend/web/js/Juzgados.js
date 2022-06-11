"use strict";
var Juzgados = {};
Juzgados.Estados = {
  init: function (IdJuzgado, estados, listadoEstados) {
    let arrayEstados = []
    const keys = estados ? Object.keys(estados) : []
    keys.forEach(i => {
      arrayEstados.push(estados[i])
    })
    arrayEstados.sort(((a, b) => a.Orden - b.Orden));
    var vm = new Vue({
      el: "#Juzgados",
      data: function () {
        return {
          IdJuzgado: IdJuzgado,
          estados: estados ? Object.assign({}, estados) : {},
          listadoEstados: listadoEstados,
          selectEstado: {},
          selectOrden: "",
          arrayEstados: arrayEstados,
          cambiarOrden: {}
        };
      },
      computed: {
        mostrarHeader: function () {
          return Object.keys(this.estados).length > 0;
        },
        estadosOpciones: function () {
          var vm = this;
          return this.listadoEstados.filter(function (e) {
            return !vm.estados[e.IdEstadoAmbitoGestion];
          });
        },
        ordenOpciones: function () {
          var Orden = [];
          $.each(this.estados, (id, e) => {
            Orden.push(e.Orden);
          });
          Orden.push(Orden.length + 1);
          return Orden.sort(((a, b) => a - b));
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
        quitarEstado: function (id) {
          $.post(
            "/juzgados/quitar-estado/" +
              this.IdJuzgado +
              "?idEstadoAmbitoGestion=" +
              id,
            {}
          )
            .done(function (r) {
              if (r.error) {
                vm.showError(r.error, "danger");
              } else {
                const keys = Object.keys(vm.estados)
                keys.forEach(i => {
                  if (vm.estados[i].Orden > vm.estados[id].Orden) {
                    const estado = {
                      EstadoAmbitoGestion: vm.estados[i].EstadoAmbitoGestion,
                      IdEstadoAmbitoGestion: vm.estados[i].IdEstadoAmbitoGestion,
                      Orden: parseInt(vm.estados[i].Orden) - 1
                    }
                    Vue.set(
                      vm.estados,
                      i,
                      estado
                    )
                  }
                })
                Vue.delete(vm.estados, id);
                let arrayEstados = vm.arrayEstados
                const i = arrayEstados.findIndex(e => e.IdEstadoAmbitoGestion === id)
                arrayEstados.forEach(e => {
                  if (e.Orden > arrayEstados[i].Orden) {
                    e.Orden = parseInt(e.Orden) - 1
                  }
                })
                arrayEstados.splice(i, 1)
                arrayEstados.sort(((a, b) => a.Orden - b.Orden));
                Vue.set(
                  vm,
                  'arrayEstados',
                  arrayEstados
                )
              }
            })
            .fail(function () {
              vm.showError(
                "Ocurrió un error en la comunicación con el servidor. Intente nuevamente más tarde.",
                "danger"
              );
            });
        },
        agregarEstado: function () {
          var selectEstado = Object.assign({}, vm.selectEstado);
          var selectOrden = vm.selectOrden;
          $.post(
            "/juzgados/agregar-estado/" +
              "?id=" +
              this.IdJuzgado +
              "&idEstadoAmbitoGestion=" +
              selectEstado.IdEstadoAmbitoGestion +
              "&Orden=" +
              selectOrden,
            {}
          )
            .done(function (r) {
              if (r.error) {
                vm.showError(r.error, "danger");
              } else {
                const keys = Object.keys(vm.estados)
                keys.forEach(i => {
                  if (vm.estados[i].Orden >= selectOrden) {
                    const estado = {
                      EstadoAmbitoGestion: vm.estados[i].EstadoAmbitoGestion,
                      IdEstadoAmbitoGestion: vm.estados[i].IdEstadoAmbitoGestion,
                      Orden: parseInt(vm.estados[i].Orden) + 1
                    }
                    Vue.set(
                      vm.estados,
                      i,
                      estado
                    )
                  }
                })
                Vue.set(
                  vm.estados,
                  selectEstado.IdEstadoAmbitoGestion,
                  {
                    EstadoAmbitoGestion: selectEstado.EstadoAmbitoGestion,
                    IdEstadoAmbitoGestion: selectEstado.IdEstadoAmbitoGestion,
                    Orden: selectOrden
                  }
                );
                let arrayEstados = vm.arrayEstados
                arrayEstados.forEach(e => {
                  if (e.Orden >= selectOrden) {
                    e.Orden = parseInt(e.Orden) + 1
                  }
                })
                arrayEstados.push(vm.estados[selectEstado.IdEstadoAmbitoGestion])
                arrayEstados.sort(((a, b) => a.Orden - b.Orden));
                Vue.set(
                  vm,
                  'arrayEstados',
                  arrayEstados
                )
                Vue.set(vm, "selectEstado", {});
                Vue.set(vm, "selectOrden", '');
              }
            })
            .fail(function () {
              vm.showError(
                "Ocurrió un error en la comunicación con el servidor. Intente nuevamente más tarde.",
                "danger"
              );
            });
        },
        ordenOpcionesEditar: function (o) {
          let opciones = this.ordenOpciones.filter(f => f !== o).sort((a, b) => a - b)
          opciones.pop()
          return opciones;
        },
        editarOrden: function (event) {
          const orden = event.target.value
          const id = Object.keys(vm.cambiarOrden)[0]
          Vue.set(vm, "cambiarOrden", {})
          $.post(
            "/juzgados/editar-estado/" +
              "?id=" +
              this.IdJuzgado +
              "&idEstadoAmbitoGestion=" +
              id +
              "&Orden=" +
              orden,
            {}
          )
            .done(function (r) {
              if (r.error) {
                vm.showError(r.error, "danger");
              } else {
                const i = vm.arrayEstados.findIndex(e => e.IdEstadoAmbitoGestion === parseInt(id))
                const ordenOld = vm.arrayEstados[i].Orden
                let array = vm.arrayEstados
                if (ordenOld > orden) {
                  array.forEach(e => {
                    if (e.Orden >= orden && e.Orden < ordenOld) {
                      e.Orden = parseInt(e.Orden) + 1
                    }
                  })
                } else {
                  array.forEach(e => {
                    if (e.Orden <= orden && e.Orden > ordenOld) {
                      e.Orden = parseInt(e.Orden) - 1
                    }
                  })
                }
                array[i].Orden = orden
                array.sort(((a, b) => a.Orden - b.Orden))
                Vue.set(
                  vm,
                  'arrayEstados',
                  array
                )
              }
            })
            .fail(function () {
              vm.showError(
                "Ocurrió un error en la comunicación con el servidor. Intente nuevamente más tarde.",
                "danger"
              );
            });
        }
      },
    });
  },
};
