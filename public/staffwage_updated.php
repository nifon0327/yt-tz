<?php 
//$DataIn.cwxzsheet/$DataIn.cwxzmain 二合一已更新$DataIn.电信---yang 20120801
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="员工薪资";		//需处理
$upDataSheet="$DataIn.cwxzsheet";	//需处理
$upDataMain="$DataIn.cwxzmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch ($ActionId){
	case 17:		
		$Log_Funtion="审核";		$SetStr="Estate=3,Locks=0";					include "../model/subprogram/updated_model_3d.php";		break;
	case 16:
		$Log_Funtion="取消结付";	
		$SetStr="Mid=0,Estate=3,Locks=0";			
		include "../model/subprogram/updated_model_3c.php";		
		//注意借支的还款日期归0
		
		break;
	case 15:
		$Log_Funtion="退回";
		if($Estate==3){					//未结付退回
			$Log_Funtion="未结付退回";		$SetStr="Estate=1,Locks=1";			include "../model/subprogram/updated_model_3d.php";
			}
		else{							//已结付退回，要处理现金流水帐
			$Log_Funtion="已结付退回";		$SetStr="Mid=0,Estate=1,Locks=1";	include "../model/subprogram/updated_model_3c.php";
			}			
		break;
	case 18:
		$ghDate=date("Ymd");
		$Log_Funtion="结付";
		include "../model/subprogram/updated_model_3pay.php";
		break;
	case 20:
			$Log_Funtion="主结付单资料更新";
			$FileDir="cwxz";
			include "../model/subprogram/updated_model_cw.php";
		break;
	case 26:
		$Log_Funtion="重置";
		for($i=0;$i<count($checkid);$i++){
			$Id=$checkid[$i];
			if ($Id!=""){
				include "staffwage_reset.php";
				}
			}	
		break;
