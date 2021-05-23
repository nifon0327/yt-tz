
<?php
include "../model/modelhead.php";
$ColsNumber=12;
$tableMenuS=500;
ChangeWtitle("$SubCompany 子母配件BOM");
$funFrom="stuffcombox_pand";
$From=$From==""?"read":$From;
$Th_Col="选项|30|序号|30|配件ID|50|母配件名称|300|图档|30|单价|60|单位|30|采购|60|供应商|80|NO.|30|配件ID|50|子配件名称|300|图档|30|单价|60|单位|30|对应关系|60|采购|60|供应商|80";
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;
$ActioToS="1,2,3,4";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	}
        
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT A.mStuffId,D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate,U.Name AS Unit 
FROM $DataIn.stuffcombox_bom A
LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.mStuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
WHERE 1 $SearchRows  AND D.StuffId>0 GROUP BY A.mStuffId order by A.mStuffId DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$StuffId=$myRow["mStuffId"];
		$StuffCname=$myRow["StuffCname"];
        $Picture=$myRow["Picture"];
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];        
        include "../model/subprogram/stuffimg_Gfile.php";	//图档显示		
		include "../model/subprogram/stuffimg_model.php";//检查是否有图片
        include"../model/subprogram/stuff_Property.php";//配件属性
        $mPrice=$myRow["Price"];
        $mUnit=$myRow["Unit"]==""?"&nbsp;":$myRow["Unit"];
         
        $mStuffId=$StuffId;        
        $mStuffCname=$StuffCname;        
        $mGfile=$Gfile;        
		//读取配件数
		$PO_Temp=mysql_query("select count(*) from $DataIn.stuffcombox_bom where mStuffId=$mStuffId",$link_id);
		$PO_myrow = mysql_fetch_array($PO_Temp);
		$numrows=$PO_myrow[0];
       
					$mbps = mysql_query("SELECT M.Name,P.Forshort 
					FROM $DataIn.bps B
					LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId 
					LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId
					LEFT JOIN $DataIn.providerdata P ON P.CompanyId=B.CompanyId
					WHERE B.StuffId='$mStuffId'",$link_id);
					if($mbpsMyrow=mysql_fetch_array($mbps)){
						$mName=$mbpsMyrow["Name"];
						$mForshort=$mbpsMyrow["Forshort"];
						}
        $disable="";
  //echo "SELECT Id  FROM $DataIn.cg1_stuffcombox WHERE mStuffId =$mStuffId  AND mStuffId IN (SELECT StuffId FROM $DataIn.stuffproperty  WHERE Property=9)  LIMIT 1";
       $checkstuffBomResult  = mysql_fetch_array(mysql_query("SELECT S.Id FROM $DataIn.cg1_stuffcombox S
					INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.mStockId 
					WHERE S.mStuffId ='$mStuffId' AND EXISTS (SELECT P.StuffId FROM $DataIn.stuffproperty P WHERE S.mStuffId=P.StuffId and P.Property=9) LIMIT 1",$link_id));
     if($checkstuffBomResult){
        $disable="disabled";
        }
		echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
		echo"<td rowspan='$numrows' scope='col' height='21' width='$Field[$m]' class='A0111' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$mStuffId' $disable>
		</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$j</td>";
       $m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$mStuffId</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$mStuffCname</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$mGfile</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'  align='center'>$mPrice</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$mUnit</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'  align='center'>$mName</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'  align='center'>$mForshort</td>";
		$m=$m+2;
		if($numrows>0){
			//从配件表和配件关系表中提取配件数据	 
			$StuffResult = mysql_query("SELECT A.Id,A.Relation,A.StuffId,D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate, U.Name AS Unit 
				FROM  $DataIn.stuffcombox_bom A  
                LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.StuffId 
                LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
               WHERE A.mStuffId='$mStuffId' ORDER BY Id",$link_id);
			$k=1;
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了
				do{	
					$n=$m;
					$PandsId=$StuffMyrow["Id"];
					$StuffId=$StuffMyrow["StuffId"];
					$StuffCname=$StuffMyrow["StuffCname"];
					$Price=$StuffMyrow["Price"];
                    $Unit=$StuffMyrow["Unit"]==""?"&nbsp;":$StuffMyrow["Unit"];
					$Relation=$StuffMyrow["Relation"];
					$bps = mysql_query("SELECT M.Name,P.Forshort 
					FROM $DataIn.bps B
					LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId 
					LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId
					LEFT JOIN $DataIn.providerdata P ON P.CompanyId=B.CompanyId
					WHERE B.StuffId='$StuffId'",$link_id);
					if($bpsMyrow=mysql_fetch_array($bps)){
						$Name=$bpsMyrow["Name"];
						$Forshort=$bpsMyrow["Forshort"];
						}
					$Picture=$StuffMyrow["Picture"];			
	            	$Gfile=$StuffMyrow["Gfile"];
		            $Gstate=$StuffMyrow["Gstate"];        
					include "../model/subprogram/stuffimg_Gfile.php";	//图档显示		
					include "../model/subprogram/stuffimg_model.php"; //检查是否有图片
                    include"../model/subprogram/stuff_Property.php";//配件属性
                    if($k>1){echo"<tr>";}
					echo"<td class='A0101' width='$Field[$n]' align='center' height='21' >$k</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$StuffId</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]'>$StuffCname</td>";
					$n=$n+2;
				    echo"<td class='A0101' width='$Field[$n]' align='center'>$Gfile</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$Price</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$Unit</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' >$Relation</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$Name</td>";
					$n=$n+2;
					echo"<td class='A0101' width='' align='left' >$Forshort</td>";
					echo"</tr>";
					$k++;
					$i++;
					} while ($StuffMyrow = mysql_fetch_array($StuffResult));
				}
				echo "</table>";
			}//结束存在配件表
		$j++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,"");
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$j-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
