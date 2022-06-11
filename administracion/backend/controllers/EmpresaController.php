<?php

namespace backend\controllers;

use common\components\PermisosHelper;
use common\models\Empresa;
use common\models\forms\BusquedaForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class EmpresaController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }

    public function actionListar($Cadena = '')
    {
        PermisosHelper::verificarPermiso('BuscarParametro');

        $gestor = new Empresa();

        $busqueda = new BusquedaForm();

        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
        }

        $parametros = $gestor->BuscarParametros($Cadena);

        return $this->render('index', [
                    'models' => $parametros,
                    'busqueda' => $busqueda,
        ]);
    }

    public function actionModificar($id)
    {
        PermisosHelper::verificarPermiso('ModificarParametro');

        $parametro = new Empresa();
        $parametro->setScenario(Empresa::SCENARIO_EDITAR);

        if ($parametro->load(Yii::$app->request->post()) && $parametro->validate()) {
            $resultado = $parametro->Modificar();

            Yii::$app->response->format = 'json';
            if ($resultado == 'OK') {
                //Vuelvo a obtener los parámetros
                $empresa = new Empresa();
                Yii::$app->session->set('Parametros', ArrayHelper::map($empresa->DameDatos(), 'Parametro', 'Valor'));
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $parametro->DameParametro($id);
            return $this->renderAjax('modificar', [
                        'titulo' => 'Editar parámetro',
                        'model' => $parametro,
            ]);
        }
    }
}
