<?php
$PageSecurity = 2;
include('includes/session.inc');
require_once('Classes/PHPExcel.php');
?>
<?php
$SelID = $_GET['ID']; // Obtener el ID del archivo desde la URL
$SelID_e = $_GET['ID_e'];
$SelID_tr = $_GET['ID_tr'];
$SelID_P = $_GET['ID_P'];
$df_poliza = [];

$UPD_status = "UPDATE CG_polizas
                SET 
                    status = 'Poliza Generada'
                WHERE id_polizas = " . intval($SelID_P);;
                    
$res_upd = DB_query($UPD_status, $db);

$UPD_status_anticipo = "UPDATE CG_anticipos
                        LEFT JOIN CG_polizas ON CG_anticipos.id_anticipo = CG_polizas.id_anticipo
                        SET CG_anticipos.status = 'D'
                        WHERE CG_polizas.id_polizas = " . intval($SelID_P);
                    
$res_upd_st_anticipo = DB_query($UPD_status_anticipo, $db);


$array_ret_iva = [];
$sql_iva_ret = "SELECT
                    'C' as 'C/D',
                    216080 as 'cuenta_contable',
                    ROUND(ret_iva,2) as ret_iva,
                    '' AS empty_column_2,
                    '' AS empty_column_3,
                    CONCAT('IVA Ret','-',desc_gasto,'-', empleado, '-',obj_gasto)
                FROM CG_comprobacion
                WHERE obj_gasto = '".$SelID."' AND estatus = 'aprobado' AND empleado = '".$SelID_e."' and ret_iva > 0";
$Res_iva_ret = DB_query($sql_iva_ret, $db); // Ejecuta la consulta
// $Row_iva_ret = DB_fetch_array($Res_iva_ret); // Obtiene el resultado de la consulta -> obtener el iva retenido

$array_isr_125 = [];
$sql_isr_125 = "SELECT  'C' as 'C/D',
                        216065 as 'cuenta_contable',
                        ROUND(ret_isr_125,2) as ret_isr_125,
                        '' AS empty_column_2,
                        '' AS empty_column_3,
                    CONCAT('ISR 1.25','-',desc_gasto,'-', empleado, '-',obj_gasto)
                FROM CG_comprobacion
                WHERE obj_gasto = '".$SelID."' AND estatus = 'aprobado' AND empleado = '".$SelID_e."' and ret_isr_125 >0";
$Res_isr_125 = DB_query($sql_isr_125, $db); // Ejecuta la consulta
// $Row_isr_125 = DB_fetch_array($Res_isr_125); // Obtiene el resultado de la consulta -> obtener isr 1.25%
while($row_isr_125 = DB_fetch_row($Res_isr_125)) {
    $array_isr_125[] = $row_isr_125; // Agregar cada fila al array principal
}

$array_isr_10 = [];
$sql_isr_10 = "SELECT   'C' as 'C/D',
                        216060 AS 'cuenta_contable',
                        ROUND(ret_isr_10,2) as ret_isr_10,
                        '' AS empty_column_2,
                        '' AS empty_column_3,
                    CONCAT('ISR10','-',desc_gasto,'-', empleado, '-',obj_gasto)
                FROM CG_comprobacion
                WHERE obj_gasto = '".$SelID."' AND estatus = 'aprobado' AND empleado = '".$SelID_e."' and ret_isr_10>0";
$Res_isr_10 = DB_query($sql_isr_10, $db); // Ejecuta la consulta
// $Row_isr_10 = DB_fetch_array($Res_isr_10); // Obtiene el resultado de la consulta -> obtener isr 10%
while($row_isr_10 = DB_fetch_row($Res_isr_10)) {
    $array_isr_10[] = $row_isr_10; // Agregar cada fila al array principal
}

