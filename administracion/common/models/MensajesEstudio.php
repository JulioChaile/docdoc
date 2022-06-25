<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 11-Apr-2018 19:37:15
 */
class MensajesEstudio extends Model
{
    public $IdMensajeEstudio;
    public $IdEstudio;
    public $MensajeEstudio;
    public $Titulo;
    public $NombreTemplate;
    public $NameSpace;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';

    public function rules()
    {
        return [
            [['IdEstudio','MensajeEstudio', 'Titulo'], 'required', 'on' => self::_ALTA],
            [['IdMensajeEstudio', 'IdEstudio', 'MensajeEstudio', 'Titulo'], 'required', 'on' => self::_MODIFICAR],
            [['IdMensajeEstudio', 'IdEstudio', 'MensajeEstudio', 'Titulo'], 'safe']
        ];
    }

    /**
     * Permite instanciar un mensaje  desde la base de datos. dsp_dame_estadocaso
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_mensajeestudio( :idMensajeEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idMensajeEstudio' => $this->IdMensajeEstudio,
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
