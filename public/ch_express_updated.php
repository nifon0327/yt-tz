<?php 
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="快递费用";		//需处理
$upDataSheet="$DataIn.ch9_expsheet";	//需处理
$upDataMain="$DataIn.cw9_expsheet";	//需处理

$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=0;
$FileDir="chexpress";
echo $ActionId;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 14:
		$Log_Funtion="请款";		$SetStr="Estate=2,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="退回";
		if($fromWebPage==$funFrom."_m"){	//审核退回
			$Log_Funtion="审核退回";			$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";			
			}
		else{							//财务退回
			if($Estate==3){					//未结付退回
				$Log_Funtion="未结付退回";		$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";
				}
			else{							//已结付退回，要处理现金流水帐
				$Log_Funtion="已结付退回";		$SetStr="Mid=0,Estate=1,Locks=1";		include "../model/subprogram/updated_model_3c.php";
				}			
			}
		break;
	case 16:
		$Log_Funtion="取消结付";	$SetStr="Mid=0,Estate=3,Locks=0";		include "../model/subprogram/updated_model_3c.php";		break;
	case 17:
		$Log_Funtion="审核";		$SetStr="Estate=3,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 18://结付
		$Log_Funtion="结付";		include "../model/subprogram/updated_model_3pay.php";
		break;
	case 20://财务更新
			//必选参数	:文件目录
			$Log_Funtion="主结付单资料更新";
			
			$Date=date("Y-m-d");
			$Estate=0;
			include "../model/subprogram/updated_model_cw.php";
		break;
		
		
    case 181:
    
	    $FilePath="../admin/phpExcelReader/expressfile/";
	     if(!file_exists($FilePath)){ 
	         makedir($FilePath);
	     }
	     $tmpFile=$_FILES['ExcelFile']['tmp_name'];
	     $imgname = $_FILES["ExcelFile"]["name"]; //获取上传的文件名称
	     $filetype = pathinfo($imgname, PATHINFO_EXTENSION);//获取后缀
	     if ($tmpFile!=""){
	        $str_time=time();
	        $tmpXmlFile=$Login_P_Number . "_" . $str_time .".".$filetype;
	        $PreFileName=$FilePath .$tmpXmlFile;
	        $uploadInfo=move_uploaded_file($tmpFile,$PreFileName);
			chmod($PreFileName,0777);
	        if($uploadInfo!=""){
	            require_once "../admin/phpExcelReader/Excel/reader.php";
				$data = new Spreadsheet_Excel_Reader();
	            $data->setOutputEncoding('gbk');
	            $data->read($PreFileName);
				error_reporting(E_ALL ^ E_NOTICE);
				$rs = $data -> getExcelResult();
				$RowNum=$data->sheets[0]['numRows'];
				$ColNum=$data->sheets[0]['numCols'];
		        for($i=2;$i<=$RowNum;$i++){
					if($rs[$i][1]!=NULL || $rs[$i][1]!=""){
				         $SendDate=date("Y")."-".trim($rs[$i][1]);
				         $ExpressNO = trim($rs[$i][2]);
				         $Weight = (float)(trim($rs[$i][3]));
				         $TypeName = trim($rs[$i][4]);
				         $TypeName = iconv("GB2312", 'UTF-8', $TypeName);
				         $Type   = $TypeName=="到付"?1:0;
				         $Amount = (float)trim($rs[$i][5]);
				         $Name=trim($rs[$i][6]);
				         $Name  = iconv("GB2312", 'UTF-8', $Name);
				         $Remark= trim($rs[$i][7]);
				         $Remark = iconv("GB2312", 'UTF-8', $Remark);  
						 $mySql="SELECT Number FROM $DataIn.staffmain WHERE Name='$Name' LIMIT 1 ";  
				         $cSign = 7;
						 $myResult=mysql_query($mySql,$link_id);
						 if($myRow=mysql_fetch_array($myResult)){
							  $HandledBy=$myRow["Number"];
						      }
						  else{
						      $HandledBy=0;
						      }
						 if($HandledBy>0){
							 $InSql="INSERT INTO $DataIn.ch9_expsheet (Id,cSign,Mid,Date,ExpressNO,CompanyId,BoxQty,Weight,Amount,Type,HandledBy,Remark,Estate,Locks,Operator) VALUES (NULL,'$cSign','0','$SendDate','$ExpressNO','$CompanyId','1','$Weight','$Amount','$Type','$HandledBy','$Remark','1','1','$Operator')";
							 $InRecode=@mysql_query($InSql);
							 if($InRecode){ $x++; } 
						 }
                       }
				  }
			     $Log.="$x 条记录插入ch9_expsheet表中 $InSql";
	         }
	      }else{
	           echo "加载EXCEL文件失败！";
	     }
    
    
         break;
         
	default:
		$SetStr="Date='$SendDate',ExpressNO='$ExpressNO',CompanyId='$CompanyId',BoxQty='$BoxQty',Weight='$Weight',Amount='$Amount',Type='$Type',HandledBy='$HandledBy',Remark='$Remark',Operator='$Operator'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&Estate=$Estate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

