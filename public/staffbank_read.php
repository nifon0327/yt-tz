<?php 
//ewen 2014-06-11
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;
$tableMenuS=500;
ChangeWtitle("$SubCompany 员工资料列表");
$funFrom="staffbank";
$nowWebPage=$funFrom."_read";

$Th_Col="选项|40|序号|40|部门|60|职位|60|员工ID|50|姓名|60|薪资<br>货币|40|农行卡号|170|工行卡号|170|其他卡号|170";  
$BanEstateSTR="";
if ($BanEstate==1){
	$Th_Col.="|农行实付|60|工行实付|60|其他/现金|60|实付总|50|加班费|50|月份|50";
	$BanEstateSTR="checked='checked'";
	}
$ActioToS="3";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
    $SearchRows="";
	echo"<input name='BanEstate' type='checkbox' id='BanEstate' value='1' $BanEstateSTR onclick='document.form1.submit()'/>显示未结付";
	if($BanEstate==1){//显示未结付资料
		$monthResult = mysql_query("SELECT S.Month FROM $DataIn.cwxzsheet S WHERE 1 and S.Estate='3' group by S.Month order by S.Id DESC",$link_id);
		if($monthRow = mysql_fetch_array($monthResult)){
			$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
			do{
				$dateValue=$monthRow["Month"];
				$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
				if($chooseMonth==$dateValue){
					$MonthSelect.="<option value='$dateValue' selected>$dateValue</option>";
					$SearchRows.="and C.Month='$dateValue'";
					}
				else{
					$MonthSelect.="<option value='$dateValue'>$dateValue</option>";					
					}
				}while($monthRow = mysql_fetch_array($monthResult));
			$SearchRows=$SearchRows==""?"and C.Month='$chooseMonth'":$SearchRows;
			$MonthSelect.="</select>&nbsp;";
			}
		echo $MonthSelect;	
		//货币
		$SelectFrom=1;
		$CurrencyOther=" AND A.Id IN(1,4)";//只显示RMB/TWD
	  	include "../model/subselect/currency.php";
		if($Currency!=""){
			$SearchRows.=" AND M.Currency='".$Currency."'";
			}
		$ActioToS="64";
		}//显示未结付资料
		
		//薪资分类
	 echo"<select name='KqSign' id='KqSign' onchange='document.form1.submit()'>";
		$KqSignFlag="SelFlag" . $KqSign;
		$$KqSignFlag="selected";
		 echo "<option value='' $SelFlag>全部</option>";
		 echo "<option value='0' $SelFlag0>固定薪资</option>";
		 echo "<option value='1' $SelFlag1>考勤薪资</option>";
	echo"</select>&nbsp;";
	     if ($KqSign=="1") $SearchRows.=" AND M.KqSign=1";
		 if ($KqSign=="0") $SearchRows.=" AND M.KqSign!=1";
		//$cSignSelect="";$cSignTB="M";
		//$cSignFromSTR=$cSignFrom==""?"":" AND A.cSign='$cSignFrom'";
		//
	  //选择地点
	$SelectTB="M";$SelectFrom=1; 
    include "../model/subselect/WorkAdd.php";
	    
	}
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
if($BanEstate==1){
	$mySql="SELECT S.Id,S.Bank AS BankB,S.Bank2 AS BankA,S.Bank3 AS BankC,M.Number,M.Name,B.Name AS Branch,J.Name AS Job,F.Symbol AS Currency 
		FROM $DataIn.cwxzsheet C 
		LEFT JOIN $DataPublic.staffmain M  ON M.Number=C.Number
		LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
		LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
		LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
		LEFT JOIN $DataPublic.currencydata F ON F.Id=M.Currency
		WHERE 1 AND C.Estate=3  $SearchRows ORDER BY M.BranchId,M.JobId Asc,M.ID,M.ComeIn";
	}
else{
	$mySql="
		SELECT S.Id,S.Bank AS BankB,S.Bank2 AS BankA,S.Bank3 AS BankC,M.Number,M.Name,B.Name AS Branch,J.Name AS Job,E.Symbol AS Currency 
		FROM  $DataPublic.staffmain M 
		LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
		LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
		LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
		LEFT JOIN $DataPublic.currencydata E ON E.Id=M.Currency
		WHERE 1 AND M.Estate=1 $SearchRows ORDER BY M.BranchId,M.JobId Asc,M.ID,M.ComeIn";
	}
	
