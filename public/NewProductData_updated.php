<?php 
//电信-ZX  2012-08-01
//步骤1  $DataIn.productdata 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="新产品资料";		//需处理
$upDataSheet="$DataIn.newproductdata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;$FilePath="../download/newproductdata/";

//echo "ActionId:$ActionId <br>";
//echo "$ProductId:$$ProductId";

switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 17://审核通过
		if($From=="m"){
			$Log_Funtion="产品资料审核";	$SetStr="Estate=1"; include "../model/subprogram/updated_model_3d.php";		$fromWebPage=$funFrom."_m"; 
			}
		else{
		$Log_Funtion="审核";	$SetStr="TestStandard=1";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$ProductId=$checkid[$i];
			if($ProductId!=""){
				//加入已审核章
				//imagecopy( dst_im,src_im,dst_x,dst_y,src_x,src_y,src_w,src_h);
				//打开文件
				$waterimg = "../images/auditing.png";
				$wFile ="../download/teststandard/T".$ProductId.".jpg";
				//////////////////////////////////////////////
				$im= imagecreatefromjpeg($wFile);
				$wfilew=imagesx($im);//取得图片的宽
				$wfileh=imagesy($im);//取得图片的高
				imagealphablending($im, true);
				//读取水印文件
				$redline 	= 	imagecolorallocate($im,204,0,0);				//红色
				$UseFont = "c:/windows/fonts/simhei.ttf"; 						//使用的中文字体
				$aDate=date("ymd");
				imagettftext($im,9,283,188,40,$redline,$UseFont,$aDate);
				$wimgx=50;$wimgy=28;//放左上角
				$im2 = imagecreatefrompng($waterimg);
				$waterw=imagesx($im2);//取得水印图片的宽
				$waterh=imagesy($im2);//取得水印图片的高
				imagecopy($im, $im2, $wimgx, $wimgy, 0, 0, $waterw,$waterh);//拷贝水印到目标文件:目标，水印，水印X位置，水印Y位置，0，0，水印宽，水印高
				$r =imagepng($im,$wFile);//输出图片
				imagedestroy($im);
				imagedestroy($im2);
/////////////////////////////////////////////
				if($r){
					//更新记录
					$sql = "UPDATE $upDataSheet SET $SetStr WHERE ProductId=$ProductId";
					$result = mysql_query($sql);
					if($result){
						$Log="<p>产品 $ProductId 的检验标准图审核成功. <a href='$wFile' target='_black'>查看图档</a>";
						}
					else{
						$Log.="<div class='redB'>产品 $ProductId 的检验标准图审核失败.</div>";
						$OperationResult="N";
						}
					}
				else{
					$Log.="<div class='redB'>产品 $ProductId 的水印生成失败.</div>";
					$OperationResult="N";
					}
				}
			}
		$fromWebPage=$funFrom."_ts";
		}
		break;
	
	
	case 58:
		$Log_Funtion="复制";
		$ALType="From=$From&CompanyId=$CompanyId&ProductType=$ProductType&Pagination=$Pagination";
		
		$cName=FormatSTR($cName);//去连续空格,去首尾空格
		$eCode=FormatSTR($eCode);
		$Price=FormatSTR($Price);
		$Unit=FormatSTR($Unit);
		$Remark=FormatSTR($Remark);
		$pRemark=FormatSTR($pRemark);
		$Description=FormatSTR($Description);
		$Code=Chop(trim($Code));
			
		//复制
		if($CopyTo==1){
			$Counts=count($_POST["CompanyIdCC"]);
			for($i=0;$i<$Counts;$i++){
				$CompanyIdTemp=$_POST["CompanyIdCC"][$i];
				//$LockSql=" LOCK TABLES $DataIn.newproductdata WRITE";$LockRes=@mysql_query($LockSql);
				$maxSql = mysql_query("SELECT MAX(ProductId) AS Mid FROM $DataIn.newproductdata",$link_id);
				$theProductId=mysql_result($maxSql,0,"Mid");
				if($theProductId){
					$theProductId=$theProductId+1;
					}
				else{
					$theProductId=10001;
					}

				$inRecode="INSERT INTO $DataIn.newproductdata (Id,ProductId,cName,eCode,TypeId,Price,Unit,Moq,CompanyId,Description,Remark,pRemark,TestStandard,Img_H,Img_L,PackingUnit,Code,Date,Estate,Locks,Operator) VALUES (NULL,'$theProductId','$cName','$eCode','$TypeId','$Price','$Unit','0','$CompanyIdTemp','$Description','$Remark','$pRemark','0','0','0','$PackingUnit','$Code','$DateTime','1','0','$Operator')";
				$inAction=@mysql_query($inRecode);
				//解锁表
				//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
				if($inAction){ 
					$Log.="产品 $cName 的资料复制至客户 $CompanyIdTemp 成功!<br>";
					/////////////
					//复制BOM表
					$inRecode2="INSERT INTO $DataIn.pands SELECT NULL,'$theProductId',StuffId,Relation,Date,Operator,1,0,Operator,NOW(),Operator,NOW() FROM $DataIn.pands WHERE ProductId='$ProductId'";
					$inAction2=@mysql_query($inRecode2);
					if($inAction2){ 
						$Log.="产品BOM($ProductId -> $theProductId)复制成功!<br>";
						} 
					else{ 
						$Log.="<div class=redB>产品BOM($ProductId -> $theProductId)复制失败! $inRecode</div><br>";
						$OperationResult="N";
						}
					//复制标准图
					if($TestStandard==1){
						$OldFile="T".$ProductId.".jpg";
						$NewFile="T".$theProductId.".jpg";
						if(!copy("../download/newproductdata/$OldFile", "../download/newproductdata/$NewFile")) {
							$Log.="<div class=redB>产品标准图($ProductId -> $theProductId)复制失败! $inRecode</div><br>";
							$OperationResult="N";
							}
						else{
							$Log.="产品标准图($ProductId -> $theProductId)复制成功";
							}
						}
					
					//复制高清标准图
					if($Img_H==1){
						$OldFile="T".$ProductId."_H.jpg";
						$NewFile="T".$theProductId."_H.jpg";
						if(!copy("../download/newproductdata/$OldFile", "../download/newproductdata/$NewFile")) {
							$Log.="<div class=redB>产品高清图($ProductId -> $theProductId)复制失败! $inRecode</div><br>";
							$OperationResult="N";
							}
						else{
							$Log.="产品标高清图($ProductId -> $theProductId)复制成功";
							}
						}
						
					////复制微缩标准图
					if($Img_L==1){
						$OldFile="T".$ProductId."_L.jpg";
						$NewFile="T".$theProductId."_L.jpg";
						if(!copy("../download/newproductdata/$OldFile", "../download/newproductdata/$NewFile")) {
							$Log.="<div class=redB>产品微缩图($ProductId -> $theProductId)复制失败! $inRecode</div><br>";
							$OperationResult="N";
							}
						else{
							$Log.="产品微缩图($ProductId -> $theProductId)复制成功";
							}
						}
					
					
					////////////
					} 
				else{ 
					$Log.="<div class=redB>产品 $cName 的资料复制至客户 $CompanyIdTemp 失败!</div><br>";
					$OperationResult="N";
					}//if ($inAction)
				}//end for($i=0;$i<$Counts;$i++){
			}//if($CopyTo==1)
		
		
		break;
	case 63:
		$Log_Funtion="标准图复制";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$PId=$checkid[$i];
			if($PId!=""){
				if($ModelFile==""){//首产品，则上传文件
					$OldFile=$TestStandard;
					$PreFileName="T".$PId.".jpg";
					$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
					if($uploadInfo!=""){
						$ModelFile=$FilePath."T".$PId.".jpg";
						$up_sql = "UPDATE $DataIn.newproductdata SET TestStandard='2' WHERE 1 AND ProductId=$PId";
						$up_result = mysql_query($up_sql);
						if($up_result){
							$Log.="产品 $PId 的标准图上传成功.<br>";
							}
						else{
							$Log.="<div class=redB>产品 $PId 的标准图上传失败!</div>";$OperationResult="N";
							}
						}
					}
				else{//复制文件
					$toFile=$FilePath."T".$PId.".jpg";
					if(copy($ModelFile,$toFile)){
						$up_sql = "UPDATE $DataIn.newproductdata SET TestStandard='2' WHERE 1 AND ProductId=$PId";
						$up_result = mysql_query($up_sql);
						if($up_result){
							$Log.="产品 $PId 的标准图上传成功.<br>";
							}
						else{
							$Log.="<div class=redB>产品 $PId 的标准图上传失败!</div>";$OperationResult="N";
							}
						}
					}
				}
			}
		break;
	
	case 66:  //多个图档同时审核  add by 	
		//include "productdata_pass.php";	
		break;	

	case "Price":
		$Log_Funtion="更新产品资料价格";
		//$sql = "UPDATE $upDataSheet SET DeliveryDate='0000-00-00',UnDeliveryReson='$UnDeliveryReson' WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1";
		$sql = "UPDATE $upDataSheet SET Price='$price' WHERE ProductId='$ProductId' ORDER BY Id DESC LIMIT 1";
		$result = mysql_query($sql);
		if ($result){
			$Log="产品ID号为$ProductId 产品资料价格更新成功.<br>";
			}
		else{
			$Log="<div class=redB>产品ID号为 $ProductId 产品资料价格更新失败.</div><br>";
			$OperationResult="N";
			}
		break;

	case "Moq":
		$Log_Funtion="更新订单下限";
		$Moq=ceil($Moq);
		//$sql = "UPDATE $upDataSheet SET DeliveryDate='0000-00-00',UnDeliveryReson='$UnDeliveryReson' WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1";
		$sql = "UPDATE $upDataSheet SET Moq='$Moq' WHERE ProductId='$ProductId' ORDER BY Id DESC LIMIT 1";
		$result = mysql_query($sql);
		if ($result){
			$Log="产品ID号为$ProductId 产品资料订单下限更新成功.<br>";
			}
		else{
			$Log="<div class=redB>产品ID号为 $ProductId 产品资料订单下限更新失败.</div><br>";
			$OperationResult="N";
			}
		break;
		

	default:
		
		if($TestStandard!=""){
			$OldFile=$TestStandard;
			$PreFileName="T".$ProductId.".jpg";
			$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
			$FileValue=$uploadInfo==""?"":",TestStandard='2'";
			}
		//if($PictureValue=="" && $oldFile==1){//没有上传文件并且已选取删除原文件
		if($TestStandard=="" && $oldFile==1){//没有上传文件并且已选取删除原文件
			$delFile="T".$ProductId.".jpg";
			$DelFilePath=$FilePath.$delFile;
			if(file_exists($DelFilePath)){
				unlink($DelFilePath);
				$FileValue=",TestStandard='0'";
				}			
			}
			
			
		//高清图
		$uploadInfo="";
		if($Img_H!=""){
			$OldFile=$Img_H;
			$PreFileName="T".$ProductId."_H.jpg";
			$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
			$FileValue_H=$uploadInfo==""?"":",Img_H='2'";
			}
			
		if($Img_H=="" && $oldFile_H==1){//没有上传文件并且已选取删除原文件
			$delFile="T".$ProductId."_H.jpg";
			$DelFilePath=$FilePath.$delFile;
			if(file_exists($DelFilePath)){
				unlink($DelFilePath);
				$FileValue_H=",Img_H='0'";
				}			
			}			


		//微缩图
		$uploadInfo="";
		if($Img_L!=""){
			$OldFile=$Img_L;
			$PreFileName="T".$ProductId."_L.jpg";
			$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
			$FileValue_L=$uploadInfo==""?"":",Img_L='2'";
			}
		if($Img_L=="" && $oldFile_L==1){//没有上传文件并且已选取删除原文件
			$delFile="T".$ProductId."_L.jpg";
			$DelFilePath=$FilePath.$delFile;
			if(file_exists($DelFilePath)){
				unlink($DelFilePath);
				$FileValue_L=",Img_L='0'";
				}			
			}
			
			
			$cName=FormatSTR($cName);//去连续空格,去首尾空格
			$eCode=FormatSTR($eCode);
			$Price=FormatSTR($Price);
			$Unit=FormatSTR($Unit);
			$Remark=FormatSTR($Remark);
			$pRemark=FormatSTR($pRemark);
			$Description=FormatSTR($Description);
			$Code=Chop(trim($Code));
			$Date=date("Y-m-d");
			/*
			$SetStr="cName='$cName',eCode='$eCode',TypeId='$TypeId',Price='$Price',Moq='$Moq',
			Unit='$Unit',CompanyId='$CompanyId',Remark='$Remark',pRemark='$pRemark',Description='$Description',
			PackingUnit='$PackingUnit',Code='$Code',Date='$Date',Operator='$Operator',Locks='0' $FileValue  $FileValue_H  $FileValue_L";
			*/
			
			$SetStr="cName='$cName',eCode='$eCode',TypeId='$TypeId',Price='$Price',Moq='$Moq',
			Unit='$Unit',Remark='$Remark',pRemark='$pRemark',Description='$Description',
			PackingUnit='$PackingUnit',Code='$Code',Date='$Date',Operator='$Operator',Locks='0' $FileValue  $FileValue_H  $FileValue_L";
			include "../model/subprogram/updated_model_3a.php";
			break;
		}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>