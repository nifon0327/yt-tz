<?php   
switch($SCodeType){
					case "1":
                          $CodeFile="<a href='dp_printtasks/product_boxlable_print.php?p=$ProductId&Type=$SCodeType&s=' target='_blank'><img src='../images/printer.gif' title='系统生成' width='18' height='18' border='0'></a>"; 
                    break;
                    case "3":
                      $CodeFile="<a href='dp_printtasks/product_bolable_print(new).php?ProductId=$ProductId&Id=$Id' target='_blank'><img src='../images/printer.gif' title='系统生成' width='18' height='18' border='0'></a>"; 
                    break;
					case "2":
                    case "4":
                          $BoxCode=$myRow["BoxCode"];
                          $bField=explode("|",$BoxCode);
						  $BoxCode0=$bField[0];      
						  $BoxCode=$bField[1];
                          if($BoxCode==""){
                             $CodeFile="<divclass='redB'>未设置条码</div>";
                             }
                         else{
                             $CodeFile="<a href='dp_printtasks/product_boxcode_print.php?p=$ProductId&Type=$SCodeType&s='target='_blank'><img src='../images/printer.gif' title='$BoxCode 系统生成' width='18' height='18' border='0'></a>" ;
                             }
                   break;
	}
	

	
?>