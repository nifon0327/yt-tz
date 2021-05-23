<?php 
//电信-EWEN
include "../model/modelhead.php";
include "../basic/downloadFileIP.php";  //取得下载文档的远程
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="QC标准图";		//需处理
$upDataSheet="$DataIn.qcstandarddata";	//需处理
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
	/*case 17://审核通过
		$Log_Funtion="审核";	$SetStr="Estate=1";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				//提取文件名
				$CheckFile=mysql_query("SELECT Picture FROM $upDataSheet WHERE Id='$Id' LIMIT 1",$link_id);
				if($CheckFileRow=mysql_fetch_array($CheckFile)){
					$Picture=$CheckFileRow["Picture"];
					//打开文件
					$waterimg = "../images/auditing.png";
					$wFile ="../download/productqcimg/".$Picture;
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
					$r =imagegif($im,$wFile);//输出图片
					imagedestroy($im);
					imagedestroy($im2);
	/////////////////////////////////////////////
					//if($r){
						//更新记录
						$sql = "UPDATE $upDataSheet SET $SetStr WHERE Id=$Id";
						$result = mysql_query($sql);
						if($result){
							$Log="<p>ID为 $Id 的QC检验标准图审核成功. <a href='$wFile' target='_black'>查看图档</a>";
							}
						else{
							$Log.="<div class='redB'>ID为 $Id 的QC检验标准图审核失败.</div>";
							$OperationResult="N";
							}
						//}
					//else{
						//$Log.="<div class='redB'>ID为 $Id 的出错案例水印生成失败.</div>";
						//$OperationResult="N";
						//}
					}//找到文件
				}//end if($Id!="")
			}//end for
		$fromWebPage=$funFrom."_m";
		break;*/
	case 82:
		$Log_Funtion="产品连接";
		$Date=date("Y-m-d");
		if($_POST['ListId']){//如果指定了操作对象
			$Counts=count($_POST['ListId']);
			$Ids="";
			for($i=0;$i<$Counts;$i++){
				$thisId=$_POST[ListId][$i];
				$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
				}
			$TypeIdSTR="and ProductId IN ($Ids)";
			//将productqcimg表中以前上传的记录，删除。
			 $delSql="delete from qcstandardimg where QcId='$Id'";
			 $delResult=mysql_query($delSql);
		     $inRecode="INSERT INTO $DataIn.qcstandardimg SELECT NULL,ProductId,'$Id','$Date','$Operator',1,0,0,'$Operator',NOW(),'$Operator',NOW() FROM $DataIn.productdata WHERE 1 $TypeIdSTR";
		    $inResult=@mysql_query($inRecode);
		    if($inResult){
			$Log.="$Ids&nbsp;&nbsp;产品连接成功! </br>";
		      	}
		    else{
			    $Log.="<div class='redB'>&nbsp;&nbsp;产品连接失败!</div></br>";
			    $OperationResult="N";
			  }
		}
		break;
	default:
		if($Attached!=""){//有上传文件
			$FileType=substr("$Attached_name", -4, 4);
			$OldFile=$Attached;
			$FilePath="../download/QCstandard/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			if($oldAttached!=""){
				$PreFileName=$oldAttached;
				}
			else{
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
			}
			$upAttached=UploadPictures($OldFile,$PreFileName,$FilePath);
			if($upAttached!=""){
				$AttachedSTR=",Picture='$PreFileName'";
				//$Estate=2;
				}
			}
		if ($IsType){$IsType=1;}else{$IsType=0;}
		$Date=date("Y-m-d");
		$SetStr="TypeId='$TypeId',Title='$Title',Estate='$Estate',IsType='$IsType',Date='$Date',Operator='$Operator',Locks='0' $AttachedSTR";
	     $updateSQL = "UPDATE $upDataSheet SET $SetStr WHERE $upDataSheet.Id='$Id' $OtherWhere";
         $updateResult = mysql_query($updateSQL);
       if ($updateResult && mysql_affected_rows()>0){
	        $Log=$Log."&nbsp;&nbsp;ID号为 $Id 的 $ItemName $Log_Item 资料 $Log_Funtion 成功!<br>";
			if ($IsType==1){
			//删除productqcimg表中以前的连接记录。
			    $delSql="delete from qcstandardimg where QcId='$Id'";
			    $delResult=mysql_query($delSql);
			}
	   }else{
	         $Log=$Log."<div class=redB>&nbsp;&nbsp;ID号为 $Id 的 $ItemName $Log_Item 资料 $Log_Funtion 失败! $updateSQL </div><br>";
	       $OperationResult="N";
	   }
		//include "../model/subprogram/updated_model_3a.php";
		
//================================================上传QC标准图原文件
	 /*$FindResult=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.productdata 
	 WHERE ProductId='$ProductId'",$link_id));*/
	 
		if ($donwloadFileIP!="") {  //有IP则走远程审核
			//$Log_Funtion="QCPDF远程(FTP)图片上传";
			$Log.="品QC标准图原件 Id 号为TypeId | $Title  的文件更新： $FileStatus2 文件名：$PreFileName2 "; 
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
				  $deldrawing=mysql_query("DELETE FROM $DataIn.doc_standarddrawing WHERE FileRemark='$FileRemark'",$link_id);
				  $FType=substr("$originalPicture_name", -4, 4);
				  $Ohycfile=$originalPicture;
				  $datelist=newGetDateSTR();
				  $PreFileName=$datelist.$FType;
				  $Attached=UploadFiles($Ohycfile,$PreFileName,$originalFilePath);
				   $inRecode="INSERT INTO $DataIn.doc_standarddrawing(Id,FileType,FileRemark,
				   FileName,CompanyId,ProductType,Estate,Locks,Date,Operator) VALUES 
				   (NULL,'2','$FileRemark','$PreFileName','$CompanyId',
				   '$ProductType','1','0','$Date','$Operator')";
				   $inAction=@mysql_query($inRecode);
				   if($inAction){ 
					  $Log.="产品QC标准图原件存档成功!<br>";
					   } 
				   else{
					  $Log=$Log."<div class=redB>产品QC标准图原件存档失败! $inRecode </div><br>";
					  $OperationResult="N";
					  }
				   }
			  else{
					$Log="<div class='redB'>ID为 $ProductId 的产品QC标准图原件上传失败</div><br>";
					$OperationResult="N";
				  }
		}
			  
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
