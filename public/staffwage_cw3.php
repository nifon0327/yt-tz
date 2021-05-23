<?php 
//加入餐费 ewen 2013-08-04
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量	
//步骤3：
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";

	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	
    $cSignResult = mysql_query("SELECT C.CShortName,S.cSign
	FROM $DataIn.cwxzsheet S 
	LEFT JOIN $DataIn.companys_group C ON C.cSign = S.cSign
	WHERE 1  and S.Estate='$Estate' $SearchRows  GROUP BY S.cSign ",$link_id);
	if($cSignRow = mysql_fetch_array($cSignResult)){
		$cSignSelect.="<select name='cSign' id='cSign' onchange='document.form1.submit()'>";
		do{
		    $cSignValue = $cSignRow["cSign"];
		    $CShortName = $cSignRow["CShortName"];
		    $cSign = $cSign==""?$cSignValue:$cSign;
			if($cSign==$cSignValue){
				$cSignSelect.="<option value='$cSignValue' selected>$CShortName</option>";
				$SearchRows.=" and  S.cSign ='$cSignValue'";
				}
			else{
				$cSignSelect.="<option value='$cSignValue'>$CShortName</option>";					
				}
			}while($cSignRow = mysql_fetch_array($cSignResult));
		$cSignSelect.="</select>&nbsp;";
		}
	
	$monthResult = mysql_query("SELECT S.Month FROM $DataIn.cwxzsheet S 
	WHERE 1 and S.Estate='$Estate' $SearchRows group by S.Month order by S.Id DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)){
		$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=$monthRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				$MonthSelect.="<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and S.Month='$dateValue'";
				}
			else{
				$MonthSelect.="<option value='$dateValue'>$dateValue</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		$SearchRows=$SearchRows==""?"and S.Month='$chooseMonth'":$SearchRows;
		$MonthSelect.="</select>&nbsp;";
	 }
  
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
    echo $cSignSelect;
	echo $MonthSelect;
	
	
	//选择地点
	$SelectTB="M";$SelectFrom=1; 
    include "../model/subselect/WorkAdd.php";
	
	//加入货币选择
		$SelectFrom=1;
		$CurrencyOther=" AND A.Id IN(1,4)";//只显示RMB/TWD
	  	include "../model/subselect/currency.php";
		if($Currency!=""){
			$SearchRows.=" AND S.Currency='".$Currency."'";
			$chooseCurrency=$Currency;
			}
	$SearchRows.="and S.Estate=3";
	
	//薪资分类
	echo"<select name='KqSign' id='KqSign' onchange='document.form1.submit()'>";
		$KqSignFlag="SelFlag" . $KqSign;
		$$KqSignFlag="selected";
		 echo "<option value='' $SelFlag>全部</option>";
		 echo "<option value='0' $SelFlag0>固定薪资</option>";
		 echo "<option value='1' $SelFlag1>考勤薪资</option>";
	echo"</select>&nbsp;";
	     if ($KqSign=="1") $SearchRows.=" AND S.KqSign=1";
		 if ($KqSign=="0") $SearchRows.=" AND S.KqSign!=1";
		 
	}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	</select>&nbsp;";
	}
//结付的银行
include "../model/selectbank1.php";
echo"$CencalSstr";
//步骤4：
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
//步骤6：需处理数据记录处理
$sumDx=$sumGljt=$sumGwjt=$sumJj=$sumShbz=$sumZsbz=$sumJtbz=$sumJbf=$sumYxbz=$sumtaxbz=$sumStudybz=$sumHousebz=$sumJz=$sumSb=$sumGjj=$sumCt=$sumKqkk=$sumdkfl=$sumRandP=$sumOtherkk=$sumAmount=$sumTotal=$sumJbjj=$sumYwjj=0;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
S.Id,S.KqSign,S.Number,S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Jbjj,S.Ywjj,S.Shbz,S.Zsbz,S.Jtbz,S.Studybz,S.Housebz,S.Jbf,S.Yxbz,S.taxbz,S.Jz,S.Sb,S.Gjj,S.Ct,
S.Kqkk,S.dkfl,S.RandP,S.Otherkk,S.Amount,S.Estate,S.Remark,S.Locks,M.Name,M.ComeIn,M.Estate AS mEsate,M.Id As PID,B.Name AS Branch,J.Name AS Job,S.Currency,C.Symbol,C.Rate,S.cSign,MS.Bank,MS.Bank2 
FROM $DataIn.cwxzsheet S 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
LEFT JOIN $DataPublic.staffsheet MS ON MS.Number=M.Number
LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId
LEFT JOIN $DataPublic.jobdata J ON J.Id=S.JobId
LEFT JOIN $DataPublic.currencydata C ON C.ID=S.Currency  
WHERE 1 $SearchRows
order by M.BranchId,M.JobId Asc,M.Id,M.ComeIn";

