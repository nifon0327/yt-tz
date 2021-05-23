<style type="text/css">
  <!--
  .moveLtoR {
    filter: revealTrans(Transition=6, Duration=0.3)
  }

  .moveRtoL {
    filter: revealTrans(Transition=7, Duration=0.3)
  }

  /* 为 DIV 加阴影 */
  .out {
    position: relative;
    background: #006633;
    margin: 10px auto;
    width: 400px;
  }

  .in {
    background: #FFFFE6;
    border: 1px solid #555;
    padding: 10px 5px;
    position: relative;
    top: -5px;
    left: -5px;
  }

  /* 为 图片 加阴影 */
  .imgShadow {
    position: relative;
    background: #bbb;
    margin: 10px auto;
    width: 220px;
  }

  .imgContainer {
    position: relative;
    top: -5px;
    left: -5px;
    background: #fff;
    border: 1px solid #555;
    padding: 0;
  }

  .imgContainer img {
    display: block;
  }

  .glow1 {
    filter: glow(color=#FF0000, strengh=2)
  }

  -->
  body, select, option {
    background: #f2f3f5 !important;
  }

  select, option {
    color: #949598 !important;
  }

  table {
    border-collapse: collapse;
  }

  tr {
    background-color: #fff;
  }

  td {
    height: 30px;
    border: 1px solid #e7e8e9;
    border-top: 0;
  }

  .shadow {
    -webkit-box-shadow: #e6e7e9 0px 0px 10px;
    -moz-box-shadow: #e6e7e9 0px 0px 10px;
    box-shadow: #e6e7e9 0px 0px 10px;
  }

  #TableHead tr {
    background-color: #F0F5F8;
  }

  body table:first-of-type tr ~ td:first-of-type {
    border: 0;
  }

  table:first-child td {
    border-top: 1px solid #e7e8e9;
  }

  table:nth-child(3) td {
    border-top: 1px solid #e7e8e9;
  }

  table:nth-child(6) td {
    border-top: 1px solid #e7e8e9;
  }

  #TableHead tr td {
    border: 0;
  }
</style>
<?php
include "../model/modelhead.php";
include "../basic/loading.php";
echo "<link rel='stylesheet' href='../model/mask.css'>";
$ColsNumber = 18;
$tableMenuS = 600;
ChangeWtitle("$SubCompany 待出订单分批出货处理");
$funFrom = "ch_shippinglist";
$From = $From == "" ? "split" : $From;
$nowWebPage = $funFrom . "_split";
$sumCols = "8,9";      //求和列,需处理

//echo "ABCDE";
// $Th_Col = "　|60|序号|40|PO|120|订单流水号|100|产品Id|50|中文名|220|Product Code|150|售价|60|订单总数|60|生产数量|60|已出数量|60|库存数量|60|本次出货|60|是否可出|60|出货方式|60|库位编号|100|待出备注|200|订单日期|100|数量更正|70";
//$Th_Col = "　|60|序号|40|PO|120|订单流水号|100|Product Code|150|库存数量|60|本次出货|60|是否可出|60|出货方式|60|库位编号|100|待出备注|200|订单日期|100|数量更正|70";

$Th_Col = "选项|60|序号|40|业务单号|120|订单流水号|100|产品条码|150|库存数量|60|本次出货|60|出货方式|60|库位编号|100|垛号|100|订单日期|100|盘点状态|60|盘点人|80|盘点日期|150|入库日期|120";
//$Th_Col = "选项|60|序号|40|业务单号|120|订单流水号|100|产品条码|150|库存数量|60|本次出货|60|出货方式|60|库位编号|100|待出备注|200|订单日期|100|数量更正|70";
$Pagination = 0;  //分页标志 0-不分页 1-分页
$Page_Size = 100;

$ActioToS = "149,150";
//$ActioToS="";
include "../model/subprogram/read_model_3.php";
$SearchRows = " AND SP.Estate = 1";

