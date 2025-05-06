<?php

$PageSecurity = 15; // Establece el nivel de seguridad de la página
$App = 'CG'; // Define la aplicación como 'CG' (Catálogo Centro de costos)
$Mod = 'Empleados'; // Define el módulo como 'Empleados'
include('includes/session.inc'); // Incluye el archivo de sesión para gestionar la autenticación y datos de sesión
$title = 'Comprobación de Gastos'; // Establece el título de la página
include('includes/header.inc'); // Incluye el archivo del encabezado de la página

// Abre un formulario HTML con el método POST y la codificación para archivos adjuntos.
echo '<form method="post" 
            enctype="multipart/form-data"
            action="CG_001_001n.php?ID='.$_GET['ID'].'&A='.$_GET['A'].'" />';

echo '<input type=hidden name=FormID value="'.$_SESSION['FormID'].'" />';

$Msg   = ''; // Inicializa la variable de mensaje como una cadena vacía
$vSaleM = 'IN'; // inicia la variable vsaleM con txt IN
$MsgC = 'green';
$SelID = $_GET['ID'];
$usuario = $_SESSION['CustomerID'];
$nombre_usuario = $_SESSION['UserID'];

//so A toma el valor de vSend envia la factura y cambia el status a enviado
if($_GET['A']=='vSend' and $vSaleM = 'IN'){
    $sql_send = "UPDATE CG_comprobacion 
                  SET estatus = 'enviado'
                  WHERE id_comprobacion = '$SelID'";
    $Res_send = DB_query($sql_send,$db);

    ///seleccionar el importe para saber a quien se le manda la factura
    $Sql_importe = "SELECT total, obj_gasto, centro_de_costo FROM CG_comprobacion WHERE id_comprobacion = '".$SelID."'";
    $Res_importe = DB_query($Sql_importe,$db);
    $Row_importe = DB_fetch_array($Res_importe);
    $importe_condicion = $Row_importe['total'];
    $viaje = $Row_importe['obj_gasto'];
    $centro = $Row_importe['centro_de_costo'];
  

    //seleccionar el mail del responsable de ese centro de costo
    $sql_mail = "SELECT mail_responsable, mail_mas_20 FROM CG_datos_contables WHERE centro_de_costo = '".$centro_costo."'";
    $res_mail = DB_query($sql_mail, $db);
    $fila_mail = mysqli_fetch_assoc($res_mail);
    $mail_responsable = $fila_mail['mail_responsable'];
    $mail_mas = $fila_mail['mail_mas_20'];


    if ($importe_condicion > 20000){
        //aqui va el mail de mas de 20
        $destinatario = "carmen.martinez@plastekgroup.com";
      }else{
        //aqui va el mail de menos de 20
        $destinatario = "carmen.martinez@plastekgroup.com";
      }
        $asunto = "Solicitud de comprobación de gastos $viaje";

        // Define el cuerpo del correo
        $cuerpo = "$nombre_usuario ha realizado una solicitud de comprobación de gastos del centro de costo $centro en la plataforma https://iatiqro.com.mx/";

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
    

        // Mensaje de éxito
        prnMsgV20('Solicitud de comprobación enviada correctamente...', 'success');


        $vSaleM = 'OUT';
}


// si A toma el valor de vMod entonces inserta estas variables en el formulario
if ($_GET['A']=='vMod' And $_POST['BtnSav']=='') {
    $Sql = "SELECT * FROM CG_comprobacion WHERE id_comprobacion = '".$SelID."'";
    $Res = DB_query($Sql,$db);
    $Row = DB_fetch_array($Res);
 
    $valor_obj_gasto = $Row['obj_gasto'];
    // $_POST['id_obj_gasto']  = $Row['obj_gasto'];
    $_POST['proovedor_cons'] = $Row['proveedor'];
    $_POST['empleado_cons'] = $Row['empleado'];
    $valor_cc = $Row['centro_de_costo'];
    // $_POST['id_centro_costos'] = $Row['centro_de_costo'];
    $valor_ga = $Row['grupo_articulos'];
    // $_POST['id_ga'] = $Row['grupo_articulos'];
    $valor_cuenta = $Row['cuenta_contable'];
    // $_POST['cuenta_cons'] = $Row['cuenta_contable'];
    $valor_desc_gasto = $Row['desc_gasto'];
    // $_POST['id_desc_gasto'] = $Row['desc_gasto'];
    $_POST['importe'] = $Row['importe'];
    $_POST['descuento'] = $Row['descuento'];
    $_POST['iva'] = $Row['iva'];
    $_POST['ieps'] = $Row['ieps'];
    $_POST['ish'] = $Row['ish'];
    $_POST['iva_0'] = $Row['iva_tasa_0'];
    $_POST['excento'] = $Row['excento'];
    $_POST['no_deducible'] = $Row['no_deducible'];
    $_POST['iva_no_deducible'] = $Row['iva_no_deducible'];
    $_POST['propina'] = $Row['propina'];
    $_POST['ret_iva'] = $Row['ret_iva'];
    $_POST['ret_isr_10'] = $Row['ret_isr_10'];
    $_POST['ret_isr_125'] = $Row['ret_isr_125'];
    $_POST['total'] = $Row['total'];
    $_POST['id_comprobacion'] = $Row['id_comprobacion'];


    echo '<input type=hidden
                 name=id_comprobacion
                 value="'.$_POST['id_comprobacion'].'" />';
}

