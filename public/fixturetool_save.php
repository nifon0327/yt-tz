<?php   
//电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
$Log_Item="加工工序资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="新增记录";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");

	$maxSql = mysql_query("SELECT MAX(ToolsId) AS Mid FROM $DataIn.fixturetool",$link_id);
	$ToolsId=mysql_result($maxSql,0,"Mid");
	if($ToolsId){
		$ToolsId=$ToolsId+1;
		}
	else{
		$ToolsId=10001;
	    }
     $Remark=FormatSTR($Remark);
     if($Picture!=""){
		  $FilePath="../download/ztools/";
		  if(!file_exists($FilePath)){
			      makedir($FilePath);
		    }
		    $oldPicture=$Picture;
			$FileType=".jpg";
			$Newpicture=$ToolsId.$FileType;
			$upInfo=UploadFiles($oldPicture,$Newpicture,$FilePath);
        }
        $typeId=$typeId==""?0:$typeId;
        $PictureInfo=$upInfo==""?0:1;
        $GoodsId=0;
        $ToolsCode ="80000000".$ToolsId;
        $inRecode="INSERT INTO $DataIn.fixturetool (Id, ToolsId, GoodsId, ToolsName, ToolsCode, UseTimes, Type, Remark, Picture, Estate, Locks, Date, Operator, PLocks, creator, created, modifier, modified)values(NULL,'$ToolsId','$GoodsId','$ToolsName','$ToolsCode','$UseTimes',
        '$typeId','$Remark','$PictureInfo','1','0','$Date','$Operator','0','$Operator','$DateTime','$Operator','$DateTime')";
        $inAction=@mysql_query($inRecode);
        if($inAction){ 
	        $Log="$TitleSTR 成功!<br>";
	        } 
        else{
	        $Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	        $OperationResult="N";
	       } 

//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>