$array_iva = [];
$sql_iva = "SELECT  'D' as 'C/D',
                    130038 as 'cuenta_contable',
                    ROUND(iva,2) AS iva,
                    '' AS empty_column_2,
                    '' AS empty_column_3,
                    CONCAT('IVA','-',desc_gasto,'-', empleado, '-',obj_gasto)
                FROM CG_comprobacion
                WHERE obj_gasto = '".$SelID."' AND estatus = 'aprobado' AND empleado = '".$SelID_e."' and iva > 0";
$Res_iva = DB_query($sql_iva, $db); // Ejecuta la consulta
// $Row_iva = DB_fetch_array($Res_iva); // Obtiene el resultado de la consulta -> obtener iva
while($row_iva = DB_fetch_row($Res_iva)) {
    $array_iva[] = $row_iva; // Agregar cada fila al array principal
}


$array_iva_no_deduct = [];
$sql_iva_non_ded = "SELECT  'D' as 'C/D',
                        695685 as 'cuenta_contable',
                        ROUND(iva_no_deducible,2) AS iva_no_deducible,
                        centro_de_costo,
                        '' AS empty_column_3,
                        CONCAT('IVA','-',desc_gasto,'-', empleado, '-',obj_gasto)
                FROM CG_comprobacion
                WHERE obj_gasto = '".$SelID."' AND estatus = 'aprobado' AND empleado = '".$SelID_e."' and iva_no_deducible >0";
$Res_iva_non_ded = DB_query($sql_iva_non_ded, $db); // Ejecuta la consulta
// $Row_non_ded = DB_fetch_array($Res_non_ded); // Obtiene el resultado de la consulta - > obtener el total del importe
while($row_iva_non = DB_fetch_row($Res_iva_non_ded)) {
    $array_iva_no_deduct[] = $row_iva_non; // Agregar cada fila al array principal
}

$array_no_deduct = [];
$sql_non_ded = "SELECT  'D' as 'C/D',
                        695685 as 'cuenta_contable',
                        ROUND(no_deducible,2) AS no_deducible,
                        centro_de_costo,
                        '' AS empty_column_3,
                        CONCAT(desc_gasto,'-', empleado, '-',obj_gasto)
                FROM CG_comprobacion
                WHERE obj_gasto = '".$SelID."' AND estatus = 'aprobado' AND empleado = '".$SelID_e."' and no_deducible >0";
$Res_non_ded = DB_query($sql_non_ded, $db); // Ejecuta la consulta
// $Row_non_ded = DB_fetch_array($Res_non_ded); // Obtiene el resultado de la consulta - > obtener el total del importe
while($row = DB_fetch_row($Res_non_ded)) {
    $array_no_deduct[] = $row; // Agregar cada fila al array principal
}


$array_propina = [];
$sql_propina = "SELECT  'D' as 'C/D',
                        695685 as 'cuenta_contable',
                        Round(propina,2) as propina,
                        centro_de_costo,
                        '' AS empty_column_3,
                        CONCAT('propina','-',desc_gasto,'-', empleado, '-',obj_gasto)
                FROM CG_comprobacion
                WHERE obj_gasto = '".$SelID."' AND estatus = 'aprobado' AND empleado = '".$SelID_e."' and propina >0 ";
$Res_propina = DB_query($sql_propina, $db); // Ejecuta la consulta
// $Row_non_ded = DB_fetch_array($Res_non_ded); // Obtiene el resultado de la consulta - > obtener el total del importe
while($row_propina = DB_fetch_row($Res_propina)) {
    $array_propina[] = $row_propina; // Agregar cada fila al array principal
}

$data_poliza = [];
//datos de la poliza desc gasto-empleado-uuid
$sql_data_poliza = "SELECT  
                            'D' as 'C/D',
                            cuenta_contable,
                            ROUND(importe-descuento,2) AS importe,
                            centro_de_costo,
                            '' AS empty_column_1,
                            CONCAT(desc_gasto,'-', empleado, '-', obj_gasto) AS concat_column,
                            '' AS empty_column_2,
                            '' AS empty_column_3,
                            folio_fiscal
                    FROM CG_comprobacion 
                    WHERE obj_gasto = '".$SelID."' AND estatus = 'aprobado' AND empleado = '".$SelID_e."' AND importe > 0";

