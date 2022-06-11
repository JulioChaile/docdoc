import { Model } from './base/model'
import Usuarios from './Usuarios'
import RolesTipoCaso from './RolesTipoCaso'
import Personas from './Personas'

export default {
  Usuarios: Model(Usuarios),
  RolesTipoCaso: Model(RolesTipoCaso),
  Personas: Model(Personas)
}
