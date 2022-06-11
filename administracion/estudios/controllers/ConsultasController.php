<?php
namespace estudios\controllers;

use common\models\Estudios;
use common\models\forms\BusquedaForm;
use common\models\forms\DerivarConsultaForm;
use Yii;
use yii\web\Controller;

class ConsultasController extends Controller
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
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Estado = $busqueda->Combo;
            $Cadena = $busqueda->Cadena;
        }
        $consultas = $estudio->BuscarConsultas($Estado, $Cadena);
        
        return $this->render('index', [
                    'consultas' => $consultas,
                    'busqueda' => $busqueda,
        ]);
    }
    
    public function actionAceptar($id)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id)) {
            return ['error' => 'La consula indicada no es válida.'];
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $consulta = new DerivarConsultaForm();
        $consulta->IdDerivacionConsulta = $id;
        $resultado = $estudio->AceptarDerivacionConsulta($consulta);
        
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionRechazar($id)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id)) {
            return ['error' => 'La consula indicada no es válida.'];
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $consulta = new DerivarConsultaForm();
        $consulta->IdDerivacionConsulta = $id;
        $resultado = $estudio->RechazarDerivacionConsulta($consulta);
        
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
}