//echo "$mySql";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		//初始化数据
		$Dx=$Gljt=$Gwjt=$Jj=$Jbjj=$Shbz=$Zsbz=$Jtbz=$Jbf=$Yxbz=$taxbz=$Studybz=$Housebz=$Jz=$Sb=$Gjj=$Ct=$Kqkk=$dkfl=$RandP=$Otherkk=$Amount=$Total=0;
		$Id=$myRow["Id"];
		$KqSign=$myRow["KqSign"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Number=$myRow["Number"];
		
		if ($APP_FACTORY_CHECK==true){
			include_once("../model/subprogram/factoryCheckDate.php");
			if(skipStaff($Number) || $APP_FACTORY_CHECK)
			{
				continue;
			}
		}
		
        $strName=$mainRows["Name"];
        include "../model/subprogram/staff_qj_day.php";
		$Name=$myRow["mEsate"]==1?$myRow["Name"]:"<div class='yellowB'>".$myRow["Name"]."</div>";
		$ComeIn=$myRow["ComeIn"];
		$Bank=$myRow["Bank"];
		$Bank2=$myRow["Bank2"];
		if ($KqSign==1){
			 $BankSTR=$Bank2==''?($Bank==''?'--':"<span class='blueB'>工行</span>"):"<b>农行</b>";
		}else{
			 $BankSTR=$Bank==''?($Bank2==''?'--':'<b>农行</b>'):"<span class='blueB'>工行</span>";
		}
		
		//$Currency=$myRow["Currency"];			//加入支付货币 ewen 2014-06-11
		$Dx=$myRow["Dx"];					//底薪
		$Jbf=$myRow["Jbf"];					//加班费
		$Gljt=$myRow["Gljt"];				//工龄津贴
		$Gwjt=$myRow["Gwjt"];			//岗位津贴
		$Jj=$myRow["Jj"];						//奖金
		$Jbjj=$myRow["Jbjj"];				//加班奖金
		$Ywjj=$myRow["Ywjj"];				//额外奖金
		$Shbz=$myRow["Shbz"]+$myRow["Zsbz"];			//生活补助
		//$Zsbz=$myRow["Zsbz"];			//住宿补助
		$Studybz=$myRow["Studybz"];  //就学补助
		$Housebz=$myRow["Housebz"];
		$Jtbz=$myRow["Jtbz"];				//交通补助
		$Yxbz=$myRow["Yxbz"];			//夜宵补助	
		$taxbz=$myRow["taxbz"];			//个税补助
		$Kqkk=$myRow["Kqkk"];			//考勤扣款
		$dkfl=$myRow["dkfl"];				//抵扣福利  //add by zx 2013-05-29
		$Total=$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Jbjj+$Ywjj+$Shbz+$Zsbz+$Jtbz+$Yxbz+$taxbz+$Studybz+$Housebz-$Kqkk-$dkfl;
		$Jz=$myRow["Jz"];					//借支
		$Sb=$myRow["Sb"];					//社保
		$Gjj=$myRow["Gjj"];					//公积金
		$Ct=$myRow["Ct"];					//餐费
		$RandP=$myRow["RandP"];		//个税
		$Otherkk=$myRow["Otherkk"];	//社保
		$AmountSys=$Total-$Jz-$Sb-$Gjj-$Ct-$RandP-$Otherkk;		//实付
		$Amount=$myRow["Amount"];		//数据表值
		$KqSignStr=$KqSign==1?"※":"●";
		$Estate=$myRow["Estate"];//结付标记
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		if(($Keys & mUPDATE) || ($Keys & mLOCK)){
			$Locks=1;
			}
		$LockRemark="";
		switch($Estate){
			case "3"://未结付
				$EstateSign="<div class='redB'>未付</div>";
				break;
			default://未审核
				$EstateSign="<div class='redB'>错误</div>";
				break;
			}
		//工龄计算
		include "subprogram/staff_model_gl.php";
       
        $Currency=$myRow["Currency"];
		$Rate=$myRow["Rate"]; 
		if ($chooseCurrency>1) $Rate=1;
		$Symbol=$myRow["Symbol"];
	    $Symbol=$Currency==1?$Symbol:"<span class='redB'>$Symbol</span>";
		
		$sumDx+=$Dx*$Rate;
		$sumGljt+=$Gljt*$Rate;	
		$sumGwjt+=$Gwjt*$Rate;
		$sumJj+=$Jj*$Rate;
		$sumJbjj+=$Jbjj*$Rate;
		$sumYwjj+=$Ywjj*$Rate;
		$sumShbz+=$Shbz*$Rate;
		//$sumZsbz+=$Zsbz*$Rate;
		$sumStudybz+=$Studybz*$Rate;
		$sumHousebz+=$Housebz*$Rate;
		$sumJtbz+=$Jtbz*$Rate; 
		$sumYxbz+=$Yxbz*$Rate;	
		$Sumtaxbz+=$taxbz*$Rate; 
		$sumJtbz+=$Jtbz*$Rate;    	
		$sumKqkk+=$Kqkk*$Rate;  
		$sumdkfl+=$dkfl*$Rate;	
		$sumRandP+=$RandP*$Rate; 
		$sumOtherkk+=$Otherkk*$Rate;
		$sumTotal+=$Total*$Rate;	
		$sumJz+=$Jz*$Rate;			
		$sumSb+=$Sb*$Rate;		
		$sumGjj+=$Gjj*$Rate;	
		$sumCt+=$Ct*$Rate;
		$sumAmount+=$Amount*$Rate;
		$sumJbf+=$Jbf*$Rate;	
			
		$Dx=SpaceValue0($Dx);
		$Gljt=SpaceValue0($Gljt);
		$Gwjt=SpaceValue0($Gwjt);
		$Jj=SpaceValue0($Jj);
		$Jbjj=SpaceValue0($Jbjj);
		$Ywjj=SpaceValue0($Ywjj);
		$Shbz=SpaceValue0($Shbz);
		$Zsbz=SpaceValue0($Zsbz);
		$Jtbz=SpaceValue0($Jtbz);
		$Yxbz=SpaceValue0($Yxbz);
		$taxbz=SpaceValue0($taxbz);
		$Studybz=SpaceValue0($Studybz);
		$Housebz=SpaceValue0($Housebz);
		$Kqkk=SpaceValue0($Kqkk);
		$dkfl=SpaceValue0($dkfl);
		
		$Total=SpaceValue0($Total);
		$Jz=SpaceValue0($Jz);
		$Sb=SpaceValue0($Sb);
		$RandP=SpaceValue0($RandP);
		$Gjj=SpaceValue0($Gjj);
		$Ct=SpaceValue0($Ct);
		$Otherkk=SpaceValue0($Otherkk);
		$Amount=SpaceValue0($Amount);
		$Jbf=SpaceValue0($Jbf);
		if($KqSign<3){//加入考勤连接以便查询
		
			if($factoryCheck == "yes")
			{
				$Jbf="<a href='kq1_checkio_count.php?CountType=1&Number=$Number&CheckMonth=$chooseMonth' target='_blank'>$Jbf</a>";//验厂
			}
			else
			{
				$Jbf="<a href='kq_checkio_count.php?CountType=1&Number=$Number&CheckMonth=$chooseMonth' target='_blank'>$Jbf</a>";//内部
			}

		
		}
		if ($Kqkk>0){
        	$Kqkk="<a href='staffwage_kqkk.php?Number=$Number&chooseMonth=$chooseMonth&Name=$strName' target='_blank'>$Kqkk</a>";
            }
		if(round($AmountSys)!=$Amount){
			$Amount="<div class='redB'>$Amount</div>";
			}
			
		if ($Ywjj>0){
	        $YwFilePath="../download/ywjj/yw_" . $Id . ".jpg";
	        if (file_exists($YwFilePath)){
		      $Ywjj= "<a href=\"$YwFilePath\"   target=\"_blank\"  style='CURSOR: pointer;color:#FF6633'>$Ywjj</a>";
	        }
        }
        $cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
			array(0=>$KqSignStr, 	1=>"align='center'"),
			array(0=>$cSign, 		1=>"align='center'"),
			array(0=>$Branch, 		1=>"align='center'"),
			array(0=>$Job, 			1=>"align='center'"),
			array(0=>$Name,	 		1=>"align='center' $qjcolor"),
			array(0=>$Gl, 			1=>"align='right'"),
			array(0=>$Symbol, 	1=>"align='center'"),
			array(0=>$Dx, 			1=>"align='center'"),
			array(0=>$Jbf, 			1=>"align='center'"),
			array(0=>$Gljt,			1=>"align='center'"),
			array(0=>$Gwjt, 		1=>"align='center'"),
			array(0=>$Shbz, 		1=>"align='center'"),
			array(0=>$Jtbz, 		1=>"align='center'"),
			array(0=>$Studybz, 		1=>"align='center'"),
			array(0=>$Housebz, 		1=>"align='center'"),
			array(0=>$Jj, 			1=>"align='center'"),
			array(0=>$Ywjj, 			1=>"align='center'"),
			array(0=>"<div class='redB'>".$Kqkk."</div>", 1=>"align='center'"),
			array(0=>"<div class='redB'>".$dkfl."</div>", 1=>"align='center'"),
			array(0=>$Total,		1=>"align='center'"),
			array(0=>"<div class='redB'>".$Jz."</div>", 1=>"align='center'"),
			array(0=>"<div class='redB'>".$Sb."</div>", 1=>"align='center'"),
			array(0=>"<div class='redB'>".$RandP."</div>", 		1=>"align='center'"),
			array(0=>"<div class='redB'>".$Gjj."</div>", 1=>"align='center'"),
			array(0=>"<div class='redB'>".$Otherkk."</div>", 		1=>"align='center'"),
			array(0=>$Amount, 		1=>"align='center'"),
			array(0=>$EstateSign, 	1=>"align='center'"),
			array(0=>$BankSTR, 	1=>"align='center'"),
			array(0=>$Remark, 		1=>"align='center'")
			);
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
$sumDx=SpaceValue0($sumDx);$sumJbf=SpaceValue0($sumJbf);$sumGljt=SpaceValue0($sumGljt);$sumGwjt=SpaceValue0($sumGwjt);$sumJj=SpaceValue0($sumJj);$sumJbjj=SpaceValue0($sumJbjj);$sumYwjj=SpaceValue0($sumYwjj);
$sumShbz=SpaceValue0($sumShbz);$sumZsbz=SpaceValue0($sumZsbz);$sumJtbz=SpaceValue0($sumJtbz);$sumYxbz=SpaceValue0($sumYxbz);$Sumtaxbz=SpaceValue0($Sumtaxbz);$sumKqkk=SpaceValue0($sumKqkk);$sumdkfl=SpaceValue0($sumdkfl);$sumStudybz=SpaceValue0($sumStudybz);
$sumHousebz=SpaceValue0($sumHousebz);
$sumTotal=SpaceValue0($sumTotal);

