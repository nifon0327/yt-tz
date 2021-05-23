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
$Str0="#总计信息\r\n#注意：本文件中的金额均以分为单位！\r\n#币种|日期|总计标志|总金额|总笔数|\r\n";
		$checkSql=mysql_query("SELECT M.Bank,S.Amount FROM $upDataSheet S LEFT JOIN $DataPublic.staffsheet M ON M.Number=S.Number WHERE S.Id IN ($Ids)",$link_id);
		if($checkRow=mysql_fetch_array($checkSql)){
			$i=1;
			$AmountTatol=0;
			do{
				$Bank=$checkRow["Bank"];
				$Amount=$checkRow["Amount"]*100;
				$AmountTatol+=$Amount;
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
		
		break;
	default:
		$result = mysql_query($sql);
		//$taxbz=0; //个税补助，>=175补100元
		//include "kqcode/staffwage_gs.php";  //得新计算个税
		$SetStr="Amount='$Amount',Jj='$Jj',dkfl='$dkfl',Otherkk='$Otherkk',Jtbz='$Jtbz',taxbz='$taxbz',RandP='$RandP',Remark='$Remark',Locks='0'";
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