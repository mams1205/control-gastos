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
            action="CG_001_001d.php?ID='.$_GET['ID'].'&A='.$_GET['A'].'" />';

echo '<input type=hidden name=FormID value="'.$_SESSION['FormID'].'" />';

$Msg   = ''; // Inicializa la variable de mensaje como una cadena vacía
$vSaleM = 'IN'; // inicia la variable vsaleM con txt IN
$MsgC = 'green';
$SelID = $_GET['ID'];
$usuario = $_SESSION['CustomerID'];
$nombre = $_SESSION['UserID'];

// Realiza la consulta y asigna los valores a $_POST
$Sql = "SELECT * FROM CG_comprobacion WHERE id_comprobacion = '".$SelID."'";
$Res = DB_query($Sql, $db);
$Row = DB_fetch_array($Res);

$_POST['importe'] = $Row['importe'];
$_POST['iva'] = $Row['iva'];
$_POST['iva_0'] = $Row['iva_tasa_0'];
$_POST['excento'] = $Row['excento'];
$_POST['no_deducible'] = $Row['no_deducible'];
$_POST['ret_iva'] = $Row['ret_iva'];
$_POST['ret_isr_10'] = $Row['ret_isr_10'];
$_POST['ret_isr_125'] = $Row['ret_isr_125'];
$_POST['total'] = $Row['total'];






//container principal donde va a estar la información del gasto
echo '<div class="container" style="margin-top:80px">
    <div class="row">
        <div class="col-sm-12">
            <div class="card text-left">
                <div class="card-body">
                    <h4>Datos de Facturación</h4>';
                    if ($Msg<>'')
                        echo '<p style="text-align: center;font-family:Arial;font-size:16px;color:'.$MsgC.'">'.$Msg.'</p>';
                            //<!-- Párrafo con estilo centrado, fuente Arial, tamaño 16px y color determinado por la variable $MsgC -->

echo'           <div class="slic-form-group row">
                    <label for="importe" class="col-sm-3 col-slic-label">Importe:</label>
                        <div class = "col-sm-4">
                            <input 
                                class="slic-input" 
                                type = "text"
                                name="importe"
                                id = "importe"
                                value = "'.$_POST['importe'].'">';
echo'                       </input>
                        </div>
                </div>';
                echo'<br>';
echo'           <div class="slic-form-group row">
                    <label for="iva" class="col-sm-3 col-slic-label">IVA:</label>
                        <div class = "col-sm-4">
                            <input 
                                class="slic-input" 
                                type = "text"
                                name="iva"
                                id = "iva"
                                value = "'.$_POST['iva'].'"
                                readonly>';
echo'                       </input>
                            <input 
                                type="hidden"
                                name="iva"
                                id="iva"
                                value="'.$_POST['iva'].'"
                            />
                        </div>
                </div>';
                echo'<br>';
echo'           <div class="slic-form-group row">
                    <label for="iva_0" class="col-sm-3 col-slic-label">IVA tasa 0:</label>
                        <div class = "col-sm-4">
                            <input 
                                class="slic-input" 
                                type = "text"
                                name="iva_0"
                                id = "iva_0"
                                value = "'.$_POST['iva_0'].'">';
echo'                       </input>
                    </div>
                </div>';
                echo'<br>';
echo'           <div class="slic-form-group row">
                    <label for="excento" class="col-sm-3 col-slic-label">Excento:</label>
                        <div class = "col-sm-4">
                            <input 
                                class="slic-input" 
                                type = "text"
                                name="excento"
                                id = "excento"
                                value = "'.$_POST['excento'].'">';
echo'                       </input>
                    </div>
                </div>';
                echo'<br>';
echo'           <div class="slic-form-group row">
                    <label for="no_deducible" class="col-sm-3 col-slic-label">No Deducible:</label>
                        <div class = "col-sm-4">
                            <input 
                                class="slic-input" 
                                type = "text"
                                name="no_deducible"
                                id = "no_deducible"
                                value = "'.$_POST['no_deducible'].'">';
echo'                       </input>
                    </div>
                </div>';
                echo'<br>';
echo'           <div class="slic-form-group row">
                    <label for="ret_iva" class="col-sm-3 col-slic-label">Ret. IVA:</label>
                        <div class = "col-sm-4">
                            <input 
                                class="slic-input" 
                                type = "text"
                                name="ret_iva"
                                id = "ret_iva"
                                value = "'.$_POST['ret_iva'].'">';
echo'                       </input>
                        </div>
                </div>';
                echo'<br>';
echo'       <div class="slic-form-group row">
                <label for="ret_isr_10" class="col-sm-3 col-slic-label">Ret. ISR 10%:</label>
                    <div class = "col-sm-4">
                        <input 
                            class="slic-input" 
                            type = "text"
                            name="ret_isr_10"
                            id = "ret_isr_10"
                            value = "'.$_POST['ret_isr_10'].'">';
echo'                   </input>
                    </div>
            </div>';
            echo'<br>';
echo'       <div class="slic-form-group row">
                <label for="ret_isr_125" class="col-sm-3 col-slic-label">Ret. ISR 1.25%:</label>
                    <div class = "col-sm-4">
                        <input 
                            class="slic-input" 
                            type = "text"
                            name="ret_isr_125"
                            id = "ret_isr_125"
                            value = "'.$_POST['ret_isr_125'].'">';
echo'                   </input>
                    </div>
            </div>';
            echo'<br>';
echo'       <div class="slic-form-group row">
                <label for="total" class="col-sm-3 col-slic-label">Total:</label>
                    <div class = "col-sm-4">
                        <input 
                            class="slic-input" 
                            type = "text"
                            name="total"
                            id = "total"
                            value = "'.$_POST['total'].'">';
echo'                   </input>
                    </div>
            </div>';
            
echo '      <a href="CG_001_001n.php?ID='.$_GET['ID'].'&A='.$_GET['A'].'" class="btn btn-secondary" role="button">Cerrar</a>';
echo '      <input type=Submit class="btn btn-primary" name=BtnSav value=Guardar>';


                    //Contenedor vacío con el ID 'tablaCG_001_001' para cargar dinámicamente la tabla de centro de costos
echo'           </div>
            </div>
        </div>
    </div>
</div>';

// Incluye el archivo del pie de página
include('includes/footer.php');
?>

</body>
</html>