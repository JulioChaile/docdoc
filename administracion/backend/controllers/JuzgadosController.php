<?php
namespace backend\controllers;

use common\components\PermisosHelper;
use common\models\forms\BusquedaForm;
use common\models\GestorJurisdicciones;
use common\models\GestorJuzgados;
use common\models\Juzgados;
use backend\models\Nominaciones;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class JuzgadosController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }
    
    public function actionListar($IdJurisdiccion = 0, $Cadena = '', $IncluyeBajas = 'N')
    {
        PermisosHelper::verificarPermiso('BuscarJuzgados');
        
        $busqueda = new BusquedaForm();
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $IdJurisdiccion = $busqueda->Id;
            $Cadena = $busqueda->Cadena;
            $IncluyeBajas = $busqueda->Check;
        }
        
        $gestor = new GestorJuzgados();
        $juzgados = $gestor->Buscar($IdJurisdiccion, $Cadena, $IncluyeBajas);
        
        $gestorJ = new GestorJurisdicciones();
        $jurisdicciones = $gestorJ->Buscar();
        
        return $this->render('index', [
                    'models' => $juzgados,
                    'busqueda' => $busqueda,
                    'jurisdicciones' => $jurisdicciones,
        ]);
    }
    
    public function actionAlta()
    {
        PermisosHelper::verificarPermiso('AltaJuzgado');
        
        $juzgado = new Juzgados();
        $juzgado->setScenario(Juzgados::_ALTA);
        if ($juzgado->load(Yii::$app->request->post()) && $juzgado->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorJuzgados();
            $resultado = $gestor->Alta($juzgado);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $gestorJ = new GestorJurisdicciones();
            $jurisdicciones = $gestorJ->Buscar();
            
            return $this->renderAjax('datos-juzgados', [
                        'model' => $juzgado,
                        'jurisdicciones' => $jurisdicciones,
                        'titulo' => 'Nuevo juzgado'
            ]);
        }
    }
    
    public function actionDuplicar($id)
    {
        
        $juzgado = new Juzgados();
        $juzgado->setScenario(Juzgados::_DUPLICAR);
        $juzgado->IdJuzgado = $id;
        if ($juzgado->load(Yii::$app->request->post()) && $juzgado->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $sql = 'SELECT * FROM Juzgados WHERE IdJuzgado = :idJuzgado';
        
            $query = Yii::$app->db->createCommand($sql);
            
            $query->bindValues([
                ':idJuzgado' => $id
            ]);

            $JuzgadoA = $query->queryOne();

            $sql13 = 'SELECT MAX(IdJuzgado) + 1 FROM Juzgados LIMIT 1';
        
            $query13 = Yii::$app->db->createCommand($sql13);

            $idB = $query13->queryScalar();

            $sql2 = 'INSERT INTO Juzgados (IdJuzgado, IdJurisdiccion, Juzgado, Estado, ModoGestion, Color) VALUES ( ' .
                $idB . ', ' .
                $JuzgadoA['IdJurisdiccion'] . ', ' .
                '"' . $juzgado->Juzgado . '", ' .
                '"' . $JuzgadoA['Estado'] . '", ' .
                '"' . $JuzgadoA['ModoGestion'] . '",' .
                '"' . $JuzgadoA['Color'] . '"' .
            ')';
        
            $query2 = Yii::$app->db->createCommand($sql2);

            $query2->execute();

            $sql4 = 'INSERT INTO JuzgadosEstadosAmbitos SELECT IdEstadoAmbitoGestion,' . $idB . ', Orden FROM JuzgadosEstadosAmbitos WHERE IdJuzgado = ' . $id;
        
            $query4 = Yii::$app->db->createCommand($sql4);

            $query4->execute();

                return ['error' => null];
        } else {
            
            return $this->renderAjax('datos-duplicar', [
                        'titulo' => 'Duplicar juzgado',
                        'model' => $juzgado
            ]);
        }
    }
    
    public function actionModificar($id)
    {
        PermisosHelper::verificarPermiso('ModificarJuzgado');
        
        if (!intval($id)) {
            throw new HttpException('422', 'El juzgado indicado no es válido.');
        }
        
        $juzgado = new Juzgados();
        $juzgado->setScenario(Juzgados::_MODIFICAR);
        $juzgado->IdJuzgado = $id;
        if ($juzgado->load(Yii::$app->request->post()) && $juzgado->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorJuzgados();
            $resultado = $gestor->Modificar($juzgado);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $juzgado->Dame();
            $gestorJ = new GestorJurisdicciones();
            $jurisdicciones = $gestorJ->Buscar();
            
            return $this->renderAjax('datos-juzgados', [
                        'model' => $juzgado,
                        'jurisdicciones' => $jurisdicciones,
                        'titulo' => 'Modificar juzgado'
            ]);
        }
    }
    
    public function actionBorrar($id)
    {
        PermisosHelper::verificarPermiso('BorrarJuzgado');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'El juzgado indicado no es válido.'];
        }
        
        $juzgado = new Juzgados();
        $juzgado->IdJuzgado = $id;
        
        $gestor = new GestorJuzgados();
        $resultado = $gestor->Borrar($juzgado);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionActivar($id)
    {
        PermisosHelper::verificarPermiso('ActivarJuzgado');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'El juzgado indicado no es válido.'];
        }
        
        $juzgado = new Juzgados();
        $juzgado->IdJuzgado = $id;
        
        $resultado = $juzgado->Activar();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionDarBaja($id)
    {
        PermisosHelper::verificarPermiso('DarBajaJuzgado');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'El juzgado indicado no es válido.'];
        }
        
        $juzgado = new Juzgados();
        $juzgado->IdJuzgado = $id;
        
        $resultado = $juzgado->DarBaja();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionNominaciones($id)
    {
        PermisosHelper::verificarPermiso('BuscarNominaciones');
        
        if (!intval($id)) {
            throw new HttpException('422', 'El juzgado indicado no es válido.');
        }
        
        $busqueda = new BusquedaForm();
        
        $IncluyeBajas = 'N';
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $IncluyeBajas = $busqueda->Check;
        }
        
        $juzgado = new Juzgados();
        $juzgado->IdJuzgado = $id;
        $juzgado->Dame();
        $nominaciones = $juzgado->BuscarNominaciones('', $IncluyeBajas);
        
        return $this->render('nominaciones', [
                    'models' => $nominaciones,
                    'juzgado' => $juzgado,
                    'busqueda' => $busqueda
        ]);
    }
    
    public function actionAltaNominacion($id)
    {
        PermisosHelper::verificarPermiso('AltaNominacion');
        
        if (!intval($id)) {
            throw new HttpException('422', 'El juzgado indicado no es válido.');
        }
        
        $nominacion = new Nominaciones();
        $nominacion->IdJuzgado = $id;
        $nominacion->setScenario(Nominaciones::_ALTA);
        if ($nominacion->load(Yii::$app->request->post()) && $nominacion->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $juzgado = new Juzgados();
            $juzgado->IdJuzgado = $id;
            $resultado = $juzgado->AltaNominacion($nominacion);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-nominaciones', [
                        'model' => $nominacion,
                        'titulo' => 'Nueva nominación',
                        
            ]);
        }
    }
    
    public function actionModificarNominacion($id)
    {
        PermisosHelper::verificarPermiso('ModificarNominacion');
        
        if (!intval($id)) {
            throw new HttpException('422', 'La nominación indicada no es válida.');
        }
        
        $nominacion = new Nominaciones();
        $nominacion->setScenario(Nominaciones::_MODIFICAR);
        $nominacion->IdNominacion = $id;
        if ($nominacion->load(Yii::$app->request->post()) && $nominacion->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $juzgado = new Juzgados();
            $resultado = $juzgado->ModificarNominacion($nominacion);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $nominacion->Dame();
            return $this->renderAjax('datos-nominaciones', [
                        'model' => $nominacion,
                        'titulo' => 'Modificar nominación',
                        
            ]);
        }
    }
    
    public function actionBorrarNominacion($id)
    {
        PermisosHelper::verificarPermiso('BorrarNominacion');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'La nominación indicada no es válida.'];
        }
        
        $nominacion = new Nominaciones();
        $nominacion->IdNominacion = $id;
        
        $juzgado = new Juzgados();
        $resultado = $juzgado->BorrarNominacion($nominacion);
        
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionActivarNominacion($id)
    {
        PermisosHelper::verificarPermiso('ActivarNominacion');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'La nominación indicada no es válida.'];
        }
        
        $nominacion = new Nominaciones();
        $nominacion->IdNominacion = $id;
        
        $resultado = $nominacion->Activar();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionDarBajaNominacion($id)
    {
        PermisosHelper::verificarPermiso('DarBajaNominacion');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'La nominación indicada no es válida.'];
        }
        
        $nominacion = new Nominaciones();
        $nominacion->IdNominacion = $id;
        
        $resultado = $nominacion->DarBaja();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }

    public function actionEstados($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'El estado indicado no es válida.');
        }
        
        $Juzgado = new Juzgados();
        $Juzgado->IdJuzgado = $id;
        $Juzgado->Dame();
        return $this->renderAjax('datos-estados', [
                    'model' => $Juzgado,
                    'titulo' => 'Estados de Proceso: ' . $Juzgado->Juzgado
        ]);
    }

    public function actionAgregarEstado($id, $idEstadoAmbitoGestion, $Orden)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id) || !intval($idEstadoAmbitoGestion)) {
            return ['error' => 'El estado indicado no es válida.'];
        }
        
        $Juzgado = new Juzgados();
        $Juzgado->IdJuzgado = $id;
        $mensaje = $Juzgado->AgregarEstado($idEstadoAmbitoGestion, $Orden);
        if ($mensaje != 'OK') {
            return ['error' => $mensaje];
        }

        return ['error' => null];
    }

    public function actionQuitarEstado($id, $idEstadoAmbitoGestion)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id) || !intval($idEstadoAmbitoGestion)) {
            return ['error' => 'La competencia indicada no es válida.'];
        }
        
        $Juzgado = new Juzgados();
        $Juzgado->IdJuzgado = $id;
        $mensaje = $Juzgado->BorrarEstado($idEstadoAmbitoGestion);
        if ($mensaje != 'OK') {
            return ['error' => $mensaje];
        }

        return ['error' => null];
    }

    public function actionEditarEstado($id, $idEstadoAmbitoGestion, $Orden)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id) || !intval($idEstadoAmbitoGestion)) {
            return ['error' => 'El estado indicado no es válida.'];
        }
        
        $Juzgado = new Juzgados();
        $Juzgado->IdJuzgado = $id;
        $mensaje = $Juzgado->EditarEstado($idEstadoAmbitoGestion, $Orden);
        if ($mensaje != 'OK') {
            return ['error' => $mensaje];
        }

        return ['error' => null];
    }
}
