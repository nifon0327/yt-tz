<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="免抵退税收益明细";		//需处理
$upDataSheet="$DataIn.cw14_mdtaxmain";	//需处理 cw14_mdtaxmain
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="From=$From&Pagination=$Pagination&Page=$Page&Id=$Id";
//步骤3：需处理，更新操作
if($Taxgetdate==""){$Taxgetdate="0000-00-00";}
$x=1;
switch($ActionId){

     case "delshipmainNumber":
		$Log_Funtion="删除报关费用";
		$sql = "delete FROM $DataIn.cw14_mdtaxsheet  WHERE shipmainNumber='$shipmainNumber'";
		$result = mysql_query($sql);
		if($result){
			$Log="报关费用出货流水号 $shipmainNumber 删除成功.</br>";
			}
		else{
			$Log="报关费用出货流水号 $shipmainNumber 删除失败! $sql</br>";
			$OperationResult="N";
			}
		break;
	case "delotherfeeNumber":
		$Log_Funtion="删除行政费用";
		$sql = "delete FROM $DataIn.cw14_mdtaxfee  WHERE otherfeeNumber='$otherfeeNumber'";
		$result = mysql_query($sql);
		if($result){
			$Log="行政费用Id号 $otherfeeNumber 删除成功.</br>";
			}
		else{
			$Log="行政费用Id号 $otherfeeNumber 删除失败! $sql</br>";
			$OperationResult="N";
			}
		break;

	case 14:
		$Log_Funtion="请款";		$SetStr="Estate=2,Date='$Date',modifier='$Operator',modified='$DateTime' ";	$EstateStr=" AND Estate=1 ";				
		include "../model/subprogram/updated_model_3d.php";		break;
	case 17:
		$Log_Funtion="审核";	$SetStr="Estate=3,modifier='$Operator',modified='$DateTime'";		include "../model/subprogram/updated_model_3d.php";		break;

	case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="退回";
		if($fromWebPage==$funFrom."_m"){	//审核退回
			$Log_Funtion="审核退回";			$SetStr="Estate=1,modifier='$Operator',modified='$DateTime'";		include "../model/subprogram/updated_model_3d.php";			
			}
		else{							//财务退回
			switch($Estate){					
                 case "3"://未结付退回
				$Log_Funtion="未结付退回";		$SetStr="Estate=1,modifier='$Operator',modified='$DateTime'";		include "../model/subprogram/updated_model_3d.php";break;
                 case "2"://审核通过退回
				$Log_Funtion="审核通过退回";		$SetStr="Estate=1,modifier='$Operator',modified='$DateTime'";		include "../model/subprogram/updated_model_3d.php";break;
				case "0"://已结付退回
				$Log_Funtion="已结付退回";		$SetStr="Estate=1,modifier='$Operator',modified='$DateTime'";		include "../model/subprogram/updated_model_3d.php";break;
				}			
			}
		break;

        case 18:
        //$UpdateSql="UPDATE $upDataSheet SET PayDate='$Date' WHERE Id='$Id'";
        //$UpdateResult=mysql_query($UpdateSql,$link_id);
		$Log_Funtion="结付";		$SetStr=" Estate=0,PayDate='$Date',modifier='$Operator',modified='$DateTime' "; $EstateStr=" AND Estate=3";				include "../model/subprogram/updated_model_3d.php";		break;

		case 123:
		$FilePath="../download/cwmdtax";
		if($proof!=""){
			$FileType=".jpg";
	        $OldFile=$proof;
	        $PreFileName="P".$TaxNo.$FileType;
	        $uploadInfo1=UploadPictures($OldFile,$PreFileName,$FilePath);
	      }
		$mainSql = "update $DataIn.cw14_mdtaxmain SET Proof='$uploadInfo1',Remark='$Remark'  WHERE Id='$Id'";	
        $mainResult=@mysql_query($mainSql);
		if ($mainResult){
			$Log.="免抵退税发票号 $TaxNo 的资料更新成功<br>";
        }
      else{
			$Log="<div class=redB>&nbsp;&nbsp;免抵退税发票号 $TaxNo 的资料添加失败</div>"; 
			$OperationResult="N";
         }
           break;
		   
     case 84://补传发票文件
        $FilePath="../download/cwmdtax";
		$FileType=".jpg";
		$AfileStr="";
		$pfileStr="";
		//检查并上传文件
		if($Attached!=""){
			$OldFile=$Attached;
			$PreFileName="M".$TaxNo.$FileType;
			$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
			$AfileStr=",Attached='$uploadInfo'";
			
			}
			
		$FileType=".jpg";	
		if($proof!=""){
			$OldFile=$proof;
			$PreFileName="P".$TaxNo.$FileType;
			$uploadInfo1=UploadPictures($OldFile,$PreFileName,$FilePath);
			$pfileStr=",Proof='$uploadInfo1'";
			}
			
		$SetStr=" Id='$Id' $AfileStr $pfileStr";
		include "../model/subprogram/updated_model_3a.php";   
      break;

     default:                 //更新免抵退税资料OK
		    $FilePath="../download/cwmdtax";
		    if(!file_exists($FilePath)){
			                makedir($FilePath);
			         }
	     
		   if($Attachedfile!=""){	//有上传文件
			  $OldFile=$Attachedfile;
			  $PreFileName="M".$TaxNo.".jpg";
			  $uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
			}
			else{
			     $AttachedResult="select Attached from $DataIn.cw14_mdtaxmain where  Id='$Id'";
			     $AttachedRow=mysql_fetch_array(mysql_query($AttachedResult));
			     $Attached=$AttachedRow["Attached"];
			     $uploadInfo=$Attached;
			    }
				//没有上传文件
						
		    if($delFile!="")
			 {
			 //已选取删除原文件
			   $DelFilePath=$FilePath.$delFile;
			   if(file_exists($DelFilePath))
			      {
				  unlink($DelFilePath);
				  }			
			 }
		$FileType=".jpg";	 
		if($proof!=""){
	        $OldFile=$proof;
	        $PreFileName="P".$TaxNo.$FileType;
	        $uploadInfo1=UploadPictures($OldFile,$PreFileName,$FilePath);
	      }
        //1主单信息更新
		$mainSql = "update $DataIn.cw14_mdtaxmain SET
        TaxNo='$TaxNo',Taxdate='$Taxdate',Taxamount='$Taxamount',BankId='$BankId',endTax='$endTax',Taxgetdate='$Taxgetdate',
         Attached='$uploadInfo',Proof='$uploadInfo1',Remark='$Remark',Operator='$Operator',modifier='$Operator',modified='$DateTime'  WHERE Id='$Id'";	
	    $mainResult = mysql_query($mainSql);
		if ($mainResult){
			$Log.="免抵退税发票号 $TaxNo 的资料更新成功<br>";
			$x=1;
			$y=1;
			//========================================更新cw14_mdtaxsheet,cw14_mdtaxfee中的TaxNo.
			$dateValue=date("Y-m",strtotime($Taxdate));
			$updec="update $DataIn.cw14_mdtaxsheet SET TaxNo='$TaxNo' where DATE_FORMAT(Date,'%Y-%m')='$dateValue'";
			$decResult=mysql_query($updec);
			
			$upother="update $DataIn.cw14_mdtaxfee SET TaxNo='$TaxNo' where DATE_FORMAT(Date,'%Y-%m')='$dateValue'";
			$otherResult=mysql_query($upother);
			//========================================加入报关费用
			$RecordCount=count($shipmainNumber);
			for($i=1;$i<=$RecordCount;$i++){	
				$thisshipmainNumber=$shipmainNumber[$i];
				$thisinvoiceNumber=$InvoiceNumber[$i];				
				if($thisshipmainNumber!=""){//第二步
					$sheetRecode="INSERT INTO $DataIn.cw14_mdtaxsheet (Id,TaxNo,shipmainNumber,InvoiceNumber,Estate,Date,Operator) 
			VALUES (NULL,'$TaxNo','$thisshipmainNumber','$thisinvoiceNumber','1','$Taxdate','$Operator')";
					//echo "<br> $sheetRecode";
					$sheetRes=@mysql_query($sheetRecode);
					if($sheetRes){
						$Log.="&nbsp;&nbsp; $i 出货流水号( $thisshipmainNumber) 的报关费用 添加成功<br>";
						}
					else{
						$Log.="&nbsp;&nbsp; $i 出货流水号( $thisshipmainNumber) 的报关费用 添加成功<br>";	
						$OperationResult="N";
						}
					}//end if($thisshipmainNumber!="");
				}//end for; 		
			 //========================================加入其他费用
		      $RecordCount1=count($otherfeeNumber);
	          for($j=1;$j<=$RecordCount1;$j++){	
		      $thisotherfeeNumber=$otherfeeNumber[$j];							
		      if($thisotherfeeNumber!=""){//第二步
			   $sheetRec="INSERT INTO $DataIn.cw14_mdtaxfee (Id,TaxNo,otherfeeNumber,Estate,Date,Operator) 
			             VALUES (NULL,'$TaxNo','$thisotherfeeNumber','1','$Taxdate','$Operator')";
			//echo "<br> $sheetRecode";
			         $sheetR=@mysql_query($sheetRec);
			         if($sheetR){
				          $Log.="&nbsp;&nbsp; $j Id号( $thisotherfeeNumber) 的行政费用 添加成功<br>";	
				        }
			        else{
				          $Log.="&nbsp;&nbsp; $j Id号( $thisotherfeeNumber) 的行政费用 添加成功<br>";	
				          $OperationResult="N";
				        }
			        }////end if ($thisPid!="")
		        }//end for			
		    }//end if($mainResult);
		else{
			$Log="<div class=redB>&nbsp;&nbsp;免抵退税发票号 $TaxNo 的资料添加失败</div>"; 
			$OperationResult="N";
		     }
		
		break;
     }
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cw14_mdtaxmain,$DataIn.cw14_mdtaxsheet");
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>