<?php

namespace backend\controllers;

use common\models\Empresa;
use common\models\forms\BusquedaForm;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;

class PadronController extends Controller
{
    public function actionIndex()
    {
        $empresa = new Empresa();
        $busqueda = new BusquedaForm();
        $paginado = new Pagination();
        
        $tipo = 'T';
        $cadena = '';
        if ($busqueda->load(Yii::$app->request->get()) && $busqueda->validate()) {
            $tipo = $busqueda->Combo;
            $cadena = $busqueda->Cadena;
        }

        if ($cadena === '') {
            return $this->render('index', [
                'busqueda' => $busqueda,
                'models' => [],
                'paginado' => $paginado,
            ]);
        }
        
        $resultado = $empresa->BuscarPadron($tipo, $cadena);
        
        $paginado->totalCount = count($resultado);
        $resultado = array_slice($resultado, $paginado->page * $paginado->pageSize, $paginado->pageSize);
        
        
        return $this->render('index', [
                    'busqueda' => $busqueda,
                    'models' => $resultado,
                    'paginado' => $paginado,
        ]);
    }
}
