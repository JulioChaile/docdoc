<?php
namespace common\models\forms;

use yii\base\Model;

class DerivarConsultaForm extends Model
{
    public $IdDerivacionConsulta;
    public $IdConsulta;
    public $IdEstudio;
    public $IdAbogado;
    public $IdUsuario;
    public $FechaDerivacion;
    
    //Derivados
    public $Estudio;
    public $Abogado;
    public $Usuario;
    
    const ESTADOS = [
        'P' => 'Pendiente',
        'A' => 'Aceptada',
        'R' => 'Rechazada'
    ];
    
    public function attributeLabels()
    {
        return [
            'IdConsulta' => 'Consulta',
            'IdEstudio' => 'Estudio jurídico',
            'IdAbogado' => 'Abogado'
        ];
    }
    
    public function rules()
    {
        return [
            [['IdDerivacionConsulta', 'IdConsulta', 'IdEstudio', 'IdAbogado', 'IdUsuario',
                'FechaDerivacion', 'Estudio', 'Abogado', 'Usuario'], 'safe']
        ];
    }
}
