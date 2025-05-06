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
             action="CG_001_004cs.php?ID='.$_GET['ID'].'&ID_o='.$_GET['ID_o'].'&ID_e='.$_GET['ID_e'].'&A='.$_GET['A'].'" />';

echo '<input type=hidden name=FormID value="'.$_SESSION['FormID'].'" />';

$Msg   = ''; // Inicializa la variable de mensaje como una cadena vacía
$vSaleM = 'IN'; // inicia la variable vsaleM con txt IN
$MsgC = 'green';
$SelID = urldecode($_GET['ID']);
$SelID_e = $_GET['ID_e'];
$usuario = $_SESSION['CustomerID'];
$dpto_usuario = $_SESSION['UserBranch'];
$nombre_usuario = $_SESSION['UserID'];
$tot_solicictud = $_GET['ID_o'];

if ($_GET['A']=='vMod' And $_POST['BtnSav']=='') {
    $Sql_mod = "SELECT * FROM CG_solicitud_viaticos_D WHERE id_detalle = 1";
    $Res_mod = DB_query($Sql_mod,$db);
    $Row_mod = DB_fetch_array($Res_mod);
 
    $_POST['obj_gasto_mod']  = $Row_mod['obj_gasto'];
    $seleccion_ant    = $Row_mod['desc_gasto'];
    $_POST['cant_solic_add']    = $Row_mod['cant_solicitada'];
    $_POST['ID_viaticos']      = $Row_mod['id_detalle'];
    echo '<input type=hidden
                 name=ID_viaticos
                 value="'.$_POST['ID_viaticos'].'" />';
}


