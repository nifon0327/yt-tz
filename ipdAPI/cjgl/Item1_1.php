<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="Item1_1.css" type="text/css" charset="utf-8">
		<link rel="stylesheet" href="sharedStyle.css" type="text/css" charset="utf-8">
		<script src="jquery-1.7.2.js" charset="utf-8"></script>
		<script src="cj_function.js" charset="utf-8"></script>
		<script src="Item1_1.js" type="text/javascript"></script>
	</head>
	<body>
	<input  type="hidden" id="typeId" name="typeId" value="<?php  echo $TypeId;?>" />
	<?php 
	//更新OK
		include "../../basic/parameter.inc";
		include "../../model/modelfunction.php";
		if($TypeId!=9113)
		{
	?>
		<table id="titleTable"> 
			<tr>
				<td width="40px">配件</td>
				<td width="30px">ID</td>
				<td width="80px">PO</td>
				<td width="230px">中文名</td>
				<td width="180px">Product Code</td>
				<td width="30px">检讨</td>
				<td width="35px"> 运货</td>
				<td width="50px">Qty</td>
				<td width="165px">生管备注</td>
				<td width="40px">限期</td>
				<td width="50px">打印</td>
				<td width="50px">已生成</td>
				<td width="50px">登记</td>
			</tr>
		</table>				
			
	<?php 	
			include "Item1_1_sc.php";//其他生产登记
		}
		else
		{
	?>
		
		
	<?php 
			include "Item1_1_kl.php";//开料登记
		}
	?>
	</body>	
	<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
	<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
</html>