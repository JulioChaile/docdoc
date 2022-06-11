<?php
namespace common\models;

use Yii;
use yii\base\Model;

class GestorCasosPendientes extends Model
{
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_caso_pendiente( :token, :idEstudio, :nombres, :apellidos, :telefono, :domicilio, :prioridad, :idEstadoCasoPendiente, :idOrigen, :documento, :latitud, :longitud, :lesion )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idEstudio' => $Objeto->IdEstudio,
            ':nombres' => $Objeto->Nombres,
            ':apellidos' => $Objeto->Apellidos,
            ':telefono' => $Objeto->Telefono,
            ':domicilio' => $Objeto->Domicilio,
            ':prioridad' => $Objeto->Prioridad,
            ':idEstadoCasoPendiente' => $Objeto->IdEstadoCasoPendiente,
            ':idOrigen' => $Objeto->IdOrigen,
            ':documento' => $Objeto->Documento,
            ':latitud' => $Objeto->Latitud,
            ':longitud' => $Objeto->Longitud,
            ':lesion' => $Objeto->Lesion
        ]);
        
        return $query->queryScalar();
    }

    public function AltaActivo($Objeto)
    {
        $sql = 'CALL dsp_alta_caso_pendiente_activo( :token, :idCasoPendiente, :idEstudio, :nombres, :apellidos, :telefono, :domicilio, :documento )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCasoPendiente' => $Objeto->IdCasoPendiente,
            ':idEstudio' => $Objeto->IdEstudio,
            ':nombres' => $Objeto->Nombres,
            ':apellidos' => $Objeto->Apellidos,
            ':telefono' => $Objeto->Telefono,
            ':domicilio' => $Objeto->Domicilio,
            ':documento' => $Objeto->Documento,
        ]);
        
        return $query->queryScalar();
    }

    public function Editar($Objeto)
    {
        $sql = 'CALL dsp_modificar_caso_pendiente( :token, :idEstudio, :idCasoPendiente, :nombres, :apellidos, :telefono, :domicilio, :prioridad, :idEstadoCasoPendiente, :idOrigen, :documento, :latitud, :longitud, :lesion, :visitado )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idEstudio' => $Objeto->IdEstudio,
            ':idCasoPendiente' => $Objeto->IdCasoPendiente,
            ':nombres' => $Objeto->Nombres,
            ':apellidos' => $Objeto->Apellidos,
            ':telefono' => $Objeto->Telefono,
            ':domicilio' => $Objeto->Domicilio,
            ':prioridad' => $Objeto->Prioridad,
            ':idEstadoCasoPendiente' => $Objeto->IdEstadoCasoPendiente,
            ':idOrigen' => $Objeto->IdOrigen,
            ':documento' => $Objeto->Documento,
            ':latitud' => $Objeto->Latitud,
            ':longitud' => $Objeto->Longitud,
            ':lesion' => $Objeto->Lesion,
            ':visitado' => $Objeto->Visitado
        ]);
        
        return $query->queryScalar();

        // return $query->getRawSql();
    }

    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_caso_pendiente( :token, :idCasoPendiente, :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idEstudio' => $Objeto->IdEstudio,
            ':idCasoPendiente' => $Objeto->IdCasoPendiente,
        ]);
        
        return $query->queryScalar();
    }

    public function ListarEstados()
    {
        $sql = 'CALL dsp_listar_estados_casos_pendientes()';
        
        $query = Yii::$app->db->createCommand($sql);
        
        return $query->queryAll();
    }

    public function Buscar($IdEstudio = 0, $Cadena = '', $Documento = '', $Telefono = '', $Offset = 0, $Limit = 30, $Visitado = '',
                            $Estados = '[]', $FechasAlta = '[]', $FechasVisitado = '[]', $Cadete = 0, $Finalizado = 'N')
    {
        $sql = 'CALL dsp_buscar_casos_pendientes( :cadena, :idEstudio, :documento, :telefono, :offset, :limit, :visitado,' .
                ':estados, :fechasAlta, :fechasVisitado, :cadete, :finalizado)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
            ':cadena' => $Cadena,
            ':documento' => $Documento,
            ':telefono' => $Telefono,
            ':offset' => $Offset,
            ':limit' => $Limit,
            ':visitado' => $Visitado,
            ':estados' => $Estados ?? '[]',
            ':fechasAlta' => $FechasAlta ?? '[]',
            ':fechasVisitado' => $FechasVisitado ?? '[]',
            ':cadete' => $Cadete ?? 0,
            ':finalizado' => $Finalizado
        ]);

        $result = $query->queryAll();
        
        return $result;
    }

    public function UbicacionesCercanas ($Latitud, $Longitud, $Distancia = 0.5)
    {
        $sql = 'CALL dsp_buscar_ubicaciones_cercanas( :latitud, :longitud, :distancia)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':latitud' => $Latitud,
            ':longitud' => $Longitud,
            ':distancia' => $Distancia
        ]);

        $result = $query->queryAll();
        
        return $result;
    }

    public function UbicacionesSimilares ($Domicilio)
    {
        $sql = 'CALL dsp_buscar_ubicaciones_similares( :domicilio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':domicilio' => $Domicilio
        ]);

        $result = $query->queryAll();
        
        return $result;
    }
}
