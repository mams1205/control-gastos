<?php
// Configuración de seguridad y sesión
$PageSecurity = 15; // Establece el nivel de seguridad de la página
$App = 'CG'; // Define la aplicación como 'CG' (Catálogo Centro de costos)
$Mod = 'Empleados'; // Define el módulo como 'Empleados'
include('includes/session.inc'); // Incluye el archivo de sesión para gestionar la autenticación y datos de sesión
$title = 'Archivos Facturas'; // Establece el título de la página
include('includes/header.inc'); // Incluye el archivo del encabezado de la página

// Abre un formulario HTML con el método POST y la codificación para archivos adjuntos.
echo '<form method="post" 
            enctype="multipart/form-data"
            action="CG_001_001rf.php?ID_c='.$_GET['ID_c'].'&ID_a='.$_GET['ID_a'].'&A='.$_GET['A'].'" />';

echo '<input type=hidden name=FormID value="'.$_SESSION['FormID'].'" />';

// Inicialización de variables
$Msg   = ''; // Inicializa la variable de mensaje como una cadena vacía
$MsgC = 'green'; // Color del mensaje
$SelID_c = $_GET['ID_c'];
$SelID_a = $_GET['ID_a'];

// Consulta SQL
$sql = "SELECT id_archivo, nombre, tipo FROM CG_archivos WHERE id_comprobacion = '".$SelID_c."'";
$res = DB_query($sql, $db);


// Verifica si el parámetro 'A' en la URL es igual a 'vEli', lo que indica que se debe realizar una eliminación.
if ($_GET['A']=='vEli') {
    // Construye una consulta SQL para eliminar el ID seleccionado.
        $Del_CC = "DELETE FROM CG_archivos WHERE id_archivo =".$SelID_a;
        $Res_CC = DB_query($Del_CC,$db);  
        prnMsgV20('Se ha eliminado el archivo : '.$SelID_a.' ...','success');

}

/////////MODAL ELIMINAR///////////////////
echo '<div class="modal fade" id="ConfirmaDel" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
       <div class="modal-content">
          <div class="modal-header">
             <h5 class="modal-title">Eliminar Archivo</h5>
             <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
             <p>Confirme para eliminar el archivo '.$SelID_a .' </p>
          </div>
          <div class="modal-footer">
             <input type=submit class="btn btn-secondary" name=BtnCer data-dismiss="modal" value=Cerrar>
             <a href="CG_001_001rf.php?A=vEli&ID_c='.$SelID_c .'&ID_a='.$SelID_a .'" class="btn btn-primary" 
                role=button >Eliminar</a>
          </div>
       </div>
    </div>
 </div>';
/****///////////////////////

// Mostrar la tabla en el container
echo '<div class="container" style="margin-top:80px">
    <div class="row">
        <div class="col-sm-12">
            <div class="card text-left">
                <div class="card-body">
                    <h4>Archivos de Comprobación de Gastos</h4>';
                    if ($Msg<>'')
                        echo '<p style="text-align: center;font-family:Arial;font-size:16px;color:'.$MsgC.'">'.$Msg.'</p>';
                            //<!-- Párrafo con estilo centrado, fuente Arial, tamaño 16px y color determinado por la variable $MsgC -->
                    echo '<div id="tablaArchivos_001_001"></div>';
                    //Contenedor vacío con el ID 'tablaCG_001_001' para cargar dinámicamente la tabla de centro de costos
echo'           </div>
            </div>
        </div>
    </div>
</div>';

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
      var idC = '<?php echo $SelID_c; ?>'; // Captura la variable PHP en JavaScript
      $('#tablaArchivos_001_001').load('CG_001_001rft.php?ID_c=' + idC);
   });
</script>
</body>
</html>

