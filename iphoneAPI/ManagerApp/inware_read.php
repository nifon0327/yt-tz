<?php 
//生产管理
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
/*if ($LoginNumber=='10868' && $ModuleType=="SAVE") {
    $jsonArray = array("ActionId"=>"$ActionId","Result"=>"Y","Info"=>"测试成功");
    echo json_encode($jsonArray);
    exit;
}
*/

$mModuleName="production";
$info=explode("|", $info);
$List_StuffDetail_Sign=($LoginNumber==10868 || $LoginNumber==10341 || $LoginNumber==10068)?true:false;
switch($dModuleId){
    case "main"://主页面
        if ($NextPage<=0){
             $NextPage=$info[0]==""?1:$info[0];
         }
         
          include "inware/inware_item_read.php"; 
/*
         if (versionToNumber($AppVersion)>=324) {
	           
         }
         else 
         if (versionToNumber($AppVersion)>298){
              include "inware/inware_item_read_v298.php";  
         }
         else{
	         include "inware/inware_item_read_old.php";   
         }
*/
        
       break;
   case "12760"://二收
   case "12761"://三收
   case "1276"://1A
	       switch($ModuleType){
		           case "SAVE":
		           $Floor=getFloor($dModuleId);
				          include "inware/ck_sh_updated.php"; 
				         break;
		            default:
		                 $Floor=getFloor($dModuleId);
		                 include "inware/ck_sh_read.php"; 
		                 break;
	       }
       break;
    case "228"://抽检
    case "2285"://1A抽检
    case "215"://全检
		     switch($ModuleType){
			        case "Pick":
			             $PickModuleId=$dModuleId;
			              $Floor=getFloor($dModuleId);
			             include "submodel/pickname_read.php"; 
			        break;
			       case "SAVE":
			            include "inware/ck_sh_updated.php"; 
			           break;  
			     
			        default:
			           $SegmentIndex=$info[0];
			           $Floor=getFloor($dModuleId);
				       include "inware/ck_sh_reach.php"; 
			        break;
		     }  
        break;
   case "2286": //1A
   case "2280"://3A
   case "2150"://分线检
	      switch($ModuleType){
	            case "SAVE":
	             $Floor=getFloor($dModuleId);
			            include "inware/ck_qc_updated.php";   
			       break;
			     case "history":
			       		include "inware/ck_qc_history.php";
			       break;
			    default:
			           $LineId=$info[0];
			           $Floor=getFloor($dModuleId);
			           
                       include "inware/ck_qc_line.php"; 
                    break;
	      }
        break;
   case "2287":
   case "2282":
   case "2152"://品检记录
          switch($ModuleType){
	         case "printInfo":
	         	$focusId = $info[0];
	         include "inware/ck_th_printinfo.php";   

	         break;
	            case "Pick":
			         $PickModuleId="2152";
		              include "submodel/pickname_read.php";  
			       break;
			     case "SAVE":
			          include "inware/ck_qc_updated.php";   
			       break;
			     case "ExtList":
			        if ($info[0]=="List1"){
			            $CheckDate=$info[1];
			            
			            $Floor=getFloor($dModuleId);
			            // $floor = getFloor dmoduleid 
			            
			            
			            if ($Floor==3 && versionToNumber($AppVersion)>=333) {
				            include "inware/ck_qc_record_list1_new.php"; 
			            } else 
			            
				         include "inware/ck_qc_record_list1.php";  
			        } else if ($info[0]=="List4") {
				        $CheckCompany=$info[1];
			            $Floor=getFloor($dModuleId);
				         include "inware/ck_qc_record_list4.php"; 
			        } else if ($info[0]=="List3") {
				        $CheckMonth=$info[1];
			            $Floor=getFloor($dModuleId);
				         include "inware/ck_qc_record_list3.php"; 
			        }
			       else{
			            $CheckMonth=$info[1];
			            $Floor=getFloor($dModuleId);
				         include "inware/ck_qc_record_list0.php";  
			       }
			       break;
			    default:
			         $SegmentIndex=$info[0];
			         
			         $Floor=getFloor($dModuleId);
			          $noSegment = 0;
			         if (($dModuleId=='2287' || $dModuleId=='2282') && versionToNumber($AppVersion)>=321) {
				         $noSegment = 1;
			         }
			         if ($SegmentIndex=='switch') {
				         
					 include "inware/ck_qc_record_switch.php";
			         } else {
				        
					 include "inware/ck_qc_record.php"; 
			         } 
			   break;
		     }
        break;
   case "2288":
   case "2281":
   case "2151"://待入库
     /*
   		if ($info[0]=="_new") {
			include "inware/ck_rk_wait_new.php"; 
			break;
		}
	*/	
        switch($ModuleType){
	            case "SAVE":
			            include "inware/ck_qc_updated.php";   
			       break;
			    default:
			          $Floor=getFloor($dModuleId);
                       include "inware/ck_rk_wait.php"; 
                    break;
	      }
        break;
    case "1263"://退料记录
        if ($sModuleId=="Label"){
               switch($ModuleType){
	            case "SAVE":
			            include "inware/ck_th_updated.php";   
			       break;
			    default:
                       $Id=$info[0];
		                include "inware/ck_th_label.php"; //标签内容
                    break;
	          }   
	      }
	      else{
	         switch($ModuleType){
			     case "SAVE":
			          include "inware/ck_th_updated.php";   
			       break;
			    case "ExtList":
			        if ($info[0]=="List1"){
			            $CheckDate=$info[1];$Floor=$info[3];
			             if (versionToNumber($AppVersion)>=333){
				             $andCompany = "";
				             // i should not care about this things
				             // i should not care about this things we 
			            if (count($info)>=5) {
				            $Floor=$info[4];
				            $andCompany = $info[2];
			            }
				              include "inware/ck_th_record_list1_new.php";
			              }
			              else
				         include "inware/ck_th_record_list1.php";  
			        }
			        else if ($info[0]=="ListC") {
				        $CheckCompany=$info[1];
			           $Floor=$info[3];
				         include "inware/ck_th_read_listC.php"; 
			        }
			       else{
			            $CheckMonth=$info[1];$Floor=$info[3];
				         include "inware/ck_th_record_list0.php";  
			       }
			       break;
			  default:
			          $SegmentIndex=$info[0];
		              $Floor=$info[1];
		              if (versionToNumber($AppVersion)>=333){
			            
				              include "inware/ck_th_read_new.php";
			              }
			              else {
				              include "inware/ck_th_read.php"; 
			              }
				       
			   break;
			   }
		  }
        break;
    case "212"://可占用
   		  if ($ModuleType == "History") {
			  //='$CompanyId' and S.ProductId='$ProductId'
			  $CompanyId=$info[0];
			  $ProductId=$info[1];
	   		 include "production/ck_kzy_history.php";
	     }  else if ($ModuleType == "SAVE") {
			 include "production/ck_kzy_updated.php";
		 } else if ($ActionId=="Lock"){
		      include "production/order_bl_updated.php"; 
	     }
	     else{
			 if ($info[0]=="_new") {
				
				 include "production/ck_kzy_read.php";
			 } else {
	       $checkWeek=$info[1];
	       $checkWeek=($checkWeek==0 && strlen($checkWeek)<2)?"Over":$checkWeek;
	        include "production/order_bl_read.php"; 
			 }
	    }
        break;
	case "241"://待分配
	{
		if ($ModuleType == "SAVE") {
			if ($ActionId == "QXZY") {
				include "production/order_dbl_updated.php";
			} else
				include "production/order_dfp_updated.php";
		} else {
			$hasOper = true;
			if ($ModuleType == "detail") {
			$POrderId = $info[0];
			$dfpPage=true;
			include "production/order_item_list.php";
			} else
			include "production/order_dfp_read.php";
		}
	}	break;
	
	case "242"://待备料
	{
		if ($ModuleType == "SAVE") {
			if ($ActionId == "QXLINE" || $ActionId == "LINGQI")
				include "production/order_dfp_updated.php";
			else if ($ActionId == "PRINT")
			include "production/ck_kzy_updated.php";
			else 
				include "production/order_dbl_updated.php";
		} else if ($ModuleType == "list") {
			$dateArg = $info[0];
			include "production/order_ybl_detail.php";
		}else if ($ModuleType == "detail") {
			$POrderId = $info[0];
			include "production/order_item_list.php";
			}  
			else
			{
			include "production/order_dbl_read.php";
			}
		}	break;
	case "243"://外发备料
	{
		if ($ModuleType == "SAVE") {
			include "production/ck_wfbl_updated.php";
		} else {
			include "production/ck_wfbl_read.php";
		}
	}	
	break;
	case "244"://待补货
	{
		switch ($ModuleType) {
		  case "SAVE":
			include "inware/ck_replenish_updated.php";
			break;
		  case "ExtList":
		     $SegmentIndex=$info[2];$CheckMonth=$info[1];
		     
		      include "inware/ck_replenish_List0.php";
			break;
		  default:
		    $SegmentIndex=$info[0]==""?0:$info[0];
		      if ((versionToNumber($AppVersion)>=324)) {
			     include "inware/ck_replenish_read_new.php";
		     } else
			include "inware/ck_replenish_read.php";
			break;
		}
	}	break;

	
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
	     case "220"://备品
       switch($ModuleType){
               case "ExtList":
                   $checkMonth=$info[0];
                   include "statistics/ck_bp_list.php";   
                    break;
                case "SAVE":
                include "statistics/ck_bp_updated.php";  
                break;
                
               default:
               		 if ((versionToNumber($AppVersion)>=327)) {
	               		 include "statistics/ck_bp_read_new.php";
               		 } else {
	               		 include "statistics/ck_bp_read.php";
               		 }
                   
                 break;
           }
       break;
    case "1018"://报废
    $ver_327 =  versionToNumber($AppVersion)>=327 ? true : false;
     
       switch($ModuleType){
               case "ExtList":
                   $checkMonth=$info[0];
                   if ($ver_327==true)
                   include "statistics/ck_bf_list_new.php";   
                   else 
                   include "statistics/ck_bf_list.php";   
                    break;
               case "SAVE":
	               include "statistics/ck_bf_updated.php";
	            break;
               default:
                if ($ver_327==true)
                   include "statistics/ck_bf_read_new.php";   
                   else 
                   include "statistics/ck_bf_read.php"; 
                 break;
           }
       break;
	default:    
	    break;
}

function getFloor($ModuleId){
	switch($ModuleId){
		 case "1276"://1A
		 case "2285":
		 case "2286":
		 case "2287":
	     case "2288":
		   $Floor=12;
		   break;
		case "12760"://3A
		case "228":
		case "2280":
		case "2281":
		case "2282":
		  $Floor=6;
		   break;
	   case "12761"://3B
	   case "215":
	   case "2150":
	   case "2151":
	   case "2152":
	   default:
	      $Floor=3;
		   break;
	}
	return $Floor;
}
?>