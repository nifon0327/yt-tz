<?php   
include "../model/modelhead.php";
$From=$From==""?"m":$From;
//需处理参数
$ColsNumber=14;				
$tableMenuS=600;
ChangeWtitle("$SubCompany PI变更交期审核列表");
$funFrom="yw_pichange";
$nowWebPage=$funFrom."_m";
$Th_Col="操作|55|序号|30|客户|100|PO|80|中文名|280|Product Code|150|Unit|40|Price|55|Qty|50|Amount|70|PI|90|当前交期|80|更改后交期|80|当前采购交期|80|更改后<br>采购交期|80|更改原因|180|操作时间|70|操作员|55";	
$Pagination=$Pagination==""?0:$Pagination;	
$Page_Size = 100;
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	}
$i = 0;

$SearchRows .= " AND C.UpdateLeadtime <> 0 ";
	//客户项目
$projectSql = mysql_query("SELECT CD.Forshort FROM $DataIn.yw3_pileadtimechange C
	INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = C.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
	INNER JOIN $DataIn.productdata P ON P.ProductId = S.ProductId
	INNER JOIN $DataIn.packingunit U ON U.Id = P.PackingUnit
	INNER JOIN $DataIn.trade_object CD ON M.CompanyId = CD.CompanyId
	LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId = S.Id
	LEFT JOIN $DataIn.yw2_cgdeliverydate CG ON CG.POrderId = C.POrderId 
    WHERE 1 $SearchRows AND C.Estate =1 GROUP BY CD.Forshort",$link_id);

if($projectRow = mysql_fetch_array($projectSql)){
    echo "<select name='Forshort' id='Forshort' onchange='ResetPages(1)'>";
    echo "<option value='all' selected>全部项目</option>";
    do{
        $theForshort=$projectRow["Forshort"];
        $Forshort = $Forshort ==""?$theForshort:$Forshort;
        if($theForshort==$Forshort){
            echo "<option value='$theForshort' selected>$theForshort</option>";
            $SearchRows.=" AND CD.Forshort='$theForshort'";
        }
        else{
            echo "<option value='$theForshort'>$theForshort</option>";
        }
    }while($projectRow = mysql_fetch_array($projectSql));
    echo "</select>&nbsp;";
    $i++;
}

//楼栋层
$buildFloorSql = mysql_query("SELECT substring_index(P.cName, '-', 2) AS buildFloor FROM $DataIn.yw3_pileadtimechange C
	INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = C.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
	INNER JOIN $DataIn.productdata P ON P.ProductId = S.ProductId
	INNER JOIN $DataIn.packingunit U ON U.Id = P.PackingUnit
	INNER JOIN $DataIn.trade_object CD ON M.CompanyId = CD.CompanyId
	LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId = S.Id
	LEFT JOIN $DataIn.yw2_cgdeliverydate CG ON CG.POrderId = C.POrderId 
    WHERE 1 $SearchRows AND C.Estate =1 GROUP BY buildFloor",$link_id);

if($buildFloorRow = mysql_fetch_array($buildFloorSql)){
    echo "<select name='buildFloor' id='buildFloor' onchange='ResetPages(2)'>";
    echo "<option value='all' selected>全部栋层</option>";
    do{
        $thebuildFloor=$buildFloorRow["buildFloor"];
        $buildFloor = $buildFloor ==""?$thebuildFloor:$buildFloor;
        if($thebuildFloor==$buildFloor){
            echo "<option value='$thebuildFloor' selected>$thebuildFloor</option>";
            $SearchRows.=" AND P.cName LIKE '$thebuildFloor%'";
        }
        else{
            echo "<option value='$thebuildFloor'>$thebuildFloor</option>";
        }
    }while($buildFloorRow = mysql_fetch_array($buildFloorSql));
    echo "</select>&nbsp;";
    $i++;
}

//日期
//$changeDateSql = mysql_query("SELECT C.UpdateLeadtime FROM $DataIn.yw3_pileadtimechange C
//	INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = C.POrderId
//	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
//	INNER JOIN $DataIn.productdata P ON P.ProductId = S.ProductId
//	INNER JOIN $DataIn.packingunit U ON U.Id = P.PackingUnit
//	INNER JOIN $DataIn.trade_object CD ON M.CompanyId = CD.CompanyId
//	LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId = S.Id
//	LEFT JOIN $DataIn.yw2_cgdeliverydate CG ON CG.POrderId = C.POrderId
//    WHERE 1 $SearchRows AND C.Estate =1 GROUP BY C.UpdateLeadtime",$link_id);
//
//if($changeDateRow = mysql_fetch_array($changeDateSql)){
//    echo "<select name='changeDate' id='changeDate' onchange='ResetPages(3)'>";
//    do{
//        $thechangeDate=$changeDateRow["UpdateLeadtime"];
//        $changeDate = $changeDate ==""?$thechangeDate:$changeDate;
//        if($thechangeDate==$changeDate && $thechangeDate != 0 && $thechangeDate != null){
//            echo "<option value='changeDate' selected>更改交期</option>";
//            $SearchRows.=" AND C.UpdateLeadtime <> 0";
//        }
//        else{
//            echo "<option value='noDate'>设置交期</option>";
//            $SearchRows.=" AND C.UpdateLeadtime = 0";
//        }
//    }while($changeDateRow = mysql_fetch_array($changeDateSql));
//    echo "</select>&nbsp;";
//    $i++;
//}


echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select> $CencalSstr";
include "../model/subprogram/read_model_5.php";

echo '<script>
    function ResetPages(e) {
        switch (e) {
            case 1:
                document.forms["form1"].elements["buildFloor"].value = "";
                 
                document.form1.submit();
                break;
            case 2:
                document.form1.submit();
                break;
        }
    }
</script>';

//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT C.Id,C.UpdateLeadtime,C.Remark,C.Date,C.Operator,S.POrderId,S.OrderPO,S.Price,S.Qty,PI.PI,PI.Leadtime,P.TestStandard,P.cName,P.eCode,CD.Forshort,
P.ProductId,U.Name AS UnitName,C.ReduceWeeks AS UpdateReduceWeeks,CG.ReduceWeeks
FROM $DataIn.yw3_pileadtimechange  C 
INNER JOIN  $DataIn.yw1_ordersheet  S  ON S.POrderId=C.POrderId
INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
INNER JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
INNER JOIN $DataIn.trade_object CD ON M.CompanyId=CD.CompanyId
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
LEFT JOIN $DataIn.yw2_cgdeliverydate CG  ON CG.POrderId=C.POrderId
WHERE 1 $SearchRows  AND C.Estate=1 ";	
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$Remark=$myRow["Remark"];
		$Date=$myRow["Date"];
		$POrderId=$myRow["POrderId"];
		$OrderPO=$myRow["OrderPO"];
		$Price=$myRow["Price"];
		$Qty=$myRow["Qty"];
		$Amount=sprintf("%.2f",$Qty*$Price);
        $UnitName=$myRow["UnitName"];
		$PI=$myRow["PI"];
		$Leadtime=$myRow["Leadtime"];
		include "../model/subprogram/PI_Leadtime.php";
	    $Leadtime=$PIRemark==""?$Leadtime:"<div title='$PIRemark' style='color:#FF0000' >$Leadtime</div>";	

		$UpdateLeadtime=$myRow["UpdateLeadtime"];
      if ($UpdateLeadtime!="" && $UpdateLeadtime!="&nbsp;" ){
	      $UpdateLeadtime=str_replace("*", "", $UpdateLeadtime);
	      $updatedateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$UpdateLeadtime',1) AS PIWeek",$link_id));
          $updatePIWeek=$updatedateResult["PIWeek"];
    
         if ($updatePIWeek>0){
	          $updateweek=substr($updatePIWeek, 4,2);
		      $updatedateArray= GetWeekToDate($updatePIWeek,"m/d");
		      $updateweekName="Week " . $updateweek;
		      $updatedateSTR=$updatedateArray[0] . "-" .  $updatedateArray[1];
		      $UpdateLeadtime="<div >$updateweekName</div><div style='font-size:10px;color:#AAAAAA'>$updatedateSTR</div>";
	      }
      }
     $updateBgColor="";
     if($myRow["Leadtime"]!=$myRow["UpdateLeadtime"]){
               $updateBgColor="style='background:#FFA07A' title='$Remark'";
         }
    $ReduceWeeksBgColor="";
     $ReduceWeeks =$myRow["ReduceWeeks"];
      $UpdateReduceWeeks =$myRow["UpdateReduceWeeks"];
       if($ReduceWeeks!==$UpdateReduceWeeks){
            $ReduceWeeksBgColor="style='background:#FFA07A' title='$Remark'";
           }
      $ReduceWeeks=$ReduceWeeks==0?"同周":"前一周";
      $UpdateReduceWeeks=$UpdateReduceWeeks==0?"同周":"前一周";
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"];
		$ProductId=$myRow["ProductId"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,0);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏配件采购明细资料. $GQty!=$LQty ' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		
		$ValueArray=array(
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$OrderPO,1=>"align='center'"),
			array(0=>$TestStandard),
			array(0=>$eCode,1=>"align='center'"),
			array(0=>$UnitName,1=>"align='center'"),
			array(0=>$Price,1=>"align='center'"),
			array(0=>$Qty,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$PI,1=>"align='center'"),
			array(0=>$Leadtime,1=>"align='center'"),
			array(0=>$UpdateLeadtime, 1=>"align='center'$updateBgColor"),
			array(0=>$ReduceWeeks,1=>"align='center'"),
			array(0=>$UpdateReduceWeeks, 1=>"align='center'$ReduceWeeksBgColor"),
			array(0=>$Remark),
			array(0=>$Date, 1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
			);

		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
$ActioToS="1,17,15";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,16审核通过
include "../model/subprogram/read_model_menu.php";
?>

