import { Validate } from './validate.js'

function BaseModel (obj) {
  this.validate = {}
  for (let prop in obj.Props) {
    this[prop] = undefined
    this.validate[prop] = () => {
      let error = ''
      for (let i = 0; i < obj.Props[prop].length; i++) {
        error = Validate(this, prop, obj.Props[prop][i])
        if (error) {
          break
        }
      }
      return error
    }
  }
  this.validate.scenario = {}
  for (let scenario in obj.Scenarios) {
    this.validate.scenario[scenario] = () => {
      let errors = null
      for (let i = 0; i < obj.Scenarios[scenario].length; i++) {
        let prop2 = obj.Scenarios[scenario][i]
        let error = this.validate[prop2]()
        if (error) {
          if (!errors) {
            errors = {}
          }
          errors[prop2] = error
        }
      }
      return errors
    }
  }
  this.validate.all = () => {
    let errors = {}
    for (let prop in obj.Props) {
      let error = this.validate[prop]()
      if (this.validate[prop]()) {
        if (!errors) {
          errors = {}
        }
        errors[prop] = error
      }
    }
    return errors
  }
  for (let attr in obj) {
    var _this = this
    if (!['Scenarios', 'Props'].includes(attr)) {
      Object.defineProperty(this, attr, {
        get: function () {
          if (typeof obj[attr] === 'function' && obj.Scenarios[attr]) {
            let fn = obj[attr]
            obj[attr] = function () {
              if (!_this.validate.scenario[attr]()) {
                fn.apply(this, arguments)
              }
            }
          }
          return obj[attr]
        }
      })
    }
  }
  this.copyProps = (otherObj) => {
    let objKeys = Object.keys(otherObj)
    objKeys.forEach(k => {
      this[k] = otherObj[k]
    })
  }
}

export function Model (model) {
  return () => new BaseModel(model)
}
