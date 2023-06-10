<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\components\FechaHelper;

/**
 * @version 1.0
 * @created 25-May-2018 19:59:22
 */
class Casos extends Model
{
    public $IdCaso;
    public $IdCasoEstudio;
    public $IdJuzgado;
    public $IdNominacion;
    public $IdCompetencia;
    public $IdTipoCaso;
    public $IdEstadoCaso;
    public $IdOrigen;
    public $IdChat;
    public $IdExternoChat;
    public $IdMediacion;
    public $IdEstadoAmbitoGestion;
    public $Caratula;
    public $NroExpediente;
    public $Carpeta;
    public $FechaAlta;
    public $Observaciones;
    public $FechaUltVisita;
    public $FechaEstado;
    public $Estado;
    public $Competencia;
    public $TipoCaso;
    public $Juzgado;
    public $Nominacion;
    public $Origen;
    public $FotoCaso;
    public $Defiende;
    public $RecDocFecha;
    public $RecDocFrec;
    public $RecDocActiva;
    
    //Derivados
    public $PersonasCaso;
    public $MovimientosCaso;
    public $EstadoAmbitoGestion;
    public $CausaPenal;
    public $Parametros;
    public $EtiquetasCaso;
    public $Comparticiones;
    
    const ESTADOS = [
        'A' => 'Activo',
        'B' => 'Archivado'
    ];

    public function rules()
    {
        return [
            [$this->attributes(), 'safe']
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'IdEstadoCaso' => 'Estado del caso',
            'IdNominacion' => 'Nominación',
            'FechaAlta' => 'Fecha de alta',
            'FechaUltVisita' => 'Última visita',
            'IdTipoCaso' => 'Tipo de caso',
            'IdOrigen' => 'Origen',
        ];
    }

