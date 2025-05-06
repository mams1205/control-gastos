<?php
// Solicitud de viáticos
//MAMS
//10-08-2024

$PageSecurity = 15; // Establece el nivel de seguridad de la página
$App = 'CG'; // Define la aplicación como 'CG' (Catálogo Centro de costos)
$Mod = 'Empleados'; // Define el módulo como 'Empleados'
include('includes/session.inc'); // Incluye el archivo de sesión para gestionar la autenticación y datos de sesión
$title = 'Solicitud de Viáticos'; // Establece el título de la página
include('includes/header.inc'); // Incluye el archivo del encabezado de la página

// Abre un formulario HTML con el método POST y la codificación para archivos adjuntos.
echo '<form method="post" 
            enctype="multipart/form-data"
            action="CG_001_004n.php?ID='.$_GET['ID'].'&A='.$_GET['A'].'&ID_d='.$_GET['ID_d'].'&t_o='.$_GET['t_o'].'&ID_e='.$_GET['ID_e'].'" />';
echo '<input type=hidden name=FormID value="'.$_SESSION['FormID'].'" />';

$Msg   = ''; // Inicializa la variable de mensaje como una cadena vacía
$vSaleM = 'IN'; // inicia la variable vsaleM con txt IN
$MsgC = 'green';
$SelID = $_GET['ID'];
$SelID_d = $_GET['ID_d'];
$SelID_e = $_GET['ID_e'];
$tot_solicictud = $_GET['t_o'];
$usuario = $_SESSION['CustomerID'];
$nombre_usuario = $_SESSION['UserID'];

if ($_GET['A']=='vMod' And $_POST['BtnSav']=='') {
    $Sql_mod = "SELECT * FROM CG_solicitud_viaticos_D WHERE id_detalle = '".$SelID."'";
    $Res_mod = DB_query($Sql_mod,$db);
    $Row_mod = DB_fetch_array($Res_mod);
 
    $_POST['obj_gasto_mod']  = $Row_mod['obj_gasto'];
    $valor_desc_gasto   = $Row_mod['desc_gasto'];
    $_POST['cant_solic_mod']    = $Row_mod['cant_solicitada'];
    $_POST['ID_viaticos']      = $Row_mod['id_detalle'];
    echo '<input type=hidden
                 name=ID_viaticos
                 value="'.$_POST['ID_viaticos'].'" />';
}


if ($_GET['A'] == 'vcSend' && $vSaleM == 'IN') { // Use '==' for comparison
    // Construct the SQL query to update the status.
    $sql = "UPDATE CG_solicitud_viaticos
            SET estatus = 'Enviado'
            WHERE ID_viaticos = '$SelID'"; // Use single quotes for variables

    $res = DB_query($sql, $db); // Execute the query

    //seleccionar el mail del responsable de ese centro de costo
    $sql_mail = "SELECT mail_responsable, mail_mas_20 FROM CG_datos_contables WHERE centro_de_costo = '".$SelID_d."'";
    $res_mail = DB_query($sql_mail, $db);
    $fila_mail = mysqli_fetch_assoc($res_mail);
    $mail_responsable = $fila_mail['mail_responsable'];
    $mail_mas = $fila_mail['mail_mas_20'];


    if ($tot_solicictud > 20000){
        //aqui va el mail de mas de 20
        $destinatario = $mail_mas;
      }else{
        //aqui va el mail de menos de 20
        $destinatario = $mail_responsable;
      }
        $asunto = "Solicitud de viaticos '.$SelID.'";

        $fecha_hora_actual = date('Y-m-d H:i:s');
        // Define el cuerpo del correo
        $cuerpo = "$nombre_usuario ha realizado una solicitud de viaticos en https://iatiqro.com.mx/ la cual necesita de su revisión";

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
        
        prnMsgV20('Solicitud de viáticos ' . htmlspecialchars($SelID, ENT_QUOTES, 'UTF-8') . ' enviada correctamente...' , 'success');
        
       

        $vSaleM = 'OUT';

               

        // header("Location: index.php");
        // exit(); // Terminate the script after redirection

    } else {
        // Handle the error if the query failed
        prnMsgV20('Error al enviar la solicitud: ' . DB_error($db), 'error');
    }
}


