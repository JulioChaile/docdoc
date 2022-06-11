<?php

namespace backend\controllers;

use backend\models\GestorCiudades;
use backend\models\Provincias;
use backend\models\Ciudades;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class CiudadesController extends Controller
{
    public function actionAutocompletar($id = 0, $cadena = '', $idProvincia = 0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if ($id != 0) {
            $ciudad = new Ciudades();
            
            $ciudad->IdCiudad = $id;
            
            $ciudad->Dame();
            
            $out = [
                'id' => $ciudad->IdCiudad,
                'text' => $ciudad->Ciudad.' (CP: '.$ciudad->CodPostal.')',
            ];
        } else {
            $gestor = new GestorCiudades();
            
            $out = array();
            
            $ciudades = $gestor->BuscarAvanzado($cadena, $idProvincia);
            
            foreach ($ciudades as $ciudad) {
                $out[] = [
                    'id' => $ciudad['IdCiudad'],
                    'text' => $ciudad['Ciudad'].' (CP: '.$ciudad['CodPostal'].')'
                ];
            }
        }
        return $out;
    }
    
    public function actionAutocompletarProvincias($id = 0, $cadena = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if ($id != 0) {
            $provincia = new Provincias();
            
            $provincia->IdProvincia = $id;
            
            $provincia->Dame();
            
            $out = [
                'id' => $provincia->IdProvincia,
                'text' => $provincia->Provincia,
            ];
        } else {
            $gestor = new GestorCiudades();
            
            $out = array();
            
            $provincias = $gestor->BuscarProvincias($cadena);
            
            foreach ($provincias as $provincia) {
                $out[] = [
                    'id' => $provincia['IdProvincia'],
                    'text' => $provincia['Provincia']
                ];
            }
        }
        return $out;
    }
}