//最后入库数据
if (!$finally || $finally == "") {
    $finallyRes = mysql_query("SELECT S.PutawayDate,M.CompanyId,S.OrderPO,S.SeatId,SUBSTRING_INDEX(P.cName,'-',2) as BuildFloor 
	FROM $DataIn.ch1_shipsplit   SP  
     LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.trade_object T ON T.CompanyId = M.CompanyId
	LEFT JOIN $DataIn.yw7_clientOutData O ON O.Mid=SP.id
	LEFT JOIN $DataIn.taxtype X ON X.Id=S.taxtypeId
	LEFT JOIN $DataIn.yw7_clientOrderPo R ON R.Mid=SP.id
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	INNER JOIN $DataIn.producttype PT ON PT.TypeId=P.TypeId 
	LEFT JOIN $DataIn.productstock K ON K.ProductId = P.ProductId
	WHERE 1 $SearchRows  order by S.PutawayDate desc LIMIT 1", $link_id);
    if ($finallyRow = mysql_fetch_array($finallyRes)) {

        //$PutawayDate = $finallyRow["PutawayDate"];
        $project1 = $finallyRow["CompanyId"];
$OrderPO = $finallyRow["OrderPO"];
$SeatId = $finallyRow["SeatId"];
$BuildFloor = $finallyRow["BuildFloor"];

    }
}

//入库日期
$PutawayDateResult = mysql_query("SELECT DATE_FORMAT(S.PutawayDate,'%Y-%m-%d') AS PutawayDate 
	FROM $DataIn.ch1_shipsplit   SP  
     LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
	WHERE 1 $SearchRows	 and S.PutawayDate is not null group BY DATE_FORMAT(S.PutawayDate,'%Y-%m-%d') order by S.PutawayDate desc ", $link_id);
if ($PutawayDateRow = mysql_fetch_array($PutawayDateResult)) {
    echo "<div style='background-color: #F2F3F5;'>&nbsp;&nbsp;<select name='PutawayDate' id='PutawayDate' onchange='ResetPages(\"PutawayDate\")'>";
    echo "<option value='all' selected>全部入库日期</option>";
    do {
        $thisPutawayDate = $PutawayDateRow["PutawayDate"];
        if ($thisPutawayDate != ''|| $thisPutawayDate != null) {
            $PutawayDate = $PutawayDate == "" ? $thisPutawayDate : $PutawayDate;
            if ($PutawayDate == $thisPutawayDate) {
                echo "<option value='$thisPutawayDate' selected>$thisPutawayDate</option>";
                $SearchRows .= " and S.PutawayDate like '$thisPutawayDate%' ";
            } else {
                echo "<option value='$thisPutawayDate'>$thisPutawayDate</option>";
            }
        }
    } while ($PutawayDateRow = mysql_fetch_array($PutawayDateResult));
    echo "</select>&nbsp;";
}

//增加项目下拉筛选
$projectResult = mysql_query("SELECT T.CompanyId,T.Forshort
	FROM $DataIn.ch1_shipsplit SP  
  LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=SP.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.trade_object T ON T.CompanyId = M.CompanyId
	LEFT JOIN $DataIn.yw7_clientOutData O ON O.Mid=SP.id
	LEFT JOIN $DataIn.taxtype X ON X.Id=S.taxtypeId
	LEFT JOIN $DataIn.yw7_clientOrderPo R ON R.Mid=SP.id
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	LEFT JOIN $DataIn.productstock K ON K.ProductId = P.ProductId
	WHERE 1 $SearchRows and T.Forshort is not null group by T.CompanyId order by S.PutawayDate desc ", $link_id);
if ($projectRow = mysql_fetch_array($projectResult)) {
    echo "&nbsp;&nbsp;<select name='project1' id='project1' onchange='ResetPages(\"project1\")'>";
    echo "<option value='all' selected>全部项目</option>";
    do {
        $thisCompanyId = $projectRow["CompanyId"];
        $Forshort = $projectRow["Forshort"];
        $project1 = $project1 == "" ? $thisCompanyId : $project1;
        if ($project1 == $thisCompanyId) {
            echo "<option value='$thisCompanyId' selected>$Forshort</option>";
            $SearchRows .= " and T.CompanyId='$thisCompanyId' ";
        }
        else {
            echo "<option value='$thisCompanyId'>$Forshort</option>";
        }
    } while ($projectRow = mysql_fetch_array($projectResult));
    echo "</select>&nbsp;";
}

//栋层
$BuildFloorResult = mysql_query( "SELECT distinct SUBSTRING_INDEX(P.cName,'-',2) as BuildFloor
	FROM $DataIn.ch1_shipsplit   SP  
     LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.trade_object T ON T.CompanyId = M.CompanyId
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	WHERE 1 $SearchRows GROUP BY BuildFloor  order by BuildFloor ", $link_id);
if ($BuildFloorRow = mysql_fetch_array($BuildFloorResult)) {
    echo "&nbsp;&nbsp;<select name='BuildFloor' id='BuildFloor' onchange='ResetPages(\"BuildFloor\")'>";
    echo "<option value='all' selected>全部栋层</option>";
    do {
        $thisBuildFloor = $BuildFloorRow["BuildFloor"];
        $BuildFloorRes=explode("-",$thisBuildFloor);
        $BuildFloor = $BuildFloor == "" ? $thisBuildFloor : $BuildFloor;
        if ($BuildFloor == $thisBuildFloor) {
            echo "<option value='$thisBuildFloor' selected>$BuildFloorRes[0]#  $BuildFloorRes[1]F</option>";
            $SearchRows .= " and P.cName LIKE '$thisBuildFloor%' ";
        }
        else {
            echo "<option value='$thisBuildFloor'>$BuildFloorRes[0]#  $BuildFloorRes[1]F</option>";
        }
    } while ($BuildFloorRow = mysql_fetch_array($BuildFloorResult));
    echo "</select>&nbsp;";
}

//类型
$TypeResult = mysql_query( "SELECT P.TypeId,PT.TypeName
	FROM $DataIn.ch1_shipsplit   SP  
     LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.trade_object T ON T.CompanyId = M.CompanyId
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	INNER JOIN $DataIn.producttype PT ON PT.TypeId=P.TypeId 
	WHERE 1 $SearchRows GROUP BY P.TypeId order by S.PutawayDate desc ", $link_id);
if ($TypeRow = mysql_fetch_array($TypeResult)) {
    echo "&nbsp;&nbsp;<select name='TypeId' id='TypeId' onchange='ResetPages(\"TypeId\")'>";
    echo "<option value='all' selected>全部类型</option>";
    do {
        $thisTypeId = $TypeRow["TypeId"];
        $thisTypeName = $TypeRow["TypeName"];
        $TypeId = $TypeId == "" ? $thisTypeId : $TypeId;
        if ($TypeId == $thisTypeId) {
            echo "<option value='$thisTypeId' selected>$thisTypeName</option>";
            $SearchRows .= " and P.TypeId = '$thisTypeId' ";
        }
        else {
            echo "<option value='$thisTypeId'>$thisTypeName</option>";
        }
    } while ($TypeRow = mysql_fetch_array($TypeResult));
    echo "</select>&nbsp;";
}

//增加库位下拉筛选
$SeatIdResult = mysql_query("SELECT S.SeatId
	FROM $DataIn.ch1_shipsplit   SP  
     LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.trade_object T ON T.CompanyId = M.CompanyId
	LEFT JOIN $DataIn.yw7_clientOutData O ON O.Mid=SP.id
	LEFT JOIN $DataIn.taxtype X ON X.Id=S.taxtypeId
	LEFT JOIN $DataIn.yw7_clientOrderPo R ON R.Mid=SP.id
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	LEFT JOIN $DataIn.productstock K ON K.ProductId = P.ProductId
	WHERE 1 $SearchRows	 and S.SeatId is not null group BY S.SeatId order by S.PutawayDate desc ", $link_id);
if ($SeatIdRow = mysql_fetch_array($SeatIdResult)) {
    echo "&nbsp;&nbsp;<select name='SeatId' id='SeatId' onchange='ResetPages(\"SeatId\")'>";
    echo "<option value='all' selected>全部库位</option>";
    do {
        $thisSeatId = $SeatIdRow["SeatId"];
        $thisSeatId = $thisSeatId ==""?"未设置库位":$thisSeatId;
        $SeatId = $SeatId == "" ? $thisSeatId : $SeatId;
        if ($SeatId == $thisSeatId) {
            echo "<option value='$thisSeatId' selected>$thisSeatId</option>";
            if ($thisSeatId == "未设置库位"){
                $SearchRows .= " and S.SeatId is null ";
            }else{
                $SearchRows .= " and S.SeatId='$thisSeatId' ";
            }

        }
        else {
            echo "<option value='$thisSeatId'>$thisSeatId</option>";
        }
    } while ($SeatIdRow = mysql_fetch_array($SeatIdResult));
    echo "</select>&nbsp;";
}

$toExcelStr = "<a href='#'  target=\"download\" style='CURSOR: pointer; color:#949598; font-weight:bold' onclick='toExcel()' >ToExcel</a>";
echo "<div id='winDialog' style='position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCCCCC;' onDblClick='closeWinDialog()'></div>
";
echo "<input type='text' name='component' id='component' value='$component'><span class='ButtonH_25' onclick='var component = document.getElementById(\"component\").value; document.form1.submit();'>查询</span>";
echo "&nbsp;&nbsp;<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>&nbsp;&nbsp;";
//echo "<span  onclick='batchPassSeatId(this)'  class='btn-confirm' style='width: auto;font-size: 12px'>移库</span>&nbsp;&nbsp; ";
echo "<span  onclick='batchPassRkdata(this)'  class='btn-confirm' style='width: auto;font-size: 12px;margin: 0 10px;'>移垛</span>&nbsp;&nbsp;<span  onclick='batchInventory(this)'  class='btn-confirm' style='width: auto;font-size: 12px'>批量盘点</span></div>";

//<input type='button' id='batchButton' value='批量出货' onclick='batchUpdateSplit()' />
//检索条件隐藏值
echo " <input type='hidden' name='companyId' id='companyId' value='$CompanyId' />";
echo " <input type='hidden' name='SId' id='SId' value='$SeatId' />";
echo " <input type='hidden' name='build' id='build' value='$BuildFloor' />";
echo " <input type='hidden' name='Type' id='Type' value='$TypeId' />";
echo " <input type='hidden' name='finally' id='finally' value='$finally' />";
//步骤5：
//步骤6：需处理数据记录处理
echo "<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
//List_Title($Th_Col,"1",0);
function List_TitleYW($Th_Col, $Sign, $Height, $ToOutId, $CompanyId, $DataIn, $link_id, $nowWebPage)
{
    if ($Height == 1) {    //高度自动
        $HeightSTR = "";
    }
    else {
        $HeightSTR = "height='25'";
    }
    $Field = explode("|", $Th_Col);
    $Count = count($Field);
    if ($Sign == 1) {
        $tId = "id='TableHead'";
    }
    $tableWidth = 0;
    // add by zx 2011-0326
    for ($i = 0; $i < $Count; $i = $i + 2) {
        $j = $i;
        $k = $j + 1;
        //$tableWidth+=$Field[$j];
        $tableWidth += $Field[$k];
        //$tableWidth=$tableWidth+10;
    }
    if (isFireFox() == 1) {   //是FirFox 兼容IE,FIREFOX
        //echo "FireFox";
        $tableWidth = $tableWidth + $Count * 2;
    }

    if (isSafari6() == 1) {
        $tableWidth = $tableWidth + ceil($Count * 1.5) + 1;
    }


    if (isGoogleChrome() == 1) {
        $tableWidth = $tableWidth + ceil($Count * 1.5);
    }

    for ($i = 0; $i < $Count; $i = $i + 2) {
        if ($Sign == 1) {
            $Class_Temp = $i == 0 ? "A1111" : "A1101";
        }
        else {
            $Class_Temp = $i == 0 ? "A0111" : "A0101";
        }
        $j = $i;
        $k = $j + 1;
        //$tableWidth+=$Field[$j];
        //$tableWidth+=$Field[$k];
        if (isSafari6() == 0) {
            if ($k == ($Count - 1)) {  // add by zx 2011-0326  兼容IE,FIREFOX
                $Field[$k] = "";
            }
        }
        $h = $j + 2;
        if (($Field[$j] == "中文名" && $Field[$h] == "&nbsp;") || $Field[$j] == "&nbsp;") {
            if ($Sign == 1) {
                $Class_Temp = "";
            }
            else {
                $Class_Temp = "";
            }

        }

        if ($Field[$j] == "转发对象名称") {
            $inForT = "";
            $ToOutNameResult = mysql_query("SELECT  D.Id,D.ToOutName as Name ,C.Forshort 
					FROM $DataIn.ch1_shipsplit   SP   
					LEFT JOIN $DataIn.yw7_clientOutData O ON O.Mid=SP.id
					LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
					WHERE  O.Mid>0 AND D.Estate=1 AND SP.Estate>0  AND D.CompanyId='$CompanyId' group by O.ToOutId", $link_id);
            /*
                      echo "SELECT D.Id,D.ToOutName as Name ,C.Forshort
                      FROM $DataIn.yw7_clientToOut D
                      LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
                      WHERE  D.Estate=1 AND D.CompanyId='$CompanyId' ORDER BY D.Id";
                      */
            if ($ToOutNameRow = mysql_fetch_array($ToOutNameResult)) {
                $inForT = "<select name='ToOutId' id='ToOutId' onchange='RefreshPage(\"$nowWebPage\")'>";
                $inForT .= "<option value='' selected> $Field[$j] </option>";
                do {

                    $thisToOutId = $ToOutNameRow["Id"];
                    if ($ToOutId == $thisToOutId) {
                        $inForT .= "<option value='$ToOutNameRow[Id]' selected>$ToOutNameRow[Forshort]-$ToOutNameRow[Name]</option>";
                    }
                    else {
                        $inForT .= "<option value='$ToOutNameRow[Id]'>$ToOutNameRow[Name]</option>";
                    }
                } while ($ToOutNameRow = mysql_fetch_array($ToOutNameResult));
                $inForT .= "</select>&nbsp;";
            }
            else {
                $inForT .= "$Field[$j]";
            }


            $TableStr .= "<td width='$Field[$k]' Class=''> $inForT </td>";

        }
        else {
            $TableStr .= "<td width='$Field[$k]' Class=''>$Field[$j]</td>";
        }
    }

    echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' $tId><tr $HeightSTR align='center'>" . $TableStr . "</tr></table>";
    if ($Sign == 0) {
        echo "<iframe name=\"download\" style=\"display:none\"></iframe>";
    }
}

//***********************************************
List_TitleYW($Th_Col, "1", 1, $ToOutId, $CompanyId, $DataIn, $link_id, $nowWebPage);

if ($ToOutId != "") {
    $SearchRows .= " AND O.ToOutId='$ToOutId' AND O.Mid>0 ";
    //echo "SearchRows:$SearchRows";
}

if ($component != '') {
    $sqlAdd = " AND P.cName LIKE '%$component%' ";
}
$mySql = "SELECT M.OrderNumber,M.CompanyId,M.OrderDate,SP.Id,SP.Qty AS thisQty,SP.ShipType,S.OrderPO,S.POrderId,S.ProductId,S.SeatId,S.Qty,S.Price,S.PackRemark,S.PutawayDate,SP.shipSign,
P.cName,P.eCode,P.TestStandard,SP.Estate,SP.OrderSign,S.dcRemark,X.name as taxName,R.OrderPO as OutOrderPO,K.tStockQty,SP.inventory_estate,SP.inventory_checker,SP.inventory_time,ISI.StackNo 
	FROM $DataIn.ch1_shipsplit   SP  
     LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.trade_object T ON T.CompanyId = M.CompanyId
	LEFT JOIN $DataIn.yw7_clientOutData O ON O.Mid=SP.id
	LEFT JOIN $DataIn.taxtype X ON X.Id=S.taxtypeId
	LEFT JOIN $DataIn.yw7_clientOrderPo R ON R.Mid=SP.id
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	INNER JOIN $DataIn.producttype PT ON PT.TypeId=P.TypeId 
	LEFT JOIN $DataIn.productstock K ON K.ProductId = P.ProductId
	LEFT JOIN $DataIn.inventory_stackinfo ISI ON ISI.ID = S.StackId 
	WHERE 1 $SearchRows $sqlAdd  order by S.PutawayDate desc";
//if ($Login_P_Number==10058)
//echo $mySql;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    do {

        $m = 1;
        $LockRemark = "";
        $theDefaultColor = "#FFFFFF";
        $Id = $myRow["Id"];
        $OrderPO = $myRow["OrderPO"] == "" ? "&nbsp;" : $myRow["OrderPO"];

        $OutOrderPO = $myRow["OutOrderPO"];
        if ($OutOrderPO != "") {
            $OrderPO = $OutOrderPO;
        }
        $SeatId = $myRow["SeatId"];
        $StackNo = $myRow["StackNo"];
        $tStockQty = $myRow["tStockQty"];
        $inventory_estate = $myRow["inventory_estate"];
        if ($inventory_estate == null || $inventory_estate == 0) {
            $inventory_estate = '未盘';
        }
        else {
            $inventory_estate = '已盘';
        }
        $inventory_checker = $myRow["inventory_checker"];
        $inventory_time = $myRow["inventory_time"];
        $OrderDate = $myRow["OrderDate"];
        $POrderId = $myRow["POrderId"];
        $ProductId = $myRow["ProductId"] == "" ? "&nbsp;" : $myRow["ProductId"];
        $dcRemark = $myRow["dcRemark"] == "" ? "&nbsp;" : $myRow["dcRemark"];
        $Qty = $myRow["Qty"];
        $thisQty = $myRow["thisQty"];
        $Price = $myRow["Price"];
        $Amount = sprintf("%.2f", $Qty * $Price);

        $PutawayDate = $myRow["PutawayDate"];
        $cName = $myRow["cName"];
        $eCode = $myRow["eCode"];
        $Description = $myRow["Description"];
        $ShipType = $myRow["ShipType"] == '' ? '7' : $myRow["ShipType"];
        //出货方式
        if (strlen(trim($ShipType)) > 0) {
            $ShipType = "<image src='../images/ship$ShipType.png' style='width:20px;height:20px;'/>";
        }
        else $ShipType = "&nbsp;";
        $TestStandard = $myRow["TestStandard"];
        include "../admin/Productimage/getPOrderImage.php";

        //$ToOutName=$myRow["ToOutName"];
        $ToOutName = "&nbsp;";

        $OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
								  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
								  WHERE O.MId='$Id'", $link_id);
        //echo ""
        if ($Outmyrow = mysql_fetch_array($OutResult)) {
            //删除数据库记录
            //$Forshort=$myRow["Forshort"];
            $ToOutName = $Outmyrow["ToOutName"];
        }
        else {
            $OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
						  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
						  WHERE  O.POrderId='$POrderId' AND O.Mid=0 ", $link_id);
            //echo "";
            if ($Outmyrow = mysql_fetch_array($OutResult)) {
                //删除数据库记录
                //$Forshort=$myRow["Forshort"];
                $ToOutName = $Outmyrow["ToOutName"];
            }
        }

        $Estate = $myRow["Estate"];
        $CheckrkQty = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS rkQty 
		FROM $DataIn.yw1_orderrk R 
		WHERE R.POrderId='$POrderId' AND R.ProductId = '$ProductId' ", $link_id));
        $rkQty = $CheckrkQty["rkQty"];

        $CheckShipSignQty = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS shipQty 
		FROM $DataIn.ch1_shipsplit  
		WHERE POrderId='$POrderId'  AND shipSign = 1 AND Id !='$Id' ", $link_id));
        $ShipSignQty = $CheckShipSignQty["shipQty"]; //可以出，或者已经出货数量

        $stockSign = 1;
        if (($rkQty - $ShipSignQty) < $thisQty) {
            $stockSign = 0;
        }


        $CheckShipQty = mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS shipQty 
		FROM $DataIn.ch1_shipsheet  
		WHERE POrderId='$POrderId'", $link_id));
        $ShipQty = $CheckShipQty["shipQty"]; //已出数量

        $StockQty = $rkQty - $ShipQty;
        $StockQty = $StockQty == 0 ? "&nbsp;" : $StockQty;

        $OrderSign = $myRow["OrderSign"];
        if ($OrderSign > 0) $theDefaultColor = "#FFAEB9";//#E9FFF5


        $taxName = $myRow["taxName"] == "" ? "&nbsp;" : $myRow["taxName"];
        $shipSign = $myRow["shipSign"] == '' ? 1 : $myRow["shipSign"];
        $shipSignImage = "";
        $shipSignStr = " onmousedown='window.event.cancelBubble=true;'onclick='ChangShipSign($i,$Id)' style='CURSOR: pointer'";
        if ($shipSign == 1) {
            $shipSignImage = "<img src='../images/ok.gif' width='30' height='30'";
        }

        if ($stockSign == 0) {
            $shipSignImage = "<span class='blueB'>库存不足</span>";
            $shipSignStr = "";
        }

        $updateImage = "";
        $updateOnclick = "";
        $CheckSplitQty = mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ch1_shipsplit  
	   WHERE POrderId='$POrderId'", $link_id));
        $TotalSplitQty = $CheckSplitQty["Qty"];
        if ($TotalSplitQty > $Qty) {
            $updateImage = "<img src='../images/register.png' width='30' height='30'";
            $updateOnclick = "onclick='updateSplitQty(this,$Id,$POrderId)'";
        }


        if ($thisQty == ($rkQty - $ShipQty)) {
            $thisQty = "<span class='greenB'>$thisQty</span>";
        }
        else {
            $thisQty = "<span class='yellowB'>$thisQty</span>";
        }
        $rkQty = $rkQty > 0 ? "<span class='yellowB'>$rkQty</span>" : "&nbsp;";

        $showPurchaseorder = "<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='$TempStrtitle' width='13' height='13' style='CURSOR: pointer'>";
        $StuffListTB = "
				<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

        //echo "$POrderId:$Id:$ShipType";
        $ValueArray = array(
            array(0 => $OrderPO, 1 => "align='center'", 2 => " onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,2,$POrderId,25,$Id)' style='CURSOR: pointer'", 3 => "..."),
            array(0 => $POrderId, 1 => "align='center'"),
            //            array(0 => $ProductId, 1 => "align='center'"),
            //            array(0 => $TestStandard, 1 => "align='center'"),
            array(0 => $eCode, 1 => "align='center'"),
            //            array(0 => $Price, 1 => "align='center'"),
            //            array(0 => $Qty, 1 => "align='center'"),
            //            array(0 => $rkQty, 1 => "align='center'"),
            //            array(0 => $ShipQty, 1 => "align='center'"),
            array(0 => $StockQty, 1 => "align='center'"),
            array(0 => $thisQty, 1 => "align='center'"),
            //            array(0 => $shipSignImage, 1 => "align='center'", $shipSignStr),
            array(0 => $ShipType, 1 => "align='center'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,14,$POrderId,3,$Id)' style='CURSOR: pointer'"),
            array(0 => $SeatId, 1 => "align='center'"),
            array(0 => $StackNo, 1 => "align='center'"),
            //            array(0 => $dcRemark),
            array(0 => $OrderDate, 1 => "align='center'", 3 => "..."),
            array(0 => $inventory_estate, 1 => "align='center'", 3 => "..."),
            array(0 => $inventory_checker, 1 => "align='center'", 3 => "..."),
            array(0 => $inventory_time, 1 => "align='center'", 3 => "..."),
            //            array(0 => $updateImage, 1 => "align='center'", 2 => "$updateOnclick"),
            array(0 => $PutawayDate, 1 => "align='center'"),
        );
        $checkidValue = $Id;
        include "../model/subprogram/read_model_6.php";
        echo $StuffListTB;
    } while ($myRow = mysql_fetch_array($myResult));
}
else {
    noRowInfo($tableWidth);
}
//步骤7：
echo '</div>';
SetMaskDiv();//遮罩初始化
$myResult = mysql_query($mySql, $link_id);
$RecordToTal = mysql_num_rows($myResult);
include "../model/subprogram/read_model_menu.php";
?>
<script src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script src='../cjgl/cj_function.js' type=text/javascript></script>
<script>
function updateSplitQty(e, Id, POrderId) {

    var url = "ch_shippinglist_splitupdated.php?Id=" + Id + "&POrderId=" + POrderId + "&ActionId=updateSplitQty";
    var ajax = InitAjax();
    ajax.open("GET", url, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4 && ajax.status == 200) {
            var BackData = ajax.responseText;
            document.form1.submit();

        }
    }
    ajax.send(null);

}


function ChangShipSign(index, Id) {
    var tabIndex = "ListTable" + index;
    var url = "ch_shippinglist_splitupdated.php?Id=" + Id + "&ActionId=shipSign";
    var ajax = InitAjax();
    ajax.open("GET", url, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4 && ajax.status == 200) {
            var BackData = ajax.responseText;
            //alert(BackData)
            if (BackData == 1) document.getElementById(tabIndex).rows[0].cells[13].innerHTML = "<img src='../images/ok.gif' width='30' height='30'>";
            if (BackData == 0) document.getElementById(tabIndex).rows[0].cells[13].innerHTML = "&nbsp;";
        }
    }
    ajax.send(null);
}


function toExcel() {
    var Ids = "";
    var CompanyId = document.getElementById("CompanyId").value;
    var ListCheck = document.getElementsByName("checkid[]");
    for (var i = 0; i < ListCheck.length; i++) {
        if (ListCheck[i].checked) {
            Ids += i == 0 ? ListCheck[i].value : "," + ListCheck[i].value;
        }
    }
    if (Ids == "") {
        alert("请选择记录!");
        return false;
    }
    document.form1.action = "ch_shippinglist_split_toexcel.php?Ids=" + Ids + "&myCompanyId=" + CompanyId;
    document.form1.target = "toExcelFrame";
    document.form1.submit();
}


function updateJq(TableId, RowId, runningNum, toObj, sId) {//行即表格序号;列，流水号，更新源
    showMaskBack();  // add by zx 加入庶影   20110323  IE_FOX_MASK.js
    var InfoSTR = "";
    var buttonSTR = "";
    var theDiv = document.getElementById("Jp");
    var ObjId = document.form1.ObjId.value;
    var tempTableId = document.form1.ActionTableId.value;
    theDiv.style.top = event.clientY + document.body.scrollTop + 'px';
    if (toObj == 25) {
        theDiv.style.left = event.clientX + document.body.scrollLeft + 'px';
    }
    else {
        theDiv.style.left = event.clientX + document.body.scrollLeft - parseInt(theDiv.style.width) + 'px';
    }
    //theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
    if (theDiv.style.visibility == "hidden" || toObj != ObjId || TableId != tempTableId) {
        document.form1.ActionTableId.value = TableId;
        document.form1.ActionRowId.value = RowId;
        document.form1.ObjId.value = toObj;
        switch (toObj) {

            case 3://出货方式
                InfoSTR = "<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='12' class='TM0000' readonly/>订单出货方式<select id='ShipType' name='ShipType' style='width:150px;'><option value='' 'selected'>请选择</option>";

            <?PHP
            $shipTypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE  Estate=1 ORDER BY Id", $link_id);
            if ($TypeRow = mysql_fetch_array($shipTypeResult)) {
                do {
                    $echoInfo .= "<option value='$TypeRow[Id]'>$TypeRow[Name]</option>";
                } while ($TypeRow = mysql_fetch_array($shipTypeResult));
            }
            ?>
                InfoSTR = InfoSTR + "<?PHP echo $echoInfo; ?>" + "</select><br>";
                break;

            case 25:	//订单备注
                InfoSTR = "更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='12' class='INPUT0000' readonly> 的PO:<input name='OrderPO' type='text' id='OrderPO' size='20' class='INPUT0100'><br>";
                break;

            case 33://转发对象名称
                InfoSTR = "<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='12' class='TM0000' readonly/>转发对象名称<select id='ToOutName' name='ToOutName' style='width:150px;'><option value='' 'selected'>请选择</option>";
                //var CompanyId=document.getElementById("CompanyId").value;
            <?PHP

            $ToOutNameResult = mysql_query("SELECT D.Id,D.ToOutName as Name ,C.Forshort 
					FROM $DataIn.yw7_clientToOut D
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
					WHERE  D.Estate=1 AND D.CompanyId='$CompanyId' ORDER BY D.Id", $link_id);
            if ($ToOutNameRow = mysql_fetch_array($ToOutNameResult)) {
                do {
                    $OutInfo .= "<option value='$ToOutNameRow[Id]'>$ToOutNameRow[Forshort]-$ToOutNameRow[Name]</option>";
                } while ($ToOutNameRow = mysql_fetch_array($ToOutNameResult));
            }
            ?>
                InfoSTR = InfoSTR + "<?PHP echo $OutInfo; ?>" + "</select><br>";
                break;


        }

        if (toObj > 1) {
            var buttonSTR = "&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate(" + sId + ")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
        }
        infoShow.innerHTML = InfoSTR + buttonSTR;
        theDiv.className = "moveRtoL";
        if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
            theDiv.filters.revealTrans.apply();//防止错误
            theDiv.filters.revealTrans.play(); //播放
        }
        else {
            theDiv.style.opacity = 0.9;
        }
        theDiv.style.visibility = "";
        theDiv.style.display = "";
    }
}

function CloseDiv() {
    var theDiv = document.getElementById("Jp");
    theDiv.className = "moveLtoR";
    if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
        theDiv.filters.revealTrans.apply();
        //theDiv.style.visibility = "hidden";
        theDiv.filters.revealTrans.play();
    }
    theDiv.style.visibility = "hidden";
    //theDiv.filters.revealTrans.play();
    infoShow.innerHTML = "";
    closeMaskBack();    //add by zx 关闭庶影   20110323   add by zx 加入庶影   20110323  IE_FOX_MASK.js
}

function aiaxUpdate(sId) {
    var ObjId = document.form1.ObjId.value;
    var tempTableId = document.form1.ActionTableId.value;
    var tempRowId = document.form1.ActionRowId.value;
    var temprunningNum = document.form1.runningNum.value;
    switch (ObjId) {
        case "3":		//出货方式
            var tempShipType0 = document.form1.ShipType.value;
            var tempShipType1 = encodeURIComponent(tempShipType0);
            myurl = "ch_shippinglist_splitupdated.php?POrderId=" + temprunningNum + "&tempShipType=" + tempShipType1 + "&ActionId=ShipType" + "&sId=" + sId;
            //alert (myurl);

            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    if (tempShipType0.length > 0)
                        eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<img src='../images/ship" + tempShipType0 + ".png' style='width:20px;height:20px;'/>";
                    else
                        eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;" + tempShipType0 + "</NOBR></DIV>";
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;

        case "25":		//更新PO
            var OrderPO = document.form1.OrderPO.value;
            var tempOrderPO = encodeURIComponent(OrderPO);
            //alert(tempOrderPO);
            //return false;
            //myurl="yw_order_updated.php?POrderId="+temprunningNum+"&tempOrderPO="+tempOrderPO+"&ActionId=OrderPO";
            myurl = "ch_shippinglist_splitupdated.php?POrderId=" + temprunningNum + "&tempOrderPO=" + tempOrderPO + "&ActionId=OrderPO" + "&sId=" + sId;
            //alert(myurl);
            //return false;
            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' ><NOBR>" + OrderPO + "</NOBR></DIV>";
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;


        case "33":		//客户出货指定转发对象
            var tempToOutName0 = document.form1.ToOutName.value;
            var ToOutNameObj = document.form1.ToOutName;
            var ToOutNameText = "";
            for (i = 1; i < ToOutNameObj.length; i++) {
                if (ToOutNameObj[i].selected == true) {
                    ToOutNameText = ToOutNameObj[i].innerText;
                }
            }
            //alert(ToOutNameText);
            var tempToOutName1 = encodeURIComponent(tempToOutName0);
            myurl = "ch_shippinglist_splitupdated.php?POrderId=" + temprunningNum + "&tempToOutName=" + tempToOutName1 + "&ActionId=ToOutName" + "&sId=" + sId;
            //alert (myurl);
            //return ;
            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    if (tempToOutName0.length > 0)
                        eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = ToOutNameText;
                    else
                        eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "&nbsp;";
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;
    }
}

function batchUpdateSplit() {
    var choosedRow = 0;
    var Ids;
    for (var i = 0; i < form1.elements.length; i++) {
        var e = form1.elements[i];
        var NameTemp = e.name;
        var Name = NameTemp.search("checkid");

        if (e.type == "checkbox" && Name != -1) {
            if (e.checked) {
                choosedRow = choosedRow + 1;
                if (choosedRow == 1)
                    Ids = e.value;
                else
                    Ids = Ids + "|" + e.value;
            }
        }
    }
    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }
    var msg = "确定修改是否出货状态吗?";
    if (!confirm(msg)) {
        return;
    }

    var url = "ch_shippinglist_split_ajax.php?Ids=" + Ids;

    var ajax = InitAjax();
    ajax.open("GET", url, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4 && ajax.status == 200) {
            //更新该单元格内容
            //alert(ajax.responseText);
            if (ajax.responseText == "Y") {
                window.location.reload();
            } else {
                alert("拆分出货失败！");
            }
        }
    }
    ajax.send(null);
}

