<?php
namespace backend\controllers;

use common\models\Casos;
use common\models\GestorCasos;
use common\models\GestorTiposCaso;
use common\models\Juzgados;
use common\components\FechaHelper;
use common\models\forms\BusquedaForm;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class CasosController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }
    
    public function actionListar()
    {
        $busqueda = new BusquedaForm();
        
        $IdEstudio = 0;
        $IdUsuario = 0;
        $IdNominacion = 0;
        $IdEstadoCaso = 0;
        $FechaInicio = null;
        $FechaFin = null;
        $Tipo = 'T';
        $Cadena = '';
        $IdTipoCaso = 0;
        
        $casos = array();
        if ($busqueda->load(Yii::$app->request->post())) {
            $IdEstudio = $busqueda->Id == '' ? 0 : $busqueda->Id;
            $IdUsuario = $busqueda->Id2 == '' ? 0 : $busqueda->Id2;
            $IdNominacion = $busqueda->Combo3 == '' ? 0 : $busqueda->Combo3;
            $IdEstadoCaso = $busqueda->Combo4 == '' ? 0 : $busqueda->Combo4;
            $FechaInicio = FechaHelper::formatearDateMysql($busqueda->FechaInicio);
            $FechaFin = FechaHelper::formatearDateMysql($busqueda->FechaFin);
            $Tipo = $busqueda->Combo;
            $Cadena = $busqueda->Cadena;
            $IdTipoCaso = $busqueda->Combo6 == '' ? 0 : $busqueda->Combo6;
            
            $gestor = new GestorCasos();
            $casos = $gestor->BuscarAvanzado($IdEstudio, $IdUsuario, $IdNominacion, $IdEstadoCaso, $FechaInicio, $FechaFin, $Tipo, $Cadena, $IdTipoCaso);
        }
                
        $gestorTC = new GestorTiposCaso();
        $tiposCaso = $gestorTC->Buscar();
        
        $juzgado = new Juzgados();
        $juzgado->IdJuzgado = 0;
        $nominaciones2 = array();
        $nominaciones = $juzgado->BuscarNominaciones();
        foreach ($nominaciones as $nom) {
            $nom['Nominacion'] = $nom['Juzgado'].' '.$nom['Nominacion'];
            $nominaciones2[] = $nom;
        }
        
        $subestados = array();
        
        return $this->render('index', [
                    'models' => $casos,
                    'busqueda' => $busqueda,
                    'tiposCaso' => $tiposCaso,
                    'nominaciones' => $nominaciones2,
                    'subestados' => $subestados,
        ]);
    }
    
    public function actionListarPersonas($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'El caso indicado no es válido.');
        }
        
        $caso = new Casos();
        $caso->IdCaso = $id;
        $actores = $caso->ListarPersonas();
        
        return $this->renderAjax('actores', [
                    'actores' => $actores,
                    'model' => $caso
        ]);
    }
    
    public function actionListarMovimientos($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'El caso indicado no es válido.');
        }
        
        $caso = new Casos();
        $caso->IdCaso = $id;
        $movimientos = $caso->ListarMovimientos();
        Yii::info($movimientos);
        return $this->renderAjax('movimientos', [
                    'movimientos' => $movimientos,
                    'model' => $caso
        ]);
    }
}