    /**
     * Permite instanciar un caso desde la base de datos. dsp_dame_caso
     */
    public function Dame($IdEstudio = '', $Movs = 'S', $sinEstudio = 'N')
    {
        if (empty($IdEstudio) && $sinEstudio !== 'S') {
            $IdEstudio = Yii::$app->user->identity->IdEstudio;
        } else {
            $IdEstudio = 0;
        }

        $sql = 'CALL dsp_dame_caso( :idCaso, :idEstudio, :movs )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $this->IdCaso,
            ':idEstudio' => $IdEstudio,
            ':movs' => $Movs
        ]);
        
        $this->attributes = $query->queryOne();
        $this->PersonasCaso = json_decode($this->PersonasCaso);
        $this->MovimientosCaso = json_decode($this->MovimientosCaso);
        $this->CausaPenal = json_decode($this->CausaPenal);
        $this->Parametros = json_decode($this->Parametros);
        $this->EtiquetasCaso = json_decode($this->EtiquetasCaso);
        $this->Comparticiones = json_decode($this->Comparticiones);
    }

    /**
     * Permite listar los movimientos de un caso ordenandolos por FechaRealizado.
     * Lista todos si el IdCaso = 0.
     * dsp_listar_movimientos_caso
     */
    public function ListarMovimientos($Cadena = '', $Offset = 0, $Limit = 30, $Color, $Usuarios, $Tipos, $IdUsuarioGestion, $Tareas, $Recordatorios)
    {
        $sql = 'CALL dsp_listar_movimientos_caso( :token, :idCaso, :offset, :limit, :cadena, :color, :usuarios, :tipos, :idUsuarioGestion, :tareas, :recordatorios )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCaso' => $this->IdCaso,
            ':offset' => $Offset,
            ':limit' => $Limit,
            ':cadena' => $Cadena,
            ':color' => $Color,
            ':usuarios' => $Usuarios,
            ':tipos' => $Tipos,
            ':idUsuarioGestion' => $IdUsuarioGestion,
            ':tareas' => $Tareas,
            ':recordatorios' => $Recordatorios
        ]);
        
        return $query->queryAll();
    }


    public function ListarMovimientosClientes()
    {
        $sql = 'CALL dsp_listar_movimientos_cliente( :token, :idCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCaso' => $this->IdCaso
        ]);
        
        return $query->queryAll();
    }


    public function ListarEventosClientes($Usuario)
    {
        $sql = 'CALL dsp_listar_eventos_cliente( :token, :usuario )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':usuario' => $Usuario
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite listar los movimientos sin realizar de un caso ordenandolos por FechaAlta.
     * Lista todos si el IdCaso = 0.
     * dsp_listar_movimientos_sin_realizar_caso
     */
    public function ListarMovimientosSinRealizar()
    {
        $sql = 'CALL dsp_listar_movimientos_sin_realizar_caso( :token, :idCaso)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCaso' => $this->IdCaso,
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite listar los movimientos realizados de un caso ordenandolos por FechaAlta.
     * Lista todos si el IdCaso = 0.
     * dsp_listar_movimientos_sin_realizar_caso
     */
    public function ListarMovimientosRealizados()
    {
        $sql = 'CALL dsp_listar_movimientos_realizados_caso( :token, :idCaso)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCaso' => $this->IdCaso,
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite cambiar el estado del caso a B: Archivado. Devuelve OK o un mensaje de
     * error en Mensaje. dsp_archivar_caso
     */
    public function Archivar($Estado)
    {
        $sql = 'CALL dsp_archivar_caso( :token, :idCaso, :estado, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCaso' => $this->IdCaso,
            ':estado' => $Estado
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite dar de alta un movimiento a un caso, controlando que el usuario sea un
     * usuario del caso y tenga permiso de Edici�n o Administraci�n sobre el caso, que
     * el responsable sea un usuario del caso y tenga permiso de Edici�n o
     * Administraci�n sobre el caso. dsp_alta_movimiento_caso
     *
     * @param Objeto
     */
    public function AltaMovimiento($Objeto, $cliente)
    {
        $sql = 'CALL dsp_alta_movimiento_caso( :token, :idCaso, :idTipoMov,'
                . ' :idResponsable, :detalle, :fechaEsperada, :cuaderno, :escrito,'
                . ' :color, :multimedia, :fechaAlta, :cliente, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCaso' => $this->IdCaso,
            ':idTipoMov' => $Objeto->IdTipoMov,
            ':idResponsable' => $Objeto->IdResponsable,
            ':detalle' => $Objeto->Detalle,
            ':fechaEsperada' => FechaHelper::formatearDateMysql($Objeto->FechaEsperada),
            ':cuaderno' => $Objeto->Cuaderno,
            ':escrito' => $Objeto->Escrito,
            ':color' => $Objeto->Color,
            ':multimedia' => is_null($Objeto->Multimedia) ? null : json_encode($Objeto->Multimedia),
            ':fechaAlta' => FechaHelper::formatearDateMysql($Objeto->FechaAlta),
            ':cliente' => $cliente
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite instanciar una persona del caso. dsp_dame_persona_caso
     *
     * @param IdPersona
     */
    public function DamePersona($IdPersona)
    {
        $sql = 'CALL dsp_dame_persona_caso( :idCaso, :idPersona )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':idCaso' => $this->IdCaso,
            ':idPersona' => $IdPersona
        ]);

        $persona = $query->queryOne();
        $persona['Telefonos'] = json_decode($persona['Telefonos']);
        return $persona;
    }

    /**
     * Permite dar de alta personas y asignarlas a un caso. Se controla si la persona
     * existe en el estudio y si existe, no se modifican sus datos personales.
     * Devuelve OK o un mensaje de error en Mensaje. dsp_alta_personas_caso
     *
     * @param Objeto
     */
    public function AltaPersonas($Objeto)
    {
        $sql = 'CALL dsp_alta_personas_caso( :token, :idCaso, :personasCaso, :IP, :userAgent, :app )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCaso' => $this->IdCaso,
            ':personasCaso' => json_encode($Objeto)
        ]);

        return $query->queryScalar();
    }

    /**
     * Permite borrar una de un caso. Devuelve OK o el mensaje de error
     * en Mensaje. dsp_borrar_persona_caso
     *
     * @param Objeto
     */
    public function BorrarPersona($IdPersona)
    {
        $sql = 'CALL dsp_borrar_persona_caso( :token, :idCaso, :idPersona, :IP, :userAgent, :app )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCaso' => $this->IdCaso,
            ':idPersona' => $IdPersona
        ]);

        return $query->queryScalar();
    }

    /**
     * Permite listar todas las personas intervinientes en un caso.
     * dsp_listar_personas_caso
     */
    public function ListarPersonas()
    {
        $sql = 'CALL dsp_listar_personas_caso( :idCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $this->IdCaso,
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite dar de alta un objetivo a un caso. Devuelve OK + Id del caso creado o
     * un mensaje de error en Mensaje. dsp_alta_objetivo
     *
     * @param Objeto
     */
    public function AltaObjetivo($Objeto)
    {
        $sql = 'CALL dsp_alta_objetivo( :token, :idCaso, :objetivo,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCaso' => $this->IdCaso,
            ':objetivo' => $Objeto->Objetivo,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite modificar un objetivo. Devuelve OK o un mensaje de error en Mensaje.
     * dsp_modificar_objetivo
     *
     * @param Objeto
     */
    public function ModificarObjetivo($Objeto)
    {
        $sql = 'CALL dsp_modificar_objetivo( :token, :idObjetivo, :objetivo,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idObjetivo' => $Objeto->IdObjetivo,
            ':objetivo' => $Objeto->Objetivo,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite borrar un objetivo y las asociaciones con los movimientos que tenga.
     * Devuelve OK o un mensaje de error en Mensaje. dsp_borrar_objetivo
     *
     * @param Objeto
     */
    public function BorrarObjetivo($Objeto)
    {
        $sql = 'CALL dsp_borrar_objetivo( :token, :idObjetivo,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idObjetivo' => $Objeto->IdObjetivo,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite listar los objetivos de un caso. Ordena por FechaAlta.
     * dsp_listar_objetivos_caso
     */
    public function ListarObjetivos()
    {
        $sql = 'CALL dsp_listar_objetivos_caso( :idCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $this->IdCaso
        ]);
        
        return $query->queryAll();
    }
    /*
     * Permite cambiar el estado de un proyecto a Baja.
     * Devuelve OK o el mensaje de error en Mensaje. dsp_darbaja_caso
     */
    public function Baja()
    {
        $sql = 'CALL dsp_darbaja_caso( :token, :idCaso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCaso' => $this->IdCaso,
        ]);
        
        return $query->queryScalar();
    }
    
    public function BorrarMovimiento($Objeto)
    {
        $sql = 'CALL dsp_borrar_movimientocaso( :token, :idMovimientoCaso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idMovimientoCaso' => $Objeto->IdMovimientoCaso,
        ]);
        
        return $query->queryScalar();
    }
    
    public function ModificarMovimiento($Objeto)
    {
        $sql = 'CALL dsp_modificar_movimiento_caso( :token, :idMovimientoCaso, '
                . ':idTipoMov, :idResponsable, :detalle, :fechaEsperada, :fechaAlta, '
                . ':cuaderno, :escrito, :color, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idMovimientoCaso' => $Objeto->IdMovimientoCaso,
            ':idTipoMov' => $Objeto->IdTipoMov,
            ':idResponsable' => $Objeto->IdResponsable,
            ':detalle' => $Objeto->Detalle,
            ':fechaEsperada' => FechaHelper::formatearDateMysql($Objeto->FechaEsperada),
            ':fechaAlta' => FechaHelper::formatearDateMysql($Objeto->FechaAlta),
            ':cuaderno' => $Objeto->Cuaderno,
            ':escrito' => $Objeto->Escrito,
            ':color' => $Objeto->Color
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite notificar al responsable o creador del movimiento de Gestion Estudio,
     * que el mismo se vención, enviándole un mail.
     * dsp_listar_notificaciones_gestion_estudio
     */
    public function ListarNotificacionesGestionEstudio()
    {
        $sql = 'CALL dsp_listar_notificaciones_gestion_estudio()';
        
        $query = Yii::$app->db->createCommand($sql);
        
        return $query->queryAll();
    }

    /**
     * Permite compartir un caso a un usuario de otro estudio, controlando que
     * el usuario que gestiona la acción tenga permisos para compartir.
     * Devuelve OK o un Mensaje de error en Mensaje.
     * dsp_compartir_caso
     *
     * @param Usuario
     */
    public function Compartir($Usuario)
    {
        $sql = 'CALL dsp_compartir_caso( :token, :idcaso, '
                . ':idusuario, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idcaso' => $this->IdCaso,
            ':idusuario' => $Usuario->IdUsuario,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite compartir un caso a un usuario de otro estudio, controlando que
     * el usuario que gestiona la acción tenga permisos para compartir.
     * Devuelve OK o un Mensaje de error en Mensaje.
     * dsp_compartir_caso
     *
     * @param Usuario
     */
    public function CompartirPorEstudio($IdEstudio)
    {
        $sql = 'CALL dsp_compartir_caso_por_estudio( :token, :idcaso, '
                . ':idestudio, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idcaso' => $this->IdCaso,
            ':idestudio' => $IdEstudio,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite listar los archivos de un caso.
     * Lista todos si el IdCaso = 0.
     * dsp_listar_multimedia_caso
     */
    public function ListarMultimedia()
    {
        $sql = 'CALL dsp_listar_multimedia_caso( :idCaso)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $this->IdCaso
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite dar de alta un archivo a un caso, controlando que el usuario sea un
     * usuario del caso y tenga permiso de Edici�n o Administraci�n sobre el caso, que
     * el responsable sea un usuario del caso y tenga permiso de Edici�n o
     * Administraci�n sobre el caso. dsp_alta_multimedia_caso
     *
     * @param Objeto
     */
    public function AltaMultimedia($Multimedia)
    {
        $sql = 'CALL dsp_alta_multimedia_caso( :idCaso, :multimedia )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $this->IdCaso,
            ':multimedia' => json_encode($Multimedia)
        ]);
        
        return $query->queryScalar();
    }


    public function EliminarMultimedia($Multimedia)
    {
        $sql = 'CALL dsp_eliminar_multimedia_caso( :idCaso, :multimedia )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $this->IdCaso,
            ':multimedia' => json_encode($Multimedia)
        ]);
        
        return $query->queryScalar();
    }


    public function EditarNombreMultimedia($IdMultimedia, $Nombre)
    {
        $sql = 'CALL dsp_editar_nombre_multimedia_caso( :idCaso, :idMultimedia, :nombre )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $this->IdCaso,
            ':idMultimedia' => $IdMultimedia,
            ':nombre' => $Nombre
        ]);
        
        return $query->queryScalar();
    }

    public function Parametros($Parametros, $IdCaso)
    {
        $sql = 'CALL dsp_parametros_caso( :token, :parametros, :idCaso )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':parametros' => json_encode($Parametros),
            ':idCaso' => $IdCaso
        ]);

        return $query->queryScalar();
    }

    public function AltaCausaPenal($CausaPenal, $IdCaso)
    {
        $sql = 'CALL dsp_alta_causapenal_caso( :token, :idCaso, :estadoCausaPenal, :nroExpedienteCausaPenal, :radicacionCausaPenal, :comisaria )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCaso' => $IdCaso,
            ':estadoCausaPenal' => $CausaPenal->EstadoCausaPenal,
            ':nroExpedienteCausaPenal' => $CausaPenal->NroExpedienteCausaPenal,
            ':radicacionCausaPenal' => $CausaPenal->RadicacionCausaPenal,
            ':comisaria' => $CausaPenal->Comisaria
        ]);

        return $query->queryScalar();
    }

    public function EditarCausaPenal($CausaPenal, $IdCaso)
    {
        $sql = 'CALL dsp_modificar_causapenal_caso( :token, :idCaso, :idCausaPenalCaso, :estadoCausaPenal, :nroExpedienteCausaPenal, :radicacionCausaPenal, :comisaria )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCaso' => $IdCaso,
            ':idCausaPenalCaso' => $CausaPenal->IdCausaPenalCaso,
            ':estadoCausaPenal' => $CausaPenal->EstadoCausaPenal,
            ':nroExpedienteCausaPenal' => $CausaPenal->NroExpedienteCausaPenal,
            ':radicacionCausaPenal' => $CausaPenal->RadicacionCausaPenal,
            ':comisaria' => $CausaPenal->Comisaria
        ]);

        return $query->queryScalar();
    }
    
    public function MoverMultimedia($IdMultimedia, $IdCarpetaCaso)
    {
        $sql = 'CALL dsp_mover_multimedia_caso( :idMultimedia, :idCarpetaCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idMultimedia' => $IdMultimedia,
            ':idCarpetaCaso' => $IdCarpetaCaso
        ]);
        
        return $query->queryScalar();
    }
    
    public function AltaCarpeta($IdCaso, $Nombre)
    {
        $sql = 'CALL dsp_alta_carpeta_caso( :token, :idCaso, :nombre )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCaso' => $IdCaso,
            ':nombre' => $Nombre
        ]);
        
        return $query->queryScalar();
    }
    
    public function EditarCarpeta($IdCarpetaCaso, $IdCaso, $Nombre)
    {
        $sql = 'CALL dsp_editar_carpeta_caso( :token, :idCaso, :idCarpetaCaso, :nombre )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCaso' => $IdCaso,
            ':idCarpetaCaso' => $IdCarpetaCaso,
            ':nombre' => $Nombre
        ]);
        
        return $query->queryScalar();
    }
    
    public function BorrarCarpeta($IdCarpetaCaso)
    {
        $sql = 'CALL dsp_eliminar_carpeta_caso( :token, :idCarpetaCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCarpetaCaso' => $IdCarpetaCaso
        ]);
        
        return $query->queryScalar();
    }
    
    public function ListarCarpetas($IdCaso)
    {
        $sql = 'CALL dsp_listar_carpetas_caso( :idCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $IdCaso
        ]);
        
        return $query->queryAll();
    }
    
    public function ListarEtiquetas($IdEstudio)
    {
        $sql = 'CALL dsp_listar_etiquetas_estudio( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio
        ]);
        
        return $query->queryAll();
    }
    
    public function AltaEtiqueta($IdCaso, $IdEstudio, $Etiqueta)
    {
        $sql = 'CALL dsp_alta_etiqueta_caso( :token, :idEstudio, :etiqueta, :idCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCaso' => $IdCaso,
            ':idEstudio' => $IdEstudio,
            ':etiqueta' => $Etiqueta
        ]);
        
        return $query->queryScalar();
    }
    
    public function BorrarEtiqueta($IdEtiquetaCaso)
    {
        $sql = 'CALL dsp_eliminar_etiqueta_caso( :token, :idEtiquetaCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idEtiquetaCaso' => $IdEtiquetaCaso
        ]);
        
        return $query->queryScalar();
    }
    
    public function AltaRecordatorio($IdCaso, $FechaLimite, $Frecuencia)
    {
        $sql = 'CALL dsp_alta_recordatorio_doc( :idCaso, :fechaLimite, :frecuencia )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $IdCaso,
            ':fechaLimite' => $FechaLimite,
            ':frecuencia' => $Frecuencia
        ]);
        
        return $query->queryScalar();
    }
}
