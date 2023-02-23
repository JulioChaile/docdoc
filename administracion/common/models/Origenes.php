<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 18-May-2018 18:19:34
 */
class Origenes extends Model
{
    public $IdOrigen;
    public $IdEstudio;
    public $Origen;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';
    
    /*
    public function attributeLabels()
    {
        return [
            'IdEstudio' => 'Estudio'
        ];
    }
    */

    public function rules()
    {
        return [
            [[/*'IdEstudio', */'Origen'], 'required', 'on' => self::_ALTA],
            [['IdOrigen', 'Origen'], 'required', 'on' => self::_MODIFICAR],
            [['IdOrigen', /*'IdEstudio',*/ 'Origen'], 'safe']
        ];
    }

    /**
     * Permite instanciar un origen de casos desde la base de datos. dame_origen
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_origen( :idOrigen )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idOrigen' => $this->IdOrigen,
        ]);
        
        $this->attributes = $query->queryOne();
    }

    /** Permite listar todos los origenes */
    public function ListarOrigenes($IdEstudio)
    {
        $sql = 'CALL dsp_listar_origenes( :idEstudio )';

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
        ]);

        return $query->queryAll();
    }
}
