<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 11-Apr-2018 19:37:15
 */
class CalendariosEstudio extends Model
{
    public $IdCalendario;
    public $IdCalendarioAPI;
    public $IdEstudio;
    public $Titulo;
    public $Descripcion;
    public $IdColor;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';

    public function rules()
    {
        return [
            [['IdEstudio', 'Titulo'], 'required', 'on' => self::_ALTA],
            [['IdCalendario', 'IdEstudio', 'Titulo'], 'required', 'on' => self::_MODIFICAR],
            [['IdCalendario', 'IdEstudio', 'Descripcion', 'Titulo', 'IdColor'], 'safe']
        ];
    }

    /**
     * Permite instanciar un mensaje  desde la base de datos. dsp_dame_estadocaso
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_calendario_estudio( :idCalendario )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCalendario' => $this->IdCalendario,
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
