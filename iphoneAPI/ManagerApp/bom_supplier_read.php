<?php 
//BOM采购项目
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="Supplier";
$info=explode("|", $info);
switch($dModuleId){
	
	case "segment":
	
		switch ($sModuleId) {
			case "Week":
			    $CompanyId=$info[0];
		        include "bom/supplier_week_read_new.php"; 
		        break;
			break;
			case "WeekSub":
			
				$CompanyId=$info[0];
				$CheckWeeks=$info[1];
			    include "bom/supplier_week_list_new.php"; 
			break;
			
			case "rklist":
		        $CheckMonth=$info[0];
		        include "bom/supplier_rksheet_company.php"; 
		    break;
			case "rklist_sub":
			    $CompanyId=$info[0];
		        $CheckMonth=$info[1];
		        include "bom/supplier_rksheet_company_month.php"; 
		    break;
		              
			
			case "nopaylist":
				$CheckMonth=$info[0];
				include "bom/supplier_nopay_company.php";
			break;
			case "nopay_sub":
			    $CompanyId=$info[0];
		        $CheckMonth=$info[1];
		        include "bom/supplier_nopay_company_month.php"; 
		    break;
		          
		    case "ordered":
		        $CheckMonth=$info[0];
				include "bom/supplier_ordered_company.php";
		    break;
		    case "ordered_sub":
			    $CompanyId=$info[0];
		        $CheckMonth=$info[1];
		        include "bom/supplier_ordered_company_month.php"; 
		    break;
		    
		    case "search":
		    	$SegmentIndex=$info[0];
		        $SearchText=$info[1];
				include "bom/supplier_cg_search.php"; 
		    break;
		    
			
			default :
			 $SegmentIndex=$info[0]==""?0:$info[0];
		   include "bom/supplier_segment_$SegmentIndex.php"; 
			break;
		}
	
	     
		   break;
    case "main": //二级主页面
          $SegmentIndex=$info[0]==""?0:$info[0];
          include "bom/supplier_item_read.php";  
         break;
		     
	 case "1650"://未收
	     switch($sModuleId){
		     case "Week":
		         $CompanyId=$info[0];
		         include "bom/supplier_week_read.php"; 
		       break;
		     default:
			      if ($ModuleType=="SAVE"){
			             include "bom/cg_stocksheet_updated.php";
		          }
		           else{
			         $CompanyId=$info[0];
			         $CheckWeeks=$info[1];
			         include "bom/supplier_week_list.php"; 
			      }
		       break;
	     }
	   	    break;
  case "1183"://未付
	     switch($sModuleId){
		     case "Week":
		         $CompanyId=$info[0];
		         include "bom/supplier_nopay_read.php"; 
		       break;
		     default:
		         $CompanyId=$info[0];
		         $CheckMonth=$info[1];
		         include "bom/supplier_nopay_list.php"; 
		       break;
	     }
	   	    break;
      case "Received"://已送
	       switch($sModuleId){
		       case "List":
		               $CompanyId=$info[0];
		               $CheckMonth=$info[1];
		                include "bom/cg_rksheet_list_1.php"; 
		             break;
	          default:
				   switch($ModuleType){
				         case "ExtList":
							     $CompanyId=$info[0];
						          include "bom/supplier_rksheet_list.php"; 
				          break;
				         default:
					              include "bom/supplier_rksheet_read.php";
					       break;
				     }
				   break;
				 }
	           break;   
	default:
	    break;
}
?>