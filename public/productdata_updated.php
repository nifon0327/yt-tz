<?php
 ob_start();
include "../model/modelhead.php";
include "../basic/downloadFileIP.php";  //取得下载文档的远程
header("Content-Type: text/html; charset=gb2312");
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：
$Log_Item="产品资料";		//需处理
$upDataSheet="$DataIn.productdata";	//需处理
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
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		$EstateStr=" AND Estate<2";
		include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		$EstateStr=" AND Estate<2";
		include "../model/subprogram/updated_model_3d.php";
        if($From=="forbidden")$fromWebPage=$funFrom."_forbidden";
        break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 15://条码文件退回
		switch($From){
			case "cm":
		               $Log_Funtion="条码或标签文件审核退回";$SetStr="Estate=1";
		               $upDataSheet="$DataIn.file_codeandlable";	//需处理
	                	include "../model/subprogram/updated_model_3d.php";
	               	   $fromWebPage=$funFrom."_cm";
		         break;
		case "m":
                   $Log_Funtion="审核退回";
                    $SetStr="ReturnReasons='$ReturnReasons',Estate=3";
                    include "../model/subprogram/updated_model_3d.php";
	               	   $fromWebPage=$funFrom."_m";
          break;
            }
		break;
	case 17://审核通过:标准图审核、产品资料审核
		switch($From){
			case "cm":
			$Log_Funtion="条码或标签文件审核";$SetStr="Estate=0";
			$upDataSheet="$DataIn.file_codeandlable";	//需处理
			include "../model/subprogram/updated_model_3d.php";
			$fromWebPage=$funFrom."_cm";
		break;
		case "m":
		      $Log_Funtion="产品资料审核";
              $SetStr="Estate=1,ReturnReasons=''";
              include "../model/subprogram/updated_model_3d.php";
             $fromWebPage=$funFrom."_m";
		     $checkResult=mysql_query("SELECT CompanyId FROM  $upDataSheet  WHERE CompanyId IN(1064,1066) AND Id IN ($Ids) GROUP BY CompanyId",$link_id);
	    	while($checkRows = mysql_fetch_array($checkResult)){
	              $CompanyId=$checkRows["CompanyId"];
	          	//更新xml文件
				include "productdata_toxml.php";
        }

        //更新pandsCharge表的状态
        $pandsChargeUpdateSql = "update $DataIn.pandscharge A 
        						 Left Join $DataIn.productdata B On A.ProductId = B.ProductId
        						 Set A.Estate = '0' 
        						 Where B.Id in ($Ids)";
        mysql_query($pandsChargeUpdateSql);
		break;
	default:
	    include_once("../model/subprogram/audit_records.php");
		$Log_Funtion="产品标准图审核";	$SetStr="TestStandard=1";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$ProductId=$checkid[$i];
				if($ProductId!=""){
					//更新记录
					$sql = "UPDATE $upDataSheet SET $SetStr WHERE ProductId=$ProductId";
					$result = mysql_query($sql);
					if($result){
						$Log="产品 $ProductId 的标准图审核成功<br>";
						include "../model/subprogram/delete_orderTfile.php";   //删除订单为重新上传标准图的标记
						/*$del="DELETE FROM $DataIn.test_remark WHERE ProductId IN ($ProductId)";//删除标准图备注纪录
						$DelResult = mysql_query($del);
						if($DelResult && mysql_affected_rows()>0){
							$Log=$Log."且该产品的备注纪录删除成功";
							}*/
							//审核记录
					      addAuditRecords($ProductId,"TestStandard","$ActionId","",$Operator,$DataIn,$link_id);
						}
					else{
						$Log.="<div class='redB'>产品 $ProductId 的检验标准图审核失败.</div>";
						$OperationResult="N";
						}
					}
				}
			$fromWebPage=$funFrom."_ts";
			break;
			}
		break;
	case 40:
		$Log_Funtion="高清图片上传";//上传带包装高清图(1)，和不带包装高清图(2)
		//之前最后一个记录
		$FilePath="../download/teststandard/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$Date=date("Y-m-d");
       $DateYmd=date("Ymd");

         //strax高清图片，上传一份给strax FTP 下载
           $CheckCompanyResult=mysql_fetch_array(mysql_query("SELECT CompanyId,eCode FROM $DataIn.productdata WHERE ProductId=$ProductId",$link_id));
           $CompanyId=$CheckCompanyResult["CompanyId"];
           $eCode=$CheckCompanyResult["eCode"];
           if($CompanyId==1074 && $eCode!="" ){
                  		$straxFilePath="../client/strax/picture";

			if($Img_H1!=""){//带包装的高清图档
				  $straxFileName1=$eCode."-".$DateYmd."(pack).zip";
				  $straxuploadInfo1=UploadPictures($Img_H1,$straxFileName1,$straxFilePath);
				}
			if($Img_H2!=""){//不带包装的高清图档
				  $straxFileName2=$eCode."-".$DateYmd."(no-pack).zip";
				  $straxuploadInfo2=UploadPictures($Img_H2,$straxFileName2,$straxFilePath);
				}
             }
           //************带包装的高清图档
			if($Img_H1!=""){
				    $HPreFileName1="T".$ProductId."_H(pack)".".zip";
				    $HuploadInfo1=UploadPictures($Img_H1,$HPreFileName1,$FilePath);
				}
              if($HuploadInfo1!=""){
                        $DelSql1="DELETE  FROM $DataIn.productimg WHERE  ProductId=$ProductId AND Type=1"; $DelResult1=@mysql_query($DelSql1);
				        $inRecode1="INSERT INTO $DataIn.productimg (Id,ProductId,Picture,Date,Type,Operator) VALUES (NULL,'$ProductId','$HuploadInfo1','$Date','1','$Operator')";
						$inAction1=@mysql_query($inRecode1);
						if($inAction1){
							  $Log.="产品 $ProductId 的带包装高清图片 $HuploadInfo1 上传成功.<br>";

							  //记录上传时间
							  $UpFileSign="PackPicture";
							  $Record_Id=$ProductId;
		                      include "../model/subprogram/upload_records.php";
							}
                }
            //不带包装的高清图档
			if($Img_H2!=""){
				    $HPreFileName2="T".$ProductId."_H(no-pack)".".zip";
				    $HuploadInfo2=UploadPictures($Img_H2,$HPreFileName2,$FilePath);
				}
              if($HuploadInfo2!=""){
                        $DelSql2="DELETE  FROM $DataIn.productimg WHERE  ProductId=$ProductId AND Type=2"; $DelResult2=@mysql_query($DelSql2);
				        $inRecode2="INSERT INTO $DataIn.productimg (Id,ProductId,Picture,Date,Type,Operator) VALUES (NULL,'$ProductId','$HuploadInfo2','$Date','2','$Operator')";
						$inAction2=@mysql_query($inRecode2);
						if($inAction2){
							  $Log.="产品 $ProductId 的不带包装高清图片 $HuploadInfo2 上传成功.<br>";

							  //记录上传时间
							  $UpFileSign="NoPackPicture";
							  $Record_Id=$ProductId;
		                      include "../model/subprogram/upload_records.php";
							}
                }

	   	//==========================================上传产品标准图
	   	$pushSign=0;
	   	$CutPath="../download/teststandard/T".$ProductId."/";
	   	if(file_exists($CutPath)){
	        unlink($CutPath);
	    	}
	   	$GoodsFilePath="../download/teststandard/";
		if(!file_exists($GoodsFilePath)){
			makedir($GoodsFilePath);
			}
	   if($GoodsPicture!=""){
			$oldGoods=$GoodsPicture;
			$FileType=".jpg";
			$NewGoodspicture="T".$ProductId.$FileType;
			$upGoodsInfo=UploadFiles($oldGoods,$NewGoodspicture,$GoodsFilePath);
			if($upGoodsInfo!=""){
				$delResult=mysql_query("DELETE FROM $DataIn.productstandimg WHERE ProductId='$ProductId'",$link_id);
			    $upGoods="INSERT INTO  $DataIn.productstandimg (Id, ProductId, Picture, Estate,Date, Operator) 
					VALUES (NULL,'$ProductId','$upGoodsInfo','2','$Date','$Operator')";
				$upAction=mysql_query($upGoods);
				if($upAction){
					$Log.="产品 $ProductId 的标准图 $upGoodsInfo 添加成功.<br>";
					$del="DELETE FROM $DataIn.test_remark WHERE ProductId IN ($ProductId)";//删除标准图备注纪录
					$DelResult = mysql_query($del);
					if($DelResult && mysql_affected_rows()>0){
						$Log=$Log."且该产品的备注纪录删除成功";
						}
					   $pushSign=1;
					}
				else{
					$Log.="<div class='redB'>产品 $ProductId 的标准图 $upGoodsInfo 添加失败. 标准图原件请一并上传 </div><br>";
					$OperationResult="N";
					}
				}
			$GoodsValues=$upGoodsInfo==""?0:2;
			$upProductdata="update productdata set TestStandard='$GoodsValues' where ProductId='$ProductId'";
			$standResult=mysql_query($upProductdata);

			if ($GoodsValues==2){//记录上传时间
				$Record_Id=$ProductId;
				$UpFileSign="TestStandard";
				include "../model/subprogram/upload_records.php";
			}
		}
	//App展示小图
	   if($AppPicture!=""){
	       $AppFilePath="../download/productIcon/";
		   if(!file_exists($AppFilePath)){
			      makedir($AppFilePath);
			}

			$oldFiles=$AppPicture;
			//$FileType=".jpg";
			$FileType=substr("$AppPicture_name", -4, 4);
			$NewFilesPicture=$ProductId.$FileType;
			$upFileInfo=UploadFiles($oldFiles,$NewFilesPicture,$AppFilePath);
			if($upFileInfo!=""){
						$Log=$Log."产品 $ProductId 的APP展示图上传成功.<br>";
			}
		else{
			$Log.="<div class='redB'>产品 $ProductId 的APP展示图上传失败.</div><br>";
			$OperationResult="N";
			}
		}

	//客户提供的产品图
	   if($ClientPicture!=""){
	       $ClientFilePath="../download/productClient/";
		   if(!file_exists($ClientFilePath)){
			      makedir($ClientFilePath);
			}

			$oldFiles=$ClientPicture;
			$FileType=substr("$ClientPicture_name", -4, 4);
			$NewFilesPicture=$ProductId.$FileType;
			$upFileInfo=UploadFiles($oldFiles,$NewFilesPicture,$ClientFilePath);
			if($upFileInfo!=""){
			 $Log=$Log."产品 $ProductId 的客户提供的产品图上传成功.<br>";
			}
		else{
			$Log.="<div class='redB'>产品 $ProductId 的APP展示图上传失败.</div><br>";
			$OperationResult="N";
			}
		}

	//================================================上传标准图原文件
	if ($donwloadFileIP!="") {  //有IP则走远程审核
		$Log_Funtion="PDF远程(FTP)图片上传";
		$Log.="Id 号为 $ProductId 的文件更新： $FileStatus2 文件名：$PreFileName2 ";
		if ($DataStatus2<1) {
			if ( $DataStatus2==0) {  //如果远程更新失败，可在这写入数据库，看情况吧 。
				$Log.=" 数据状态更新失败: $DataStatus2 </br>";
			}
			else {
				$Log.=" 数据状态更新失败: $DataStatus2 </br>";
			}

		}
		else {
			$Log.=" 数据状态更新成功: $DataStatus2  </br>";
		}
		$OperationResult="N";

	}

	else {

		 $FindResult=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.productdata 
		 WHERE ProductId='$ProductId'",$link_id));
			$CompanyId=$FindResult["CompanyId"];
			$ProductType=$FindResult["TypeId"];
			$FileRemark=$FindResult["cName"];
			$originalFilePath="../download/standarddrawing/";
			if(!file_exists($originalFilePath)){
				   makedir($originalFilePath);
			   }
		 if($originalPicture!=""){
				  $FType=substr("$originalPicture_name", -4, 4);
				  $Ohycfile=$originalPicture;
				  $datelist=newGetDateSTR();
				  $PreFileName=$datelist.$FType;
				  $Attached=UploadFiles($Ohycfile,$PreFileName,$originalFilePath);
			  if($Attached!=""){
				   $inRecode="INSERT INTO $DataIn.doc_standarddrawing(Id,FileType,FileRemark,
				   FileName,CompanyId,ProductType,Estate,Locks,Date,Operator) VALUES 
				   (NULL,'1','$FileRemark','$PreFileName','$CompanyId',
				   '$ProductId','1','0','$Date','$Operator')";
				   $inAction=@mysql_query($inRecode);
				   if($inAction){
					  $Log.="产品标准图存档成功!<br>";
					   }
				   else{
					  $Log.="<div class=redB>产品标准图存档失败! $inRecode </div><br>";
					  $OperationResult="N";
					  }
					  $Log.="ID为 $ProductId 的产品标准图原件上传成功<br>";
				   }
			  else{
					 $Log.="<div class='redB'>ID为 $ProductId 的产品标准图原件上传失败</div><br>";
					 $OperationResult="N";
				  }
			   }
		  else{
			   $Log.="<div class='redB'>ID为 $ProductId 的产品标准图原件没有上传</div><br>";
			 }
		}
		if ($pushSign==1) {
			 //推送通知
		     include "d:/website/mc/iphoneAPI/subpush/teststandard_push.php";
		}
		break;

	case 58:
		$Log_Funtion="复制";
		$Lens=count($CopyTo);
		for($i=0;$i<$Lens;$i++){
			$cSign=$CopyTo[$i];
			//复制产品资料
			$CompanyIdTemp="CompanyId".strval($cSign);
			$theCompanyId=$$CompanyIdTemp;
			$theData="d".strval($cSign);
			////////////////////////////////////////////////////////////////
			$ALType="From=$From&CompanyId=$CompanyId&ProductType=$ProductType&Pagination=$Pagination";
			//$LockSql=" LOCK TABLES $theData.productdata WRITE";$LockRes=@mysql_query($LockSql);
			$maxSql = mysql_query("SELECT MAX(ProductId) AS Mid FROM $theData.productdata",$link_id);
			$theProductId=mysql_result($maxSql,0,"Mid");
			if($theProductId){
				$theProductId=$theProductId+1;}
			else{
				$theProductId=80001;}

			$cName=trim($cName);//去连续空格,去首尾空格
			$eCode=trim($eCode);
			$Price=FormatSTR($Price);
			$Unit=FormatSTR($Unit);
			$Remark=FormatSTR($Remark);
			$pRemark=FormatSTR($pRemark);
			$Description=FormatSTR($Description);
			$Code=Chop(trim($Code));
			$Date=date("Y-m-d");
			$inRecode="INSERT INTO $theData.productdata
				(Id,ProductId,cName,eCode,TypeId,Price,Unit,Moq,MainWeight,Weight,CompanyId,Description,Remark,pRemark,bjRemark,LoadQty,TestStandard,Img_H,Date,PackingUnit,dzSign,Estate,Locks,Code,Operator)			 
				VALUES(NULL,'$theProductId','$cName','$eCode','$TypeId','$Price','$Unit','0','$MainWeight','$Weight','$theCompanyId','$Description','$Remark','$pRemark','$bjRemark','0','0','0','$Date','$PackingUnit','0','1','0','$Code','$Operator')";
			$inAction=@mysql_query($inRecode);
			//解锁表
			//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
			if ($inAction){
				$Log.="产品基本资料($ProductId -> $theProductId)复制成功!<br>";
				}
			else{
				$Log.="<div class=redB>产品基本资料($ProductId -> $theProductId)复制失败(检查中文名是否有重复)! $inRecode</div><br>";
				$OperationResult="N";
				}
				////////////////////////////////////////////////////////////////ProductId
			//复制BOM表
			$inRecode="INSERT INTO $theData.pands SELECT NULL,'$theProductId',StuffId,Relation,Diecut,Cutrelation,bpRate,Date,Operator,1,0,0,Operator,NOW(),Operator,NOW() FROM $DataIn.pands WHERE ProductId='$ProductId'";
			$inAction=@mysql_query($inRecode);
			if ($inAction){
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
				if (!copy("../data$Login_cSign/teststandard/$OldFile", "../data$cSign/teststandard/$NewFile")) {
					$Log.="<div class=redB>产品标准图($ProductId -> $theProductId)复制失败! $inRecode</div><br>";
					$OperationResult="N";
					}
				else{
					$Log.="产品标准图($ProductId -> $theProductId)复制成功";
					}
				}
			}//end for
		break;
	case 81://案例连接
		$delRecode = "DELETE FROM $DataIn.casetoproduct WHERE ProductId='$ProductId'";
		$delAction =@mysql_query($delRecode);

		$Log_Funtion="检讨报告连接更新";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$cId=$checkid[$i];
			if ($cId!=""){
				$inRecode="INSERT INTO $DataIn.casetoproduct (Id,ProductId,cId) VALUES (NULL,'$ProductId','$cId')";
				$inAction=@mysql_query($inRecode);
				if($inAction){
					$Log.="$i - $Log_Funtion 成功!<br>";
					}
				else{
					$Log.="<div class=redB>$i - $Log_Funtion 失败! </div><br>";
					$OperationResult="N";
					}
				}
			}

		break;

	case 74:
	     $Log_Funtion="更改标准图";
	     $Lens=count($checkid);
		  for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			$UpdateTestStandard="UPDATE $DataIn.productdata SET TestStandard='3' WHERE Id='$Id'";
			$UpdateReuslt=mysql_query($UpdateTestStandard);
			if($UpdateReuslt){
			    $Log.="ID为 $Id - $Log_Funtion 成功!<br>";
			    }
			else{
			    $Log.="<div class=redB>ID为 $Id - $Log_Funtion 失败! </div><br>";
			    $OperationResult="N";
			    }
			}
	    break;
	default:
		$FilePath="../download/teststandard/";
		$Date=date("Y-m-d");
		if($TestStandard!=""){
			$OldFile=$TestStandard;
			$PreFileName="T".$ProductId.".jpg";
			$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
			$FileValue=$uploadInfo==""?"":",TestStandard='2'";
			if($uploadInfo!=""){
			     $delResult=mysql_query("DELETE FROM $DataIn.productstandimg WHERE ProductId='$ProductId'",$link_id);
			     $upGoods="INSERT INTO  $DataIn.productstandimg (Id, ProductId, Picture, Date, Operator) 
				           VALUES (NULL,'$ProductId','$uploadInfo','$Date','$Operator')";
					 $upAction=mysql_query($upGoods);
					 if($upAction){
					           $Log.="&nbsp;&nbsp;ID号为 $Id 的产品 $ProductId 的标准图 $upGoodsInfo 添加成功.<br>";
							       }
						     else{
							         $Log.="<div class='redB'>&nbsp;&nbsp;ID号为 $Id 的产品 $ProductId 的标准图 $upGoodsInfo 添加失败 $upGood</div><br>";
							      $OperationResult="N";
							     }
						}

				$GoodsValues=$upGoodsInfo==""?0:2;
				$upProductdata="update $DataIn.productdata set TestStandard='$GoodsValues' where ProductId='$ProductId'";
				$standResult=mysql_query($upProductdata);

				if ($GoodsValues==2){
					$Record_Id=$ProductId;
				    $UpFileSign="TestStandard";
				    include "../model/subprogram/upload_records.php";
				}
		   }
		 if($PictureValue=="" && $oldFile==1){//没有上传文件并且已选取删除原文件
			$delFile="T".$ProductId.".jpg";
			$DelFilePath=$FilePath.$delFile;
			if(file_exists($DelFilePath)){
				unlink($DelFilePath);
				$FileValue=",TestStandard='0'";
				}
			}
            $Img_HValue="";

            $cName=trim($cName);
			$Remark=FormatSTR($Remark);
			$pRemark=FormatSTR($pRemark);
			$Description=FormatSTR($Description);
			$Code=Chop(trim($Code));
			$Date=date("Y-m-d");

			/////////////////////////////////////////认证图档上传
			$CerPath="../download/productcer/";
			if(!file_exists($CerPath)){
				makedir($CerPath);
				}
			$Date=date("Y-m-d");
			$EndNumber=1;
			$checkEndFile=mysql_fetch_array(mysql_query("SELECT MAX(Picture) AS EndPicture FROM $DataIn.product_certification WHERE ProductId='$ProductId'",$link_id));
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
				$TemprzRemark=$rzRemark[$i];
				if ($upPicture!=""){
					$OldFile=$upPicture;
					//检查是否有原档，如果有则使用原档名称，如果没有，则分配新档名
					if($TempOldImg!=""){
						$PreFileName=$TempOldImg;
						}
					else{
						$PreFileName=$ProductId."_".$EndNumber.".pdf";
						}
					$uploadInfo=UploadFiles($OldFile,$PreFileName,$CerPath);
					if($uploadInfo!=""){
						if($TempOldImg==""){//写入记录
							$inCer="INSERT INTO $DataIn.product_certification (Id,ProductId,Picture,Date,Remark,Operator) VALUES (NULL,'$ProductId','$uploadInfo','$Date','$TemprzRemark','$Operator')";
							$CerAction=@mysql_query($inCer);
							if($CerAction){
								$Log.="产品 $ProductId 的图档 $uploadInfo 更新成功.<br>";
								$EndNumber++;
							     }
							else{
								$Log.="<div class='redB'>产品 $ProductId 的图档 $uploadInfo 更新失败. $inCer </div><br>";
							   }
						   }
					   }
				  }
			}

			if($ModelFromcSign>0){
				if($SuplierId =="2140"){ //供应商是研砼信息
					$ClientCompanyId = "100426";//产品的客户就是研砼上海
				}else{
					$ClientCompanyId = $CompanyId;
				}
			}else{
				$ClientCompanyId = $CompanyId;
			}
			//检查同一客户同一名称是否存在
			$checkNameSql=mysql_query("SELECT Id  FROM $DataIn.productdata 
			WHERE cName='$cName' AND CompanyId='$ClientCompanyId' AND Id!='$Id' LIMIT 1 ",$link_id);
			if($checkNameRow=mysql_fetch_array($checkNameSql)){
				    $Log="&nbsp;&nbsp;&nbsp;&nbsp;该客户已经存在名称为 $cName 的产品!<br>";
				    $OperationResult="N";
				}
			else{
            	$checkEstateResult=mysql_query("SELECT Id  FROM $DataIn.productdata 
            	WHERE Id='$Id' AND cName='$cName' AND eCode='$eCode' AND Price='$Price' AND bjRemark='$bjRemark'",$link_id);
            	$SetEstateStr=mysql_num_rows($checkEstateResult)>0?"":",Estate='2' ";

            	if($SalePrice>0 || $SuplierId>0){
	            	$checkEstateResult2=mysql_query("SELECT Id  FROM $DataIn.sale_productdata 
            	    WHERE Id='$Id' AND SalePrice='$SalePrice' AND SuplierId='$SuplierId' ",$link_id);
            	    $SetEstateStr=mysql_num_rows($checkEstateResult2)>0?"":",Estate='2' ";
            	}
				$Description   = FormatSTR($Description);
				$Moq           = $Moq ==""?0:$Moq;
				$Weight        = $Weight ==""?0:$Weight;
				$MainWeight    = $MainWeight ==""?0:$MainWeight;
				$MisWeight     = $MisWeight ==""?0:$MisWeight;
				$LoadQty       = $LoadQty ==""?0:$LoadQty;
				$taxtypeId     = $taxtypeId ==""?1:$taxtypeId;
				$dzSign        = $dzSign ==""?0:$dzSign;
				$InspectionSign = $InspectionSign ==""?0:$InspectionSign;
				$MaterialQ  = $MaterialQ ==""?0:$MaterialQ;
		        $UseWay  = $UseWay ==""?0:$UseWay;
				$SetStr="cName='$cName',eCode='$eCode',TypeId='$TypeId',Price='$Price',Moq='$Moq',Weight='$Weight',
				MisWeight='$MisWeight',MainWeight='$MainWeight',Unit='$Unit',CompanyId='$ClientCompanyId',Remark='$Remark',
				pRemark='$pRemark',bjRemark='$bjRemark',LoadQty='$LoadQty',Description='$Description',dzSign='$dzSign',
				taxtypeId='$taxtypeId',buySign='$buySign',InspectionSign='$InspectionSign',PackingUnit='$PackingUnit',
				Code='$Code',MaterialQ='$MaterialQ',UseWay='$UseWay',
				Date='$Date',Operator='$Operator',Locks='0' $SetEstateStr $FileValue ";
				include "../model/subprogram/updated_model_3a.php";

				if($ModelFromcSign>0){ //来自于(研砼HK,或者研砼贸易) 添加产品资料
				    $BuyerId = $BuyerId==""?0:$BuyerId;
				    $SuplierId = $SuplierId ==""?0:$SuplierId;
				    $SalePrice = $SalePrice ==""?0:$SalePrice;
				    $GWeight   = $GWeight ==""?0:$GWeight;
		            $SaleBoxPcs   = $SaleBoxPcs ==""?0:$SaleBoxPcs;
					$CheckSaleResult = mysql_query("SELECT Id FROM sale_productdata WHERE ProductId ='$ProductId'",$link_id);
					if($CheckSaleRow = mysql_fetch_array($CheckSaleResult)){
						$UpdateSaleSql = "UPDATE $DataIn.sale_productdata 
						SET CompanyId='$CompanyId',SalePrice='$SalePrice',SuplierId='$SuplierId',SaleBoxSpec='$SaleBoxSpec', 
						SaleBoxPcs='$SaleBoxPcs',GWeight='$GWeight',BuyerId='$BuyerId' WHERE ProductId ='$ProductId'";
						$UpdateSaleResult = mysql_query($UpdateSaleSql);
					}else{

						 $InsertSaleSql = "INSERT INTO $DataIn.sale_productdata(Id,CompanyId,ProductId,SalePrice,SuplierId,
						 BuyerId,SaleBoxSpec,SaleBoxPcs,GWeight,Estate,Locks,Date,Operator,PLocks,creator,created,modifier,
						 modified)VALUES(NULL,'$CompanyId','$ProductId','$SalePrice','$SuplierId','$BuyerId','$SaleBoxSpec',
						 '$SaleBoxPcs','$GWeight','1','0','$Date','$Operator','0','$Operator',
						 '$DateTime','$Operator','$DateTime')";
						 $InsertSaleResult = mysql_query($InsertSaleSql);
						 $InsertSaleStockSql = "INSERT INTO $DataIn.sale_productstock(Id,ProductId,tStockQty,oStockQty,Estate, 
						 Locks,Date,Operator)VALUES(NULL,'$ProductId','0','0','1','0','$Date','$Operator')";
						 $InsertSaleStockResult = mysql_query($InsertSaleStockSql);
					}
				}

				//$ClientProxy
				if($ClientProxy>0){
					$ClientProxyRow = mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.yw7_clientproduct 
					WHERE ProductId ='$ProductId'",$link_id));
					$ClientProxyId = $ClientProxyRow["Id"];
					if($ClientProxyId>0){
						$ClientProxySql = "UPDATE $DataIn.yw7_clientproduct SET cId ='$ClientProxy' WHERE ProductId ='$ProductId'";
						$ClientProxyResult = mysql_query($ClientProxySql);
						if($ClientProxyResult){
							$Log="&nbsp;&nbsp;&nbsp;&nbsp;该产品的品牌授权书更新成功!<br>";
						}else{
							$Log="&nbsp;&nbsp;&nbsp;&nbsp;该产品的品牌授权书更新失败!<br>";
				            $OperationResult="N";
						}
					}else{
						$ClientProxySql="INSERT INTO $DataIn.yw7_clientproduct (Id,ProductId,cId,Estate,Locks,PLocks,creator,
					    created, modifier,modified,Date,Operator)VALUES(NULL,'$ProductId','$ClientProxy','1','0','0',
					    '$Operator','$DateTime','$Operator','$DateTime','$Date','$Operator')";
		                $ClientProxyResult = mysql_query($ClientProxySql);
		                if($ClientProxyResult){
							$Log="&nbsp;&nbsp;&nbsp;&nbsp;该产品的品牌授权书新增成功!<br>";
						}else{
							$Log="&nbsp;&nbsp;&nbsp;&nbsp;该产品的品牌授权书新增失败!<br>";
				            $OperationResult="N";
						}
					}
				}

				 //更新xml文件
				 include "productdata_toxml.php";
 				 if($ClientCompanyId==1074){//strax生成XML文件
                      include "productdata_strax_toxml.php";
                   }
	        }
			break;
		}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=mysql_query($IN_recode);
include "../model/logpage.php";
?>