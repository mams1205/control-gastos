<?php
   $PageSecurity = 2;
   include('includes/session.inc');
?>

<div class="pt-2 table-responsive table-hover">
   <table id="tablapoliza" class="table table-bordered">
   	  <thead style="background-color: #1c86ee;color: white; font-weight: bold;">
   	    <tr>
            <th>ID</th>
            <th>Obj. del Gasto</th>
            <th>Empleado</th>
            <th>Total de Anticipo</th>
            <th>Total Comprobado</th>
            <th>Condición</th>
            <th>Gasto Real</th>
            <th>Status</th>
            <th>Acciones</th>
        </tr>   	            
   	  </thead>
      <tfoot style="background-color: #bebebe;color: white;">              
      </tfoot>
      <tbody>
         <?php
            $sql = "SELECT 
                        p.id_polizas,
                        p.obj_gasto,
                        p.empleado,
                        p.total_anticipo,
                        COALESCE(c.total_comprobado, 0) AS total_comprobado, -- Uses 0 if there's no matching result
                        p.gasto_real,
                        p.status
                     FROM 
                        CG_polizas AS p
                     LEFT JOIN (
                     SELECT 
                        obj_gasto, 
                        empleado, 
                        SUM(total) AS total_comprobado 
                     FROM 
                        CG_comprobacion 
                     WHERE 
                        estatus = 'aprobado' 
                     GROUP BY 
                        obj_gasto, empleado
                     ) AS c ON p.obj_gasto = c.obj_gasto AND p.empleado = c.empleado
                     ORDER BY p.status DESC";
	          $res = DB_query($sql,$db);
	          while($row = DB_fetch_row($res)) {
                  echo '<tr>';
                  echo '<td>'.$row[0].'</td>';//id
	               echo '<td>'.$row[1].'</td>';//obj gasto
                  echo '<td>'.$row[2].'</td>';//empleado
	               echo '<td> $'. number_format($row[3], 2,'.',',').'</td>';//anticipo
                  echo '<td> $'. number_format($row[4], 2,'.',',').'</td>';//total comprobado
                  // Condición para solicitudes aprobadas y mostrarlo en la columna 4
                  if (round($row[4],2) > round($row[3],2)) {
                     // Si el anticipo es mayor que el total comprobado
                     echo '<td>Presupuesto Excedido</td>';
                  } elseif (round($row[4],2) < round($row[3],2)) {
                     // Si el anticipo es menor que el total comprobado
                     echo '<td>Dentro del presupuesto</td>'; // O cualquier otra palabra que prefieras
                  } else {
                     // Si el anticipo coincide con el total comprobado
                     echo '<td>Presupuesto Coincide con Gastos</td>';
                  }
                  
                  // echo '<td>'.$row[4].'</td>';//solicitudes aprobadas
                  echo '<td> $'. number_format($row[5], 2,'.',',').'</td>';//Total real
                  if ($row[6] == 'Poliza Generada') {
                     echo '<td class="text-success">' . $row[6] . '</td>';
                 } else {
                     echo '<td class="text-danger">' . $row[6] . '</td>';
                 }

                  echo '<td style="white-space: nowrap;">
                           <a 
                              href="CG_001_003n.php?A=vGR&ID='.$row[0].'&ID_tr='.$row[5].'" 
                              data-toggle="tooltip" 
                              title="Gasto Real">
                              <i class="pr-2 fa-solid fa-credit-card"></i>
                           </a>

                           <a 
                              href="CG_001_003dp.php?A=vPol&ID='.$row[1].'&ID_e='.$row[2].'&ID_tr='.$row[5].'&ID_P='.$row[0].'"
                              data-toggle="tooltip" 
                              title="Generar Poliza"
                              target="_blank" 
                              onclick="reloadPageAfterDownload()">
                              <i class="pr-2 fa-solid fa-file-invoice-dollar"></i>
                           </a>
                        </td>';
               }
                        
	       ?>
   	  </tbody>
   </table>
</div>

<script type="text/javascript">
   $(document).ready(function() {
      $('#tablapoliza').DataTable({
         "language": {
         "url": "plugins/bootstrap/js/Spanish.json"},
         "order": [[7, "desc"]]
      });
   });      
</script>

<script type="text/javascript">
   $(document).ready(function() {
      $('#tablapoliza').DataTable();
   });
</script>

<script>
    function reloadPageAfterDownload() {
        // Esperar 3 segundos (ajusta según el tiempo estimado de descarga)
        setTimeout(function() {
            location.reload();
        }, 200);
    }
</script>