function Change(type) {

    switch (type) {
        case "project1" :
            jQuery("#companyId").val(jQuery("#project1").val());
            jQuery("#SId").val(jQuery("#SeatId").val());
            jQuery("#build").val();
            jQuery("#Type").val();
            RefreshPage("ch_shippinglist_split");
            break;
        case "SeatId":
            jQuery("#project1").val(jQuery("#project1").val());
            jQuery("#SId").val(jQuery("#SeatId").val());
            jQuery("#build").val(jQuery("#BuildFloor").val());
            jQuery("#Type").val(jQuery("#TypeId").val());
            RefreshPage("ch_shippinglist_split");
            break;
        case "BuildFloor":
            jQuery("#project1").val(jQuery("#project1").val());
            jQuery("#SId").val(jQuery("#SeatId").val());
            jQuery("#build").val(jQuery("#BuildFloor").val());
            jQuery("#Type").val();
            RefreshPage("ch_shippinglist_split");
            break;
        case "TypeId":
            jQuery("#project1").val(jQuery("#project1").val());
            jQuery("#SId").val(jQuery("#SeatId").val());
            jQuery("#build").val(jQuery("#BuildFloor").val());
            jQuery("#Type").val();
            RefreshPage("ch_shippinglist_split");
            break;
    }
}

