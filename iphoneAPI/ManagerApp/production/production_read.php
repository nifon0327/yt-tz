<?php 
//生产管理
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="production";
$info=explode("|", $info);
switch($dModuleId){
    case "main"://主页面
		if ($ModuleType=="SAVE") {
			switch ($ActionId) {
				case "LINE":
				case "QXLINE": include "production/order_dfp_updated.php"; break;
				case "RK": 
				case "Inspection":
				      include "production/order_rk_updated.php"; 
				      break;
				case "QXZY":
				case "CJLL": 
					
					include "production/order_dbl_updated.php"; break;
				case "REMARK":
				 include "production/order_dzz_updated.php";break;
				default : break;
			}
			break;
		} else if ($ModuleType=="New") {
			include "production/production_new_mode.php";
			break;
		}  else if ($ModuleType=="IP"){
			include "production/line_ip.php";
			break;
		}  else if ("detail"==$ModuleType) {
			$dfpPage = true;
				$POrderId = $info[0];
			include "production/order_item_list.php";
		}
        if ($NextPage<=0){
             $NextPage=$info[0]==""?1:$info[0];
         }
		 if (versionToNumber($AppVersion)>297) {
			 include "production/production_item_read.php";  
		 } else 
         include "production/production_item_read_old.php";  
       break;
   case "242"://待备料
	{
		if ($ActionId == "QXLINE") {
			include "production/order_dfp_updated.php";
		} else if ($ActionId=="PRINT"){
			include "production/ck_kzy_updated.php";
			}else {
			if ($ModuleType == "detail") {
			$POrderId = $info[0];
			$noOper = $info[1];
			include "production/order_item_list.php";
			} else
			include "production/order_dbl_read.php";
		}
	}	break;

    case "213"://待组装
	{
		if ($ModuleType == "detail") {
			$POrderId = $info[0];
			$DzzSign= true;
			include "production/order_item_list.php";
			break;
		} else
		if ($ModuleType=="SAVE") {
			//补料 ActionId=="BULIAO" StockId=$info[0] 补料数量=$info[1];
			include "production/order_dzz_updated.php";
			break;
		} else if ($ModuleType=="IP"){
			include "production/line_ip.php";
			break;
		}
		if ($info[0]=="_new") {
			
			if ($info[1]=="Over"){
		           $ActionId=$dModuleId=="213"?21302:21301;
	       }$checkWeek=$info[1];
			include "production/order_dzz_read.php";
			break;
		} else if ($info[0]=="new_") {
			if ($info[1]=="Over"){
		           $ActionId=$dModuleId=="213"?21302:21301;
	       }$checkWeek=$info[1];
		   if ($checkWeek == "") { $checkWeek = -1;}
			include "production/order_dzz_read2.php";
			break;
		}
	}
	
    //case "21302":
    case "21300":
    //case "21301":
        switch($ModuleType){
	        case "Pick":
	             $PickModuleId="213";
		          include "submodel/pickdate_read.php";
		         break;
		    case "SAVE":
		          include "production/order_bl_updated.php"; 
		         break;
            default:
               $ActionId=$dModuleId;
	           if ($info[1]=="Over"){
		           $ActionId=$dModuleId=="213"?21302:21301;
	           }
	           $CompnayId=$info[0];
		       $checkWeek=$info[1];
               include "production/order_bled_read.php"; 
       }
        break;
     case "1111"://今日生产
     case "1112"://今日组装
           switch($ModuleType){
               case "MonthList":
			   		$monthParam = $info[0];
					include "production/order_scdj_read_month.php";
			   
			   		  break;
               case "ExtList": {
			  	    $isNew = $info[0];
					if ($isNew == '_new') {
						$checkDate=$info[1];
                   	include "production/order_scdj_list_new.php";
					} else {
						$checkDate=$info[0];
                   	include "production/order_scdj_list.php"; 
					}
                    break;
			   }
			   case "SAVE": {
				   include "production/order_scdj_update.php";
			   }
			   break;
               default:
			       $isNew = $info[0];
					if ($isNew == '_new') {
						include "production/order_scdj_read_new.php";
					} else {
                  	include "production/order_scdj_read.php";
					}
                 break;
           }
          break;
     case "216"://待出
     				if ($ModuleType=="SAVE"){
		              include "order/order_noch_updated.php";
	            } else if( $ModuleType=="ExtList"){
	             $checkWeek=$info[0];
	             $checkCompanyId=$info[1];
	             include "order/order_wait_list1.php";
				 } else {
		            if (versionToNumber($AppVersion)>298 || $LoginNumber == "11965")
					include "order/ch_wait_read1.php";
					else 
					include "order/ch_wait_read.php";
	            }
	            break;
	 		
          break;
       case "1041"://每日出货
           switch($ModuleType){
               case "ExtList":
                   $checkDate=$info[0];
                   include "production/ch_today_list.php";   
                    break;
               default:
                   include "production/ch_today_read.php";
                 break;
           }
          break;
     case "2105"://已出
	     switch($ModuleType){
               case "ExtList":
                   $checkMonth=$info[0];
                   include "order/ch_month_list.php";   
                    break;
               default:
                   include "order/ch_month_read.php";
                 break;
           }
	     break;
	case "WLRK": {//待组装 入库
	if ($ModuleType == "SAVE") {
		include "production/order_rk_updated.php";
	}
	else 
		include "production/order_rk_read.php";
	}
	break;
	case "244"://补料单
	switch ($ModuleType) {
		  case "SAVE":
			include "inware/ck_replenish_updated.php";
			break;
		  case "ExtList":
		    $FromMainPage="production";
		     $SegmentIndex=$info[2];$CheckMonth=$info[1];
		      include "inware/ck_replenish_List0.php";
			break;
		  default:
		    $SegmentIndex=$info[0]==""?0:$info[0];
		     $FromMainPage="production";
			include "inware/ck_replenish_read.php";
			break;
		}
	break;
	default:    
	    break;
}
?>