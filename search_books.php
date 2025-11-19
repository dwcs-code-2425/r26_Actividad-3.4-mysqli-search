<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
<div class="container-fluid">
    
        <h1>Búsqueda de libros</h1>
    
        <form method="GET">
            <div class="mb-3">
                <label class="form-label" for="busqueda">Introduzca los términos de búsqueda: </label>
                <input type="search" class="form-control" name="busqueda" id="busqueda" required>
            </div>
            <button type="submit"  class="btn btn-primary">Buscar</button>
        </form>
</div>
</body>

</html>
<?php
if (isset($_GET["busqueda"])) {
    $terminos_busqueda = $_GET["busqueda"];
    if (trim($terminos_busqueda) !== "") {

        require_once "connection.php";

        try {
            $con =  getConnection();

            //En la bd bookdb no importan mayúsculas/minúsculas porque está usando collation caseinsensitive, pero no está demás que nuestro código no dependa de la collation de la base de datos
            $stmt = $con->prepare("select title as resultado from books where UPPER(title) like ?
                union 
                select Concat(first_name,' ', last_name)
                 as resultado from authors where first_name like ?;");

            $filtro = "%" . strtoupper($terminos_busqueda) . "%";
            $stmt->bind_param("ss", $filtro, $filtro);
            $stmt->execute();

            $resultado = $stmt->get_result();

            $counter = 0;
            //fetch_assoc devuelve:
            // array asoc
            //null si no hay más filas
            //false si falla algo
            while (($row = $resultado->fetch_assoc())) {
                $counter++;
                if ($counter == 1) {
                    echo "<ol>";
                }
                echo "<li>" . $row["resultado"] . "</li>";
            }
            if ($counter > 0) {
                echo "</ol>";
            }
            if ($counter == 0) {
                echo "<p>No se han encontrado resultados</p>";
            }
        } catch (Exception $e) {
            error_log("Ha ocurrido una una excepción: " . $e->getMessage());
            echo "<p>Ha ocurrido un error inesperado: </p>";
        } finally {
            //Cerramos los recursos
            //Open non-persistent MySQL connections and result sets are automatically closed when their objects are destroyed. Explicitly closing open connections and freeing result sets is optional.
            if (isset($con)) {
                $con->close();
            }
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($resultado)) {
                $resultado->free();
            }
        }
    } else {
        echo "<p> Introduzca una cadena no vacía </p>";
    }
}

?>