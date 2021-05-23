<?php 
//$DataPublic.net_cpdata 二合一已更新
//电信-joseph
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="固定资产资料";		//需处理
$upDataSheet="$DataPublic.fixed_assetsdata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

$ALType="OMTypeId=$OMTypeId&TypeId=$TypeId&BranchId=$BranchId&UserMCID=$UserMCID";
//步骤3：需处理，更新操作
$cSignTemp=$_SESSION["Login_cSign"];

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

	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
		
	case "AddUser":
		$Log_Funtion="增加领用人";
		$checkRow=mysql_fetch_array(mysql_query("SELECT cSign,BranchId FROM $DataPublic.staffmain  WHERE Number='$User' LIMIT 1",$link_id));
         $BranchId=$checkRow["BranchId"]; 
         $cSign=$checkRow["cSign"]; 
		
		$sheetInSql="INSERT INTO $DataPublic.fixed_userdata (Id,cSign,Mid,User,SDate,UserType,Remark,Date,Estate,Operator)
		VALUES (NULL,'$cSign','$Mid','$User','$UserDate','1','$Remark','$DateTime','1','$Operator')";  //
		//echo "$sheetInSql";
		$sheetInAction=@mysql_query($sheetInSql);
		if($sheetInAction && mysql_affected_rows()>0){
			$Log.="加入领用人成功.<br>";
                   //更新使用部门
                  $upSql="UPDATE $DataPublic.fixed_assetsdata SET BranchId='$BranchId' WHERE Id=$Id ";
		        $upAction=@mysql_query($upSql);  
                        $Log.="$Id 使用部门($BranchId)更新成功.<br>";     
		}
		else{
			$Log.="<div class='redB'>加入领用人失败 </div><br>";
		   $OperationResult="N";
		}	
		break;
		
	case "AddMaintainer":
		$Log_Funtion="增加维护记录";
		$sheetInSql="INSERT INTO $DataPublic.fixed_userdata (Id,cSign,Mid,User,SDate,UserType,Remark,Date,Estate,Operator)
		VALUES (NULL,'$cSignTemp','$Mid','$User','$UserDate','2','$Remark','$DateTime','1','$Operator')";  //
		//echo "$sheetInSql";
		$sheetInAction=@mysql_query($sheetInSql);
		if($sheetInAction && mysql_affected_rows()>0){
			$Log.="加入维护记录成功.<br>";
		}
		else{
			$Log.="<div class='redB'>加入维护记录失败 </div><br>";
		   $OperationResult="N";
		}	
		break;	
		
	case "UpMCID":
		$Log_Funtion="更改使用公司";
		$SetStr=" cSign='$cSign'  ";
		include "../model/subprogram/updated_model_3a.php";
       //使用公司是杰藤的话直接报废
        if($cSign==9){
            $bfSql="UPDATE $DataPublic.fixed_assetsdata SET Estate=0  WHERE $SetStr AND Id=$Id";
            $bfResult=@mysql_query($bfSql);
            if($bfResult){
			      $Log.="给杰藤使用的固定资产报废成功.<br>";
                }
         }		
       else {
             $bfSql="UPDATE $DataPublic.fixed_assetsdata SET Estate=1  WHERE $SetStr AND Id=$Id";
              $bfResult=@mysql_query($bfSql);
               if($bfResult){
			      $Log.="给杰藤报废已转回.<br>";
                }
             }
		$sheetInSql="INSERT INTO $DataPublic.fixed_userdata (Id,cSign,Mid,User,SDate,UserType,Remark,Date,Estate,Operator)
		VALUES (NULL,'$cSign','$Mid','$User','$UserDate','3','$Remark','$DateTime','1','$Operator')";  //
		//echo "$sheetInSql";
		$sheetInAction=@mysql_query($sheetInSql);
		if($sheetInAction && mysql_affected_rows()>0){
			$Log.="加入使用公司成功.<br>";
		  }
		else{
			$Log.="<div class='redB'>加入使用公司失败 </div><br>";
		   $OperationResult="N";
		}			
		break;
	
	default:		

		$CpName=FormatSTR($CpName);
		$TypeId=$TypeId;
		$BranchId=$BranchId;
		$User=$User;
		$Qty=$Qty;
		$price=$price;
		$Model=FormatSTR($Model);
		$SSNumber=FormatSTR($SSNumber);
		$BuyDate=$BuyDate;
		$Warranty=$Warranty;
		$ServiceLife=$ServiceLife;
		$Retiredate=$Retiredate;
		$Remark=FormatSTR($Remark);
		$Attached=$Attached;
		$CompanyId=$CompanyId;
		$Attached=$Attached;
		$Estate=$Estate;
                
       if ($Attached1!="" || $Attached2!="" || $Attached3!="" || $Attached4!="") {  //有上传文件
           
            $FilePath="../download/fixedFile/";
            
            for ($i=1;$i<5;$i++){ $oldUpFile[$i]="";}
            $AttachedResult = mysql_query("SELECT Id,FileName,Type  FROM $DataPublic.fixed_file WHERE Mid=$Id order by Type",$link_id); 
             while ($AttachedRow=mysql_fetch_array($AttachedResult)){
                 $Type=$AttachedRow["Type"];
                 $oldUpFile[$Type]=$FilePath . $AttachedRow["FileName"];
             }

	    if(!file_exists($FilePath)){
	       makedir($FilePath);
	    }
            
	    for ($i=1;$i<5;$i++){
                
              $Attached="Attached" . $i;
              $OldFile=$$Attached;
              
              if($OldFile!=""){//有上传文件
                  if ($oldUpFile[$i]!=""){
                      if(file_exists($oldUpFile[$i])) unlink($oldUpFile[$i]);
		   }
                   
	          $FileName = $_FILES["$Attached"]["name"]; //获取上传的文件名称 
                  $FileType =substr($FileName,strpos($FileName,"."));//获取后缀 
                  
	          $PreFileName=$Id . "_" .$i .$FileType;
	          $AttachedName=UploadFiles($OldFile,$PreFileName,$FilePath);
                  
	         if ($Attached!=""){
                     
                    if ($oldUpFile[$i]!=""){
                        $upSql="UPDATE $DataPublic.fixed_file SET FileName='$PreFileName',Date='$DateTime', Operator='$Operator' WHERE Mid=$Id AND Type=$i";
		        $upAction=@mysql_query($upSql);  
                        $Log.="附件 $PreFileName 更新成功.<br>";
                    }
                     else{
                         $upSql="INSERT INTO $DataPublic.fixed_file (Id, Mid, FileName, Type, Date, Operator) VALUES
                             (NULL,'$Id', '$PreFileName','$i', '$DateTime','$Operator')";
		         $upAction=@mysql_query($upSql); 
                         $Log.="附件 $PreFileName 上传成功.<br>";
                     }
                     
		   }
	       else{
		    $Log.="<div class='redB'>附件 $PreFileName 上传失败！</div><br>";
		    $OperationResult="N";
		    }
	     }
          }
       }
		/*if($Attached!=""){//有上传文件
			$FileType=substr("$Attached_name", -4, 4);
			$OldFile=$Attached;
			$FilePath="../download/cpreport/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			$PreFileName=$CpName.$FileType;
			$AttachedName=UploadFiles($OldFile,$PreFileName,$FilePath);
			if ($Attached!=""){		
				$Log="附件上传成功.<br>";
				$AttachedSTR=",Attached='$AttachedName'";
				}
			else{
				$Log="<div class='redB'>附件上传失败！</div><br>";
				$AttachedSTR="";
				$OperationResult="N";
				}
			}
		else {
			if($oldFile!="")   //删除图片
			{
				$AttachedSTR=",Attached=''";
			}
		}*/
		
		//buyer 和 Operator要直接写员工名字，而不是员工号 add by zx 2011-11-30
		$BuyerArry=explode("-",$BuyerName);
		if(count($BuyerArry)==1){
			$Buyer=$BuyerArry[0]; 
		}
		else {
			$Buyer=$BuyerArry[1]; 	
		}
		$Operator=$Login_P_Number;
		//include "../model/subprogram/staffname.php";   //把Operator 员工名字
		
		$SetStr="BranchId='$BranchId',price='$price',CpName='$CpName',TypeId='$TypeId',CompanyId='$CompanyId',Model ='$Model',SSNumber='$SSNumber',BuyDate='$BuyDate',Warranty='$Warranty',MTCycle='$MTCycle',ServiceLife='$ServiceLife',Retiredate='$Retiredate',Buyer='$Buyer',Date='$DateTime',Operator='$Operator',Remark='$Remark',Estate='$Estate',Locks='0' $AttachedSTR";
		include "../model/subprogram/updated_model_3a.php";
                
	  
	     if($oldCompanyId==-1 && $ShowCompany=="on") { 
			$upDataSheet="$DataPublic.company_assets";	//需处理
			$SetStr="Company='$NewCompany',Name='$NewName',Tel='$NewTel',Fax='$NewFax',Address='$NewAddress',Remark ='$NewRemark'";
			$sql = "UPDATE $upDataSheet SET $SetStr WHERE Mid=$Id";
			//echo $sql;
			$result = mysql_query($sql);
			if($result){
				$Log.="MidD号在$Id 的临时供应商记录成功 $Log_Funtion.</br>";
				}
			else{
				$Log.="Mid号为$Id 的临时供应商记录$Log_Funtion 失败! $sql</br>";
				$OperationResult="N";
				}
		}
            else{
              if ($ShowCompany=="on" && $oldCompanyId!=-1) {  //表示直接输入公司的地址 
		$sheetInSql="INSERT INTO $DataPublic.company_assets (Id,Mid,Company,Name,Tel,Fax,Address,Remark) VALUES (NULL,'$Id','$NewCompany','$NewName','$NewTel','$NewFax','$NewAddress','$NewRemark')";
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
            }
		break;
	}
$Operator=$Login_P_Number;	
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

include "../model/logpage.php";
?>