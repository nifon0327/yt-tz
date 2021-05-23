<?php 
//图例项目
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info

$mModuleName="Chart";
$info=explode("|", "$info");
switch($dModuleId){
     case "104"://已出
     case "2105":
         if ($ModuleType=="Pick"){
				       $PickModuleId="104";
				       $checkMonth=$info[1];
				       include "submodel/pickname_read.php";
		        }
		       else{
		         $CompanyId=$info[0];
		          include "chart/ch_punctuality_curve.php";
		          }
	       	  break;
        break;
	 case "109": //今日新单
	           if (count(explode("-",$info[0]))==3){
				       if ($ModuleType=="Pick"){
						       $PickModuleId="1090";
						       include "submodel/pickdate_read.php";
				        }
				       else{
				         $checkDay=$info[0];
				          include "chart/order_today_chart.php";
				          }
			       	  break;
	       	 }
	  case "210":
	          if ($ModuleType=="Pick"){
				       $PickModuleId="210";
				       include "submodel/pickdate_read.php";
		        }
		       else{
	                 $checkMonth=$info[0];
	                 include "chart/order_month_chart.php";
	            }
	       break;
	  case "101"://未出
	  case "110":
	  case "1232":
	           include "chart/order_noch_chart.php";
	      break;
	case "198":
	        if ($ModuleType=="Pick"){
				       $PickModuleId="198";
				       include "submodel/pickdate_read.php";
		        }
		       else{
				         $checkMonth=$info[0];
				          include "chart/ch_declare_chart.php";
		          }
	       	  break;

	     break;
	default:
	    break;
}
?>