function ResetPages(e) {
    switch (e){
        case 'PutawayDate':
            document.forms["form1"].elements["BuildFloor"].value="";
            document.forms["form1"].elements["project1"].value="";
            document.forms["form1"].elements["TypeId"].value="";
            document.forms["form1"].elements["SeatId"].value="";
            document.forms["form1"].elements["finally"].value="all";
            document.form1.submit();
            break;
        case 'SeatId':
            document.forms["form1"].elements["finally"].value="all";
            document.form1.submit();
            break;
        case 'project1':
            document.forms["form1"].elements["BuildFloor"].value="";
            document.forms["form1"].elements["TypeId"].value="";
            document.forms["form1"].elements["SeatId"].value="";
            document.forms["form1"].elements["finally"].value="all";
            document.form1.submit();
            break;
        case 'BuildFloor':
            document.forms["form1"].elements["TypeId"].value="";
            document.forms["form1"].elements["SeatId"].value="";
            document.forms["form1"].elements["finally"].value="all";
            document.form1.submit();
            break;
        case 'TypeId':
            document.forms["form1"].elements["SeatId"].value="";
            document.forms["form1"].elements["finally"].value="all";
            document.form1.submit();
            break;
    }
}

$(function () {
    // tr td 样式
    $('td').css('border-top', '0');
})
function All_elects() {
    jQuery('input[name^="checkid"]:checkbox').prop("checked", true);
}
function Instead_elects() {
    jQuery('input[name^="checkid"]:checkbox').prop("checked", false);
}
function batchPassRkdata(e) {
    var choosedRow = 0;
    jQuery('input[name^="checkid"]:checkbox').each(function () {

        if (jQuery(this).prop('checked') == true) {
            choosedRow = choosedRow + 1;
        }

    });
    var SeatId = jQuery("#SeatId option:selected").val();
    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        window.location.reload();
        return;
    }
    var url = "for_choose_seat.php?SeatId1="+SeatId;
    openWinDialog(e, url, 405, 300, 'bottom');

}

