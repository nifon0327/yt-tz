<?php 
//人员项目
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="staff";
$info=explode("|", $info);
//if (versionToNumber($AppVersion)>=287 && $dModuleId=="main")  $dModuleId="NewMain";//Created by 2014/08/28
if ($dModuleId=="" && $ModuleType=="SAVE") $dModuleId="SAVE";
switch($dModuleId){
	 case "main": //二级主页面
	 /*
		       $LoginUserId=$LoginNumber;
		       $SegmentId=$info[0];
		       if ($SegmentId==3){
			               include "staff/staff_dimission_read.php";
		                }
                  else{		           
		                  include "staff/staff_item_read.php";
		        }
	     break;
	 */
	case "NewMain":
	      $LoginUserId=$LoginNumber;
		  $SegmentId=$info[0];
		  include "staff/staff_item_read.php";
	     break;
	case "Dismiss":
	    $LoginUserId=$LoginNumber;
	    include "staff/staff_dimission_read.php";
	    break;
	 case "list"://明细
	        switch($sModuleId){
			     case "Leave":
			             $Number=$info[0]; 
		                 $ReadBranchSign=0;
		                 include "audit/kq_qj_list.php";
			           break;
			     case "Wage":
			          $Number=$info[0]; 
			           include "staff/staff_list_wage.php";
			           break;
			      case "Cost":
			          $Number=$info[0]; 
			           include "staff/staff_list_cost.php";
			           break;
			      case "Sb":
			          $Number=$info[0]; 
			           include "staff/staff_list_sb.php";
			           break;
			       case "Cpf":
			          $Number=$info[0]; 
			           include "staff/staff_list_cpf.php";
			           break;
			      case "Study":
			      case "EduGrant":
			          $Number=$info[0]; 
			           include "staff/staff_list_study.php";
			           break; 
			       case "Fixed":
			          $Number=$info[0]; 
			           include "staff/staff_list_fixed.php";
			           break;  
			      case "Performance":
			          $Number=$info[0]; 
			           include "staff/staff_list_performance.php";
			           break;    
			       case "CompensatedLeave":
			            $Number=$info[0]; 
			             include "staff/staff_list_compensatedLeave.php";
			           break;  
			      case "CarMaintain":
			      case "CarFine":
			      case "ETC":
			      case "Refuel":
			            $Number=$info[0];
			             include "staff/staff_list_carfee.php";
			           break;
			       case "Bonus":
			       		//add by cabbage 20150108 人員加上獎金列表顯示
			            $Number=$info[0]; 
			             include "staff/staff_list_bonus.php";
			           break;  
			      default:
				       $Number=$info[0];
				        include "staff/staff_list.php";
		           break;
		           
		       }
		       break;
    case "dList": //离职人员
             $checkMonth=$info[0];
             $hidden=0;$jsondata=array();
              include "staff/staff_dimission_list.php"; 
              $jsonArray=$jsondata;
            break;
	 case "Image":
		      $Number=$info[0];
		      include "staff/staff_image.php";
		     break;
    case "SAVE":
            include "staff/staff_updated.php";
             break;
    case "Search":
    		//add by cabbage 20141211 人員搜尋
		    $searchCondition = $info[0];
	        $hidden=0;
	        $jsondata=array();
	        
	        include "staff/staff_dimission_list.php";
            
            $jsonArray=$jsondata;
    		break;
	default:
	    break;
}
?>