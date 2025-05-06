<?php
   $PageSecurity = 2;
   include('includes/session.inc');

$clave_customer = $_SESSION['CustomerID'];
$nombre = $_SESSION['UserID'];

$sql_jefe = "SELECT depto FROM CG_responsables where user_id = '$nombre'";
$res_jefe = DB_query($sql_jefe,$db);
// Convertir resultados a un array
$dptos_array = [];
while ($row = DB_fetch_array($res_jefe)) {
    $dptos_array[] = $row['depto'];  // Añadir cada departamento al array
}

// 2. Convertir array a una lista de valores separados por comas para la consulta
$dptos_list = implode(',', $dptos_array);  // Crear lista '1,2,3,...'
?>

<?php 
// si es el director == X que se muestren las solicitudes mayores a $20,000 y las de sus departamentos 
if($clave_customer == 'dir'){
?>
 <!-- Example comparison -->
        <div class="pt-2 table-responsive table-hover">
   <table id="tablaviaticos" class="table table-bordered">
   	  <thead style="background-color: #1c86ee;color: white; font-weight: bold;">
   	     <tr>
            <th>ID</th>            
            <th>Empleado</th>
            <th>Departamento</th>
            <th>Obj. Gasto</th>
            <th>Cant. Total Solicitada</th>
            <th>Estatus</th>
            <th>Acciones</th>
   	     </tr>   	            
   	  </thead>
      <tfoot style="background-color: #bebebe;color: white;">              
      </tfoot>
      <tbody>
         <?php
            $sql = " SELECT   a.ID_viaticos,
                              a.empleado,
                              a.departamento,
                              a.obj_gasto,
                              SUM(d.cant_solicitada) AS total_solicitado,
                              a.estatus
                     FROM CG_solicitud_viaticos a
                     LEFT JOIN CG_solicitud_viaticos_D d ON ID_viaticos = id_solicitud
                     WHERE a.estatus = 'Enviado'
                     GROUP BY a.empleado, a.departamento, a.obj_gasto, a.estatus
                     HAVING SUM(d.cant_solicitada) > 20000 or departamento in ($dptos_list)";
	          $res = DB_query($sql,$db);
	          while($row = DB_fetch_row($res)) {
               echo '<tr>';
	            echo '<td>'.$row[0].'</td>';
               echo '<td>'.$row[1].'</td>';
	            echo '<td>'.$row[2].'</td>';
               echo '<td>'.$row[3].'</td>';
               echo '<td>$'. number_format($row[4], 2,'.',',').'</td>';
               echo '<td>'.$row[5].'</td>';
               if ($row[5] === 'Enviado'){
               echo '<td> 
                        <a href= "CG_001_005n.php?A=vCons&ID='.$row[0].'&ID_o='.$row[3].'&emp_='.$row[1].'"
                           data-toggle="tooltip" title="Consultar detalles de Solicitud">
                           <i class="pr-2 fa-solid fa-magnifying-glass"></i>
                        </a>

                        <a href="CG_001_005n.php?A=Aprov&ID='.$row[0].'&ID_o='.$row[3].'&emp_='.$row[1].'&tot_='.$row[4].'"
                           data-toggle="tooltip" title="Aprovar Solicitud">
                           <i class="pr-2 fa-solid fa-circle-check" style = "color:green;"></i>
                        </a>

                        <a href="CG_001_005n.php?A=Rej&ID='.$row[0].'&ID_o='.$row[3].'&emp_='.$row[1].'"
                           data-toggle="tooltip" title="Rechazar">
                           <i class="fa-solid fa-circle-xmark" style="color:red;"></i>
                        </a>
                     </td>';
               }
            }
            ?>
   	  </tbody>
   </table>
</div>
<?php
   } else {
?>
      <!-- Example comparison -->
      <div class="pt-2 table-responsive table-hover">
 <table id="tablaviaticos" class="table table-bordered">
      <thead style="background-color: #1c86ee;color: white; font-weight: bold;">
         <tr>
            <th>ID</th>            
            <th>Empleado</th>
            <th>Departamento</th>
            <th>Obj. Gasto</th>
            <th>Cant. Total Solicitada</th>
            <th>Estatus</th>
            <th>Acciones</th>
   	   </tr>   	            
   	</thead>
      <tfoot style="background-color: #bebebe;color: white;">              
      </tfoot>
      <tbody>
         <?php
            $sql = " SELECT   a.ID_viaticos,
                              a.empleado,
                              a.departamento,
                              a.obj_gasto,
                              SUM(d.cant_solicitada) AS total_solicitado,
                              a.estatus
                     FROM CG_solicitud_viaticos a
                     LEFT JOIN CG_solicitud_viaticos_D d ON ID_viaticos = id_solicitud
                     WHERE a.estatus = 'Enviado' AND departamento IN ($dptos_list)
                     GROUP BY empleado, departamento, obj_gasto, estatus
                     HAVING SUM(d.cant_solicitada) <= 20000;";
           $res = DB_query($sql,$db);
           while($row = DB_fetch_row($res)) {
            echo '<tr>';
            echo '<td>'.$row[0].'</td>';
            echo '<td>'.$row[1].'</td>';
            echo '<td>'.$row[2].'</td>';
            echo '<td>'.$row[3].'</td>';
            echo '<td>$'. number_format($row[4], 2,'.',',').'</td>';
            echo '<td>'.$row[5].'</td>';
            if ($row[5] === 'Enviado'){
             echo '<td> 
                      <a href= "CG_001_005n.php?A=vCons&ID='.$row[0].'&ID_o='.$row[3].'&emp_='.$row[1].'"
                           data-toggle="tooltip" title="Consultar detalles de Solicitud">
                           <i class="pr-2 fa-solid fa-magnifying-glass"></i>
                        </a>

                      <a href="CG_001_005n.php?A=Aprov&ID='.$row[0].'&ID_o='.$row[3].'&emp_='.$row[1].'&tot_='.$row[4].'"
                           data-toggle="tooltip" title="Aprovar Solicitud">
                           <i class="pr-2 fa-solid fa-circle-check" style = "color:green;"></i>
                        </a>

                      <a href="CG_001_005n.php?A=Rej&ID='.$row[0].'&ID_o='.$row[3].'&emp_='.$row[1].'"
                           data-toggle="tooltip" title="Rechazar">
                           <i class="fa-solid fa-circle-xmark" style="color:red;"></i>
                        </a>
                   </td>';
             }
          }
          ?>
      </tbody>
 </table>
</div>
<?php
}
?>


<script type="text/javascript">
   $(document).ready(function() {
      $('#tablaviaticos').DataTable({
         "language": {
         "url": "plugins/bootstrap/js/Spanish.json"},
         "order": [[0, "asc"]]
      });
   });      
</script>

<script type="text/javascript">
   $(document).ready(function() {
      $('#tablaviaticos').DataTable();
   });
</script>

