<?php
namespace common\models;

use Yii;
use yii\base\Model;

class TiposMovimiento extends Model
{
    public $IdTipoMov;
    public $IdEstudio;
    public $TipoMovimiento;
    public $Categoria;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';
    
    const CATEGORIAS = [
        'P' => 'Procesal',
        'O' => 'Gestión de oficina'
    ];
    
    public function attributeLabels()
    {
        return [
            'IdEstudio' => 'Estudio jurídico'
        ];
    }
    public function rules()
    {
        return [
            [['IdEstudio', 'TipoMovimiento', 'Categoria'], 'required', 'on' => self::_ALTA],
            [['IdTipoMov', 'TipoMovimiento', 'Categoria'], 'required', 'on' => self::_MODIFICAR],
            [['IdTipoMov', 'IdEstudio', 'TipoMovimiento', 'Categoria'], 'safe'],
        ];
    }
    /**
     * Permite instanciar un tipo de movimiento desde la base de datos.
     * dame_tipomovimiento
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_tipomovimiento( :idTipoMov )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idTipoMov' => $this->IdTipoMov
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
