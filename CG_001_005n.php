<?php
// Solicitud de viáticos
//MAMS
//10-08-2024

$PageSecurity = 15; // Establece el nivel de seguridad de la página
$App = 'CG'; // Define la aplicación como 'CG' (Catálogo Centro de costos)
$Mod = 'Empleados'; // Define el módulo como 'Empleados'
include('includes/session.inc'); // Incluye el archivo de sesión para gestionar la autenticación y datos de sesión
$title = 'Comprobación de Gastos'; // Establece el título de la página
include('includes/header.inc'); // Incluye el archivo del encabezado de la página

// Abre un formulario HTML con el método POST y la codificación para archivos adjuntos.
echo '<form method="post" 
            enctype="multipart/form-data"
            action="CG_001_005n.php?ID='.$_GET['ID'].'&A='.$_GET['A'].'&ID_o='.$_GET['ID_o'].'&emp_='.$_GET['emp_'].'&tot_='.$_GET['tot_'].'" />';
echo '<input type=hidden name=FormID value="'.$_SESSION['FormID'].'" />';

$Msg   = ''; // Inicializa la variable de mensaje como una cadena vacía
$vSaleM = 'IN'; // inicia la variable vsaleM con txt IN
$MsgC = 'green';
$SelID = $_GET['ID'];
$Sel_obj = $_GET['ID_o'];
$Sel_tot = $_GET['tot_'];
$usuario = $_SESSION['CustomerID'];
$nombre_usuario = $_SESSION['UserID'];
$emp_soli = $_GET['emp_'];

// Verifica si el parámetro 'A' en la URL es igual a 'CAprov', lo que indica que se debe aprovar la solicitud.
if ($_GET['A']=='CAprov') {
    
    // agregar a catalogo de anticipos
        $fecha_actual  = date('Y-m-d');
        $sql_catalogo = "INSERT INTO CG_anticipos (obj_gasto, empleado, valor_anticipo, fecha_aprobacion, status)
                         VALUES('".$Sel_obj."',
                                '".$emp_soli."',
                                '".$Sel_tot."',
                                '".$fecha_actual."',
                                'A')";
        
        $res  = DB_query($sql_catalogo,$db);

            //max id anticipos
        $sql_max_id_anticipo = "SELECT MAX(id_anticipo) AS max_value FROM CG_anticipos";
        $res_max_id_anticipo = DB_query($sql_max_id_anticipo, $db);
        $fila_max_id_anticipo = mysqli_fetch_assoc($res_max_id_anticipo);
        $max_id_anticipo = $fila_max_id_anticipo['max_value'];




        // agregar a catalogo de anticipos
        $sql_poliza = "INSERT INTO CG_polizas(obj_gasto, empleado, total_anticipo, id_anticipo, status)
                        VALUES('".$Sel_obj."',
                                '".$emp_soli."',
                                '".$Sel_tot."',
                                '".$max_id_anticipo."',
                                'Poliza Pendiente')";
        
        $res  = DB_query($sql_poliza,$db);



    // Construye una consulta SQL para eliminar el ID seleccionado.
        $sql_apro = "UPDATE CG_solicitud_viaticos
                    SET estatus = 'Aprobada' 
                    WHERE ID_viaticos = '$SelID'";
        $Res_apro = DB_query($sql_apro, $db);
        
        
        $sql_mail = "SELECT email FROM www_users WHERE userid = '".$emp_soli."'";
        $res_mail = DB_query($sql_mail, $db);
        $fila_mail = mysqli_fetch_assoc($res_mail);
        $mail_solicitante = $fila_mail['email'];

        // Verifica si el archivo especificado existe
        
        // Define el destinatario del correo
        // $destinatario = "mariosegovia1205@gmail.com";
        // Define el asunto del correo, incluyendo un valor del formulario
        $asunto = "Solicitud de viáticos ";

        // Define el cuerpo del correo
        $cuerpo = "Se ha aprobado su solicitud de viaticos para $Sel_obj en https://iatiqro.com.mx/";

        // Define el límite para el contenido del correo
        $boundary = "xyz123";
        // Configura las cabeceras del correo
        $headers = "From: plastek@iatiqro.com.mx\r\n"; // Remitente
        $headers .= "Reply-To: plastek@iatiqro.com.mx\r\n"; // Dirección de respuesta
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n"; // Tipo de contenido

        // Comienza a construir el cuerpo del mensaje
        $message = "--$boundary\r\n";
        $message .= "Content-Type: text/plain; charset=\"utf-8\"\r\n"; // Tipo de contenido para texto
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n"; // Método de codificación
        $message .= $cuerpo . "\r\n"; // Agrega el cuerpo del mensaje

        // Envía el correo electrónico
        mail($mail_solicitante, $asunto, $message, $headers);
        mail('carmen.martinez@plastekgroup.com', $asunto, $message, $headers);
        prnMsgV20('Se ha aprobado la solicitud: ...','success');
}