case 40:
		$Log_Funtion="其他扣款凭证上传";
		//之前最后一个记录
		$FilePath="../download/otherkk/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$Month=$chooseMonth;
		$EndNumber=1;
		$checkEndFile=mysql_fetch_array(mysql_query("SELECT MAX(Picture) AS EndPicture FROM $DataPublic.staffwage_otherkk  WHERE Number='$Number' AND Month='$Month'",$link_id));
		$EndFile=$checkEndFile["EndPicture"];
		if($EndFile!=""){
			$TempArray1=explode("_",$EndFile);
			$TempArray2=explode(".",$TempArray1[1]);
			$EndNumber=$TempArray2[0]+1;
			}
		$uploadNums=count($Picture);
		for($i=0;$i<$uploadNums;$i++){
			//上传文档				
			$upPicture=$Picture[$i];
			$TempOldImg=$OldImg[$i];
			if ($upPicture!=""){	
				$OldFile=$upPicture;
				//检查是否有原档，如果有则使用原档名称，如果没有，则分配新档名
				if($TempOldImg!=""){
					$PreFileName=$TempOldImg;
					}
				else{
					$PreFileName=$Number."_".$EndNumber.".pdf";
					}
				$uploadInfo=$PreFileName;
				$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
				if($uploadInfo!=""){
					if($TempOldImg==""){//写入记录
						$inRecode="INSERT INTO $DataPublic.staffwage_otherkk (Id,Number,Picture,Month,`Estate`, `Locks`,Operator) VALUES (NULL,'$Number','$uploadInfo','$Month','1','0','$Operator')";
						$inAction=@mysql_query($inRecode);
						if($inAction){
							$Log.="ID号为 $Number 的员工其他扣款凭证 $uploadInfo 上传成功.<br>";
							$EndNumber++;
                             }
						else{
							$Log.="<div class='redB'>ID号为 $Number 的员工其他扣款凭证 $uploadInfo 上传 添加失败. $inRecode</div><br>";
							$OperationResult="N";
							}
						}
					}
				}
			}
       break;

	case 64:
	    //header("Content-Type:text/html; charset=UTF-8");
		$Log_Funtion="生成银行单";
		$Lens=count($checkid);
		$Dir=anmaIn("download/bankbill/",$SinkOrder,$motherSTR);
		
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
        
		$checkSql=mysql_query("SELECT M.Bank,M.Bank2,S.Amount FROM $upDataSheet S 
		    LEFT JOIN $DataPublic.staffsheet M ON M.Number=S.Number 
		    WHERE S.Id IN ($Ids) ",$link_id);//
		if($checkRow=mysql_fetch_array($checkSql)){
			$i=1;$j=1;
			$AmountTatol=0;
			$B_AmountTatol=0;
			
			do{
				$Bank=$checkRow["Bank"];
				$Amount=$checkRow["Amount"];
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
				//$Str1.="RMB|20100420|$i|6222004000122695224|灵通卡|".$Bank."|".$Amount."|||0|\r\n";
				//$Str1.="RMB|20100420|$i|6222004000122695224|灵通卡|".$Bank."|".$B_Amount."|||0|\r\n";
				//$i++;
				}while ($checkRow=mysql_fetch_array($checkSql));
			//$i=$i-1;
			//$Str0.="RMB|20100420|1|$AmountTatol|$i|\r\n#明细指令信息\r\n#其中付款账号类型：灵通卡、理财金0；信用卡1\r\n#币种|日期|顺序号|付款帐号|付款账号类型|收款帐号|收款帐号名称|金额|用途|备注信息|是否允许收款人查看付款人信息|\r\n";
			//$Str0.="RMB|20100420|1|$B_AmountTatol|$i|\r\n#明细指令信息\r\n#其中付款账号类型：灵通卡、理财金0；信用卡1\r\n#币种|日期|顺序号|付款帐号|付款账号类型|收款帐号|收款帐号名称|金额|用途|备注信息|是否允许收款人查看付款人信息|\r\n";
			/*$Str1.="*";
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
			
			$Str=$Str0.$Str1;
			//$Str=iconv("UTF-8","GB2312//IGNORE",$Str);
			//输出文本文件
			$datetime=date("YmdHis");
			$file="../download/bankbill/xz".$datetime."_1.txt";
			if(!file_exists($file)){
				$handle=fopen($file,"a");
				fwrite($handle,$Str);
				fclose($handle);
			}
			
			$Str_2=$Str0.$Str2;
			//$Str_2=iconv("UTF-8","GB2312//IGNORE",$Str_2);
			
			$file_2="../download/bankbill/xz".$datetime."_2.txt";
			if(!file_exists($file_2)){
				$handle=fopen($file_2,"a");
				fwrite($handle,$Str_2);
				fclose($handle);
			}
			
			$filename1=anmaIn("xz".$datetime."_1.txt",$SinkOrder,$motherSTR);
			$filename2=anmaIn("xz".$datetime."_2.txt",$SinkOrder,$motherSTR);
				
			$Log="点击下载生成的银行单文件:<a href='#' onClick='OpenOrLoad(\"$Dir\",\"$filename1\",\"6\",\"\")' >工行</a> &nbsp;&nbsp;&nbsp;<a href='#' onClick='OpenOrLoad(\"$Dir\",\"$filename2\",\"6\",\"\")'>农行</a> ";
				
			//$Log="点击下载生成的银行单文件:<a href='$file' target='_blank'>工行</a> &nbsp;&nbsp;&nbsp;<a href='$file_2' target='_blank'>农行</a> ";
		}
		break;
	default:
	    //之前最后一个记录
	    if ($YwjjPicture!=""){
		    $FilePath="../download/ywjj/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
			}
			$PreFileName="yw_" . $Id.".jpg";
			$PreFileName=$FilePath . $PreFileName;
			$uploadInfo=move_uploaded_file($YwjjPicture,$PreFileName);
			if ($uploadInfo){
				$Log.="ID号为 $Id 的额外奖金凭证上传成功.<br>";
			}
			else{
				$Log.="<div class='redB'>ID号为 $Id 的额外奖金凭证上传失败.</div><br>";
			}
	    }
	    
	     if ($dkflPicture!=""){
		    $FilePath="../download/dkfl/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
			}
			$PreFileName="fl_" . $Id.".jpg";
			$PreFileName=$FilePath . $PreFileName;
			$uploadInfo=move_uploaded_file($dkflPicture,$PreFileName);
			if ($uploadInfo){
				$Log.="ID号为 $Id 的取消津贴凭证上传成功.<br>";
			}
			else{
				$Log.="<div class='redB'>ID号为 $Id 的取消津贴凭证上传失败.</div><br>";
			}
	    }
		

		//$result = mysql_query($sql);
		//$taxbz=0; //个税补助，>=175补100元
		//include "kqcode/staffwage_gs.php";  //得新计算个税
		$SetStr="Amount='$Amount',Jj='$Jj',Ywjj='$Ywjj',dkfl='$dkfl',Kqkk='$Kqkk',Otherkk='$Otherkk',Jtbz='$Jtbz',taxbz='$taxbz',Jz='$Jz',RandP='$RandP',Remark='$Remark',Locks='0'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_m";
	}
$ALType="From=$From&Estate=$Estate&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>