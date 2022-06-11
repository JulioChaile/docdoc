<?php
namespace frontend\modules\api\controllers;

use backend\models\GestorCiudades;
use Yii;

class CiudadesController extends BaseController
{
    /**
     * @api {get} /ciudades Listar Ciudades
     * @apiName ListarCiudades
     * @apiGroup Ciudades
     * 
     * @apiParam {String} Cadena
     * @apiParam {Number} IdProvincia
     *
     * @apiSuccess {[]Object} - Ciudades
     * @apiSuccess {Number} -.IdCiudad
     * @apiSuccess {Number} -.IdProvincia
     * @apiSuccess {String} -.Ciudad
     * @apiSuccess {String} -.CodPostal
     */
    public function actionIndex()
    {
        $gestor = new GestorCiudades;
        return $gestor->BuscarAvanzado(Yii::$app->request->get('Cadena'), Yii::$app->request->get('IdProvincia'));
    }
}