$Res_data_poliza = DB_query($sql_data_poliza,$db);
while($row_data_poliza = DB_fetch_row($Res_data_poliza)) {
    $data_poliza[] = $row_data_poliza; // Agregar cada fila al array principal
}

$data_iva_0 = [];
//datos de la poliza desc gasto-empleado-uuid
$sql_data_iva_0 = "SELECT  
                            'D' as 'C/D',
                            cuenta_contable,
                            ROUND(iva_tasa_0,2) AS iva_tasa_0,
                            centro_de_costo,
                            '' AS empty_column_1,
                            CONCAT(desc_gasto,'-', empleado, '-', obj_gasto) AS concat_column,
                            '' AS empty_column_2,
                            '' AS empty_column_3,
                            folio_fiscal
                    FROM CG_comprobacion 
                    WHERE obj_gasto = '".$SelID."' AND estatus = 'aprobado' AND empleado = '".$SelID_e."' AND iva_tasa_0 > 0";

$Res_data_iva_0 = DB_query($sql_data_iva_0,$db);
while($row_data_iva_0 = DB_fetch_row($Res_data_iva_0)) {
    $data_iva_0[] = $row_data_iva_0; // Agregar cada fila al array principal
}
$data_ieps = [];
//datos de la poliza desc gasto-empleado-uuid
$sql_data_ieps = "SELECT  
                            'D' as 'C/D',
                            cuenta_contable,
                            ROUND(ieps,2) AS ieps,
                            centro_de_costo,
                            '' AS empty_column_1,
                            CONCAT(desc_gasto,'-', empleado, '-', obj_gasto) AS concat_column,
                            '' AS empty_column_2,
                            '' AS empty_column_3,
                            folio_fiscal
                    FROM CG_comprobacion 
                    WHERE obj_gasto = '".$SelID."' AND estatus = 'aprobado' AND empleado = '".$SelID_e."' AND ieps > 0";

$Res_data_ieps= DB_query($sql_data_ieps,$db);
while($row_data_ieps = DB_fetch_row($Res_data_ieps)) {
    $data_ieps[] = $row_data_ieps; // Agregar cada fila al array principal
}

$data_ish = [];
//datos de la poliza desc gasto-empleado-uuid
$sql_data_ish = "SELECT  
                            'D' as 'C/D',
                            cuenta_contable,
                            ROUND(ish,2) AS ish,
                            centro_de_costo,
                            '' AS empty_column_1,
                            CONCAT(desc_gasto,'-', empleado, '-', obj_gasto) AS concat_column,
                            '' AS empty_column_2,
                            '' AS empty_column_3,
                            folio_fiscal
                    FROM CG_comprobacion 
                    WHERE obj_gasto = '".$SelID."' AND estatus = 'aprobado' AND empleado = '".$SelID_e."' AND ish > 0";

$Res_data_ish= DB_query($sql_data_ish,$db);
while($row_data_ish = DB_fetch_row($Res_data_ish)) {
    $data_ish[] = $row_data_ish; // Agregar cada fila al array principal
}


$array_gasto_real = [];
//datos de la poliza desc gasto-empleado-uuid
$sql_gasto_real = "SELECT  
                            'C' as 'C/D',
                            127000 as cuenta_contable,
                            ROUND(gasto_real,2) AS gasto_real,
                            '' AS empty_column_1,
                            '' AS empty_column_2,
                            CONCAT(empleado, '-', obj_gasto) AS concat_column
                    FROM CG_polizas
                    WHERE obj_gasto = '".$SelID."'  AND empleado = '".$SelID_e."'";

$Res_gasto_real= DB_query($sql_gasto_real,$db);
while($row_gasto_real = DB_fetch_row($Res_gasto_real)) {
    $array_gasto_real[] = $row_gasto_real; // Agregar cada fila al array principal
}

