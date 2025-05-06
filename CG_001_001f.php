<?php

$PageSecurity = 15; // Establece el nivel de seguridad de la página
$App = 'CG'; // Define la aplicación como 'CG' (Catálogo Centro de costos)
$Mod = 'Empleados'; // Define el módulo como 'Empleados'
include('includes/session.inc'); // Incluye el archivo de sesión para gestionar la autenticación y datos de sesión
$title = 'Cargar Archivos de Factura'; // Establece el título de la página
include('includes/header.inc'); // Incluye el archivo del encabezado de la página

// Abre un formulario HTML con el método POST y la codificación para archivos adjuntos.
echo '<form method="post" 
            enctype="multipart/form-data"
            action="CG_001_001f.php?ID='.$_GET['ID'].'&A='.$_GET['A'].'&ID_ob='.$_GET['ID_ob'].'&P='.$_GET['P'].'" />';

echo '<input type=hidden name=FormID value="'.$_SESSION['FormID'].'" />';


$Msg   = ''; // Inicializa la variable de mensaje como una cadena vacía
$vSaleM = 'IN'; // inicia la variable vsaleM con txt IN
$MsgC = 'green';
$SelID = $_GET['ID'];
$SelID_ob = $_GET['ID_ob'];
$P = $_GET['P'];
$clave_usuario = $_SESSION['CustomerID'];
$nombre_usuario = $_SESSION['UserID'];

/* parte 1 */
if ($_POST['Cargar'] == 'Cargar Factura') {

  $foliofiscal_comprobar = $_POST['folio_fiscal'];
  if(empty($foliofiscal_comprobar)){
    $mensaje = 'folio fiscal vacio';
  }else{
    $mensaje = '';
  }
  ## sql para revisar que los folios fiscales no se vayan a repetir
  $sql_folio_fiscal = "SELECT folio_fiscal FROM CG_comprobacion WHERE folio_fiscal = '$foliofiscal_comprobar'";
  $res_folio_fiscal = DB_query($sql_folio_fiscal, $db);
  $num_rows = DB_num_rows($res_folio_fiscal);

  if($num_rows == 0){
    $mensaje = "";
  } else{
    $mensaje = 'Folio Fiscal cargado anteriormente';
  }
  
  if ($mensaje == '') {
    ####sql alimentos local y foraneo
    $sql_alimento = "SELECT desc_gasto FROM CG_comprobacion WHERE id_comprobacion = '$SelID'";
    $res_alimento = DB_query($sql_alimento, $db);
    $fila_alimento = mysqli_fetch_assoc($res_alimento);
    $valor_alim = $fila_alimento['desc_gasto'];
    

    if ($valor_alim == 'Aliment Loc') {
        // If it's local food, apply a deduction of 8.5%
        $deduct = 0.085;
      } else {
        // Default case, no deduction
        $deduct = 1;
    }
    

      // Retrieve submitted values
      $foliofiscal = $_POST['folio_fiscal'];
      $importe = $_POST['sub_total_factura'];
      $exento = $_POST['exento'];
      $Descuento = $_POST['descuento'];
      $iva = $_POST['iva'];
      $IEPS = $_POST['ieps'];
      $ISH = $_POST['ish'];
      $I_complementos = $_POST['i_complementos'];
      $ret_isr_1_25 = $_POST['ret_isr_1_25'];
      $ret_isr_10 = $_POST['ret_isr_10'];
      $ret_iva = $_POST['ret_iva'];
      $total = $_POST['total'];

      if ($deduct != 1){
        $importe_end = $importe*$deduct;
        $iva_end = $importe_end * 0.16;
        $no_deduct = $total - ($importe_end+$iva_end);
        $real_no_deducible = $no_deduct/1.16;
        $iva_no_deducible = $no_deduct - $real_no_deducible;
        }
        else{
          $real_no_deducible = 0;
          $importe_end = $importe*$deduct;
          $iva_end = $iva;
          $iva_no_deducible = 0;
        }
  

      //variables finales
      
      // Initialize an array to hold empty field names
      $emptyFields = [];

      // Check each field and add to array if empty
      if (empty($foliofiscal)) $emptyFields[] = 'Folio Fiscal';
      if (empty($importe)) $emptyFields[] = 'Sub Total Factura';
      if (empty($exento)) {
          $exento = 0;
      }
      if (empty($iva)) {
          $iva = 0;
      }
      if (empty($ret_isr_1_25)) {
          $ret_isr_1_25 = 0;
      }
      if (empty($ret_isr_10)) {
          $ret_isr_10 = 0;
      }
      if (empty($ret_iva)) {
          $ret_iva = 0;
      }
      if (empty($total)) $emptyFields[] = 'Total';

      // Check if there are any empty fields
      if (empty($emptyFields)) {
          // If no fields are empty, proceed with the query
          $sql = "UPDATE CG_comprobacion 
                  SET  
                      importe = '$importe_end',
                      iva = '$iva_end',
                      ieps = '$IEPS',
                      ish = '$ISH', 
                      excento = '$exento',
                      descuento = '$Descuento',
                      ret_iva = '$ret_iva',
                      ret_isr_10 = '$ret_isr_10',
                      ret_isr_125 = '$ret_isr_1_25',
                      total = '$total',
                      no_deducible = '$real_no_deducible',
                      iva_no_deducible = '$iva_no_deducible',
                      folio_fiscal = '$foliofiscal', 
                      estatus = 'Archivos cargados',
                      jerarquia = CASE 
                                  WHEN $total > 20000 THEN 'mayor'
                                  ELSE 'menor'
                                  END
                      WHERE id_comprobacion = '$SelID'";

          if ($stmt = mysqli_prepare($db, $sql)) {
              
              // Execute the query
              if (mysqli_stmt_execute($stmt)) {
                  $Msg = 'exito.';
                  $MsgC = 'green'; 
                  // Clear the form data
                  unset($_POST['folio_fiscal']);
                  unset($_POST['sub_total_factura']);
                  unset($_POST['exento']);
                  unset($_POST['iva']);
                  unset($_POST['ieps']);
                  unset($_POST['ish']);
                  unset($_POST['ret_isr_1_25']);
                  unset($_POST['ret_isr_10']);
                  unset($_POST['total']);
                  $vSaleM = 'OUT';
                  echo "Success: The query executed successfully.<br>";
            
                
                // Mensaje de éxito
                prnMsgV20('Se ha cargado el archivo XML correctamente...', 'success');
                // header("Location: index.php");
                // exit(); // Terminate the script after redirection

                // Limpia las variables del formulario después de enviar el correo
                $_POST['Validar'] = '';
                $_POST['Cargar'] = '';

                ?>
                <script type="text/javascript">
                  window.onload = function() {
                    window.location.href = 'CG_001_001n.php';  // Cambia la URL por la que desees
                  };
                </script>
                <?php

  
              } else {
                  // Capture MySQL error
                  $error = mysqli_stmt_error($stmt);
                    $Msg = 'Error al agregar el objetivo del gasto. MySQL Error: ' . $error;
                    $MsgC = 'red';
              }
              // Close the statement
              mysqli_stmt_close($stmt);
          } else {
              // Capture error in SQL preparation
              echo "SQL Preparation Error: " . mysqli_error($db) . "<br>";
          }
      } else {
          // If there are empty fields, display them in the error message
          $Msg = 'Error en la preparación de la consulta. Favor de llenar los siguientes campos: ' . implode(', ', $emptyFields);
          $MsgC = 'red'; // Red color indicates an error
          echo $Msg . "<br>"; // Debug output
      }
  } else {
    prnMsgV20('Error: ' . $mensaje . ' ...', 'error');

  }
}
/* fin parte 1 */



