<?php 
//客户未出项目
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info

$mModuleName="Client";
$info=explode("|", "$info");
switch($dModuleId){
    	case "110"://未出
	     switch($sModuleId){
	       case "List0":
	            $checkCompanyId=$info[0];
	             include "order/client_noch_list0.php";
	            break;
	      case "List1":
	             $checkCompanyId=$info[0];
	             $checkWeek=$info[1];
	             //include "order/order_noch_list1.php";
	             
	             if ($checkWeek=="WAIT"){
		              include "order/order_wait_list1.php";
	             }
	             else{
	                 include "order/order_noch_list1.php";
	             }
	             
	            break;
	      default:
	             include "order/client_noch_read.php";
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