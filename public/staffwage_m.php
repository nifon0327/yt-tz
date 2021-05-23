<?php 
//EWEN 2013-08-04 加入餐费、加班奖金
include "../model/modelhead.php";
$path = $_SERVER["DOCUMENT_ROOT"];
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceDecorator.php");
//步骤2：需处理
$ColsNumber=28;
$tableMenuS=450;
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 薪资列表");
$funFrom="staffwage";
if($From!="slist"){
	$sumCols="22,23,24,25,26";		//求和列
	$Th_Col="选项|40|序号|30|分类|30|所属公司|60|工作</br>地点|40|部门|40|职位|40|小组|50|员工<br>姓名|50|工龄<br>Y(M)|40|支付<br>货币|40|底薪|40|加班费|40|工龄<br>津贴|40|岗位<br>津贴|40|生活/住宿<br>补助|60|交通<br>补助|40|就学<br>补助|40|购房<br>补助|40|绩效<br>奖金|40|其他<br>奖金|40|考勤<br>扣款|40|取消<br>津贴|40|小计|55|社保|50|公积金|50|个税|40|实付|55|状态|40|备注|40";
	$swidth=270;
	//$ActioToS="1,2,3,4,26,17,33,19,11";	
	$ActioToS="1,17";	
}
else
{
        
	$sumCols="23,24,25,26,27";		//求和列
	$Th_Col="选项|40|序号|30|分类|30|所属公司|60|月份|60|工作</br>地点|40|部门|40|职位|40|小组|50|员工<br>姓名|50|工龄<br>Y(M)|40|支付<br>货币|40|底薪|40|加班费|40|工龄<br>津贴|40|岗位<br>津贴|40|生活/住宿<br>补助|60|交通<br>补助|40|就学<br>补助|40|购房<br>补助|40|绩效<br>奖金|40|其他<br>奖金|40|考勤<br>扣款|40|取消<br>津贴|40|小计|55|社保|50|公积金|50|个税|40|实付|55|状态|40|备注|40";
	$swidth=330;
	$ActioToS="1,17";
     
	
}

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量	
//$ActioToS="1,2,3,4,26,17,33,19,11";							//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
if($From=="slist") echo "<input name='tempFrom' type='hidden' id='tempFrom' value='m'/>";
//非必选,过滤条件
//echo "From :$From <br>";
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	
	
	   /* $cSignTB="M";$SelectFrom=5;

	    //include "../model/subselect/cSign.php";
		$SharingShowSTR=$SharingShow=="Y"?"":" AND A.cSign in (3,7) ";
		$cSignSelect="";
		$cSignFromSTR=$cSignFrom==""?"":" AND A.cSign='$cSignFrom'";

		//echo "cSignFromSTR:$cSignFromSTR";
		$cSignResult = mysql_query("SELECT A.cSign,A.CShortName,A.Db,ColorValue FROM $DataPublic.companys_group  A WHERE  1 $EstateStr  $SharingShowSTR $cSignFromSTR ORDER BY A.Id",$link_id);
		if($cSignRow = mysql_fetch_array($cSignResult)){
			if($cSignFrom!=""){//来自于记录行，输出类型名称
				$ColorValue=$cSignRow["ColorValue"];
				$CShortName=$cSignRow["CShortName"];
				$cSign="<spnn style='color:$ColorValue'><strong>".$CShortName."</strong></span>";
				}
			else{//输出选择框
				switch($SelectFrom){
					case 1://来自于浏览页面
						$cSignSelect="<select name='cSign' id='cSign' onchange='document.form1.submit()'><option value=''>全部</option>";
					break;
					case 4://来自于查询页面
						$cSignWidth= $cSignWidth==""?"380px": $cSignWidth;
						$cSignSelect="<select name=value[] id='value[]' style='width:$cSignWidth'><option selected  value=''>全部</option>";
					break;
					case 5://不含"全部"选项
						 $cSignSelect="<select name='cSign' id='cSign' onchange='document.form1.submit()'>";
						 if ($cSign=="") $cSign=$_SESSION["Login_cSign"] ;
						 break;
					break;
					default://来自于新增或更新页面
						$cSignWidth= $cSignWidth==""?"380px": $cSignWidth;
						$cSignSelect="<select name='cSign' id='cSign' style='width:$cSignWidth' dataType='Require' msg='未选择'><option value='' selected>--请选择--</option>";
					break;
					}
				do{
					$theId=$cSignRow["cSign"];
					$theName=$cSignRow["CShortName"];
					$dbName=$cSignRow["Db"];
					if ($theId==$cSign){
						$cSignSelect.="<option value='$theId' selected>$theName</option>";
						if($cSignTB!=""){
							$SearchRows.=" AND $cSignTB.cSign='$theId'";
							}
						}
					else{
						$cSignSelect.="<option value='$theId'>$theName</option>";
						}
					}while ($cSignRow = mysql_fetch_array($cSignResult));
				$cSignSelect.= "</select>&nbsp;";
				echo $cSignSelect;
				}
			}	*/
		$cSignTB="";	
	  //**********************************************	
	
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	$DefaultMonth="2014-01-01";
	$NewMonth=date("Y-m");
	$Months=intval(abs((date("Y")-2014)*12+date("m")));
	for($i=$Months-1;$i>=0;$i--){
		$dateValue=date("Y-m",strtotime("$i month", strtotime($DefaultMonth))); 
		$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				$optionStr.="<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and S.Month='$dateValue'";
				}
			else{
				$optionStr.="<option value='$dateValue'>$dateValue</option>";					
				}
		}

	}