if ($_POST['BtnSav'] == 'Modificar') {
    // Asegúrate de que $_POST['ID_CC'] esté definido y no esté vacío
    if (isset($_POST['id_comprobacion']) && !empty($_POST['id_comprobacion'])) {
        $id_comprobacion = $_POST['id_comprobacion'];
        $mod_centro_costo = $_POST['mod_centro_costos'];
        $mod_obj_gasto = $_POST['mod_obj_gasto'];
        $mod_prov = $_POST['mod_prov'];
        $mod_empleado = $_POST['empleado_cons'];
        $mod_ga = $_POST['mod_ga'];
        $mod_cuenta_contable = $_POST['mod_cuenta_contable'];
        $mod_desc_gasto = $_POST['mod_desc_gasto'];
        $mod_importe = $_POST['mod_importe'];
        $mod_descuento = $_POST['mod_descuento'];
        $mod_iva = $_POST['mod_iva'];
        $mod_ieps = $_POST['mod_ieps'];
        $mod_ish = $_POST['mod_ish'];
        $mod_iva_0 = $_POST['mod_iva_0'];
        $mod_excento = $_POST['mod_excento'];
        $mod_no_deducible = $_POST['mod_no_deducible'];
        $mod_iva_no_deducible = $_POST['mod_iva_no_deducible'];
        $mod_propina = $_POST['mod_propina'];
        $mod_ret_iva = $_POST['mod_ret_iva'];
        $mod_ret_isr_10 = $_POST['mod_ret_isr_10'];
        $mod_ret_isr_125 = $_POST['mod_ret_isr_125'];
        $mod_total = ($mod_importe + $mod_iva + $mod_ieps + $mod_ish+ $mod_iva_0 + $mod_excento + $mod_iva_no_deducible + $mod_no_deducible + $mod_propina - $mod_ret_iva - $mod_ret_isr_10 - $mod_ret_isr_125) - $mod_descuento;
    

        $UPD_CC = "UPDATE CG_comprobacion
        SET obj_gasto = '$mod_obj_gasto',
            proveedor = '$mod_prov',
            centro_de_costo = '$mod_centro_costo',
            grupo_articulos = '$mod_ga',
            cuenta_contable = '$mod_cuenta_contable',
            desc_gasto = '$mod_desc_gasto',
            importe = '$mod_importe',
            descuento = '$mod_descuento',
            iva = '$mod_iva',
            ieps = '$mod_ieps',
            ish = '$mod_ish',
            iva_tasa_0 = '$mod_iva_0',
            excento = '$mod_excento',
            no_deducible = '$mod_no_deducible',
            iva_no_deducible = '$mod_iva_no_deducible',
            propina = '$mod_propina',
            ret_iva = '$mod_ret_iva',
            ret_isr_10 = '$mod_ret_isr_10',
            ret_isr_125 = '$mod_ret_isr_125',
            total = '$mod_total',
            jerarquia = CASE 
                        WHEN $mod_total > 20000 THEN 'mayor'
                        ELSE 'menor'
                        END
            WHERE id_comprobacion = $SelID";

        $res_cc = DB_query($UPD_CC, $db);

        if ($res_cc) {
        prnMsgV20('Se ha modificado el ID Centro de Costo # : '.$id_comprobacion.' ...','success');
        } else {
        $Msg = 'Error al modificar el Centro de Costo #: '.$id_comprobacion.'';
        $MsgC = 'red';
        }

        $vSaleM = 'OUT';
        } else {
        $Msg = 'ID del Centro de Costo no proporcionado';
        $MsgC = 'red';
        }
}

///////enviar de nuevo comprobacion////
if ($_POST['BtnSav'] == 'Enviar'){

        $UPD = "UPDATE CG_comprobacion
        SET estatus = 'enviado'
        WHERE id_comprobacion = $SelID";

        $res = DB_query($UPD, $db);

        if ($res) {
        prnMsgV20('Se ha enviado la comprobación de gastos','success');
        } else {
        $Msg = 'Error al enviar la comprobación de gastos #: '.$id_comprobacion.'';
        $MsgC = 'red';
        }

        $vSaleM = 'OUT';

}

// al dar click en guardar insertar los datos de los campos en la database
if ($_POST['BtnSav'] == 'Guardar') {
    // Verifica que los datos no estén vacíos
    $obj_gasto = trim($_POST['id_obj_gasto']);
    $empleado = trim($_POST['empleado']);
    $cc = trim($_POST['id_centro_costos']);
    $ga = trim($_POST['id_ga']);
    $cuenta = trim($_POST['id_cuenta_contable']);
    $desc = trim($_POST['id_desc_gasto']);
    $proveedor = trim($_POST['proveedor']);
    $estado = 'Por completar';
    
    if (!empty($obj_gasto) && !empty($empleado) && !empty($cc) && !empty($ga) && !empty($cuenta) && !empty($desc) &&!empty($proveedor)) {
        // Prepara la consulta SQL con marcadores de posición
        $sql = "INSERT INTO CG_comprobacion (obj_gasto, empleado, centro_de_costo, grupo_articulos, cuenta_contable, desc_gasto, proveedor, estatus) VALUES (?,?, ?, ?, ?,?,?,?)";
        
        // Prepara la consulta
        if ($stmt = mysqli_prepare($db, $sql)) {
            // Vincula las variables a la consulta preparada. primer parametro string 's' segundo float 'd' por eso sd
            mysqli_stmt_bind_param($stmt, "ssssisss", $obj_gasto, $empleado, $cc, $ga, $cuenta, $desc, $proveedor, $estado);

            // Ejecuta la consulta
            if (mysqli_stmt_execute($stmt)) {
                $Msg = 'exito';
                $MsgC = 'green'; 
                // Limpia los datos del formulario
                unset($_POST['id_obj_gasto']);
                unset($_POST['empleado']);
                unset($_POST['id_centro_costos']);
                unset($_POST['id_ga']);
                unset($_POST['id_cuenta_contable']);
                unset($_POST['id_desc_gasto']);
                unset($_POST['proveedor']);
                $vSaleM = 'OUT';
     
            } else {
                $Msg = 'Error al agregar el objetivo del gasto.';
                $MsgC = 'red';
            }
            
            // Cierra la declaración
            mysqli_stmt_close($stmt);
            
        }
    
    } else {
        $Msg = 'Error en la preparación de la consulta. Favor de llenar todos los campos: ';        
        $MsgC = 'red'; // El color rojo indica un error
    }
}

// Verifica si el parámetro 'A' en la URL es igual a 'vEli', lo que indica que se debe realizar una eliminación.
if ($_GET['A']=='vEli') {
    // Construye una consulta SQL para eliminar el ID seleccionado.
        $Del_CC = "Delete FROM CG_comprobacion WHERE id_comprobacion =".$SelID;
        $Res_CC = DB_query($Del_CC,$db);  
        prnMsgV20('Se ha eliminado el tipo de gasto : '.$SelID.' ...','success');
}

