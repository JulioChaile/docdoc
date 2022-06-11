<?php
namespace common\models;

use Yii;
use yii\base\Model;

class GestorPlantillas extends Model
{
    /**
     * Permite dar de alta una plantilla
     * Devuelve 'OK' concatenado con el IdPlantilla o un mensaje de error
     */
    public function AltaPlantilla($IdEstudio, $Nombre, $Plantilla, $Actores, $Demandados, $IdCarpetaPadre)
    {
        $sql = 'CALL dsp_alta_plantilla_estudio(:idEstudio, :nombre, :plantilla, :actores, :demandados, :idCarpetaPadre)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
            ':nombre' => $Nombre,
            ':plantilla' => $Plantilla,
            ':actores' => $Actores,
            ':demandados' => $Demandados,
            'idCarpetaPadre' => $IdCarpetaPadre
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite dar de alta una plantilla
     * Devuelve 'OK' concatenado con el IdPlantilla o un mensaje de error
     */
    public function AltaCarpeta($IdEstudio, $Nombre, $IdCarpetaPadre)
    {
        $sql = 'CALL dsp_alta_carpeta_plantilla_estudio(:idEstudio, :nombre, :idCarpetaPadre)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
            ':nombre' => $Nombre,
            'idCarpetaPadre' => $IdCarpetaPadre
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite listar todas las carpetas de plantillas del estudio.
     * Si IdEstudio es null, lista todos.
     */
    public function ListarCarpetas($IdEstudio = null)
    {
        $sql = "CALL dsp_listar_carpetas_plantillas_estudio( :idEstudio )";

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio
        ]);

        return $query->queryAll();
    }

    /**
     * Permite listar todas las plantillas del estudio.
     * Si IdEstudio es null, lista todos.
     */
    public function ListarPlantillas($IdEstudio = null)
    {
        $sql = "CALL dsp_listar_plantillas_estudio( :idEstudio )";

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio
        ]);

        return $query->queryAll();
    }

    /**
     * Permite eliminar una plantilla.
     */
    public function EliminarPlantilla($IdPlantilla)
    {
        $sql = "CALL dsp_eliminar_plantilla_estudio( :idPlantilla )";

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idPlantilla' => $IdPlantilla
        ]);

        return $query->queryScalar();
    }

    /**
     * Permite eliminar una plantilla.
     */
    public function EliminarCarpeta($IdCarpetaPlantilla)
    {
        $sql = "CALL dsp_eliminar_carpeta_plantilla_estudio( :idCarpetaPlantilla )";

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCarpetaPlantilla' => $IdCarpetaPlantilla
        ]);

        return $query->queryScalar();
    }

    /**
     * Permite editar una plantilla
     * Devuelve 'OK' o un mensaje de error
     */
    public function ModificarPlantilla($IdEstudio, $IdPlantilla, $Nombre, $Plantilla, $Actores, $Demandados)
    {
        $sql = 'CALL dsp_modificar_plantilla_estudio( :idEstudio, :idPlantilla, :nombre, :plantilla, :actores, :demandados )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
            ':idPlantilla' => $IdPlantilla,
            ':nombre' => $Nombre,
            ':plantilla' => $Plantilla,
            ':actores' => $Actores,
            ':demandados' => $Demandados
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite editar una plantilla
     * Devuelve 'OK' o un mensaje de error
     */
    public function ModificarCarpetaPlantilla($IdEstudio, $IdCarpetaPlantilla, $Nombre)
    {
        $sql = 'CALL dsp_modificar_carpeta_plantilla_estudio( :idEstudio, :idCarpetaPlantilla, :nombre )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
            ':idCarpetaPlantilla' => $IdCarpetaPlantilla,
            ':nombre' => $Nombre
        ]);
        
        return $query->queryScalar();
    }
}
