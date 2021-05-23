<?php 
//电信-ZX  2012-08-01
//步骤1 $DataIn.errorcasedata 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="REACH法规图";		//需处理
$upDataSheet="$DataIn.stuffreach";	//需处理
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
		$Log_Funtion="锁定";	$SetStr="Locks=0";include "../model/subprogram/updated_model_3d.php";		   
		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";include "../model/subprogram/updated_model_3d.php";		   
	    break;
	case 82:
		$Log_Funtion="配件连接";
		$Date=date("Y-m-d");
		if($_POST['ListId']){//如果指定了操作对象
			$Counts=count($_POST['ListId']);
			$Ids="";
			for($i=0;$i<$Counts;$i++){
				$thisId=$_POST[ListId][$i];
				$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
				}
			$TypeIdSTR="and StuffId IN ($Ids)";
			//将productqcimg表中以前上传的记录，删除。
			 $delSql="delete from $DataIn.stuffreachlink where QcId='$Id'";
			 $delResult=mysql_query($delSql);
		     $inRecode="INSERT INTO $DataIn.stuffreachlink SELECT NULL,StuffId,'$Id','$Date','$Operator' FROM $DataIn.Stuffdata WHERE 1 $TypeIdSTR";
		    $inResult=@mysql_query($inRecode);
		    if($inResult){
			$Log.="$Ids&nbsp;&nbsp;配件连接成功! </br>";
		      	}
		    else{
			    $Log.="<div class='redB'>&nbsp;&nbsp;配件连接失败!</div></br>";
			    $OperationResult="N";
			  }
		}
		break;
	default:
		if($Attached!=""){//有上传文件
			$FileType=substr("$Attached_name", -4, 4);
			$OldFile=$Attached;
			$FilePath="../download/stuffreach/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			if($oldAttached!=""){
				$PreFileName=$oldAttached;
				}
			else{
			   $PreFileName="R".$TypeId. $FileType;
	           $ImageFile=$FilePath . $PreFileName;
	           if (file_exists($ImageFile)){
	              $n=0;
	             do{
		        	$n+=1;
		       	    $PreFileName="R".$TypeId. "_" . $n . $FileType;
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
			    $delSql="delete from $DataIn.qcstandardimg where QcId='$Id'";
			    $delResult=mysql_query($delSql);
			}
	   }else{
	         $Log=$Log."<div class=redB>&nbsp;&nbsp;ID号为 $Id 的 $ItemName $Log_Item 资料 $Log_Funtion 失败! $updateSQL </div><br>";
	       $OperationResult="N";
	   }
		//include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
