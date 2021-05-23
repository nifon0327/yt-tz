<script type="text/javascript" src="jquery-1.8.2.min.js"></script>
<?php

	$SearchRows=" AND A.TypeId='7100' AND S.scFrom>0 AND S.Estate>0";//生产分类里的ID

	//$SearchRows=" AND A.TypeId='$TypeId' AND S.scFrom>0 AND S.Estate>0";//生产分类里的ID
	$ClientList="";
	$ClientResult= mysql_query("SELECT M.CompanyId,C.Forshort 
								FROM $DataIn.yw1_ordermain M 
								LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
								LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
								LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
								LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
								LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId 
								WHERE 1 $SearchRows GROUP BY M.CompanyId order by M.CompanyId",$link_id);
	if ($ClientRow = mysql_fetch_array($ClientResult))
	{
		$ClientList="<select name='CompanyId' id='CompanyId' style='width:200px' onChange='ResetPage(1,4)'>";
		$i=1;
		do{
			$theCompanyId=$ClientRow["CompanyId"];
			$theForshort=$ClientRow["Forshort"];
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId)
			{
				$ClientList.="<option value='$theCompanyId' selected>$i 、$theForshort</option>";
				$SearchRows.=" AND M.CompanyId='$theCompanyId'";
				$nowInfo="当前:".$ItemRemark." - ".$theForshort;
			}
			else
			{
				$ClientList.="<option value='$theCompanyId'>$i 、$theForshort</option>";
			}
			$i++;
		}
		while($ClientRow = mysql_fetch_array($ClientResult));
		$ClientList.="</select>";
	}

	//分类
	$TypeResult= mysql_query("SELECT P.TypeId,T.TypeName
							  FROM $DataIn.yw1_ordermain M 
							  LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
							  LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
							  LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
							  LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
							  LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
							  LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId 
							  WHERE 1  $SearchRows GROUP BY P.TypeId ORDER BY T.mainType,T.TypeId",$link_id);
	if ($TypeRow = mysql_fetch_array($TypeResult))
	{
		$TypeList="<select name='ProductTypeId' id='ProductTypeId' onchange='ResetPage(1,4)'>";
		do{
			$theTypeId=$TypeRow["TypeId"];
			$TypeName=$TypeRow["TypeName"];
			if($ProductTypeId==$theTypeId)
			{
				$TypeList.="<option value='$theTypeId' selected>$TypeName</option>";$SearchRows.=" AND P.TypeId='$theTypeId'";
			}
			else
			{
				$TypeList.="<option value='$theTypeId'>$TypeName</option>";
			}
		}while($TypeRow = mysql_fetch_array($TypeResult));
		$TypeList.="</select>&nbsp;";
	}

	//产品
	$productSql="SELECT M.CompanyId,S.OrderPO,S.POrderId,S.ProductId,S.Estate,S.Locks,P.cName,P.eCode,P.TestStandard,P.Code
	FROM $DataIn.yw1_ordermain M
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
	WHERE 1  $SearchRows ORDER BY M.OrderDate";

	$proResult = mysql_query($productSql);

	if ($productRow = mysql_fetch_array($proResult))
	{
		$proList="<select name='proId' id='proId' onchange='fillTheTable()'>";
		do{
			$theproInfo = $productRow["cName"]."|".$productRow["Code"]."|".$productRow["OrderPO"];
			$theProName = $productRow["cName"];
			$proList.="<option value='$theproInfo'>$theProName</option>";
		}while($productRow = mysql_fetch_array($proResult));
		$proList.="</select>&nbsp;";
	}



//步骤5：
	echo"<table id='ListTable' border='1' width='90%' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#D9D9D9'>
	<td height='40px' colspan='2' class='A1010'>$ClientList &nbsp;&nbsp; $TypeList &nbsp;&nbsp; $proList &nbsp;&nbsp; <input type = 'button' id='setInfo' value='设为当前任务' onclick = 'setPrintInfo()' /> </td></tr>";

	$subTitle = array("中文名称","英文名称","条码","PO号");

	for($i=0;$i< count($subTitle);$i++)
	{
		echo "<tr bgcolor='#D9D9D9'>
				<td height='40px' width='20%' style='font-size:20px;' class='A1010'>".$subTitle[$i]."</td><td height='40px' style='font-size:20px;' class='A1010'><div name='printInfo'></div></td>
			  </tr>";
	}
?>

<script type="text/javascript">

	fillTheTable();

	function fillTheTable()
	{
		var infoContain = document.getElementsByName("printInfo");
		var info = document.getElementById("proId").value;
		var infoArray = info.split("|");
		for(var i=0;i<infoArray.length;i++)
		{
			infoContain[i].innerHTML = infoArray[i];
		}
	}

	function setPrintInfo()
	{
		var infoContain = document.getElementsByName("printInfo");
		$.post("item7_ajax.php",
			   {
				   'cNameTag': infoContain[0].innerHTML,
				   'eCodeTag': infoContain[1].innerHTML,
				   'barCodeTag': infoContain[2].innerHTML,
				   'POStrTag': infoContain[3].innerHTML
			   }
			  );
	}

</script>