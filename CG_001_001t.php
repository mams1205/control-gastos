<?php
   $PageSecurity = 2;
   include('includes/session.inc');


$clave_customer = $_SESSION['CustomerID'];
$nombre = $_SESSION['UserID'];
$dpto = $_SESSION['UserBranch'];
?>




 <!-- Example comparison -->
        <div class="pt-2 table-responsive table-hover">
   <table id="tablaComprobacion_Gasto" class="table table-bordered">
   	  <thead style="background-color: #1c86ee;color: white; font-weight: bold;">
   	     <tr>
           <th>ID</th>
            <th>Obj. Gasto</th>
            <th>Proveedor</th>
            <th>Centro de Costo</th>
            <th>Grupo Art.</th>
            <th>Cuenta Contable</th>
            <th>Desc. del Gasto</th>
            <th>Estatus</th>
            <th>Acciones</th>
   	     </tr>   	            
   	  </thead>
      <tfoot style="background-color: #bebebe;color: white;">              
      </tfoot>
      <tbody>
         <?php
            $sql = "SELECT  id_comprobacion, obj_gasto,proveedor, centro_de_costo, grupo_articulos, cuenta_contable, desc_gasto, estatus
                    FROM CG_comprobacion
                    WHERE empleado  = '$nombre' and estatus != 'aprobado' ";
	          $res = DB_query($sql,$db);
	          while($row = DB_fetch_row($res)) {
               echo '<tr>';
	            echo '<td>'.$row[0].'</td>';
	            echo '<td>'.$row[1].'</td>';
               echo '<td>'.$row[2].'</td>';
               echo '<td>'.$row[3].'</td>';
               echo '<td>'.$row[4].'</td>';
               echo '<td>'.$row[5].'</td>';
               echo '<td>'.$row[6].'</td>';
               echo '<td>'.$row[7].'</td>';
               if ($row[7] === 'Por completar'){
               echo '<td> 
                        <div class="d-flex flex-column gap-2">
                           <a href="CG_001_001n.php?A=vMod&ID='.$row[0].'" 
                              class = "btn btn-primary"
                              role = button>
                              <i class="pr-2 fas fa-edit"></i> Modificar
                           </a>
                           <a href="CG_001_001f.php?&ID='.$row[0].'&ID_ob='.$row[1].'&P='.$row[2].'"
                              class="btn btn-primary mt-1"
                              role=button
                              >
                              <i class="fa-solid fa-file-invoice"></i> Cargar Factura
                           </a>
                           
                           <a href="CG_001_001of.php?&ID='.$row[0].'&ID_ob='.$row[1].'&P='.$row[2].'"
                              class = "btn btn-primary mt-1"
                              role = button
                              >
                              <i class="pr-2 fa-solid fa-file-circle-question"></i> Cargar Otro Archivo
                           </a>

                           <a href="CG_001_001n.php?A=vDel&ID='.$row[0].'"
                              class = "btn btn-danger mt-1"
                              role = button
                              >
                              <i class="pr-2 fas fa-trash-alt"></i> Eliminar
                           </a>
                        </div>
                     </td>';

               }
               if ($row[7] === 'Archivos cargados'){
                  echo '<td>
                           <div class="d-flex flex-column gap-2"> 
                              <a href="CG_001_001n.php?A=vMod&ID='.$row[0].'" 
                                 class = "btn btn-primary"
                                 role = button>
                                 <i class="pr-2 fas fa-edit"></i> Modificar
                              </a>
                              <a href="CG_001_001n.php?A=vSend&ID='.$row[0].'" 
                                 class = "btn btn-success mt-1"
                                 role = button
                                 >
                                 <i class="pr-2 fa-solid fa-paper-plane"></i> Enviar
                              </a>
      
                              <a href="CG_001_001edf.php?&ID='.$row[0].'&ID_ob='.$row[1].'&P='.$row[2].'"
                                 class = "btn btn-primary mt-1"
                                 role = button
                                 >
                                 <i class="pr-2 fa-solid fa-file-pen"></i> Editar Archivos
                              </a>

                              <a href="CG_001_001rf.php?&ID_c='.$row[0].'" 
                                 class = "btn btn-primary mt-1"
                                 role = button
                                 >
                                 <i class="pr-2 fa-solid fa-folder-open"></i> Revisar Archivos
                              </a>
      
                              <a href="CG_001_001n.php?A=vDel&ID='.$row[0].'"
                                 class = "btn btn-danger"
                                 role = button
                                 >
                                 <i class="pr-2 fas fa-trash-alt"></i> Eliminar
                              </a>
                           </div>
                        </td>';
   
                  }
                  if ($row[7] === 'Gasto sin Factura'){
                     echo '<td> 
                     <a href=""
                        data-toggle="tooltip" 
                        title="Cargar Importe Manualmente">
                        <i class="pr-2 fa-solid fa-triangle-exclamation" style="color:orange;"></i>
                     </a>

                     <a href="CG_001_001f.php?&ID='.$row[0].'&ID_ob='.$row[1].'&P='.$row[2].'"
                           data-toggle="tooltip" title="Agregar Archivos de Factura">
                           <i class="pr-2 fa-solid fa-file-invoice-dollar"></i>
                     </a>
   
                     <a href="CG_001_001n.php?A=vMod&ID='.$row[0].'" 
                        data-toggle="tooltip" 
                        title="Completar Formulario">
                        <i class="pr-2 fas fa-edit"></i>
                     </a>
                     
                     <a href="CG_001_001rf.php?&ID_c='.$row[0].'" 
                        data-toggle="tooltip" 
                        title="Revisar Archivos">
                        <i class="pr-2 fa-solid fa-folder-open"></i>
                     </a>

                     <a href="CG_001_001n.php?A=vSend&ID='.$row[0].'" 
                              data-toggle="tooltip" 
                              title="Enviar">
                              <i class="pr-2 fa-solid fa-paper-plane"></i>
                           </a>
   
                     <a href="CG_001_001n.php?A=vDel&ID='.$row[0].'"
                        data-toggle="tooltip" title="Eliminar">
                        <i class="pr-2 fas fa-trash-alt"></i>
                     </a>
                  </td>';
                  }
               if ($row[7] === 'Rechazado'){
                  echo '<td> 
                  <a href="CG_001_001n.php?A=vinfo&ID='.$row[0].'" 
                     data-toggle="tooltip" 
                     title="Información">
                     <i class="pr-2 fa-solid fa-triangle-exclamation" style="color:orange;"></i>
                  </a>

                  <a href="CG_001_001n.php?A=vMod&ID='.$row[0].'" 
                     data-toggle="tooltip" 
                     title="Modificar">
                     <i class="pr-2 fas fa-edit"></i>
                  </a>
                  
                  <a href="CG_001_001edf.php?&ID='.$row[0].'&ID_ob='.$row[1].'&P='.$row[2].'"
                     data-toggle="tooltip" title="Editar Archivos de Factura">
                     <i class="pr-2 fa-solid fa-file-pen"></i>
                  </a>
                  <a href="CG_001_001of.php?&ID='.$row[0].'&ID_ob='.$row[1].'&P='.$row[2].'"
                     data-toggle="tooltip" title="Otros Archivos">
                     <i class="pr-2 fa-solid fa-file-circle-question"></i>
                  </a>
                  <a href="CG_001_001rf.php?&ID_c='.$row[0].'" 
                     data-toggle="tooltip" 
                     title="Revisar Archivos">
                     <i class="pr-2 fa-solid fa-folder-open"></i>
                  </a>

                  <a href="CG_001_001n.php?A=vDel&ID='.$row[0].'"
                     data-toggle="tooltip" title="Eliminar">
                     <i class="pr-2 fas fa-trash-alt"></i>
                  </a>
               </td>';
               }
               if ($row[7] === 'enviado'){
                  echo '<td>
                  </td>'; 
	          }
            }
	       ?>
   	  </tbody>
   </table>
</div>

<script type="text/javascript">
   $(document).ready(function() {
      $('#tablaComprobacion_Gasto').DataTable({
         "language": {
         "url": "plugins/bootstrap/js/Spanish.json"},
         "order": [[0, "asc"]]
      });
   });      
</script>