$array_factura_pendiente = [];
$sql_facturas_pendientes = "SELECT 
                                'D' as 'C/D',
                                127000 as cuenta_contable,
                                (ROUND(p.gasto_real-SUM(CG_comprobacion.total))) AS result,
                                '' AS empty_column_1,
                                '' AS empty_column_2,
                                CONCAT('Factura pendiente','-',CG_comprobacion.empleado, '-', CG_comprobacion.obj_gasto) AS concat_column
                            FROM CG_comprobacion
                            LEFT JOIN CG_polizas p ON p.obj_gasto = CG_comprobacion.obj_gasto
                            WHERE CG_comprobacion.obj_gasto = '".$SelID."' AND CG_comprobacion.empleado = '".$SelID_e."' AND CG_comprobacion.estatus = 'aprobado' HAVING result>0";

$Res_data_fact_pendiente= DB_query($sql_facturas_pendientes,$db);
while($row_facturas_pendientes = DB_fetch_row($Res_data_fact_pendiente)) {
    $array_factura_pendiente[] = $row_facturas_pendientes; // Agregar cada fila al array principal
}


$sql_importes = "SELECT  ROUND(importe,2) AS importe
                    FROM CG_comprobacion 
                    WHERE obj_gasto = '".$SelID."' AND estatus = 'aprobado' AND empleado = '".$SelID_e."'";

$Res_importes= DB_query($sql_importes,$db);

$sumas_gastos = 0;
while($row_gastos = mysqli_fetch_assoc($Res_importes)){
    $sumas_gastos += $row_gastos['importe'];
}

$anticip_gastos = $anticipo - $sumas_gastos;
//datos de la poliza empleado-obj del gasto
$sql_data_c = " SELECT  CONCAT(empleado, '-', obj_gasto) AS concat_column 
                FROM CG_comprobacion 
                WHERE obj_gasto = '".$SelID."' AND estatus = 'aprobado' AND empleado = '".$SelID_e."'";

$Res_data_c = DB_query($sql_data_c,$db);
$Row_concat_c = DB_fetch_array($Res_data_c);
$C_data = $Row_concat_c['concat_column'];

//crea el array vacio
$df_credito = [];

if ($anticip_gastos != 0) {
    $df_credito['127000'] = $SelID_tr;
}


// Crear filas a partir de $df_credito y añadirlas a $df_poliza
foreach ($df_credito as $key => $value) {
    // Añadir fila con 'D' al inicio y 'C' al final, ajusta si necesitas más columnas
    $df_poliza[] = ['C', $key, $value, '', '', $C_data];
}


while($row = DB_fetch_row($Res_data_poliza)) {
    array_unshift($row, 'D'); // Agregar 'D' al inicio de la fila
    $df_poliza[] = $row;
}

$poliza = array_merge($array_gasto_real, $array_factura_pendiente, $array_isr_10, $array_isr_125,$array_ret_iva, $array_iva,$array_no_deduct,$array_iva_no_deduct,$array_propina,$data_iva_0, $data_ieps, $data_ish, $data_poliza);

    $filename = "poliza_{$SelID}_{$SelID_e}.csv";


    // Establecer las cabeceras para forzar la descarga
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Abrir la salida para escritura
    $output = fopen('php://output', 'w');

    // Escribir el encabezado de las columnas
    // fputcsv($output, ['Credito/Debito', 'Cuenta Contable', 'Importe', 'Centro de Costo', 'Descripcion del Gasto', 'UUID']);

    // Asegúrate de que $df_poliza esté bien formado y no contenga filas vacías
    foreach ($poliza as $rowx) {
        // Verifica que $row no esté vacío y tenga el formato correcto
        if (count($rowx) > 0) {
            fputcsv($output, $rowx); // Escribir cada fila en el CSV
        }
    }

    // Cerrar la salida
    fclose($output);
    // Reload the page after executing the query
    exit(); // Asegúrate de finalizar el script

?>