//步骤4：需处理-条件选项
if($From!="slist"){
      //加入货币选择
		$SelectFrom=1;
		$CurrencyOther=" AND A.Id IN(1,4)";//只显示RMB/TWD
	  	include "../model/subselect/currency.php";
		if($Currency!=""){
			$SearchRows.=" AND S.Currency='".$Currency."'";
			$chooseCurrency=$Currency;
	  }

	//月份
	echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>$optionStr</select>&nbsp;";
	//结付状态
	 $SelectTB="M";$SelectFrom=1; 
	//选择地点
    include "../model/subselect/WorkAdd.php";  
	  
	echo"<select name='BranchId' id='BranchId' onchange='document.form1.submit()'>";
	$B_Result=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata 
						   WHERE 1  AND (cSign=$Login_cSign OR cSign=0 ) ORDER BY Id",$link_id);
	if($B_Row = mysql_fetch_array($B_Result)) {
		echo "<option value='' selected>全部</option>";
		do{
			$B_Id=$B_Row["Id"];
			$B_Name=$B_Row["Name"];
			if($BranchId==$B_Id){
				echo "<option value='$B_Id' selected>$B_Name</option>";
				$SearchRows.=" AND S.BranchId='$BranchId'";
				}
			else{
				echo "<option value='$B_Id'>$B_Name</option>";
				}
			}while ($B_Row = mysql_fetch_array($B_Result));
		}
	echo"</select>&nbsp;";
	
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

	//状态分类
	echo"<select name='mEstate' id='mEstate' onchange='document.form1.submit()'>";
		$EstateFlag="SelEstateFlag" . $mEstate;
		$$EstateFlag="selected";
		 echo "<option value='' $SelEstateFlag>全部</option>";
		 echo "<option value='0' $SelEstateFlag0>已核</option>";
		 echo "<option value='1' $SelEstateFlag1>未通过</option>";
	echo"</select>&nbsp;";
	     if ($mEstate=="1") $SearchRows.=" AND S.Estate not in (0,3)";
		 if ($mEstate=="0") $SearchRows.=" AND S.Estate=3";

	}
/*
else
{
	$chooseMonth='';
}
*/
//echo "chooseMonth0:$chooseMonth <br>";
$otherPageStr="&nbsp;&nbsp;&nbsp;&nbsp;<a href='staffwage_m_jbf.php' target='_blank'>薪资+加班费列表</a>";
echo"$CencalSstr $otherPageStr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
//初始化
$sumDx=$sumGljt=$sumGwjt=$sumJj=$sumShbz=$sumZsbz=$sumJtbz=$sumStudybz=$sumHousebz=$sumJbf=$sumYxbz=$sumtaxbz=$sumJz=$sumSb=$sumGjj=$sumCt=$sumKqkk=$sumdkfl=$sumRandP=$sumOtherkk=$sumAmount=$sumTotal=$sumJbjj=$sumYwjj=0;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);

