<?php
//电信-joseph
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
$funFrom="fixed_maintain";

$cSigntmp=$_SESSION["Login_cSign"];
$cSignSTR=" AND D.cSign=$cSigntmp ";


?>
<style type="text/css">
<!--
.Table1{
  border:0px;
  width:1050px;
}
-->
</style>

<script LANGUAGE="JavaScript">
/*
function window.onload() {
	factory.printing.header ="";
  	factory.printing.footer ="";
  	factory.printing.portrait = false ;//纵向,false横向
	factory.printing.leftMargin =5;
  	factory.printing.topMargin = 1.5;
  	factory.printing.rightMargin =5;
  	factory.printing.bottomMargin = 0.5;
 }
 */
//  End -->
</script>
<body lang=ZH-CN>
<form  name="form1" id="form1" action="" >
<object id="factory" viewastext  style="display:none"
  classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814"
  codebase="http://www.middlecloud.com/basic/smsx.cab#Version=6,2,433,70">
</object>
<?php
$UserNumber=$Login_P_Number;  //剥出来，为了可以指定的人，正常下是本人自已
if ($from=="view"){  //fixed_maintain_view.php
	$FstrFrom=" AND D.Id=$FixedID ";
	echo "<input name='from' id='from' type='hidden' value='view' >";
	echo "<input name='FixedID' id='FixedID' type='hidden' value='$FixedID' >";
	$PassStr="?FixedID=$FixedID&from=view";
}
else {
	$FstrFrom=" AND F.User=$UserNumber ";
	$PassStr="";
}
?>

