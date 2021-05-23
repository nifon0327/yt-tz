<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";

$Log_Item="节日奖金记录";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");

//指定部门
$BranchIdSTR=$BranchId==""?"":"and M.BranchId='$BranchId'";
//指定职位
$BranchIdSTR=$JobId==""?"":$BranchIdSTR." AND M.JobId='$JobId'";

//如果指定员工，则以上条件取消
if($_POST['ListId']){//如果指定了操作对象
	$Counts=count($_POST['ListId']);
	$Ids="";
	for($i=0;$i<$Counts;$i++){
		$thisId=$_POST[ListId][$i];
		$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
		}
	$BranchIdSTR="AND M.Number IN ($Ids)";	//指定员工
	}	
$ItemName=$theYear.$ItemName;


$mySql= "Select M.Number,M.BranchId,M.JobId,DATE_FORMAT(ComeIn,'%Y-%m') as ComeIn,S.Idcard  
		FROM  $DataPublic.staffmain M  
		LEFT JOIN $DataPublic.staffsheet S ON M.Number = S.Number
		Where 1  AND M.Estate='1' $BranchIdSTR 
		AND  M.Number NOT IN(SELECT Number FROM $DataIn.cw11_jjsheet WHERE ItemName='$ItemName'  )
        AND M.cSign='$Login_cSign'";

//echo "$mySql";
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{			
		$MonthArray=array();  
		$Number=$myRow["Number"];
		$BranchId=$myRow["BranchId"];
		$JobId=$myRow["JobId"];
		$ComeIn=$myRow["ComeIn"];
		$Idcard=$myRow["Idcard"];

	    $LocalSql= "SELECT Month,sum(Amount) as Amount  from(
					SELECT M.Month, M.Amount+M.Sb+M.Jz-M.taxbz AS Amount 
					FROM $DataIn.cwxzsheet M
					LEFT JOIN $DataPublic.staffmain P ON M.Number=P.Number
					WHERE M.Month >= '$MonthS' AND M.Month>= DATE_FORMAT(P.ComeIn,'%Y-%m') AND M.Month <= '$MonthE'  AND M.Number='$Number'  
					UNION ALL 
					SELECT M.Month as Month,M.Amount AS Amount 
					FROM $DataIn.hdjbsheet M 
					LEFT JOIN $DataPublic.staffmain P ON M.Number=P.Number
					WHERE M.Month >= '$MonthS' AND M.Month>= DATE_FORMAT(P.ComeIn,'%Y-%m') AND M.Month <= '$MonthE'  AND M.Number='$Number'
					)K GROUP BY Month";
       echo $LocalSql;
		$strArray="'0'=>0 ,";
		$LocalResult = mysql_query($LocalSql,$link_id);
		if($LocalRow = mysql_fetch_array($LocalResult)){
			do{	
				
				$CurMonth=$LocalRow["Month"];
				$CurAmount=$LocalRow["Amount"];
				if($CurMonth!="" && $CurAmount!=""){
					  $strArray.="'$CurMonth'=>$CurAmount ,";
				  }
			   }while ($LocalRow = mysql_fetch_array($LocalResult));		
		}
	
		$strArray="\$InArray=array($strArray);";
		eval($strArray);			
		$SumAmount=0;
		foreach($InArray as $key=>$value)
		{
			$SumAmount=$SumAmount+$value;			
		}	
		$jjAmount=floor(($SumAmount/($Divisor*100))*$Rate);
	    
		//写入数据库
		if($jjAmount>0)
		{
			/*$inRecode = "INSERT INTO $DataIn.cw11_jjsheet
			SELECT NULL,'0','$ItemName','$BranchId','$JobId','$Number','$Month','$MonthS','$MonthE','$Divisor','$Rate','$jjAmount','1','1','$Date','$Operator'";
			$inResult=@mysql_query($inRecode);
			if($inResult){
				$Log.="&nbsp;&nbsp;员工号:$Number 奖金：$jjAmount $ItemName  成功.</br>";
				}
			else{
				$Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;员工 $TitleSTR 失败! $inRecode </div></br>";
				$OperationResult="N";
				}	*/	
		}		
	}while ($myRow = mysql_fetch_array($myResult));
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
