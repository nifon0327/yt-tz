
<?php   
//步骤1电信---yang 20120801
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=10;
$tableMenuS=500;
ChangeWtitle("$SubCompany 配件模具关系表");
$funFrom="stuff_die";
$From=$From==""?"read":$From;
$Th_Col="选项|30|序号|30|配件ID|60|配件名|320|默认供应商|120|单价|60|NO.|30|模具ID|60|模具名称|350|默认供应商|120|单价|60";
//必选，分页默认值

$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;                           //每页默认记录数量
$ActioToS="1,2,3,4";							
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
		echo"
	<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT T.TypeId,T.Letter,T.TypeName 
	FROM $DataIn.cut_die A 
     LEFT JOIN $DataIn.stuffdata S ON S.StuffId=A.StuffId 
     LEFT JOIN  $DataIn.stufftype T ON S.TypeId=T.TypeId 
	 WHERE 1 AND  A.StuffId>0 $SearchRows GROUP BY T.TypeId order by T.Letter",$link_id);
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
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
//$helpFile=1;
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT A.StuffId,S.StuffCname,S.Picture,S.Price,T.TypeName,P.Forshort
FROM $DataIn.cut_die  A
LEFT JOIN $DataIn.stuffdata S ON S.StuffId=A.StuffId  
LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId
LEFT JOIN $DataIn.providerdata P ON P.CompanyId=B.CompanyId
LEFT JOIN  $DataIn.stufftype T ON S.TypeId=T.TypeId
WHERE  1 AND  A.StuffId>0  $SearchRows GROUP BY A.StuffId order by StuffId DESC";
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
		$Forshort=$myRow["Forshort"];
        //检查是否有图片
        include "../model/subprogram/stuffimg_model.php";	   
	   
		//读取配件数
	   $BOM_Temp=mysql_query("select count(*) from $DataIn.cut_die where StuffId='$StuffId'",$link_id);
	   $BOM_myrow = mysql_fetch_array($BOM_Temp);
	   $numrows=$BOM_myrow[0];

	    echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5' id='ListTable$i'>
                  <tr onClick='ClickUpCheck($i)'>";
	    echo"<td rowspan='$numrows' scope='col' height='21' width='$Field[$m]' class='A0111' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$StuffId'></td>";
	    $m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$j</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$StuffId</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$StuffCname</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$Forshort</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$Price</td>";
		$m=$m+2;
	    if($numrows>0){
		     $DieResult=mysql_query("SELECT G.GoodsId,G.GoodsName,G.Attached,G.Price,M.Forshort
					           FROM $DataIn.cut_die  A  
                               LEFT JOIN $DataPublic.nonbom4_goodsdata G ON G.GoodsId=A.GoodsId  
                              LEFT JOIN $DataPublic.nonbom5_goodsstock  S  ON S.GoodsId=G.GoodsId
                              LEFT JOIN $DataPublic.nonbom3_retailermain  M ON M.CompanyId=S.CompanyId
							   WHERE  A.StuffId='$StuffId'",$link_id);
			$r=1;
			 if($DieRow=mysql_fetch_array($DieResult)){//对应关系
					 	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
			  do{	
					        $n=$m;
		                  $GoodsId=$DieRow["GoodsId"];
		                  $GoodsName=$DieRow["GoodsName"];
		                  $GoodsPrice=$DieRow["Price"];
		                  $GoodsForshort=$DieRow["Forshort"];
		                  $Attached=$DieRow["Attached"];
				    	if($Attached==1){
							 $Attached=$GoodsId.".jpg";
							  $Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
							  $GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633' >$GoodsName</span>";
					  	 }        
							if($r>1){echo"<tr>";}
                             echo"<td class='A0101' height='25' width='$Field[$n]' align='center'>$r</td>";
					         $n=$n+2;
					         echo"<td class='A0101' height='25' width='$Field[$n]' align='center'>$GoodsId</td>";
                             $n=$n+2;
							echo"<td class='A0101' height='25' width='$Field[$n]' >$GoodsName</td>";
                             $n=$n+2;
							echo"<td class='A0101' width='$Field[$n]' >$GoodsForshort</td>";
					        $n=$n+2;
							echo"<td class='A0101' width='$Field[$n]' align='center'>$GoodsPrice</td>";
					        $r++;
				}while($DieRow=mysql_fetch_array($DieResult));
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