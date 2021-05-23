<?php   
//电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
$tableWidth=850;$tableMenuS=500;
//步骤4：需处理
?>
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<form name="form1" id="checkFrom" enctype="multipart/form-data" action="" method="post" >
<table width="<?php    echo $tableWidth?>" height="133" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
    <tr>
		<td width="67" height="38" align="right" class='A1010'>&nbsp;</td>
	  <td width="890" class='A1001'>条码关键字
	    <input name="CodeNum" type="text" id="CodeNum" value="" size="120" maxlength="60" dataType="Require" Msg="未填写">
      <input type="submit" name="Submit" value="查找"></td>
    </tr>
    <tr>
      <td height="43" align="right" class='A0010'>&nbsp;</td>
    <td class='A0001'>&nbsp;
	<?php   
	if($CodeNum!=""){
		$CodeFile="&nbsp;";
		$checkCodeFileSql=mysql_query("
		SELECT F.StuffId,F.Estate,P.ProductId,P.cName,D.StuffId,D.StuffCname
		FROM $DataIn.stuffcodefile F
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=F.StuffId
		LEFT JOIN $DataIn.pands A ON D.StuffId=A.StuffId
		LEFT JOIN $DataIn.productdata P ON P.ProductId=A.ProductId
		WHERE D.StuffCname LIKE '%$CodeNum%' GROUP BY F.StuffId ORDER BY F.StuffId",$link_id);
		if($checkCodeFileRow=mysql_fetch_array($checkCodeFileSql)){
			$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
			echo"<table border='0' cellpadding='0' cellspacing='0'>
			  <tr align='center' class=''>
			  <td width='110' height='25' class='A1111'>条码关键字</td>
			  <td width='65' class='A1101'>产品ID</td>
			  <td width='200' class='A1101'>产品名称</td>
			  <td width='300' class='A1101'>配件名称</td>
			  <td width='55' class='A1101'>打印文件</td>
			  </tr>";
			do{
				$StuffId=$checkCodeFileRow["StuffId"];
				$ProductId=$checkCodeFileRow["ProductId"];
				$cName=$checkCodeFileRow["cName"];
				$StuffCname=$checkCodeFileRow["StuffCname"];
				$CFile=$StuffId.".qdf";
				$CodeFileFilePath="../download/stufffile/".$CFile;
				$CodeFileEstate=$checkCodeFileRow["Estate"];
				switch($CodeFileEstate){
					case 1://审核不通过
						$AltStr=$TypeSTR."文件审核未通过,请重新上传.";
						$CodeFile="<img src='../images/del.gif' alt='$AltStr' width='18' height='18'>";
						break;
					case 2://审核中
						$AltStr=$TypeSTR."文件审核中";
						$CodeFile="<img src='../images/audit.gif' alt='$AltStr' width='18' height='18'>";
						break;
					case 0://审核通过
						$AltStr=$TypeSTR."文件,用于车间自行打印";
						$CFile=anmaIn($CFile,$SinkOrder,$motherSTR);
						$CodeFile="<img onClick='OpenOrLoad(\"$d\",\"$CFile\",6)' src='../images/down.gif' alt='$AltStr' width='18' height='18'>";
						break;
					}
				echo"
				<tr>
				<td align='center' class='A0111' height='25'>$CodeNum</td>
				<td align='center' class='A0101'>$ProductId</td>
				<td class='A0101'>$cName</td>
				<td class='A0101'>$StuffCname</td>
				<td align='center' class='A0101'>$CodeFile</td>
				</tr>";
				}while($checkCodeFileRow=mysql_fetch_array($checkCodeFileSql));
			echo"</table>";
			}
		}
	?>
	</td>
    </tr>
    <tr>
      <td height="52" align="right" class='A0110'>&nbsp;</td>
      <td class='A0101'>条码关键字：输入和产品或配件相关的条码，可以只输入后四位条码，或输入配件名称</td>
    </tr>
</table>
</form>
</table>
</body>
</html>