//Rechazar Solicitud
if ($_POST['BtnRej'] == 'Rechazar') {
  // Verifica que los datos no estén vacíos
  $coment = $_POST['coments'];
  
  if (!empty($coment)) {
      // Limpia el comentario para prevenir inyección SQL
      $coment = mysqli_real_escape_string($db, $coment);
      
      // Prepara la consulta SQL correctamente
      $sql_rec = "UPDATE CG_solicitud_viaticos
              SET estatus = 'Rechazado',
              comentarios = '$coment'
              WHERE ID_viaticos = '$SelID'";
      
      // Ejecuta la consulta
      $Res = DB_query($sql_rec, $db);
      $sql_mail = "SELECT email FROM www_users WHERE userid = '".$emp_soli."'";
        $res_mail = DB_query($sql_mail, $db);
        $fila_mail = mysqli_fetch_assoc($res_mail);
        $mail_solicitante = $fila_mail['email'];

        // Verifica si el archivo especificado existe
        
        // Define el destinatario del correo
        // $destinatario = "mariosegovia1205@gmail.com";
        // Define el asunto del correo, incluyendo un valor del formulario
        $asunto = "Solicitud de viáticos ";

        // Define el cuerpo del correo
        $cuerpo = "$nombre_usuario ha rechazado su solicitud de viaticos para $SelID,
                    revisas comentarios en plataforma https://iatiqro.com.mx/";

        // Define el límite para el contenido del correo
        $boundary = "xyz123";
        // Configura las cabeceras del correo
        $headers = "From: plastek@iatiqro.com.mx\r\n"; // Remitente
        $headers .= "Reply-To: plastek@iatiqro.com.mx\r\n"; // Dirección de respuesta
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n"; // Tipo de contenido

        // Comienza a construir el cuerpo del mensaje
        $message = "--$boundary\r\n";
        $message .= "Content-Type: text/plain; charset=\"utf-8\"\r\n"; // Tipo de contenido para texto
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n"; // Método de codificación
        $message .= $cuerpo . "\r\n"; // Agrega el cuerpo del mensaje

        // Envía el correo electrónico
        mail($mail_solicitante, $asunto, $message, $headers);

      if ($Res) {
          $Msg = 'Solicitud rechazada con éxito ';        
          $MsgC = 'green'; // El color rojo indica un error
          $vSaleM = 'OUT';


      } else {
          $Msg = 'Error en la preparación de la consulta. SQL: ';        
          $MsgC = 'red'; // El color rojo indica un error
      }
  } else {
      $Msg = 'Error en la preparación de la consulta. COMENTARIOS VACIOS: ';        
      $MsgC = 'red'; // El color rojo indica un error;
  }
}


