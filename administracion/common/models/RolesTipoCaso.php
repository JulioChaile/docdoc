<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 04-Apr-2018 19:21:01
 */
class RolesTipoCaso extends Model
{
    public $IdRTC;
    public $IdTipoCaso;
    public $Rol;
    public $Parametros;
    
    //Deriados
    public $TipoCaso;

    public function rules()
    {
        return [
            [['IdRTC', 'IdTipoCaso', 'Rol', 'TipoCaso', 'Parametros'], 'safe']
        ];
    }

    /**
     * Permite instanciar un rol de tipo de caso desde la base de datos.
     * dame_rol_tipocaso
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_rol_tipocaso ( :idRTC )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idRTC' => $this->IdRTC
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
