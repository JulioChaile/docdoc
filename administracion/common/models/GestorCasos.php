<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\components\FechaHelper;

/**
 * @version 1.0
 * @created 25-May-2018 20:31:43
 */
class GestorCasos extends Model
{
    
    /**
     * Permite buscar casos filtr�ndolos por estudio en pIdEstudio (obligatorio),  por
     * abogado en pIdUsuario (opcional. 0 para todos), por nominaci�n en pIdNominacion
     * (opcional. 0 para todas.), por sub estado de caso en pIdSubEstadoCaso (opcional.
     * 0 para todos), por fecha de alta entre pFechaInicio y pFechaFin, por tipo en
     * pTipo = E: Expediente - C: Caratula - R: Carpeta - T: Todos, por una cadena de
     * b�squeda e indicando si el caso proviene de DocDoc en pEsDocDoc = [S|N|T].
     * Ordena por FechaAlta.
     * buscar_avanzado_casos
     *
     * @param IdEstudio    0 para todos.
     * @param IdUsuario    0 para todos.
     * @param IdNominacion    0 para todos.
     * @param IdSubEstadoCaso    0 para todos.
     * @param FechaInicio
     * @param FechaFin
     * @param Tipo    E: Expediente - C: Caratula -  R: Carpeta - T: Todos
     * @param Cadena
     * @param EsDocDoc    S: Si - N: No - T: Todos.
     * @param IdTipoCaso    0 para todos
     */
    public function BuscarAvanzado($IdEstudio = 0, $IdUsuario = 0, $IdNominacion = 0, $IdEstadoCaso = 0, $FechaInicio = null, $FechaFin = null, $Tipo = 'T', $Cadena = '', $IdTipoCaso = 0, $Estado = 'T')
    {
        $sql = 'CALL dsp_buscar_avanzado_casos( :idEstudio, :idUsuario, :idNominacion,'
                . ' :idEstadoCaso, :fechaInicio, :fechaFin, :tipo, :cadena,'
                . ' :idTipoCaso, :estado )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
            ':idUsuario' => $IdUsuario,
            ':idNominacion' => $IdNominacion,
            ':idEstadoCaso' => $IdEstadoCaso,
            ':fechaInicio' => $FechaInicio,
            ':fechaFin' => $FechaFin,
            ':tipo' => $Tipo,
            ':cadena' => $Cadena,
            ':idTipoCaso' => $IdTipoCaso,
            ':estado' => $Estado
        ]);
        
        return $query->queryAll();
    }

    public function Buscar($IdEstudio = 0, $IdUsuario = 0, $Tipo = 'T', $Cadena = '', $Min = 0, $Max = 0, $Offset = 0, $Orden = 'fecha', $Limit = 50)
    {
        $sql = 'CALL dsp_buscar_casos( :idEstudio, :idUsuario,'
                . ' :tipo, :cadena, :min, :max, :offset, :orden, :limit )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
            ':idUsuario' => $IdUsuario,
            ':tipo' => $Tipo,
            ':cadena' => $Cadena,
            ':min' => $Min,
            ':max' => $Max,
            ':offset' => $Offset,
            ':orden' => $Orden,
            ':limit' => $Limit
        ]);
        
        return $query->queryAll();
    }

    public function BuscarJudiciales($IdEstudio, $IdUsuario, $IdEstadoAmbitoGestion)
    {
        $sql = 'CALL dsp_buscar_casos_judiciales( :idEstudio, :idUsuario, :idEstadoAmbitoGestion )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
            ':idUsuario' => $IdUsuario,
            ':idEstadoAmbitoGestion' => $IdEstadoAmbitoGestion
        ]);
        
        return $query->queryAll();
    }
    
    /**
     * Permite obtener una previsualizaci�n de los elementos a ser eliminados en un
     * caso. dsp_previsualizar_borrado_caso
     *
     * @param IdCaso
     */
    public function PrevisualizarBorrado($IdCaso)
    {
        $sql = 'CALL dsp_previsualizar_borrado_caso( :token, :idCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCaso' => $IdCaso,
        ]);
        
        return $query->queryOne();
    }
    
    public function AltaJudicialesC($Cantidad, $IdEstadoAmbitoGestion, $IdUsuario)
    {
        $sql = 'CALL dsp_alta_judiciales_c( :idEstadoAmbitoGestion, :idUsuario, :cantidad )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cantidad' => $Cantidad,
            ':idEstadoAmbitoGestion' => $IdEstadoAmbitoGestion,
            ':idUsuario' => $IdUsuario
        ]);
        
        return $query->queryScalar();
    }
    
    public function AltaJudicialesI($IdJudicialesC, $IdCaso)
    {
        $sql = 'CALL dsp_alta_judiciales_i( :idJudicialesC, :idCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idJudicialesC' => $IdJudicialesC,
            ':idCaso' => $IdCaso
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite dar de alta un caso controlando que el usuario que ejecuta el
     * procedimiento pertenezca y est� activo en alg�n estudio. El caso se da de alta
     * junto con las personas relacionadas, siempre que existan en el par�metro
     * pPersonasCaso JSON. Si una persona indicada en el par�metro existe ya en el
     * estudio, se utiliza su Id para asociarla al caso. Si no existe, se la crea. El
     * criterio para determinar si una persona existe o no es, si es F�sica, su
     * documento debe existir en el sistema; si es Jur�dica, su CUIT debe existir en
     * el sistema. Si no se indica una car�tula, la misma se genera a partir del Tipo
     * de Caso y el nombre del actor principal, siguiendo el formato: Actor Principal
     * s/ Tipo de Caso. Devuelve OK + el id del caso creado o un mensaje de error en
     * Mensaje. dsp_alta_caso
     *
     * @param Objeto
     */
    public function Alta($Objeto, $DetalleOrigen)
    {
        $sql = 'CALL dsp_alta_caso( :token, :idJuzgado, :idNominacion, :idCompetencia, :idTipoCaso,'
                . ' :idEstadoCaso, :idOrigen, :caratula, :nroExpediente,'
                . ' :carpeta, :observaciones, :personasCaso, :idEstadoAmbitoGestion, :defiende, :detalleOrigen,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token ? Yii::$app->user->identity->Token : '',
            // ':token' => $Objeto->IdJuzgado == 12 && $Objeto->IdTipoCaso == 0 ? Yii::$app->user->identity->Token : '',
            // ':token' => false ? Yii::$app->user->identity->Token : '',
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idJuzgado' => $Objeto->IdJuzgado == '' ? null : $Objeto->IdJuzgado,
            ':idNominacion' => $Objeto->IdNominacion == '' ? null : $Objeto->IdNominacion,
            ':idCompetencia' => $Objeto->IdCompetencia,
            ':idTipoCaso' => $Objeto->IdTipoCaso,
            ':idEstadoCaso' => $Objeto->IdEstadoCaso == '' ? null : $Objeto->IdEstadoCaso,
            ':idOrigen' => $Objeto->IdOrigen == '' ? null : $Objeto->IdOrigen,
            ':caratula' => $Objeto->Caratula,
            ':nroExpediente' => $Objeto->NroExpediente,
            ':carpeta' => $Objeto->Carpeta,
            ':observaciones' => $Objeto->Observaciones,
            ':idEstadoAmbitoGestion' => $Objeto->IdEstadoAmbitoGestion == '' ? null : $Objeto->IdEstadoAmbitoGestion,
            ':defiende' => $Objeto->Defiende == '' ? null : $Objeto->Defiende,
            ':personasCaso' => json_encode($Objeto->PersonasCaso),
            ':detalleOrigen' => $DetalleOrigen
        ]);
        
        return $query->queryScalar();
    }

    /** Alta para wordpress */
    public function WordpressAlta($Objeto, $token)
    {
        $sql = 'CALL dsp_alta_caso( :token, :idJuzgado, :idNominacion, :idCompetencia, :idTipoCaso,'
                . ' :idEstadoCaso, :idOrigen, :caratula, :nroExpediente,'
                . ' :carpeta, :observaciones, :personasCaso, :idEstadoAmbitoGestion,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => $token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idJuzgado' => $Objeto->IdJuzgado == '' ? null : $Objeto->IdJuzgado,
            ':idNominacion' => $Objeto->IdNominacion == '' ? null : $Objeto->IdNominacion,
            ':idCompetencia' => $Objeto->IdCompetencia,
            ':idTipoCaso' => $Objeto->IdTipoCaso,
            ':idEstadoCaso' => $Objeto->IdEstadoCaso == '' ? null : $Objeto->IdEstadoCaso,
            ':idOrigen' => $Objeto->IdOrigen == '' ? null : $Objeto->IdOrigen,
            ':caratula' => $Objeto->Caratula,
            ':nroExpediente' => $Objeto->NroExpediente,
            ':carpeta' => $Objeto->Carpeta,
            ':observaciones' => $Objeto->Observaciones,
            ':idEstadoAmbitoGestion' => $Objeto->IdEstadoAmbitoGestion == '' ? null : $Objeto->IdEstadoAmbitoGestion,
            ':personasCaso' => json_encode($Objeto->PersonasCaso),
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Procedimiento que eliminar un caso junto con todas sus MultimediaMovimiento,
     * MovimientosCaso, UsuariosCaso, PersonasCaso. Devuelve OK un mensaje de error en
     * Mensaje. dsp_borrar_caso
     *
     * @param Objeto
     */
    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_caso( :token, :idCaso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCaso' => $Objeto->IdCaso
        ]);
        
        return $query->queryScalar();
    }
    
    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_caso( :token, :idCaso, :idJuzgado, :idNominacion, :idCompetencia, :idEstadoAmbitoGestion,'
                . ' :idEstadoCaso, :caratula, :nroExpediente, :carpeta, :idOrigen, :idTipoCaso, :fechaEstado,'
                . ' :observaciones, :idCasoEstudio, :defiende, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCaso' => $Objeto->IdCaso,
            ':idCasoEstudio' => $Objeto->IdCasoEstudio,
            ':idJuzgado' => $Objeto->IdJuzgado == '' ? null : $Objeto->IdJuzgado,
            ':idNominacion' => $Objeto->IdNominacion == '' ? null : $Objeto->IdNominacion,
            ':idCompetencia' => $Objeto->IdCompetencia,
            ':idEstadoCaso' => $Objeto->IdEstadoCaso == '' ? null : $Objeto->IdEstadoCaso,
            ':idEstadoAmbitoGestion' => $Objeto->IdEstadoAmbitoGestion == '' ? null : $Objeto->IdEstadoAmbitoGestion,
            ':caratula' => $Objeto->Caratula,
            ':nroExpediente' => $Objeto->NroExpediente,
            ':carpeta' => $Objeto->Carpeta,
            ':idOrigen' => $Objeto->IdOrigen,
            ':observaciones' => $Objeto->Observaciones,
            ':idTipoCaso' => $Objeto->IdTipoCaso,
            ':fechaEstado' => FechaHelper::formatearDateMysql($Objeto->FechaEstado),
            ':defiende' => $Objeto->Defiende == '' ? null : $Objeto->Defiende
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite buscar movimientos de caso filtr�ndolos por multiples cadenas de
     * busqueda y por fecha de inicio y fin. dsp_filtrar_movimientos_tribunales
     *
     * @param IdEstudio
     * @param Busqueda
     * @param FechaInicio
     * @param FechaFin
     * @param Cantidad
     * @param Offset
     */
    public function FiltrarMovimientosTribunales($IdEstudio)
    {
        $sql = 'CALL dsp_filtrar_movimientos_tribunales( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio
        ]);
        
        return $query->queryAll();
    }
    public function MovimientosDelDia($IdEstudio)
    {
        $sql = 'CALL dsp_movimientos_del_dia( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio
        ]);
        
        return $query->queryAll();
    }
    /**
     * Permite modificar el permiso de un usuario sobre un caso, da de alta el usuario caso si no existe.
     * Controla que el usuario que está intentando realizar esta acción tenga permiso de administrador sobre
     * el caso.
     * Devuelve OK + el id del UsuarioCaso creado o un mensaje de error en Mensaje.
     * dsp_modifica_permiso_usuario_caso
     *
     * @param IdUsuario
     * @param IdCaso
     * @param Permiso
     */
    public function ModificarPermisoUsuarioCaso($IdUsuario, $IdCaso, $Permiso)
    {
        $sql = 'CALL dsp_modifica_permiso_usuario_caso( :token, :idusuario, :idcaso, :permiso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idusuario' => $IdUsuario,
            ':idcaso' => $IdCaso,
            ':permiso' => $Permiso
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite eliminar un usuario de un caso.
     * Controla que el usuario que está intentando realizar esta acción tenga permiso de administrador sobre
     * el caso.
     * Devuelve OK o un mensaje de error en Mensaje.
     * dsp_modifica_permiso_usuario_caso
     *
     * @param IdUsuario
     * @param IdCaso
     */
    public function EliminarUsuarioCaso($IdUsuario, $IdCaso)
    {
        $sql = 'CALL dsp_eliminar_usuario_caso( :token, :idusuario, :idcaso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idusuario' => $IdUsuario,
            ':idcaso' => $IdCaso,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite registrar una compartición de caso.
     */
    public function RegistrarComparticion($params, $IdComparticion = null)
    {
        $sql = 'CALL dsp_registrar_comparticion_caso( :idComparticion, :idcaso, :email, :tokenMensaje, :fechaEnviado,'
        . ':fechaRecibido, :idUsuarioOrigen, :idUsuarioDestino, :idEstudioDestino , :idEstudioOrigen )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idComparticion' => $IdComparticion,
            ':idcaso' => $params['IdCaso'],
            ':email' => $params['Email'],
            ':tokenMensaje' => $params['Token'],
            ':fechaEnviado' => $params['FechaEnviado'],
            ':fechaRecibido' => $params['FechaRecibido'],
            ':idUsuarioOrigen' => $params['IdUsuarioOrigen'],
            ':idUsuarioDestino' => $params['IdUsuarioDestino'],
            ':idEstudioDestino' => $params['IdEstudioDestino'],
            ':idEstudioOrigen' => $params['IdEstudioOrigen']
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite obtener la cantidad de casos agrupados por TipoCaso
     */
    public function NumeroCasos($IdEstudio)
    {
        $sql = 'CALL dsp_dame_numero_casos( :idEstudio )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':idEstudio' => $IdEstudio
        ]);

        return $query->queryAll();
    }

    public function EliminarComparticion($IdCaso, $IdEstudio)
    {
        $sql = 'CALL dsp_eliminar_comparticion( :token, :idCaso, :idEstudio )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCaso' => $IdCaso,
            ':idEstudio' => $IdEstudio
        ]);

        return $query->queryScalar();
    }

    public function OpcionesParametros()
    {
        $sql = 'CALL dsp_dame_opciones_parametros_caso()';

        $query = Yii::$app->db->createCommand($sql);

        return $query->queryAll();
    }

    public function EditarOpcionesParametros($IdOpcionesParametrosCaso, $Opciones)
    {
        $sql = 'CALL dsp_editar_opciones_parametros_caso( :idOpcionesParametrosCaso, :opciones )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':idOpcionesParametrosCaso' => $IdOpcionesParametrosCaso,
            ':opciones' => $Opciones
        ]);

        return $query->queryScalar();
    }

    public function BuscarComisaria($Cadena = '', $Offset = 0, $Limit = 10)
    {
        $sql = 'CALL dsp_buscar_comisarias( :cadena, :limit, :offset )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
            ':limit' => $Limit,
            ':offset' => $Offset
        ]);

        $result = $query->queryAll();
        
        return $result;
    }

    public function BuscarCentroMedico($Cadena = '', $Offset = 0, $Limit = 10)
    {
        $sql = 'CALL dsp_buscar_centros_medicos( :cadena, :limit, :offset )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
            ':limit' => $Limit,
            ':offset' => $Offset
        ]);

        $result = $query->queryAll();
        
        return $result;
    }

    public function BuscarCiaSeguro($Cadena = '', $Offset = 0, $Limit = 10)
    {
        $sql = 'CALL dsp_buscar_cias_seguro( :cadena, :limit, :offset )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
            ':limit' => $Limit,
            ':offset' => $Offset
        ]);

        $result = $query->queryAll();
        
        return $result;
    }

    public function AltaComisaria($Comisaria, $Telefono = '', $Direccion = '')
    {
        $sql = 'CALL dsp_alta_comisaria( :comisaria, :telefono, :direccion )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':comisaria' => $Comisaria,
            ':telefono' => $Telefono,
            ':direccion' => $Direccion
        ]);

        $result = $query->queryScalar();
        
        return $result;
    }

    public function AltaCentroMedico($CentroMedico, $Telefono = '', $Direccion = '')
    {
        $sql = 'CALL dsp_alta_centro_medico( :centroMedico, :telefono, :direccion )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':centroMedico' => $CentroMedico,
            ':telefono' => $Telefono,
            ':direccion' => $Direccion
        ]);

        $result = $query->queryScalar();
        
        return $result;
    }

    public function AltaCiaSeguro($CiaSeguro, $Telefono = '', $Direccion = '')
    {
        $sql = 'CALL dsp_alta_cia_seguro( :ciaSeguro, :telefono, :direccion )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':ciaSeguro' => $CiaSeguro,
            ':telefono' => $Telefono,
            ':direccion' => $Direccion
        ]);

        $result = $query->queryScalar();
        
        return $result;
    }

    public function ModificarCiaSeguro($Objeto)
    {
        $sql = 'CALL dsp_modificar_cia_seguro( :idCiaSeguro, :ciaSeguro, :telefono, :direccion )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCiaSeguro' => $Objeto->IdCiaSeguro,
            ':ciaSeguro' => $Objeto->CiaSeguro,
            ':telefono' => $Objeto->Telefono,
            ':direccion' => $Objeto->Direccion
        ]);

        $result = $query->queryScalar();
        
        return $result;
    }
}
