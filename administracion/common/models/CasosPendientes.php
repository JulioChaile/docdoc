<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 25-May-2018 19:59:22
 */
class CasosPendientes extends Model
{
    public $IdCasoPendiente;
    public $Apellidos;
    public $Nombres;
    public $Domicilio;
    public $Telefono;
    public $Prioridad;
    public $IdEstadoCasoPendiente;
    public $IdOrigen;
    public $Documento;
    public $Visitado;
    public $Latitud;
    public $Longitud;
    public $IdEstudio;
    public $FechaAlta;
    public $Lesion;
    public $IdCaso;
    public $IdPersona;
}
