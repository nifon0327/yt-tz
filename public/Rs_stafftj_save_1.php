<?php 
include "../model/modelhead.php";
//步骤2：
$Log_Item="员工体检费";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");
$Month=date("Y-m");
if($_POST['ListId']){//如果指定了操作对象
	$Counts=count($_POST['ListId']);
	//$Ids="";
       $FilePath="../download/tjfile/";
	    if(!file_exists($FilePath)){
			  makedir($FilePath);
			}
        if($Attached!=""){
	          $OldFile=$Attached;
	          $FileType=substr("$Attached_name", -4, 4);
	          $PreFileName=date("YmdHis").$FileType;
	          $uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
             }
         $Attached=$uploadInfo==""?"":$uploadInfo;

	for($i=0;$i<$Counts;$i++){
		 $thisId=$_POST[ListId][$i];
		 
		 $CheckNumberRow = mysql_fetch_array(mysql_query("SELECT cSign,BranchId,JobId FROM $DataIn.staffmain WHERE Number = '$thisId'",$link_id));
		 $BranchId = $CheckNumberRow["BranchId"];
		 $JobId = $CheckNumberRow["JobId"];
		 $cSign= $CheckNumberRow["cSign"];
         $CheckRow=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS CheckQty FROM $DataIn.cw17_tjsheet WHERE Number='$thisId' AND tjType='$tjType'",$link_id));
         $CheckQty=$CheckRow["CheckQty"];
         $CheckTime=$CheckQty+1;
         $inRecode ="INSERT INTO $DataIn.cw17_tjsheet(Id,cSign,Mid,tjType,CheckT,Number,BranchId,JobId,Month,Amount,
         Remark,Attached,HG,tjDate,Date,Estate,Locks,Operator)VALUES(NULL,'$cSign',0,'$tjType','$CheckTime','$thisId',
         '$BranchId','$JobId','$Month','$Amount','$Remark','$Attached','$HG','$tjDate','$Date',1,0,'$Operator')";
         $inResult=@mysql_query($inRecode);
         if($inResult){
		           $Log.="&nbsp;&nbsp; 员工号 $thisId 体检费用添加成功.</br>";
		         }
         else{
		          $Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp; 员工号 $thisId 体检费用添加 失败! $inRecode </div></br>";
		          $OperationResult="N";
		       }
		 }
 }

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
