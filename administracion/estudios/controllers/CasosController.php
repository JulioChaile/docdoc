<?php
namespace estudios\controllers;

use common\models\Estudios;
use common\models\GestorCasos;
use common\models\forms\BusquedaForm;
use Yii;
use yii\web\Controller;

class CasosController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }
    
    public function actionListar($Estado = 'T', $Cadena = '')
    {
        $estudio = new Estudios();
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $busqueda = new BusquedaForm();
        $gestor = new GestorCasos;
        if ($busqueda->load(Yii::$app->request->post())) {
            $Estado = $busqueda->Combo;
            $Cadena = $busqueda->Cadena;
            $casos = $gestor->BuscarAvanzado($estudio->IdEstudio, 0, 0, 0, null, null, 'T', $Cadena, 0, $Estado);
        } else {
            $casos = $gestor->BuscarAvanzado($estudio->IdEstudio);
        }
        
        return $this->render('index', [
                    'casos' => $casos,
                    'busqueda' => $busqueda,
        ]);
    }
}
