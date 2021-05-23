<?php 
/*
代码、数据库合并后共享-ZXQ 2012-08-08
加入血型字段 EWEN 2012-10-29
*/
//$ipadTag = $_GET["ipadTag"];
if($ipadTag == "yes")
{
	include "../basic/parameter.inc";
	include "../model/modelfunction.php";
	$Sex = ($Sex=="男")?1:0;
	//民族ID
	$NationResult = mysql_query("SELECT Id FROM $DataPublic.nationdata WHERE Estate=1 And Name='$Nation' ",$link_id);
	$NationRow = mysql_fetch_assoc($NationResult);
	$Nation = $NationRow["Id"];
	//籍贯ID
	$RprResult = mysql_query("SELECT Id FROM $DataPublic.rprdata WHERE Estate=1 And Name = '$Rpr'",$link_id);
	$RprRow = mysql_fetch_assoc($RprResult);
	$Rpr = $RprRow["Id"];
	//教育程度
	$EducationResult = mysql_query("SELECT Id FROM $DataPublic.education WHERE Estate=1 And Name = '$Education' ",$link_id);
	$EducatonRow = mysql_fetch_assoc($EducationResult);
	$Education = $EducatonRow["Id"];
	
	//婚姻状况
	$Married = ($Married == "未婚")?1:0;
	
	//血型
	if($BloodGroup == "未确定")
	{
		$BloodGroup = 0;
	}
	else
	{
		$checkBGSql=mysql_query("SELECT Id FROM $DataPublic.bloodgroup_type WHERE Name='$BloodGroup' And Estate=1 ORDER BY Id",$link_id);
		$bloodRow = mysql_fetch_assoc($checkBGSql);
		$BloodGroup = $bloodRow["Id"];
	}
	//员工类别
	$FormalSign = ($FormalSign == "正式工")?1:2;
	
	$cSign = ($cSign == "研砼")?"7":"3";
	//部门
	$BranchResult = mysql_query("SELECT Id FROM $DataPublic.branchdata WHERE Estate=1 And Name = '$BranchId' ",$link_id);
	$branchRow = mysql_fetch_assoc($BranchResult);
	$BranchId = $branchRow["Id"];
	
	//职位
	$JobResult = mysql_query("SELECT Id FROM $DataPublic.jobdata WHERE Estate=1 And Name = '$JobId' ",$link_id);
	$jobRow = mysql_fetch_assoc($JobResult);
	$JobId = $jobRow["Id"];
	//小组
	$dataTmp = ($cSign == "7")?"d7":"d3";
	$GroupResult = mysql_query("SELECT GroupId FROM $dataTmp.staffgroup WHERE Estate=1 And GroupName = '$GroupId' ",$link_id);
	$groupRow = mysql_fetch_assoc($GroupResult);
	$GroupId = $groupRow["GroupId"];
	//介绍人
	$Introducer = ($Introducer == "")?"":substr($Introducer, strcspn($Introducer,"-")+1,strlen($Introducer)-strcspn($Introducer,"-"));
	
	//工作地点
	$workAddResult = mysql_query("Select Id From $DataPublic.staffworkadd Where Name = '$WorkAdd'");
	$workAddRow = mysql_fetch_assoc($workAddResult);
	$WorkAdd = $workAddRow["Id"];
	
	$floorAddResult = mysql_query("Select Id From $DataPublic.attendance_floor Where Name = '$floorAdd'");
	$floorAddRow = mysql_fetch_assoc($floorAddResult);
	$floorAdd = $floorAddRow["Id"];	
	if($floorAdd == "")
	{
		$floorAdd = "0";
	}
	$LogInfo = "";
	
}
else
{
	include "../model/modelhead.php";
	include "public_appconfig.php";
	$_SESSION["nowWebPage"]=$nowWebPage;
} 
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
//步骤2：
$Log_Item="员工资料";		//需处理
$upDataSheet="$DataPublic.staffmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;

