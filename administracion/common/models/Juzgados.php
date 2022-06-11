<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 12-Apr-2018 17:48:22
 */
class Juzgados extends Model
{
    public $IdJuzgado;
    public $IdJurisdiccion;
    public $Juzgado;
    public $Estado;
    public $ModoGestion;
    // Derivados
    public $EstadoAmbitoGestion;

    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';

    const ESTADOS = [
        'A' => 'Activo',
        'B' => 'Baja'
    ];
    const MODOS_GESTION = [
        'E' => 'Extrajudicial',
        'J' => 'Judicial'
    ];
    
    public function attributeLabels()
    {
        return [
            'IdJurisdiccion' => 'Jurisdicción'
        ];
    }
    public function rules()
    {
        return [
            [['IdJurisdiccion', 'Juzgado', 'ModoGestion'], 'required', 'on' => self::_ALTA],
            [['IdJuzgado', 'Juzgado', 'ModoGestion'], 'required', 'on' => self::_MODIFICAR],
            [$this->attributes(), 'safe'],
        ];
    }

    /**
     * Permite instanciar un juzgado des de la base de datos.
     * dsp_dame_juzgado
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_juzgado( :idJuzgado )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idJuzgado' => $this->IdJuzgado
        ]);
        
        $this->attributes = $query->queryOne();

        $this->EstadoAmbitoGestion = json_decode($this->EstadoAmbitoGestion, true);
    }

    /**
     * Permite cambiar el estado de un juzgado a Activo. Devuelve OK o un mensaje de
     * error en Mensaje.
     * dsp_activar_juzgado
     */
    public function Activar()
    {
        $sql = 'CALL dsp_activar_juzgado( :token, :idJuzgado, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idJuzgado' => $this->IdJuzgado
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite cambiar el estado de un juzgado a Baja. Devuelve OK o un mensaje de
     * error en Mensaje.
     * dsp_darbaja_juzgado
     */
    public function DarBaja()
    {
        $sql = 'CALL dsp_darbaja_juzgado( :token, :idJuzgado, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idJuzgado' => $this->IdJuzgado
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite buscar nominaciones filtr�ndolas por juzgado (pJuzgado = 0 para todas),
     * una cadena de b�squeda e indicando si se incluyen o no las dadas de baja.
     * Ordena por Nominacion. dsp_buscar_nominaciones
     *
     *
     * @param Cadena
     * @param IncluyeBajas
     */
    public function BuscarNominaciones($Cadena = '', $IncluyeBajas = 'N')
    {
        $sql = 'CALL dsp_buscar_nominaciones( :idJuzgado, :cadena, :incluyeBajas )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idJuzgado' => $this->IdJuzgado,
            ':cadena' => $Cadena,
            ':incluyeBajas' => $IncluyeBajas,
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite crear una nominaci�n controlando que exista el juzgado y el nombre no
     * se encuentre en uso ya. Devuelve OK + el id de la nominaci�n creada o un
     * mensaje de error en Mensaje. dsp_alta_nominacion
     *
     * @param Objeto
     */
    public function AltaNominacion($Objeto)
    {
        $sql = 'CALL dsp_alta_nominacion( :token, :idJuzgado, :nominacion, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idJuzgado' => $this->IdJuzgado,
            ':nominacion' => $Objeto->Nominacion,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite modificar una nominaci�n controlando que exista el juzgado y el nombre
     * no se encuentre en uso ya. Devuelve OK o un mensaje de error en Mensaje.
     * dsp_modificar_nominacion
     *
     * @param Objeto
     */
    public function ModificarNominacion($Objeto)
    {
        $sql = 'CALL dsp_modificar_nominacion( :token, :idNominacion, :nominacion, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idNominacion' => $Objeto->IdNominacion,
            ':nominacion' => $Objeto->Nominacion,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite borrar una nominaci�n controlando que no existan casos asociados.
     * Devuelve OK o un mensaje de error en Mensaje. dsp_borrar_nominacion
     *
     * @param Objeto
     */
    public function BorrarNominacion($Objeto)
    {
        $sql = 'CALL dsp_borrar_nominacion( :token, :idNominacion, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idNominacion' => $Objeto->IdNominacion,
        ]);
        
        return $query->queryScalar();
    }

    public function AgregarEstado($IdEstadoAmbitoGestion, $Orden)
    {
        $sql = 'CALL dsp_alta_estadoambitogestion_juzgado( :token, :IdEstadoAmbitoGestion, :IdJuzgado, :Orden, :IP, :userAgent, :app )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':IdEstadoAmbitoGestion' => $IdEstadoAmbitoGestion,
            ':Orden' => $Orden,
            ':IdJuzgado' => $this->IdJuzgado,
        ]);
        
        return $query->queryScalar();
    }

    public function BorrarEstado($IdEstadoAmbitoGestion)
    {
        $sql = 'CALL dsp_borrar_estado_juzgado( :token, :IdEstadoAmbitoGestion, :IdJuzgado, :IP, :userAgent, :app )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':IdEstadoAmbitoGestion' => $IdEstadoAmbitoGestion,
            ':IdJuzgado' => $this->IdJuzgado,
        ]);
        
        return $query->queryScalar();
    }

    public function EditarEstado($IdEstadoAmbitoGestion, $Orden)
    {
        $sql = 'CALL dsp_editar_estadoambitogestion_juzgado( :token, :IdEstadoAmbitoGestion, :IdJuzgado, :Orden )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IdEstadoAmbitoGestion' => $IdEstadoAmbitoGestion,
            ':Orden' => $Orden,
            ':IdJuzgado' => $this->IdJuzgado,
        ]);
        
        return $query->queryScalar();
    }

    public function Estados()
    {
        $sql = 'CALL dsp_listar_estadosambitogestion_juzgado';

        $query = Yii::$app->db->createCommand($sql);

        return $query->queryAll();
    }
}
