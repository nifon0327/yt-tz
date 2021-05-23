<?php 
//数据统计项目
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="statistics";
$info=explode("|", $info);
switch($dModuleId){
    case "main"://主页面
         if ($NextPage<=0){
             $NextPage=$info[0]==""?1:$info[0];
         }
         
     
         if (($AppVersion == "testversion") || (versionToNumber($AppVersion) > 303)) {//(versionToNumber($AppVersion) > 300) {	//add by cabbage 20150226
	         include "statistics/statistics_item_read_new.php";  
         }
         else {
	         include "statistics/statistics_item_read.php";  
         }
       break;
    case "107"://在库
        switch($ModuleType){
             case "Pick":
                $PickModuleId="107";
                 include "submodel/pickname_read.php";
                break;
	          case "SAVE":
	               include "statistics/ck_stockqty_updated.php";
	            break;
	          case "ExtList":
	             $CompanyId=$info[0];
	             include "statistics/ck_stockqty_list.php"; 
	             break;
			   case "listVC": {
			   		include "statistics/ck_stockqty_cat.php";
					break;
			   }
			   case "TypeList": {
				   $typeID=$info[0];
				   include "statistics/ck_stockqty_type.php";
			   		break;
			   }
			   case "TypeComp": {
				   $typeID=$info[0];
				   $CompanyId=$info[1];
				   include "statistics/ck_stockqty_list_new.php"; 
	             break;
			   }
			   case "search": {
				   $search=$info[0];
				   $searching = TRUE;
				   include "statistics/ck_stockqty_list_new.php"; 
			   } break;
	          default:
			  		if ($info[0] == '_new') {
						include "statistics/ck_stockqty_new.php"; 
					} else {
	               	include "statistics/ck_stockqty_read.php";
					}
	           break;
         }
	    break;
    case "210"://下单
       if (versionToNumber($AppVersion)>=277){//Created by 2014/08/28
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
       }
       else{
        switch($ModuleType){
               case "ExtList":
                   $checkMonth=$info[0];
                   include "statistics/order_month_list.php";   
                    break;
               default:
                   include "statistics/order_month_read.php";
                 break;
           }
        }
       break;
    case "220"://备品
       switch($ModuleType){
               case "ExtList":
                   $checkMonth=$info[0];
                   include "statistics/ck_bp_list.php";   
                    break;
               default:
                   include "statistics/ck_bp_read.php";
                 break;
           }
       break;
    case "1018"://报废
       switch($ModuleType){
               case "ExtList":
                   $checkMonth=$info[0];
                   include "statistics/ck_bf_list.php";   
                    break;
               case "SAVE":
	               include "statistics/ck_bf_updated.php";
	            break;
               default:
                   include "statistics/ck_bf_read.php";
                 break;
           }
       break;
	case "2105"://已出
		if (versionToNumber($AppVersion)>=277){//Created by 2014/08/28
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
		case "198"://报关
	     switch($ModuleType){
               case "ExtList":
                   $checkMonth=$info[0];
                   include "order/ch_declare_list.php";   
                    break;
               default:
                   include "order/ch_declare_read.php";
                 break;
           }
	     break;
	default:  //主页面
	    //  include "statistics/statistics_item_read.php";  
	    break;
}
?>