$mensaje = '';
/* Parte 2 */
// al click en validar revisar si el archivo es menor a 100,0000 o el archivo es diferente a texto o xml
if ($_POST['Validar'] == 'Validar Archivo') {
  if ($_FILES[FileXML][name]=='' Or $_FILES[FileXML][size]>100000 Or $_FILES[FileXML][type]<>'text/xml') {
    //si el campo del nombre esta vacio 
    if ($_FILES[FileXML][name]=='') { 
      prnMsgV20('Debe indicar el nombre del archivo XML ...','error'); 
      $mensaje = 'Error'; 
    }
    //si el tamaño es mayor a 300,000
	 if ($_FILES[FileXML][size]>300000) { 
	   prnMsgV20('El archivo XML es mayor a 100 KB, no es un archivo correcto...','error'); 
	   $mensaje = 'Error'; 
	 }
     // si el archivo no es xml
	 if ($_FILES[FileXML][type]<>"text/xml") { 
	   prnMsgV20('El archivo no es XML...','error'); 
	   $mensaje = 'Error'; 
    }    
  }
  //condiciones para PDF
  if($_FILES[FilePDF][name]==''){
    prnMsgV20('Debe indicar el nombre del archivo PDF ...','error'); 
      $mensaje = 'Error'; 
  }

  if ($mensaje == '') {
    // Genera una sesión única usando SHA-1 y un identificador único
    $Sesion = sha1(uniqid(mt_rand(), true));
    $nombre =  $SelID . '-'. $SelID_ob . '-' . $P . '-' . $nombre_usuario;

    
    // Define el nombre del archivo XML basado en la sesión generada
    $file_namex = $_SESSION['path_gastos'] . '/xml/xml-' . $nombre . '.xml';
    $file_namepdf = $_SESSION['path_gastos'] . '/pdf/pdf-'. $nombre . '.pdf';


    
    // Intenta mover el archivo subido a la ubicación deseada
    if (!move_uploaded_file($_FILES['FileXML']['tmp_name'], $file_namex)) {
        // Si ocurre un error al mover el archivo, muestra un mensaje de error
        echo 'Error: ' . $_FILES['FileXML']['error'];
        prnMsgV20('No se ha cargado el archivo XML... ' . $file_namex, 'error');
        $mensaje = 'Error'; // Marca el mensaje como un error
    }

    if (!move_uploaded_file($_FILES['FilePDF']['tmp_name'], $file_namepdf)) {
      // Si ocurre un error al mover el archivo, muestra un mensaje de error
      echo 'Error: ' . $_FILES['FilePDF']['error'];
      prnMsgV20('No se ha cargado el archivo PDF... ' . $file_namepdf, 'error');
      $mensaje = 'Error'; // Marca el mensaje como un error
    }
    
    // Imprime un campo oculto en el formulario con el nombre del archivo
    echo '<input type="hidden" name="FileName" value="' . $file_namex . '" />';

    
    // Abre el archivo XML recién subido en modo lectura
    $filexml = fopen($file_namex, "r");
    
    // Lee el contenido del archivo
    $contenido = fread($filexml, filesize($file_namex));

    //obtener el nombre de los archivos
    $nombre_archivo_xml = basename($file_namex);
    $nombre_archivo_pdf = basename($file_namepdf);
    $nombre_archivo_otro = basename($file_nameotro);

    //obtener las extensiones
    $extension_xml = strtolower(pathinfo($nombre_archivo_xml, PATHINFO_EXTENSION));
    $extension_pdf = strtolower(pathinfo($nombre_archivo_pdf, PATHINFO_EXTENSION));
    $extension_otro = strtolower(pathinfo($nombre_archivo_otro, PATHINFO_EXTENSION));

    
    //insertar el archivo xml en la base de datos
    $sql_up = "INSERT INTO CG_archivos (id_comprobacion, nombre, tipo)
                VALUES ($SelID, '$nombre_archivo_xml', '$extension_xml')";
    $res_up = DB_query($sql_up,$db);
    
    // Insertar el archivo PDF en la base de datos
    $sql_up_pdf = "INSERT INTO CG_archivos (id_comprobacion, nombre, tipo)
                    VALUES ($SelID, '$nombre_archivo_pdf', '$extension_pdf')";
    $res_up_pdf = DB_query($sql_up_pdf, $db);

    // // Insertar el archivo PDF en la base de datos
    // $sql_up_ot = "INSERT INTO CG_archivos (id_comprobacion, nombre, tipo)
    //                 VALUES ($SelID, '$nombre_archivo_otro', '$extension_otro')";
    // $res_up_ot = DB_query($sql_up_pdf, $db);



    // Cierra el archivo
    fclose($filexml);
}
}
/* fin parte 2 */

