<?php 
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="员工体检费";		//需处理
$upDataSheet="$DataIn.cw17_tjsheet";	//需处理
$upDataMain="$DataIn.cw17_tjmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$FileDir="sbjf";
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";		include "../model/subprogram/updated_model_3d.php";		break;
	case 14:
		$Log_Funtion="请款";	$SetStr="Estate=2,Locks=0,OPdatetime='$DateTime' ";		include "../model/subprogram/updated_model_3d.php";		break;
	case 17:
		$Log_Funtion="审核";	$SetStr="Estate=3,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 16:
		$Log_Funtion="取消结付";	$SetStr="Mid=0,Estate=3,Locks=0";	
		include "../model/subprogram/updated_model_3c.php";		break;
	case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="退回";
		if($fromWebPage==$funFrom."_m"){	//审核退回
			$Log_Funtion="审核退回";		$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";			
			}
		else{							//财务退回
			if($Estate==3){					//未结付退回
				$Log_Funtion="未结付退回";		$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";
				}
			else{							//已结付退回，要处理现金流水帐
				$Log_Funtion="已结付退回";		$SetStr="Mid=0,Estate=1,Locks=1";
				include "../model/subprogram/updated_model_3c.php";
				}			
			}
		break;
	case 18://结付
		//$AmountValue="Amount";
		$Log_Funtion="结付";		include "../model/subprogram/updated_model_3pay.php";
		break;
	case 20://财务更新
		$Date=date("Y-m-d");
		$Estate=0;
		$Log_Funtion="主结付单资料更新";
		include "../model/subprogram/updated_model_cw.php";
		break;
		
	case 64:
		$Log_Funtion="生成银行单";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		//提取如下内容 收款帐号|收款帐号名称|金额
		//输出格式：#币种|日期|顺序号|付款帐号|付款账号类型|收款帐号|收款帐号名称|金额|用途|备注信息|是否允许收款人查看付款人信息|
         //$Str0="#总计信息\r\n#注意：本文件中的金额均以分为单位！\r\n#币种|日期|总计标志|总金额|总笔数|\r\n";
         
        $Str0="顺序号|收款帐号名称|收款帐号|金额" . "\r\n";
        $Str1=''; $Str2='';
        
		$checkSql=mysql_query("SELECT M.Bank,M.Bank2,S.Amount FROM $upDataSheet S LEFT JOIN $DataPublic.staffsheet M ON M.Number=S.Number WHERE S.Id IN ($Ids)",$link_id);
		if($checkRow=mysql_fetch_array($checkSql)){
			$i=1;$j=1;
			$AmountTatol=0;
			do{
			    $Bank=$checkRow["Bank"];
				$Amount=$checkRow["Amount"]*100;
				$AmountTatol+=$Amount;
				
			    if (strlen($checkRow["Bank2"])>1){
				    $Banks=explode('|', $checkRow["Bank2"]);
				    $Bank2 = $Banks[0];
				    $Name  = $Banks[1];			   
			     	$Str2.="$j|$Name|".$Bank2."|".$Amount . "\r\n";
			     	$j++;
			    }
			    else{
			        $Banks=explode('|', $checkRow["Bank"]);
				    $Bank  = $Banks[0];	
			        $Name  = $Banks[1];
				    $Str1.="$i|$Name|".$Bank."|".$Amount . "\r\n";
				    $i++;
			    }
			 }while ($checkRow=mysql_fetch_array($checkSql));
		  }
		  
		  $Dir=anmaIn("download/bankbill/",$SinkOrder,$motherSTR);
		  $Log="点击下载生成的银行单文件:";
		  
		  if ($Str1!=''){
			    $Str=$Str0.$Str1;
				$datetime=date("YmdHis");
				$file="../download/bankbill/tjf".$datetime."_1.txt";
				if(!file_exists($file)){
					$handle=fopen($file,"a");
					fwrite($handle,$Str);
					fclose($handle);
				}
		        $filename1=anmaIn("tjf".$datetime."_1.txt",$SinkOrder,$motherSTR);
		        $Log.="<a href='#' onClick='OpenOrLoad(\"$Dir\",\"$filename1\",\"6\",\"\")' >工行</a>";
		  }	
		  
		  if ($Str2!=''){
			$Str_2=$Str0.$Str2;
			//$Str_2=iconv("UTF-8","GB2312//IGNORE",$Str_2);
			$datetime=date("YmdHis");
			
			$file_2="../download/bankbill/tjf".$datetime."_2.txt";
			if(!file_exists($file_2)){
				$handle=fopen($file_2,"a");
				fwrite($handle,$Str_2);
				fclose($handle);
			}
			$filename2=anmaIn("tjf".$datetime."_2.txt",$SinkOrder,$motherSTR);
			$Log.="&nbsp;&nbsp;&nbsp;<a href='#' onClick='OpenOrLoad(\"$Dir\",\"$filename2\",\"6\",\"\")'>农行</a> ";
		 }
		 
		   
			/*
			if($checkRow=mysql_fetch_array($checkSql)){
			$i=1;
			$AmountTatol=0;
			do{
				$Bank=$checkRow["Bank"];
				$Amount=$checkRow["Amount"]*100;
				$AmountTatol+=$Amount;
				//$Str1.="RMB|20100420|$i|6222004000122695224|灵通卡|".$Bank."|".$Amount."|||0|\r\n";
				$Str1.="RMB|20100420|$i|6222004000122695224|灵通卡|".$Bank."|".$Amount."|||0|\r\n";
				$i++;
				}while ($checkRow=mysql_fetch_array($checkSql));
			$i=$i-1;
			$Str0.="RMB|20100420|1|$AmountTatol|$i|\r\n#明细指令信息\r\n#其中付款账号类型：灵通卡、理财金0；信用卡1\r\n#币种|日期|顺序号|付款帐号|付款账号类型|收款帐号|收款帐号名称|金额|用途|备注信息|是否允许收款人查看付款人信息|\r\n";
			$Str1.="*";
			$Str=$Str0.$Str1;
			$Str=iconv("UTF-8","GB2312//IGNORE",$Str);
			//输出文本文件
			$datetime=date("YmdHis");
			$file="../download/bankbill/xz".$datetime.".gbpt";
			if(!file_exists($file)){
				$handle=fopen($file,"a");
				fwrite($handle,$Str);
				fclose($handle);
				}
			$Log="点击下载生成的<a href='$file'>银行单文件</a>";
			
			}
			*/
		
		break;			
      case 22:
              $UpSql="UPDATE $DataIn.cw17_tjsheet SET tjDate='$tjDate' WHERE Id='$Mid'";
              $UpResult=mysql_query($UpSql);
              if($UpResult){
                      $Log="体检时间更新成功!";
                       }
             else $Log="<span class='redB'>体检时间更新失败!$UpSql</span>";
           break;	
      case 23:
              $UpSql="UPDATE $DataIn.cw17_tjsheet SET HG='$HG' WHERE Id='$Mid'";
              $UpResult=mysql_query($UpSql);
              if($UpResult){
                      $Log="合格与否更新成功!";
                       }
             else $Log="<span class='redB'>合格与否更新失败!$UpSql</span>";
           break;	
           case 127:
              $Log_Funtion="体检报告上传";
		       //之前最后一个记录
		      $FilePath="../download/tjfile/";
		      if(!file_exists($FilePath)){
			      makedir($FilePath);
			    }
             if($Attached!=""){
                    $OldFile=$Attached;
					$FileType=substr("$Attached_name", -4, 4);
					$PreFileName=$Number.$Mid.$FileType;
                	$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
				    if($uploadInfo!=""){
						  $inRecode="INSERT INTO $DataPublic.staff_tj (Id,Number,Mid,Attached,Date,Estate,Locks,Operator) VALUES (NULL,'$Number','$Mid','$PreFileName','$Date','1','0','$Operator')";
						$inAction=@mysql_query($inRecode);
						if($inAction){
							    $Log.="员工ID号为: $Number 的体检报告 $uploadInfo 上传成功.<br>";
							    $EndNumber++;
                               }
						else{
							    $Log.="<div class='redB'>员工ID号为: $Number 的体检报告 $uploadInfo 上传失败. </div><br>";
							    $OperationResult="N";
							 }
					 }

             }
             break;
	default:
         $FilePath="../download/tjfile/";
	     if(!file_exists($FilePath)){
			  makedir($FilePath);
			}
         if($Attached!=""){
	          $OldFile=$Attached;
	          $FileType=substr("$Attached_name", -4, 4);
	          $PreFileName=date("YmdHis").$FileType;
	          $uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
	          $AttachedStr=$uploadInfo==""?"":",Attached='$uploadInfo'";
             }
         
		 $SetStr="tjType='$tjType',Amount='$Amount',Remark='$Remark',HG='$HG',tjDate='$tjDate',
		 Date='$Date',Locks='0',Operator='$Operator' $AttachedStr";
		 include "../model/subprogram/updated_model_3a.php";
		 break;
	  }
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth&Estate=$Estate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>