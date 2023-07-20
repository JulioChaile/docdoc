<?php
namespace common\models;

use Yii;
use yii\base\Model;

class Comunicados extends Model
{
    public $IdComunicado;
    public $Titulo;
    public $Contenido;
    public $FechaAlta;
    public $FechaComunicado;
    public $IdMultimedia;
    public $IdEstudio;
}
