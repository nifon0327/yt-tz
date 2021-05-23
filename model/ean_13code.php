<?php 
//二合一已更新
//include "../model/shiplablefun.php";
//echo $Code;
//EAN_13($Code,$lw,$hi);
if (strlen($Code)==13 && $NewCode==1){
    $Codetext=$Code;
    $Codebar='BCGean13';
    include "ean_barcode.php";
}
else{
	include "../model/shiplablefun.php";
    EAN_13($Code,$lw,$hi);
}


?>  
