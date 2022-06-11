<?php
namespace common\models;

use Yii;
use yii\base\Model;

class Mediadores extends Model
{
    public $IdMediador;
    public $Nombre;
    public $Registro;
    public $MP;
    public $Domicilio;
    public $Telefono;
    public $Email;

    public $IdChatMediador;

    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';

    public function rules()
    {
        return [
            [['IdMediador', 'Nombre'], 'required', 'on' => self::_MODIFICAR],
            ['Nombre', 'required', 'on' => self::_ALTA],
            [$this->attributes(), 'safe']
        ];
    }

    public function Dame()
    {
        $sql = 'CALL dsp_dame_mediador( :idMediador )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idMediador' => $this->IdMediador
        ]);
        
        $this->attributes = $query->queryOne();
    }

    /**
     * Permite dar de alta un archivo a un Contacto, controlando que el usuario sea un
     * usuario del Contacto y tenga permiso de Edici�n o Administraci�n sobre el Contacto, que
     * el responsable sea un usuario del Contacto y tenga permiso de Edici�n o
     * Administraci�n sobre el Contacto. dsp_alta_multimedia_Contacto
     *
     * @param Objeto
     */
    public function AltaMultimedia($Multimedia)
    {
        $sql = 'CALL dsp_alta_multimedia_mediador( :idMediador, :multimedia )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idMediador' => $this->IdMediador,
            ':multimedia' => json_encode($Multimedia)
        ]);
        
        return $query->queryScalar();
    }
}
