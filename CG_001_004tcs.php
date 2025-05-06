<?php
   $PageSecurity = 2;
   include('includes/session.inc');

    $clave_customer = $_SESSION['CustomerID'];
    $nombre = $_GET['emp_'];
    $dpto = $_SESSION['UserBranch'];
    $SelID = urldecode($_GET['ID']);

?>
 <!-- Example comparison -->
    <div class="pt-2 table-responsive table-hover">
   <table id="tabla_detalle_solicitud" class="table table-bordered">
   	  <thead style="background-color: #1c86ee;color: white; font-weight: bold;">
   	     <tr>
            <th>ID</th>
            <th>Obj. del Gasto</th>
            <th>Desc. del Gasto</th>
            <th>Cant. Solicitada</th>
            <th>Acciones</th>
   	     </tr>   	            
   	  </thead>
      <tfoot style="background-color: #bebebe;color: white;">              
      </tfoot>
      <tbody>
        <?php
            //obtener la justificacion
            $sql_just = "SELECT DISTINCT justificacion 
            FROM CG_solicitud_viaticos 
            WHERE empleado = '$nombre' AND ID_viaticos = '$SelID' AND justificacion IS NOT NULL AND justificacion <> ''";

            $res_just = DB_query($sql_just, $db);
            $justificaciones = [];
            while ($row_just = DB_fetch_row($res_just)) {
            $justificaciones[] = $row_just[0]; // Assuming justificacion is the first column
            }

            $sql = "SELECT ID_detalle, 
                        obj_gasto,
                        desc_gasto,
                        cant_solicitada
                    FROM CG_solicitud_viaticos_D
                    WHERE id_solicitud = '$SelID'";
            $res = DB_query($sql, $db);

            $totalCantSolicitada = 0; // Initialize total variable

            while ($row = DB_fetch_row($res)) {
                echo '<tr>';
                echo '<td>' . $row[0] . '</td>';
                echo '<td>' . $row[1] . '</td>';
                echo '<td>' . $row[2] . '</td>';
                echo '<td>$' . number_format($row[3], 2, '.', ',') . '</td>';
                echo '<td> 
                        <a 
                           href="CG_001_004n.php?A=vMod&ID=' . $row[0] . '&ID_e=' . $row[1] . '"
                           data-toggle="tooltip" title="Modificar">
                           <i class="pr-2 fa-solid fa-pen-to-square"></i>
                        </a>
                        <a 
                           href="CG_001_004n.php?A=vDelD&ID=' . $row[0] . '&ID_e=' . $row[1] . '"
                           data-toggle="tooltip" title="Eliminar">
                           <i class="pr-2 fas fa-trash-alt"></i>
                        </a>

                       
                    </td>';
                echo '</tr>';

                // Add the current row's cant_solicitada to the total
                $totalCantSolicitada += $row[3];
            }

            // After the loop, add a new row for the total
            echo '<tr>';
            echo '<td colspan="3" style="text-align: right; font-weight: bold;">Total Solicitado:</td>';
            echo '<td>$' . number_format($totalCantSolicitada, 2, '.', ',') . '</td>';
            echo '<td></td>'; // Leave the last cell empty or add an appropriate action
            echo '</tr>';

            // Add the long text in the following row
            echo '<tr>';
            echo '<td colspan="4" style="padding-top: 20px; text-align: left;">';
            echo 'Justificación: <br>' . implode('<br>', array_map('htmlspecialchars', $justificaciones));
            echo '</td>';
            echo '</tr>';
        ?>
   	  </tbody>
   </table>
</div>

<script type="text/javascript">
   $(document).ready(function() {
      $('#tabla_detalle_solicitud').DataTable({
         "language": {
         "url": "plugins/bootstrap/js/Spanish.json"},
         "order": [[0, "asc"]]
      });
   });      
</script>

<script type="text/javascript">
   $(document).ready(function() {
      $('#tabla_detalle_solicitud').DataTable();
   });
</script>