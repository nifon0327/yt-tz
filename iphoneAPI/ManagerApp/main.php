<?php 
//主入口文件
$CURWEEK_BGCOLOR="#E8F1F7";//"#CCFF99"
$CURWEEK_TITLECOLOR="#3888B6";
$TITLE_GRAYCOLOR="#86898A";
$TEXT_GREENCOLOR="#00A945";
$FORSHORT_COLOR="#308CC0";

//数据库连接参数
include "../../basic/parameter.inc";
 include "../subprogram/myfunction.php";
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
//传入参数
$MC_ReadModuleType ='App';
$MC_FactoryCheckSign=$factoryCheck=='on'?1:0;

//公用密匙
$public_key = 'ashcloud.com';

$LoginNumber=$_POST["LoginNumber"]==""?$LoginNumber:$_POST["LoginNumber"];
$LoginKeys=$_POST["KEY"];//用于认证安全，未使用
$AppVersion=$_POST["AppVersion"]==""?$AppVersion:$_POST["AppVersion"];
$ModuleId=$_POST["ModuleId"]==""?$ModuleId:$_POST["ModuleId"]; //含N级子模块，用"|"分开，目前分三级
$ModuleType=$_POST["ModuleType"]==""?$ModuleType:$_POST["ModuleType"];//表（默认值）/图例 /报表 /选择/保存
$ActionId=$_POST["ActionId"]==""?$ActionId:$_POST["ActionId"]; //操作 READ（默认值）/ADD/UPDATE/DEL
$info=$_POST["info"]==""?$info:$_POST["info"];   //含N个传入参数，用"|"分开
$NextPage=$_POST["NextPage"]==""?$NextPage:$_POST["NextPage"]; 

if ($LoginNumber!=""){
	$ReadAccessSign=5;
	include "user_access.php";  //用户权限
	if ($Login_Estate!=1){//帐号无效 或更改密码
	//	echo 'Error! Need relogin!';
		exit;
	} 
	
	if ($LoginKeys!=''){
		$new_key= hash('md5', $public_key . $Login_uPwd . date('Y') . date('W' ));
		if ($LoginKeys!=$new_key)   {
		  //    echo "Error! Need relogin!";  exit;
		}
	}
	
	if ($Login_uType==4 && $LoginNumber!='50019' && $ModuleType=="SAVE" ) exit; //外部人员帐号不能操作
	$ReadAccessSign="";
}

/*
if ($LoginNumber==10868){
		$fp = fopen("test.log", "a");
		fwrite($fp, date("Y-m-d H:i:s") . "    ModuleId=" . $ModuleId .  "  info=". $info . "\r\n");
		fclose($fp);  
}
*/        
         
$ModuleArray=explode("|", "$ModuleId");
$mModuleId=$ModuleArray[0]; //一级模块Id
$dModuleId=$ModuleArray[1];//二级模块Id
$sModuleId=$ModuleArray[2];//三级模块Id

$jsonArray = array(); //返回结果数组
$NoEchoSign=0; 
$mModuleName="";
switch($mModuleId){
	 case "Main": //主页面
	    $mModuleName=versionToNumber($AppVersion)>315?"main":"main_old";
	     break;
	  case "Badge"://标记
	    if ($info=="Share"){
		     $mModuleName="badge_share";
	    }
	    else{	     
	         $mModuleName="badge";
	    }
	     break;
	   case "TodayWidget"://今日
	       $selectDate =date("Y-m-d");
	      // $selectDate ="2015-03-25";
	      if ($Login_uType!=4){
		      include "calendar/calendar_list.php";
	      }
		break;
      /*
	   case "NewTodayWidget"://今日//已弃用
	     $mModuleName="todaywidget";
	     break;
	  */
	 case "Bom":
	 case "bom":
	 case "107"://BOM采购
	     $mModuleName="bom";  
	     break; 
	case "Supplier"://按供应商显示
	      $mModuleName="bom_supplier";
	     break;   
	case "Order":
	case "110"://未出
	    $mModuleName="order";
	     break;
   case "Client"://按客人显示
	    $mModuleName="client";
	     break;
   case "101"://通知  
	     $mModuleName="bulletin";
	     break;
	case "102"://监控 
	     $mModuleName="cam";
	     break;
   case "103"://人员
   case "203":
         $mModuleName="staff";
        break;
   case "105"://数据统计
          switch($dModuleId){
            case 110:  $mModuleName="order";          break;
	        case 213:  $mModuleName="production"; break;
	        case 216:  $mModuleName=$sModuleId==""?"production":"order"; break;
      	   default:
      	        $mModuleName="statistics";
      	       break;
      }
	     break;
   case "106"://审核
   case "1044"://审核
        $mModuleName="audit";
        break;
		
	case "133": { //new 新增的 仓库
	$mModuleName="inware";
		break;
	}
		
  case "108"://包装
      switch($dModuleId){
	        case 2105:
      	       $mModuleName="statistics";
      	       break;
      	   default:
      	        $mModuleName="production";
      	       break;
      }
       break;
 case "113"://个人助理
       $mModuleName="assistant";
       break;
  case "114"://证书
       $mModuleName="certificate";
       break;
  case "115"://门禁
       $mModuleName="doorcontrol";
       break;
  case "116"://产品查询
        $mModuleName="scan";
       break;
  case "117"://授权书
       $mModuleName="authorize";
       break;
  case "118"://业务处理
      $mModuleName="merchandiser";
      break;
  case "120"://开发
        $mModuleName="develop";
      break;
  case "121"://公司简介
       $mModuleName="about";
       break;
  case "123"://启动页
       $mModuleName="launch";
       break;
  case "Chart"://图例
  case "Curve"://曲线图
        $mModuleName="chart";
	     break;
  case "Report"://报表
       $mModuleName="report";
       break;
  case "Detail"://共用明细
       $mModuleName="detail";
       break;
  case "100":	//行事曆 add by cabbage 201502
  		if ($Login_uType!=4){
  		     $mModuleName = "calendar";
  		}
  		break;
  case "124":	//產品目錄 add by cabbage 20150210
		$mModuleName = "productlist";
  		break;
		
	case "128":
	case "stuff":
	$mModuleName = "stuff";
		break;
	case "104":
	$mModuleName = "nobom";
	break;
	default:
	break;
}
if ($mModuleName!=""){
	include  $mModuleName . "_read.php";
}

if ($NoEchoSign==0)  echo json_encode($jsonArray);
?>