// al dar click en guardar insertar los datos de los campos en la database
if ($_POST['BtnSav']=='Guardar') {
    $fecha_actual  = date('Y-m-d');
    $cc = trim($_POST['id_centro_costos']);
    $sql = "INSERT INTO CG_solicitud_viaticos   (empleado,
                                                departamento,
                                                obj_gasto,
                                                estatus,
                                                justificacion,
                                                fecha_solicitud,
                                                fecha_inicio,
                                                fecha_fin) 
                                    VALUES ( '".$nombre_usuario."'
                                            ,'".$cc."'
                                            ,'".$_POST['obj_gasto_2']."'
                                            ,'Por enviar'
                                            ,'".$_POST['justificacion']."'
                                            ,'".$fecha_actual."'
                                            ,'".$_POST['date_inicio']."'
                                            ,'".$_POST['date_fin']."')";
        $res  = DB_query($sql,$db);
        $Msg  = 'Se agrego Nuevo Solicitud de viáticos '.$_POST['obj_gasto_2'];
        $MsgC = 'green'; 
        unset ($_POST['obj_gasto_2']);
        unset ($_POST['id_centro_costos']);
        unset ($_POST['justificacion']);
        unset ($_POST['date_inicio']);
        unset ($_POST['date_fin']);
        $vSaleM = 'OUT';
}

