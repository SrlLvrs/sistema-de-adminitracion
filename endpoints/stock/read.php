<?php
// VISTA: PRODUCCION.VUE
// Estos encabezados permiten el acceso a la API desde cualquier origen y especifican que el contenido de la respuesta será JSON.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Se incluye el archivo que contiene la clase para la conexión a la base de datos.
include_once '../config/db.php';

// Se instancia la base de datos y se obtiene la conexión.
$database = new Database();
$db = $database->getConnection();

// GET stock de la semana
$query = "  SELECT 
                IDProducto AS idproducto,
                CONCAT('[', 
                    IFNULL(MAX(CASE WHEN DAYOFWEEK(Fecha) = 2 THEN Stock END), 0), ',',  -- Lunes
                    IFNULL(MAX(CASE WHEN DAYOFWEEK(Fecha) = 3 THEN Stock END), 0), ',',  -- Martes
                    IFNULL(MAX(CASE WHEN DAYOFWEEK(Fecha) = 4 THEN Stock END), 0), ',',  -- Miércoles
                    IFNULL(MAX(CASE WHEN DAYOFWEEK(Fecha) = 5 THEN Stock END), 0), ',',  -- Jueves
                    IFNULL(MAX(CASE WHEN DAYOFWEEK(Fecha) = 6 THEN Stock END), 0), ',',  -- Viernes
                    IFNULL(MAX(CASE WHEN DAYOFWEEK(Fecha) = 7 THEN Stock END), 0), ',',  -- Sábado
                    IFNULL(MAX(CASE WHEN DAYOFWEEK(Fecha) = 1 THEN Stock END), 0)        -- Domingo
                , ']') AS stock
            FROM stock
            WHERE YEARWEEK(Fecha, 1) = YEARWEEK(CURDATE(), 1)  -- Filtrar por semana actual
            GROUP BY IDProducto";

$stmt = $db->prepare($query);

// Se ejecuta la consulta.
$stmt->execute();

// Array para almacenar resultados
$resultados = array();

// Recorrer todos los registros obtenidos
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $resultados[] = $row;
}

// Verifica si se encontraron registros
if (count($resultados) > 0) {
    http_response_code(200);
    echo json_encode($resultados);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No hay stock"));
    exit();
}
