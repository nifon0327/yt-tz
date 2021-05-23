<?php
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");

	include "d:/website/ac/basic/parameter.inc";
	$Action=$argv[1];
	$OtherAction="";
	$CompanyId="";
	switch($Action){
		case 1074://17:00推送
		       $bundleId="ClientApp";
		       $userId="strax";
		        $CompanyId=1074;//Strax
		        $OtherAction="Ascendeo|CGMobile|Mconomy|Skech|Cellular|Puro";
		  break;
     case 1056://22:00推送
		       $bundleId="ClientAppForOntario";
		       $userId="ontario";
		        $CompanyId=1056;//Ontario
		  break;
	}
	
	if ($CompanyId!=""){
	       //今日订单
          $checkDay=date("Y-m-d");
          $Today=date("Y/m/d");
		  $CheckResult=mysql_query("SELECT S.OrderPO  FROM $DataIn.yw1_ordermain M
                        LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
                        WHERE  M.CompanyId IN ($CompanyId)  and  M.OrderDate='$checkDay'  GROUP BY S.OrderPO",$link_id);
                 if($CheckRow = mysql_fetch_array($CheckResult)){
                       $message="$Today  new order PO:";
	                  do{
	                       $OrderPO=$CheckRow["OrderPO"];
	                       $message.=$OrderPO . ";";
	                  }while($CheckRow = mysql_fetch_array($CheckResult));
	                   $userinfo="1";   include "push_apple.php";
                 }
                 
                 //出货信息
                 $CheckResult=mysql_query("SELECT M.InvoiceNO FROM $DataIn.ch1_shipmain M
                        WHERE M.CompanyId IN ($CompanyId) AND M.Date='$checkDay' ",$link_id);
                 if($CheckRow = mysql_fetch_array($CheckResult)){
                       $message="$Today  shipments NO:";
                       $Ids="";
	                  do{
	                       $InvoiceNO=$CheckRow["InvoiceNO"];
	                       $message.=$InvoiceNO . ";";
	                  }while($CheckRow = mysql_fetch_array($CheckResult));
	                  
	                   $userinfo="3";   include "push_apple.php";
                 }
	}

  if ($OtherAction!=""){
     $oActionArr=explode("|", $OtherAction);
     for($k=0;$k<count($oActionArr);$k++){
		      switch($OtherAction){
			  case "Ascendeo": 
			      $bundleId="ClientAppForAscendeo";
		          $userId="Ascendeo";
			      $oCompanyId="1089,1064,1071";
			      break;
			   case "CGMobile":
			      $bundleId="ClientAppForCGMobile";
		          $userId="CGMobile";
			      $oCompanyId="1049,1083";
			      break; 
			  case  "Mconomy":
			     $bundleId="ClientAppForMconomy";
		         $userId="mconomy";
		         $oCompanyId="1066,1084";//Mconomy 
		        break;
		      case "Skech":
		        $bundleId="ClientAppForSkech";
		       $userId="Skech";
		        $oCompanyId="1091";//Skech
		        break;
		     case "Cellular":
		       $bundleId="ClientAppForCellular";
		       $userId="Cellular";
		        $oCompanyId="1004,1059";//Cellular 
		        break;   
		      case "Puro":
		       $bundleId="ClientAppForPuro";
		       $userId="Puro";
		        $oCompanyId="1094";//Puro 
		        break; 
			  default: 
			      $oCompanyId="";
			      break;
		   }
		   if ($oCompanyId!=""){
				    //今日订单
		          $checkDay=date("Y-m-d");
		          $Today=date("Y/m/d");
				  $CheckResult=mysql_query("SELECT S.OrderPO  FROM $DataIn.yw1_ordermain M
		                        LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
		                        WHERE  M.CompanyId IN ($oCompanyId)  and  M.OrderDate='$checkDay'  GROUP BY S.OrderPO",$link_id);
		                 if($CheckRow = mysql_fetch_array($CheckResult)){
		                       $message="$Today  new order PO:";
			                  do{
			                       $OrderPO=$CheckRow["OrderPO"];
			                       $message.=$OrderPO . ";";
			                  }while($CheckRow = mysql_fetch_array($CheckResult));
			                   $userinfo="1";   include "push_apple.php";
		                 }
		                 
		                 //出货信息
		                 $CheckResult=mysql_query("SELECT M.InvoiceNO FROM $DataIn.ch1_shipmain M
		                        WHERE M.CompanyId IN ($oCompanyId) AND M.Date='$checkDay' ",$link_id);
		                 if($CheckRow = mysql_fetch_array($CheckResult)){
		                       $message="$Today  shipments NO:";
		                       $Ids="";
			                  do{
			                       $InvoiceNO=$CheckRow["InvoiceNO"];
			                       $message.=$InvoiceNO . ";";
			                  }while($CheckRow = mysql_fetch_array($CheckResult));
			                  
			                   $userinfo="3";   include "push_apple.php";
		                 }
		   }
     }
} 	  
?>