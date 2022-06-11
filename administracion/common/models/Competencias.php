<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\components\FechaHelper;

/**
 * @version 1.0
 * @created 25-May-2018 19:59:22
 */
class Competencias extends Model
{
    public $IdCompetencia;
    public $Competencia;
    public $Estado;
    // Derivados
    public $TiposCaso;

    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';

    const ESTADOS = [
        'A' => 'Activo',
        'B' => 'Archivado'
    ];

    public function rules()
    {
        return [
            [['IdCompetencia', 'Competencia'], 'required', 'on' => self::_MODIFICAR],
            ['Competencia', 'required', 'on' => self::_ALTA],
            [$this->attributes(), 'safe']
        ];
    }

    public function Activar()
    {
        $sql = 'CALL dsp_activar_competencia( :token, :idCompetencia, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCompetencia' => $this->IdCompetencia
        ]);
        
        return $query->queryScalar();
    }

    public function DarBaja()
    {
        $sql = 'CALL dsp_darbaja_competencia( :token, :idCompetencia, :IP, :userAgent, :app )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCompetencia' => $this->IdCompetencia
        ]);

        return $query->queryScalar();
    }

    public function AgregarTipoCaso($IdTipoCaso)
    {
        $sql = 'CALL dsp_competencia_agregar_tipocaso( :token, :IdCompetencia, :IdTipoCaso, :IP, :userAgent, :app )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':IdTipoCaso' => $IdTipoCaso,
            ':IdCompetencia' => $this->IdCompetencia,
        ]);
        
        return $query->queryScalar();
    }

    public function BorrarTipoCaso($IdTipoCaso)
    {
        $sql = 'CALL dsp_borrar_tipocasocompetencia( :token, :IdTipoCaso, :IdCompetencia, :IP, :userAgent, :app )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':IdTipoCaso' => $IdTipoCaso,
            ':IdCompetencia' => $this->IdCompetencia,
        ]);

        return $query->queryScalar();
    }

    public function Dame()
    {
        $sql = 'CALL dsp_dame_competencia( :IdCompetencia )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':IdCompetencia' => $this->IdCompetencia
        ]);
        
        $this->attributes = $query->queryOne();

        $this->TiposCaso = json_decode($this->TiposCaso, true);
    }
}
