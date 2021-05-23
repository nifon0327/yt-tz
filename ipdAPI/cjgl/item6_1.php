<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
		<script src="jquery-1.7.2.js" charset="utf-8"></script>
		<script src="../../cjgl/cj_function.js" charset="utf-8"></script>
		<script src="item6_1.js" charset="utf-8"></script>
		<link rel="stylesheet" href="item6_1.css"  type="text/css" media="screen" charset="utf-8">
		<link rel='stylesheet' href='../../model/css/sharing.css'>
		<link rel='stylesheet' href='../../model/Totalsharing.css'>
		<link rel='stylesheet' href='../../model/keyright.css'>
		<link rel='stylesheet' href='../../model/SearchDiv.css'>
	</head>
	<body>
		<table id="titleTable">
			<tr>
				<td width="30px">序号</td>
				<td width="40px">配件</td>
				<td width="30px">选项</td>
				<td width="80px">PO#</td>
				<td width="100px">订单流水号</td>
				<td width="300px">中文名</td>
				<td width="55px">售  价</td>
				<td width="55px">订单数量</td>
				<td width="55px">金  额</td>
				<td width="70px">订单日期</td>
				<td>备注</td>
			</tr>
		</table>
		<div id="dataDiv">
		<table id="dataTable">
		<?php
			$Count = 11;
			include "item6_1_data.php";
		?>
		</table>
		</div>
	</body>
</html>