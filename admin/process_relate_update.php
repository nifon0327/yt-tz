<?php
include "../model/modelhead.php";
echo"<SCRIPT src='../model/process_tools.js' type=text/javascript></script>";
ChangeWtitle("$SubCompany 半成品工序治工具更新");//需处理
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Id,$Id";
//步骤3：
$tableWidth=850;$tableMenuS=500;$ColsNumber=10;
$CheckFormURL="thisPage";

$m_Row = mysql_fetch_array(mysql_query("SELECT StuffCname FROM $DataIn.stuffdata WHERE StuffId='$Id' LIMIT 1",$link_id));
$mStuffCname=$m_Row["StuffCname"];
$mStuffId = $Id;
$StuffRow = mysql_fetch_array(mysql_query("SELECT D.StuffCname,D.StuffId
		        FROM $DataIn.semifinished_bom B
				LEFT JOIN $DataIn.process_bom A ON A.StuffId=B.StuffId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
				WHERE B.mStuffId='$Id' AND A.StuffId>0",$link_id));
			
$StuffId = $StuffRow["StuffId"];
$StuffCname = $StuffRow["StuffCname"];

$SelectCode=" <b>$Id - $mStuffCname($StuffCname)  </b><input name='mStuffId' type='hidden' id='mStuffId' value='$Id'>";
include "../model/subprogram/add_model_pt.php";
//步骤4：需处理
?>
<table border="0" width="<?php echo $tableWidth?>" cellpadding="0" cellspacing="0"  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor="#FFFFFF">
     <tr >
        <td width="20" height="25" class="A0011" >&nbsp;</td>
        <td width="40"  class="A1101" align="center">序号</td>
        <td width="60" class="A1101" align="center">工序ID</td>
        <td width="180" class="A1101" align="center">工序名称</td>
        <td width="80" class="A1101" align="center">操作</td>
        <td width="30" class="A1101" align="center">序号</td>
        <td width="310" class="A1101" align="center">治工具</td>
        <td width="100"  class="A1101" align="center">对应数量</td> 
        <td width="20"  class="A0001" >&nbsp;</td>
     </tr>
		
   <?php
     $ProcessSql = "SELECT A.ProcessId,C.ProcessName,C.Picture 
					           FROM $DataIn.process_bom A  
							   LEFT JOIN $DataIn.process_data C ON C.ProcessId=A.ProcessId 
							   WHERE A.StuffId='$StuffId' ORDER BY A.Id";
     $ProcessResult = mysql_query($ProcessSql, $link_id);
     $tempi=1;
     while($ProcessRow = mysql_fetch_array($ProcessResult)){
	     
	      $ProcessId = $ProcessRow["ProcessId"];
	      $Picture = $ProcessRow["Picture"];
	      $ProcessName = $ProcessRow["ProcessName"];
	     
	      echo "<tr >
		        <td height='25' class='A0011' >&nbsp;</td>
		        <td class='A0101' align='center'>$tempi</td>
		        <td class='A0101' align='center'>$ProcessId</td>
		        <td class='A0101' align='center'>$ProcessName</td>
		        <td class='A0101' align='center' colspan ='4'>
		        <table cellpadding='0' width='100%' cellspacing='0' bgcolor='#FFFFFF' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='ListTable$tempi'>";
		 
		    $tempj= 1;
		    $toolsResult = mysql_query("SELECT  T.ToolsId,F.ToolsName,T.Relation FROM $DataIn.semifinished_tools T 
		    LEFT JOIN $DataIn.fixturetool F ON F.ToolsId = T.ToolsId
		    WHERE T.mStuffId = $mStuffId AND T.ProcessId='$ProcessId'",$link_id);
		    while($toolsRow = mysql_fetch_array($toolsResult)){
		      $thisToolsName= $toolsRow["ToolsName"];
		      $thisToolsId= $toolsRow["ToolsId"];
		      $thisRelation= $toolsRow["Relation"];
		      if($tempj==1){
			      $deleteRow = "<a href='#' onclick='deleteRow(this.parentNode,$tempi,$tempj)' title='删除当前行'>×</a>";
		      }
		      echo "<tr>
				    <td align='center' class='A0001' width='80' height='25' onmousedown='window.event.cancelBubble=true;'>
				    <a href='#' onclick='addRow(this.parentNode,$tempi,$tempj,$ProcessId)' title='当前行上移'>+</a>&nbsp;&nbsp;&nbsp;
				    $deleteRow</td>
				    <td class='A0001' width='30' align='center'>$tempj</td>
				    <td class='A0001' width='310' align='center'><input name='toolsName[]'  id='toolsName$tempi$tempj'  type='text'  size='35' value='$thisToolsName' onclick='addtoolsName(this,$tempi,$tempj)'><input type='hidden' name='toolsId[]' id='toolsId$tempi$tempj' value='$toolsId' ></td>
				    <td class='A0001' width='100' align='center'><input name='Qty[]' type='text' id='Qty$tempi$tempj' size='8' value='$thisRelation' onchange='checkNum(this)' onfocus='toTempValue(this.value)'><input name='tempProcessId[]' type='hidden' id='tempProcessId$tempi$tempj' value='$ProcessId'></td>
				  </tr>";  
				$tempj++;  
			}
		 if($tempj==1){	
				echo "<tr>
				    <td align='center' class='A0001' width='80' height='25' onmousedown='window.event.cancelBubble=true;'>
				    <a href='#' onclick='addRow(this.parentNode,$tempi,$tempj,$ProcessId)' title='当前行上移'>+</a>
				    <td class='A0001' width='30' align='center'>$tempj</td>
				    <td class='A0001' width='310' align='center'><input name='toolsName[]'  id='toolsName$tempi$tempj'  type='text'  size='35' value='' onclick='addtoolsName(this,$tempi,$tempj)'><input type='hidden' name='toolsId[]' id='toolsId$tempi$tempj' value='' ></td>
				    <td class='A0001' width='100' align='center'><input name='Qty[]' type='text' id='Qty$tempi$tempj' size='8' value='0' onchange='checkNum(this)' onfocus='toTempValue(this.value)'><input name='tempProcessId[]' type='hidden' id='tempProcessId$tempi$tempj' value='$ProcessId'></td>
				  </tr>";  
			}	  
				  
		 echo "</table></td>
		        <td class='A0001' >&nbsp;</td>
		        </tr>";
	     
	     $tempi++;
     }
   ?>
</table>
<input name="TempValue" type="hidden" id="TempValue">
<input name="SIdList" type="hidden" id="SIdList">
<input name="tempi" type="hidden" id="tempi" value="<?php echo $tempi?>">
<input name="tempj" type="hidden" id="tempj" value="<?php echo $tempj?>">
<?php
//步骤5：
include "../model/subprogram/add_model_ps.php";
?>