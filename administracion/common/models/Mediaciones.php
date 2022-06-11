<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\components\FechaHelper;

class Mediaciones extends Model
{
    public $IdMediacion;
    public $IdMediador;
    public $IdCaso;
    public $IdBono;
    public $Bono;
    public $IdEstadoBeneficio;
    public $IdBeneficio;
    public $Beneficio;
    public $FechaBonos;
    public $FechaPresentado;
    public $FechaProximaAudiencia;
    public $Legajo;
    public $IdChatMediador;
    public $Mediador;

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

    public function DameMediacion()
    {
        $sql = "CALL dsp_dame_mediacion( :idMediacion )";

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':idMediacion' => $this->IdMediacion
        ]);

        $this->attributes = $query->queryOne();

        $this->FechaBonos = FechaHelper::formatearDateLocal($this->FechaBonos);
        $this->FechaPresentado = FechaHelper::formatearDateLocal($this->FechaPresentado);
        $this->FechaProximaAudiencia = FechaHelper::formatearDatetimeLocal($this->FechaProximaAudiencia);

        $this->Mediador = json_decode($this->Mediador, true);
    }

    public function AltaEvento($IdEvento)
    {
        $sql = "CALL dsp_alta_evento_mediacion( :idEvento, :idMediacion)";

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            'idEvento' => $IdEvento,
            'idMediacion' => $this->IdMediacion
        ]);

        return $query->queryScalar();
    }
}
