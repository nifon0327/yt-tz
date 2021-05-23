<?php
//选择器
$mySql="";
switch($PickModuleId){
      case "104"://已出
            $curMonth=date("Y-m");
            $checkMonth=$checkMonth==""?$curMonth:$checkMonth;
           $mySql="SELECT M.CompanyId AS Id,C.Forshort AS Name,SUM(S.Qty*S.Price*M.Sign*D.Rate)  AS Amount 
                          FROM $DataIn.ch1_shipmain M
				          LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
			              LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
			              LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency  
			              WHERE M.Estate='0'   AND C.Estate=1   AND DATE_FORMAT(M.Date,'%Y-%m')='$checkMonth'  
			             GROUP BY M.CompanyId ORDER BY Amount DESC";
           break;
      case "107"://配件禁用原因选择
	         $jsonArray[]=array("Id"=>"1","Name"=>"客户换包装");
	         $jsonArray[]=array("Id"=>"2","Name"=>"一年未下单");
	         $jsonArray[]=array("Id"=>"3","Name"=>"配件名重复/备品转入");
	         $jsonArray[]=array("Id"=>"0","Name"=>"其他原因");
	        break; 
      case "ShipType":
            $mySql="SELECT Id,Name FROM $DataPublic.ch_shiptype  WHERE Estate=1 ORDER BY Id ";
       break; 
     case "VisitorType":
           $mySql = "SELECT Id,Name FROM $DataPublic.come_type WHERE Estate=1 order by Id";
       break;
    case "215":
    case "228":
    case "2285"://全检
           //$Floor=$PickModuleId==215?3:6;
           $mySql="SELECT Id,Name FROM $DataIn.qc_scline  WHERE Estate=1 AND Floor='$Floor' ORDER BY Id ";
       break;
   case "2152":
          $mySql="SELECT Id,Cause AS Name FROM $DataIn.qc_causetype WHERE Type=1 AND Estate=1 ORDER BY Id ";
        break;
     
 }
 if ($mySql!=""){
		$myResult = mysql_query($mySql);
		while($myRow = mysql_fetch_array($myResult))
		 {
		       $Id=$myRow["Id"];
		       $Name=$myRow["Name"];
		       $jsonArray[]=array("Id"=>"$Id","Name"=>"$Name");
		}
		if ($PickModuleId==2152){
			  $jsonArray[]=array("Id"=>"-1","Name"=>"其他原因");
		}
}
?>