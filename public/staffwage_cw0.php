<?php
//加入餐费、加班奖金 ewen 2013-08-04
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$mainData="$DataIn.cwxzmain";
$sheetData="$DataIn.cwxzsheet";
//步骤1：
include "../model/subprogram/cw0_model1.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
///////////////////
$mySql="SELECT M.PayDate,M.PayAmount,M.Payee,M.Remark AS PayRemark,M.Locks AS MLocks,S.Id,S.Mid,S.KqSign,S.Number,S.Month,
S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Jbjj,S.Ywjj,S.Shbz,S.Zsbz,S.Jtbz,S.Jbf,S.Yxbz,S.taxbz,S.Studybz,S.Housebz,S.Jz,S.Sb,S.Kqkk,S.dkfl,S.RandP,S.Otherkk,S.Amount,S.Estate,S.Locks,S.Gjj,S.Ct,
	P.Name,P.ComeIn,P.Estate AS mEsate,B.Name AS Branch,J.Name AS Job,BK.Title,F.Symbol AS Currency,M.cSign
FROM $mainData M
LEFT JOIN $sheetData S ON S.Mid=M.Id
LEFT JOIN $DataIn.staffmain P ON P.Number=S.Number 
LEFT JOIN $DataIn.branchdata B ON B.Id=S.BranchId
LEFT JOIN $DataIn.jobdata J ON J.Id=S.JobId
LEFT JOIN $DataIn.my2_bankinfo BK ON BK.Id=M.BankId
LEFT JOIN $DataIn.currencydata F ON F.Id=S.Currency
WHERE 1 $SearchRows
order by M.Date DESC,M.Id DESC,S.BranchId,S.JobId,P.ComeIn";
//echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);

$cz_all_sum = 0;

