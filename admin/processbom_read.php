<?php
include "../model/modelhead.php";
$ColsNumber=10;
$tableMenuS=500;
ChangeWtitle("$SubCompany 加工工序BOM列表");
$funFrom="processbom";
$From=$From==""?"read":$From;
$Th_Col="选项|30|序号|30|配件ID|70|配件名|280|配件类型|90|单位|30|单价|60|NO.|30|工序ID|50|工序名称|100|基础损耗|60|图档|30|对应关系|60|约束工序|100|排序|40";
$Pagination=$Pagination==""?1:$Pagination;	
$Page_Size = 100;
$ActioToS="1,2,3,4";							
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT T.TypeId,T.Letter,T.TypeName 
	FROM $DataIn.process_bom A 
    LEFT JOIN $DataIn.stuffdata S ON S.StuffId=A.StuffId 
    LEFT JOIN  $DataIn.stufftype T ON S.TypeId=T.TypeId 
	WHERE 1 $SearchRows GROUP BY T.TypeId order by T.Letter",$link_id);
	echo "<option value='' selected>全部</option>";
	while ($myrow = mysql_fetch_array($result)){
			$TypeId=$myrow["TypeId"];
			if ($StuffType==$TypeId){
				echo "<option value='$TypeId'  selected>$myrow[Letter]-$myrow[TypeName]</option>";
				}
			else{
				echo "<option value='$TypeId'>$myrow[Letter]-$myrow[TypeName]</option>";
				}
			} 
		echo"</select>&nbsp;";
	$TypeIdSTR=$StuffType==""?"":" AND T.TypeId=".$StuffType;
	$SearchRows.=$TypeIdSTR;
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";

//步骤5：
//$helpFile=1;
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT A.StuffId,S.StuffCname,S.Picture,S.Price,T.TypeName,U.Name AS UnitName   
FROM $DataIn.process_bom A
LEFT JOIN $DataIn.stuffdata S ON S.StuffId=A.StuffId  
LEFT JOIN $DataIn.stufftype T ON S.TypeId=T.TypeId
LEFT JOIN $DataIn.stuffunit U ON U.Id=S.Unit 
where 1 $SearchRows GROUP BY A.StuffId order by StuffId DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
     $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
   do{
	   $m=1;
	   $StuffId=$myRow["StuffId"];
	   $StuffCname=$myRow["StuffCname"];
	   $TypeName=$myRow["TypeName"];
	   $Picture=$myRow["Picture"];
	   $Price=$myRow["Price"];
	   $UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];	
        //检查是否有图片
       include "../model/subprogram/stuffimg_model.php";	   
       include"../model/subprogram/stuff_Property.php";//配件属性
	   
		//读取配件数
	   $BOM_Temp=mysql_query("select count(*) from $DataIn.process_bom where StuffId='$StuffId'",$link_id);
	   $BOM_myrow = mysql_fetch_array($BOM_Temp);
	   $numrows=$BOM_myrow[0];

	    echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5' id='ListTable$i'><tr onClick='ClickUpCheck($i)'>";
	    echo"<td rowspan='$numrows' scope='col' height='21' width='$Field[$m]' class='A0111' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$StuffId'></td>";
	    $m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$j</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$StuffId</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$StuffCname</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$TypeName</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$UnitName</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$Price</td>";
		$m=$m+2;
	    if($numrows>0){
		       $ProcessResult=mysql_query("SELECT D.ProcessId,D.ProcessName,A.Relation,D.Price,D.Picture,
		       PT.Color,PT.SortId,D.BassLoss,A.BeforeProcessId
	           FROM $DataIn.process_bom A  
               LEFT JOIN $DataIn.process_data D ON D.ProcessId=A.ProcessId   
               LEFT JOIN $DataIn.process_type PT ON PT.gxTypeId=D.gxTypeId
			   WHERE  A.StuffId='$StuffId' ORDER BY PT.SortId ",$link_id);
			   $r=1;
			   if($ProcessRow=mysql_fetch_array($ProcessResult)){//对应关系
			     $d=anmaIn("download/process/",$SinkOrder,$motherSTR);
			     do{	
			        $n=$m;
                    $ProcessId=$ProcessRow["ProcessId"];
		            $ProcessName=$ProcessRow["ProcessName"];
			        $Relation=$ProcessRow["Relation"];
                    $Price=$ProcessRow["Price"]==""?"&nbsp;":$ProcessRow["Price"];
                    $BeforeProcessId=$ProcessRow["BeforeProcessId"];
                    $BeforeProcessNameStr ="";
                  
                    if($BeforeProcessId!=0){
	                    $BeforeResult = mysql_query("SELECT ProcessName FROM $DataIn.process_data WHERE ProcessId IN ($BeforeProcessId)",$link_id);
	                    while($BeforeRow = mysql_fetch_array($BeforeResult)){
		                    $BeforeProcessName = $BeforeRow["ProcessName"];
		                    $BeforeProcessNameStr = $BeforeProcessNameStr==""?$BeforeProcessName:$BeforeProcessNameStr.",".$BeforeProcessName;
	                    }
                    }else{
	                    $BeforeProcessNameStr = "&nbsp;";
                    }
                    
                    $BassLoss=$ProcessRow["BassLoss"];              
                    $BassLoss=($BassLoss*100)."%";  
                    $Picture=$ProcessRow["Picture"];
                    $SortId=$ProcessRow["SortId"]; 
                    $Color=$ProcessRow["Color"];
                    $bgColor="bgcolor='$Color'";                        
                    include "subprogram/process_Gfile.php";	//图档显示                
					if($r>1){echo"<tr>";}
                     echo"<td class='A0101' height='25' width='$Field[$n]' align='center'>$r</td>";
			         $n=$n+2;
			         echo"<td class='A0101' height='25' width='$Field[$n]' align='center'>$ProcessId</td>";
                     $n=$n+2;
					 echo"<td class='A0101' height='25' width='$Field[$n]' $bgColor>$ProcessName</td>";
                     $n=$n+2;
					 echo"<td class='A0101' width='$Field[$n]' align='center'>$BassLoss</td>";
			         $n=$n+2;
					 echo"<td class='A0101' width='$Field[$n]' align='center'>$Gfile</td>";
			         $n=$n+2;
					 echo"<td class='A0101' width='$Field[$n]' align='center'>$Relation</td>";
			         $n=$n+2;
                     echo"<td class='A0101' width='$Field[$n]' align='left'>$BeforeProcessNameStr</td>";
                     $n=$n+2;
                     echo"<td class='A0101' width='$Field[$n]' align='center'>$SortId</td>";
			        $r++;
				    }while($ProcessRow=mysql_fetch_array($ProcessResult));
				   echo"</tr>";
				 echo "</table>";
	          }//if($ProcessRow=mysql_fetch_array($ProcessResult))
		   }//if($numrows>0)
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
<script>
function ClickUpCheck(index){
    var checkid="checkid"+index;
    var listTable="ListTable"+index;
    var e=document.getElementById(checkid);
    if (e.checked){
        e.checked=false;
        document.getElementById(listTable).style.background="";
    }else{
        e.checked=true;
        document.getElementById(listTable).style.background="#FFCC99";
    }
}
</script>