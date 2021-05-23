<?php 
//电信-ZX
//步骤1 $DataIn.errorcasedata 二合一已更新
include "../model/modelhead.php";
include "../basic/downloadFileIP.php";  //取得下载文档的远程


$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="出错案例";		//需处理
$upDataSheet="$DataIn.errorcasedata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 17://审核通过
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
					/*$waterimg = "../images/auditing.png";
					$wFile ="../download/errorcase/".$Picture;
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
					imagedestroy($im2);*/
	/////////////////////////////////////////////
					//if($r){
						//更新记录
						$sql = "UPDATE $upDataSheet SET $SetStr WHERE Id=$Id";
						$result = mysql_query($sql);
						if($result){
							$Log="<p>ID为 $Id 的出错案例审核成功. <a href='$wFile' target='_black'>查看图档</a>";
							}
						else{
							$Log.="<div class='redB'>ID为 $Id 的出错案例审核失败.</div>";
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
		break;
	case 82:
		$Log_Funtion="产品连接";
		if($uType!=""){
			$TypeIdSTR="and  TypeId='$uType'";
			$Remark="分类ID为 $uType 的";}
		else{
			$TypeIdSTR="";}
		if($_POST['ListId']){//如果指定了操作对象
			$Counts=count($_POST['ListId']);
			$Ids="";
			for($i=0;$i<$Counts;$i++){
				$thisId=$_POST[ListId][$i];
				$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
				}
			$TypeIdSTR="and ProductId IN ($Ids)";
			$delproduct=mysql_query("DELETE FROM $DataIn.casetoproduct WHERE cId='$Id'",$link_id);
		    $inRecode="INSERT INTO $DataIn.casetoproduct SELECT NULL,ProductId,'$Id' FROM $DataIn.productdata WHERE 1 $TypeIdSTR";
		    $inResult=@mysql_query($inRecode);
			}
			
		 //相关配件读入casetostuff表中
		 if($_POST['stuffId']){
		    $Count2=count($_POST['stuffId']);
		    $Id2="";
		    for($j=0;$j<$Count2;$j++){
		        $thisId=$_POST[stuffId][$j];
		        $Id2=$Id2==""?$thisId:$Id2.",".$thisId;
		        }
		     $TypeIdSTR2="and StuffId IN ($Id2)";
			 $delstuff=  mysql_query("DELETE FROM $DataIn.casetostuff WHERE cId='$Id'",$link_id);
		     $stuffRecode="INSERT INTO $DataIn.casetostuff SELECT NULL,StuffId,'$Id' FROM $DataIn.stuffdata WHERE 1 $TypeIdSTR2";
		     $stuffResult=@mysql_query($stuffRecode);
		   } 
		if($inResult||$stuffResult){
			$Log.="&nbsp;&nbsp;产品连接成功! </br>";
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;产品连接失败!</div></br>";
			$OperationResult="N";
			}
		break;
	default:
		if($Attached!=""){//有上传文件
			$FileType=substr("$Attached_name", -4, 4);
			$OldFile=$Attached;
			$FilePath="../download/errorcase/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			if($oldAttached!=""){
				$PreFileName=$oldAttached;
				}
			else{
				$datelist=newGetDateSTR();
				$PreFileName=$datelist.$FileType;
				}
			$upAttached=UploadPictures($OldFile,$PreFileName,$FilePath);
			if($upAttached!=""){
				$AttachedSTR=",Picture='$PreFileName',Estate=2";
				}
			}
		$Date=date("Y-m-d");
		$Caption=FormatSTR($Caption);
		$Owner=FormatSTR($Owner);
		$SetStr="Type='$Type',Title='$Title',Owner='$Owner',Date='$Date',Operator='$Operator',Locks='0' $AttachedSTR";
		include "../model/subprogram/updated_model_3a.php";
		
		
//================================================上传QC标准图原文件
	 /*$FindResult=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.productdata 
	 WHERE ProductId='$ProductId'",$link_id));*/
	if ($donwloadFileIP!="") {  //有IP则走远程审核
		//$Log_Funtion="PDF远程(FTP)图片上传";
		$Log.="出错案例原件 Id 号为Type | $Title  的文件更新： $FileStatus2 文件名：$PreFileName2 "; 
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
		$ProductType=$Type;
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
	           (NULL,'5','$FileRemark','$PreFileName','$CompanyId',
		       '$ProductType','1','0','$Date','$Operator')";
		       $inAction=@mysql_query($inRecode);
		       if($inAction){ 
			       $Log.="出错案例原件图存档成功!<br>";
			       } 
		       else{
			       $Log=$Log."<div class=redB>出错案例原件图存档失败! $inRecode </div><br>";
			       $OperationResult="N";
			      }
				  $Log.="ID为 $ProductType 的出错案例原件图原件上传成功<br>";
		       }
	      else{
		         $Log="<div class='redB'>ID为 $ProductType 的出错案例原件图上传失败</div><br>";
		         $OperationResult="N";
		      }
		   }
	  else{
		   $Log="<div class='redB'>ID为 $ProductType 的出错案例原件图没有上传</div><br>";
		  }	
	}	
	
		
		
		
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
