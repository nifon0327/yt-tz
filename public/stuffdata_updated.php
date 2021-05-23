<?php
include "../model/modelhead.php";
include "../basic/downloadFileIP.php";  //取得下载文档的远程IP

$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：
$Log_Item="配件资料";		//需处理
$upDataSheet="$DataIn.stuffdata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$FilePath="../download/stufffile/";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";	$EstateStr=" AND Estate=0 ";	include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0"; $EstateStr=" AND Estate=1 ";		include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 107://禁用配件
        $Log_Funtion="禁用配件";
    	$Date=date("Y-m-d");
	    $Delstuff="DELETE FROM $DataIn.stuffdisable WHERE StuffId  IN SELECT StuffId FROM $upDataSheet  WHERE Id IN ($Ids)";
	    $DelResult=@mysql_query($Delstuff);
    	$InSql="INSERT INTO $DataIn.stuffdisable SELECT NULL,StuffId,'$Reason','$Date','$Operator','1','0','0','$Operator','$DateTime',null,null FROM $upDataSheet WHERE Id IN ($Ids)";
    	$InRecode=mysql_query($InSql);
    	if($InRecode){
		    	$Log.="配件ID  ($Ids) 禁用原因添加成功</br>";
	           	$SetStr="Estate=0,Locks=0";
	    		$sql = "UPDATE $upDataSheet SET $SetStr WHERE Id IN ($Ids)";
                $result = mysql_query($sql);
                if($result){
	                 $Log.="配件ID号在 ($Ids) 的记录成功 $Log_Funtion. $sql</br>";
	                 }
                else{
	                $Log.="<div class='redB'>配件ID号为  ($Ids) 的记录$Log_Funtion 失败! $sql</br></div>";
	                $OperationResult="N";
	               }
                  //直接报废
                 $StuffCount =count($StuffIdArray);
                 for($k=0;$k<$StuffCount;$k++){
                       if($bfQtyArray[$k]>0){
                               $StuffId=$StuffIdArray[$k];
                               $bfQty=$bfQtyArray[$k];
                               $bfReason =$bfReasonArray[$k];
                               $bfType=$bfTypeArray[$k];
                               $inRecode="INSERT INTO $DataIn.ck8_bfsheet SELECT NULL,'$Operator',StuffId,'$bfQty','$bfReason','$bfType','0','','$Date','1','0','$Operator','$DateTime','0','$Operator','$DateTime',null,null
                               FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' and (oStockQty-mStockQty)>=$bfQty and tStockQty>=$bfQty";
                               $inAction=@mysql_query($inRecode);
                               if ($inAction && mysql_affected_rows()>0){
	                                  $Log.="禁用配件 $StuffId 报废成功!，报废原因：$bfReason ，请相关人员审核<br>";
                                  }
                               else{
	                                  $Log.="<div class='redB'>禁用配件 $StuffId 报废失败!,请手动报废</div><br>";
                                 }
                             }
                       }
	             }
       else {
	             $Log.="<div class='redB'>配件ID号为  ($Ids) 的记录$Log_Funtion 失败!</br></div>";
		         $Log.="<div class='redB'>配件ID  ($Ids) 禁用原因添加失败!$InSql</br></div>";
	             $OperationResult="N";
	           }
	     $fromWebPage=$funFrom."_forbidden";
     break;
	case 133://取消禁用
     $Log_Funtion="取消禁用配件";
	$Date=date("Y-m-d");
	$Delstuff="DELETE FROM $DataIn.stuffdisablenot WHERE StuffId  IN SELECT StuffId FROM $upDataSheet  WHERE Id IN ($Id)";
	$DelResult=@mysql_query($Delstuff);
	$InSql="INSERT INTO $DataIn.stuffdisablenot SELECT NULL,StuffId,'$Date','$Operator','1','0','0','$Operator','$DateTime',null,null FROM $upDataSheet WHERE Id IN ($Id)";
	$InRecode=mysql_query($InSql);
	if($InRecode){
			$Log.="配件ID  ($Id) 取消禁用成功</br>";
	      }
	 else {
		  $Log.="<div class='redB'>配件ID  ($Id) 取消禁用失败!$InSql</br></div>";
	      $OperationResult="N";
	      }
