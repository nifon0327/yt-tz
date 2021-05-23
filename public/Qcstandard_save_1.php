<?php 
//电信-EWEN
include "../model/modelhead.php";
include "../basic/downloadFileIP.php";  //取得下载文档的远程
//步骤2：
$Log_Item="QC标准图";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

$Title=FormatSTR($Title);
$Date=date("Y-m-d");
if($Attached!="" && ($originalPicture!="" || $FileStatus2="CopyOK")){//有上传文件 原件一起上传
	$FileType=substr("$Attached_name", -4, 4);
	$OldFile=$Attached;
	$FilePath="../download/QCstandard/";
	if(!file_exists($FilePath)){
		makedir($FilePath);
		}
	$PreFileName="T".$TypeId. $FileType;
	$ImageFile=$FilePath . $PreFileName;
	if (file_exists($ImageFile)){
	    $n=0;
	   do{
			$n+=1;
			$PreFileName="T".$TypeId. "_" . $n . $FileType;
		    $ImageFile=$FilePath . $PreFileName;
		}while(file_exists($ImageFile));
	}
	$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
	if ($Attached!=""){		
		$Log="附件上传成功.<br>";
		}
	else{
		$Log="<div class='redB'>附件上传失败！可能没有上传源文件</div><br>";
		$OperationResult="N";
		}
	if($OperationResult=="Y"){
		if ($IsType){$IsType=1;}else{$IsType=0;}
		$Caption=FormatSTR($Caption);
		$Date=date("Y-m-d");
		$IN_recode="INSERT INTO $DataIn.qcstandarddata (Id,TypeId,Title,Picture,IsType,Estate,Date,Locks,Operator) VALUES (NULL,'$TypeId','$Title','$PreFileName','$IsType','1','$Date','0','$Operator')";
		$res=@mysql_query($IN_recode);
		if($res){
			$Log="$TitleSTR 成功. <br>";
			}
		else{
			$Log="<div class='redB'>$TitleSTR 失败. $IN_recode </div><br>";
			}
		}
	}
else{
	$Log="<div class='redB'>未选择上传的附件.</div>";
	$OperationResult="N";
	}
//================================================上传QC标准图原文件
	 /*$FindResult=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.productdata 
	 WHERE ProductId='$ProductId'",$link_id));*/
	if ($donwloadFileIP!="") {  //有IP则走远程审核
		//$Log_Funtion="PDF远程(FTP)图片上传";
		$Log.="产品QC标准图原件 Id 号为TypeId | $Title  的文件更新： $FileStatus2 文件名：$PreFileName2 "; 
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
	    $CompanyId="1047";
		$ProductType=$TypeId;
		$FileRemark=$Title;
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
		       $inRecode="INSERT INTO $DataIn.doc_standarddrawing (Id,FileType,FileRemark,
			   FileName,CompanyId,ProductType,Estate,Locks,Date,Operator) VALUES 
	           (NULL,'2','$FileRemark','$PreFileName','$CompanyId',
		       '$ProductType','1','0','$Date','$Operator')";
		       $inAction=@mysql_query($inRecode);
		       if($inAction){ 
			       $Log.="产品QC标准图存档成功!<br>";
			       } 
		       else{
			       $Log=$Log."<div class=redB>产品QC标准图存档失败! $inRecode </div><br>";
			       $OperationResult="N";
			      }
				  $Log.="ID为 $ProductType 的产品QC标准图原件上传成功<br>";
		       }
	      else{
		         $Log="<div class='redB'>ID为 $ProductType 的产品QC标准图原件上传失败</div><br>";
		         $OperationResult="N";
		      }
		   }
	  else{
		   $Log="<div class='redB'>ID为 $ProductType 的产品QC标准图原件没有上传</div><br>";
		  }	
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