$mySql="SELECT 
S.Id,S.Month,S.KqSign,S.Number,S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Jbjj,S.Ywjj,S.Shbz,S.Zsbz,S.Jtbz,S.Studybz,S.Housebz,S.Jbf,S.Yxbz,S.taxbz,S.Jz,S.Sb,S.Gjj,S.Ct,
S.Kqkk,S.dkfl,S.RandP,S.Otherkk,S.Amount,S.Estate,S.Remark,S.Locks,M.Name,M.ComeIn,M.Estate AS mEsate,M.WorkAdd,M.Id As PID,B.Name AS Branch,J.Name AS Job,E.GroupName,S.Currency,C.Symbol,C.Rate,S.cSign 
FROM $DataIn.cwxzsheet S 
LEFT JOIN $DataIn.staffmain M ON M.Number=S.Number 
LEFT JOIN $DataIn.branchdata B ON B.Id=S.BranchId
LEFT JOIN $DataIn.jobdata J ON J.Id=S.JobId
LEFT JOIN $DataIn.staffgroup E ON E.GroupId=S.GroupId
LEFT JOIN $DataIn.currencydata C ON C.ID=S.Currency  
WHERE 1 $SearchRows
order by M.Estate DESC,S.Estate DESC,B.SortId,S.JobId,M.ComeIn";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		//初始化数据
		$Dx=$Gljt=$Gwjt=$Jj=$Ywjj=$Shbz=$Zsbz=$Jtbz=$Jbf=$Yxbz=$taxbz=$Studybz=$Housebz=$Jz=$Sb=$Gjj=$Ct=$Kqkk=$dkfl=$RandP=$Otherkk=$Amount=$Total=0;
		$Id=$myRow["Id"];
		$SMonth=$myRow["Month"];
		$KqSign=$myRow["KqSign"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$GroupName=$myRow["GroupName"];
		$Number=$myRow["Number"];
		if($GroupName==""){
			//补充小组:从每天小组中提取当月最后一天的小组为准，如果没有则使用默认的
			$FirstDay=$SMonth."-01";
			$LastDay=date("Y-m-t",strtotime("$FirstDay"));
			$checkGS=mysql_query("SELECT GroupId  FROM $DataIn.sc1_memberset WHERE Date='$LastDay'  AND Number='$Number'",$link_id);
			//补充小组:从每天小组中提取当月最后一天的小组为准，如果没有则使用默认的
			if($checkGR=mysql_fetch_array($checkGS)){
				$GroupId=$checkGR["GroupId"];
				}
			else{
				$GroupId=$myRow["GroupId"];
				}
			//更新
			$update_SQL = mysql_query("UPDATE $DataIn.cwxzsheet SET GroupId='$GroupId' WHERE Id='$Id'",$link_id);
			$update_Result = mysql_query($update_SQL);
			}
		
        //员工请假超过半个月的显示颜色
        include "../model/subprogram/staff_qj_day.php";
        $strName=$myRow["Name"];
		$Name=$myRow["mEsate"]==1?$myRow["Name"]:"<div class='yellowB'>".$myRow["Name"]."</div>";
		$ComeIn=$myRow["ComeIn"];
		$WorkAddFrom=$myRow["WorkAdd"];
		include "../model/subselect/WorkAdd.php";
		$Dx=$myRow["Dx"];			//底薪
		$Jbf=$myRow["Jbf"];			//加班费

		//***********************
		$APP_FACTORY_CHECK = true;
		$reduceMoney = 0;
		if($APP_FACTORY_CHECK && $KqSign == '1' && $chooseMonth == '2015-10' && $cSign =='7'){
			$fliterDate = array('2015-10-25');
			for($i=0; $i<count($fliterDate);$i++){
				$tempDate = $fliterDate[$i];
				$staff = new AttendanceAvatar($Number, $DataIn, $DataPublic, $link_id);
				$staff->setupAttendanceData($Number, $tempDate, $DataIn, $DataPublic, $link_id);
				$staff->attendanceSetup($DataIn, $DataPublic, $link_id);
				$dayAttendanceInfomation =  $staff->getInfomationByTag();
				$over =  $dayAttendanceInfomation['weekOtTime'] > 8?8:$dayAttendanceInfomation['weekOtTime'];
				$reduceMoney += $over ; 
			}
			
		}

		//皮套10月超
		if($APP_FACTORY_CHECK && $KqSign == '1' && $chooseMonth == '2015-10' && $cSign =='3'){
			$fliterDate = array('2015-10-31', '2015-10-17');
			for($i=0; $i<count($fliterDate);$i++){
				$tempDate = $fliterDate[$i];
				$staff = new AttendanceAvatar($Number, $DataIn, $DataPublic, $link_id);
				$staff->setupAttendanceData($Number, $tempDate, $DataOut, $DataPublic, $link_id);
				$staff->attendanceSetup($DataIn, $DataPublic, $link_id);
				$dayAttendanceInfomation =  $staff->getInfomationByTag(); 
				$over =  ($dayAttendanceInfomation['weekOtTime'] > 10 ?10:$dayAttendanceInfomation['weekOtTime'])-8;
				$over = $over > 0 ? $over : 0;
				$reduceMoney += $over ; 
			}
		}

		$reduceMoney = floor($reduceMoney * 23.33);
		//echo $otherReduceSql;
		$otherReduceSql = "SELECT wage FROM $DataIn.reduceWage WHERE Number = $Number AND Month = '$chooseMonth'";
		$otherReduceResult = mysql_query($otherReduceSql);
		if($otherReduceRow = mysql_fetch_assoc($otherReduceResult)){
			$otherReduce = $otherReduceRow['wage'];
			$reduceMoney += $otherReduce;
		}

		$Jbf = $Jbf - $reduceMoney;
		$Ywjj=$myRow["Ywjj"]+$reduceMoney;   //额外奖金

		$Gljt=$myRow["Gljt"];		//工龄津贴
		$Gwjt=$myRow["Gwjt"];	//岗位津贴
		$Jj=$myRow["Jj"];				//奖金
		$Jbjj=$myRow["Jbjj"];	//加班奖金
		$Shbz=$myRow["Shbz"]+$myRow["Zsbz"];	//生活补助+住宿补助
		
		$Studybz=$myRow["Studybz"];	//就学补助
		$Housebz=$myRow["Housebz"];
		//$Zsbz=$myRow["Zsbz"];	//
		$Jtbz=$myRow["Jtbz"];		//交通补助
		$Yxbz=$myRow["Yxbz"];	//夜宵补助
		$taxbz=$myRow["taxbz"];	//个税补助
		$Kqkk=$myRow["Kqkk"];	//考勤扣款	
		$dkfl=$myRow["dkfl"];		//抵扣福利  //add by zx 2013-05-29
		
		
		$Currency=$myRow["Currency"];
		$Rate=$myRow["Rate"];
		$Symbol=$myRow["Symbol"];
		$Symbol=$Currency==1?$Symbol:"<span class='redB'>$Symbol</span>";
		

		$checkResult = mysql_query("SELECT Amount FROM $DataIn.hdjbsheet WHERE Number='$Number' and Month='$chooseMonth'",$link_id);
		if($checkRow = mysql_fetch_array($checkResult)){
			$Jbjj=sprintf("%.0f",$checkRow["Amount"]);
	    }
	    
		$Total=$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Ywjj+$Shbz+$Jtbz+$Studybz+$Housebz+$Yxbz+$taxbz-$Kqkk-$dkfl;
		if ($chooseMonth<'2015-09'){$Total+=$Jbjj;}
		
		
		$Jz=$myRow["Jz"];			//借支
		if ($Jz==0 && $From!="slist"){
			include "kqcode/staffwage_jz.php";
		}
		
		$Sb=$myRow["Sb"];			//社保
        $Gjj=$myRow["Gjj"];			//住房公积金
		$Ct=$myRow["Ct"];			//餐费
		$RandP=$myRow["RandP"];
		$Otherkk=$myRow["Otherkk"];		//其它扣款
		$AmountSys=$Total-$Jz-$Sb-$Gjj-$Ct-$RandP-$Otherkk;		//实付
		
		$Amount=$myRow["Amount"];
		$KqSignStr=$KqSign==1?"※":"●";
		$Estate=$myRow["Estate"];//结付标记
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$LockRemark="";
		if ($From=="slist"){
			$chooseMonth=$SMonth;
			}
		switch($Estate){
			case "3"://未结付
				$EstateSign="<div class='yellowB'>已核</div>";
				$LockRemark="记录已经审核,强制锁定!修改需财务退回.";
				break;
			case "0"://已结付
				$EstateSign="<div class='greenB'>已付</div>";
				$LockRemark="记录已经结付,强制锁定!修改需取消结付.";
				break;
			default://未审核
				$EstateSign="<div class='redB'>未核</div>";
				break;
			}
		//工龄计算
		include "subprogram/staff_model_gl.php";

				
		if ($chooseCurrency>1) $Rate=1;
		
		$sumDx+=$Dx*$Rate;			$sumGljt+=$Gljt*$Rate;			$sumGwjt+=$Gwjt*$Rate;		$sumJj+=$Jj*$Rate;  $sumJbjj+=$Jbjj*$Rate;
		$sumShbz+=$Shbz*$Rate;				$sumJtbz+=$Jtbz*$Rate;     	$sumJbf+=$Jbf*$Rate;
		$sumYxbz+=$Yxbz*$Rate;		$sumtaxbz+=$taxbz*$Rate;	$sumJz+=$Jz*$Rate;				$sumSb+=$Sb*$Rate;
		$sumGjj+=$Gjj*$Rate;			$sumCt+=$Ct*$Rate;				$sumKqkk+=$Kqkk*$Rate; 		$sumdkfl+=$dkfl*$Rate;
		$sumRandP+=$RandP*$Rate;		$sumYwjj+=$Ywjj*$Rate;	$sumStudybz+=$Studybz*$Rate; $sumHousebz+=$Housebz*$Rate; 					
		$sumOtherkk+=$Otherkk*$Rate;
		$sumAmount+=$Amount*$Rate;
		$sumTotal+=$Total*$Rate;	
				
		$Dx=SpaceValue0($Dx);
		$Gljt=SpaceValue0($Gljt);
		$Gwjt=SpaceValue0($Gwjt);
		$Jj=SpaceValue0($Jj);
		//$Jbjj=SpaceValue0($Jbjj);
		$Ywjj=SpaceValue0($Ywjj);
		$Shbz=SpaceValue0($Shbz);
		$Zsbz=SpaceValue0($Zsbz);
		$Studybz=SpaceValue0($Studybz);
		$Housebz=SpaceValue0($Housebz);
		$Jtbz=SpaceValue0($Jtbz);
		$Jbf=SpaceValue0($Jbf);	
		$Yxbz=SpaceValue0($Yxbz);
		$taxbz=SpaceValue0($taxbz);
		$Jz=SpaceValue0($Jz);
		$Gjj=SpaceValue0($Gjj);
		$Ct=SpaceValue0($Ct);
		$Kqkk=SpaceValue0($Kqkk);
		$dkfl=SpaceValue0($dkfl); 
		$RandP=SpaceValue0($RandP);
		$Otherkk=SpaceValue0($Otherkk);
		$Total=SpaceValue0($Total);
		if($KqSign<3){//加入考勤连接以便查询
			if($factoryCheck == "yes"){
				$Jbf="<a href='kq1_checkio_count.php?CountType=1&Number=$Number&CheckMonth=$chooseMonth' target='_blank'>$Jbf</a>";//验厂
			}
			else{
			    /*
				$headURL = "http://10.0.10.1/Public";
				if($cSign == '3'){
					$headURL = "http://10.0.10.2/Public";
				}
				*/
				$Jbf="<a href='kq_checkio_count.php?CountType=1&Number=$Number&CheckMonth=$chooseMonth' target='_blank'>$Jbf</a>";//内部
			}
		}
		if ($Kqkk>0){
             $Kqkk="<a href='staffwage_kqkk.php?Number=$Number&chooseMonth=$chooseMonth&Name=$strName' target='_blank'>$Kqkk</a>";
             }	
         $CertificateResult=mysql_fetch_array(mysql_query("SELECT Picture FROM $DataPublic.staffwage_otherkk  WHERE Number=$Number AND Month='$chooseMonth' LIMIT 1",$link_id));
         $CertificatePicture=$CertificateResult["Picture"];
         if($CertificatePicture!=""){
                 $Otherkk="<a href=\"staffwage_otherkk_view.php?Number=$Number&Month=$chooseMonth\"   target=\"_blank\"  style='CURSOR: pointer;color:#FF6633'>$Otherkk</a>";
                 }
        $Number=$Number."|".$chooseMonth;

		$AmountSys=round($AmountSys);
		if($AmountSys!=$Amount){
			echo "$Name 实付：$AmountSys   系统保存： $Amount <br>  ";
			$Amount="<div class='redB'>$Amount</div>";
		}
		else{
		    $Amount=SpaceValue0($Amount);//要放上述代码后面，不然0与空值对比会显示
		}
        $gzcheckStr="<input name='newcheckid[]' type='hidden' id='newcheckid$i' value='$Id'>";
        
        if ($Ywjj>0){
	        $YwFilePath="../download/ywjj/yw_" . $Id . ".jpg";
	        if (file_exists($YwFilePath)){
		      $Ywjj= "<a href=\"$YwFilePath\"   target=\"_blank\"  style='CURSOR: pointer;color:#FF6633'>$Ywjj</a>";
	        }
        }
        
        if ($dkfl>0){
	        $flFilePath="../download/dkfl/fl_" . $Id . ".jpg";
	        if (file_exists($flFilePath)){
		      $dkfl= "<a href=\"$flFilePath\"   target=\"_blank\"  style='CURSOR: pointer;color:#FF6633'>$dkfl</a>";
	        }
        }
        
        $cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		if($From!="slist"){	
			$ValueArray=array(
				array(0=>$KqSignStr,	1=>"align='center'"),
				array(0=>$cSign,	1=>"align='center'"),
				array(0=>$WorkAdd,	1=>"align='center'"),
				array(0=>$Branch,		1=>"align='center'"),
				array(0=>$Job,			1=>"align='center'"),
				array(0=>$GroupName,			1=>"align='center'"),
				array(0=>$Name,			1=>"align='center' $qjcolor"),
				array(0=>$Gl, 			1=>"align='right'"),
				array(0=>$Symbol, 	1=>"align='center'"),
				array(0=>$Dx, 			1=>"align='center'"),
				array(0=>$Jbf,			1=>"align='center'"),
				//array(0=>$Jbjj,			1=>"align='center'"),
				array(0=>$Gljt, 		1=>"align='center'"),
				array(0=>$Gwjt, 		1=>"align='center'"),
				array(0=>$Shbz,			1=>"align='center'"),
				array(0=>$Jtbz, 		1=>"align='center'"),
				array(0=>$Studybz, 		1=>"align='center'"),
				array(0=>$Housebz, 		1=>"align='center'"),
				array(0=>$Jj,			1=>"align='center'"),
				array(0=>$Ywjj,			1=>"align='center'"),
				array(0=>"<div class='redB'>".$Kqkk."</div>", 1=>"align='center'"),
				array(0=>"<div class='redB'>".$dkfl."</div>", 1=>"align='center'"),
				array(0=>$Total, 1=>"align='center'"),
				array(0=>"<div class='redB'>".$Sb."</div>", 1=>"align='right'"),
			    array(0=>"<div class='redB'>".$Gjj."</div>", 1=>"align='center'"),
				array(0=>"<div class='redB'>".$RandP."</div>", 	1=>"align='center'"),
				array(0=>$Amount,		1=>"align='center'"),
				array(0=>$EstateSign, 	1=>"align='center'"),
				array(0=>$Remark,		1=>"align='center'")
				);
		    }
		else
			{
			$ValueArray=array(
			   
				array(0=>$KqSignStr.$gzcheckStr,	1=>"align='center'"),
				array(0=>$cSign,	1=>"align='center'"),
				array(0=>$chooseMonth,		1=>"align='center'"),
				array(0=>$WorkAdd,	1=>"align='center'"),
				array(0=>$Branch,		1=>"align='center'"),
				array(0=>$Job,			1=>"align='center'"),
				array(0=>$GroupName,			1=>"align='center'"),
				array(0=>$Name,			1=>"align='center'"),
				array(0=>$Gl, 			1=>"align='right'"),
				array(0=>$Symbol, 	1=>"align='center'"),
				array(0=>$Dx, 			1=>"align='center'"),
				array(0=>$Jbf,			1=>"align='center'"),
				//array(0=>$Jbjj,			1=>"align='center'"),
				array(0=>$Gljt, 		1=>"align='center'"),
				array(0=>$Gwjt, 		1=>"align='center'"),
				array(0=>$Shbz,			1=>"align='center'"),
				array(0=>$Jtbz, 		1=>"align='center'"),
				array(0=>$Studybz, 		1=>"align='center'"),
				array(0=>$Housebz, 		1=>"align='center'"),
				array(0=>$Jj,			1=>"align='center'"),
				array(0=>$Ywjj,			1=>"align='center'"),
				array(0=>"<div class='redB'>".$Kqkk."</div>", 1=>"align='center'"),
				array(0=>"<div class='redB'>".$dkfl."</div>", 1=>"align='center'"),
				array(0=>$Total, 1=>"align='center'"),
				array(0=>"<div class='redB'>".$Sb."</div>", 1=>"align='center'"),
				array(0=>"<div class='redB'>".$Gjj."</div>", 1=>"align='center'"),
				array(0=>"<div class='redB'>".$RandP."</div>", 	1=>"align='center'"),
				array(0=>$Amount,		1=>"align='center'"),
				array(0=>$EstateSign, 	1=>"align='center'"),
				array(0=>$Remark,		1=>"align='center'")
				);				
			}
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
$sumDx=SpaceValue0($sumDx);
$sumJbf=SpaceValue0($sumJbf);$sumGljt=SpaceValue0($sumGljt);$sumGwjt=SpaceValue0($sumGwjt);$sumJj=SpaceValue0($sumJj);$sumJbjj=SpaceValue0($sumJbjj);$sumYwjj=SpaceValue0($sumYwjj);
$sumShbz=SpaceValue0($sumShbz);$sumZsbz=SpaceValue0($sumZsbz);$sumJtbz=SpaceValue0($sumJtbz);$sumYxbz=SpaceValue0($sumYxbz);$sumtaxbz=SpaceValue0($sumtaxbz);$sumKqkk=SpaceValue0($sumKqkk);$sumdkfl=SpaceValue0($sumdkfl);
$sumRandP=SpaceValue0($sumRandP);$sumStudybz=SpaceValue0($sumStudybz);$sumHousebz=SpaceValue0($sumHousebz);
$sumTotal=SpaceValue0($sumTotal);$sumHolidayjb=SpaceValue0($sumHolidayjb);$sumJz=SpaceValue0($sumJz);
$sumOtherkk=SpaceValue0($sumOtherkk);$sumAmount=SpaceValue0($sumAmount);$sumGjj=SpaceValue0($sumGjj);$sumCt=SpaceValue0($sumCt);
$m=1;

if($From!="slist"){	

	$ValueArray=array(
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"<div >".$sumDx."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumJbf."</div>", 1=>"align='center'"),
		//array(0=>"<div >".$sumJbjj."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumGljt."</div>", 	1=>"align='center'"),
		array(0=>"<div >".$sumGwjt."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumShbz."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumJtbz."</div>", 	1=>"align='center'"),
		array(0=>"<div >".$sumStudybz."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumHousebz."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumJj."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumYwjj."</div>", 1=>"align='center'"),	
		array(0=>"<div >".$sumKqkk."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumdkfl."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumTotal."</div>",1=>"align='center'"),
		array(0=>"<div >".$sumSb."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumGjj."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumRandP."</div>", 	1=>"align='center'"),
		array(0=>"<div >".$sumAmount."</div>", 1=>"align='center'"),
		array(0=>"&nbsp;&nbsp;"	),
		array(0=>"&nbsp;"	)
		);
	}
else{
	$ValueArray=array(
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
		array(0=>"&nbsp;"	),
        array(0=>"<div >".$sumDx."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumJbf."</div>", 1=>"align='center'"),
		//array(0=>"<div >".$sumJbjj."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumGljt."</div>", 	1=>"align='center'"),
		array(0=>"<div >".$sumGwjt."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumShbz."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumJtbz."</div>", 	1=>"align='center'"),
		array(0=>"<div >".$sumStudybz."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumHousebz."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumJj."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumYwjj."</div>", 1=>"align='center'"),	
		array(0=>"<div >".$sumKqkk."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumdkfl."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumTotal."</div>",1=>"align='center'"),
		array(0=>"<div >".$sumSb."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumGjj."</div>", 1=>"align='center'"),
		array(0=>"<div >".$sumRandP."</div>", 	1=>"align='center'"),
		array(0=>"<div >".$sumAmount."</div>", 1=>"align='center'"),
		array(0=>"&nbsp;&nbsp;"	),
		array(0=>"&nbsp;"	)
		);				
	}
	
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