// Verifica si el parámetro 'A' en la URL es igual a 'CRej', lo que indica que se debe realizar un rechazo de la solicitud.
if ($_POST['BtnRej'] == 'Rechazar') {
    // Verifica que los datos no estén vacíos
    $coment = $_POST['coments'];
    
    if (!empty($coment)) {
        // Limpia el comentario para prevenir inyección SQL
        $coment = mysqli_real_escape_string($db, $coment);
        
        // Prepara la consulta SQL correctamente
        $sql = "UPDATE CG_comprobacion
                SET estatus = 'Rechazado',
                comentarios = '$coment'
                WHERE id_comprobacion = " . intval($SelID);
        
        // Ejecuta la consulta
        $Res = DB_query($sql, $db);
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
        

// Verifica si el parámetro 'A' en la URL es igual a 'CAprov', lo que indica que se debe aprovar la solicitud.
if ($_GET['A']=='CAprov') {
    // Construye una consulta SQL para eliminar el ID seleccionado.
        $Del_CC = "UPDATE CG_comprobacion
                   SET estatus = 'Aprobado' 
                   WHERE id_comprobacion =".$SelID;
        $Res_CC = DB_query($Del_CC,$db);  
        prnMsgV20('Se ha aprovado la solicitud: '.$SelID.' ...','success');
}

// esto es al dar click en la lupa
// si A toma el valor de C entonces inserta estas variables en el formulario para revisar la informacion de la facturacion
if ($_GET['A']=='C') {
    $Sql_c = "SELECT * FROM CG_comprobacion WHERE id_comprobacion = '".$SelID."'";
    $Res_c = DB_query($Sql_c, $db);
    $Row_c = DB_fetch_array($Res_c);
    
    $_POST['obj_gasto'] = $Row_c['obj_gasto'];
    $_POST['proovedor_cons'] = $Row_c['proveedor'];
    $_POST['empleado_cons'] = $Row_c['empleado'];
    $_POST['cc_cons'] = $Row_c['centro_de_costo'];
    $_POST['ga_cons'] = $Row_c['grupo_articulos'];
    $_POST['cuenta_cons'] = $Row_c['cuenta_contable'];
    $_POST['desc_gasto_cons'] = $Row_c['desc_gasto'];
    $_POST['importe'] = $Row_c['importe'];
    $_POST['iva'] = $Row_c['iva'];
    $_POST['iva_0'] = $Row_c['iva_tasa_0'];
    $_POST['excento'] = $Row_c['excento'];
    $_POST['no_deducible'] = $Row_c['no_deducible'];
    $_POST['ret_iva'] = $Row_c['ret_iva'];
    $_POST['ret_isr_10'] = $Row_c['ret_isr_10'];
    $_POST['ret_isr_125'] = $Row_c['ret_isr_125'];
    $_POST['total'] = $Row_c['total'];

}

// consultar los comentarios de las solicitudes rechazadas
// si A toma el valor de inf haz la consulta del comentario para ese id
if ($_GET['A']=='vinfo') {
    $Sql_inf = "SELECT Comentarios FROM CG_comprobacion WHERE id_comprobacion = '".$SelID."'";
    $Res_inf = DB_query($Sql_inf, $db);
    $Row_c = DB_fetch_array($Res_inf);
    
    $_POST['informacion'] = $Row_c['Comentarios'];


}



//container principal donde va a estar la tabla 
echo '<div class="container" style="margin-top:80px">
    <div class="row">
        <div class="col-sm-12">
            <div class="card text-left">
                <div class="card-body">
                    <h4>Comprobación de Gastos</h4>';
                    if ($Msg<>'')
                        echo '<p style="text-align: center;font-family:Arial;font-size:16px;color:'.$MsgC.'">'.$Msg.'</p>';
                            //<!-- Párrafo con estilo centrado, fuente Arial, tamaño 16px y color determinado por la variable $MsgC -->
                        echo '<a href="CG_001_001n.php?A=vNew" class="btn btn-primary" role="button">
                        Agregar
                        <span class="pl-2 fas fa-plus-circle"></span>
                        </a>';
                    echo '<div id="tablaComprobacion_Gasto_001_001"></div>';
                    //Contenedor vacío con el ID 'tablaCG_001_001' para cargar dinámicamente la tabla de centro de costos
echo'           </div>
            </div>
        </div>
    </div>
</div>';

if ($_GET['A']=='vNew') {
    $TituloModal = 'Agregar Nuevo Gasto';
    $_POST['estatus'] = 1;
  }




/***
* Modal Pantalla de Datos para agregar nuevo gasto
*/
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
echo'           <div class = "modal-body">
                <div class = "row">
                    <div class="col-sm-12">';
echo'                   <div class="slic-form-group row">
                            <label for="obj_gasto" class="col-sm-3 col-slic-label">Obj del Gasto:</label>
                                <div class = "col-sm-4">
                                    <select 
                                        class="slic-input" 
                                        name="id_obj_gasto"
                                        id = "id_obj_gasto"
                                        onchange="submit()">';

                                        // Default value for the dropdown
                                        $selected_obj_gasto = isset($_POST['id_obj_gasto']) ? htmlspecialchars($_POST['id_obj_gasto'], ENT_QUOTES, 'UTF-8') : '';
                                            
                                        // Consulta SQL para obtener las opciones del combobox
                                        $sql_obj_gasto = "SELECT DISTINCT obj_gasto
                                                            FROM CG_anticipos WHERE
                                                            empleado = '$nombre_usuario' and status = 'A'";
                                        $result_obj_gasto = DB_query($sql_obj_gasto, $db);

                                        echo '<option value="">Seleccione un Objetivo del Gasto:</option>';

                                        // Loop a través de los resultados de la consulta
                                        while ($row_obj_gasto = DB_fetch_array($result_obj_gasto)) {

                                            // mandarle el valor de la fila a la variable desc_gasto
                                                $obj_gasto = htmlspecialchars($row_obj_gasto['obj_gasto'], ENT_QUOTES, 'UTF-8');
                                            
                                            //si desc_gasto es igual a selected_desc_gasto la variable isSelected_desc_gasto se pondra en el combobox
                                                $isSelected_obj_gasto = ($obj_gasto === $selected_obj_gasto) ? 'selected' : '';
                                                
                                                echo '<option value="' . $obj_gasto . '" ' . $isSelected_obj_gasto . '>'
                                                    . $obj_gasto . '</option>';
                                            }
echo'                               </select>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="proveedor" class="col-sm-3 col-slic-label">Proovedor:</label>
                                <div class = "col-sm-4">';
                                    
                                    // Mantener el valor del input "proveedor" si ya fue enviado
                                    $proveedor = isset($_POST['proveedor']) ? htmlspecialchars($_POST['proveedor'], ENT_QUOTES, 'UTF-8') : '';

echo'                               <input 
                                        type = "text"
                                        class="slic-input" 
                                        name="proveedor"
                                        id="proveedor"
                                        value="' . $proveedor . '"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="empleado" class="col-sm-3 col-slic-label">Empleado:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="empleado_display"
                                        id="empleado_display"
                                        value="' . htmlspecialchars($nombre_usuario) . '"
                                        readonly
                                    />
                                    <input 
                                        type="hidden"
                                        name="empleado"
                                        id="empleado"
                                        value="' . htmlspecialchars($nombre_usuario) . '"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="centro_costos" class="col-sm-3 col-slic-label">Centro de Costos:</label>
                                <div class = "col-sm-4">
                                    <select 
                                        class="slic-input" 
                                        name="id_centro_costos"
                                        id = "id_centro_costos"
                                        onchange="submit()">';
                                                        // Default value for the dropdown
                                        $selectedcc = isset($_POST['id_centro_costos']) ? htmlspecialchars($_POST['id_centro_costos'], ENT_QUOTES, 'UTF-8') : '';

                                        // Consulta SQL para obtener las opciones del combobox
                                        $sql = "SELECT DISTINCT centro_de_costo, desc_centro_de_costo FROM CG_datos_contables";
                                        $result = DB_query($sql, $db);

                                        // Agregar opción por defecto
                                        echo '<option value="">Seleccione un Centro de Costo</option>';

                                        // Loop a través de los resultados de la consulta
                                        while ($row = DB_fetch_array($result)) {
                                            $centro_de_costo = htmlspecialchars($row['centro_de_costo'], ENT_QUOTES, 'UTF-8');
                                            $desc_cc = htmlspecialchars($row['desc_centro_de_costo'], ENT_QUOTES, 'UTF-8');
                                            $isSelected = ($centro_de_costo === $selectedcc) ? 'selected' : '';
                                            echo '<option value="' . $centro_de_costo . '" ' . $isSelected . '>'
                                            . $centro_de_costo . ' - ' . $desc_cc . '</option>';
                                        }
                            echo '  </select>
                                </div>
                        </div>';

                                ///segundo select grupo de articulos
echo'                   <div class="slic-form-group row">
                            <label for="grupo_articulos" class="col-sm-3 col-slic-label">Grupo de artículos:</label>
                                <div class = "col-sm-4">
                                    <select 
                                        class="slic-input" 
                                        name="id_ga"
                                        id = "id_ga"
                                        onchange="submit()">';
                                        // Default value for the dropdown
                                        $selectedGa = isset($_POST['id_ga']) ? htmlspecialchars($_POST['id_ga'], ENT_QUOTES, 'UTF-8') : '';

                                        // Ensure that we use the selected centro_de_costo in the second dropdown query
                                        $centro_costo_value = isset($_POST['id_centro_costos']) ? htmlspecialchars($_POST['id_centro_costos'], ENT_QUOTES, 'UTF-8') : '';
                                        if ($centro_costo_value) {
                                            // Consulta SQL para obtener las opciones del combobox
                                            $sql_ga = "SELECT DISTINCT grupo_articulos, desc_grupo_articulos 
                                                    FROM CG_datos_contables
                                                    WHERE centro_de_costo = '" . $centro_costo_value . "'";
                                            $result_ga = DB_query($sql_ga, $db);

                                            // Agregar opción por defecto
                                            echo '<option value="">Seleccione un Grupo de Artículos</option>';

                                            // Loop a través de los resultados de la consulta
                                            while ($row = DB_fetch_array($result_ga)) {

                                            // mandarle el valor de la fila a la variable grupo_articulos
                                                $grupo_articulos = htmlspecialchars($row['grupo_articulos'], ENT_QUOTES, 'UTF-8');

                                            //mandarlel el valor de la fila a la variable desc_grupo_articulos
                                                $desc_grupo_articulos = htmlspecialchars($row['desc_grupo_articulos'], ENT_QUOTES, 'UTF-8');
                                            
                                            //si grupo_articulos es igual a selectedGa la variable isSelected se pondra en el combobox
                                                $isSelected = ($grupo_articulos === $selectedGa) ? 'selected' : '';
                                                echo '<option value="' . $grupo_articulos . '" ' . $isSelected . '>'
                                                    . $grupo_articulos . ' - ' . $desc_grupo_articulos . '</option>';
                                            }
                                        } else {
                                            // If no centro_costo selected, display a default option
                                            echo '<option value="">Seleccione un Grupo de Artículos</option>';
                                        }
echo'                               </select>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="cuenta_contable" class="col-sm-3 col-slic-label">Cuenta Contable:</label>
                                <div class = "col-sm-4">
                                    <select 
                                        class="slic-input" 
                                        name="id_cuenta_contable"
                                        id = "id_cuenta_contable"
                                        onchange="submit()">';
                                        // Default value for the dropdown
                                        $selected_cuenta_contable = isset($_POST['id_cuenta_contable']) ? htmlspecialchars($_POST['id_cuenta_contable'], ENT_QUOTES, 'UTF-8') : '';

                                        // Ensure that we use the selected grupo_articulos in the third dropdown
                                        $grupo_articulos_value = isset($_POST['id_ga']) ? htmlspecialchars($_POST['id_ga'], ENT_QUOTES, 'UTF-8') : '';

                                        if ($grupo_articulos_value) {
                                            // Consulta SQL para obtener las opciones del combobox
                                            $sql_cuenta_contable = "SELECT DISTINCT cuenta_contable
                                                                    FROM CG_datos_contables
                                                                    WHERE grupo_articulos = '" . $grupo_articulos_value . "'";
                                            $result_cuenta_contable = DB_query($sql_cuenta_contable, $db);

                                            // Loop a través de los resultados de la consulta
                                            while ($row_cuenta_contable = DB_fetch_array($result_cuenta_contable)) {

                                                // mandarle el valor de la fila a la variable grupo_articulos
                                                    $cuenta_contable = htmlspecialchars($row_cuenta_contable['cuenta_contable'], ENT_QUOTES, 'UTF-8');
                                                
                                                //si cuenta_contable es igual a selected_cuenta_contable la variable isSelected_cuenta_contable se pondra en el combobox
                                                    $isSelected_cuenta_contable = ($cuenta_contable === $selected_cuenta_contable) ? 'selected' : '';
                                                    echo '<option value="' . $cuenta_contable . '" ' . $isSelected_cuenta_contable . '>'
                                                        . $cuenta_contable . '</option>';
                                                }
                                            } else {
                                                // If no cuenta_contable selected, display a default option
                                                echo '<option value="">Seleccione una Cuenta Contable</option>';
                                            }
echo'                               </select>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="desc_gasto" class="col-sm-3 col-slic-label">Descripción del Gasto:</label>
                                <div class = "col-sm-4">
                                    <select 
                                        class="slic-input" 
                                        name="id_desc_gasto"
                                        id = "id_desc_gasto"
                                        onchange="submit()">';
                                        // Default value for the dropdown
                                        $selected_desc_gasto = isset($_POST['id_desc_gasto']) ? htmlspecialchars($_POST['id_desc_gasto'], ENT_QUOTES, 'UTF-8') : '';
                                        
                                        // Consulta SQL para obtener las opciones del combobox
                                        if ($selectedcc == 5210530 ) {
                                            $sql_desc_gasto = "SELECT DISTINCT desc_gasto FROM CG_gastos";
                                        } else {
                                            $sql_desc_gasto = "SELECT DISTINCT desc_gasto FROM CG_gastos WHERE depto = 'A'";
                                        }
                                        // $sql_desc_gasto = "SELECT DISTINCT desc_gasto
                                        //                     FROM CG_gastos";
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
                        </div>
                    </div>
                </div>
                </div>';
echo '          <div class="modal-footer">';
echo '          <input type=Submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>';
                if ($_GET['A']=='vNew') 
echo '              <input type=Submit class="btn btn-primary" name=BtnSav value=Guardar>';
                if ($_GET['A']=='vMod')
echo '              <input type=Submit class="btn btn-primary" name=BtnSav value=Modificar>';
echo'           </div>
            </div>
        </div>
    </div>';

/////////MODAL ELIMINAR///////////////////
echo '<div class="modal fade" id="ConfirmaDel" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
       <div class="modal-content">
          <div class="modal-header">
             <h5 class="modal-title">Eliminar Gasto</h5>
             <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
             <p>Confirme para eliminar el gasto ID #'.$SelID .'</p>
          </div>
          <div class="modal-footer">
             <input type=submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>
             <a href="CG_001_001n.php?A=vEli&ID='.$SelID .'" class="btn btn-primary" 
                role=button >Eliminar</a>
          </div>
       </div>
    </div>
 </div>';
/****///////////////////////

/////////MODAL RECHAZAR/////////////
echo '<div class="modal fade" id="Rechazar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
       <div class="modal-content">
          <div class="modal-header">
             <h5 class="modal-title">Rechazar Gasto</h5>
             <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <p>Confirme para rechazar el gasto ID #'.$SelID .'</p>
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

/****/////////////////////////

/////////MODAL APROVAR/////////
echo '<div class="modal fade" id="Aprovar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
       <div class="modal-content">
          <div class="modal-header">
             <h5 class="modal-title">Aprovar Gasto</h5>
             <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
             <p>Confirme para aprovar el gasto ID #'.$SelID .'</p>
          </div>
          <div class="modal-footer">
             <input type=submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>
             <a href="CG_001_001n.php?A=CAprov&ID='.$SelID .'" class="btn btn-primary" 
                role=button >Aprovar</a>
          </div>
       </div>
    </div>
 </div>';
/****///////////////////////////

///MODAL PARA CONSULTAR DATOS DEL ID DE LA FACTURACION///
echo '<div class="modal fade" id="CONS_DATOS" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">COMPROBACIÓN DE GASTOS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
echo'           <div class = "modal-body">
                <div class = "row">
                    <div class="col-sm-12">';
echo'                   <div class="slic-form-group row">
                            <label for="obj_gasto" class="col-sm-3 col-slic-label">Obj del Gasto:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="obj_gasto"
                                        id = "obj_gasto"
                                        value = "'.$_POST['obj_gasto'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="obj_gasto"
                                        id="obj_gasto"
                                        value="'.$_POST['obj_gasto'].'"
                                    />
                        </div>
                    </div>';
echo'                   <div class="slic-form-group row">
                            <label for="proveedor" class="col-sm-3 col-slic-label">Proovedor:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="proveedor_cons"
                                        id = "proovedor_cons"
                                        value = "'.$_POST['proovedor_cons'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="proovedor_cons"
                                        id="proovedor_cons"
                                        value="'.$_POST['proovedor_cons'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="empleado" class="col-sm-3 col-slic-label">Empleado:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="empleado_cons"
                                        id = "empleado_cons"
                                        value = "'.$_POST['empleado_cons'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="empleado_cons"
                                        id="empleado_cons"
                                        value="'.$_POST['empleado_cons'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="centro_costos" class="col-sm-3 col-slic-label">Centro de Costos:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="cc_cons"
                                        id = "cc_cons"
                                        value = "'.$_POST['cc_cons'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="cc_cons"
                                        id="cc_cons"
                                        value="'.$_POST['cc_cons'].'"
                                    />
                                </div>
                        </div>';

                                ///segundo select grupo de articulos
echo'                   <div class="slic-form-group row">
                            <label for="grupo_articulos" class="col-sm-3 col-slic-label">Grupo de artículos:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="ga_cons"
                                        id = "ga_cons"
                                        value = "'.$_POST['ga_cons'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="ga_cons"
                                        id="ga_cons"
                                        value="'.$_POST['ga_cons'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="cuenta_contable" class="col-sm-3 col-slic-label">Cuenta Contable:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="cuenta_cons"
                                        id = "cuenta_cons"
                                        value = "'.$_POST['cuenta_cons'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="cuenta_cons"
                                        id="cuenta_cons"
                                        value="'.$_POST['cuenta_cons'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="desc_gasto" class="col-sm-3 col-slic-label">Descripción del Gasto:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="desc_gasto_cons"
                                        id = "desc_gasto_cons"
                                        value = "'.$_POST['desc_gasto_cons'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="desc_gasto_cons"
                                        id="desc_gasto_cons"
                                        value="'.$_POST['desc_gasto_cons'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="importe" class="col-sm-3 col-slic-label">Importe:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="importe"
                                        id = "importe"
                                        value = "'.$_POST['importe'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="importe"
                                        id="importe"
                                        value="'.$_POST['importe'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="iva" class="col-sm-3 col-slic-label">IVA:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="iva"
                                        id = "iva"
                                        value = "'.$_POST['iva'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="iva"
                                        id="iva"
                                        value="'.$_POST['iva'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="iva_0" class="col-sm-3 col-slic-label">IVA tasa 0:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="iva_0"
                                        id = "iva_0"
                                        value = "'.$_POST['iva_0'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="iva_0"
                                        id="iva_0"
                                        value="'.$_POST['iva_0'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="excento" class="col-sm-3 col-slic-label">Excento:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="excento"
                                        id = "excento"
                                        value = "'.$_POST['excento'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="excento"
                                        id="excento"
                                        value="'.$_POST['excento'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="no_deducible" class="col-sm-3 col-slic-label">No deducible:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="no_deducible"
                                        id = "no_deducible"
                                        value = "'.$_POST['no_deducible'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="no_deducible"
                                        id="no_deducible"
                                        value="'.$_POST['no_deducible'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="ret_iva" class="col-sm-3 col-slic-label">Ret. IVA:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="ret_iva"
                                        id = "ret_iva"
                                        value = "'.$_POST['ret_iva'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="ret_iva"
                                        id="ret_iva"
                                        value="'.$_POST['ret_iva'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="ret_isr_10" class="col-sm-3 col-slic-label">Ret. ISR 10%:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="ret_isr_10"
                                        id = "ret_isr_10"
                                        value = "'.$_POST['ret_isr_10'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="ret_isr_10"
                                        id="ret_isr_10"
                                        value="'.$_POST['ret_isr_10'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="ret_isr_125" class="col-sm-3 col-slic-label">Ret. ISR 1.25%:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="ret_isr_125"
                                        id = "ret_isr_125"
                                        value = "'.$_POST['ret_isr_125'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="ret_isr_125"
                                        id="ret_isr_125"
                                        value="'.$_POST['ret_isr_125'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                        <label for="total" class="col-sm-3 col-slic-label">Total:</label>
                            <div class = "col-sm-4">
                                <input 
                                    class="slic-input" 
                                    name="total"
                                    id = "total"
                                    value = "'.$_POST['total'].'"
                                    readonly>';
echo'                               </input>
                                <input 
                                    type="hidden"
                                    name="total"
                                    id="total"
                                    value="'.$_POST['total'].'"
                                />
                            </div>
                    </div>
                    </div>
                </div>
                </div>';
echo '          <div class="modal-footer">';
echo '          <input type=Submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>';
echo'           </div>
            </div>
        </div>
    </div>';
//////////////////////////////

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

/////////MODAL MODIFICAR/////////////
echo '<div class="modal fade" id="MOD" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modificar Formulario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
echo'           <div class = "modal-body">
                <div class = "row">
                    <div class="col-sm-12">';
echo'                   <div class="slic-form-group row">
                            <label for="obj_gasto" class="col-sm-3 col-slic-label">Obj del Gasto:</label>
                                <div class = "col-sm-4">
                                    <select 
                                            class="slic-input" 
                                            name="mod_obj_gasto"
                                            id = "mod_obj_gasto"
                                            onchange="submit()">';
                                            // Default value for the dropdown
                                            $seleccion_obj_gasto = isset($_POST['mod_obj_gasto']) ? htmlspecialchars($_POST['mod_obj_gasto'], ENT_QUOTES, 'UTF-8') : '';
                                            
                                            // Consulta SQL para obtener las opciones del combobox
                                            $sql_obj_gasto = "SELECT DISTINCT obj_gasto
                                                            FROM CG_anticipos WHERE
                                                            empleado = '$nombre_usuario' and status = 'A'";
                                            $result_obj_gasto = DB_query($sql_obj_gasto, $db);

                                            echo '<option value="' . htmlspecialchars($valor_obj_gasto) . '">' . htmlspecialchars($valor_obj_gasto) . '</option>';

                                            // Loop a través de los resultados de la consulta
                                            while ($row_obj_gasto = DB_fetch_array($result_obj_gasto)) {

                                                // mandarle el valor de la fila a la variable desc_gasto
                                                    $obj_gasto_2 = htmlspecialchars($row_obj_gasto['obj_gasto'], ENT_QUOTES, 'UTF-8');
                                                
                                                //si desc_gasto es igual a selected_desc_gasto la variable isSelected_desc_gasto se pondra en el combobox
                                                    $isSelected_obj_gasto_2 = ($obj_gasto_2 === $seleccion_obj_gasto) ? 'selected' : '';
                                                    
                                                    echo '<option value="' . $obj_gasto_2 . '" ' . $isSelected_obj_gasto_2 . '>'
                                                        . $obj_gasto_2 . '</option>';
                                                }
    echo'                               </select>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="proveedor" class="col-sm-3 col-slic-label">Proovedor:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="mod_prov"
                                        id = "mod_prov"
                                        value = "'.$_POST['proovedor_cons'].'">';
echo'                               </input>
                                    
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="empleado" class="col-sm-3 col-slic-label">Empleado:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="empleado_cons"
                                        id = "empleado_cons"
                                        value = "'.$_POST['empleado_cons'].'"
                                        readonly>';
echo'                               </input>
                                    <input 
                                        type="hidden"
                                        name="empleado_cons"
                                        id="empleado_cons"
                                        value="'.$_POST['empleado_cons'].'"
                                    />
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="centro_costos" class="col-sm-3 col-slic-label">Centro de Costos:</label>
                                <div class = "col-sm-4">
                                    <select 
                                        class="slic-input" 
                                        name="mod_centro_costos"
                                        id = "mod_centro_costos"
                                        onchange="submit()">';
                                                        // Default value for the dropdown
                                        $selected_centro = isset($_POST['mod_centro_costos']) ? htmlspecialchars($_POST['mod_centro_costos'], ENT_QUOTES, 'UTF-8') : '';

                                        // Consulta SQL para obtener las opciones del combobox
                                        $sql = "SELECT DISTINCT centro_de_costo, desc_centro_de_costo FROM CG_datos_contables";
                                        $result = DB_query($sql, $db);

                                        // Agregar opción por defecto
                                        echo '<option value="' . htmlspecialchars($valor_cc) . '">' . htmlspecialchars($valor_cc) . '</option>';

                                        // Loop a través de los resultados de la consulta
                                        while ($row = DB_fetch_array($result)) {
                                            $centro_de_costo_2 = htmlspecialchars($row['centro_de_costo'], ENT_QUOTES, 'UTF-8');
                                            $desc_cc_2 = htmlspecialchars($row['desc_centro_de_costo'], ENT_QUOTES, 'UTF-8');
                                            $isSelected_centro = ($centro_de_costo_2 === $selected_centro) ? 'selected' : '';
                                            echo '<option value="' . $centro_de_costo_2 . '" ' . $isSelected_centro . '>'
                                            . $centro_de_costo_2 . ' - ' . $desc_cc_2 . '</option>';
                                        }
                            echo '  </select>
                                </div>
                        </div>';
                                ///segundo select grupo de articulos
echo'                   <div class="slic-form-group row">
                            <label for="grupo_articulos" class="col-sm-3 col-slic-label">Grupo de artículos:</label>
                                <div class = "col-sm-4">
                                    <select 
                                        class="slic-input" 
                                        name="mod_ga"
                                        id = "mod_ga"
                                        onchange="submit()">';
                                        // Default value for the dropdown
                                        $selectedGa_2 = isset($_POST['mod_ga']) ? htmlspecialchars($_POST['mod_ga'], ENT_QUOTES, 'UTF-8') : '';

                                        // Ensure that we use the selected centro_de_costo in the second dropdown query
                                        $centro_costo_value_2 = isset($_POST['mod_centro_costos']) ? htmlspecialchars($_POST['mod_centro_costos'], ENT_QUOTES, 'UTF-8') : '';
                                        if ($centro_costo_value_2) {
                                            // Consulta SQL para obtener las opciones del combobox
                                            $sql_ga = "SELECT DISTINCT grupo_articulos, desc_grupo_articulos 
                                                    FROM CG_datos_contables
                                                    WHERE centro_de_costo = '" . $centro_costo_value_2 . "'";
                                            $result_ga = DB_query($sql_ga, $db);

                                            
                                            // Loop a través de los resultados de la consulta
                                            while ($row = DB_fetch_array($result_ga)) {

                                            // mandarle el valor de la fila a la variable grupo_articulos
                                                $grupo_articulos_2 = htmlspecialchars($row['grupo_articulos'], ENT_QUOTES, 'UTF-8');

                                            //mandarlel el valor de la fila a la variable desc_grupo_articulos
                                                $desc_grupo_articulos_2 = htmlspecialchars($row['desc_grupo_articulos'], ENT_QUOTES, 'UTF-8');
                                            
                                            //si grupo_articulos es igual a selectedGa la variable isSelected se pondra en el combobox
                                                $isSelected_ga = ($grupo_articulos_2 === $selectedGa_2) ? 'selected' : '';
                                                echo '<option value="' . $grupo_articulos_2 . '" ' . $isSelected_ga . '>'
                                                    . $grupo_articulos_2 . ' - ' . $desc_grupo_articulos_2 . '</option>';
                                            }
                                        } else {
                                            // Agregar opción por defecto
                                            echo '<option value="' . htmlspecialchars($valor_ga) . '">' . htmlspecialchars($valor_ga) . '</option>';
                                        }
echo'                               </select>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="cuenta_contable" class="col-sm-3 col-slic-label">Cuenta Contable:</label>
                                <div class = "col-sm-4">
                                    <select 
                                        class="slic-input" 
                                        name="mod_cuenta_contable"
                                        id = "mod_cuenta_contable"
                                        onchange="submit()">';
                                        // Default value for the dropdown
                                        $selected_cuenta_contable_2 = isset($_POST['mod_cuenta_contable']) ? htmlspecialchars($_POST['mod_cuenta_contable'], ENT_QUOTES, 'UTF-8') : '';

                                        // Ensure that we use the selected grupo_articulos in the third dropdown
                                        $grupo_articulos_value_2 = isset($_POST['mod_ga']) ? htmlspecialchars($_POST['mod_ga'], ENT_QUOTES, 'UTF-8') : '';

                                        if ($grupo_articulos_value_2) {
                                            // Consulta SQL para obtener las opciones del combobox
                                            $sql_cuenta_contable = "SELECT DISTINCT cuenta_contable
                                                                    FROM CG_datos_contables
                                                                    WHERE grupo_articulos = '" . $grupo_articulos_value_2 . "'";
                                            $result_cuenta_contable = DB_query($sql_cuenta_contable, $db);

                                            // Loop a través de los resultados de la consulta
                                            while ($row_cuenta_contable = DB_fetch_array($result_cuenta_contable)) {

                                                // mandarle el valor de la fila a la variable grupo_articulos
                                                    $cuenta_contable_2 = htmlspecialchars($row_cuenta_contable['cuenta_contable'], ENT_QUOTES, 'UTF-8');
                                                
                                                //si cuenta_contable es igual a selected_cuenta_contable la variable isSelected_cuenta_contable se pondra en el combobox
                                                    $isSelected_cuenta_contable_2 = ($cuenta_contable_2 === $selected_cuenta_contable_2) ? 'selected' : '';
                                                    echo '<option value="' . $cuenta_contable_2 . '" ' . $isSelected_cuenta_contable_2 . '>'
                                                        . $cuenta_contable_2 . '</option>';
                                                }
                                            } else {
                                                // If no cuenta_contable selected, display a default option
                                                echo '<option value="' . htmlspecialchars($valor_cuenta) . '">' . htmlspecialchars($valor_cuenta) . '</option>';
                                            }
echo'                               </select>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="desc_gasto" class="col-sm-3 col-slic-label">Descripción del Gasto:</label>
                                <div class = "col-sm-4">
                                    <select 
                                        class="slic-input" 
                                        name="mod_desc_gasto"
                                        id = "mod_desc_gasto"
                                        onchange="submit()">';
                                        // Default value for the dropdown
                                        $selected_desc_gasto_2 = isset($_POST['mod_desc_gasto']) ? htmlspecialchars($_POST['mod_desc_gasto'], ENT_QUOTES, 'UTF-8') : '';
                                        
                                        // Consulta SQL para obtener las opciones del combobox
                                        $sql_desc_gasto = "SELECT DISTINCT desc_gasto
                                                            FROM CG_gastos";
                                        $result_desc_gasto = DB_query($sql_desc_gasto, $db);

                                        echo '<option value="' . htmlspecialchars($valor_desc_gasto) . '">' . htmlspecialchars($valor_desc_gasto) . '</option>';
                                        
                                        // Loop a través de los resultados de la consulta
                                        while ($row_desc_gasto = DB_fetch_array($result_desc_gasto)) {

                                            // mandarle el valor de la fila a la variable desc_gasto
                                                $desc_gasto_2 = htmlspecialchars($row_desc_gasto['desc_gasto'], ENT_QUOTES, 'UTF-8');
                                            
                                            //si desc_gasto es igual a selected_desc_gasto la variable isSelected_desc_gasto se pondra en el combobox
                                                $isSelected_desc_gasto_2 = ($desc_gasto_2 === $selected_desc_gasto_2) ? 'selected' : '';
                                                
                                                echo '<option value="' . $desc_gasto_2 . '" ' . $isSelected_desc_gasto_2 . '>'
                                                    . $desc_gasto_2 . '</option>';
                                            }
echo'                               </select>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="importe" class="col-sm-3 col-slic-label">Importe:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="mod_importe"
                                        id = "mod_importe"
                                        value = "'.$_POST['importe'].'"';
echo'                               </input>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="descuento" class="col-sm-3 col-slic-label">Descuento:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="mod_descuento"
                                        id = "mod_descuento"
                                        value = "'.$_POST['descuento'].'"';
echo'                               </input>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="iva" class="col-sm-3 col-slic-label">IVA:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="mod_iva"
                                        id = "mod_iva"
                                        value = "'.$_POST['iva'].'"';
echo'                               </input>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="ieps" class="col-sm-3 col-slic-label">IEPS:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="mod_ieps"
                                        id = "mod_ieps"
                                        value = "'.$_POST['ieps'].'"';
echo'                               </input>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="ish" class="col-sm-3 col-slic-label">ISH:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="mod_ish"
                                        id = "mod_ish"
                                        value = "'.$_POST['ish'].'"';
echo'                               </input>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="iva_0" class="col-sm-3 col-slic-label">IVA tasa 0:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="mod_iva_0"
                                        id = "mod_iva_0"
                                        value = "'.$_POST['iva_0'].'"';
echo'                               </input>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="excento" class="col-sm-3 col-slic-label">Excento:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="mod_excento"
                                        id = "mod_excento"
                                        value = "'.$_POST['excento'].'"';
echo'                               </input>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="no_deducible" class="col-sm-3 col-slic-label">No deducible:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="mod_no_deducible"
                                        id = "mod_no_deducible"
                                        value = "'.$_POST['no_deducible'].'"';
echo'                               </input>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="iva_no_deducible" class="col-sm-3 col-slic-label">IVA No deducible:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="mod_iva_no_deducible"
                                        id = "mod_iva_no_deducible"
                                        value = "'.$_POST['iva_no_deducible'].'"';
echo'                               </input>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                        <label for="propina" class="col-sm-3 col-slic-label">Propina:</label>
                            <div class = "col-sm-4">
                                <input 
                                    class="slic-input" 
                                    name="mod_propina"
                                    id = "mod_propina"
                                    value = "'.$_POST['propina'].'"';
echo'                               </input>
                            </div>
                    </div>';                        
echo'                   <div class="slic-form-group row">
                            <label for="ret_iva" class="col-sm-3 col-slic-label">Ret. IVA:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="mod_ret_iva"
                                        id = "mod_ret_iva"
                                        value = "'.$_POST['ret_iva'].'"';
echo'                               </input>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="ret_isr_10" class="col-sm-3 col-slic-label">Ret. ISR 10%:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="mod_ret_isr_10"
                                        id = "mod_ret_isr_10"
                                        value = "'.$_POST['ret_isr_10'].'"';
echo'                               </input>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                            <label for="ret_isr_125" class="col-sm-3 col-slic-label">Ret. ISR 1.25%:</label>
                                <div class = "col-sm-4">
                                    <input 
                                        class="slic-input" 
                                        name="mod_ret_isr_125"
                                        id = "mod_ret_isr_125"
                                        value = "'.$_POST['ret_isr_125'].'"';
echo'                               </input>
                                </div>
                        </div>';
echo'                   <div class="slic-form-group row">
                        <label for="total" class="col-sm-3 col-slic-label">Total:</label>
                            <div class = "col-sm-4">
                                <input 
                                    class="slic-input" 
                                    name="mod_total"
                                    id = "mod_total"
                                    value = "'.$_POST['total'].'"
                                    readonly>';
echo'                               </input>
                            </div>
                    </div>
                    </div>
                </div>
                </div>';
echo '          <div class="modal-footer">';
echo '          <input type=Submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>';
echo'           <input type=Submit class="btn btn-primary" name=BtnSav value=Modificar>';
// echo'           <input type=Submit class="btn btn-success" name=BtnSav value=Enviar>';
echo'           </div>
            </div>
        </div>
    </div>';
//////////////////////////////




//script para activar el modal de nuevo centro de costo
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

// script para activar el modal de rechazar gasto
if ($_GET['A']=='Rej'and $vSaleM == 'IN') {
    ?>
    <script type="text/javascript">
        $('#Rechazar').modal('show');
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

// script para activar el modal de consulta de datos
if ($_GET['A']=='C') {
    ?>
    <script type="text/javascript">
        $('#CONS_DATOS').modal('show');
    </script>
    <?php   
}

// script para activar el modal de ver comentarios
if ($_GET['A']=='vinfo') {
    ?>
    <script type="text/javascript">
        $('#INFO').modal('show');
    </script>
    <?php   
}

if ($_GET['A']=='vMod' and $vSaleM == 'IN') {
    ?>
    <script type="text/javascript">
        $('#MOD').modal('show');
    </script>
    <?php   
}

// Incluye el archivo del pie de página
include('includes/footer.php');
?>

<!--Agregar el script donde esta la tabla -->
<script type="text/javascript">
   $(document).ready(function() {
      $('#tablaComprobacion_Gasto_001_001').load('CG_001_001t.php');
   });
</script>
</body>
</html>