if ($mensaje<>'') $_POST['Validar'] = '';

//al dar click en validar archivo, crear estas variables vacias
if ($_POST['Validar'] == 'Validar Archivo') {
    /*********************************
    *          Encabezado            *
    *********************************/
    $RFCEmisor      = '';
    $FolioFiscal    = '';
 
    $RegFis_Emi     = '';
    $Serie          = '';
    $Condiciones    = '';


    $TipoDocumento  = '';
    $NoSerieCer     = '';


    $RFCReceptor    = '';
    $RS_Receptor    = '';
    $RegFis_Rec     = '';
    $DirFiscal      = '';
    $UsoCFDI        = '';
  


    $SubTotal       = '';
    $Descuento      = '';
    $Imp_Tras       = '';  
    $ImporteFactura = '';
  
    $SelloSAT       = '';
    $SelloCFDI      = '';
    $CadenaSAT      = '';
    /*********************************
    *           Detalle              *
    *********************************/
    $Cant           = '';
    $CveProd        = '';
    $CveUni         = '';
    $UniMed         = '';
    $Descrip        = '';
    $PU             = '';
    $DescUni        = '';
    $ImpUni         = '';  
  
    /*********************************
    *            Version             *
    **********************************/
    $pVersion = strpos($contenido,'version="');
    if ($pVersion == 0) {
      $pVersion = strpos($contenido,'Version="');
      if ($pVersion == 0) $Version = '';
      else $Version = substr($contenido,$pVersion+9,strpos($contenido,'"',$pVersion+10)-($pVersion+9));
    } else $Serie = substr($contenido,$pVersion+9,strpos($contenido,'"',$pVersion+10)-($pVersion+9));
    /*********************************
    *             Serie              *
    **********************************/
    $pSerie = strpos($contenido,'serie="');
    if ($pSerie == 0) {
      $pSerie = strpos($contenido,'Serie="');
      if ($pSerie == 0) $Serie = '';
      else $Serie = substr($contenido,$pSerie+7,strpos($contenido,'"',$pSerie+8)-($pSerie+7));
    } else $Serie = substr($contenido,$pSerie+7,strpos($contenido,'"',$pSerie+8)-($pSerie+7));

    /*********************************
    *            Sello CFDI          *
    **********************************/
    $pSelloCFDI = strpos($contenido,'sello="');
    if ($pSelloCFDI == 0) {
      $pSelloCFDI = strpos($contenido,'Sello="');
      if ($pSelloCFDI == 0) $SelloCFDI = '';
      else $SelloCFDI = substr($contenido,$pSelloCFDI+7,strpos($contenido,'"',$pSelloCFDI+8)-($pSelloCFDI+7)); 
    } else $SelloCFDI = substr($contenido,$pSelloCFDI+7,strpos($contenido,'"',$pSelloCFDI+8)-($pSelloCFDI+7));

    /*********************************
    *     Número Serie del CSD       *
    **********************************/
    $pNoSerCSD = strpos($contenido,'NoCertificado="');
    if ($pNoSerCSD == 0) $NoSerCSD = '';
    else $NoSerCSD = substr($contenido,$pNoSerCSD+15,strpos($contenido,'"',$pNoSerCSD+16)-($pNoSerCSD+15)); 
    /*********************************
    *           Certificado          *
    **********************************/
    $pCertificado = strpos($contenido,'Certificado="');
    if ($pCertificado == 0) $Certificado = '';
    else $Certificado = substr($contenido,$pCertificado+13,strpos($contenido,'"',$pCertificado+14)-($pCertificado+13)); 
    /*********************************
    *            Condiciones         *
    **********************************/
    $pCondiciones = strpos($contenido,'CondicionesDePago="');
    if ($pCondiciones == 0) $Condiciones = '';
    else $Condiciones = substr($contenido,$pCondiciones+19,strpos($contenido,'"',$pCondiciones+20)-($pCondiciones+19)); 
    /*********************************
    *             Moneda             *
    **********************************/
    $pMon = strpos($contenido,'Moneda="');
    if ($pMon == 0) $Moneda = '';
    else $Moneda = substr($contenido,$pMon+8,strpos($contenido,'"',$pMon+9)-($pMon+8));
    /*********************************
    *          Tipo de Cambio        *
    **********************************/
    $pTC = strpos($contenido,'TipoCambio="');
    if ($pTC == 0) $TipoCambio = '0';
    else $TipoCambio = substr($contenido,$pTC+12,strpos($contenido,'"',$pTC+13)-($pTC+12));

    /*********************************
    *         SubTotal Factura       *
    **********************************/
    $pSub = strpos($contenido,'subTotal="');
    if ($pSub == 0) $SubtotalFactura32 = '';
    else $SubtotalFactura32 = substr($contenido,$pSub+10,strpos($contenido,'"',$pSub+11)-($pSub+10));
  
    $pImp = strpos($contenido,'total="');
    if ($pImp == 0) $ImporteFactura32 = '';
    else $ImporteFactura32 = substr($contenido,$pImp+7,strpos($contenido,'"',$pImp+8)-($pImp+7));
    $pSub = strpos($contenido,'SubTotal="');
    if ($pSub == 0) $SubtotalFactura = '';
    else {
      $SubtotalFactura = substr($contenido,$pSub+10,strpos($contenido,'"',$pSub+11)-($pSub+10));
      $pOffSetS   = strpos($contenido,'SubTotal="');
      $pOffSetS_F = strpos($contenido,'"',$pOffSetS);
      $SubString  = substr($contenido,$pOffSetS_F);
      $pImp       = strpos($SubString,'Total="');
      if ($pImp == 0) {
        $pImp = strpos($contenido,'Total="');
      if ($pImp==0) $ImporteFactura = '';
      else $ImporteFactura = substr($contenido,$pImp+7,strpos($contenido,'"',$pImp+8)-($pImp+7));  
      } else $ImporteFactura = substr($SubString,$pImp+7,strpos($SubString,'"',$pImp+8)-($pImp+7)); 
    }
  
    $SubtotalFactura = ($SubtotalFactura==''?$SubtotalFactura32:$SubtotalFactura);
    $ImporteFactura  = ($ImporteFactura==''?$ImporteFactura32:$ImporteFactura);
    /*********************************
    *        Descuentos       *
    **********************************/
    // $pConcepto = strpos($contenido, '<cfdi:Concepto');
    $pDescuento = strpos($contenido, 'Descuento=');

    if ($pDescuento !== false ) {
      // Extract substring starting from the position of Impuesto="003"
      // $start = max(0, $pDescuento - 20);
      // $length = 250;
      // $substring = substr($contenido, $pConcepto, $length); // Get a substring of 200 characters after Impuesto="003"
      
      // Use regex to find Importe value within this substring
      if (preg_match('/Descuento="([\d\.]+)"/', $contenido, $matches)) {
          // Capture the Importe value
          $Descuento = $matches[1];
      } else {
          $Descuento = 0;
      }
    } else {
      $Descuento = 0;
    }
    /*********************************
    *        IVA      *
    **********************************/
    $ptraslados = strpos($contenido, '<cfdi:Impuestos '); //TotalImpuestosTrasladados
    $pImpuesto = strpos($contenido, 'TasaOCuota="0.160000"', $ptraslados);

    // Check if both positions were found
    if ($ptraslados !== false && $pImpuesto !== false && $pImpuesto > $ptraslados) {
      // Extract substring starting from the position of Impuesto="003"
      $start = max(0, $pImpuesto - 50);
      $length = 200;
      $substring = substr($contenido, $start, $length); // Get a substring of 200 characters after Impuesto="003"
      
      // Use regex to find Importe value within this substring
      if (preg_match('/Importe="([\d\.]+)"/', $substring, $matches)) {
          // Capture the Importe value
          $Impuesto = $matches[1];
      } else {
          $Impuesto = 0;
      }
    } else {
      $Impuesto = 0;
    }
    /*********************************
    *        IEPS      *
    **********************************/
    $ptraslados = strpos($contenido, '<cfdi:Impuestos TotalImpuestosTrasladados');
    $pIEPS = strpos($contenido, 'Impuesto="003"', $ptraslados);

    // Check if both positions were found
    if ($ptraslados !== false && $pIEPS !== false && $pIEPS > $ptraslados) {
      // Extract substring starting from the position of Impuesto="003"
      $start = max(0, $pIEPS - 20);
      $length = 150;
      $substring = substr($contenido, $start, $length); // Get a substring of 200 characters after Impuesto="003"
      
      // Use regex to find Importe value within this substring
      if (preg_match('/Importe="([\d\.]+)"/', $substring, $matches)) {
          // Capture the Importe value
          $IEPS = $matches[1];
      } else {
          $IEPS = 0;
      }
    } else {
      $IEPS = 0;
    }


    // $ptraslados = strpos($contenido, '<cfdi:Traslados>');
    // $pIEPS = strpos($contenido, 'Impuesto="003"', $ptraslados);
    
    // // Check if both positions were found
    // if ($ptraslados !== false && $pIEPS !== false && $pIEPS > $ptraslados) {
    //   // Extract substring starting from the position of Impuesto="003"
    //   $start = max(0, $pIEPS - 35);
    //   $length = 150;
    //   $substring = substr($contenido, $start, $length);
      
    //   // Use regex to find Importe value within this substring
    //   if (preg_match('/Importe="([\d\.]+)"/', $substring, $matches)) {
    //       // Capture the Importe value
    //       $IEPS = $matches[1];
    //   } else {
    //       $IEPS = 0;
    //   }
    // } else {
    //   $IEPS = 0;
    // }
    /*********************************
    *        ISH      *
    **********************************/
    $pComplemento = strpos($contenido, '<cfdi:Complemento>');
    $pISH = strpos($contenido, 'ImpLocTrasladado="ISH"', $pComplemento);

    // Check if both positions were found
    if ($pComplemento !== false && $pISH !== false && $pISH > $pComplemento) {
      // Extract substring starting from the position of Impuesto="003"
      $start = max(0, $pISH - 75);
      $length = 150;
      $substring = substr($contenido, $start, $length);
      
      // Use regex to find Importe value within this substring
      if (preg_match('/Importe="([\d\.]+)"/', $substring, $matches)) {
          // Capture the Importe value
          $ISH = $matches[1];
      } else {
          $ISH = 0;
      }
    } else {
      $ISH = 0;
    }
    /*********************************
    *        Impuestos complementos      *
    **********************************/
    $pComplemento = strpos($contenido, '<cfdi:Complemento>');
    $pTUA = strpos($contenido, 'TUA', $pComplemento);
    $pOtrosCargos = strpos($contenido, 'OtrosCargos', $pComplemento);

    // Check if both positions were found
    if ($pComplemento !== false &&  $pTUA !== false && $pTUA > $pComplemento) {
      // Extract substring starting from the position of otros cargos
      $start = max(0, $pTUA - 5);
      $length = 50;
      $substring = substr($contenido, $start, $length);
      
      // Use regex to find Importe value within this substring
      if (preg_match('/TUA="([\d\.]+)"/', $substring, $matches)) {
          // Capture the Importe value
          $TUA = $matches[1];
      } else {
        $TUA = 0;
      }
    } else {
      $TUA = 0;
    }


    // Check if both positions were found
    if ($pComplemento !== false &&  $pOtrosCargos !== false && $pOtrosCargos > $pComplemento) {
      // Extract substring starting from the position of otros cargos
      $start = max(0, $pOtrosCargos - 5);
      $length = 50;
      $substring = substr($contenido, $start, $length);
      
      // Use regex to find Importe value within this substring
      if (preg_match('/TotalCargos="([\d\.]+)"/', $substring, $matches)) {
          // Capture the Importe value
          $I_complementos = $matches[1];
      } else {
        $I_complementos = 0;
      }
    } else {
      $I_complementos = 0;
    }
    $I_complementos = $I_complementos+$TUA;

    /*********************************
    *        Ret. ISR 1.25%      *
    **********************************/
    $pretenciones = strpos($contenido, '<cfdi:Retenciones>');
    $pISR125 = strpos($contenido, 'TasaOCuota="0.012500"');

    // Verificar que el nodo <cfdi:Retenciones> esté presente y que la tasa 0.0125 esté dentro de él
    if ($pretenciones !== false && $pISR125 !== false && $pISR125 > $pretenciones) {
        $Ret_ISR_125 = $SubtotalFactura * 0.0125;
    } else {
        $Ret_ISR_125 = '0';
    }
    /*********************************
    *        Ret. ISR 10%      *
    **********************************/
    $pretenciones = strpos($contenido, '<cfdi:Retenciones>');
    $pISR10 = strpos($contenido, 'TasaOCuota="0.100000"');

    // Verificar que el nodo <cfdi:Retenciones> esté presente y que la tasa 0.0125 esté dentro de él
    if ($pretenciones !== false && $pISR10 !== false && $pISR10 > $pretenciones) {
        $Ret_ISR_10 = $SubtotalFactura * 0.10;
    } else {
        $Ret_ISR_10 = '0';
    }
    /*********************************
    *        Excento      *
    **********************************/
    $pexento = strpos($contenido, 'TipoFactor="Exento"');

    // Verificar que pexento no este vacio
    if ($pexento !== false) {
        $exento = $SubtotalFactura;
        
    } else {
        $exento = '0';
    }
    /*********************************
    *        Ret IVA      *
    **********************************/
    $pretenciones = strpos($contenido, '<cfdi:Retenciones>');
    $pIVA_ret = strpos($contenido, 'TasaOCuota="0.106667"');

    // Verificar que el nodo <cfdi:Retenciones> esté presente y que la tasa 0.010666
    if ($pretenciones !== false && $pIVA_ret !== false && $pIVA_ret > $pretenciones) {
        $Ret_IVA = $SubtotalFactura * 0.106667;
    } else {
        $Ret_IVA = '0';
    }
    /*********************************
    *     Numero de Cer del SAT      *
    **********************************/
    $pNoSerCer = strpos($contenido,'NoCertificadoSAT="');
    if ($pNoSerCer == 0) $NoSerCer = '0';
    else $NoSerCer = substr($contenido,$pNoSerCer+18,strpos($contenido,'"',$pNoSerCer+19)-($pNoSerCer+18));
    /*********************************
    *          Folio UUID            *
    **********************************/
    $PosRelacionados = strpos($contenido,'/cfdi:CfdiRelacionados>');
    if ($PosRelacionados == 0) {
      $pUUID = strpos($contenido,'UUID="');
       if ($pUUID == 0) $FolioFiscal = '';
       else $FolioFiscal = substr($contenido,$pUUID+6,strpos($contenido,'"',$pUUID+7)-($pUUID+6));
    } else {
      $SubXML = substr($contenido,$PosRelacionados+23);
        $pUUID = strpos($SubXML,'UUID="');
        if ($pUUID == 0) $FolioFiscal = '';
        else $FolioFiscal = substr($SubXML,$pUUID+6,strpos($SubXML,'"',$pUUID+7)-($pUUID+6));				
    }
    /*********************************
    *         Fecha Timbrado         *
    **********************************/
    $pFecT = strpos($contenido,'FechaTimbrado="');
    if ($pFecT == 0) $FechaTimbrado = '';
    else $FechaTimbrado = substr($contenido,$pFecT+15,strpos($contenido,'"',$pFecT+16)-($pFecT+15));
  
    /*********************************
    *     Regimen Fiscal Emisor      *
    **********************************/
    $pOffSetE     = strpos($contenido,'cfdi:Emisor');
    $pRegFis_Emi  = strpos($contenido,'regimenfiscal="',$pOffSetE+12);
    if ($pRegFis_Emi == 0) {
      $pRegFis_Emi = strpos($contenido,'RegimenFiscal="',$pOffSetE+12);
      if ($pRegFis_Emi == 0) {
       $pRegFis_Emi = strpos($contenido,'RegimenFiscal ="',$pOffSetE+12);
       if ($pRegFis_Emi == 0) $RegFis_Emi = '';
        else $RegFis_Emi = substr($contenido,$pRegFis_Emi+16,strpos($contenido,'"',$pRegFis_Emi+17)-($pRegFis_Emi+16)); 
      } else $RegFis_Emi = substr($contenido,$pRegFis_Emi+15,strpos($contenido,'"',$pRegFis_Emi+16)-($pRegFis_Emi+15)); 
    } else $RegFis_Emi = substr($contenido,$pRegFis_Emi+15,strpos($contenido,'"',$pRegFis_Emi+16)-($pRegFis_Emi+15));
    /*********************************
    *          RFC Emisor            *
    **********************************/
    $pOffSetE  = strpos($contenido,'cfdi:Emisor');
    $pRFCE     = strpos($contenido,'rfc="',$pOffSetE+12);
    if ($pRFCE == 0) {
      $pRFCE = strpos($contenido,'Rfc="',$pOffSetE+12);
      if ($pRFCE == 0) {
         $pRFCE = strpos($contenido,'Rfc ="',$pOffSetE+12);
         if ($pRFCE == 0) $RFCEmisor = '';
        else $RFCEmisor = substr($contenido,$pRFCE+6,strpos($contenido,'"',$pRFCE+7)-($pRFCE+6)); 
      } else $RFCEmisor = substr($contenido,$pRFCE+5,strpos($contenido,'"',$pRFCE+6)-($pRFCE+5)); 
    } else $RFCEmisor = substr($contenido,$pRFCE+5,strpos($contenido,'"',$pRFCE+6)-($pRFCE+5));
    /*********************************
    *      Razon Social Emisor       *
    **********************************/
    $pOffSetE  = strpos($contenido,'cfdi:Emisor');
    $pRSE     = strpos($contenido,'nombre="',$pOffSetE+12);
    if ($pRSE == 0) {
      $pRSE = strpos($contenido,'Nombre="',$pOffSetE+12);
      if ($pRSE == 0) {
       $pRFCE = strpos($contenido,'Nombre ="',$pOffSetE+12);
       if ($pRSE == 0) $RSEmisor = '';
        else $RSEmisor = substr($contenido,$pRSE+9,strpos($contenido,'"',$pRSE+10)-($pRSE+9)); 
      } else $RSEmisor = substr($contenido,$pRSE+8,strpos($contenido,'"',$pRSE+9)-($pRSE+8)); 
    } else $RSEmisor = substr($contenido,$pRSE+8,strpos($contenido,'"',$pRSE+9)-($pRSE+8));
    /*********************************
    *        Lugar Expedicion        *
    **********************************/
    $pLugExp = strpos($contenido,'LugarExpedicion="');
    if ($pLugExp == 0) $LugExp = '0';
    else $LugExp = substr($contenido,$pLugExp+17,strpos($contenido,'"',$pLugExp+18)-($pLugExp+17));  
    /*********************************
    *          RFC Receptor          *
    **********************************/
    $pOffSetR  = strpos($contenido,'cfdi:Receptor');
    $pFacRFR     = strpos($contenido,'rfc="',$pOffSetR+14);
    if ($pFacRFR == 0) {
      $pFacRFR = strpos($contenido,'Rfc="',$pOffSetR+14);
      if ($pFacRFR == 0) {
        $pFacRFR = strpos($contenido,'Rfc ="',$pOffSetR+14);
        if ($pFacRFR == 0) $FacRFR = '';
          else $FacRFR = trim(substr($contenido,$pFacRFR+6,strpos($contenido,'"',$pFacRFR+7)-($pFacRFR+6)));
      } else $FacRFR = trim(substr($contenido,$pFacRFR+5,strpos($contenido,'"',$pFacRFR+6)-($pFacRFR+5))); 
    } else $FacRFR = trim(substr($contenido,$pFacRFR+5,strpos($contenido,'"',$pFacRFR+6)-($pFacRFR+5)));     
    $RFCReceptor = $FacRFR; 
    /*********************************
    *      Razon Social Receptor     *
    **********************************/
    $pOffSetR  = strpos($contenido,'cfdi:Receptor');
    $pFacRSR     = strpos($contenido,'nombre="',$pOffSetR+14);
    if ($pFacRSR == 0) {
      $pFacRSR = strpos($contenido,'Nombre="',$pOffSetR+14);
      if ($pFacRSR == 0) {
        $pFacRSR = strpos($contenido,'Nombre ="',$pOffSetR+14);
        if ($pFacRSR == 0) $FacRSR = '';
        else $FacRSR = trim(substr($contenido,$pFacRSR+9,strpos($contenido,'"',$pFacRSR+10)-($pFacRSR+9)));
      } else $FacRSR = trim(substr($contenido,$pFacRSR+8,strpos($contenido,'"',$pFacRSR+9)-($pFacRSR+8))); 
    } else $FacRSR = trim(substr($contenido,$pFacRSR+8,strpos($contenido,'"',$pFacRSR+9)-($pFacRSR+8)));      
    /*********************************
    *   Domicilio Fiscal Receptor    *
    **********************************/
    $pOffSetR  = strpos($contenido,'cfdi:Receptor');
    $pFacDFiR     = strpos($contenido,'DomicilioFiscalReceptor="',$pOffSetR+14);
    if ($pFacDFiR == 0) {
      $pFacDFiR = strpos($contenido,'domiciliofiscalreceptor="',$pOffSetR+14);
      if ($pFacDFiR == 0) {
        $pFacDFiR = strpos($contenido,'DomicilioFiscalReceptor ="',$pOffSetR+14);
        if ($pFacDFiR == 0) $FacDFiR = '';
        else $FacDFiR = trim(substr($contenido,$pFacDFiR+26,strpos($contenido,'"',$pFacDFiR+27)-($pFacDFiR+26)));
      } else $FacDFiR = trim(substr($contenido,$pFacDFiR+25,strpos($contenido,'"',$pFacDFiR+26)-($pFacDFiR+25))); 
    } else $FacDFiR = trim(substr($contenido,$pFacDFiR+25,strpos($contenido,'"',$pFacDFiR+26)-($pFacDFiR+25)));
    /*********************************
    *     Regimen Fiscal Receptor    *
    **********************************/
    $pOffSetR  = strpos($contenido,'cfdi:Receptor');
    $pFacRFiR     = strpos($contenido,'RegimenFiscalReceptor="',$pOffSetR+14);
    if ($pFacRFiR == 0) {
      $pFacRFiR = strpos($contenido,'regimenfiscalreceptor="',$pOffSetR+14);
      if ($pFacRFiR == 0) {
        $pFacRFiR = strpos($contenido,'RegimenFiscalReceptor ="',$pOffSetR+14);
        if ($pFacRFiR == 0) $FacRFiR = '';
        else $FacRFiR = trim(substr($contenido,$pFacRFiR+24,strpos($contenido,'"',$pFacRFiR+25)-($pFacRFiR+24)));
      } else $FacRFiR = trim(substr($contenido,$pFacRFiR+23,strpos($contenido,'"',$pFacRFiR+24)-($pFacRFiR+23))); 
    } else $FacRFiR = trim(substr($contenido,$pFacRFiR+23,strpos($contenido,'"',$pFacRFiR+24)-($pFacRFiR+23)));      
    /*********************************
    *           Uso de CFDI          *
    **********************************/
    $pUsoFac = strpos($contenido,'UsoCFDI="');
    if ($pUsoFac == 0) {
      $pUsoFac = strpos($contenido,'UsoCFDI ="');
      if ($pUsoFac == 0) $UsoFac = '';
      else $UsoFac = substr($contenido,$pUsoFac+10,strpos($contenido,'"',$pUsoFac+11)-($pUsoFac+10)); 
    } else $UsoFac = substr($contenido,$pUsoFac+9,strpos($contenido,'"',$pUsoFac+10)-($pUsoFac+9));
    /*********************************
    *          Tipo Documento        *
    **********************************/
    $pTipo = strpos($contenido,'tipoDeComprobante="');
    if ($pTipo == 0) {
      $pTipo = strpos($contenido,'TipoDeComprobante="');
        if ($pTipo == 0) $TipoDocumento = '';
        else $TipoDocumento = substr($contenido,$pTipo+19,strpos($contenido,'"',$pTipo+20)-($pTipo+19)); 
    } else $TipoDocumento = substr($contenido,$pTipo+19,strpos($contenido,'"',$pTipo+20)-($pTipo+19));
    /*********************************
    *       Sello Digital SAT        *
    **********************************/
    $pSelloDSAT = strpos($contenido,'Sello="');
    if ($pSelloDSAT == 0) {
      $pSelloDSAT = strpos($contenido,'sello="');
      if ($pDSelloSAT == 0) $SelloDSAT = '';
      else $SelloDSAT = substr($contenido,$pSelloDSAT+7,strpos($contenido,'"',$pSelloDSAT+8)-($pSelloDSAT+7)); 
    } else $SelloDSAT = substr($contenido,$pSelloDSAT+7,strpos($contenido,'"',$pSelloDSAT+8)-($pSelloDSAT+7));
    /*********************************
    *            Sello SAT           *
    **********************************/
    $pSelloSAT = strpos($contenido,'SelloSAT="');
    if ($pSelloSAT == 0) {
      $pSelloSAT = strpos($contenido,'sellosat="');
      if ($pSelloSAT == 0) $SelloSAT = '';
      else $SelloSAT = substr($contenido,$pSelloSAT+10,strpos($contenido,'"',$pSelloSAT+11)-($pSelloSAT+10)); 
    } else $SelloSAT = substr($contenido,$pSelloSAT+10,strpos($contenido,'"',$pSelloSAT+11)-($pSelloSAT+10));
  
    /*********************************
    *         TOTAL                  *
    **********************************/
    $total_factura = $SubtotalFactura + $Impuesto + $IEPS + $ISH - $Ret_ISR_10 - $Ret_ISR_125 - $Ret_IVA - $Descuento;


  
  echo '<div class="container" style="margin-top:80px"> 
          <div class="row">
            <div class="col-sm-12">
              <div class="card text-left">
                <div class="card-body">
                  <h4>Carga de Facturas Electrónicas</h4>';
                  if ($Msg<>'')
  echo '            <p style="text-align: center;font-family:Arial;font-size:16px;color:'.$MsgC.'">'.$Msg.'</p>'; 
  echo '          <br>';
  echo '          <br>';
  echo '          <div class="slic-form-group row">
                    <label for="contenido" class="col-sm-2 col-form-label">Factura Electrónica</label>
                    <div class="col-sm-8">
                      <textarea class="form-control" rows="5" name="contenido">'.stripslashes($contenido).'</textarea>
                    </div>
                  </div>';              
  echo '          <div class="container">';
  echo '            <table class="table">';
  echo '              <thead>
                        <tr>
                          <th>Informacion</th>
                          <th>Valor</th>
                        </tr>
                      </thead>';
  echo '              <tbody>
                        <tr>
                          <td>Folio Fiscal</td>
                          <td>'.$FolioFiscal.'</td>
                        </tr>
                        <tr>
                          <td>Importe</td>
                          <td>'.number_format($SubtotalFactura,2).'</td>
                        </tr>
                        <tr>
                          <td>Exento</td>
                          <td>'.number_format($exento,2).'</td>
                        </tr>
                        <tr>
                            <td>Descuento</td>
                            <td>'.number_format($Descuento,2).'</td>
                        </tr>
                        <tr>
                            <td>IVA</td>
                            <td>'.number_format($Impuesto,2).'</td>
                        </tr>
                        <tr>
                            <td>IEPS</td>
                            <td>'.number_format($IEPS,2).'</td>
                        </tr>
                        <tr>
                            <td>ISH</td>
                            <td>'.number_format($ISH,2).'</td>
                        </tr>
                        <tr>
                            <td>Impuestos Complementos</td>
                            <td>'.number_format($I_complementos,2).'</td>
                        </tr>
                        <tr>
                            <td>Ret. ISR 1.25%</td>
                            <td>'.number_format($Ret_ISR_125,2).'</td>
                        </tr>
                        <tr>
                            <td>Ret. ISR 10%</td>
                            <td>'.number_format($Ret_ISR_10,2).'</td>
                        </tr>
                        <tr>
                            <td>Ret. IVA</td>
                            <td>'.number_format($Ret_IVA,2).'</td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td>'.number_format($total_factura,2).'</td>
                        </tr>
                        
                        <tr>
                            <td>RFC Emisor</td>
                            <td>'.$RFCEmisor.'</td>
                        </tr>
                        <tr>
                            <td>RFC Receptor</td>
                            <td>'.$RFCReceptor.'</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>';
    echo '</table>';
    $mensaje = '';
    if (rtrim(ltrim($FolioFiscal)) == '') {
      prnMsgV20('El FOLIO FISCAL esta en blanco...','error'); 
      $mensaje = 'Error'; 
    }
    if ('IPQ060425H37' <> $RFCReceptor) {
      prnMsgV20('El RFC del receptor es invalido... NO SE PUEDE CARGAR LA FACTURA','error'); 
      $mensaje = 'Error'; 
    }
    // if ($TaxRef <> $RFCEmisor) {
    //   prnMsgV20('El RFC del emisor es invalido...','error'); 
    //   $mensaje = 'Error'; 
    // }
    if ($Row_UUID['YaUUID'] >= 1) {
      prnMsgV20('El UUID ya fue registrado con anterioridad...','error'); 
      $mensaje = 'Error'; 
    }

    if ($ImporteFactura == '') {
      prnMsgV20('No fue posible obtener el Total de la Factura...','error');
      $mensaje = 'Error';
    }
    if ($SubtotalFactura == '') {
      prnMsgV20('No fue posible obtener el SubTotal de la Factura...','error'); 
      $mensaje = 'Error';
    }

    if ($mensaje == '') {
      prnMsgV20('La factura es valida, puede cargarla al sistema...','success');
        if ($FolioInterno == '' And $Forzar_FI='Si') 
        prnMsgV20('A la factura le falta un folio interno, debe indicar uno...','error');
        
          
  echo '          <br>';
  echo '          <br>';
  echo '          <input type=Submit class="btn btn-primary" name="Cargar" value="Cargar Factura">';
  echo '          <input type=Submit class="btn btn-primary" name="Cancelar" value="Cancelar Carga">';
  echo '        </div>
              </div>
            </div>      
          </div>
        </div>';
  
  echo '</table>';
  echo '<br>';
    }         
  }
  /* fin parte 3 */
  echo '<input type="hidden" name="folio_fiscal"   value="'.$FolioFiscal.'" />';
  echo '<input type="hidden" name="sub_total_factura"   value="'.$SubtotalFactura.'" />';
  echo '<input type="hidden" name="exento"   value="'.$exento.'" />';
  echo '<input type="hidden" name="iva"   value="'.$Impuesto.'" />';
  echo '<input type="hidden" name="descuento"   value="'.$Descuento.'" />';
  echo '<input type="hidden" name="ieps"   value="'.$IEPS.'" />';
  echo '<input type="hidden" name="ish"   value="'.$ISH.'" />';
  echo '<input type="hidden" name="i_complementos"   value="'.$I_complementos.'" />';
  echo '<input type="hidden" name="ret_isr_1_25"   value="'.$Ret_ISR_125.'" />';
  echo '<input type="hidden" name="ret_isr_10"   value="'.$Ret_ISR_10.'" />';
  echo '<input type="hidden" name="ret_iva"   value="'.$Ret_IVA.'" />';
  echo '<input type="hidden" name="total"   value="'.$total_factura.'" />';


