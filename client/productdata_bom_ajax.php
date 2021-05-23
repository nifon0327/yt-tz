<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=600;
echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='20' height='20'>NO</td>
		<td width='80' align='center'>ID</td>
		<td width='300' align='center'>Name</td>
		<td width='30' align='center'>Gfile</td>		
		<td width='40' align='center'>Unit</td>
		<td width='60' align='center'>Relation</td></tr>";

			$StuffResult = mysql_query("SELECT D.StuffCname,D.Picture,D.Gfile,D.Gstate,D.Gremark,D.StuffId,D.TypeId,A.Relation,A.Id,MT.TypeColor,MT.Id AS MTID,U.Name AS UnitName
				FROM $DataIn.pands A
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
				LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
				LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
				LEFT JOIN $DataPublic.stuffmaintype MT ON MT.Id=ST.mainType
				WHERE A.ProductId='$ProductId'  AND ST.mainType<2 ORDER BY MT.Id,A.Id",$link_id);
$i=1;
$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
				do{	
					$PandsId=$StuffMyrow["Id"];
					$StuffId=$StuffMyrow["StuffId"];
					$StuffCname=$StuffMyrow["StuffCname"];
					$UnitName=$StuffMyrow["UnitName"]==""?"&nbsp;":$StuffMyrow["UnitName"];
					$TypeId=$StuffMyrow["TypeId"];
					$TypeColor=$StuffMyrow["TypeColor"];
					$Relation=$StuffMyrow["Relation"];
					$theDefaultColor=$TypeColor;
					$Picture=$StuffMyrow["Picture"];
					$Gfile=$StuffMyrow["Gfile"];
					$Gstate=$StuffMyrow["Gstate"]; 
					$Gremark=$StuffMyrow["Gremark"];
					include "../model/subprogram/stuffimg_Gfile.php";	//图档显示		
					//检查是否有图片
					include "../model/subprogram/stuffimg_model.php";

		
					echo"<tr bgcolor='$theDefaultColor'><td bgcolor='$Sbgcolor' align='center' height='20'>$i</td>";//
					echo"<td  align='center' >$StuffId</td>";	
					echo"<td  align='Left'>$StuffCname</td>";//
					echo"<td  align='center'>$Gfile</td>";
					echo"<td  align='center'>$UnitName</td>";
					echo"<td  align='center'>$Relation</td>";
					echo"</tr>";
					$i++;
					} while ($StuffMyrow = mysql_fetch_array($StuffResult));
}
else{
	echo"<tr><td height='30' colspan='6'>No</td></tr>";
	}

echo"</table>"."";

?>