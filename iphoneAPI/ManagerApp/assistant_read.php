<?php 
//个人助理项目
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="assistant";
switch($dModuleId){
     case "main":
          include "assistant/assistant_item_read.php"; 
         break;
    default:
        switch($ModuleType){
            case "Pick":
	            include "assistant/assistant_pick_read.php"; 
	            break;
	        case "List":
	           switch($dModuleId){
		              case "1190"://出差登记
		                  include "assistant/info_business_read.php"; 
		                  break;
		             case "1346"://请假登记
		                  include "assistant/kq_qj_read.php"; 
		                  break;
		           case "1402"://点餐登记
		                  include "assistant/meals_read.php"; 
		                  break;
		            case "3201"://薪资签收
		                  include "assistant/staff_wage_read.php"; 
		                  break;
		           case "1532"://补休申请
		                  include "assistant/kq_bx_read.php"; 
		                  break;
		          case "1060"://费用报销
		                  include "assistant/adminicost_read.php"; 
		                  break;
	           }
	           break;
	        case "SAVE":
	             $Log_Item="";
	             $info=explode("|", $info);
	             $Date=date("Y-m-d");$DateTime=date("Y-m-d H:i:s");
	             $OperationResult="N";
				 $Operator=$LoginNumber;
				 
	             switch($dModuleId){
	                   case "1190"://出差登记
		                  include "assistant/info_business_updated.php"; 
		                  break;
	                    case "1346"://请假登记
		                  include "assistant/kq_qj_updated.php"; 
		                  break;
		              case "1402"://点餐登记
		                  include "assistant/meals_updated.php"; 
		                  break; 
		               case "3201"://薪资签收
		                  include "assistant/staff_wage_updated.php"; 
		                 break;
		               case "1532"://补休申请
		                  include "assistant/kq_bx_updated.php"; 
		                  break;
		              case "1060"://费用报销
		                  include "assistant/adminicost_updated.php"; 
		                  break;
	             }
	             
	            if ($Log_Item!=""){
			         $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$LoginNumber')";
	                 $IN_res=@mysql_query($IN_recode);
	                 $jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
		        }
	          break;    
        }
	 break;
}
?>