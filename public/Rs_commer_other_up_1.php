<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_other_up";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="商业险操作的其它功能";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");

/*switch($Action){
	case "1"://配件替换
		$Log_Funtion="员工的意外险替换";
         $OldArray=explode("^^",$OldName);
         $NewArray=explode("^^",$NewName);
         $oldNumber=$OldArray[1];
         $newNumber=$NewArray[1];
        $NewResult=mysql_fetch_array(mysql_query("SELECT BranchId,JobId FROM $DataIn.staffmain WHERE Number=$newNumber AND Estate=1 limit 1",$link_id));
       	$NewBranchId=$NewResult["BranchId"];
       	$NewJobId=$NewResult["JobId"];
	   
        
		$SMResult=mysql_fetch_array(mysql_query("SELECT Number FROM $DataIn.sbpaysheet  WHERE  Number=$oldNumber AND Month='$Month' AND TypeId=4 limit 1",$link_id));
       	$SMNumber=$SMResult["Number"];
	    if ($SMNumber>0){
		
		   $up_Sql = "UPDATE  $DataIn.sbpaysheet  S 
                   SET S.Number=$newNumber ,S.BranchId=$NewBranchId,S.JobId=$NewJobId
                   WHERE S.Number=$oldNumber AND S.Month='$Month' AND S.TypeId=4";
		
			$up_Result = mysql_query($up_Sql);
			if($up_Result && mysql_affected_rows()>0){
	               //记录原来缴费人
	               $In_Sql="INSERT INTO $DataIn.rs_casualty(`Id`, `Month`, `NewNumber`, `OldNumber`, `Date`, `Operator`)VALUES(NULL,'$Month','$newNumber','$oldNumber','$Date','$Operator')";
	               $In_Result=@mysql_query($In_Sql);
				   $Log.="员工ID $oldNumber 替换ID为 $newNumber 的商业险操作成功!$up_Sql";
				}
			else{
				$Log.="<div class='redB'>员工ID $oldNumber 替换ID为 $newNumber 的商业险操作失败! $up_Sql </div>";
				$OperationResult="N";
				}
			}else{
				
				$Log.="<div class='redB'>员工ID $oldNumber 没有商业险操作记录，不能替换! </div>";
				$OperationResult="N";
			}
		break;
       case 2:
		$Log_Funtion="上传单据";
       $DateTime=date("YmdHis");
		$Date=date("Y-m-d");
		if($PassData && $Attached!=""){//如果指定了操作对象,并且选择文件
              $OldFile=$Attached;
              $FilePath="../download/Casualty/";
              if(!file_exists($FilePath)){
                     makedir($FilePath);
                     }
              $PreFileName=$DateTime.".jpg";
              $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
              if($Attached){
                        $Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
                      }
              else{
                          $Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
                        }
            $DataArray=explode("|",$PassData);
			$Counts=count($DataArray);
			for($i=0;$i<$Counts;$i++){
                    $Mid=$DataArray[$i];
                    $DelSql="DELETE FROM $DataIn.rs_casualty_picture WHERE Mid='$Mid'";
				    $DelResult=mysql_query($DelSql);
				    
                    $PictureSql="INSERT INTO  $DataIn.rs_casualty_picture(`Id`, `Mid`, `Picture`, `Date`, `Operator`) VALUES(NULL,'$Mid','$PreFileName','$Date','$Operator')";
                    $PictureResult=@mysql_query($PictureSql);
				 }
          }
        break;
		}*/
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
