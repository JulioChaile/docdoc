import axios from 'axios'
import { postAlike, getAlike, uniqGet } from './base'

export default {
  Get (url, params, callback) {
    getAlike(axios.get, url, params, callback, true)
  },
  Post (url, params, callback) {
    postAlike(axios.post, url, params, callback)
  },
  Put (url, params, callback) {
    postAlike(axios.put, url, params, callback)
  },
  Patch (url, params, callback) {
    postAlike(axios.patch, url, params, callback)
  },
  Delete (url, id, callback) {
    getAlike(axios.delete, url, {id}, callback)
  },
  UniqGet (url, params, callback) {
    uniqGet(url, params, callback)
  }
}
