<?php 
//业务处理
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info

$mModuleName="Merchandiser";
$info=explode("|", "$info");
switch($dModuleId){
      case "main":
         switch($ModuleType){
               case "ExtList":
                     switch($sModuleId){
	                     case "101"://未出
	                          $checkNumber=$info[0];
	                          include "merchandiser/order_noch_list.php";
	                          break;
	                     case "102"://待出
	                        $checkNumber=$info[0];
	                         include "merchandiser/order_wait_list.php";
	                        break;
                     }
                    break;
              case "SAVE":
                     include "merchandiser/merchandiser_updated.php";
                    break;
             case "Pick":
                    $PickModuleId="ShipType";
		             include "submodel/pickname_read.php";
		             break;
               default:
                      include "merchandiser/merchandiser_item_read.php"; 
                  break;
           }
          break;
      default:
         break;
}
?>