////pantalla principal
if ($_POST['Validar'] == '') {
  echo '<div class="container" style="margin-top:80px"> 
  <div class="row">
    <div class="col-sm-12">
      <div class="card text-left">
        <div class="card-body">
          <h4>Carga de Facturas Electrónicas</h4>';
          if ($Msg <> '')
              echo '<p style="text-align: center;font-family:Arial;font-size:16px;color:' . $MsgC . '">' . $Msg . '</p>';  
echo '      <br>';
echo '      <br>';
echo '      <div class="slic-form-group row">
            <div class="col-sm-2"></div>
            <label for="FileXML" class="col-sm-2 col-slic-label">Archivo XML: </label>
            <div class="col-sm-6">
              <input 
                  type="file"
                  class="form-control-file border"
                  id="FileXML"
                  name="FileXML"
                  accept=".xml">
            </div>                      
            <div class="col-sm-2"></div>
          </div>';
echo'<br>';
// New file input for PDF
echo '  <div class="slic-form-group row">
        <div class="col-sm-2"></div>
        <label for="FilePDF" class="col-sm-2 col-slic-label">Archivo PDF: </label>
        <div class="col-sm-6">
          <input 
              type="file"
              class="form-control-file border"
              id="FilePDF"
              name="FilePDF"
              accept=".pdf">
        </div>                      
        <div class="col-sm-2"></div>
      </div>';
echo'<br>';
echo '      <br>';
echo '      <br>';
echo '      <input 
            type="submit" 
            class="btn btn-primary" 
            name="Validar" 
            value="Validar Archivo"
        >';            
echo '    </div>
      </div>
    </div>      
  </div>
</div>';

    }



include('includes/footer.php');
?>