function batchPassSeatId(e) {
    var choosedRow = 0;
    jQuery('input[name^="checkid"]:checkbox').each(function () {

        if (jQuery(this).prop('checked') == true) {
            choosedRow = choosedRow + 1;
        }

    });
    var SeatId = jQuery("#SeatId option:selected").val();
    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        window.location.reload();
        return;
    }
    var url = "for_choose_seat.php?SeatId1="+SeatId;
    openWinDialog(e, url, 405, 300, 'bottom');

    jQuery(document).ready(function(){
        getDataForAjax();
    });
}


function doPassRkData(level) {

    jQuery('.response').show();
    var choosedRow = 0;
    var Ids;
    var checkSeatId = true;
    var checkStackId = true;
    var num = 0;
    jQuery('input[name^="checkid"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {

            choosedRow = choosedRow + 1;
            if (num == 0) {
                Ids = jQuery(this).val();

            } else {
                Ids = Ids + '|' + jQuery(this).val();
            }
            num++;

            if (level == 1) {
                var SeatId = jQuery("#SeatId1 option:selected").val();

                if (SeatId == "") {
                    checkSeatId = false;
                    return false;
                }

                var StackId = jQuery("#StackId1 option:selected").val();

                if (StackId == "") {
                    checkStackId = false;
                    return false;
                }

            }
        }
    });

    if (!checkSeatId) {
        alert("该选择库位编号！");
        window.location.reload();
        return;
    }
