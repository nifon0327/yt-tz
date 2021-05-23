<?php 
//电信---yang 20120801
include "../model/modelhead.php";
//$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="模具费用返回";		
$upDataSheet="$DataIn.cw16_modelfee";	
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
$x=1;
switch($ActionId){
   case 14:
          $Lens=count($checkid);
          for($i=0,$j=1;$i<$Lens;$i++,$j++){
                   $Id=$checkid[$i];
	               $Moqstr="Moq".$j;
	               $cgstuffQtystr="cgstuffQty".$j;
	               if($$Moqstr>$$cgstuffQtystr){
                             $Log.="<div class='redB'>已采购的配件数未达到最低配件数,请款失败!</div>";
		                     $OperationResult="N";	
	                         }
	               else{
		                       $updateSql="UPDATE  $DataIn.cw16_modelfee SET Estate=2,Date='$Date'  WHERE Id='$Id'";
	                           $updateReuslt=mysql_query($updateSql);
	                           if($updateReuslt&& mysql_affected_rows()>0){
		                                      $Log.="$Id 记录请款成功. <br>";
	                                           }
	                            else{
		                                     $Log.="<div class='redB'>$Id 记录请款失败</div>  $updateSql . <br>";
		                                     $OperationResult="N";		
	                                       }
	                         }
	          }
      break;
     case 17:
		$Log_Funtion="审核";	$SetStr="Estate=3,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
      case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="退回";
		if($fromWebPage==$funFrom."_m"){	//审核退回
			$Log_Funtion="审核退回";			$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";			
			}
       else{							//财务退回
			    if($Estate==3){					//未结付退回
				     $Log_Funtion="未结付退回";		$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";
				   }	
			}
			break;
   case 18:
            $fromWebPage=$funFrom."_cw";
          $Log_Funtion="结付";	$SetStr="BankId=$BankId,Estate=0,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
        break;
	default:
	   $updateSql="UPDATE  $DataIn.cw16_modelfee SET Moq='$Moq', OutAmount='$OutAmount' ,Remark='$Remark' WHERE Id='$Id'";
	   $updateReuslt=mysql_query($updateSql);
	   if($Attached!=""){//有上传文件
		$FileType=".jpg";
		$OldFile=$Attached;
		$FilePath="../download/modelfee/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="M".$Id.$FileType;
		//echo $OldFile;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			       $Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
			       $Attached=1;
			       //更新刚才的记录
			       $sql = "UPDATE $DataIn.cw16_modelfee SET Bill='1' WHERE Id=$Id";
			       $result = mysql_query($sql);
			     }
		else{
			      $Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
			      $OperationResult="N";			
			     }
		  }
		if($_POST['ListId']){//如果指定了操作对象
			$Counts=count($_POST['ListId']);
			$Ids="";
			for($i=0;$i<$Counts;$i++){
				$thisId=$_POST[ListId][$i];
				$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
				}
			$TypeIdSTR="and StuffId IN ($Ids)";
			 $delSql="DELETE from $DataIn.modelfeestuff where mId='$Id' OR StuffId IN ($Ids)";
			 $delResult=mysql_query($delSql);                  
		     $inRecode="INSERT INTO $DataIn.modelfeestuff SELECT NULL,'$Id',StuffId,'$Date','$Operator',1,0,'$Operator',NOW(),'$Operator',NOW() FROM $DataIn.Stuffdata WHERE 1 $TypeIdSTR";
		    $inResult=@mysql_query($inRecode);
		    if($inResult){
			          $Log.="$Ids&nbsp;&nbsp;$Log_Item $Log_Funtion 成功! </br>";
		      	    }
		    else{
			        $Log.="<div class='redB'>&nbsp;&nbsp;$Log_Item $Log_Funtion 失败!</div></br>";
			        $OperationResult="N";
			     }
		}
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
