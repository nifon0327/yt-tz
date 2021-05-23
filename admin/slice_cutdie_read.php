
<?php
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=700;
ChangeWtitle("$SubCompany 片材刀模图档");
$funFrom="slice_cutdie";
$From=$From==""?"read":$From;
$Th_Col="选项|30|序号|30|配件ID|50|片材配件名称|200|单价|60|成本价|60|单位|30|NO.|30|配件ID|50|原材料名称|200|单位|30|对应关系|60|采购|60|供应商|80|刀模编号|120|图档|30";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,3,92";//13				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,40
//步骤3：
$nowWebPage=$funFrom."_read";
include "subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
     	$SearchRows="";
	}
        
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
include "../model/subprogram/getCutStuff.php"; //获得片材配件
//步骤5：
//$helpFile=1;
include "subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
//AND A.mStuffId  IN ($propertyStuffIds) 

$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT A.mStuffId,D.StuffCname,D.Price,D.CostPrice,D.Picture,D.Gfile,D.Gstate,U.Name AS Unit 
FROM $DataIn.semifinished_bom A
LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.mStuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
where 1 $SearchRows  AND D.StuffId>0  AND A.mStuffId  IN ($propertyStuffIds)   GROUP BY A.mStuffId order by A.mStuffId DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
    $dt=anmaIn("download/cut_data/",$SinkOrder,$motherSTR);
    $dw=anmaIn("download/cut_drawing/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$StuffId=$myRow["mStuffId"];
		$StuffCname=$myRow["StuffCname"];
        $Price=$myRow["Price"];
        $gCostPrice=$myRow["CostPrice"];
        $Unit=$myRow["Unit"]==""?"&nbsp;":$myRow["Unit"];
        $Picture=$myRow["Picture"];
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];        
       include "../model/subprogram/stuffimg_Gfile.php";	//图档显示		
		//检查是否有图片
		include "../model/subprogram/stuffimg_model.php";
         include"../model/subprogram/stuff_Property.php";//配件属性   
        $mStuffId=$StuffId;              
		//读取配件数
		$PO_Temp=mysql_query("select count(*) from $DataIn.semifinished_bom where mStuffId=$mStuffId",$link_id);
		$PO_myrow = mysql_fetch_array($PO_Temp);
		$numrows=$PO_myrow[0]; 

		echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
		echo"<td rowspan='$numrows' scope='col' height='21' width='$Field[$m]' class='A0111' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$mStuffId'>
		</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$j</td>";
       $m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$mStuffId</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$StuffCname</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'  align='center'>$Price</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'  align='center'>$gCostPrice</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$Unit</td>";
		$m=$m+2;
        include "../pt/slice_cutdie_show.php";	   

		if($numrows>0){
			//从配件表和配件关系表中提取配件数据	  
			$StuffResult = mysql_query("SELECT A.Id,A.Relation,A.StuffId,D.StuffCname,D.Price,D.CostPrice,D.Picture,D.Gfile,D.Gstate,  
			    D.Gremark,D.TypeId,D.SendFloor,U.Name AS Unit
				FROM  $DataIn.semifinished_bom A  
                LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.StuffId 
                LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
		        LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
               WHERE A.mStuffId='$mStuffId' ORDER BY Id",$link_id);
			$k=1;$CostPrice=$Price;
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
				do{	
					$n=$m;
					$PandsId=$StuffMyrow["Id"];
					$StuffId=$StuffMyrow["StuffId"];
					$StuffCname=$StuffMyrow["StuffCname"];
					$TypeId=$StuffMyrow["TypeId"];
					$Price=$StuffMyrow["Price"];
					$mCostPrice=$StuffMyrow["CostPrice"];
                    $Unit=$StuffMyrow["Unit"]==""?"&nbsp;":$StuffMyrow["Unit"];
					$Relation=$StuffMyrow["Relation"];
					$RelArray=explode("/", $Relation);
					$mRelation=count($RelArray)==2?$RelArray[0]/$RelArray[1]:$RelArray[0];
					
					if ($mCostPrice>0){
						$CostPrice+=($mCostPrice*$mRelation);
					}
					else{
						$CostPrice+=($Price*$mRelation);
					}
					
					$bps = mysql_query("SELECT M.Name,P.Forshort 
					FROM $DataIn.bps B
					LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId 
					LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId
					LEFT JOIN $DataIn.providerdata P ON P.CompanyId=B.CompanyId
					WHERE B.StuffId='$StuffId'",$link_id);
					if($bpsMyrow=mysql_fetch_array($bps)){
						$Name=$bpsMyrow["Name"];
						$Forshort=$bpsMyrow["Forshort"] == ""?"&nbsp;":$bpsMyrow["Forshort"];
						}
					//配件名称
                  if($k>1){echo"<tr>";}
					echo"<td class='A0101' width='$Field[$n]' align='center' height='21' >$k</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$StuffId</td>";
					$n=$n+2;
					$Picture=$StuffMyrow["Picture"];
					//检查是否有图片
					include "../model/subprogram/stuffimg_model.php";
					$SendFloor=$StuffMyrow["SendFloor"];
					include "../model/subprogram/stuff_GetFloor.php";
					$FloorName=$FloorName=""?"&nbsp":$FloorName;
		           
					echo"<td class='A0101' width='$Field[$n]'>$StuffCname</td>";
					$n=$n+2;
				    echo"<td class='A0101' width='$Field[$n]' align='center'>$Unit</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' >$Relation</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$Name</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$Forshort</td>";          
	              if($k==1){
							$n=$n+2;       
		                   echo"<td class='A0101' width='$Field[$n]' align='left' rowspan='$numrows'>$CutStr</td>";
							$n=$n+2;
							echo"<td class='A0101' width='$Field[$n]' align='center'  rowspan='$numrows'>$CutDrawing</td>";
	                 }
					echo"</tr>";
					$k++;
					$i++;
					} while ($StuffMyrow = mysql_fetch_array($StuffResult));
				}//if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
				echo "</table>";
				
				//添加配件成本计算
				$gCostPrice=number_format($gCostPrice,4);
				$CostPrice=number_format($CostPrice,4);
				if ($gCostPrice<>$CostPrice){
					 $upSql = "UPDATE  $DataIn.stuffdata SET CostPrice='$CostPrice' WHERE StuffId='$mStuffId' ";
					 echo "配件 ($gStuffId) 的成本价格($gCostPrice)已更新为:$CostPrice";
					 $upResult = mysql_query($upSql);
				}
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
$ChooseFun="N";
include "subprogram/read_model_menu.php";
?>
