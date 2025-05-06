
<?php
$PageSecurity = 2;
include('includes/session.inc');
?>
<?php
// Ruta del archivo que deseas descargar
$SelID = $_GET['ID']; // Obtener el ID del archivo desde la URL
$sql = "SELECT * FROM CG_archivos WHERE id_archivo = $SelID"; // Consulta para obtener el archivo
$Res = DB_query($sql, $db); // Ejecuta la consulta
$Row = DB_fetch_array($Res); // Obtiene el resultado de la consulta

// Asegúrate de que se encontró un resultado
if ($Row) {
    $nombre_file = $Row['nombre'];
    $folder_tipo = $Row['tipo'];

    // Verificar si el tipo no es 'xml' ni 'pdf'
    if ($folder_tipo != 'xml' && $folder_tipo != 'pdf') {
        $folder_tipo = 'otros'; // Asignar la carpeta 'otros' si no es xml o pdf
    }

    // Define la ruta del archivo
    $file_path = $_SERVER['DOCUMENT_ROOT'] . "/companies/iatiqroc_Plastek/gastos/$folder_tipo/$nombre_file";

    // Verifica si el archivo existe
    if (file_exists($file_path)) {
        // Envía las cabeceras HTTP necesarias para forzar la descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf'); // Cambia esto a 'application/pdf' si es un PDF
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        // Limpia el búfer de salida
        ob_clean();
        flush();

        // Lee el archivo y lo envía al navegador
        readfile($file_path);
        exit; // Finaliza el script después de la descarga
    } else {
        echo "El archivo no existe $folder."; // Mensaje si el archivo no se encuentra
    }
} else {
    echo "No se encontró el archivo en la base de datos."; // Mensaje si no se encuentra en la base de datos
}
?>

