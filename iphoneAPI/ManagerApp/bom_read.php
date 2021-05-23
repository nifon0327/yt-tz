<?php 
//BOM采购项目
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="bom";
$info=explode("|", $info);
switch($dModuleId){
   case "main": 
   case "newMain": //新
          $SegmentIndex=$info[0]==""?0:$info[0];
          include "bom/bom_item_read.php";  
         break;
    case "person":
    
    $person = $info[0];
    $segIndex = $info[1];
    if ($sModuleId=='weeklist') {
	    $BuyerId = $person;
	    $CheckWeeks = $info[2];
	      include "bom/bom_weeklist.php";  
    } else {
	     if ($ModuleType=="SAVE"){
		       include "bom/cg_stocksheet_updated.php";
	     } else {
		      include "bom/bom_person_$segIndex.php";  
	     }
    }
     

    	break;
         /*
	 case "main": //二级主页面
	       include "bom/bom_item_read_old.php";
	     break;
	    */
	case "1184"://下单
	     switch($ModuleType){
		     case "SAVE"://新
		          include "bom/cg_stocksheet_updated.php";
		          break;
		    case "List"://新
		            $Id=$info[0];
		            include "bom/bom_sub_1_list.php";
		          break;
		    case "UpdateList"://新
		          $Id=$info[0];
		          $PickSign=$info[1]=="Pick"?1:0;
		           include "bom/cg_update_list.php"; 
		         break;
		     case "ExtList":
		          $CompanyId=$info[0];
	              $BuyerId=$info[1];
	              $ColSign=$info[2];
		          include "bom/cg_stocksheet_list_old.php"; 
		        break;
		     default:
		           $BuyerId=$info[0];
	               $ColSign=$info[1];
		           include "bom/cg_stocksheet_read.php"; 
		        break;
	     }
	    break;
	 case "newAdd"://新增
	         switch($ModuleType){
		          case "StuffName":
				          $SearchText=$info[0];
				          $PickSign=$info[1]=="Pick"?1:0;
				           include "bom/bom_stuffcname_read.php"; 
		           break;
		         case "SAVE":
		                  include "bom/cg_stocksheet_updated.php";
		           break;
		     }
		    break;
	case "Received"://已收
	       switch($sModuleId){
		       case "List":
		               $CheckMonth=$info[0];
		               $CompanyId=$info[1];
		                include "bom/cg_rksheet_list_1.php"; 
		             break;
	          default:
				   switch($ModuleType){
				         case "ExtList":
							     $CheckMonth=$info[0];
						          include "bom/cg_rksheet_list.php"; 
				          break;
				         default:
					              include "bom/cg_rksheet_read.php";
					       break;
				     }
				   break;
				 }
	           break; 
	case "POrder"://已下单
			   switch($ModuleType){
			         case "ExtList":
						     $CheckDate=$info[0];
					          include "bom/cg_porder_list.php"; 
			          break;
			         default:
				              include "bom/cg_porder_read.php";
				       break;
			     }
	           break;    
	 case "1650"://未收
	     if ($ModuleType=="SAVE"){
		       include "bom/cg_stocksheet_updated.php";
	     }
	     else{
	           $BuyerId=$info[0];
	            $CheckWeeks=$info[1];
	             include "bom/bom_sub_2_weeklist.php"; 
	    }
	    break;
	 case "165"://未收
	     if ($ModuleType=="ExtList"){
		      $CompanyId=$info[0];
             $BuyerId=$info[1];
              $ColSign=$info[2];
	          include "bom/cg_deliverydate_list.php"; 
	     }
	     else{
	           $BuyerId=$info[0];
	           $ColSign=$info[1];
		       include "bom/cg_deliverydate_read.php"; 
	     }
	    break;
	 case "1182"://未补
	      switch($ModuleType){
	          case "List"://新
	               $StuffId=$info[0];
		            include "bom/bom_sub_4_list.php";
	             break;
	         case "ExtList":
				     $CompanyId=$info[0];
		             $BuyerId=$info[1];
		             $ColSign=$info[2];
			          include "bom/cg_bcsheet_list.php"; 
	          break;
	         default:
		           $BuyerId=$info[0];
		           $ColSign=$info[1];
		           if ( $ColSign=="Over"){
			          include "bom/cg_bcsheet_overtime.php";  
		           }
		           else{
			           include "bom/cg_bcsheet_read.php"; 
			       }
		       break;
	     }
	    break;   
	 case "1183"://未付货款
	       include "bom/cg_fk_report.php";
	     break;
	  case "StuffDetail"://采购详情
	      $Id=$info[0];
	       include "bom/cg_stuffdetail_read.php";
	     break;
	  case "PIDate"://采购交期变更日志
	       $StockId=$info[0];
	       include "bom/cg_deliverydate_log.php";
	       break;
	default:
	    break;
}
?>