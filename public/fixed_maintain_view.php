<?php
/*
已更新
电信-joseph
*/
include "../model/modelhead.php";
//解密
$fArray=explode("|",$f);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$FixedID=anmaOut($RuleStr1,$EncryptStr1,"f");

$cSigntmp=$_SESSION["Login_cSign"];
$cSignSTR=" AND D.cSign=$cSigntmp ";
include "../model/subprogram/Getstaffname.php";
if($FixedID!=""){
		$StockResult = mysql_query("SELECT D.Id,D.CpName,D.Qty,D.price,C.CShortName,C.cSign,D.BranchId,K.CShortName as BuyCompany,D.BuycSign,D.Buyer,D.Model,D.TypeID,D.SSNumber,D.BuyDate,D.ServiceLife,D.MTCycle,D.Estate,D.Retiredate,D.Warranty,D.Attached,T.Name AS TypeName,D.Remark,D.Date,D.Operator,D.Locks,S.Id AS CId,D.CompanyId,S.Forshort,D.Operator 
	FROM $DataPublic.fixed_assetsdata D 
	LEFT JOIN $DataPublic.companys_group  C ON C.cSign=D.cSign
	LEFT JOIN $DataPublic.companys_group  K ON K.cSign=D.BuycSign
	LEFT JOIN $DataPublic.oa2_fixedsubtype T ON T.Id=D.TypeId
	LEFT JOIN $DataPublic.oa1_fixedmaintype O ON O.Id=T.MainTypeID
	LEFT JOIN $DataPublic.dealerdata S ON S.CompanyId=D.CompanyId
    WHERE D.Id='$FixedID' ",$link_id);

	if ($myRow = mysql_fetch_array($StockResult)) {


		$Id=$myRow["Id"];
		$BoxCode='8'.str_pad($Id,11,"0",STR_PAD_LEFT);  //条码不够位前边补800000****
		$BoxCode=GetCode($BoxCode,13,"0",1);  //获得条码: include "subprogram/Getstaffname.php";

		//使用的公司
		$CShortName=$myRow["CShortName"];
		$CShortName="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"fixed_assets_upMCID\",\"$Id\")' src='../images/edit.gif' alt='更改使用公司' width='13' height='13'>$CShortName";
		$cSign=$myRow["cSign"];

		//获取使用人员,维修人员表
		$User="&nbsp;";
		$UserDate="";
		$User_Date=GetSName_Date($Id,'fixed_userdata','1',$DataIn,$DataPublic,$link_id);
		//echo "User_Date:$User_Date";
		if($User_Date!=""){
			$Temp_User=explode('|',$User_Date);
			$User=$Temp_User[0];
			$UserDate=$Temp_User[1];
			//$User="<div  title='$UserDate'>$User</div>";
		}
		$User="<span  title='$UserDate'>$User</span>";
		$User="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"fixed_assets_upUser\",\"$Id\")' src='../images/edit.gif' alt='更新领用人' width='13' height='13'>$User";

		//$User=GetSName_Date($Id,'fixed_userdata','1',$DataIn,$link_id);
		$maintainer="&nbsp;";
		$maintainerDate="";
		$maintainer_Date=GetSName_Date($Id,'fixed_userdata','2',$DataIn,$DataPublic,$link_id);
		if($maintainer_Date!=""){
			$Temp_maintainer=explode('|',$maintainer_Date);
			$maintainer=$Temp_maintainer[0];
			$maintainerDate=$Temp_maintainer[1];
			//$maintainer="<div  title='$maintainerDate'>$maintainer</div>";
		}
		$maintainer="<span  title='$maintainerDate'>$maintainer</span>";
		//$maintainer="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"fixed_assets_upMaintainer\",\"$Id\")' src='../images/edit.gif' alt='维护记录操作' width='13' height='13'>$maintainer";

		//$maintainer=GetSName_Date($Id,'fixed_userdata','2',$DataIn,$link_id);

		$CpName=$myRow["CpName"];
		$TypeName=$myRow["TypeName"];
		$Qty=$myRow["Qty"];
		$price=$myRow["price"];
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"]==""?"&nbsp;":$myRow["Forshort"];
		$Idc=anmaIn($CompanyData,$SinkOrder,$motherSTR);
		$CId=$myRow["CId"];
		$Ids=anmaIn($CId,$SinkOrder,$motherSTR);
		//echo "$CompanyId <br>";
		if ($CompanyId==-1)
		{
			$CResult = mysql_query("SELECT Company FROM $DataPublic.company_assets WHERE Mid=$Id ",$link_id);
			//echo "SELECT Company FROM $DataPublic.company_assets WHERE Mid=$Id <br>";
			if($CRow = mysql_fetch_array($CResult)){
				$Company=$CRow["Company"];
				$KIdstr=anmaIn($Id,$SinkOrder,$motherSTR);
				//$Forshort="<a href='companyinfo_assets.php?Mid=$Id' target='_blank'>$Company</a>";
				$Forshort="<a href='companyinfo_assets.php?d=$KIdstr' target='_blank'>$Company</a>";
				}
		}
		else{
			$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$Forshort</a>";
		}

		$BranchId=$myRow["BranchId"];
		$Branch="&nbsp";
		if(($cSign==$cSigntmp)){
			$Branch=GetBranchName($BranchId,$DataIn,$DataPublic,$link_id);
		}

		$BuyCompany=$myRow["BuyCompany"];
		$Buyer=$myRow["Buyer"];
		$Model=$myRow["Model"];
		$IDdSTR=anmaIn($Id,$SinkOrder,$motherSTR);
		//$ModelStr="<a href='fixed_assets_view.php?d=$IDdSTR' target='_blank'>$Model</a>";
		$BoxCode="<a href='fixed_assets_view.php?d=$IDdSTR' target='_blank'>$BoxCode</a>";

		$TypeID=$myRow["TypeID"];
        $SSNumber=$myRow["SSNumber"]==""?"&nbsp;":$myRow["SSNumber"];
		$BuyDate=$myRow["BuyDate"];
		$tempBuyDate=$BuyDate;
		$ServiceLife=$myRow["ServiceLife"];   //使用年限
		$MTCycle=$myRow["MTCycle"];   //维修周期
		$Warranty=$myRow["Warranty"];    //保修年限


		$Date=$myRow["Date"];
		//$Remark=$myRow["Remark"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		//include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		//计算保修期是否已过
		$BDate=date("Y",strtotime($BuyDate))+$Warranty."-".date("m",strtotime($BuyDate))."-".date("d",strtotime($BuyDate));
		$BuyDate=$BDate>=date("Y-m-d")?"<span class='greenB'>$BuyDate</span>":"<span class='redB'>$BuyDate</span>";

		$Estate=$myRow["Estate"];
		if($Estate==1){ //在使用需要维护
			$NexMTDate=$maintainerDate==""?$tempBuyDate:$maintainerDate; //如果有最新维修期，否则用采购日期，则加上维修周期
			//echo "NexMTDate:$NexMTDate";
			$NexMTDate=date("Y-m-d",strtotime($NexMTDate."+ $MTCycle day"));
			//echo "NexMTDate:$NexMTDate :MTCycle:$MTCycle";
			if($NexMTDate<date("Y-m-d")){ //如果超过时间了，则显示红色
				$NexMTDate="<b style='color:#F00;'>$NexMTDate</b>";
			}
			else {
				$NexMTDate="<div>$NexMTDate</div>";
			}
		}
		switch($Estate){
			case 0:
				$Estate="<div class='redB' title='报废'>×报废</div>";
				break;
			case 1:
				$Estate="<div class='greenB' title='使用中'>√使用中</div>";
				break;
			case 3://配件名称审核中
				$Estate="<div class='yellowB' title='闲置'>√闲置</div>";
				break;
			}
		$Retiredate=$myRow["Retiredate"];
		//echo "Estate:$Estate";
                //获得附件
                 $Attached1="&nbsp;";
                 $Dir="download/fixedFile/";
                 $Dir=anmaIn($Dir,$SinkOrder,$motherSTR);
                 $AttachedResult = mysql_query("SELECT Id,FileName,Type  FROM $DataPublic.fixed_file WHERE Mid=$Id order by Type",$link_id);
                 while ($AttachedRow=mysql_fetch_array($AttachedResult)){
                     $FileName=$AttachedRow["FileName"];
                     $FileName=anmaIn($FileName,$SinkOrder,$motherSTR);
                     switch($AttachedRow["Type"]){
                       case 1:
                           $Model="<span onClick='OpenOrLoad(\"$Dir\",\"$FileName\")' style='CURSOR: pointer;color:#F00;'>$Model</span>"; break;
                       case 2:
                          $Attached1="<a href=\"../admin/openorload.php?d=$Dir&f=$FileName&Type=&Action=6\" target=\"download\">view</a>";break;
                          //$Attached1="<a href='../download/fixedFile/" . $FileName . "' target='_blank'>view</a>"; break;
                       case 3:
                           $Warranty="<span onClick='OpenOrLoad(\"$Dir\",\"$FileName\")' style='CURSOR: pointer;color:#F00;'>$Warranty</span>"; break;
                     }

                 }

	} ///if ($myrow = mysql_fetch_array($StockResult)) {
} //if($Id!=""){
$CpNameStr="<a href='fixed_maintain_day.php?FixedID=$FixedID&from=view' target='_blank' title='点击进入保养检查'>$CpName</a>";
?>
<body>
<P align="center" style="font-size:18px;">上海市研砼包装有限公司</P>
<P align="center" style="font-size:25px;font-weight:bold;">设备保养记录详情表  </P>
<center>
 <table cellpadding="0"  cellspacing="0" style="border:2px #478384 solid;">
  <tr align="center">
      <td>
        <table cellspacing="0" class="Table1" Width="1050">
          <tr align="center">
   	   		 <td width="72" height="35" class="A0101">设备名称</td>
            <td width="248" class="A0101" align="left"><?php  echo $Model?></td>
    	    <td width="72"  class="A0101">设备编号</td>
            <td width="122" class="A0101"><?php  echo $CpNameStr?></td>
    	    <td width="72"  class="A0101">使用部门</td>
            <td width="122" class="A0101"><?php  echo $Branch?></td>
	   		 <td width="72"  class="A0101">保养人</td>
            <td width="72" class="A0101"><?php  echo $maintainer?></td>
            <td width="72"  class="A0101">购买日期</td>
            <td width=""  class="A0100"><?php  echo $BuyDate?></td>
        </tr>
      </table>
      </td>
  </tr>


  <tr align="center">
      <td>
        <table cellspacing="0" class="Table1" Width="1050">
          <tr align="center">
    	     <td width="50"  class="A0101">序号</td>
   	   		 <td width="80" height="35" class="A0101">日期</td>
             <td width="" class="A0101">保养检查项目</td>
             <td width="80" class="A0101">分类</td>
    	    <td width="50"  class="A0101">状态</td>
    	    <td width="260"  class="A0101">问题</td>
	   		 <td width="260"  class="A0101">解决方法</td>
            <td width="72"  class="A0101">操作人</td>
        </tr>
        <?php
				$CheckIs=0; //0表示无检查日期，1表示有
				$CheckDate="";
				$CheckResult = mysql_query("select DaysID,Days,CycleDate,Operator 
				FROM $DataPublic.fixed_m_check M
				WHERE 1 AND  M.FixedID='$FixedID' Order by M.CycleDate  DESC  ",$link_id);
				if($CheckRow = mysql_fetch_array($CheckResult)){
					$CheckDate=$CheckRow["CycleDate"];
					$CheckOperator=$CheckRow["Operator"];
					$CheckIs=1;
				}

				$QuestionSql= mysql_query("select M.ID,M.Days,M.CycleDate,M.Operator as MOperator,S.Name,O.CName,Q.Question,Q.Solution,Q.Operator
				FROM $DataPublic.fixed_m_main M
				LEFT JOIN $DataPublic.oa3_maitaintype S ON S.DaysID=M.DaysID AND S.Days=M.Days 
				LEFT JOIN $DataPublic.oa3_maitaindays O ON O.ID=S.DaysID
				LEFT JOIN $DataPublic.fixed_m_sheet Q ON Q.Mid=M.Id AND Q.maitainID=S.ID
				WHERE 1 AND  M.FixedID='$FixedID' AND S.TypeID='$TypeID'  Order by M.Days DESC,M.CycleDate  DESC ",$link_id);
				/*
				echo "select M.ID,M.CycleDate,M.Operator as MOperator,S.Name,O.CName,Q.Question,Q.Solution,Q.Operator
				FROM $DataPublic.fixed_m_main M
				LEFT JOIN $DataPublic.oa3_maitaintype S ON S.DaysID=M.DaysID AND S.Days=M.Days
				LEFT JOIN $DataPublic.oa3_maitaindays O ON O.ID=S.DaysID
				LEFT JOIN $DataPublic.fixed_m_sheet Q ON Q.Mid=M.Id AND Q.maitainID=S.ID
				WHERE 1 AND  M.FixedID='$FixedID' Order by M.DaysId,M.CycleDate  DESC   ";

			    */

				if($QuestionRow = mysql_fetch_array($QuestionSql)){
					$i=1;
					do{
						$M_ID=$QuestionRow["ID"];
						$M_Days=$QuestionRow["Days"];
						$M_CycleDate=$QuestionRow["CycleDate"];

						$tempCycleDate=$tempCycleDate==""?$M_CycleDate:$tempCycleDate;
						//echo "if ($tempCycleDate!=$M_CycleDate) ";
						if (($tempCycleDate!=$M_CycleDate) || ($CheckDate >=$M_CycleDate)) { //表示换日期了,要检查
							$tempCycleDate=$M_CycleDate;
							//echo "if (($CheckDate>=$M_CycleDate) && ($CheckIs==1) ) { ";
							if (($CheckDate>=$M_CycleDate) && ($CheckIs==1) ) {  //检查点
									echo "
									 <tr align='center' style='background:#CCC'>
										<td   class='A0101'>△</td>
										<td   class='A0101' > $CheckDate </td>
										<td   colspan='5' class='A0101' >检查核实点 </td>
										<td   class='A0100' width='72' >$CheckOperator</td>
									</tr>  ";
									$CheckIs=0;
									if($CheckRow = mysql_fetch_array($CheckResult)){
										$CheckDate=$CheckRow["CycleDate"];
										$CheckOperator=$CheckRow["Operator"];
										$CheckIs=1;
									}
									else {
										$CheckIs=0;
									}
							}
						}


						$MOperator=$QuestionRow["MOperator"];
						$CDayName=$QuestionRow["CName"]."($M_Days"."天)";

						$SubName=$QuestionRow["Name"];
						$Question_Question=$QuestionRow["Question"];
						$Question_Solution=$QuestionRow["Solution"]==""?"&nbsp;":$QuestionRow["Solution"];
						$Question_Operator=$QuestionRow["Operator"]==""?"$MOperator":$QuestionRow["Operator"];


						$status="&nbsp;";
						//$Question_Solution="&nbsp;";
						if ($Question_Question==null || $Question_Question=="") { //表示当天
							$Question_Question="&nbsp;";
							$Question_Solution="&nbsp;";
							$status="<div class='greenB' title='正常'>√</div>";
						}
						else {

							$status="<div class='redB' title=''>×</div>";
						}
						//echo "$Question_Question: $Question_Operator";
					 echo "
					 <tr align='center'>
						<td   class='A0101'>$i</td>
						<td   class='A0101' > $M_CycleDate </td>
						<td   class='A0101' align='left'> $SubName </td>
						<td   class='A0101' align='left'>$CDayName</td>
						<td   class='A0101' >$status</td>
						<td   class='A0101' align='left'>$Question_Question</td>
						<td   class='A0101' align='left' > $Question_Solution </td>
						<td   class='A0100'>$Question_Operator</td>
					</tr>  ";
					$i=$i+1;
					}while ($QuestionRow = mysql_fetch_array($QuestionSql));
				 }
		?>


      </table>
     </td>
  </tr>

</table>
</body>
</html>
