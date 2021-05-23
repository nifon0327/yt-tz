<?php
//报表项目
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info

$mModuleName="Report";
$info=explode("|", "$info");
$isNew = $info[0];
switch($dModuleId){
          case "11830"://未付
		  		if ($sModuleId == "_leaf") {
					$compId = $info[1];
					$month = $info[2];
					include "report/fk_unpaid_list.php";
					break;
				}
			   if ($sModuleId == "_log") {


					include "report/unpay_unreci_log.php";
					break;
				}
		       if ($isNew && "_new" == $isNew) {
		  			include "report/fk_unpaid_rpt_new.php";
					break;
				}
          case "1183":
               $SelMonth= $info[0];
               $ActionId= $info[1];
	           include "report/fk_unpaid_report.php";
	     break;
        case "122"://未收货款
				if ($sModuleId == "_leaf") {
					$companyID = $info[1];
					$month = $info[2];
					include "report/fk_unreceive_list.php";
					break;
				}
			    if ($sModuleId == "_log") {


					include "report/unpay_unreci_log.php";
					break;
				}
				if ($isNew && "_new" == $isNew) {
		  			include "report/fk_unreceive_rpt_new.php";
				} else {
              		$SelMonth= $info[0];
              		$ActionId= $info[1];
              		include "report/fk_unreceive_report.php";
				}
            break;
		case "Detail": {
			if ($ModuleType == "SAVE") {
				$StuffId=$info[0];
				$tStockQty=$info[1];
				$oStockQty=$info[2];
				include "report/stuff_error_updated.php";
			} else {
				$StuffId=$info[0];
				
				include "report/stuff_detail_sheet_old.php";
			}
			
		}	break;
	     case "125"://损益表

     			$MonthCount = $info[2];
	     		//月份明細	     		
	     		if ($sModuleId == "List") {

	     			//moduleType
              		$type = $ModuleType;
              		
	     			//損益表項目Id
	     			$categoryId = $info[3];
	     			//子類別Id
              		$ItemMid = $info[4];
              		//選擇月份
              		$SelMonth = $info[5];

              		//供應商
              		$SelCompany = "";
              		if (count($info) > 6) {
	              		$SelCompany = $info[6];
              		}
              		
              		if ($categoryId == "subtotal") {
	              		include "report/syb_report_subfunction/list_subtotal.php";
					}
              		else {
			     		include "report/syb_report_list.php";	              		
              		}
              		
		     		break;
	     		}

	     		//首頁
	     		if ($isNew && "_new" == $isNew) {
		     		include "report/syb_report_new.php";
	     		}
	     		else {
		  			include "report/syb_report.php";
				}

         break;
	default:
	    break;
}
?>
