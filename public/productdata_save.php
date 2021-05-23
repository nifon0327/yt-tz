<?php
include "../model/modelhead.php";
$Log_Item="产品资料";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
if($ModelFromcSign>0){
	if($SuplierId =="2140"){ //供应商是研砼信息
		$ClientCompanyId = "100426";//产品的客户就是研砼上海
	}else{
		$ClientCompanyId = $CompanyId;
	}
}else{
	$ClientCompanyId = $CompanyId;
}

$ALType="From=$From&CompanyId=$ClientCompanyId&ProductType=$ProductType&Pagination=$Pagination";
$maxSql = mysql_query("SELECT MAX(ProductId) AS Mid FROM $DataIn.productdata",$link_id);
$ProductId=mysql_result($maxSql,0,"Mid");
if($ProductId){
	$ProductId=$ProductId+1;}
else{
	$ProductId=80001;
	}

//上传图片
if($TestStandard!=""){
	$FilePath="../download/teststandard";
	$OldFile=$TestStandard;
	$PreFileName="T".$ProductId.".jpg";
	$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
	}
$PictureValue=$uploadInfo==""?0:2;


$Img_HValue=0;
$cName=trim($cName);
$eCode=trim($eCode);
$Price=$Price;
$Unit=$Unit;
$Remark=FormatSTR($Remark);
$pRemark=FormatSTR($pRemark);
$Description=FormatSTR($Description);
$Code=Chop(trim($Code));
$Date=date("Y-m-d");

//检查该客户下是否已经存在相同中名字的产品
$checkNameSql=mysql_query("SELECT  Id FROM $DataIn.productdata WHERE cName='$cName' AND CompanyId='$ClientCompanyId' LIMIT 1",$link_id);
if($checkNameRow=mysql_fetch_array($checkNameSql)){
	$Log="&nbsp;&nbsp;&nbsp;&nbsp;该客户已经存在名称为 $cName 的产品!<br>";
	$OperationResult="N";
	}
else{
	//上传认证图档
	$uploadResult="N";
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
							$Log.="产品 $ProductId 的图档 $uploadInfo 添加成功.<br>";
							$EndNumber++;
						     }
						else{
							$Log.="<div class='redB'>产品 $ProductId 的图档 $uploadInfo 添加失败. $inCer </div><br>";
						   }
					   }
				   }
			  }
		}
		$buySign    = $buySign==""?0:$buySign;
		$taxtypeId  = $taxtypeId ==""?1:$taxtypeId;
		$InspectionSign = $InspectionSign ==""?0:$InspectionSign;
		$dzSign     = $dzSign ==""?0:$dzSign;
		$LoadQty    = $LoadQty ==""?0:$LoadQty;
		$MainWeight = $MainWeight ==""?0.00:$MainWeight;
		$Weight     = $Weight ==""?0.00:$Weight;
		$MisWeight  = $MisWeight ==""?0.00:$MisWeight;
		$Moq        = $Moq ==""?0:$Moq;

		$MaterialQ  = $MaterialQ>0?$MaterialQ:0;
		$UseWay     = $UseWay>0?$UseWay:0;

		$inRecode="INSERT INTO $DataIn.productdata(Id,ProductId,cName,eCode,TypeId,Price,Unit,Moq,MainWeight,Weight,MisWeight,
		CompanyId,Description,Remark,pRemark,bjRemark,LoadQty,TestStandard,Img_H,PackingUnit,Code,
		Date,dzSign,taxtypeId,buySign,productsize,ReturnReasons,InspectionSign,MaterialQ,UseWay,Estate,Locks,Operator)VALUES 
		(NULL,'$ProductId','$cName','$eCode','$TypeId','$Price','$Unit','$Moq','$MainWeight','$Weight','$MisWeight',
		'$ClientCompanyId','$Description','$Remark','$pRemark','$bjRemark','$LoadQty','$PictureValue','$Img_HValue',
		'$PackingUnit','$Code','$Date','$dzSign','$taxtypeId','$buySign','','','$InspectionSign',
		'$MaterialQ','$UseWay','2','0','$Operator')";

		$inAction=@mysql_query($inRecode);
		if ($inAction){
				$Log.="&nbsp;&nbsp;&nbsp;&nbsp;新增名称为 $cName / $ProductId 的产品资料成功!<br>";
				if($ModelFromcSign>0){
				     $BuyerId = $BuyerId==""?0:$BuyerId;
				     $SuplierId  = $SuplierId ==""?0:$SuplierId;
		             $SalePrice  = $SalePrice ==""?0:$SalePrice;
		             $GWeight   = $GWeight ==""?0:$GWeight;
		             $SaleBoxPcs   = $SaleBoxPcs ==""?0:$SaleBoxPcs;
					 $InsertSaleSql = "INSERT INTO $DataIn.sale_productdata(Id,CompanyId,ProductId,SalePrice,SuplierId,
					 BuyerId,SaleBoxSpec,SaleBoxPcs,GWeight,Estate,Locks,Date,Operator,PLocks,creator,created,modifier,modified)
					 VALUES(NULL,'$CompanyId','$ProductId','$SalePrice','$SuplierId','$BuyerId','$SaleBoxSpec','$SaleBoxPcs',
					 '$GWeight','1','0','$Date','$Operator','0','$Operator','$DateTime','$Operator','$DateTime')";
					 $InsertSaleResult = mysql_query($InsertSaleSql);
					 $InsertSaleStockSql = "INSERT INTO $DataIn.sale_productstock(Id,ProductId,tStockQty,oStockQty,Estate, 
					 Locks,Date,Operator)VALUES(NULL,'$ProductId','0','0','1','0','$Date','$Operator')";
					 $InsertSaleStockResult = mysql_query($InsertSaleStockSql);
				}

				if($ClientProxy>0){ //品牌授权书连接
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
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;名称为 $cName / $ProductId 的产品资料新增失败! $inRecode </div><br>";
			$OperationResult="N";
			}
}
  if($ClientCompanyId==1074){//strax生成XML文件
      include "productdata_strax_toxml.php";
  }

//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
