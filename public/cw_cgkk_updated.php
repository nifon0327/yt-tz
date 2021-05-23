<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="采购单扣款记录";		//需处理
$upDataSheet="$DataIn.cw15_gyskksheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$x=1;
switch($ActionId){
    case 15:
       $Lens=count($checkid);
      for($i=0;$i<$Lens;$i++){
	        $Id=$checkid[$i];
	       if ($Id!=""){
		         $Ids=$Ids==""?$Id:$Ids.",".$Id;
		       }
	      }
	     /*$DelSql="DELETE  $DataIn.cw15_gyskkmain M,$DataIn.cw15_gyskksheet S
		          FROM $DataIn.cw15_gyskkmain M
				  LEFT JOIN $DataIn.cw15_gyskksheet S ON S.Mid=M.Id
		          WHERE  M.Id='$Id'";*/
         $DelSql="DELETE  FROM $DataIn.cw15_gyskkmain WHERE Id IN ($Ids)";
		 $DelReuslt=mysql_query($DelSql);
		 if($DelReuslt){
		     $Log.="记录退回成功!<br>";
               $DelSheetSql="DELETE  FROM $DataIn.cw15_gyskksheet WHERE Mid IN ($Ids)";
               $DelSheetResult=@mysql_query($DelSheetSql);
		      }
		  else{
		       $Log.="<div class='redB'>记录退回失败! $DelSql</div><br>";
			   $OperationResult="N";
		      }
	     break;
    case 17://审核
	    $upDataSheet="$DataIn.cw15_gyskkmain";
	    $Log_Funtion="审核";		$SetStr="Estate=0,Locks=0";				
		include "../model/subprogram/updated_model_3d.php";		break;
	case 20://?
		$Log_Funtion="主采购单扣款更新";
		$Remark=FormatSTR($Remark);
        if($Attached!=""){//有上传文件
		            $FileType=".jpg";
		            $OldFile=$Attached;
		            $FilePath="../download/cgkkbill/";
		            if(!file_exists($FilePath)){
			            makedir($FilePath);
			           }
		             $PreFileName=$BillNumber.$FileType;
		             $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		            if($Attached){
			             $Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
			             $Attached=1;
			            }
		       else{
			          $Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
			           $OperationResult="N";			
			          }
          }
       $fileStr="";
       if($Attached==1)$fileStr=" , Picture=1";

		$upSql = "UPDATE $DataIn.cw15_gyskkmain SET Date='$Date',BillNumber='$BillNumber',Remark='$Remark',OPdatetime='$DateTime' $fileStr WHERE Id='$Mid'";
		$upResult = mysql_query($upSql);		
		if($upResult && mysql_affected_rows()>0){
			$Log.="主采购单扣款资料更新成功.<br>";
			}
		else{
			$Log.="<div class='redB'>主采购单扣款资料更新失败! $upSql </div><br>";
			$OperationResult="N";
			}
		$Id=$Mid;
		include "cw_cgkk_topdf.php";
		break;

	default:
		$Log_Funtion="采购单扣款数据更新";
		$SheetSql=mysql_query("SELECT Qty,Price,Mid,Amount FROM $upDataSheet WHERE Id='$Id'",$link_id);
		$Price=mysql_result($SheetSql,0,"Price");
		$Qty=mysql_result($SheetSql,0,"Qty");
		$Mid=mysql_result($SheetSql,0,"Mid");
		$OldAmount=mysql_result($SheetSql,0,"Amount");
		
		$upSTR="";
		if ($newQty>0){
			 $Qty=$newQty;
			 $upSTR=",Qty=$newQty";
		}

		if ($newPrice>0){
			$Price=$newPrice;
			$upSTR.=",Price=$newPrice";
		}
        if ($SheetRemark!=""){
	        $upSTR.="Remark='$SheetRemark'";
        }
		$NewAmount=sprintf("%.2f",$Price*$Qty);

		$UpSql="UPDATE $upDataSheet SET Amount=$NewAmount $upSTR WHERE Id='$Id'";
		$upAction=mysql_query($UpSql);
		
		$UpmainSql="UPDATE $DataIn.cw15_gyskkmain  SET TotalAmount=TotalAmount+($NewAmount-$OldAmount) WHERE Id='$Mid'";
		$UpmainResult=mysql_query($UpmainSql);
		if($UpmainResult && $upAction){
		     $Log.="主采购单扣款总金额更新成功!<br>";
			 $Log.="从采购单扣款数量更新成功!<br>";
			 $DelMidSql = "DELETE FROM $DataIn.cw15_gyskkmain WHERE Id =$Mid  AND TotalAmount=0 ";
			 $DelMidResult = mysql_query($DelMidSql);
		    }
		else{
		     $Log.="<div class='redB'>主采购单扣款总金额更新失败!<br>$UpmainSql<div>";
			 $Log.="<div class='redB'>从采购单扣款数量更新失败!<br>$UpSql<div>";
			 $OperationResult="N";
		    }
		$Id=$Mid;
		include "cw_cgkk_topdf.php";
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&CompanyId=$CompanyId";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
  