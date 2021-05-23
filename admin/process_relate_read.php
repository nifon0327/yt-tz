
<?php 
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
ChangeWtitle("$SubCompany 半成品工序关联表");
$funFrom="process_relate";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|30|配件ID|60|半成品称|350|配件ID|60|加工配件名称|150|序号|30|工序ID|60|工序名称|150|工序图片|50|序号|30|治工具ID|60|治工具|180|对应关系|60";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;                           //每页默认记录数量
$ActioToS="1,3";							
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT T.TypeId,T.Letter,T.TypeName
	FROM $DataIn.semifinished_bom B 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId = B.mStuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
	WHERE 1 GROUP BY T.TypeId ",$link_id);
	echo "<option value='' selected>全部</option>";
	while ($myrow = mysql_fetch_array($result)){
			$TypeId=$myrow["TypeId"];
			if ($StuffType==$TypeId){
				echo "<option value='$TypeId'  selected>$myrow[Letter]-$myrow[TypeName]</option>";
				}
			else{
				echo "<option value='$TypeId' >$myrow[Letter]-$myrow[TypeName]</option>";
				}
			} 
		echo"</select>&nbsp;";
	$TypeIdSTR=$StuffType==""?"":" AND D.TypeId='$StuffType'";
	$SearchRows.=$TypeIdSTR;
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
//$helpFile=1;
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT B.mStuffId,D.Picture,D.StuffCname ,COUNT(*) AS Nums  
FROM $DataIn.semifinished_bom B
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=B.mStuffId
LEFT JOIN $DataIn.process_bom A ON A.StuffId=B.StuffId 
WHERE  1  $SearchRows AND A.StuffId>0 GROUP BY B.mStuffId ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
   $dp=anmaIn("download/productfile/",$SinkOrder,$motherSTR);
   do{
	    $m=1;
	    $StuffId=$myRow["mStuffId"];
	    $StuffCname=$myRow["StuffCname"];
	    $Picture=$myRow["Picture"];
 	    include "../model/subprogram/stuffimg_model.php";
	    $mStuffId = $StuffId;
	    $mStuffCname = $StuffCname;
	    $numrows=$myRow["Nums"];

        $checkidValue=$StuffId;
	    echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5' id='ListTable$i'><tr>";
	    echo"<td class='A0110' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' ></td>";
		$m=$m+2;
		echo"<td class='A0111' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$j</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$mStuffId</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$mStuffCname</td>";
		$m=$m+2;
		
		$StuffMyrow =mysql_fetch_array(mysql_query("SELECT D.StuffCname,D.StuffId,D.Picture
	        FROM $DataIn.semifinished_bom B
			LEFT JOIN $DataIn.process_bom A ON A.StuffId=B.StuffId 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
			WHERE B.mStuffId='$mStuffId' AND A.StuffId>0 ",$link_id));
		
		$StuffId=$StuffMyrow["StuffId"];
		$StuffCname=$StuffMyrow["StuffCname"];
		
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' align='center'>$StuffId</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows'>$StuffCname</td>";
		$m=$m+2;
	    if($numrows>0){
			$processResult=mysql_query("SELECT A.ProcessId,C.ProcessName,C.Picture 
			           FROM $DataIn.process_bom A  
					   LEFT JOIN $DataIn.process_data C ON C.ProcessId=A.ProcessId 
					   WHERE A.StuffId='$StuffId' ORDER BY A.Id",$link_id);
               
			  if($processRow=mysql_fetch_array($processResult)){//切割关系
			  $dc=anmaIn("download/processimg/",$SinkOrder,$motherSTR);
			  $r=0; $tempK = 1;
			     do{
				    $n=$m;
			        $ProcessId=$processRow["ProcessId"];
					$ProcessName=$processRow["ProcessName"];
					$Picture=$processRow['Picture'];
					
                    $ProcessFile=$mStuffId."_".$StuffId."_".$ProcessId.".jpg";
                    
                    $ProcessFileStr="";
                    $RowId=$r==0?10:2;
                    $bgfile="onmousedown='window.event.cancelBubble=true;' onclick='showUpFile($StuffId,$i,$RowId,$mStuffId,$ProcessId,$r)' style='CURSOR: pointer'";
					if(file_exists("../download/processimg/".$ProcessFile)){
					   $ProcessFile=anmaIn($ProcessFile,$SinkOrder,$motherSTR);
					   $ProcessName="<span onClick='OpenOrLoad(\"$dc\",\"$ProcessFile\")' style='CURSOR: pointer;color:#FF6633'>$ProcessName</span>"; 
					   $ProcessFileStr="<img src='../images/upFile.jpg' title='重新上传工序图' width='18' height='18'  $bgfile>";
				    }
				    else{
					   $ProcessFileStr="<img src='../images/upFile.jpg' title='点击上传工序图' width='18' height='18'  $bgfile>";
				    }           					
			         
				    if($r>0){echo"</tr><tr>";}
					echo"<td class='A0101' height='25' width='$Field[$n]' align='center'>$tempK</td>";
			        $n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$ProcessId</td>";
			        $n=$n+2;
					echo"<td class='A0101' width='$Field[$n]'>$ProcessName</td>";
			        $n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$ProcessFileStr</td>";
					$n=$n+2;
					$width1= $Field[$n];
					$n=$n+2;
					$width2= $Field[$n];
					$n=$n+2;
					$width3= $Field[$n];
					$n=$n+2;
					$width4= $Field[$n]-1;
					$width=$width1+$width2+$width3+$width4+10;
                    
					echo"<td class='A0101' width='$width' align='center'><table  cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
					      
				    $toolsResult = mysql_query("SELECT  T.ToolsId,F.ToolsName,T.Relation,F.Picture FROM $DataIn.semifinished_tools T 
				    LEFT JOIN $DataIn.fixturetool F ON F.ToolsId = T.ToolsId
				    WHERE T.mStuffId = $mStuffId AND T.ProcessId='$ProcessId'",$link_id);
				    $tempj = 1;
				    $toolsDir=anmaIn("download/ztools/",$SinkOrder,$motherSTR);
				    while($toolsRow = mysql_fetch_array($toolsResult)){
				       $thisToolsName= $toolsRow["ToolsName"];
				       $thisToolsId= $toolsRow["ToolsId"];
				       $thisRelation= $toolsRow["Relation"];
				       $thisPicture=$myRow["Picture"];    
				       if($thisPicture==1){
					     $thisPicture=$ToolsId.".jpg";
					     $thisPicture=anmaIn($thisPicture,$SinkOrder,$motherSTR);
					     $thisToolsName="<span onClick='OpenOrLoad(\"$toolsDir\",\"$thisPicture\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>$thisToolsName</span>";
					   }
				      
				       echo "<tr>
				             <td class='A0001' width='$width1' align='center' height='25'>$tempj</td>
				             <td class='A0001' width='$width2' align='center'>$thisToolsId</td>
				             <td class='A0001' width='$width3' align='left'>$thisToolsName</td>
				             <td  width='$width4' align='center'>$thisRelation</td>
				            </tr>";
				       $tempj++;
				    }
					      
					echo"</table></td>";
			        $r++; $tempK++;
			       }while($processRow=mysql_fetch_array($processResult));
				   echo"</tr>";
			    }
			   echo "</table>";
	        }
		 $j++;
		 $i++;
	  }while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,"");
  	}
//步骤7：
echo '</div>';
echo "<input name='backValue' type='hidden' id='backValue' value=''>";
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
$ChooseFun="N";
include "../model/subprogram/read_model_menu.php";
?>
<script language="JavaScript" type="text/JavaScript">
function showUpFile(StuffId,TableId,RowId,mStuffId,ProcessId,r){
	var backValId=document.getElementById("backValue");
	backValId.value="";
	var rand=Math.random();
	var b=window.showModalDialog("process_relate_upFile.php?r="+rand+"&StuffId="+StuffId+"&mStuffId="+mStuffId+"&ProcessId="+ProcessId +"&ActionId=101",window,"dialogHeight =220px;dialogWidth=400px;center=yes;scroll=no");
	BackValue=backValId.value;
    if (BackValue!=""){
	   var Backdata=BackValue.split("@");
	   switch(Backdata[0]){
		case "Y":
		   var showText="<img src='../images/down.gif' alt='已上传,图档未审核' width='18' height='18'>";
		   eval("ListTable"+TableId).rows[r].cells[RowId].innerHTML="<DIV STYLE='overflow: hidden; text-overflow:ellipsis'><NOBR>"+showText+"</NOBR></DIV>";	
		   break;
		 case "N":
		   showText="<img src='../images/upFile.jpg' alt='点击上传' width='18' height='18'>";
		   eval("ListTable"+TableId).rows[r].cells[RowId].innerHTML="<DIV STYLE='overflow: hidden; text-overflow:ellipsis'><NOBR>"+showText+"</NOBR></DIV>";	
		  break;
		default:
		  break;	   
	   }
	}
}
</script>