<P align="center" style="font-size:18px;">上海市研砼包装有限公司</P>
<P align="center" style="font-size:25px;font-weight:bold;">设备(日/周/半月)保养记录表  <a href="fixed_maintain_month.php <?php  echo $PassStr ?>" target="_self"  title='设备(月/季/年)保养记录表'><img src='../images/TheNext.gif' width='35' height='23' border='0'></a></P>
<center>
 <table cellpadding="0"  cellspacing="0" style="border:2px #478384 solid;">
  <tr align="center">
      <td>
        <table cellspacing="0" class="Table1" Width="1050">
          <tr align="center">
   	    <td width="72" height="35" class="A0101">设备名称</td>
            <td width="248" class="A0101" align="left">
           	<?php




				/*
				LEFT JOIN $DataPublic.oa2_fixedsubtype T ON T.Id=D.TypeId
				*/
				//$DaysID=1;   //剥出来 ，为了以后每月、每年的等
				$DaysIDStr=" (1,2,7) "; //日 或 周,及维护周期少于或等于15天的
				$DaysStr=" AND S.Days<=15 ";



				$DateTime=date("Y-m-d");
				$TodayDay=date("d");
				/*
				echo "select D.ID,D.CpName,D.Model,D.TypeId,D.BuyDate, B.Name AS Branch,M.Name as maintainer,S.DaysID,S.Days,K.CName
				FROM
				(
					SELECT *
						FROM (
						SELECT *
						FROM $DataPublic.fixed_userdata where UserType=2
						ORDER BY Sdate DESC
						)A
						GROUP BY Mid
					)F
				LEFT JOIN $DataPublic.fixed_assetsdata D ON D.Id=F.Mid
				LEFT JOIN (select  distinct TypeId, DaysID,Days from $DataPublic.oa3_maitaintype where DaysID in $DaysStr )
				S ON S.TypeId=D.TypeId
				LEFT JOIN $DataPublic.oa3_maitaindays K ON K.ID=S.DaysID
				LEFT JOIN $DataPublic.staffmain M ON M.Number=F.User
				LEFT JOIN $DataPublic.branchdata B ON B.Id=D.BranchId
				WHERE 1 $cSignSTR AND S.DaysID in $DaysStr AND F.User=$UserNumber order by S.DaysID    ";
				*/
				$tempCpName="&nbsp;";
				$tempBranch="&nbsp";
				$tempMaintainer="&nbsp";
				$providerSql= mysql_query("select D.ID,D.CpName,D.Model,D.TypeId,D.BuyDate, B.Name AS Branch,M.Name as maintainer,S.DaysID,S.Days,K.CName 
				FROM 
				(
					SELECT * 
						FROM (
						SELECT * 
						FROM $DataPublic.fixed_userdata where UserType=2
						ORDER BY Sdate DESC 
						)A
						GROUP BY Mid
					)F
				LEFT JOIN $DataPublic.fixed_assetsdata D ON D.Id=F.Mid
				LEFT JOIN (select  distinct S.TypeId, S.DaysID,S.Days from $DataPublic.oa3_maitaintype S where S.DaysID in $DaysIDStr $DaysStr ) 
				S ON S.TypeId=D.TypeId
				LEFT JOIN $DataPublic.oa3_maitaindays K ON K.ID=S.DaysID
				LEFT JOIN $DataPublic.staffmain M ON M.Number=F.User
				LEFT JOIN $DataPublic.branchdata B ON B.Id=D.BranchId
				WHERE 1 AND D.Estate=1 $cSignSTR AND S.DaysID in $DaysIDStr $DaysStr $FstrFrom  order by S.DaysID  ",$link_id);  //S.DaysID=1 表示只是每天维护的
				if($providerRow = mysql_fetch_array($providerSql)){
					echo "<select name='CID' id='CID' onchange='zhtj(this.name)'>";
					$UniqueID=1;
					do{
						$CpName=$providerRow["CpName"];
						$Model=$providerRow["Model"];
						$TypeId=$providerRow["TypeId"];
						$Forshort=$Model.'-'.$CpName;
						$BuyDate=$providerRow["BuyDate"];
						$Branch=$providerRow["Branch"];
						$Maintainer=$providerRow["maintainer"];
						$thisID=$providerRow["ID"];
						$thisDaysID=$providerRow["DaysID"];
						$thisCName=$providerRow["CName"];
						$thisDays=$providerRow["Days"];
						if($thisDaysID==7) {  //只有自定义的才要标天数
							$thisCName="";
							$UndefineStr="$thisDays"."天";

						}
						//$CID=$CID==""?$thisID:$CID;
						//if($CID==$thisID){
						$CID=$CID==""?$UniqueID:$CID;
						if($CID==$UniqueID){

							//echo"<option value='$thisID' selected>$Forshort($thisCName$UndefineStr)</option>";
							echo"<option value='$UniqueID' selected>$Forshort($thisCName$UndefineStr)</option>";
							$tempCpName=$CpName;
							$tempBranch=$Branch;
							$tempMaintainer=$Maintainer;
							$tempTypeId=$TypeId;
							$tempBuyDate=$BuyDate;
							$FixedID=$thisID; //资产ID
							$DaysID=$thisDaysID;
							$Days=$thisDays;
							//$SearchRows.=" and M.CompanyId='$thisCompanyId'";
							}
						else{
							//echo"<option value='$thisID'>$Forshort($thisCName$UndefineStr)</option>";
							echo"<option value='$UniqueID'>$Forshort($thisCName$UndefineStr)</option>";
							}
						$UniqueID=$UniqueID+1;
						}while ($providerRow = mysql_fetch_array($providerSql));
					echo"</select>";
				}
			/*
			else{
						 echo "<SCRIPT LANGUAGE=JavaScript>; location.href='fixed_maintain_month.php" . $PassStr . "'; </script>";
				}
				*/
			echo "&nbsp;";
			$curnetMonth=date("Y-m");

			if ($DaysID=='1') {
				$EveryDaysID=1;  //只能每天的。
				if 	($SYYYYMM=="" || $curnetMonth==$SYYYYMM ) { //如果不是当月，则不能用
					echo "<input type='button'  name='SaveToday' id='SaveToday' title='今天全部合格(只对每天)' value='√'   onclick=\"SaveTodays('','Mtable1',0,'$TodayDay','$UserNumber','$EveryDaysID')\" />";
				}
			}

			$FixedIDSTR=anmaIn($FixedID,$SinkOrder,$motherSTR);
			$CpNameStr="<a href='fixed_maintain_view.php?f=$FixedIDSTR' target='_blank' title='点击进入保养详情'>$tempCpName</a>";
			?>



            </td>
    	    <td width="72"  class="A0101">设备编号</td>
            <td width="122" class="A0101"><?php  echo $CpNameStr?></td>
    	    <td width="72"  class="A0101">使用部门</td>
            <td width="122" class="A0101"><?php  echo $tempBranch?></td>
	    <td width="72"  class="A0101">保养人</td>
            <td width="97" class="A0101"><?php  echo $tempMaintainer?></td>
            <td width="72"  class="A0101">保养年月</td>
            <td width=""  class="A0100">
			<?php
			//这里可以看到前一年到现在的记录。
			$tempCurYM=$Date=date("Y-m");  //当前月，倒推至前十二个月
			$DateTime=date("Y-m-d");
			//echo "$tempBuyDate";
			echo"<select name='SYYYYMM' id='SYYYYMM' onchange='zhtj(this.name)'>";
			for ($i=0;$i<12;$i++) {
				$StartYYYMM=date("Y-m",strtotime("$DateTime-$i months"));
				//$SubOneMonth=$i;
				//$StartDate=date("Y-m",strtotime("$DateTime-$SubOneMonth months"))."-01"; //一个月的第一天
				$tempBuyMonth=substr($tempBuyDate,0,7);
				if ($StartYYYMM<$tempBuyMonth) //如果比购买日期还前，则直接跳出循环
				{
					break;
				}

				$SYYYYMM=SYYYYMM==""?$tempCurYM:$SYYYYMM;
					if ($StartYYYMM==$SYYYYMM){
					   echo"<option value='$StartYYYMM' selected>$StartYYYMM</option>";

					}else{
						echo"<option value='$StartYYYMM'>$StartYYYMM</option>";
					}
			}
			echo"</select>";
			//=$Date=date("Y-m");
			?></td>
        </tr>
      </table>
  </tr>

   <tr align="center">
      <td>
        <table cellspacing="0" class="Table1">
         <tr bgcolor="#CCCCCC" align="center">
            <td width="30" height="26" class="A0101">序号</td>
   	    <td width="165" class="A0101">保养检查项目</td>
            <?php
            //for ($i=1;$i<32;$i++){
			//$CurrentDate=date("Y-m-d");
			//$YYYYMM=$Date=date("Y-m");
			$weekarray=array("日","一","二","三","四","五","六",);
			$This_YYYYMM=$SYYYYMM==""?date("Y-m"):$SYYYYMM;
			//echo "$YYYYMM=$SYYYYMM";
            for ($i=1;$i<32;$i++){
				$DD=$i;
				if($i<10){$DD='0'."$i";}  //取得日
				$This_Date=$This_YYYYMM.'-'."$DD";
				$underline="";
				if ($This_Date==date("Y-m-d")) {
					$underline=" text-decoration:underline; color:#F0F";
					$TodayStr="今天：$This_Date &nbsp; ";
				}
				else {
					$TodayStr="$This_Date &nbsp; ";
				}

				$This_WDay=$weekarray[date('w',strtotime($This_Date))];  //0是星期天，6是星期六
				if($This_WDay=="日" || $This_WDay=="六") {

					echo "<td width='22' class='A0101'><div style='$underline' class='redB' title='$TodayStr 星期$This_WDay'>$i</div></td> ";

				}
				else {
                	echo "<td width='22' style='$underline' class='A0101'><div  title='$TodayStr 星期$This_WDay'>$i</div></td> ";
				}
             }
            ?>
        <td width="" class="A0100">备注</td>
      </tr></table>
  </tr>
  <?php
  //统计维护项目，如果不够18，则就18
    /*
	$Id_row=mysql_fetch_array(mysql_query("SELECT count(*) as Dcount FROM $DataPublic.oa3_maitaintype
										   WHERE 1  AND TypeId=$tempTypeId AND DaysID=$DaysID  ",$link_id));    //是否有生复的记录
	$Dcount=$Id_row["Dcount"];
    */
	$Dcount=1;
	$StockResult = mysql_query("SELECT * FROM $DataPublic.oa3_maitaintype 
										   WHERE 1  AND TypeId=$tempTypeId AND DaysID=$DaysID AND Days=$Days  ",$link_id);
if  ($StockResult )
	if($StockRows = mysql_fetch_array($StockResult)){

		do{

  //for ($j=1;$j<19;$j++){
	 $Curtable='Mtable'.$Dcount;
	?>
   <tr align="center">
      <td>
        <table cellspacing="0" class="Table1" id="<?php  echo $Curtable?>" >
         <tr align="center">

            <?php
            echo "<td width='30' height='26' class='A0101'><b>$Dcount</b></td> ";
            echo "<td width='165'  class='A0101' align='left'>";
			$maitainID=$StockRows["Id"];
			$SubName=$StockRows["Name"];
			echo "$SubName";  //维护项目
			echo "</td> ";
			$CurrentDate=date("Y-m-d");
			//$YYYYMM=$Date=date("Y-m");
			$YYYYMM=$SYYYYMM==""?date("Y-m"):$SYYYYMM;
			//echo "$YYYYMM=$SYYYYMM";
            for ($i=1;$i<32;$i++){
				$DD=$i;
				if($i<10){$DD='0'."$i";}  //取得日

				$CycleDate=$YYYYMM.'-'."$DD";
				$passvalue="$FixedID|$maitainID|$DaysID|$SubName|$CycleDate|$Days";
				//echo "if (($CycleDate<=$CurrentDate) && ($CycleDate>=$tempBuyDate))";
				if (($CycleDate<=$CurrentDate) && ($CycleDate>=$tempBuyDate))
				{

				/*
				echo "select S.Question,S.Operator
				FROM $DataPublic.fixed_m_main M
				LEFT JOIN $DataPublic.fixed_m_sheet S ON S.MID=M.ID
				WHERE M.CycleDate='$CycleDate' and M.DaysID='$DaysID' AND M.FixedID='$FixedID' AND S.maitainID='$maitainID'";
				*/
				$M_ID="";
				$Question_Question="";
				$Question_Operator="";
					//查找是否正常,如不正常则显示问题


				$ConditioSTR=" AND M.CycleDate='$CycleDate'";
				$QuestionSql= mysql_query("select M.ID,M.CycleDate 
				FROM $DataPublic.fixed_m_main M
				LEFT JOIN $DataPublic.oa3_maitaintype S ON S.DaysID=M.DaysID 
				WHERE 1 $ConditioSTR $DaysStr and M.DaysID='$DaysID' and M.Days='$Days' AND M.FixedID='$FixedID' Order by M.CycleDate DESC Limit 1 ",$link_id);
				if($QuestionRow = mysql_fetch_array($QuestionSql)){
					do{
						$M_ID=$QuestionRow["ID"];
						$M_CycleDate=$QuestionRow["CycleDate"];
						$count_Temp=mysql_query("SELECT Question,Solution,Operator FROM $DataPublic.fixed_m_sheet 
												 WHERE CycleDate='$CycleDate' and Mid='$M_ID' AND maitainID='$maitainID' ",$link_id);  //
						//echo "SELECT count( * ) AS counts FROM $DataIn.cg1_lockstock WHERE StockId='$StockId";
						$Question_Question=mysql_result($count_Temp,0,"Question");
						$Question_Solution=mysql_result($count_Temp,0,"Solution");
						$Question_Operator=mysql_result($count_Temp,0,"Operator");

						//echo "$Question_Question: $Question_Operator";

					}while ($QuestionRow = mysql_fetch_array($QuestionSql));
				 }
					//$passvalue="$FixedID|$maitainID|$DaysID|$SubName|$CycleDate|$Days|$Question_Question|$Question_Solution";
					if ($Question_Question!=null && $Question_Question!="") { //表示当天有问题，

						echo "<td width='22' class='A0101' onmousedown='window.event.cancelBubble=true;' onclick='showCCDiv(\"fixed_maintain_mask.php\",\"$Curtable\",$Dcount,$i,500,\"$passvalue\")'>
						<div class='redB' title='$Question_Operator: $Question_Question \n $Question_Solution'>×</div>
						</td> ";

					}
					else {
						if ($M_ID!=""){  //表示当天保操作过设备正常
							echo "<td width='22' class='A0101' onmousedown='window.event.cancelBubble=true;' onclick='showCCDiv(\"fixed_maintain_mask.php\",\"$Curtable\",$Dcount,$i,500,\"$passvalue\")'>
							<div class='greenB' title='正常'>√</div>
							</td> ";
						}
						else {  //表示末操作或是周日

							if($Days>1 && $showSign=="") {  // 周，月，年等维护 说明是大于1，则判断最后一个维护日期，加上$Days, 如果小于当月，则把当月1号标示，如果大于本月，则全部显示空闲
								$Last_Temp=mysql_query("select M.CycleDate 
								FROM $DataPublic.fixed_m_main M
								WHERE 1  and M.DaysID='$DaysID' AND M.FixedID='$FixedID' Order by M.CycleDate DESC Limit 1 ",$link_id);
								/*
								echo "select M.CycleDate
								FROM $DataPublic.fixed_m_main M
								WHERE 1  and M.DaysID='$DaysID' AND M.FixedID='$FixedID' Order by M.CycleDate DESC Limit 1 <br> ";
								*/
								//echo "SELECT count( * ) AS counts FROM $DataIn.cg1_lockstock WHERE StockId='$StockId";
								$Last_Date=mysql_result($Last_Temp,0,"CycleDate");
								if($Last_Date!=null && $Last_Date!="") {
									$New_MaintainDate=date("Y-m-d",strtotime("$Last_Date+$Days days"));
									//echo "New_MaintainDate:$New_MaintainDate <br>";
									if($CycleDate>=$New_MaintainDate){
										$New_MaintainDate=$YYYYMM.'-'."01";  //一个月第一日标示
										$showSign="△";
									}
								}
								else {  //如果从来没有维护过，则标示当月第一日
									$New_MaintainDate=$YYYYMM.'-'."01";  //一个月第一日标示
									$showSign="△"; //
								}

							}
							if ($showSign=="△") {  //应该维护的日期
								echo "<td width='22' class='A0101' onmousedown='window.event.cancelBubble=true;' onclick='showCCDiv(\"fixed_maintain_mask.php\",\"$Curtable\",$Dcount,$i,500,\"$passvalue\")'>
														<div  class='blueB' title='应维护的日期'>△</div>
														</td> ";
								$showSign="◎";
							}
							else {
								echo "<td width='22' class='A0101' onmousedown='window.event.cancelBubble=true;' onclick='showCCDiv(\"fixed_maintain_mask.php\",\"$Curtable\",$Dcount,$i,500,\"$passvalue\")'>
														<div  title='当天空闲'>◎</div>
														</td> ";
							}

						} //表示末操作或是周日
					}
				}else{  //表示日期末到
                	echo "<td width='22' class='A0101'>&nbsp;</td> ";
				}
               }
            ?>
        <td width="" class="A0100">&nbsp;</td>
      </tr></table>
  </tr>
  <?php
  		$Dcount++;
	 }while($StockRows = mysql_fetch_array($StockResult));
  }
  ?>

 	<?php   //如果不够18就被空
   //$remain=18-$Dcount;
   for ($j=$Dcount;$j<19;$j++){
	?>
   <tr align="center">
      <td>
        <table cellspacing="0" class="Table1">
         <tr align="center">

            <?php
            echo "<td width='30' height='26' class='A0101'><b>$j</b></td> ";
            echo "<td width='165'  class='A0101' align='left'>";
			echo "&nbsp";

			echo "</td> ";
            for ($i=1;$i<32;$i++){
                echo "<td width='22' class='A0101'>&nbsp;</td> ";
               }
            ?>
        <td width="" class="A0100">&nbsp;</td>
      </tr></table>
  </tr>
  <?php  } ?>

  <tr align="center">
      <td>
        <table cellspacing="0" class="Table1">
         <tr  align="center"  height="35" >
            <td width="72" rowspan="2" class="A0101">保养说明</td>
            <td width="823" rowspan="2" class="A0101" align="left" valign="top">
                1、正常用“√”，空闲用“◎”，不正常或故障用“×”，提示维护日“△“表示。
            </td>
            <td width="72" class="A0101">保养检查签名</td>
            <td width="" class="A0100">
            <?php
				$tempMonth=$SYYYYMM==""?substr($DateTime,0,7):$SYYYYMM;  //获取月份的
				$count_Temp=mysql_query("SELECT CycleDate,Operator FROM $DataPublic.fixed_m_check 
								 WHERE substring(CycleDate,1,7)>='$tempMonth' and DaysID='$DaysID' AND Days='$Days' AND FixedID='$FixedID' order by CycleDate desc Limit 1 ",$link_id);
				/*
				echo "SELECT CycleDate,Operator FROM $DataPublic.fixed_m_check
								 WHERE substring(CycleDate,1,7)>='$tempMonth' and DaysID='$DaysID' AND FixedID='$FixedID' order by CycleDate desc Limit 1 ";
				*/
				if(mysql_num_rows($count_Temp))
				{
					$CheckDate=mysql_result($count_Temp,0,"CycleDate"); //当月的最后检查日期
					$CheckOperator=mysql_result($count_Temp,0,"Operator"); //栓查人
				}
				$CheckDate=$CheckDate==""?"&nbsp;":$CheckDate;
				//echo "CheckDate:$CheckDate CheckOperator: $CheckOperator";
				if ($passvalue!="") {
					if ($CheckOperator=="") {
						$CheckOperator="当月未核";
					}
					echo "<input type='button'  name='SaveToCheck' id='SaveToCheck' title='领导检查核实签名！不是保养成人签名!!' value='$CheckOperator'  
							onclick='SaveToChecks(\"$passvalue\",\"$DateTime\")' />";
					//<?php  echo $Date=date("Y-m-d")
				}
				else {
					echo "&nbsp;";
				}
			?>
            </td>
         </tr>
         <tr  align="center" height="35">
            <td width="72" class="A0101">最近检查日期</td>
            <td width="" class="A0100">
            <?php
				/*
				$tempMonth=$SYYYYMM==""?substr($DateTime,0,7):$SYYYYMM;  //获取月份的
				$count_Temp=mysql_query("SELECT CycleDate FROM $DataPublic.fixed_m_check
								 WHERE substring(CycleDate,1,7)>='$tempMonth' and DaysID='$DaysID' AND FixedID='$FixedID' order by CycleDate desc Limit 1 ",$link_id);

				$CheckDate=mysql_result($count_Temp,0,"CycleDate"); //当月的最后检查日期
				$CheckDate=$CheckDate==""?"&nbsp;":$CheckDate;
				*/
			?>
            <div id="CheckDate" ><?php  echo $CheckDate?> </div> </td>
      </tr></table>
  </tr>

	<?php /*
	$checkSql= mysql_query("
	SELECT M.OrderDate,S.OrderPO,S.POrderId,S.ProductId,S.Qty,P.cName,D.StuffCname,G.OrderQty,G.StockQty,(G.AddQty+G.FactualQty) AS CgQty,G.StuffId
	FROM $DataIn.yw1_ordersheet S
	LEFT JOIN $DataIn.yw1_ordermain M  ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
	LEFT JOIN $DataIn.stuffdata	D ON D.StuffId=G.StuffId
	LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId
	WHERE 1 AND P.cName LIKE '%FSC%' AND D.StuffCname LIKE 'FSC%' ORDER BY M.OrderDate,S.OrderPO,S.POrderId",$link_id);
	$i=1;
	if($checkRow=mysql_fetch_array($checkSql)){
		do{
			$OrderDate=$checkRow["OrderDate"];
			$OrderPO=$checkRow["OrderPO"];
			$ProductId=$checkRow["ProductId"];
			$Qty=$checkRow["Qty"];
			$cName=$checkRow["cName"];
			$StuffCname=$checkRow["StuffCname"];
			$StuffId=$checkRow["StuffId"];
			$OrderQty=$checkRow["OrderQty"];
			$StockQty=$checkRow["StockQty"];
			$CgQty=$checkRow["CgQty"];
			$POrderId=$checkRow["POrderId"];
			//以下需要通过PO和产品ID来计算
			//入库数量计算
			$checkRkSql= mysql_query("
			SELECT IFNULL(SUM(R.Qty),0) AS Qty
			FROM $DataIn.yw1_ordersheet S
			LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
			LEFT JOIN $DataIn.ck1_rksheet R  ON G.StockId=R.StockId
			WHERE 1 AND S.ProductId='$ProductId' AND S.POrderId='$POrderId' AND G.StuffId='$StuffId'",$link_id);
			$RkQty=mysql_result($checkRkSql,0,"Qty");
			//领料数量计算出货数量计算
			$checkChSql= mysql_query("
			SELECT IFNULL(SUM(S.Qty),0) AS Qty,CM.InvoiceNO
			FROM $DataIn.yw1_ordersheet S
			LEFT JOIN $DataIn.ch1_shipsheet CS ON CS.POrderId=S.POrderId
			LEFT JOIN $DataIn.ch1_shipmain CM ON CM.Id=CS.Mid
			WHERE 1 AND S.ProductId='$ProductId' AND S.POrderId='$POrderId' AND S.Estate=0",$link_id);
			if($checkChRow=mysql_fetch_array($checkChSql)){
				$ChQty=$checkChRow["Qty"];
				$InvoiceNO=$checkChRow["InvoiceNO"];
				}
			//库存数量计算
			$KcQty=$RkQty-$ChQty;
			//$RkQty=zerotospace($RkQty);
			//$ChQty=zerotospace($ChQty);
			//$KcQty=zerotospace($KcQty);
			//$RkQty=zerotospace($RkQty);
			echo"<tr>";
			echo"<td class=\"A0111\" align=\"center\">$OrderDate</td>";		//订单日期
			echo"<td class=\"A0101\">$OrderPO</td>";						//订单PO
			echo"<td class=\"A0101\">$cName</td>";							//产品名称
			echo"<td class=\"A0101\" align=\"right\">$Qty&nbsp;</td>";		//订单数量
			echo"<td class=\"A0101\">$StuffCname</td>";							//配件名称
			echo"<td class=\"A0101\" align=\"right\">$StockQty&nbsp;</td>";		//使用库存
			echo"<td class=\"A0101\" align=\"right\">$CgQty&nbsp;</td>";		//采购数量
			echo"<td class=\"A0101\" align=\"right\">$RkQty&nbsp;</td>";		//入库数量
			echo"<td class=\"A0101\" align=\"right\">$ChQty&nbsp;</td>";		//领实数量
			echo"<td class=\"A0101\" align=\"right\">0&nbsp;</td>";		//可用库存
			echo"<td class=\"A0101\" align=\"center\">$InvoiceNO&nbsp;</td>";	//出货文件
			echo"<td class=\"A0101\">&nbsp;</td>";								//发票号码
			echo"<td class=\"A0101\" align=\"right\">0&nbsp;</td>";				//成品库存
			echo"<td class=\"A0101\" align=\"right\">$ChQty&nbsp;</td>";		//出货数量
			echo"</tr>";
			$i++;
			}while($checkRow=mysql_fetch_array($checkSql));
		}
	*/?>
</table>

<input name="CCtable" id="CCtable" type="hidden" value="">
<input name="CCRow" id="CCRow" type="hidden" value="">
<input name="CCCol" id="CCCol" type="hidden" value="">

<?php

SetMaskDiv();//遮罩初始化
?>
</form>
</body>
</html>
<script LANGUAGE='JavaScript'  type="text/JavaScript">
function zhtj(obj){
	switch(obj){
		case "CID"://改变维护周期
		break;

		}
	document.form1.action="fixed_maintain_day.php";
	document.form1.submit();


}


function SaveTodays(WebPage,CTable,CRow,CCol,UserNumber,DaysID){  //把所有属于当前维护人的统一标示正常，那个有问题就改那个
	var myurl="../public/fixed_maintain_ajax_updated.php?UserNumber="+UserNumber+"&DayID="+DaysID+"&Action=SaveToday";
	//alert(myurl);
	//return false;
	CCol=(CCol)*1+1;
	var ajax=InitAjax();
	ajax.open("GET",myurl,true);
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4){// && ajax.status ==200

				eval(CTable).rows[0].cells[CCol].innerHTML="<div class='greenB' title=''>√</div>";


			//eval(CTable).rows[CRow].cells[CCol].innerHTML="<div class='redB' title='"+Question+"'>×</div>";
		}
	}
	ajax.send(null);
}


function SaveToChecks(passvalue,DateTime){  //领导检查确认表
	passvalue=encodeURIComponent(passvalue);
	var myurl="../public/fixed_maintain_ajax_updated.php?passvalue="+passvalue+"&Action=SaveCheck";
	//alert(myurl);
	//return false;
	var ajax=InitAjax();
	ajax.open("GET",myurl,true);
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4){// && ajax.status ==200
				document.getElementById('CheckDate').innerHTML=DateTime;
				//eval(CTable).rows[0].cells[CCol].innerHTML="<div class='greenB' title=''>√</div>";


			//eval(CTable).rows[CRow].cells[CCol].innerHTML="<div class='redB' title='"+Question+"'>×</div>";
		}
	}
	ajax.send(null);

}

function trimStr(stringToTrim) //除掉空格
{return stringToTrim.replace(/^\s+|\s+$/g,"");}


/////////遮罩层函数/////////////
function showCCDiv(WebPage,CTable,CRow,CCol,winWidth,passvalue){	//显示遮罩对话框
	document.getElementById('CCtable').value=CTable;
	document.getElementById('CCRow').value=CRow;  //
	document.getElementById('CCCol').value=CCol;

	document.getElementById('divShadow').style.display='block';
	divPageMask.style.width = document.body.scrollWidth;
	divPageMask.style.height = document.body.scrollHeight>document.body.clientHeight?document.body.scrollHeight:document.body.clientHeight;
	document.getElementById('divPageMask').style.display='block';
	sCCDiv(""+WebPage+"",CTable,CRow,CCol,winWidth,passvalue);
}

function closeCCDiv(){	//隐藏遮罩对话框
	document.getElementById('divShadow').style.display='none';
	document.getElementById('divPageMask').style.display='none';
	}

//对话层的显示和隐藏:层的固定名称divInfo,目标页面,传递的参数
function sCCDiv(WebPage,CTable,CRow,CCol,winWidth,passvalue){
		passvalue=encodeURIComponent(passvalue);
		var url="../public/"+WebPage+"?passvalue="+passvalue;
	　	//var show=eval("divInfo");
	　	var ajax=InitAjax();
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
	　		if(ajax.readyState==4){// && ajax.status ==200
				var BackData=ajax.responseText;
				divInfo.style.width=winWidth;
				divInfo.innerHTML=BackData;
				}
			}
		ajax.send(null);
		}

/////////////////////////////////
function CCCurentSave(passvalue)  //需要自已改变的函数
{

	var CTable=document.getElementById('CCtable').value;  //表
	var CRow=document.getElementById('CCRow').value+1;  //
	var CCol=(document.getElementById('CCCol').value)*1+1;  //
	//alert (CTable+"--"+CRow+"--"+CCol);
	//return false;
	var Question=document.getElementById('Question').value;
	Question=trimStr(Question);
	var Solution=document.getElementById('Solution').value;
	Solution=trimStr(Solution);

	//alert(passvalue);
	passvalue=encodeURIComponent(passvalue);
	var tempQuestion=Question;
	Question=encodeURIComponent(Question);

	var tempSolution=Solution;
	Solution=encodeURIComponent(Solution);

	var myurl="../public/fixed_maintain_ajax_updated.php?passvalue="+passvalue+"&Question="+Question+"&Solution="+Solution+"&Action=EveryDay";
	//alert(myurl);

	var ajax=InitAjax();
	ajax.open("GET",myurl,true);
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4){// && ajax.status ==200
			if (Question=="") {
				eval(CTable).rows[0].cells[CCol].innerHTML="<div class='greenB' title='"+tempQuestion+" \n "+tempSolution+"'>√</div>";
			}
			else{
				eval(CTable).rows[0].cells[CCol].innerHTML="<div class='redB' title='"+tempQuestion+" \n "+tempSolution+"'>×</div>";
			}

			//eval(CTable).rows[CRow].cells[CCol].innerHTML="<div class='redB' title='"+Question+"'>×</div>";
		}
	}
	ajax.send(null);
	closeCCDiv();
}



function CCSave($passvalue){  //可以公用
	//alert("Here");
	//passvalue=encodeURIComponent(passvalue);
	CCCurentSave($passvalue);  //这是这了通用而设成的CCCurentSave（）本身地存储，你自已定义的

}
</script>
