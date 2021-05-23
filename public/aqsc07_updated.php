<?php 
//电信-joseph
//步骤1
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="安全生产培训计划";		//需处理
$upDataSheet="$DataPublic.aqsc07";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 138://登记受训员工
		$Log_Item="受训记录登记";
		//员工过滤
		$NumberSTR="";
		$InFo="全部";
		if($_POST["ListId"]){//如果指定
			$Counts=count($_POST["ListId"]);
			$Ids="";
			for($i=0;$i<$Counts;$i++){
				$thisId=$_POST["ListId"][$i];
				$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
				}
			$NumberSTR="AND Number IN ($Ids)";
			$InFo="员工ID在($Ids)的";
			}
		else{
			if($JobId!=""){
				$NumberSTR="AND JobId='$JobId'";
				$InFo="职位ID为 $JobId的";
				}
			else{
				if($BranchId!=""){
					$NumberSTR="AND BranchId='$BranchId'";
					$InFo="部门ID为 $BranchId的";
					}
				}		
			}
		//写入数据
		if($DataIn !== 'ac'){
			$inRecode="INSERT INTO $DataPublic.aqsc08
			SELECT NULL,'$Id',Number,'$Exam','$DateTime','1','0','$Operator' 
			FROM $DataPublic.staffmain WHERE cSign='$Login_cSign' $NumberSTR AND Estate='1' 
			AND Number NOT IN(SELECT Number FROM $DataPublic.aqsc08 WHERE ItemId='$Id' ORDER BY Number)";
		}else{
			$inRecode="INSERT INTO $DataPublic.aqsc08
			SELECT NULL,'$Id',Number,'$Exam','$DateTime','1','0','$Operator', 0, '$Operator', '$DateTime', '$Operator', '$DateTime' 
			FROM $DataPublic.staffmain WHERE cSign='$Login_cSign' $NumberSTR AND Estate='1' 
			AND Number NOT IN(SELECT Number FROM $DataPublic.aqsc08 WHERE ItemId='$Id' ORDER BY Number)";
		}
		$inResult=@mysql_query($inRecode);
		if($inResult){
			$Log.="&nbsp;&nbsp;$x-".$InFo."员工".$Log_Item."成功!</br>";
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;$x-".$InFo."员工".$Log_Item."的失败! $inRecode</div></br>";
			$OperationResult="N";
			}
	break;
	default:
		$ItemName=FormatSTR($ItemName);
		$Estate=$Estate==""?0:$Estate;
		if($Estate==1){
			//图片附件更新或新增
			if($Img!=""){//有上传文件
				$FileType=substr("$Img_name", -4, 4);
				$OldFile=$Img;
				$FilePath="../download/aqsc/";
				if(!file_exists($FilePath)){
					makedir($FilePath);
					}
				if($oldImg!=""){
					$PreFileName=$oldImg;
					}
				else{
					$datelist=newGetDateSTR();
					$PreFileName="aqsc07_img_".$Id.$FileType;
					}
				$upImg=UploadPictures($OldFile,$PreFileName,$FilePath);
				if($upImg!=""){
					$ImgSTR=",Img='1'";
					}
				}
			//视频附件更新或新增
			if($Movie!=""){//有上传文件
				$FileType=substr("$Movie_name", -4, 4);
				$OldFile=$Movie;
				$FilePath="../download/aqsc/";
				if(!file_exists($FilePath)){
					makedir($FilePath);
					}
				if($oldMovie!=""){
					$PreFileName=$oldMovie;
					}
				else{
					$datelist=newGetDateSTR();
					$PreFileName="aqsc07_movie_".$Id.$FileType;
					}
				$upMovie=UploadPictures($OldFile,$PreFileName,$FilePath);
				if($upMovie!=""){
					$MovieSTR=",Movie='1'";
					}
				}
			//签名附件更新或新增
			if($List!=""){//有上传文件
				$FileType=substr("$List_name", -4, 4);
				$OldFile=$List;
				$FilePath="../download/aqsc/";
				if(!file_exists($FilePath)){
					makedir($FilePath);
					}
				if($oldList!=""){
					$PreFileName=$oldList;
					}
				else{
					$datelist=newGetDateSTR();
					$PreFileName="aqsc07_list_".$Id.$FileType;
					}
				$upList=UploadPictures($OldFile,$PreFileName,$FilePath);
				if($upList!=""){
					$ListSTR=",List='1'";
					}
				}
			}
		else{
			//$Estate=0;
			$MovieSTR=$ListSTR=$Lecturer=$Reviewer="";
			$ImgSTR=",Img='0',Movie='0',List='0'";
			//清除文件
			$imgPath="../download/aqsc/aqsc07_img_".$Id.".pdf";
			if(file_exists($imgPath)){
				unlink($imgPath);
				}
			$MoviePath="../download/aqsc/aqsc07_movie_".$Id.".pdf";
			if(file_exists($MoviePath)){
				unlink($MoviePath);
				}
			$ListPath="../download/aqsc/aqsc07_list_".$Id.".pdf";
			if(file_exists($ListPath)){
				unlink($ListPath);
				}
			}
			
		$SetStr="DefaultDate='$DefaultDate',ItemName='$ItemName',ItemTime='$ItemTime',Tutorial='$Tutorial',Lecturer='$Lecturer',Reviewer='$Reviewer',TeachId='$TeachId',ExamId='$ExamId',OUId='$OUId',ObjectId='$ObjectId',TypeId='$TypeId',Date='$DateTime',Estate='$Estate',Locks='0',Operator='$Operator' $ImgSTR $MovieSTR $ListSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
//$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
//$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>