//if ($Login_P_Number==10868) echo "$mySql";	// AND M.cSign='$Login_cSign' 
$myBandResult = mysql_query($mySql." $PageSTR",$link_id);
$AmountSUM_A=$AmountSUM_B=$AmountSUM_C=$AmountSUM=0;$SumHolidayjb=0;
if($myBankRow = mysql_fetch_array($myBandResult)){
	do{
		$m=1;
		$Id=$myBankRow["Id"];		
		$Branch=$myBankRow["Branch"];				
		$Job=$myBankRow["Job"];
		$Number=$myBankRow["Number"];
		$Name="<span class='greenB'>".$myBankRow["Name"]."</sapn>";
		$Currency=$myBankRow["Currency"];
		
		
		$Bank_A=$myBankRow["BankA"];	//农行
		$Bank_B=$myBankRow["BankB"];	//工行
		$Bank_C=$myBankRow["BankC"];	//其他
		$Locks=0;
		if($Keys & mUPDATE){
			$Locks=1;
			}
		
		//月份资料初始化
        $Month="&nbsp;";		
	    $Amount="&nbsp;";
		if($BanEstate==1){//如果显示未结付资料
			$Amount_A=$Amount_B=$Amount_C=$AmountSys=$Amount=$tmp_Amount=0;//初始化
			$mySql="SELECT S.Id,S.Month,S.KqSign,S.Number,S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Shbz,S.Zsbz,S.Jtbz,S.Jbf,S.Yxbz,S.taxbz,S.Jz,S.Sb,S.Kqkk,S.RandP,S.Otherkk,S.Amount,S.Estate,S.Remark,S.Locks,M.Name,M.ComeIn,M.Estate AS mEsate,M.Id As PID,B.Name AS Branch,J.Name AS Job
			FROM $DataIn.cwxzsheet S 
			LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
			LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId
			LEFT JOIN $DataPublic.jobdata J ON J.Id=S.JobId
			WHERE 1 AND S.Estate=3  AND S.Number='$Number' AND S.Month='$chooseMonth'
			order by S.Month DESC  LIMIT 0 ,1";			$myResult = mysql_query($mySql,$link_id);
			if($myRow = mysql_fetch_array($myResult)){
				$m=1;
				//初始化数据
				$Dx=$Gljt=$Gwjt=$Jj=$Shbz=$Zsbz=$Jtbz=$Jbf=$Yxbz=$taxbz=$Jz=$Sb=$Kqkk=$RandP=$Otherkk=$Total=$Amount=0;
				$Month=$myRow["Month"];
				$Id=$myRow["Id"];				//更新Id
				$Dx=$myRow["Dx"];				//底薪
				$Jbf=$myRow["Jbf"];				//加班费
				$Gljt=$myRow["Gljt"];			//工龄津贴
				$Gwjt=$myRow["Gwjt"];		//岗位津贴
				$Jj=$myRow["Jj"];					//奖金
				$Shbz=$myRow["Shbz"];		//生活补助
				$Zsbz=$myRow["Zsbz"];		//住宿补助
				$Jtbz=$myRow["Jtbz"];			//交通补助
				$Yxbz=$myRow["Yxbz"];		//夜宵补助	
				$taxbz=$myRow["taxbz"];		//个税补助
				$Kqkk=$myRow["Kqkk"];		//考勤扣款
				$Total=$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jtbz+$Yxbz+$taxbz-$Kqkk;
				$Jz=$myRow["Jz"];				//借支
				$Sb=$myRow["Sb"];				//社保
				$RandP=$myRow["RandP"];
				$Otherkk=$myRow["Otherkk"];
				$AmountSys=$Total-$Jz-$Sb-$RandP-$Otherkk;	//数据计算值
				$Amount=$myRow["Amount"];								//数据表保存值
				$tmp_Amount=$Amount;	
				
				/*薪资分配:农行的值不大于3985，其他划分给工行
				*/
				switch($Currency){
					case 6://台币
						$Amount_C=$tmp_Amount;
					break;
					default:
						if($tmp_Amount>3985){
							$Amount_A=3985;									//农行
							$Amount_B=$tmp_Amount-$Amount_A;	//工行
							}
						else{
							$Amount_A=$tmp_Amount;						//只发农行
							}
					break;
					}
				/*
				//根据卡号有无的情况分配:如果农行没有卡号，则检查工行是否有卡号，有则累加至工行卡号，如果工行卡也没有，则累加至其他
				if($Bank_A==""){//如果农行卡号为空，
					if($Bank_B==""){//如果工行卡号也为空
						$Amount_C=$Amount_A+$Amount_B;
						$Amount_A=$Amount_B=0;
						//颜色分配
						}
					else{//如果工行卡号不为空
						$Amount_B+=$Amount_A;
						$Amount_A=0;
						}
					}
				else{//农行卡不为空
					if($Bank_B==""){//工行卡为空
					//	$Amount_A+=$Amount_B;
						$Amount_C=$Amount_B;	//将工行部分分配至其他
						$Amount_B=0;					//工行付置0
						}
					}*/
				}
				
			//累计
			$AmountSUM_A+=$Amount_A;
			$AmountSUM_B+=$Amount_B;
			$AmountSUM_C+=$Amount_C;
			$AmountSUM+=$Amount;
			//0值处理
			$Amount_A=SpaceValue0($Amount_A);
			$Amount_B=SpaceValue0($Amount_B);
			$Amount_C=SpaceValue0($Amount_C);
			$Amount=SpaceValue0($Amount);
			if(round($AmountSys)!=$Amount){//数据表保存值与计算值比较，如果不一致
				$Amount="<div class='redB' title='请核对该员工的工资 $AmountSys!=$Amount'>$Amount</div>";
				}
			}//end if($BanEstate==1)

		$Currency=$Currency=="TWD"?"<span class='redN'>$Currency</span>":$Currency;
		if($BanEstate!=1){
			$ValueArray=array(
				array(0=>$Branch,		1=>"align='center'"),
				array(0=>$Job,			1=>"align='center'"),
				array(0=>$Number,	1=>"align='center'"),
				array(0=>$Name,		1=>"align='center'"),
				array(0=>$Currency,	1=>"align='center'"),
				array(0=>$Bank_A),
				array(0=>$Bank_B),
				array(0=>$Bank_C)
				);  
			}
		else{
		     $Holidayjb=0;
		     $checkResult = mysql_query("SELECT Amount FROM $DataIn.hdjbsheet WHERE Number='$Number' and Month='$Month' AND Estate=3",$link_id);
		if($checkRow = mysql_fetch_array($checkResult)){
			   $Holidayjb=$checkRow["Amount"];
			   $SumHolidayjb+=$Holidayjb;
		    }
		
		    $Holidayjb=$Holidayjb==0?"&nbsp;":$Holidayjb;
			$ValueArray=array(
				array(0=>$Branch,		1=>"align='center'"),
				array(0=>$Job,			1=>"align='center'"),
				array(0=>$Number,	1=>"align='center'"),
				array(0=>$Name,		1=>"align='center'"),
				array(0=>$Currency,	1=>"align='center'"),
				array(0=>$Bank_A),
				array(0=>$Bank_B),
				array(0=>$Bank_C),
				array(0=>$Amount_A, 	1=>"align='right'"),
				array(0=>$Amount_B, 	1=>"align='right'"),
				array(0=>$Amount_C, 	1=>"align='right'"),
				array(0=>$Amount, 		1=>"align='right'"),
				array(0=>$Holidayjb, 		1=>"align='right'"),
				array(0=>$Month, 			1=>"align='center'")
				);
			}
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myBankRow = mysql_fetch_array($myBandResult));
	//统计
	if($BanEstate==1){
		$m=1;$HightStr="height='25'";
		$ValueArray=array(
				array(0=>"&nbsp;"),
				array(0=>"&nbsp;"),
				array(0=>"&nbsp;"),
				array(0=>"&nbsp;"),
				array(0=>"&nbsp;"),
				array(0=>"&nbsp;"),
				array(0=>"&nbsp;"),
				array(0=>"&nbsp;"),
				array(0=>$AmountSUM_A, 	1=>"align='right'"),
				array(0=>$AmountSUM_B, 		1=>"align='right'"),
				array(0=>$AmountSUM_C, 	1=>"align='right'"),
				array(0=>$AmountSUM, 		1=>"align='right'"),
				array(0=>$SumHolidayjb, 	1=>"align='right'"),
				array(0=>"&nbsp;"));
		$ShowtotalRemark="合计";
		$isTotal=1;
		include "../model/subprogram/read_model_total.php";	
		}
	}
else{
	noRowInfo($tableWidth);
  	}

//步骤7：
echo '</div>';
$RecordToTal= mysql_num_rows($myBandResult );
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script language="JavaScript" type="text/JavaScript">
function ResetPage(obj){
	document.form1.action="staffbank_read.php";
	document.form1.submit();
	}
</script>