if (!checkStackId) {
        alert("该选择垛号！");
        window.location.reload();
        return;
    }

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        window.location.reload();
        return;
    }

    var msg = "确定将成品移入库位" + jQuery("#SeatId1 option:selected").val() + " 垛号" + jQuery("#StackId1 option:selected").val() + "?";
    if (confirm(msg)) {
        var url = "change_seat_for_output.php";
        var params = "Ids=" + Ids;
        if (level == 1) {
            var SeatId = jQuery("#SeatId1 option:selected").val();
            var StackId = jQuery("#StackId1 option:selected").val();
            params = params + "&SeatId=" + SeatId+ "&StackId=" + StackId;
        }

        var ajax = InitAjax();
        ajax.open("POST", url, true);
        ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                //更新该单元格内容
                //alert(ajax.responseText);
                if (ajax.responseText.trim() == "Y") {
                    alert("修改库位成功！");
                    window.location.reload();
                    //e.innerHTML="<div style='background-color:#FF0000'>已确认</div>";
                } else {
                    alert("修改库位失败！");
                    window.location.reload();


                }
            }
        }
        ajax.send(params);
    } else {
        window.location.reload();
    }
}

function batchInventory() {

    jQuery('.response').show();
    var checkid = jQuery('input[name^="checkid"]:checked');
    var num = 0;
    var Ids = '';
    checkid.each(function () {
        if (num === 0) {
            Ids = jQuery(this).val();
        } else {
            Ids = Ids + '|' + jQuery(this).val();
        }
        num++;
    });

    if (num === 0) {
        alert('请选择盘点对象！');
        jQuery('.response').hide();
        return;
    }
    jQuery.ajax({
        url: 'batch_inventory.php',
        type: 'post',
        dataType: 'json',
        async: false,
        data: {
            Ids: Ids
        }
    });
    window.location.reload();

}

