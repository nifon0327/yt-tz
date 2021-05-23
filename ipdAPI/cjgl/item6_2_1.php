<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
		<script src="jquery-1.7.2.js" charset="utf-8"></script>
		<script src="../../cjgl/cj_function.js" charset="utf-8"></script>
		<script src="item6_2.js" charset="utf-8"></script>
		<link rel="stylesheet" href="item6_2.css"  type="text/css" media="screen" charset="utf-8">
		<link rel='stylesheet' href='../../model/css/sharing.css'>
		<link rel='stylesheet' href='../../model/Totalsharing.css'>
		<link rel='stylesheet' href='../../model/keyright.css'>
		<link rel='stylesheet' href='../../model/SearchDiv.css'>
	</head>
	<body>
		<div id="titleTool">
			<?php
				include "../../basic/parameter.inc";
				include "../../model/modelfunction.php";

				$ShipEstate=$ShipEstate==""?1:$ShipEstate;
				$SelStr="ShipEstate".$ShipEstate;
				$$SelStr="selected";
				$ClientList.="<select name='ShipEstate' id='ShipEstate' onchange='changeInfo()'>
              <option value='1' $ShipEstate1>当前出货明细</option>
			  <option value='0' $ShipEstate0>已出货明细</option>
			  </select>";

			 $SearchRows=" AND M.Estate='$ShipEstate'";
			 $date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M 
	   WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);

	   		if($dateRow = mysql_fetch_array($date_Result))
	   		{
		   		$ClientList.="<select name='chooseDate' id='chooseDate' class='dateSelectStyle' onchange='changeInfo();'>";

		   		do
		   		{
					$dateValue=date("Y-m",strtotime($dateRow["Date"]));
					$StartDate=$dateValue."-01";
					$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
					$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
					if($chooseDate==$dateValue)
					{
						$ClientList.="<option value='$dateValue' selected>$dateValue</option>";
						$SearchRows.=" AND DATE_FORMAT(M.Date,'%Y-%m')='$dateValue'";
					}
					else
					{
						$ClientList.="<option value='$dateValue'>$dateValue</option>";
					}
				}while($dateRow = mysql_fetch_array($date_Result));

			$ClientList.="</select>&nbsp;";
			}

			echo $ClientList;

			?>
		</div>
		<table id="titleTable">
			<tr>
				<td width="30px">序号</td>
				<td width="40px">配件</td>
				<td width="40px">选项</td>
				<td width="100px">出货流水号</td>
				<td width="120px">客户</td>
				<td width="120px">Invoice名称</td>
				<td width="120px">Invoice文档</td>
				<td width="80px">外箱标签</td>
				<td width="80px">出货金额</td>
				<td width="100px">出货日期</td>
				<td width="120px">货运信息</td>
				<td>操作</td>
			</tr>
		</table>
		<div id="dataDiv">
		<table id="dataTable">
		<?php
			$Count = 12;
			include "item6_2_data.php";
		?>
		</table>
		</div>
	</body>
</html>