break;

	case 17://审核通过
		switch($From){
		case "m":
			$Log_Funtion="审核名称";
			$SetStr="Estate=1";
			include "../model/subprogram/updated_model_3d.php";
			$fromWebPage=$funFrom."_m";
			$upSql = "UPDATE $DataIn.stuffchange C SET C.Estate=0 WHERE NOT EXISTS (SELECT StuffId FROM $DataIn.stuffdata S WHERE C.StuffId=S.StuffId AND S.Estate=2)";
			$upResult = mysql_query($upSql);
			break;
		default:
			$Log_Funtion="审核图片";
			$SetStr="Picture=1";
			include "../model/subprogram/updated_model_3F.php";  //无法对PDF加水印，add by zx 20100904
			$fromWebPage=$funFrom."_img";
			break;
			}
		break;
	case 162:
	{
		$Log_Funtion="审核名称";
		$fromWebPage=$funFrom."_m";
		//先改变配件状态
		$updateStuffStateSql = "update $DataIn.stuffdata set Estate = '3' Where Id = '$Id'";
		mysql_query($updateStuffStateSql);
		//再作废更改记录
		$upSql = "UPDATE $DataIn.stuffchange C SET C.Estate=0 WHERE NOT EXISTS (SELECT StuffId FROM $DataIn.stuffdata S WHERE C.StuffId=S.StuffId AND S.Estate=2)";
		$upResult = mysql_query($upSql);
		//写入退回原因

		$returnReasonSql = "Insert Into $DataIn.returnreason (Id, tableId, targetTable, Reason, DateTime) Values (NULL, '$Id', '$DataIn.stuffdata','$ReturnReasons', '$DateTime')";
		mysql_query($returnReasonSql);
	}
	break;
	case 40://配件图片上传
	    $UpFileSign="";
		if ($donwloadFileIP!="") {  //有IP则走远程审核
			$Log_Funtion="PDF远程(FTP)图片上传";
			$Log.="StuffId号为 $StuffId 的文件更新： $FileStatus1 ";
			if ($DataStatus1<1) {
				if ( $DataStatus1==0) {  //如果远程更新失败，可在这写入数据库，看情况吧 。
					$Log.=" 数据状态更新失败: $DataStatus1 </br>";
				}
				else {
					$Log.=" 数据状态更新失败: $DataStatus1 </br>";
				}
			}
			else {
				$Log.=" 数据状态更新成功: $DataStatus1 </br>";
				$UpFileSign="Stuffpdf";

			}
			$OperationResult="N";

			}
		else {

				$Log_Funtion="PDF图片上传";
				$Date=date("Y-m-d");
				$SetStr="";
				if ($upPicture!=""){
					$OldFile=$upPicture;
					$PreFileName=$StuffId.".pdf";
					$uploadInfo=$PreFileName;
					$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
					if($uploadInfo!=""){
										 $SetStr="Picture=1";   //上传成功,2审核，1为已审核，图片以后不用审核 2013-05-23 ewen
										 $UpFileSign="Stuffpdf";
										 //生成JPG图片
										 $pdfFile=$FilePath . $PreFileName;
										 $jpgFile=$FilePath . $StuffId . "_s.jpg";
										  exec("$execImageMagick -colorspace sRGB -transparent white -trim $pdfFile $jpgFile");
								/*		 $nmw =NewMagickWand();
										 $pdfFile=$FilePath . $PreFileName;
										 $jpgFile=$FilePath . $StuffId . "_s.jpg";
										 MagickReadImage($nmw,$pdfFile);
										 MagickWriteImage($nmw,$jpgFile);
										 DestroyMagickWand($nmw);*/
									   }

								   else{
										  $SetStr="Picture=0";
									   }

					if($SetStr!="")
								  {
									 $sql = "UPDATE $upDataSheet SET $SetStr WHERE   StuffId=$StuffId";
									 //echo $sql;
									 $result = mysql_query($sql);
									 if($result){
												   $Log="StuffId号在 $StuffId 的记录成功 $Log_Funtion.</br>";
												}
											else{
												   $Log="StuffId号为 $StuffId 的记录$Log_Funtion 失败! $sql</br>";
												   $OperationResult="N";
												}
								   }

				}
				else{ //未选择文件保存，删除原有上传文件
					$pdfFile="../download/stufffile/".$StuffId.".pdf";
					if(file_exists($pdfFile)){
					   unlink($pdfFile);
					  $sql = "UPDATE $upDataSheet SET Picture=0 WHERE  StuffId=$StuffId";
						//echo $sql;
					  $result = mysql_query($sql);
					   if($result){
							 $Log="StuffId号为 $StuffId 的图档删除成功.</br>";
						 }
						 else{
							$Log="StuffId号为 $StuffId 图档删除失败! $sql</br>";
							$OperationResult="N";
					   }
				  }
				}

		}

		//App展示小图
	   if($AppPicture!=""){
	       $AppFilePath="../download/stuffIcon/";
		   if(!file_exists($AppFilePath)){
			      makedir($AppFilePath);
			}

			$oldFiles=$AppPicture;
			//$FileType=".jpg";
			$FileType=substr("$AppPicture_name", -4, 4);
			$NewFilesPicture=$StuffId.$FileType;
			$upFileInfo=UploadFiles($oldFiles,$NewFilesPicture,$AppFilePath);

			if($upFileInfo!=""){
					$Log.="产品 $ProductId 的APP展示图上传成功.<br>";
			}
		else{
			$Log.="<div class='redB'>产品 $ProductId 的APP展示图上传失败.</div><br>";
			}
		}


		if ($UpFileSign!=""){
			$Record_Id=$StuffId;
		    include "../model/subprogram/upload_records.php";
		}
		break;
	case 73:
		if ($donwloadFileIP!="") {  //有IP则走远程审核
			$Lens=count($checkid);
			$R_IdStr=""; //把Id送到远程处理
			for($i=0;$i<$Lens;$i++){
				$Id=$checkid[$i];
				$R_IdStr.="$Id|";
			}
			if($R_IdStr!="") {
				//echo "$donwloadFileIP";
				//echo "R_IdStr:$R_IdStr";
				$url="$donwloadFileIP/remoteDloadFile/R_UpDateFiles.php?Login_P_Number=$Login_P_Number&UpFileSign=stuff&ActionId=$ActionId&R_IdStr=$R_IdStr";
				//echo "$url";
				$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//注意：要将地址转为GB2312，否则读取失败
				//$content= str_replace("\"","'",$str);
				$content=$str;
				$start="^";
				$strP=strpos($content,$start);
				$tempStr=substr($content,$strP+1);
				$Log=$tempStr;
				//echo "处理成功的图档:$tempStr";
				$OperationResult="N";
			}

		}
		else {
			$Date=date("Y-m-d H:i:s");
			$Log_Funtion="图档审核通过";	$SetStr="Gstate=1,GfileDate='$Date'";
			include "../model/subprogram/updated_model_3G.php";
		}


		$fromWebPage=$funFrom."_Gfile";	break;


	case 98://更新最低库存
		//写入最低库存
		////$LockSql=" LOCK TABLES $DataIn.cg1_stocksheet WRITE,$DataIn.ck9_stocksheet WRITE";$LockRes=@mysql_query($LockSql);
		//计算是否需要特采
		$Log_Funtion="更新最低库存";

		$checkRow=mysql_fetch_array(mysql_query("SELECT oStockQty FROM $DataIn.ck9_stocksheet WHERE  StuffId='$StuffId'",$link_id));
		$oStockQty=$checkRow["oStockQty"];//原订单数量
		$tcQty=$mStockQty-$oStockQty;//需特采数量,如果这个数量大于0，则需要再购买配件方向满足最低库存数量
		if($tcQty>0){//需生成特采
		     $MyPDOEnabled=1;
             include "../basic/parameter.inc";

		     $myResult=$myPDO->query("CALL proc_cg1_stocksheet_add('',$StuffId,$tcQty,'',1,$Operator);");
	         $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
	         $OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:$OperationResult;

	         $Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>";
	         $Log.="</br>";
	         $myResult=null; $myRow=null;
		}

		if ($OperationResult=="Y"){
			$upSql = "UPDATE $DataIn.ck9_stocksheet	SET mStockQty='$mStockQty' WHERE StuffId='$StuffId'";
			$upResult = mysql_query($upSql);
			if($upResult){
				$Log.="配件 $StuffId 的最低库存更新成功.<br>";
				}
			else{
				$Log.="<div class='redB'>配件 $StuffId 的最低库存更新失败.</div>";
			}
		}
		break;
   case 151:
      $addRecodes = "Replace Into $DataIn.stuffovertime (Id,StuffId,OverTime,Estate,Locks,Date,Operator ) Values (NULL, '$StuffId',  '$OverTime', '1', '0', '$Date', '$Operator')";
      $inRres=@mysql_query($addRecodes);
	  if($inRres){
		$Log.="配件 $StuffId 设置延长期限成功.<br>";
		}
	else{
		$Log.="<div class='redB'>配件 $StuffId设置 延长期限失败.</div>";
		}
      $fromWebPage=$funFrom."_forbidden";
      break;
    case "develop":
        $checkResult=mysql_query("SELECT * FROM $DataIn.stuffdevelop WHERE  StuffId='$StuffId' LIMIT 1",$link_id);
        if (mysql_num_rows($checkResult)>0){//,Targetdate='$Targetdate'
            $upStr=$developEstate==1?",Estate='1',Type=0 ":"";
	        $addRecodes="UPDATE $DataIn.stuffdevelop SET CompanyId='$ClientCompanyId',GroupId='$GroupId',Number='$DevelopNumber',Remark='$Remark' $upStr WHERE StuffId='$StuffId' ";
        }
        else{
			$addRecodes="INSERT INTO $DataIn.stuffdevelop (Id,StuffId,GroupId,Number,Targetdate,Finishdate,KfRemark,Remark,dFile,ReturnReasons,Estate,Date,Operator) 
			VALUES (NULL, '$StuffId','$GroupId', '$DevelopNumber', '0000-00-00','0000-00-00 00:00:00','','$Remark','','','2','$Date', '$Operator')";
	        $add_DevelopSign=1;
        }
      //echo $addRecodes;
     $inRres=@mysql_query($addRecodes);
	  if($inRres){
		$Log.="配件 $StuffId 设置开发信息成功.<br>";

			$developFilePath="../download/Stuffdevelopfile/";  // add by zx 2014-10-15
			if(!file_exists($developFilePath)){
				   makedir($developFilePath);
			   }
		 if($developfile!=""){
				  $FType=substr("$developfile_name", -4, 4);
				  $Ohycfile=$developfile;
				  $PreFileName=$StuffId.$FType;
				  $Attached=UploadFiles($Ohycfile,$PreFileName,$developFilePath);
			  if($Attached!=""){
				   $inRecode="UPDATE $DataIn.stuffdevelop SET dFile='$PreFileName' WHERE StuffId='$StuffId'";
				   $inAction=@mysql_query($inRecode);
				   if($inAction){
					  $Log.="$StuffId 开发文存档成功!<br>";
					   }
				   else{
					  $Log.="<div class=redB>开发文件存档失败! $inRecode </div><br>";
					  $OperationResult="N";
					  }
					  $Log.="ID为 $StuffId 的开发文件上传上传成功<br>";
				   }
			  else{
					 $Log.="<div class='redB'>ID为 $StuffId 的开发文件上传失败</div><br>";
					 $OperationResult="N";
				  }
			   }
		  else{
			   if($delFile!=""){//已选取删除原文件
			   		$inRecode="UPDATE $DataIn.stuffdevelop SET dFile='' WHERE StuffId='$StuffId'";
					$inAction=@mysql_query($inRecode); //把文件置空，但不册除具体文件
					$Log.="<div class='redB'>ID为 $StuffId 的开发文件已清除！</div><br>";
			   }
			   $Log.="<div class='redB'>ID为 $StuffId 的开发文件没有上传！</div><br>";
			 }
		}
	else{
		$Log.="<div class='redB'>配件 $StuffId 设置开发信息失败. $addRecodes </div>";
		}

		if ($add_DevelopSign==1){
			//新增推送信息
			     include "../iphoneAPI/subpush/develop_push.php";
		}
      break;

	default:
	   if($comSubSign==1){//子配件只更新名字。。。。

			$checkproperty=mysql_query("SELECT Id FROM $DataIn.stuffproperty WHERE StuffId='$StuffId' AND Property=10",$link_id);
			if(!$checkpropertyROW=mysql_fetch_array($checkproperty)){
	            $inSql3="INSERT INTO $DataIn.stuffproperty(Id,StuffId,Property)VALUES(NULL,'$StuffId','10')";
	            $inRes3=@mysql_query($inSql3);
				}

			$SetStr="StuffCname='$StuffCname',Weight='$Weight'";
			include "../model/subprogram/updated_model_3a.php";
		}else{
	        echo "$comSign:".$_POST["comSign"];
	        $StuffCname=trim($StuffCname);
	        $oldStuffCname=trim($oldStuffCname);
			$Spec=FormatSTR($Spec);
			$Remark=FormatSTR($Remark);
			$Date=date("Y-m-d");
			$GCField=explode("|",$GcheckNumber);
			$GCjobid=$GCField[0];
			$GcheckNumber=$GCField[1];
		     //配件属性
		     $tempCount=count($Property);
		     $DelSql="DELETE FROM $DataIn.stuffproperty WHERE StuffId IN (SELECT StuffId FROM $DataIn.stuffdata WHERE Id=$Id)";
		     $DelResult=@mysql_query($DelSql);
		     $mSign =0 ;
		       for($k=0;$k<$tempCount;$k++){
		            if($Property[$k]>0){
		                  if($Property[$k]==9)$mSign=1;
		                   $inSql3="INSERT INTO $DataIn.stuffproperty(Id,StuffId,Property)VALUES(NULL,'$StuffId','$Property[$k]')";
		                   $inRes3=@mysql_query($inSql3);
		                   }
		          }
				if ($oldStuffCname!=$StuffCname || $oldPrice!=$Price ||  $oldCompanyId!=$CompanyId ||  $oldSeatId!=$SeatId){
				      $Reason_Sql ="INSERT INTO $DataIn.stuffchange (Id,StuffId,oldStuffCname,StuffCname,oldPrice,Price,oldCompanyId,CompanyId,Reason,Date,Operator,oldSeatId,SeatId) VALUES (NULL,'$StuffId','$oldStuffCname','$StuffCname','$oldPrice','$Price','$oldCompanyId','$CompanyId','$Reason','$Date','$Operator','$oldSeatId','$SeatId')";
				      $Reason_Result = mysql_query($Reason_Sql);
				}

				$SetStr1="";$SetStr2="";
				if ($oldStuffCname!=$StuffCname || $oldPrice!=$Price ||  $oldTypeId!=$TypeId ||  $oldSpec!=$Spec || $oldjhDays!=$jhDays ||  $oldWeight!=$Weight ||  $oldUnit!=$Unit ||  $oldBoxPcs!=$BoxPcs){
				      $SetStr1=",Estate='2' ";

				      if ($oldTypeId!=$TypeId){
							$checkResult=mysql_fetch_array(mysql_query("SELECT T.BuyerId,T.DevelopGroupId,T.DevelopNumber,T.Position,M.CheckSign  
							FROM $DataIn.StuffType T 
							LEFT JOIN $DataIn.base_mposition M ON M.Id=T.Position 
							WHERE T.TypeId='$TypeId' LIMIT 1",$link_id));
							$DevelopGroupId=$checkResult["DevelopGroupId"];
							$DevelopNumber=$checkResult["DevelopNumber"];
							//$SendFloor=$checkResult["Position"];
							//$CheckSign=$checkResult["CheckSign"];
                           $SetStr1=",Pjobid='$DevelopGroupId',PicNumber='$DevelopNumber' ";
				      }
				}
			$CostPriceStr = "";
			if($mainType != 7){
				//计算配件成本价，采购价格/(1+增值税率)
				$checkTaxRow = mysql_fetch_array(mysql_query("SELECT T.Value FROM $DataIn.providersheet P 
				LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
				WHERE P.CompanyId = '$CompanyId'",$link_id));
				$AddValue = $checkTaxRow["Value"];
				$AddValue=$AddValue==""?0:$AddValue;
				$CostPrice = sprintf("%.4f", $Price/(1+$AddValue));
				$CostPriceStr =",CostPrice='$CostPrice'";
			 }
			 if($Price>0){
				 $PriceDetermined = 0;
			 }

				$SetStr="StuffCname='$StuffCname',StuffEname='$StuffEname',TypeId='$TypeId',
				Spec='$Spec',SendFloor='$SendFloor',CheckSign='$CheckSign',Weight='$Weight',
				Price='$Price',Unit='$Unit',PriceDetermined ='$PriceDetermined',
				SeatId='$SeatId',
                BoxPcs='$BoxPcs',Remark='$Remark',ForcePicSpe='$ForcePicSpe',Date='$Date',	
                DevelopState='$DevelopState',GcheckNumber='$GcheckNumber',Operator='$Operator',Locks='0',
                OPdatetime='$DateTime' $SetStr1 $CostPriceStr";

				include "../model/subprogram/updated_model_3a.php";
				//更新或新增
				$checkBPS=mysql_query("SELECT Id FROM $DataIn.bps WHERE StuffId='$StuffId'",$link_id);
				if($checkROW=mysql_fetch_array($checkBPS)){
					$Relation_Sql = "UPDATE $DataIn.bps SET CompanyId='$CompanyId',BuyerId='$BuyerId' WHERE StuffId='$StuffId'";
					}
				else{
					$Relation_Sql ="INSERT INTO $DataIn.bps (Id,StuffId,BuyerId,CompanyId,Locks) VALUES (NULL,'$StuffId','$BuyerId','$CompanyId','0')";
					}
				$Relation_Result = mysql_query($Relation_Sql);
				if($Relation_Result){
					$Log.="<br>&nbsp;&nbsp;&nbsp;&nbsp;配件采购供应商关系更新成功!!  ";
					}
				else{
					$Log.="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;配件采购供应商关系更新失败!!</div>";
					$OperationResult="N";
					}


			     if ($DevelopState==1 && $oldTypeId!=$TypeId){
					   $checkTypeResult= mysql_fetch_array(mysql_query("SELECT G.GroupId  AS DevelopGroupId,T.DevelopNumber
				        FROM  $DataIn.StuffType T  
				        LEFT JOIN $DataIn.staffgroup G ON G.Id=T.DevelopGroupId
			            WHERE T.TypeId='$TypeId' LIMIT 1",$link_id));
				        $DevelopNumber=$checkTypeResult["DevelopNumber"];
				        $GroupId=$checkTypeResult["DevelopGroupId"];

				       $checkDevelopResult=mysql_query("SELECT Id FROM $DataIn.stuffdevelop WHERE StuffId='$StuffId'",$link_id);
				       if (mysql_num_rows($checkDevelopResult)>0){
					       $Develop_Sql = "UPDATE $DataIn.stuffdevelop SET GroupId='$GroupId',Number='$DevelopNumber' WHERE StuffId='$StuffId'";
				       }
				       else{
					       $Develop_Sql = "INSERT INTO $DataIn.stuffdevelop (Id,StuffId,GroupId,Number,Targetdate,Finishdate,CompanyId,KfRemark,Remark,dFile,ReturnReasons,Estate,Date,Operator) VALUES (NULL, '$StuffId',  '$GroupId', '$DevelopNumber', '0000-00-00','0000-00-00 00:00:00','0','','','','','1','$Date', '$Operator')";
				       }
				       $Develop_Result = mysql_query($Develop_Sql);
		         }

		            // 如果是母配件，则要更新子配件的相关信息(供应商，采购等信息)
		         if($mSign==1){
		               include "stuffdata_updated_sub.php";
		          }

		          //更新含该配件的半成品价格
		          if ($oldPrice!=$Price){
			           include "stuffdata_updated_costprice.php";
		          }
            }
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&StuffType=$StuffType";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
  ?>