//container principal onde va a estar la tabla 
echo '<div class="container" style="margin-top:80px">
    <div class="row">
        <div class="col-sm-12">
            <div class="card text-left">
                <div class="card-body">
                    <h4>Revisión de Solicitudes de Viáticos</h4>';
                    if ($Msg<>'')
                        echo '<p style="text-align: center;font-family:Arial;font-size:16px;color:'.$MsgC.'">'.$Msg.'</p>';
                            //<!-- Párrafo con estilo centrado, fuente Arial, tamaño 16px y color determinado por la variable $MsgC -->
                    echo '<div id="tablaviaticos_001_005"></div>';
                    //Contenedor vacío con el ID 'tablaCG_001_001' para cargar dinámicamente la tabla de centro de costos
echo'           </div>
            </div>
        </div>
    </div>
</div>';
////////////////////////////////



//// /////////MODAL APROVAR/////////
echo '<div class="modal fade" id="Aprovar" tabindex="-1" role="dialog">
<div class="modal-dialog modal-sm">
   <div class="modal-content">
      <div class="modal-header">
         <h5 class="modal-title">Aprobar Solicitud</h5>
         <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
         <p>Confirme para Aprobar la Solicitud</p>
      </div>
      <div class="modal-footer">
         <input type=submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>
         <a href="CG_001_005n.php?A=CAprov&ID='.$SelID .'&emp_='.$emp_soli .'&ID_o='.$Sel_obj .'&tot_='.$Sel_tot .'" class="btn btn-primary" 
            role=button >Aprobar</a>
      </div>
   </div>
</div>
</div>';
/****///////////////////////////

////////////////////////////////
////MODAL RECHAZAR SOLICITUD
echo '<div class="modal fade" id="Rechazar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
       <div class="modal-content">
          <div class="modal-header">
             <h5 class="modal-title">Rechazar Solicitud</h5>
             <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <p>Confirme para rechazar la Solicitud </p>
            <div class="row">
                <div class="col-sm-12">
                    <div class="slic-form-group">
                        <label for="comentarios" class="col-slic-label">Comentarios:</label>
                    </div>
                </div>
                <div class="col-sm-12">
                    <textarea class="slic-input" 
                              name="coments" 
                              rows="4" 
                              maxlength="200" 
                              style="width: 100%;"></textarea>
                </div>
            </div>
          </div>
          <div class="modal-footer">
             <input type="submit" class="btn btn-secondary" name="BtnCer" data-dismiss="modal" value="Cerrar">
             <input type=Submit class="btn btn-primary" name=BtnRej value=Rechazar>
          </div>
       </div>
    </div>
</div>';

//////////////////////////////
///modal consulta de detalle de la solicitud///
echo' <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Detalle de la Solicitud</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div id="tablarev_001_005"> </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>';
/////////

// script para activar el modal de ver comentarios
if ($_GET['A']=='vinfo') {
  ?>
  <script type="text/javascript">
      $('#INFO').modal('show');
  </script>
  <?php   
}

// script para activar el modal de aprovacion de gasto
if ($_GET['A']=='Aprov') {
  ?>
  <script type="text/javascript">
      $('#Aprovar').modal('show');
  </script>
  <?php   
}

// script para activar el modal de rechazar gasto
if ($_GET['A']=='Rej'and $vSaleM == 'IN') {
  ?>
  <script type="text/javascript">
      $('#Rechazar').modal('show');
  </script>
  <?php   
}

//script para activar el modal de nuevo centro de costo
if ($_GET['A']=='vCons'  and $vSaleM == 'IN') {
    ?>
    <script type="text/javascript">
        $('#exampleModal').modal('show');
    </script>
    <?php   
}

// Incluye el archivo del pie de página
include('includes/footer.php');
?>


<!--Agregar el script donde esta la tabla -->
<script type="text/javascript">
   $(document).ready(function() {
      $('#tablaviaticos_001_005').load('CG_001_005t.php');
   });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        var id = '<?php echo urldecode($SelID); ?>';
        var emp = '<?php echo $emp_soli; ?>';
        $("#exampleModal").on("shown.bs.modal", function () {
            $("#tablarev_001_005").load('CG_001_005rt.php?ID='+id+'&emp_='+emp);
        });
    });
</script>
</body>
</html>

