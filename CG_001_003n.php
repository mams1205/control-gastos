<?php

$PageSecurity = 15; // Establece el nivel de seguridad de la página
$App = 'CG'; // Define la aplicación como 'CCC' (Catálogo Centro de costos)
$Mod = 'Centro de Costos'; // Define el módulo como 'Proveedores'
include('includes/session.inc'); // Incluye el archivo de sesión para gestionar la autenticación y datos de sesión
$title = 'Control de Polizas'; // Establece el título de la página
include('includes/header.inc'); // Incluye el archivo del encabezado de la página

// Abre un formulario HTML con el método POST y la codificación para archivos adjuntos.
echo '<form method="post" 
            enctype="multipart/form-data"
            action="CG_001_003n.php?ID='.$_GET['ID'].'&A='.$_GET['A'].'&ID_tr='.$_GET['ID_tr'].'" />';
echo '<input type=hidden name=FormID value="'.$_SESSION['FormID'].'" />';


$Msg   = ''; // Inicializa la variable de mensaje como una cadena vacía
$vSaleM = 'IN'; // inicia la variable vsaleM con txt IN
$MsgC = 'green';
$SelID = $_GET['ID'];
$SelID_tr = $_GET['ID_tr'];


if ($_POST['BtnSav']=='Guardar') {
    // Asegúrate de que $_POST['ID_CC'] esté definido y no esté vacío
    if (isset($_POST['gasto_real']) && !empty($_POST['gasto_real'])) {
        $total_real = mysqli_real_escape_string($db, $_POST['gasto_real']);
    //QUERY SQL
        $UPD = "UPDATE CG_polizas
                    SET  gasto_real = '$total_real'
                    WHERE id_polizas = $SelID";

        $res = DB_query($UPD, $db);

        if ($res) {
            prnMsgV20('Se ha modificado el total del gasto real...','success');
        } else {
            $Msg = 'Error al modificar la solicitud';
            $MsgC = 'red';
        }

        $vSaleM = 'OUT';
    } else {
        $Msg = 'ID de la poliza no proporcionado';
        $MsgC = 'red';
    }
}

//container principal donde va a estar la tabla 
echo '<div class="container" style="margin-top:80px">
    <div class="row">
        <div class="col-sm-12">
            <div class="card text-left">
                <div class="card-body">
                    <h4>Control de Polizas </h4>';
                    if ($Msg<>'')
                        echo '<p style="text-align: center;font-family:Arial;font-size:16px;color:'.$MsgC.'">'.$Msg.'</p>';
                            //<!-- Párrafo con estilo centrado, fuente Arial, tamaño 16px y color determinado por la variable $MsgC -->
                    echo '<div id="tablaPol_001_003"></div>';
echo'           </div>
            </div>
        </div>
    </div>
</div>';

/***
* Modal Pantalla de Datos para agregar nuevo centro de costos
*/
// Modal HTML
echo '<div class="modal fade" id="GastoReal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Añadir Gasto Real</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
echo'           <div class = "modal-body">
                <div class = "row">
                    <div class="col-sm-12">
                        <div class="slic-form-group row">
                            <label for="gasto_real" class="col-sm-3 col-slic-label">Gasto Real:</label>
                            <div class = "col-sm-4">
                                <input
                                    type = "text"
                                    class="slic-input" 
                                    name="gasto_real"
                                    id="gasto_real"
                                    value="' . $SelID_tr . '"
                                />
                            </div>
                        </div>';
echo'               </div>
                </div>
                </div>';
echo '          <div class="modal-footer">';
echo '          <input type=Submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>';
echo '              <input type=Submit class="btn btn-primary" name=BtnSav value=Guardar>';
echo '          </div>
            </div>
        </div>
    </div>';


// script para activar el modal de eliminar
if ($_GET['A']=='vPol') {
    ?>
    <script type="text/javascript">
        $('#ConfirmaPol').modal('show');
    </script>
    <?php   
}

if ($_GET['A']=='vGR' and $vSaleM == 'IN') {
    ?>
    <script type="text/javascript">
        $('#GastoReal').modal('show');
    </script>
    <?php   
}

include('includes/footer.php'); // Incluye el archivo del pie de página
?>

<!--Agregar el script donde esta la tabla -->
<script type="text/javascript">
   $(document).ready(function() {
    $('#tablaPol_001_003').load('CG_001_003t.php');
   });
</script>


</body>
</html>

