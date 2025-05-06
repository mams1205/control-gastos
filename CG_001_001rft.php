<?php
   $PageSecurity = 2;
   include('includes/session.inc');
   $SelID_c = $_GET['ID_c'];
?>

<!-- obtener el id -->

<div class="pt-2 table-responsive table-hover">
   <table id="tablaArchivos" class="table table-bordered">
   	  <thead style="background-color: #1c86ee;color: white; font-weight: bold;">
   	     <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Acciones</th>
   	     </tr>   	            
   	  </thead>
      <tfoot style="background-color: #bebebe;color: white;">              
      </tfoot>
      <tbody>
         <?php
            $sql = "SELECT id_archivo, nombre, tipo FROM CG_archivos
                    WHERE id_comprobacion = '$SelID_c'";
	          $res = DB_query($sql,$db);
	          while($row = DB_fetch_row($res)) {
               echo '<tr>';
	            echo '<td>'.$row[0].'</td>';
	            echo '<td>'.$row[1].'</td>';
               echo '<td>'.$row[2].'</td>';
               if ($row[2] === 'pdf'){
               echo '<td> 
                        <a target = "_black" href="http://' . $_SERVER['HTTP_HOST'] . '/companies/iatiqroc_Plastek/gastos/' . $row[2] . '/' . $row[1] . '"
                           data-toggle="tooltip" 
                           title="Visualizar Archivos">
                           <i class="pr-2 fa-solid fa-eye"></i>
                        </a>

                        <a href="CG_001_001rfd.php?&ID='.$row[0].'" 
                              data-toggle="tooltip" 
                              title="Descargar">
                              <i class="pr-2 fa-solid fa-download"></i>
                        </a>
                     </td>';
               }else{
                  echo '<td> 

                                    <a href="CG_001_001rfd.php?&ID='.$row[0].'" 
                                        data-toggle="tooltip" 
                                        title="Descargar">
                                        <i class="pr-2 fa-solid fa-download"></i>
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
      $('#tablaArchivos').DataTable({
         "language": {
         "url": "plugins/bootstrap/js/Spanish.json"},
         "order": [[0, "asc"]]
      });
   });      
</script>

<script type="text/javascript">
   $(document).ready(function() {
      $('#tablaArchivos').DataTable();
   });
</script>
