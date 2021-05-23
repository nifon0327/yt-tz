<?php 
//BOM采购项目
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info

$mModuleName="Order";
$info=explode("|", "$info");
//if (versionToNumber($AppVersion)>=277 && $dModuleId==104)  $dModuleId= "104_New";//Created by 2014/08/28
//if (versionToNumber($AppVersion)>=277 && $dModuleId==109)  $dModuleId= "109_New";//Created by 2014/08/28
switch($dModuleId){
      case "109"://新订单 
         switch($ModuleType){
              case "Switch"://月份
                   switch($sModuleId){
				       case "List0":
				            $checkMonth=$info[0];
				             include "order/order_month_list0.php";
				            break;
				      case "List1":
				             $checkMonth=$info[0];
				             $checkCompanyId=$info[1];
				             include "order/order_month_list1.php";
				            break;
				       default :
				             include "order/order_month_read.php";
				            break;
			      }
                break;
            default://日
		           switch($sModuleId){
			       case "List0":
			            $checkDate=$info[0];
			             include "order/order_today_list0.php";
			            break;
			      case "List1":
			             $checkDate=$info[0];
			             $checkCompanyId=$info[1];
			             include "order/order_today_list1.php";
			            break;
			       default :
			             include "order/order_today_read.php";
			            break;
			      }
			    break;
		  }
        break; 
      case "104"://已出 Created by 2014/08/28 
      case "111":
         switch($ModuleType){
              case "Switch"://以客人排序
                   switch($sModuleId){
				       case "List0":
				            $checkCompanyId=$info[0];
				             include "order/ch_client_list0.php";
				            break;
				      case "List1":
				             $checkMonth=$info[0];
				             $checkCompanyId=$info[1];
				             include "order/ch_month_list1.php";
				            break;
				       default :
				             include "order/ch_client_read_new.php";
				            break;
			      }
                break;
            default://以月份排序
		           switch($sModuleId){
			       case "List0":
			            $checkMonth=$info[0];
			             include "order/ch_month_list0.php";
			            break;
			      case "List1":
			             $checkMonth=$info[0];
			             $checkCompanyId=$info[1];
			             include "order/ch_month_list1.php";
			            break;
			       default :
			             include "order/ch_month_read_new.php";
			            break;
			      }
			    break;
		  }
        break; 
        /*
      case "104"://已出
         if ($sModuleId=="Switch"){
	           switch($ModuleType){
	               case "ExtList":
	                   $checkCompanyId=$info[0];
	                   include "order/ch_client_list.php";   
	                    break;
	               default:
	                   include "order/ch_client_read.php";
	                 break;
	           }
         }
         else{
	           switch($ModuleType){
	               case "ExtList":
	                   $checkMonth=$info[0];
	                   include "order/ch_month_list.php";   
	                    break;
	               default:
	                   include "order/ch_month_read.php";
	                 break;
	           }
           }
          break;
	 case "109": //今日新单
	       if ($ModuleType=="Pick"){
		       $PickModuleId="109";
		       include "submodel/pickdate_read.php";
	       }
	       else{
		         if ($sModuleId=="ExtMain" || $ModuleType=="ExtList"){
		             if ($ModuleType!="ExtList"){
			             include "order/order_today_ext_read.php";
			          }
			          else{
			              $checkDate=$info[0];
				          include "order/order_today_ext_list.php";
			          }
		         }
		         else{
			           $checkDate=$info[1];
			           $SearchText=$info[2];
			           include "order/order_today_read_old.php";
		           }
	        }
	     break;
	    */
	case "110"://未出
	     switch($sModuleId){
	       case "List0":
	            $checkWeek=$info[0];
	             include "order/order_noch_list0.php";
	            break;
	      case "List1":
	             $checkWeek=$info[0];
	             $checkCompanyId=$info[1];
	             include "order/order_noch_list1.php";
	            break;
	       case "New":
	       default:
	            if ($ModuleType=="SAVE"){
		              include "order/order_noch_updated.php";
	            }
	            else{
	                 include "order/order_noch_read.php";
	             }
	            break;
	     }
	      break;
	 case "216"://待出
	      switch($sModuleId){
	       case "List0":
	            $checkWeek=$info[0];
	             include "order/order_wait_list0.php";
	            break;
	      case "List1":
	             $checkWeek=$info[0];
	             $checkCompanyId=$info[1];
	             include "order/order_wait_list1.php";
	            break;
	       }
	      break;
	 case "Detail"://订单详情
	        $POrderId=$info[0];
	        include "order/order_detail_read.php";
	     break;
	 case "InvoiceNO":
	       $InvoiceNO=$info[0];
	        include "order/ch_invoice_read.php";
	       break;
	default:
	    break;
}
?>