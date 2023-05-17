import request from '../request'

const Personas = {
  Props: {
    Nombres: ['max 30'],
    Apellidos: ['max 30'],
    Domicilio: ['max 120'],
    Email: ['email', 'max 120'],
    EsJuridica: ['boolean'],
    EsPrincipal: ['max 1', 'min 1'],
    IdRTC: ['int'],
    Documento: ['int'],
    CUIT: ['max 11', 'min 11'],
    Telefonos: ['max 30'],
    RazonSocial: ['max 300'],
    FechaNacimiento: ['datetime'],
    Sexo: ['max 1', 'min 1'],
    Oficio: ['max 50'],
    EstadoCivil: ['max 30'],
    Parametros: ['object'],
    Categorias: ['object']
  },
  Scenarios: {
    Dame: ['Documento'],
    Alta: ['Nombres', 'Apellidos', 'Domicilio', 'Email', 'Documento', 'CUIT', 'Telefonos']
  },
  Dame (callback) {
    request.Get('/casos/Personas', {id: this.Documento}, r => {
      let result = { Error: null }
      if (r.Error) {
        result.Error = r.Error
      } else {
        this.copyProps(r)
      }
      callback(result)
    })
  }
}

export default Personas
