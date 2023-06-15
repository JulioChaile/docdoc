<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 04-Apr-2018 18:58:23
 */
class TiposCaso extends Model
{
    public $IdTipoCaso;
    public $TipoCaso;
    public $Estado;
    public $Color;
    // Derivados
    public $Juzgados;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';
    const ESTADOS = [
        'A' => 'Activo',
        'B' => 'Baja'
    ];
    

    public function rules()
    {
        return [
            [['IdTipoCaso', 'TipoCaso', 'Color'], 'required', 'on' => self::_MODIFICAR],
            ['TipoCaso', 'Color', 'required', 'on' => self::_ALTA],
            [$this->attributes(), 'safe']
        ];
    }
    /**
     * Permite instanciar un tipo de caso desde la base de datos. dsp_dame_tipocaso
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_tipocaso( :idTipoCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idTipoCaso' => $this->IdTipoCaso
        ]);
        
        $this->attributes = $query->queryOne();
    }

    public function ListarJuzgados()
    {
        $sql = 'CALL dsp_listar_tipocasojuzgados( :idTipoCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idTipoCaso' => $this->IdTipoCaso
        ]);

        $resultado = $query->queryOne();

        $resultado['Juzgados'] = json_decode($resultado['Juzgados'], true);
        
        $this->attributes = $resultado;

        return $resultado;
    }

    /**
     * Permite cambiar el estado de un tipo de caso a Activo, controlando que no se
     * encuentre activo ya. Devuelve OK o un mensaje de error en Mensaje.
     * dsp_activar_tipocaso
     */
    public function Activar()
    {
        $sql = 'CALL dsp_activar_tipocaso( :token, :idTipoCaso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idTipoCaso' => $this->IdTipoCaso
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite cambiar el estado de un tipo de caso a Baja, controlando que no se
     * encuentre dado de baja ya. Devuelve OK o un mensaje de error en Mensaje.
     * dsp_darbaja_tipocaso
     */
    public function DarBaja()
    {
        $sql = 'CALL dsp_darbaja_tipocaso( :token, :idTipoCaso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idTipoCaso' => $this->IdTipoCaso
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite dar de alta un rol al tipo de caso, controlando que el nombre no se
     * encuentre en uso ya. Devuelve OK o un mensaje de error en Mensaje.
     * dsp_alta_rol_tipocaso
     *
     * @param Objeto
     */
    public function AltaRol($Objeto)
    {
        $sql = 'CALL dsp_alta_rol_tipocaso( :token, :idTipoCaso, :rol, :parametros,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idTipoCaso' => $this->IdTipoCaso,
            ':rol' => $Objeto->Rol,
            ':parametros' => $Objeto->Parametros,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite modificar un rol de un tipo de caso controlando que el nombre no se
     * encuentre en uso ya. Devuelve OK o un mensaje de error en Mensaje.
     * dsp_modificar_rol_tipocaso
     *
     * @param Objeto
     */
    public function ModificarRol($Objeto)
    {
        $sql = 'CALL dsp_modificar_rol_tipocaso( :token, :idRTC, :rol, :parametros,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idRTC' => $Objeto->IdRTC,
            ':rol' => $Objeto->Rol,
            ':parametros' => $Objeto->Parametros,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite borrar un rol de tipo de caso controlando que no existan actores con
     * ese rol. Devuelve OK o un mensaje de error en Mensaje. dsp_borrar_rol_tipocaso
     *
     * @param Objeto
     */
    public function BorrarRol($Objeto)
    {
        $sql = 'CALL dsp_borrar_rol_tipocaso( :token, :idRTC, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idRTC' => $Objeto->IdRTC,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite listar los rles de un tipo de caso. Ordena por Rol.
     * dsp_listar_roles_tipocaso
     */
    public function ListarRoles()
    {
        $sql = 'CALL dsp_listar_roles_tipocaso( :idTipoCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idTipoCaso' => $this->IdTipoCaso,
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite agregar un juzgado al tipo de caso.
     * dsp_tipocaso_agregar_juzgado
     */
    public function AgregarJuzgado($IdJuzgado, $IdCompetencia)
    {
        $sql = 'CALL dsp_tipocaso_agregar_juzgado( :token, :idTipoCaso, :idCompetencia, :idJuzgado, :IP, :userAgent, :app )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idTipoCaso' => $this->IdTipoCaso,
            ':idJuzgado' => $IdJuzgado,
            ':idCompetencia' => $IdCompetencia
        ]);

        return $query->queryScalar();
    }

    public function QuitarJuzgado($IdJuzgado, $IdCompetencia)
    {
        $sql = 'CALL dsp_tipocaso_quitar_juzgado( :token, :idCompetencia, :idTipoCaso, :idJuzgado, :IP, :userAgent, :app )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            'idTipoCaso' => $this->IdTipoCaso,
            'idJuzgado' => $IdJuzgado,
            'idCompetencia' => $IdCompetencia
        ]);

        return $query->queryScalar();
    }
}