// 
if ($_GET['A'] == 'vcSend' && $vSaleM == 'IN') { // Use '==' for comparison
    // Construct the SQL query to update the status.
    $sql = "UPDATE CG_solicitud_viaticos
            SET estatus = 'Enviado'
            WHERE obj_gasto = '$SelID' AND empleado = '$nombre_usuario'"; // Use single quotes for variables

    $res = DB_query($sql, $db); // Execute the query

    //seleccionar el mail del responsable de ese centro de costo
    $sql_mail = "SELECT mail_responsable, mail_mas_20 FROM CG_datos_contables WHERE centro_de_costo = '".$dpto_usuario."'";
    $res_mail = DB_query($sql_mail, $db);
    $fila_mail = mysqli_fetch_assoc($res_mail);
    $mail_responsable = $fila_mail['mail_responsable'];
    $mail_mas = $fila_mail['mail_mas_20'];


    if ($tot_solicictud > 20000){
        //aqui va el mail de mas de 20
        $destinatario = "mariosegovia1205@gmail.com";
      }else{
        //aqui va el mail de menos de 20
        $destinatario = "elpepinoliterario@gmail.com";
      }
        $asunto = "Solicitud de viaticos";

        $fecha_hora_actual = date('Y-m-d H:i:s');
        // Define el cuerpo del correo
        $cuerpo = "$nombre_usuario ha realizado una solicitud de viaticos";

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
        mail($destinatario, $asunto, $message, $headers);

    //enviar correos

    if ($res) { // Check if the query was successful
        
        prnMsgV20('Solicitud de viáticos ' . htmlspecialchars($SelID, ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($nombre_usuario, ENT_QUOTES, 'UTF-8') . ' enviada correctamente...', 'success');
        
       

        $vSaleM = 'OUT';

               

        header("Location: index.php");
        exit(); // Terminate the script after redirection

    } else {
        // Handle the error if the query failed
        prnMsgV20('Error al enviar la solicitud: ' . DB_error($db), 'error');
    }
}

//Si el boton es modificar entonces ejecuta esto
if ($_POST['BtnSav'] == 'Modificar') {
    // Asegúrate de que $_POST['ID_CC'] esté definido y no esté vacío
    if (isset($_POST['ID_viaticos']) && !empty($_POST['ID_viaticos'])) {
        $ID_viaticos = mysqli_real_escape_string($db, $_POST['ID_viaticos']);
        $descrip_gasto = mysqli_real_escape_string($db, $_POST['id_desc_gasto_add']);
        $cantidad_modif = mysqli_real_escape_string($db, $_POST['cant_solic_add']);

    //QUERY SQL
        $UPD_mod = "UPDATE CG_solicitud_viaticos
                    SET  desc_gasto = '$descrip_gasto',
                        cant_solicitada = '$cantidad_modif'
                    WHERE ID_viaticos = $ID_viaticos";

        $res_mod = DB_query($UPD_mod, $db);

        if ($res_mod) {
            prnMsgV20('Se ha modificado la solicitud de viaticos ...','success');
        } else {
            $Msg = 'Error al modificar la solicitud';
            $MsgC = 'red';
        }

        $vSaleM = 'OUT';
    } else {
        $Msg = 'ID del viatico no proporcionado';
        $MsgC = 'red';
    }
}
//////

if ($_GET['A']=='vEli') {
    // Construye una consulta SQL para eliminar el ID seleccionado.
        // Ensure variables are properly escaped and quoted for SQL
        $Del_gasto =    "DELETE FROM CG_solicitud_viaticos 
                         WHERE ID_viaticos = '$SelID_e'"; // Use single quotes for variables

        $Res_gasto = DB_query($Del_gasto, $db); // Execute the deletion query

        if ($Res_gasto) { // Check if the query was successful
        prnMsgV20('Se ha eliminado el gasto de la solicitud de viáticos ...', 'success');
        } else {
        // If the query failed, show an error message
        prnMsgV20('Error al eliminar el gasto: ' . DB_error($db), 'error');
        }

}
//////

//container principal onde va a estar la tabla 
echo '<div class="container" style="margin-top:80px">
     <form method="post" action="">
    <div class="row">
        <div class="col-sm-12">
            <div class="card text-left">
                <div class="card-body">
                    <h4>Detalle de la solicitud '.$SelID.'</h4>';
                    if ($Msg<>'')
                        echo '<p style="text-align: center;font-family:Arial;font-size:16px;color:'.$MsgC.'">'.$Msg.'</p>';
                            //<!-- Párrafo con estilo centrado, fuente Arial, tamaño 16px y color determinado por la variable $MsgC -->
                    echo '<div id="tabla_gastos_solic_001_004"></div>';
                    //Contenedor vacío con el ID 'tablaCG_001_001' para cargar dinámicamente la tabla de centro de costos
echo '          </div>
                    <div class="card-footer">
                       <a href="CG_001_004cs.php?A=vSend&ID=' . $SelID .'&ID_o=' . $tot_solicictud .'" class="btn btn-success" role="button">
                                    Enviar Solicitud
                                    <span class="fa-solid fa-paper-plane"</span>
                                </a>
                                <a href="CG_001_004n.php" class="btn btn-danger" role="button" id="Regresar">
                                    Regresar
                                </a>
                    </div>
                </div>
            </div>
        </div>
    </div>';

/////////MODAL MODIFICAR///////////////////
echo '<div class="modal fade" id="ModGasto" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modificar Gasto '.$SelID_e.'</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
echo'           <div class = "modal-body">
                <div class = "row">
                    <div class="col-sm-12">';
echo'                   <div class="slic-form-group row">
                            <label for="obj_gasto_mod" class="col-sm-3 col-slic-label">Objetivo del Gasto:</label>
                                <div class = "col-sm-4">';
echo'                           <input
                                        class="slic-input" 
                                        name="obj_gasto_mod"
                                        id = "obj_gasto_mod"
                                        value = "'.$_POST['obj_gasto_mod'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="obj_gasto_cons"
                                        id="obj_gasto_cons"
                                        value="'.$_POST['obj_gasto_mod'].'"
                                    />
                                </div>  
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="desc_gasto" class="col-sm-3 col-slic-label">Descripción del Gasto:</label>
                                <div class = "col-sm-4">
                                    <select 
                                        class="slic-input" 
                                        name="id_desc_gasto_add"
                                        id = "id_desc_gasto_add"
                                        onchange="submit()">';
                                        // Default value for the dropdown
                                        $selected_desc_gasto = isset($_POST['id_desc_gasto_add']) ? htmlspecialchars($_POST['id_desc_gasto_add'], ENT_QUOTES, 'UTF-8') : '';
                                        
                                        // Consulta SQL para obtener las opciones del combobox
                                        $sql_desc_gasto = "SELECT DISTINCT desc_gasto
                                                            FROM CG_gastos";
                                        $result_desc_gasto = DB_query($sql_desc_gasto, $db);
                                        echo '<option value="">'.$seleccion_ant.'</option>';
                                        // Loop a través de los resultados de la consulta
                                        while ($row_desc_gasto = DB_fetch_array($result_desc_gasto)) {

                                            // mandarle el valor de la fila a la variable desc_gasto
                                                $desc_gasto = htmlspecialchars($row_desc_gasto['desc_gasto'], ENT_QUOTES, 'UTF-8');
                                            
                                            //si desc_gasto es igual a selected_desc_gasto la variable isSelected_desc_gasto se pondra en el combobox
                                                $isSelected_desc_gasto = ($desc_gasto === $selected_desc_gasto) ? 'selected' : '';
                                                
                                                echo '<option value="' . $desc_gasto . '" ' . $isSelected_desc_gasto . '>'
                                                    . $desc_gasto . '</option>';
                                            }
echo'                               </select>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="Cantidad" class="col-sm-3 col-slic-label">Cantidad a solicitar:</label>
                                <div class = "col-sm-4">';
                                    // Mantener el valor del input "cant_solic" si ya fue enviado
                                    $cant_solic_add = isset($_POST['cant_solic_add']) ? htmlspecialchars($_POST['cant_solic_add'], ENT_QUOTES, 'UTF-8') : '';

echo'                               <input 
                                        type = "number"
                                        class="slic-input" 
                                        name="cant_solic_add"
                                        id="cant_solic_add"
                                        value="' . $cant_solic_add . '"
                                        min = "0"
                                        step = "0.01"
                                        oninput = "validarNumero(this)"
                                    />
                                </div>
                        </div>
                        </div>';
echo'           </div>
                </div>';
echo '          <div class="modal-footer">';
echo '          <input type=Submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>';
                if ($_GET['A']=='vMod') 
echo '              <input type=Submit class="btn btn-primary" name=BtnSav value=Modificar>';
echo'           </div>
            </div>
        </div>
    </div>';

//////////////////////////////

/////////MODAL Enviar///////////////////
echo '<div class="modal fade" id="ConfirmaEnviar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
       <div class="modal-content">
          <div class="modal-header">
             <h5 class="modal-title">Enviar Solicitud</h5>
             <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
             <p>Confirme para enviar la solicitud </p>
          </div>
          <div class="modal-footer">
             <input type=submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>
             <a href="CG_001_004cs.php?A=vcSend&ID='.$SelID .'&ID_o='.$tot_solicictud .'" class="btn btn-primary" role=button >Enviar</a>
          </div>
       </div>
    </div>
 </div>';
/****///////////////////////


/***
* Modal Eliminar
*/
echo '<div class="modal fade" id="ConfirmaDel" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-sm">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title">Eliminar Gasto</h5>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <div class="modal-body">
                  <p>Confirme para eliminar el gasto</p>
               </div>
               <div class="modal-footer">
                  <input type=submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>
                  <a href="CG_001_004cs.php?A=vEli&ID='.$SelID .'&ID_e='.$SelID_e .'" class="btn btn-primary" 
                     role=button >Eliminar</a>
               </div>
            </div>
         </div>
      </div>';
/****/



// script para activar el modal de enviar
if ($_GET['A']=='vSend') {
    ?>
    <script type="text/javascript">
        $('#ConfirmaEnviar').modal('show');
    </script>
    <?php   
}



// script para activar el modal de nuevo gasto
if ($_GET['A']=='vPlus' and $vSaleM == 'IN') {
    ?>
    <script type="text/javascript">
        $('#NuevoGasto').modal('show');
    </script>
    <?php   
}

if ($_GET['A']=='vMod' and $vSaleM == 'IN') {
    ?>
    <script type="text/javascript">
        $('#ModGasto').modal('show');
    </script>
    <?php   
}

// script para activar el modal de eliminar
if ($_GET['A']=='vDel') {
    ?>
    <script type="text/javascript">
        $('#ConfirmaDel').modal('show');
    </script>
    <?php   
}

// Incluye el archivo del pie de página
include('includes/footer.php');
?>

<!--Agregar el script donde esta la tabla -->
<script type="text/javascript">
   $(document).ready(function() {
        var id = '<?php echo urldecode($SelID); ?>';
    $('#tabla_gastos_solic_001_004').load('CG_001_004tcs.php?ID='+id);
   });
</script>

<script>
function validarNumero(input) {
    // Elimina caracteres no numéricos excepto puntos y comas
    input.value = input.value.replace(/[^0-9.]/g, '');
}
</script>


</body>
</html>

