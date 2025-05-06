<?php

$PageSecurity = 15; // Establece el nivel de seguridad de la página
$App = 'CG'; // Define la aplicación como 'CG' (Catálogo Centro de costos)
$Mod = 'Empleados'; // Define el módulo como 'Empleados'
include('includes/session.inc'); // Incluye el archivo de sesión para gestionar la autenticación y datos de sesión
$title = 'Cargar Otros Archivos del Gasto'; // Establece el título de la página
include('includes/header.inc'); // Incluye el archivo del encabezado de la página

// Abre un formulario HTML con el método POST y la codificación para archivos adjuntos.
echo '<form method="post" 
            enctype="multipart/form-data"
            action="CG_001_001of.php?ID='.$_GET['ID'].'&A='.$_GET['A'].'&ID_ob='.$_GET['ID_ob'].'&P='.$_GET['P'].'" />';

echo '<input type=hidden name=FormID value="'.$_SESSION['FormID'].'" />';

$Msg   = ''; // Inicializa la variable de mensaje como una cadena vacía
$vSaleM = 'IN'; // inicia la variable vsaleM con txt IN
$MsgC = 'green';
$SelID = $_GET['ID'];
$SelID_ob = $_GET['ID_ob'];
$P = $_GET['P'];
$clave_usuario = $_SESSION['CustomerID'];
$nombre_usuario = $_SESSION['UserID'];

$mensaje = '';
/* Parte 2 */
// al click en validar revisar si el archivo es menor a 100,0000 o el archivo es diferente a texto o xml
if ($_POST['Cargar'] == 'Cargar') {
  //condiciones 
  if($_FILES[FileOtro][name]==''){
    prnMsgV20('Debe indicar el nombre del archivo ...','error'); 
      $mensaje = 'Error'; 
  }

  if ($mensaje == '') {
    $file_old_name = $_FILES['FileOtro']['name'];
    $file_extension = pathinfo($file_old_name, PATHINFO_EXTENSION);
    // Genera una sesión única usando SHA-1 y un identificador único
    $Sesion = sha1(uniqid(mt_rand(), true));
    $nombre =  $SelID . '-'. $SelID_ob . '-' . $P . '-' . $nombre_usuario;

    
    // Define el nombre del archivo XML basado en la sesión generada
    $file_name_otro = $_SESSION['path_gastos'] . '/otros/tkt-'. $nombre. '.'. $file_extension;

    if (!move_uploaded_file($_FILES['FileOtro']['tmp_name'], $file_name_otro)) {
      // Si ocurre un error al mover el archivo, muestra un mensaje de error
      echo 'Error: ' . $_FILES['FileOtro']['error'];
      prnMsgV20('No se ha cargado el archivo PDF... ' . $file_name_otro, 'error');
      $mensaje = 'Error'; // Marca el mensaje como un error
    }
    
    // // Imprime un campo oculto en el formulario con el nombre del archivo
    // echo '<input type="hidden" name="FileName" value="' . $file_namex . '" />';

    
    // Abre el archivo XML recién subido en modo lectura
    // $filexml = fopen($file_namex, "r");
    
    // Lee el contenido del archivo
    // $contenido = fread($filexml, filesize($file_namex));

    //obtener el nombre de los archivos
    // $nombre_archivo_xml = basename($file_namex);
    // $nombre_archivo_pdf = basename($file_namepdf);
    $nombre_archivo_otro = basename($file_name_otro);

    //obtener las extensiones
    // $extension_xml = strtolower(pathinfo($nombre_archivo_xml, PATHINFO_EXTENSION));
    // $extension_pdf = strtolower(pathinfo($nombre_archivo_pdf, PATHINFO_EXTENSION));
    $extension_otro = strtolower(pathinfo($nombre_archivo_otro, PATHINFO_EXTENSION));

    
    //insertar el archivo xml en la base de datos
    $sql_up = "INSERT INTO CG_archivos (id_comprobacion, nombre, tipo)
                VALUES ($SelID, '$nombre_archivo_otro', 'tkt')";
    $res_up = DB_query($sql_up,$db);

    $sql_actu = "UPDATE CG_comprobacion 
                  SET  
                      estatus = 'Gasto sin Factura'
                      WHERE id_comprobacion = '$SelID'";
    $res_actu = DB_query($sql_actu, $db); // Execute the query

    if ($res_actu) {
      prnMsgV20('Se ha cargado el archivo correctamente...', 'success');
    }  else {
      prnMsgV20('Error al cargar el archivo ...', 'error');
  }
}
}
/* fin parte 2 */



////pantalla principal
if ($_POST['Cargar'] == '') {
    echo '<div class="container" style="margin-top:80px"> 
    <div class="row">
      <div class="col-sm-12">
        <div class="card text-left">
          <div class="card-body">
            <h4>Carga de Archivos</h4>';
            if ($Msg <> '')
                echo '<p style="text-align: center;font-family:Arial;font-size:16px;color:' . $MsgC . '">' . $Msg . '</p>';  
  echo '      <br>';
  echo '      <br>';
  echo '      <div class="slic-form-group row">
              <div class="col-sm-2"></div>
              <label for="FileOtro" class="col-sm-2 col-slic-label">Archivo: </label>
              <div class="col-sm-6">
                <input 
                    type="file"
                    class="form-control-file border"
                    id="FileOtro"
                    name="FileOtro">
              </div>                      
              <div class="col-sm-2"></div>
            </div>';
  echo'<br>';
  echo '      <br>';
  echo '      <input 
              type="submit" 
              class="btn btn-primary" 
              name="Cargar" 
              value="Cargar"
          >';            
  echo '    </div>
        </div>
      </div>      
    </div>
  </div>';
  
      }
  
  
  
  include('includes/footer.php');
  ?>