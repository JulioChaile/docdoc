<?php

namespace frontend\modules\api\controllers;

use common\models\Estudios;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use common\models\Personas;

class EstudiosController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bearerAuth' => [
                    'class' => OptionalBearerAuth::className(),
                    'except' => ['options'],
                    'actionsClient' => ['modificar-persona']
                ],
            ]
        );
    }
    
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $estudio = new Estudios();
        
        $estudio->IdEstudio = $id;
        
        $estudio->Dame();
        
        return $estudio;
    }
    
    public function actionEstadosCaso($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $estudio = new Estudios();
        
        $estudio->IdEstudio = $id;
        
        $estados = $estudio->ListarEstadosCaso();
        
        return $estados;
    }
    
    public function actionOrigenes($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $estudio = new Estudios();
        
        $estudio->IdEstudio = $id;
        
        $origenes = $estudio->ListarOrigenes();
        
        return $origenes;
    }
    
    public function actionTiposMovimiento($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $estudio = new Estudios();
        
        $estudio->IdEstudio = $id;
        
        $Cadena = Yii::$app->request->get('Cadena');
        
        $Categoria = Yii::$app->request->get('Categoria');
        
        $tm = $estudio->BuscarTiposMovimiento($Cadena, $Categoria);
        
        return $tm;
    }
    
    public function actionBorrarPersona()
    {
        $estudio = new Estudios();
        
        $IdPersona = Yii::$app->request->post('IdPersona');
        
        $tm = $estudio->BorrarPersona($IdPersona);
        
        if ($tm != 'OK') {
            return ['Error' => $tm];
        } else {
            return ['Error' => null];
        }
    }
    
    public function actionIntervaloFechasMovimientos($id)
    {
        $estudio = new Estudios();
        
        $estudio->IdEstudio = $id;
        
        $intervalo = $estudio->DameIntervaloFechasMovimientos();
        
        return $intervalo;
    }

    public function actionUsuarios($id)
    {
        $estudio = new Estudios();
        
        $estudio->IdEstudio = $id;
        
        return $estudio->BuscarUsuarios();
    }

    public function actionModificarPersona($id, $idPersona)
    {
        $estudio = new Estudios();

        $estudio->IdEstudio = $id;

        $persona = new Personas();

        $persona->setAttributes(Yii::$app->request->post('persona'));

        $persona->IdPersona = $idPersona;

        $IdCaso = Yii::$app->request->post('IdCaso');

        $EsPrincipal = Yii::$app->request->post('EsPrincipal');
        
        return $estudio->ModificarPersona($persona, $EsPrincipal, $IdCaso);
    }

    public function actionListar()
    {
        $estudio = new Estudios();

        return $estudio->ListarEstudios();
    }

    public function actionMensajesEstudio($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $estudio = new Estudios();
        
        $estudio->IdEstudio = $id;

        return $estudio->ListarMensajes();
    }

    public function actionCuadernos($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $estudio = new Estudios();
        
        $estudio->IdEstudio = $id;

        return $estudio->ListarCuadernos();
    }

    public function actionCalendario($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $estudio = new Estudios();
        
        $estudio->IdEstudio = $id;

        return $estudio->ListarCalendarios();
    }

    public function actionBuscarPersonas($id)
    {
        $Cadena = Yii::$app->request->get('Cadena');
        $Tipo = Yii::$app->request->get('Tipo');

        $estudio = new Estudios();
        
        $estudio->IdEstudio = $id;

        return $estudio->BuscarAvanzadoPersonas($Cadena, $Tipo);
    }

    public function actionEventos()
    {
        $estudio = new Estudios();
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        
        $calendario = $estudio->ListarCalendarios();

        !empty($calendario) ? $eventos = $estudio->BuscarEventos($calendario['IdCalendario'], '') : $eventos = array();
            
        return $eventos;
    }

    public function actionListarDocumentacionSolicitada()
    {
        $estudio = new Estudios();
        
        $sql = 'SELECT * FROM CombosDocumentacion';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $Combos = $query->queryAll();

        return [
            'Doc' => $estudio->ListarDocumentacionSolicitada(),
            'Combos' => $Combos
        ];
    }

    public function actionAltaDocumentacionSolicitada()
    {
        $doc = Yii::$app->request->post('doc');

        $estudio = new Estudios();

        $respuesta = $estudio->AltaDocumentacionSolicitada($doc);

        return [
            'Error' => null
        ];
    }
}