if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		//结付主表数据
		$Mid=$mainRows["Mid"];
		$Currency=$mainRows["Currency"];
		$PayDate=substr($mainRows["PayDate"],5,5);
		$JRJB=0;
		$TempA=mysql_fetch_array(mysql_query("SELECT SUM(Amount) AS Amount FROM $DataIn.cwxzsheet WHERE Mid='$Mid'",$link_id));
		$TempAmount=$TempA["Amount"];
		$PayAmount=$mainRows["PayAmount"]."<br>".$TempAmount;
		$BankName=$mainRows["Title"];
		$ImgDir="/download/cwxz/";
		$Checksheet=$mainRows["Checksheet"];
		$Payee=$mainRows["Payee"];
		$Receipt=$mainRows["Receipt"];
		include "../model/subprogram/cw0_imgview.php";
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];

		//结付明细数据
		$Id=$mainRows["Id"];
		$KqSign=$mainRows["KqSign"];
		$Branch=$mainRows["Branch"];
		$Job=$mainRows["Job"];
		$Month=$mainRows["Month"];
		/*
		$JRJBRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS JRJB FROM $DataIn.hdjbsheet WHERE Number='$Number' AND Month='$Month'",$link_id));
		$JRJB=$JRJBRow["JRJB"];
		*/
		$chooseMonth=$Month;
		$Number=$mainRows["Number"];

		if ($APP_FACTORY_CHECK==true){
			include_once("../model/subprogram/factoryCheckDate.php");
			if(skipStaff($Number) || skipStaff($NumberT))
			{
				continue;
			}
		}

        $strName=$mainRows["Name"];
        $cSignFrom=$mainRows["cSign"];
		include"../model/subselect/cSign.php";
		$Name=$mainRows["mEsate"]==1?$mainRows["Name"]:"<div class='yellowB'>".$mainRows["Name"]."</div>";
		$ComeIn=$mainRows["ComeIn"];
		$Dx=$mainRows["Dx"];					//底薪
		$Jbf=$mainRows["Jbf"];					//加班费
		$Gljt=$mainRows["Gljt"];				//工龄津贴
		$Gwjt=$mainRows["Gwjt"];			//岗位津贴
		$Jj=$mainRows["Jj"];						//奖金
		$Jbjj=$mainRows["Jbjj"];				//加班奖金
		$Ywjj=$mainRows["Ywjj"];				//额外奖金
		$Shbz=$mainRows["Shbz"]+$mainRows["Zsbz"];			//生活补助
		//$Zsbz=$mainRows["Zsbz"];			//住宿补助
		$Studybz=$mainRows["Studybz"];
		$Housebz=$mainRows["Housebz"];
		$Jtbz=$mainRows["Jtbz"];				//交通补助
		$Yxbz=$mainRows["Yxbz"];			//夜宵补助
		$taxbz=$mainRows["taxbz"];			//个税补助
		$Kqkk=$mainRows["Kqkk"];			//考勤扣款
		$dkfl=$mainRows["dkfl"];	    		//抵扣福利  //add by zx 2013-05-29
		$RandP=$mainRows["RandP"];		//扣税
		$Otherkk=$mainRows["Otherkk"];	//考勤扣款
		$Total=$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Jbjj+$Ywjj+$Shbz+$Zsbz+$Jtbz+$Yxbz+$taxbz+$Studybz+$Housebz-$Kqkk-$dkfl;
		$Jz=$mainRows["Jz"];			//借支
		$Sb=$mainRows["Sb"];			//社保
		$Gjj=$mainRows["Gjj"];		//公积金
		$Ct=$mainRows["Ct"];		//餐费
		$AmountSys=sprintf("%.0f",$Total-$Jz-$Sb-$Gjj-$Ct-$RandP-$Otherkk);		//实付
		$Amount=$mainRows["Amount"];		//数据表值

		$cz_all_sum += $Amount;
		$KqSignStr=$KqSign==1?"※":"●";
		$Estate=$mainRows["Estate"];//结付标记
		switch($Estate){
			case "0"://未结付
				$EstateSign="<div class='greenB'>已付</div>";
				break;
			default://未审核
				$EstateSign="<div class='redB'>错误</div>";
				break;
			}
		//工龄计算
		include "subprogram/staff_model_gl.php";
		$sumDx+=$Dx;
		$sumGljt+=$Gljt;
		$sumGwjt+=$Gwjt;
		$sumJj+=$Jj;
		$sumJbjj+=$Jbjj;
		$sumYwjj+=$Ywjj;
		$sumShbz+=$Shbz;
		//$sumZsbz+=$Zsbz;
		$sumStudybz+=$Studybz;
		$sumHousebz+=$Housebz;
		$sumGljt+=$Gljt;
		$sumJtbz+=$Jtbz;
		$sumYxbz+=$Yxbz;
		$sumtaxbz+=$taxbz;
		$sumKqkk+=$Kqkk;
		$sumdkfl+=$dkfl;
		$sumTotal+=$Total;
		$sumJz+=$Jz;
		$sumSb+=$Sb;
		$sumGjj+=$Gjj;
		$sumCt+=$Ct;
		$sumAmount+=$Amount;
		$sumJbf+=$Jbf;
		$Dx=SpaceValue0($Dx);
		$Gljt=SpaceValue0($Gljt);
		$Gwjt=SpaceValue0($Gwjt);
		$Jj=SpaceValue0($Jj);
		$Jbjj=SpaceValue0($Jbjj);
		$Ywjj=SpaceValue0($Ywjj);
		$Shbz=SpaceValue0($Shbz);
		//$Zsbz=SpaceValue0($Zsbz);
		$Studybz=SpaceValue0($Studybz);
		$Housebz=SpaceValue0($Housebz);
		$Jtbz=SpaceValue0($Jtbz);
		$Yxbz=SpaceValue0($Yxbz);
		$taxbz=SpaceValue0($taxbz);
		$Kqkk=SpaceValue0($Kqkk);
		$dkfl=SpaceValue0($dkfl);
		$Total=SpaceValue0($Total);

		$Jz=SpaceValue0($Jz);
		$RandP=SpaceValue0($RandP);
		$Gjj=SpaceValue0($Gjj);
		$Ct=SpaceValue0($Ct);
		$Otherkk=SpaceValue0($Otherkk);
		$Amount=SpaceValue0($Amount);
		$Jbf=SpaceValue0($Jbf);
		if ($Kqkk>0){
			$Kqkk="<a href='staffwage_kqkk.php?Number=$Number&chooseMonth=$chooseMonth&Name=$strName' target='_blank'>$Kqkk</a>";
			}
		//输出
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"staffwage_upmain\",$Mid)' src='../images/edit.gif' title='更新结付单资料!' width='13' height='13'>";
			}
		else{
			$upMian="&nbsp;";
			}
		if($MLocks==0){
			$Choose="<img src='../images/lock.png' title='主记录已锁定!' width='15' height='15'>";
			}
		else{
			if($Keys & mUPDATE){
				$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' disabled>";
				}
			else{
				$Choose="<img src='../images/lock.png' title='没有操作权限!' width='15' height='15'>";
				}
			}
		if($Amount!=$AmountSys){//实付
		echo "$Amount!=$AmountSys";
			$Amount="<div class='redB'>$Amount</div>";
			}

		if($tbDefalut==0 && $midDefault==""){//首行
			//检查员工日加班总和
			//并行列
			echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
			echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
			$unitWidth=$tableWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayDate</td>";//结付日期
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Payee</td>";		//凭证
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//结付备注
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayAmount</td>";		//结付总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$BankName</td>";		//结付银行
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$cSign</td>";
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			//并行宽
			//echo"<td width='$unitWidth' class='A0101'>";
			echo"<td width='' class='A0101'>";
			$midDefault=$Mid;
			}
		if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
			$m=15;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Month</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Branch</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Job</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Number</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Name</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Gl</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Currency</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Dx</td>";//金额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Jbf</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Gljt</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Gwjt</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Shbz</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Jtbz</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Studybz</td>";
            $m=$m+2;
            echo"<td  class='A0001' width='$Field[$m]' align='center'>$Housebz</td>";
            $m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Jj</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Ywjj</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Kqkk</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$dkfl</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Total</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Jz</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Sb</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$RandP</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Gjj</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Otherkk</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Amount</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$EstateSign</td>";//请款状态
			//echo"<td  class='A0001' width='' align='center'>$JRJB</td>";//请款状态
			echo"</tr></table>";
			$i++;
			}
		else{
			//新行开始
			echo"</td></tr></table>";//结束上一个表格
			//并行列
			echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
			echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
			$unitWidth=$tableWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayDate</td>";//结付日期
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Payee</td>";		//凭证
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//结付备注
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayAmount</td>";		//结付总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$BankName</td>";		//结付银行
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$cSign</td>";
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			//并行宽
			//echo"<td width='$unitWidth' class='A0101'>";
			echo"<td width='' class='A0101'>";
			$midDefault=$Mid;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'><div align='center'>$i</div></td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Month</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Branch</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Job</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Number</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Name</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Gl</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Currency</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Dx</td>";//金额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Jbf</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Gljt</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Gwjt</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Shbz</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Jtbz</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Studybz</td>";
            $m=$m+2;
            echo"<td  class='A0001' width='$Field[$m]' align='center'>$Housebz</td>";
            $m=$m+2;
            echo"<td  class='A0001' width='$Field[$m]' align='center'>$Jj</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Ywjj</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Kqkk</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$dkfl</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Total</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Jz</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Sb</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$RandP</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Gjj</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Otherkk</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Amount</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$EstateSign</td>";//请款状态
			//echo"<td  class='A0001' width='' align='center'>$JRJB</td>";//请款状态
			echo"</tr></table>";
			$i++;
			}
		}while($mainRows = mysql_fetch_array($mainResult));
	echo"</tr></table>";
	}
else{
	noRowInfo($tableWidth);
	}
///////////////////
List_Title($Th_Col,"0",1);
if ($Login_P_Number==11965  || $Login_P_Number==11093 ) {
	echo('cz-------'.number_format($cz_all_sum).'-------cz');
}
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>