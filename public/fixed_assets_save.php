<?php  
//$DataPublic.net_cpdata 二合一已更新
//电信-joseph
include "../model/modelhead.php";
//步骤2：
$Log_Item="固定资产资料保存";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
//$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$CpName=FormatSTR($CpName);
$Model=FormatSTR($Model);
$SSNumber=FormatSTR($SSNumber);
$IpAddress=FormatSTR($IpAddress);
$MacAddress=FormatSTR($MacAddress);

//buyer 和 Operator要直接写员工名字，而不是员工号 add by zx 2011-11-30
$BuyerArry=explode("-",$BuyerName);
$Buyer=$BuyerArry[1]; 

//echo "$BuyerName : buyer <Br>"
$Operator=$Login_P_Number;
include "../model/subprogram/staffname.php";   //把Operator 员工名字
$Operator=$Login_P_Number;

$cSign=$_SESSION["Login_cSign"];

//增加新职位
$inRecode="INSERT INTO $DataPublic.fixed_assetsdata (Id,cSign,CpName,TypeId,Qty,price,CompanyId,Model,SSNumber,BuyDate,ServiceLife,MTCycle,Warranty,BranchId,BuycSign,Buyer,
Remark,Attached,Retiredate,Estate,Locks,Date,Operator) VALUES (NULL,'$cSign','$CpName','$TypeId','$Qty','$price','$CompanyId','$Model','$SSNumber','$BuyDate','$ServiceLife','$MTCycle','$Warranty','$BranchId','$cSign','$Buyer','$Remark','$AttachedName','$Retiredate','1','0','$DateTime','$Operator')";

//echo "$inRecode";

$inAction=@mysql_query($inRecode);
$Mid=mysql_insert_id();   //获取编码
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	    if ($ShowCompany=="on") {  //表示直接输入公司的地址 
		$sheetInSql="INSERT INTO $DataPublic.company_assets (Id,Mid,Company,Name,Tel,Fax,Address,Remark) VALUES (NULL,'$Mid','$NewCompany','$NewName','$NewTel','$NewFax','$NewAddress','$NewRemark')";
		//echo "$sheetInSql";
		$sheetInAction=@mysql_query($sheetInSql);
		if($sheetInAction && mysql_affected_rows()>0){
			$Log.="加入临时供应商明细表成功.<br>";
		}
		else{
			$Log.="<div class='redB'>加入临时供应商明细表失败</div><br>";
		   $OperationResult="N";
		}
	  }
	  ////插入使用者及维护人员  
	  	$Operator=$Login_P_Number; //重新回到员工号
		$sheetInSql="INSERT INTO $DataPublic.fixed_userdata (Id,cSign,Mid,User,SDate,UserType,Remark,Date,Estate,Operator)
		VALUES (NULL,'$cSign','$Mid','$User','$UserDate','1','','$DateTime','1','$Operator'),
			   (NULL,'$cSign','$Mid','$maintainer','$BuyDate','2','','$DateTime','0','$Operator')
		";  //新登记的维修日期也就是购买日期
		//echo " <br> $sheetInSql";
		$sheetInAction=@mysql_query($sheetInSql);
		if($sheetInAction && mysql_affected_rows()>0){
			$Log.="加入使用者维修人员成功.<br>";
		}
		else{
			$Log.="<div class='redB'>加入使用者维修人员失败 </div><br>";
		   $OperationResult="N";
		}
                
	   $FilePath="../download/fixedFile/";
	   if(!file_exists($FilePath)){
	      makedir($FilePath);
	   }
	   for ($i=1;$i<5;$i++){
              $Attached="Attached" . $i;
              $OldFile=$$Attached;
              if($OldFile!=""){//有上传文件
	          $FileName = $_FILES["$Attached"]["name"]; //获取上传的文件名称 
                  $FileType =substr($FileName,strpos($FileName,"."));//获取后缀 
                  
	          $PreFileName=$Mid . "_" .$i .$FileType;
	          $AttachedName=UploadFiles($OldFile,$PreFileName,$FilePath);
	         if ($Attached!=""){	
                     $upSql="INSERT INTO $DataPublic.fixed_file (Id, Mid, FileName, Type, Date, Operator) VALUES
                             (NULL,'$Mid', '$PreFileName', $i , '$DateTime','$Operator')";
		     $upAction=@mysql_query($upSql);
		     $Log.="附件 $PreFileName 上传成功.<br>";
		   }
	       else{
		    $Log.="<div class='redB'>附件 $PreFileName 上传失败！</div><br>";
		    $OperationResult="N";
		    }
	     }
          }
	
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$Operator=$Login_P_Number;
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
