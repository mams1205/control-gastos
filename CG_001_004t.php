<?php
   $PageSecurity = 2;
   include('includes/session.inc');


$clave_customer = $_SESSION['CustomerID'];
$nombre = $_SESSION['UserID'];
$dpto = $_SESSION['UserBranch'];
?>
 <!-- Example comparison -->
        <div class="pt-2 table-responsive table-hover">
   <table id="tablaviaticos" class="table table-bordered">
   	  <thead style="background-color: #1c86ee;color: white; font-weight: bold;">
   	     <tr>
           <th>ID</th>
            <th>Obj. Gasto</th>
            <th>Departamento</th>
            <th>Fecha de Inicio del Viaje</th>
            <th>Fecha Final del Viaje</th>
            <!-- <th>Gastos Registrados</th> -->
            <!-- <th>Cant. Total Solicitada</th> -->
            <th>Fecha de la Solicitud</th>
            <th>Estatus</th>
            <th>Acciones</th>
   	     </tr>   	            
   	  </thead>
      <tfoot style="background-color: #bebebe;color: white;">              
      </tfoot>
      <tbody>
         <?php
            $sql = " SELECT 
                           ID_viaticos,
                           obj_gasto, 
                           departamento,
                           fecha_inicio,
                           fecha_fin,
                           -- COUNT(desc_gasto) AS row_count,
                           -- SUM(cant_solicitada) AS total_solicitado,
                           fecha_solicitud,
                           estatus
                     FROM CG_solicitud_viaticos
                     WHERE empleado = '$nombre'
                     GROUP BY obj_gasto";
	          $res = DB_query($sql,$db);
	          while($row = DB_fetch_row($res)) {
               echo '<tr>';
	            echo '<td>'.$row[0].'</td>';
	            echo '<td>'.$row[1].'</td>';
               echo '<td>'.$row[2].'</td>';
               echo '<td>'.$row[3].'</td>';
               echo '<td>'.$row[4].'</td>';
               echo '<td>'.$row[5].'</td>';
               // echo '<td>$'. number_format($row[6], 2,'.',',').'</td>';
               echo '<td>'.$row[6].'</td>';
               // echo '<td>'.$row[7].'</td>';
               if ($row[6] === 'Por enviar'){
               echo '<td> 
                        <a href="CG_001_004n.php?A=vCons&ID='.$row[0].'&emp_='.$nombre.'&ID_d='.$row[2].'&t_o='.$row[5].'"
                           data-toggle="tooltip" title="Consultar detalles de Solicitud">
                           <i class="pr-2 fa-solid fa-magnifying-glass"></i>
                        </a>

                        <a href="CG_001_004n.php?A=vPlus&ID='.$row[0].'&ID_d='.$row[1].'"
                           data-toggle="tooltip" title="Agregar Nuevo Gasto">
                           <i class="pr-2 fa-solid fa-square-plus"></i>
                        </a>

                        <a href="CG_001_004n.php?A=vDel&ID='.$row[0].'"
                           data-toggle="tooltip" title="Eliminar">
                           <i class="pr-2 fas fa-trash-alt"></i>
                        </a>
                     </td>';
               }
               if ($row[6] === 'Rechazado'){
                  echo '<td>
                           <a href="CG_001_004n.php?A=vinfo&ID='.$row[0].'"
                              data-toggle="tooltip" title="Consultar Comentarios">
                              <i class="pr-2 fa-solid fa-triangle-exclamation" style="color:orange;"></i>
                           </a>

                           <a href="CG_001_004n.php?A=vCons&ID='.$row[0].'&emp_='.$nombre.'&ID_d='.$row[2].'&t_o='.$row[5].'"
                           data-toggle="tooltip" title="Consultar detalles de Solicitud">
                           <i class="pr-2 fa-solid fa-magnifying-glass"></i>
                           </a>

                           <a href="CG_001_004n.php?A=vPlus&ID='.$row[0].'&ID_d='.$row[1].'"
                           data-toggle="tooltip" title="Agregar Nuevo Gasto">
                           <i class="pr-2 fa-solid fa-square-plus"></i>
                           </a>

                           <a href="CG_001_004n.php?A=vDel&ID='.$row[0].'"
                           data-toggle="tooltip" title="Eliminar">
                           <i class="pr-2 fas fa-trash-alt"></i>
                           </a>
                        </td>';
                  }
            }
            ?>
   	  </tbody>
   </table>
</div>

<script type="text/javascript">
   $(document).ready(function() {
      $('#tablaviaticos').DataTable({
         "language": {
         "url": "plugins/bootstrap/js/Spanish.json"},
         "order": [[6, "asc"]]
      });
   });      
</script>

<!-- <script type="text/javascript">
   $(document).ready(function() {
      $('#tablaviaticos').DataTable();
   });
</script> -->