//Si el boton es modificar entonces ejecuta esto
if ($_POST['BtnSav'] == 'Modificar') {
    // Asegúrate de que $_POST['ID_CC'] esté definido y no esté vacío
    if (isset($_POST['ID_viaticos']) && !empty($_POST['ID_viaticos'])) {
        $ID_viaticos = mysqli_real_escape_string($db, $_POST['ID_viaticos']);
        $descrip_gasto = mysqli_real_escape_string($db, $_POST['id_desc_gasto_mod']);
        $cantidad_modif = mysqli_real_escape_string($db, $_POST['cant_solic_mod']);

    //QUERY SQL
        $UPD_mod = "UPDATE CG_solicitud_viaticos_D
                    SET  desc_gasto = '$descrip_gasto',
                        cant_solicitada = '$cantidad_modif'
                    WHERE id_detalle = $ID_viaticos";

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

// al dar click en guardar insertar los datos de los campos en la database
if ($_POST['BtnSav']=='Agregar') {
    $sql = "INSERT INTO CG_solicitud_viaticos_D  (empleado,
                                                obj_gasto,
                                                desc_gasto,
                                                cant_solicitada,
                                                id_solicitud) 
                                    VALUES ( '".$nombre_usuario."'
                                            ,'".$_POST['obj_gasto_add']."'
                                            ,'".$_POST['id_desc_gasto_add']."'
                                            ,'".$_POST['cant_solic_add']."'
                                            ,'".$SelID."')";
        $res  = DB_query($sql,$db);
        $Msg  = 'Se agrego Nuevo Gasto a '.$_POST['obj_gasto_cons'];
        $MsgC = 'green'; 
        unset ($_POST['obj_gasto_cons']);
        unset ($_POST['id_desc_gasto_add']);
        unset ($_POST['cant_solic']);
        $vSaleM = 'OUT';
}

if ($_GET['A']=='vEli') {
    // Construye una consulta SQL para eliminar el ID seleccionado.
        // Ensure variables are properly escaped and quoted for SQL
        $Del_CC = "DELETE FROM CG_solicitud_viaticos 
        WHERE ID_viaticos = '$SelID'"; // Use single quotes for variables

        $Del_CC_D = "DELETE FROM CG_solicitud_viaticos_D
        WHERE ID_solicitud = '$SelID'"; // Use single quotes for variables

        $Res_CC = DB_query($Del_CC, $db); // Execute the deletion query
        $Res_CC_D = DB_query($Del_CC_D, $db); // Execute the deletion query

        if ($Res_CC && $Res_CC_D) { // Check if the query was successful
        prnMsgV20('Se ha eliminado la solicitud de viáticos ...', 'success');
        } else {
        // If the query failed, show an error message
        prnMsgV20('Error al eliminar la solicitud: ' . DB_error($db), 'error');
        }

}

if ($_GET['A']=='vEliD') {
    // Construye una consulta SQL para eliminar el ID seleccionado.
        // Ensure variables are properly escaped and quoted for SQL
        $Del_CC = "DELETE FROM CG_solicitud_viaticos_D 
                    WHERE id_detalle = '$SelID'"; // Use single quotes for variables

        $Res_CC = DB_query($Del_CC, $db); // Execute the deletion query

        if ($Res_CC) { // Check if the query was successful
        prnMsgV20('Se ha eliminado el gasto ...', 'success');
        } else {
        // If the query failed, show an error message
        prnMsgV20('Error al eliminar la solicitud: ' . DB_error($db), 'error');
        }

}

/// sql informacion
if ($_GET['A']=='vinfo') {
    $Sql_inf = "SELECT DISTINCT(comentarios) 
            FROM CG_solicitud_viaticos
            WHERE ID_viaticos = '$SelID'";
    $Res_inf = DB_query($Sql_inf, $db);
    $Row_c = DB_fetch_array($Res_inf);
    
    $_POST['informacion'] = $Row_c['comentarios'];


}


//container principal onde va a estar la tabla 
echo '<div class="container" style="margin-top:80px">
    <div class="row">
        <div class="col-sm-12">
            <div class="card text-left">
                <div class="card-body">
                    <h4>Solicitud de Viáticos</h4>';
                    if ($Msg<>'')
                        echo '<p style="text-align: center;font-family:Arial;font-size:16px;color:'.$MsgC.'">'.$Msg.'</p>';
                            //<!-- Párrafo con estilo centrado, fuente Arial, tamaño 16px y color determinado por la variable $MsgC -->
                        echo    '<a href="CG_001_004n.php?A=vNew" class="btn btn-primary" role="button">
                                    Agregar Nueva Solicitud
                                    <span class="pl-2 fas fa-plus-circle"></span>
                                </a>';
                    echo '<div id="tablaviaticos_001_004"></div>';
                    //Contenedor vacío con el ID 'tablaCG_001_001' para cargar dinámicamente la tabla de centro de costos
echo'           </div>
            </div>
        </div>
    </div>
</div>';

if ($_GET['A']=='vNew') {
    $TituloModal = 'Solicitud de Viáticos';
  }

// /***
// * Modal Pantalla de Datos para agregar nuevo
// */
// Modal HTML

echo '<div class="modal fade" id="PantallaDatos" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">'.$TituloModal.'</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
                
// Cuerpo del modal
echo '    <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">';
                    
// Formulario de solicitud
echo '           <div class="slic-form-group row">
                        <label for="obj_slic" class="col-sm-3 col-slic-label">Objetivo de la Solicitud:</label>
                        <div class="col-sm-4">
                            <input type="text" class="slic-input" name="obj_gasto_2" id="obj_gasto_2"/>
                        </div>  
                    </div>';

echo '           <div class="slic-form-group row">
                        <label for="centro_costos" class="col-sm-3 col-slic-label">Centro de Costos:</label>
                        <div class="col-sm-4">
                            <select class="slic-input" name="id_centro_costos" id="id_centro_costos">';
                            
                                // Valor por defecto y opciones del combobox
                                $selectedcc = isset($_POST['id_centro_costos']) ? htmlspecialchars($_POST['id_centro_costos'], ENT_QUOTES, 'UTF-8') : '';
                                $sql = "SELECT DISTINCT centro_de_costo, desc_centro_de_costo FROM CG_datos_contables";
                                $result = DB_query($sql, $db);
                                
                                echo '<option value="">Seleccione un Centro de Costo</option>';
                                while ($row = DB_fetch_array($result)) {
                                    $centro_de_costo = htmlspecialchars($row['centro_de_costo'], ENT_QUOTES, 'UTF-8');
                                    $desc_cc = htmlspecialchars($row['desc_centro_de_costo'], ENT_QUOTES, 'UTF-8');
                                    $isSelected = ($centro_de_costo === $selectedcc) ? 'selected' : '';
                                    echo '<option value="' . $centro_de_costo . '" ' . $isSelected . '>'
                                    . $centro_de_costo . ' - ' . $desc_cc . '</option>';
                                }
                            
echo '                  </select>
                        </div>
                    </div>';

// Fecha inicial del viaje
echo '           <div class="slic-form-group row">
                        <label for="date_inicio" class="col-sm-3 col-slic-label">Fecha inicial del viaje:</label>
                        <div class="col-sm-4">
                            <input type="date" class="slic-input" name="date_inicio" id="date_inicio"/>
                        </div>  
                    </div>';

// Fecha final del viaje
echo '           <div class="slic-form-group row">
                        <label for="date_fin" class="col-sm-3 col-slic-label">Fecha final del viaje:</label>
                        <div class="col-sm-4">
                            <input type="date" class="slic-input" name="date_fin" id="date_fin"/>
                        </div>  
                    </div>';

// Justificación de la solicitud
echo '           <div class="slic-form-group row">
                        <label for="Justificación" class="col-sm-3 col-slic-label">Justificación de la Solicitud:</label>
                        <div class="col-sm-9">
                            <textarea class="slic-input" name="justificacion" rows="4" maxlength="200" style="width: 100%;"></textarea>
                        </div>
                    </div>';

echo '           </div>
                </div>
            </div>';  // Fin de modal-body y row, col-sm-12

// Pie de página del modal
echo '    <div class="modal-footer">
                <button type="submit" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <input type="submit" class="btn btn-primary" name="BtnSav" value="Guardar">
            </div>';
echo '      </div>
        </div>
    </div>';

 

//MODAL PARA AGREGAR UN NUEVO GASTO A LA SOLICITUDs
echo '<div class="modal fade" id="NuevoGasto" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Nuevo Gasto a '.htmlspecialchars($SelID_d, ENT_QUOTES, 'UTF-8').'</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
echo'           <div class = "modal-body">
                <div class = "row">
                    <div class="col-sm-12">';
echo'                   <div class="slic-form-group row">
                            <label for="obj_gasto_add" class="col-sm-3 col-slic-label">Obj del Gasto:</label>
                                <div class = "col-sm-4">';
echo'                           <input
                                        class="slic-input" 
                                        name="obj_gasto_add"
                                        id = "obj_gasto_add"
                                        value = "'.$SelID_d.'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="obj_gasto_cons"
                                        id="obj_gasto_cons"
                                        value="'.$SelID_d.'"
                                    />
                                </div>  
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="desc_gasto" class="col-sm-3 col-slic-label">Descripción del Gasto:</label>
                                <div class = "col-sm-4">
                                    <select 
                                        class="slic-input" 
                                        name="id_desc_gasto_add"
                                        id = "id_desc_gasto_add"';
                                        // Default value for the dropdown
                                        $selected_desc_gasto = isset($_POST['id_desc_gasto_add']) ? htmlspecialchars($_POST['id_desc_gasto_add'], ENT_QUOTES, 'UTF-8') : '';
                                        
                                        // Consulta SQL para obtener las opciones del combobox
                                        $sql_desc_gasto = "SELECT DISTINCT desc_gasto
                                                            FROM CG_gastos";
                                        $result_desc_gasto = DB_query($sql_desc_gasto, $db);
                                        echo '<option value="">Seleccione un tipo de gasto</option>';
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
                                        type = "text"
                                        class="slic-input" 
                                        name="cant_solic_add"
                                        id="cant_solic_add"
                                        value="' . $cant_solic_add . '"
                                        oninput = "validarNumero(this)"
                                    />
                                </div>
                        </div>
                        </div>';
echo'           </div>
                </div>';
echo '          <div class="modal-footer">';
echo '          <input type=Submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>';
                if ($_GET['A']=='vPlus') 
echo '              <input type=Submit class="btn btn-primary" name=BtnSav value=Agregar>';
echo'           </div>
            </div>
        </div>
    </div>';

//////////////////////////////

/***
* Modal Eliminar
*/
echo '<div class="modal fade" id="ConfirmaDel" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-sm">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title">Eliminar Solicitud de viáticos</h5>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <div class="modal-body">
                  <p>Confirme para eliminar la solicitud</p>
               </div>
               <div class="modal-footer">
                  <input type=submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>
                  <a href="CG_001_004n.php?A=vEli&ID='.$SelID .'" class="btn btn-primary" 
                     role=button >Eliminar</a>
               </div>
            </div>
         </div>
      </div>';
/****/

/////////MODAL INFO/////////////
echo '<div class="modal fade" id="INFO" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
       <div class="modal-content">
          <div class="modal-header">
             <h5 class="modal-title">Información</h5>
             <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="slic-form-group">
                        <label for="comentarios" class="col-slic-label">Comentarios:</label>
                    </div>
                </div>
                <div class="col-sm-12">
                    <textarea class="slic-input" 
                              name="comentarios" 
                              rows="4" 
                              maxlength="200" 
                              style="width: 100%;"
                              readonly>' . htmlspecialchars($_POST['informacion'], ENT_QUOTES, 'UTF-8') . '</textarea>
                </div>
            </div>
          </div>
          <div class="modal-footer">
             <input type="submit" class="btn btn-secondary" name="BtnCer" data-dismiss="modal" value="Cerrar">
          </div>
       </div>
    </div>
</div>';
/****/////////////////////////

///modal consulta de detalle de la solicitud///
echo' <div class="modal fade" id="Modal_detalle" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Detalle de la Solicitud</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div id="tabla_detalle"> </div>
            </div>
            <div class="modal-footer">
                <a 
                    href="CG_001_004n.php?A=vcSend&ID='.$SelID .'&ID_d='.$SelID_d .'&t_o='.$tot_solicictud .'"
                    class="btn btn-success" 
                    role=button >Enviar
                    <span class="fa-solid fa-paper-plane"</span>
                </a>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              
            </div>
          </div>
        </div>
      </div>';
/////////

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
                                        name="id_desc_gasto_mod"
                                        id = "id_desc_gasto_mod">';
                                        // Default value for the dropdown
                                        $selected_desc_gasto = isset($_POST['id_desc_gasto_mod']) ? htmlspecialchars($_POST['id_desc_gasto_mod'], ENT_QUOTES, 'UTF-8') : '';
                                        
                                        // Consulta SQL para obtener las opciones del combobox
                                        $sql_desc_gasto = "SELECT DISTINCT desc_gasto
                                                            FROM CG_gastos";
                                        $result_desc_gasto = DB_query($sql_desc_gasto, $db);
                                        echo '<option value="' . htmlspecialchars($valor_desc_gasto) . '">' . htmlspecialchars($valor_desc_gasto) . '</option>';
                                        // Loop a través de los resultados de la consulta
                                        while ($row_desc_gasto_mod = DB_fetch_array($result_desc_gasto)) {

                                            // mandarle el valor de la fila a la variable desc_gasto
                                                $desc_gasto = htmlspecialchars($row_desc_gasto_mod['desc_gasto'], ENT_QUOTES, 'UTF-8');
                                            
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
                                    $cant_solic_add = isset($_POST['cant_solic_mod']) ? htmlspecialchars($_POST['cant_solic_mod'], ENT_QUOTES, 'UTF-8') : '';

echo'                               <input 
                                        type="text"
                                        class="slic-input" 
                                        name="cant_solic_mod"
                                        id="cant_solic_mod"
                                        value="' . $cant_solic_add . '"
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

/***
* Modal Eliminar
*/
echo '<div class="modal fade" id="ConfirmaDelD" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-sm">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title">Eliminar</h5>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <div class="modal-body">
                  <p>Confirme para eliminar</p>
               </div>
               <div class="modal-footer">
                  <input type=submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>
                  <a href="CG_001_004n.php?A=vEliD&ID='.$SelID .'" class="btn btn-primary" 
                     role=button >Eliminar</a>
               </div>
            </div>
         </div>
      </div>';
/****/

//Script para activar el modal modificar gasto
if ($_GET['A']=='vMod' and $vSaleM == 'IN') {
        ?>
        <script type="text/javascript">
            $('#ModGasto').modal('show');
        </script>
        <?php   
}

//script para activar el modal del detalle de la solicitud
if ($_GET['A']=='vCons'  and $vSaleM == 'IN') {
    ?>
    <script type="text/javascript">
        $('#Modal_detalle').modal('show');
    </script>
    <?php   
}

//script para activar el modal de comenarios
if ($_GET['A']=='vinfo') {
    ?>
    <script type="text/javascript">
        $('#INFO').modal('show');
    </script>
    <?php   
}

//script para activar el modal de nuevo 
if ($_GET['A']=='vNew'  and $vSaleM == 'IN') {
    ?>
    <script type="text/javascript">
        $('#PantallaDatos').modal('show');
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

if ($_GET['A']=='vDelD') {
    ?>
    <script type="text/javascript">
        $('#ConfirmaDelD').modal('show');
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



// Incluye el archivo del pie de página
include('includes/footer.php');
?>

<!-- reemplazar los espacios por guiones y poner todo en mayusculas -->
<script type="text/javascript">
    document.getElementById('obj_gasto_2').addEventListener('input', function() {
        // Reemplazar espacios por guiones bajos y poner en mayusculas
        this.value = this.value.replace(/ /g, '_').toUpperCase();
    });
</script>

<!--Agregar el script donde esta la tabla principal -->
<script type="text/javascript">
   $(document).ready(function() {
      $('#tablaviaticos_001_004').load('CG_001_004t.php');
   });
</script>

<!-- eliminar caracteres no numericos de la cantidad solicitada -->
<script>
function validarNumero(input) {
    // Elimina caracteres no numéricos excepto puntos y comas
    input.value = input.value.replace(/[^0-9.]/g, '');
}
</script>

<!-- activa la tabla de detalle en el modal -->
<script type="text/javascript">
    $(document).ready(function(){
        var id = '<?php echo urldecode($SelID); ?>';
        var emp = '<?php echo $nombre_usuario; ?>';
        $("#Modal_detalle").on("shown.bs.modal", function () {
            $("#tabla_detalle").load('CG_001_004tcs.php?ID='+id+'&emp_='+emp);
        });
    });
</script>


</body>
</html>