if($ipadTag != "yes")
{
	ChangeWtitle($TitleSTR);
}
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
if(!file_exists($StaffPhotoPath)){
		makedir($StaffPhotoPath);
}
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:$Log_Funtion="锁定";	$SetStr="Locks=0";				
	include "../admin/subprogram/updated_model_3d.php";		break;
	case 8:$Log_Funtion="解锁";	$SetStr="Locks=1";				
	include "../admin/subprogram/updated_model_3d.php";		break;
	case 32:$Log_Funtion="离职";	$SetStr="Estate=0,Locks=0";	
	//include "subprogram/updated_model_3b.php";
	if ($Id!=""){
		if($KillOnline==1){
			include "../model/subprogram/killonline_model.php";
			}
		    include "../model/subprogram/updated_model_3a.php";		
		   $x++;
		}		
	
      break;
	  case 49:
	             $Log_Funtion="调动"; 
	            // $DelSql="DELETE FROM $DataPublic.staff_move WHERE Number IN (SELECT Number FROM $upDataSheet WHERE Id='$Id')";
		         //$DelResult=mysql_query($DelSql);
		         $InSql="INSERT INTO $DataPublic.staff_move SELECT NULL,cSign,Number,BranchId,JobId,GroupId,'1','0','$Date','$Operator', '0', '$Operator', NOW(), '$Operator', NOW() FROM $upDataSheet WHERE Id='$Id'";
                // echo $InSql;
		         $InResult=mysql_query($InSql);
		         if($InResult){
	                 $mainSql = "UPDATE $upDataSheet SET cSign='$cSign',GroupId='$GroupId',BranchId='$BranchId',JobId='$JobId',WorkAdd='$WorkAdd' WHERE Id='$Id'";
                     // echo $mainSql;
	                $mainResult = mysql_query($mainSql);
	                if($mainResult ){
                         $Log.="ID号为 $Id 的员工调动成功.<br>";
	                    }
                     else{
                         $Log.="<div class='redB'>ID号为 $Id 的员工调动失败. $mainSql</div><br>";
                         $OperationResult="N";
                        }
	             }
					   
				/*
				$cSignResult=mysql_fetch_array(mysql_query("SELECT cSign,Number from $upDataSheet WHERE Id='$Id'",$link_id));
				$cSign=$cSignResult["cSign"];
				$Number=$cSignResult["Number"];				
                $remotecSign=$cSign==7?3:7;  //从皮套或包装互调
				$cSignResult = mysql_query("SELECT InIP FROM $DataPublic.companys_group WHERE cSign=$remotecSign Limit 1",$link_id);
				if($cSignRow = mysql_fetch_array($cSignResult)){
					$InIP=$cSignRow["InIP"];
					include_once "../model/R_Function.php"; 
					$InIP="http://$InIP/download/staffPhoto/";
					
					// "p".$Number.".jpg'   c".$Number.".jpg'   H".$Number.".jpg' ,"D".$Number.".jpg"; 
					$save_dir="../download/staffPhoto/";
					$filename='P'.$Number.'.jpg' ;
					$url="$InIP".$filename;
					$return=get_url_Image($url,$save_dir,$filename,$type=0);
					if (($return['error']>0)   ){
						 $Log.="<div class='redB'>Number号为 $Number 的员工相片调动失败. $url </div><br>";
					}
					else {
						 $Log.="Number号为 $Number 的员工相片调动成功<br>";
						
					}
					
					
					$filename='C'.$Number.'.jpg' ;
					$url="$InIP".$filename;
					$return=get_url_Image($url,$save_dir,$filename,$type=0);
					if (($return['error']>0)  ){
						 $Log.="<div class='redB'>Number号为 $Number 的员工身份证调动失败. $url </div><br>";
					}
					else {
						 $Log.="Number号为 $Number 的员工身份证调动成功<br>";
						
					}
					
					$filename='H'.$Number.'.jpg' ;
					$url="$InIP".$filename;
					$return=get_url_Image($url,$save_dir,$filename,$type=0);
					if (($return['error']>0)   ){
						 $Log.="<div class='redB'>Number号为 $Number 的员工健康证调动失败. $url </div><br>";
					}
					else {
						 $Log.="Number号为 $Number 的员工健康证调动成功<br>";
						
					}
					
					$filename='D'.$Number.'.jpg' ;
					$url="$InIP".$filename;
					$return=get_url_Image($url,$save_dir,$filename,$type=0);
					if (($return['error']>0)    ){
						 $Log.="<div class='redB'>Number号为 $Number 的员工驾驶证调动失败.</div><br>";
					}
					else {
						 $Log.="Number号为 $Number 的员工驾驶证调动成功<br>";
						
					}						 
				}	
				*/		
					   
	            break;
	case 69: 
	         $Log_Funtion="转正";
	         $SetStr="FormalSign=1";	
	         include "../admin/subprogram/updated_model_3d.php";
	         if($OperationResult=="Y"){
	                $cSignResult=mysql_fetch_array(mysql_query("SELECT cSign,Number from $upDataSheet WHERE Id='$Id'",$link_id));
	                $cSign=$cSignResult["cSign"];
	                $Number=$cSignResult["Number"];
	                $InResult="INSERT INTO $DataPublic.staff_formaldate (Id, Number, FormalDate, Estate, Locks, Operator)values(NULL,'$Number','$Date','1','0','$Operator' )";
	                $InRecode=mysql_query($InResult);
	                if($InRecode){
	                                    $Log.="$Date $Number 转入正式员工成功.<br>";
		                            }
	                     else{
	                                   $Log.="<div class='redB'>$Date $Number 转入正式员工失败. $InResult</div><br>";
		                               $OperationResult="N";
	                              }
	             }
	         break;
      case 127:
           $Log_Funtion="体检报告上传";
		    //之前最后一个记录
		   $FilePath="../download/tjfile/";
		   if(!file_exists($FilePath)){
			      makedir($FilePath);
			  }
		$EndNumber=1;
		$checkEndFile=mysql_fetch_array(mysql_query("SELECT MAX(Attached) AS EndAttached FROM $DataPublic.staff_tj WHERE Number='$Number'",$link_id));
		$EndFile=$checkEndFile["EndAttached"];
		if($EndFile!=""){
			$TempArray1=explode("_",$EndFile);
			$TempArray2=explode(".",$TempArray1[1]);
			$EndNumber=$TempArray2[0]+1;
			}
        $uploadNums=count($Picture);
		for($i=0;$i<$uploadNums;$i++){
			 //上传文档				
			 $upPicture=$Picture[$i];
             $OldFile=$upPicture;
			 $PreFileName=$Number."_".$EndNumber.".pdf";
	         $uploadInfo=$PreFileName;
				$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
				if($uploadInfo!=""){
						$inRecode="INSERT INTO $DataPublic.staff_tj (Id,Number,Attached,Date,Estate,Locks,Operator) VALUES (NULL,'$Number','$PreFileName','$Date','1','0','$Operator')";
						$inAction=@mysql_query($inRecode);
						if($inAction){
							    $Log.="员工ID号为: $Number 的体检报告 $uploadInfo 上传成功.<br>";
							    $EndNumber++;
                               }
						else{
							    $Log.="<div class='redB'>员工ID号为: $Number 的体检报告 $uploadInfo 上传失败. </div><br>";
							    $OperationResult="N";
							 }
					 }
             }
            break;
      case 40:
		$Log_Funtion="个人证书上传";
		//之前最后一个记录
		$FilePath="../download/Certificate/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$Date=date("Y-m-d");
		$EndNumber=1;
		$checkEndFile=mysql_fetch_array(mysql_query("SELECT MAX(Picture) AS EndPicture FROM $DataPublic.staff_Certificate WHERE Number='$Number'",$link_id));
		$EndFile=$checkEndFile["EndPicture"];
		if($EndFile!=""){
			$TempArray1=explode("_",$EndFile);
			$TempArray2=explode(".",$TempArray1[1]);
			$EndNumber=$TempArray2[0]+1;
			}
		$uploadNums=count($Picture);
		for($i=0;$i<$uploadNums;$i++){
			//上传文档				
			$upPicture=$Picture[$i];
			$TempOldImg=$OldImg[$i];
			if ($upPicture!=""){	
				$OldFile=$upPicture;
				//检查是否有原档，如果有则使用原档名称，如果没有，则分配新档名
				if($TempOldImg!=""){
					$PreFileName=$TempOldImg;
					}
				else{
					$PreFileName=$Number."_".$EndNumber.".jpg";
					}
				$uploadInfo=$PreFileName;
				$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
				if($uploadInfo!=""){
					if($TempOldImg==""){//写入记录
						$inRecode="INSERT INTO $DataPublic.staff_Certificate(Id,Number,Picture,Date,`Estate`, `Locks`,Operator) VALUES (NULL,'$Number','$uploadInfo','$Date','1','0','$Operator')";
						$inAction=@mysql_query($inRecode);
						if($inAction){
							$Log.="ID号为 $Number 的员工证书 $uploadInfo 上传成功.<br>";
							$EndNumber++;
                             }
						else{
							$Log.="<div class='redB'>ID号为 $Number 的员工证书 $uploadInfo 上传 添加失败. $inRecode</div><br>";
							$OperationResult="N";
							}
						}
					}
				}
			}
       break;
	   
    case -1:
		$Log_Funtion="更新个人驾照上传";
		$FilePath=$StaffPhotoPath;  //../download/staffPhoto/
		//照片处理
		$PreFileName1="D".$Number.".jpg";
		if($DriverPhoto!=""){
			/*
$OldFile1=$Photo;			
			$uploadInfo1=UploadPictures($OldFile1,$PreFileName1,$FilePath);
*/
		if($ipadTag == "yes")
			{
				if(is_uploaded_file($_FILES['DriverPhoto']['tmp_name']))
				{
					$PreFileName1="D".$Number.".jpg";
					$path = $_SERVER['DOCUMENT_ROOT']."/download/staffPhoto/".$PreFileName1;
					if(move_uploaded_file($_FILES['DriverPhoto']['tmp_name'],$path))
					{
						$uploadInfo1 = 1;
					}
					else
					{
						$uploadInfo1 = "";
					}
				}
			}
			else
			{
				$OldFile1=$DriverPhoto;
				$PreFileName1="D".$Number.".jpg";
				$uploadInfo1=UploadPictures($OldFile1,$PreFileName1,$StaffPhotoPath);
			}
		}

		$upValue1=$uploadInfo1==""?"":",DriverPhoto='1'";		
		if($upValue1=="" && $oldDriverPhoto==1){//没有上传文件并且已选取删除原文件
			$delFilePath=$FilePath.$PreFileName1;
			if(file_exists($delFilePath)){
				unlink($delFilePath);
				$upValue1=",DriverPhoto='0'";
				}			
			}

			$sheetSql ="UPDATE $DataPublic.staffsheet SET Number=$Number
		      $upValue1  WHERE Number=$Number";
			$sheetResult = mysql_query($sheetSql);
			if($sheetResult){
				$Log.="&nbsp;&nbsp; $Number 的个人驾照上传信息更新成功!$sheetSql</br>";
				}
			else{
				$Log.="&nbsp;&nbsp; $Number 的个人驾照上传信息更新失败! $sheetSql </br>";
				$OperationResult="N";
				}
			
			
		break;	

		case  98: //护照上传
		$Log_Funtion="更新个人护照上传";
		$FilePath=$StaffPhotoPath;  //../download/staffPhoto/
		$PreFileName1="PP".$Number.".jpg";
		if($PassPort!=""){
				$OldFile1=$PassPort;
				$uploadInfo1=UploadPictures($OldFile1,$PreFileName1,$StaffPhotoPath);
			}

		$upValue1=$uploadInfo1==""?"":"PassPort='1'";		
		if($upValue1=="" && $oldPassPort==1){//没有上传文件并且已选取删除原文件
			$delFilePath=$FilePath.$PreFileName1;
			if(file_exists($delFilePath)){
				unlink($delFilePath);
				$upValue1="PassPort='0'";
				}			
			}

			$sheetSql ="UPDATE $DataPublic.staffsheet SET  $upValue1  WHERE Number=$Number";
			$sheetResult = mysql_query($sheetSql);
			if($sheetResult){
				$Log.="&nbsp;&nbsp; $Number 的个人护照上传信息更新成功!$sheetSql</br>";
				}
			else{
				$Log.="&nbsp;&nbsp; $Number 的个人护照上传信息更新失败! $sheetSql </br>";
				$OperationResult="N";
				}
           break;

     case  99: //通行证上传
		$Log_Funtion="更新通行证上传";
		$FilePath=$StaffPhotoPath;  //../download/staffPhoto/
		if($PassTicket!=""){
				$OldFile1=$PassTicket;
				$PreFileName1="PT".$Number.".jpg";
				$uploadInfo1=UploadPictures($OldFile1,$PreFileName1,$StaffPhotoPath);
			}

		$upValue1=$uploadInfo1==""?"":"PassTicket='1'";		
		if($upValue1=="" && $oldPassTicket==1){//没有上传文件并且已选取删除原文件
			$delFilePath=$FilePath.$PreFileName1;
			if(file_exists($delFilePath)){
				unlink($delFilePath);
				$upValue1="PassTicket='0'";
				}			
			}

			$sheetSql ="UPDATE $DataPublic.staffsheet SET  $upValue1  WHERE Number=$Number";
			$sheetResult = mysql_query($sheetSql);
			if($sheetResult){
				$Log.="&nbsp;&nbsp; $Number 的个人护照上传信息更新成功!$sheetSql</br>";
				}
			else{
				$Log.="&nbsp;&nbsp; $Number 的个人护照上传信息更新失败! $sheetSql </br>";
				$OperationResult="N";
				}
           break;

     case  "ClothesSize":
				  $sql = "UPDATE $DataPublic.staffsheet SET ClothesSize='$tempClothesSize' WHERE Number='$Number'";
			      $result = mysql_query($sql,$link_id);
				  if ($result){
				     	$Log="员工ID号为 $Number 的工衣尺寸更新成功.<br>";
					}
				else{
					$Log="<div class=redB>员工ID号为 $Number 的工衣尺寸更新失败.</div><br>";
					$OperationResult="N";
					}
           break;

      case  "HouseSize":
				  $sql = "UPDATE $DataPublic.staffsheet SET HouseSize='$tempHouseSize' WHERE Number='$Number'";
			      $result = mysql_query($sql,$link_id);
				  if ($result){
				     	$Log="员工ID号为 $Number 的购房地址更新成功.<br>";
					}
				else{
					$Log="<div class=redB>员工ID号为 $Number 的购房地址更新失败.</div><br>";
					$OperationResult="N";
					}
           break;
           
     case "87":
           include "staff_upfile_load.php";
      break;

	default:
	    if ($Name=="") break;
		$Log_Funtion="更新";
		$FilePath=$StaffPhotoPath;  //../download/staffPhoto/
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		//照片处理
		$PreFileName1="P".$Number.".jpg";
		if($Photo!=""){
		  if($ipadTag == "yes")
			{
				if(is_uploaded_file($_FILES['Photo']['tmp_name']))
				{
					$PreFileName1="P".$Number.".jpg";
					$path = $_SERVER['DOCUMENT_ROOT']."/download/staffPhoto/".$PreFileName1;
					if(move_uploaded_file($_FILES['Photo']['tmp_name'],$path))
					{
						$uploadInfo1 = 1;
					}
					else
					{
						$uploadInfo1 = 0;
					}
				}
			}
			else
			{
				$OldFile1=$Photo;
				$PreFileName1="P".$Number.".jpg";
				$uploadInfo1=UploadPictures($OldFile1,$PreFileName1,$StaffPhotoPath);
			}
		}
		
		if($PngPhoto!=""){
		      $PreFileName2="P".$Number.".png";
		      $uploadInfo2=UploadPictures($PngPhoto,$PreFileName2,$StaffPhotoPath);
		}

		$upValue1=$uploadInfo1==""?"":",Photo='1'";		
		if($upValue1=="" && $oldPhoto==1){//没有上传文件并且已选取删除原文件
			$delFilePath=$FilePath.$PreFileName1;
			if(file_exists($delFilePath)){
				unlink($delFilePath);
				$upValue1=",Photo='0'";
				}			
			}
		
		//身份证扫描档处理
		$PreFileName2="C".$Number.".jpg";
		if($IdcardPhoto!="")
		{
			if($ipadTag == "yes")
			{
				if(is_uploaded_file($_FILES['IdcardPhoto']['tmp_name']))
				{
					$PreFileName2="C".$Number.".jpg";
					$path = $_SERVER['DOCUMENT_ROOT']."/download/staffPhoto/".$PreFileName2;
					if(move_uploaded_file($_FILES['IdcardPhoto']['tmp_name'],$path))
					{
						$uploadInfo2 = 1;
					}
					else
					{
						$uploadInfo2 = 0;
					}
				}
			}
		else
		{
			$OldFile2=$IdcardPhoto;
			$PreFileName2="C".$Number.".jpg";
			$uploadInfo2=UploadPictures($OldFile2,$PreFileName2,$StaffPhotoPath);
		}


		}
		$upValue2=$uploadInfo2==""?"":",IdcardPhoto='1'";
		if($upValue2=="" && $oldIphoto==1){//没有上传文件并且已选取删除原文件
			$delFilePath=$FilePath.$PreFileName2;
			if(file_exists($delFilePath)){
				unlink($delFilePath);
				$upValue2=",IdcardPhoto='0'";
				}			
			}
			
		//入职文档处理
		$PreFileName3="I".$Number.".pdf";
		if($InFile!="")
		{
		
			if($ipadTag == "yes")
			{
				if(is_uploaded_file($_FILES['HealthPhoto']['tmp_name']))
				{
					$PreFileName3="H".$Number.".jpg";
					$path = $_SERVER['DOCUMENT_ROOT']."/download/staffPhoto/".$PreFileName3;
					if(move_uploaded_file($_FILES['HealthPhoto']['tmp_name'],$path))
					{
						$uploadInfo3 = 1;
					}
					else
					{
						$uploadInfo3 = 0;
					}
				}
			}
			else
			{
				$OldFile3=$InFile;
		        $PreFileName3="I".$Number.".pdf";
				$uploadInfo3=UploadPictures($OldFile3,$PreFileName3,$StaffPhotoPath);
			}
			

		}
		$upValue3=$uploadInfo3==""?"":",`InFile`='1'";
		if($upValue3=="" && $oldInFile==1){//没有上传文件并且已选取删除原文件
			$delFilePath=$FilePath.$PreFileName3;
			if(file_exists($delFilePath)){
				unlink($delFilePath);
				$upValue3=",InFile='0'";
				}			
			}
		//健康../model/subselect/cSign.php证扫描上传
		$PreFileName4="H".$Number.".jpg";
		if($HealthPhoto!=""){
			$OldFile4=$HealthPhoto;			
			$uploadInfo4=UploadPictures($OldFile4,$PreFileName4,$FilePath);
			}
		$upValue4=$uploadInfo4==""?"":",HealthPhoto='1'";
		if($upValue4=="" && $oldHPhoto==1){//没有上传文件并且已选取删除原文件
			$delFilePath=$FilePath.$PreFileName4;
			if(file_exists($delFilePath)){
				unlink($delFilePath);
				$upValue4=",HealthPhoto='0'";
				}			
			}


		//职业体检../model/subselect/cSign.php证扫描上传
		$PreFileName5="V".$Number.".jpg";
		if($vocationHPhoto!=""){
			$OldFile5=$vocationHPhoto;			
			$uploadInfo5=UploadPictures($OldFile5,$PreFileName5,$FilePath);
			}
		$upValue5=$uploadInfo5==""?"":",vocationHPhoto='1'";
		if($upValue5=="" && $oldVPhoto==1){//没有上传文件并且已选取删除原文件
			$delFilePath=$FilePath.$PreFileName5;
			if(file_exists($delFilePath)){
				unlink($delFilePath);
				$upValue5=",vocationHPhoto='0'";
				}			
			}


		$Name=FormatSTR($Name);//去连续空格,去首尾空格			
		$Idcard=FormatSTR($Idcard);
		$IdNum=FormatSTR($IdNum);
		$Address=FormatSTR($Address);
		$Postalcode=FormatSTR($Postalcode);
		$Tel=FormatSTR($Tel);
		$Mobile=FormatSTR($Mobile);
		$Mail=FormatSTR($Mail);
		$Dh=FormatSTR($Dh);
		$Note=FormatSTR($Note);
		$ContractSDate=$ContractSDate==""?"0000-00-00":$ContractSDate;
		$ContractEDate=$ContractEDate==""?"0000-00-00":$ContractEDate;
		
		$offSignValue=$OffStaffSign==1?",OffStaffSign='1'":",OffStaffSign='0'";
		//echo $offSignValue;
		
		$mainSql = "UPDATE $upDataSheet SET WorkAdd='$WorkAdd',Name='$Name',IdNum='$IdNum',Nickname='$Nickname',GroupId='$GroupId',Mail='$Mail',GroupEmail='$GroupEmail',AppleID='$AppleID',ExtNo='$ExtNo',ComeIn='$ComeIn',ContractSDate='$ContractSDate',