$sumJz=SpaceValue0($sumJz);
$sumSb=SpaceValue0($sumSb);
$sumRandP=SpaceValue0($sumRandP);
$sumGjj=SpaceValue0($sumGjj);
$sumCt=SpaceValue0($sumCt);
$sumOtherkk=SpaceValue0($sumOtherkk);
$sumAmount=SpaceValue0($sumAmount);

$m=1;
$ValueArray=array(
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"<div >".$sumDx."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumJbf."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumGljt."</div>", 	1=>"align='center'"),
		array(0=>"<div >".$sumGwjt."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumShbz."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumJtbz."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumStudybz."</div>", 	1=>"align='center'"),
		array(0=>"<div >".$sumHousebz."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumJj."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumYwjj."</div>", 1=>"align='center'"),		
		array(0=>"<div >".$sumKqkk."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumdkfl."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumTotal."</div>",1=>"align='center'"),
		array(0=>"<div >".$sumJz."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumSb."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumRandP."</div>", 	1=>"align='center'"),
		array(0=>"<div >".$sumGjj."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumOtherkk."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumAmount."</div>", 1=>"align='center'"),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	)
		);
$ShowtotalRemark=$chooseCurrency>1?"合计($Symbol)":"合计(RMB)";
$isTotal=1;
include "../model/subprogram/read_model_total.php";		
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script language="JavaScript" type="text/JavaScript">
function ResetPage(obj){
	document.form1.submit();
	}
</script>