function refreshSeat() {
    jQuery("#SeatId1").change(function () {
        getDataForAjax();
    });
}

function getDataForAjax() {
    // SeatId = jQuery('#SeatId1').val();
    // var SeatId = jQuery("#SeatId1 option:selected").val();
    // jQuery.ajax({
    //     type:"post",
    //     url:"change_seat_for_output.php",
    //     dataType:'json',
    //     async:false,
    //     data:{
    //         ActionId:'131',
    //         SeatId:SeatId
    //     },
    //     success:function (data) {
    //         console.log(data);
    //         $('#StackId1').empty();
    //         for(var i = 0;i < data.length;i++){
    //             $('#StackId1').append("<option value=" + data[i]['id'] + ">"
    //                 + data[i]['name'] + "</option>");
    //         }
    //     }
    // });
    var url = "change_seat_for_output.php";
    var params = "ActionId=131";
        var SeatId = jQuery("#SeatId1 option:selected").val();
        params = params + "&SeatId=" + SeatId;

    var ajax = InitAjax();
    ajax.open("POST", url, true);
    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4 && ajax.status == 200) {
            //更新该单元格内容
            console.log(ajax.responseText);
            if (ajax.responseText.trim()) {
                alert("修改库位成功！");
                // window.location.reload();
                //e.innerHTML="<div style='background-color:#FF0000'>已确认</div>";
            } else {
                alert("修改库位失败！");
                // window.location.reload();


            }
        }
    }
    ajax.send(params);
}

</script>