ContractEDate='$ContractEDate',Introducer='$Introducer',AttendanceFloor='$floorAdd',FormalSign='$FormalSign',Date='$Date',Locks=0,Operator='$Operator' $offSignValue WHERE Id='$Id'";
		$mainResult = mysql_query($mainSql);
		if ($mainResult){
			$Log.="&nbsp;&nbsp; $Name 的入职资料更新成功!</br>";
			// 更新从表信息
			$sheetSql ="UPDATE $DataPublic.staffsheet SET 
		Sex='$Sex',BloodGroup='$BloodGroup',Nation='$Nation',Rpr='$Rpr',Education='$Education',Married='$Married',Birthday='$Birthday',
		Idcard='$Idcard',Address='$Address',Postalcode='$Postalcode',Tel='$Tel',Mobile='$Mobile',Dh='$Dh',Weixin='$Weixin',LinkedIn='$LinkedIn',
		Note='$Note',ClothesSize='$ClothesSize' $upValue1 $upValue2 $upValue3 $upValue4 $upValue5 WHERE Number=$Number";
			$sheetResult = mysql_query($sheetSql);
			if($sheetResult){
				$Log.="&nbsp;&nbsp; $Name 的从表信息更新成功!$sheetSql</br>";
				}
			else{
				$Log.="&nbsp;&nbsp; $Name 的从表信息更新失败! $sheetSql </br>";
				$OperationResult="N";
				}
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp; $Name 的入职资料更新失败! $mainSql </div></br>";
			$OperationResult="N";
			}
        if($Jobduties!=""){//工作职责
                         $CheckNumberResult =mysql_fetch_array(mysql_query("SELECT Id FROM $DataPublic.staff_jobduties WHERE Number=$Number",$link_id));
                         $CheckId=$CheckNumberResult["Id"];
                         if($CheckId>0){
                                   $UpdateSql ="UPDATE  $DataPublic.staff_jobduties SET Description='$Jobduties',Date='$DateTime' WHERE Number=$Number";
                                   $UpdateResult =@mysql_query($UpdateSql);
                                   if($UpdateResult && mysql_affected_rows()>0){
		                                       	$Log.="&nbsp;&nbsp; $Name 的工作职责更新成功! </br>";
                                         }
                             }
                        else{
                                      $InsertSql ="INSERT INTO   $DataPublic.staff_jobduties (Id,Number,Description,Date,Estate,Locks,Operator) VALUES(NULL,'$Number','$Jobduties','$DateTime','1','0','$Operator')";
                                       $InsertResult =@mysql_query($InsertSql);
                                      if($InsertResult && mysql_affected_rows()>0){
		                                       	$Log.="&nbsp;&nbsp; $Name 的工作职责新增成功! </br>";
                                         }
                              }
               }
		break;
	}
	
	if($ipadTag == "yes"){
		     echo json_encode(array($OperationResult,$Log));
	    }

$ALType="FormalSign=$FormalSign&BranchId=$BranchId";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
if($ipadTag != "yes")
{
include "../model/logpage.php";
}
?>