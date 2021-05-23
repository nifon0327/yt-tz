<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw11_jjsheet
$DataIn.cw11_jjmain
未更新：
*/
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="节日奖金记录";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
//echo $ActionId;
switch($ActionId){
          case 89:
           $IdArray=explode("^^",$Id);
           echo $chooseItem;
           for($i=0;$i<count($IdArray);$i++){
                $Id=$IdArray[$i];
                $JfTimeResult=mysql_query("SELECT  COUNT(*) AS jfTime,SUM(JfRate) AS JfRate  FROM $DataIn.cw11_jjsheet WHERE  Number IN (SELECT Number FROM $DataIn.cw11_jjsheet_frist  WHERE Id='$Id')  AND ItemName='$chooseItem'",$link_id );
                $JfTime=mysql_result($JfTimeResult,0,"jfTime");
                $JfedRate=mysql_result($JfTimeResult,0,"JfRate");
                 if($JfTime=="")  $JfTime=1;
                 else   $JfTime=  $JfTime+1;
                 
                 if ($jfSign==2){
	                  $checkRate=mysql_query("SELECT Rate  FROM  $DataIn.cw11_jjsheet_frist    
		                            WHERE Id='$Id' ",$link_id);
		              $Rate=mysql_result($checkRate,0,"Rate");
		              $Rate=$Rate/100;
		              
		               $jfRate=1/$Rate;
		               $jfRate2=$jfRate;
		              if ($JfTime==floor($Rate)){
		                   $JfedRate=$JfedRate==""?0:$JfedRate;
			               $jfRate2=1-$JfedRate; 
		              }
                 }
                 else{
	                 $jfRate2=$jfRate;
                 }
                 
                 if ($qkMonth==""){
                 $inRecode = "INSERT INTO $DataIn.cw11_jjsheet( `Mid`, `ItemName`, `BranchId`, `JobId`, `Number`, `Month`, `MonthS`, `MonthE`, `Divisor`, `JfRate`, `JfTime`, `Rate`, `jjAmount`, `Amount`, `Estate`, `Locks`, `Date`, `Operator`, `PLocks`, `creator`, `created`)
			    SELECT   '0', ItemName, BranchId, JobId, Number, Month, MonthS, MonthE, Divisor,  '$jfRate2', '$JfTime',Rate, floor(Amount*$jfRate), floor(Amount*$jfRate), Estate, Locks, Date, Operator,PLocks,creator,created    FROM  $DataIn.cw11_jjsheet_frist  WHERE Id='$Id'";
			    }
			    else{
				    $inRecode = "INSERT INTO $DataIn.cw11_jjsheet( `Mid`, `ItemName`, `BranchId`, `JobId`, `Number`, `Month`, `MonthS`, `MonthE`, `Divisor`, `JfRate`, `JfTime`, `Rate`, `jjAmount`, `Amount`, `Estate`, `Locks`, `Date`, `Operator`, `PLocks`, `creator`, `created`)
			    SELECT  '0', ItemName, BranchId, JobId, Number, '$qkMonth', MonthS, MonthE, Divisor,  '$jfRate2', '$JfTime',Rate, floor(Amount*$jfRate), floor(Amount*$jfRate), Estate, Locks, Date, Operator,PLocks,creator,created    FROM  $DataIn.cw11_jjsheet_frist  WHERE Id='$Id'";
			    }
			    $inResult=@mysql_query($inRecode);
			   if($inResult){
				    $Log.="&nbsp;&nbsp;员工奖金预先结付成功.</br>";
				    }
			else{
				   $Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;员工奖金预先结付失败! $inRecode </div></br>";
				    $OperationResult="N";
				   }	
               }
          break;
}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth&Estate=$Estate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>