<?php 
//退货标签信息 
$myResult=mysql_query("SELECT  S.Id,S.StuffId,S.StockId,S.Qty,S.Date,D.StuffCname,M.CompanyId,P.Forshort  
                        FROM $DataIn.qc_badrecord S 
                        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
						LEFT JOIN $DataIn.gys_shmain M ON S.shMid=M.Id
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
						WHERE S.Id='$Id'",$link_id);
 if($myRow = mysql_fetch_array($myResult)) 
  {
        $Forshort=$myRow["Forshort"];
        $StuffCname=$myRow["StuffCname"];//配件名称
        $Qty=number_format($myRow["Qty"]);
        //不良原因
        $ReasonText="";
        $CheckReason=mysql_query("SELECT B.CauseId,B.Reason,T.Cause  
                     FROM $DataIn.qc_badrecordsheet B 
                     LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  
                     WHERE B.Mid='$Id' order by CauseId",$link_id);       
    	while($ReasonRow = mysql_fetch_array($CheckReason)){
		         $Reason=$ReasonRow["CauseId"]==-1?$ReasonRow["Reason"]:$ReasonRow["Cause"];
		         $ReasonText.=$ReasonText==""?$Reason:"/" . $Reason;
		}
		
		$today=date("y-m-d");
		$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
        $curWeek=substr($dateResult["CurWeek"],4,2);
 
		$jsonArray=array("Company"=>"$Forshort",
									"StuffCname"=>"$StuffCname",
									"Qty"=>"$Qty",
									"Reason"=>"$ReasonText",
									"Date"=>"$today",
									"Weeks"=>"$curWeek");  
}
?>
