<?php 
/*
 * 鼠宝皮套专用 zhongxq-2012-08-17
 */
//步骤1 $DataIn.info1_business 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="刀模资料";		//需处理
$upDataSheet="$DataIn.pt_cut_data";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d H:i:s");
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 17:
    $fromWebPage=$funFrom."_verify";
	 $Log_Funtion="审核通过";	$SetStr="Estate=1,Locks=0";		include "../model/subprogram/updated_model_3b.php";		
	break;
	default:
    $FilePath="../download/cut_data/";
    if(!file_exists($FilePath)){
	  makedir($FilePath);
     }
    if($Picture!=""){
        $oldPicture=$Picture;
		$FileType=".jpg";
		$Newpicture="C".$Id.$FileType;
	    $upInfo=UploadFiles($oldPicture,$Newpicture,$FilePath);
        $PictureInfo=$upInfo==""?"":" ,Picture='1',Estate='2'";
      }
     $SetStr="CutName='$CutName',CutSize='$CutSize',cutSign='$cutSign',Date='$Date',Operator='$Operator' $PictureInfo";
     
     include "../model/subprogram/updated_model_3a.php";
    break;
}
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>