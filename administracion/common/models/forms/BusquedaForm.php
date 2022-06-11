<?php

namespace common\models\forms;

use yii\base\Model;

class BusquedaForm extends Model
{
    public $Cadena;
    public $Cadena2;
    public $Check;
    public $FechaInicio;
    public $FechaFin;
    public $Id;
    public $Id2;
    public $Numero;
    public $Combo;
    public $Numero2;
    public $Combo2;
    public $Combo3;
    public $Combo4;
    public $Combo5;
    public $Combo6;

    /**
     * Reglas para validar los formularios.
     *
     * @return Array Reglas de validación
     */
    public function rules()
    {
        return [
            [['Cadena','Cadena2'], 'trim'],
            ['Check', 'in','range' => ['S','N']],
            [['Numero', 'Id', 'Id2' ,'Numero2'], 'integer', 'min' => 0],
            [['Numero', 'Id', 'Id2','Numero2', 'Combo', 'Combo2', 'Combo3'], 'default', 'value' => 0],
            [['Check', 'FechaInicio', 'FechaFin', 'Id', 'Numero', 'Combo',
            'Numero2', 'Combo2', 'Combo3', 'Combo4', 'Combo5', 'Combo6', 'Id2'], 'safe'],
        ];
    }
}
