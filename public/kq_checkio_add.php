<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="员工考勤记录";			//需处理
$Log_Funtion="导入";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_add";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
//取现有记录中最后一条记录的时间
$Last_Result = mysql_query("SELECT MAX(CheckTime) AS LastTime FROM $DataIn.checkinout WHERE dFrom=0 order by CheckTime DESC",$link_id);
$LastTime=mysql_result($Last_Result,0,"LastTime");

$connstr = "DRIVER=Microsoft Access Driver (*.mdb);DBQ=".realpath("c:\program files\att2008\att2000.mdb"); 
$connid = odbc_connect($connstr,"","",SQL_CUR_USE_ODBC); 
$result = odbc_do($connid, "SELECT USERINFO.Badgenumber,USERINFO.NAME,CHECKINOUT.CHECKTIME,CHECKINOUT.CHECKTYPE,CHECKINOUT.USERID 
		from CHECKINOUT,USERINFO WHERE CHECKINOUT.USERID=USERINFO.USERID ORDER BY CHECKINOUT.CHECKTIME,CHECKINOUT.USERID");
$i=1;
while(odbc_fetch_row($result)){
	$NUMBER=odbc_result($result,1);
	$Name=Chop(str_replace("?","",odbc_result($result,2)));
	$CHECKTIME=odbc_result($result,3);
	$CHECKTYPE=odbc_result($result,4);	
	switch($CHECKTYPE){
		case "i":
			$CHECKTYPE="I";
			break;
		case "I":
			$CHECKTYPE="I";
			break;
		case "o":
			$CHECKTYPE="O";
			break;
		case "O":
			$CHECKTYPE="O";
			break;
		default:
			$CHECKTYPE="O";
			break;
		}
	$USERID=odbc_result($result,5);	
	if($CHECKTIME>$LastTime){
		//符合条件的记录入库:分七楼和五楼分别导入
		$CheckSignSql=mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE Number='$NUMBER' AND cSign='$Login_cSign' LIMIT 1",$link_id);
		if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			$IN_recode="INSERT INTO $DataIn.checkinout (Id,USERID,Number,CheckTime,CheckType,dFrom, dFromId,Estate,Locks,ZlSign,KrSign,Operator) VALUES (NULL,'$USERID','$NUMBER','$CHECKTIME','$CHECKTYPE','0','0','1','1','0','0','$Operator')";
			$res=@mysql_query($IN_recode);
			if ($res){ 
				if($Log==""){
					$Log="成功导入的考勤记录：<br>"."&nbsp;&nbsp; $i - $USERID - $NUMBER - $CHECKTIME - $CHECKTYPE!</br>";
					}
				else{
					$Log.="&nbsp;&nbsp; $i - $USERID - $NUMBER - $CHECKTIME - $CHECKTYPE!</br>";
					}
				$i++;
				}
			}
		}
	}
odbc_close($connid);
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>