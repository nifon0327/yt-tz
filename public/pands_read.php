<?php 
//步骤1电信---yang 20120801
//代码共享-EWEN 2012-08-19
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
ChangeWtitle("$SubCompany BOM列表");
$funFrom="pands";
$From=$From==""?"read":$From;

$Th_Col="选项|30|序号|30|产品ID|80|中文名|180|Product Code|150|单品重<br>(g)|40|成品重<br>(g)|50|外箱<br>承重(kg)|50|检讨|40|关联图|40|外箱<br>标签|30|&nbsp;|20|NO|25|配件ID|50|配件名称|280|图档|30|历史<br>订单|30|含税价|60|单位|40|对应数量|60|备品率|60|采购|40|供应商|90|楼层|60";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 300;							//每页默认记录数量
$ActioToS="1,2,3,4,13";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,40
//步骤3：
include "../model/subprogram/business_authority.php";//看客户权限
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
	echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
	/*$result = mysql_query("SELECT M.CompanyId,M.Forshort FROM $DataIn.trade_object M
	WHERE 1 AND ObjectSign IN (1,2) AND M.Estate>0 $ClientStr  ORDER BY CONVERT(M.Forshort USING gbk)",$link_id);
	*/
	$result = mysql_query("SELECT M.CompanyId,M.Forshort
		FROM $DataIn.pands A
		LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
		LEFT JOIN $DataIn.trade_object M ON M.CompanyId=P.CompanyId  
		WHERE M.Estate>0 $ClientStr GROUP BY M.CompanyId ORDER BY CONVERT(Forshort USING gbk) ",$link_id);

	if($myrow = mysql_fetch_array($result)){
          $chinese=new chinese;
		do{
			$theCompanyId=$myrow["CompanyId"];
			$Forshort=$myrow["Forshort"];
               $Letter=substr($chinese->c($Forshort),0,1);
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$Forshort</option>";
				$SearchRows .=" AND P.CompanyId=".$CompanyId;
				}
			 else{
			 	echo"<option value='$theCompanyId'>$Forshort</option>";
				}
			}while($myrow = mysql_fetch_array($result));
		}
	echo"</select>&nbsp; ";

	//栋层
	$result = mysql_query("SELECT substring_index( P.cName, '-', 1 ) AS build,substring_index((substring_index( P.cName, '-', 2 ) ), '-', -1 ) as floor
		FROM $DataIn.pands A
		LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
		LEFT JOIN $DataIn.trade_object M ON M.CompanyId=P.CompanyId  
		WHERE M.Estate>0 $SearchRows GROUP BY build,Floor+0 ",$link_id);

	if($myrow = mysql_fetch_array($result)){
        echo "<select name='buildFloor' id='buildFloor' onchange='ResetPage(this.name)'>";
        echo "<option value='all' selected>全部栋层</option>";
		do{
			$theBuild=$myrow["build"];
			$theFloor=$myrow["floor"];
            $thebuildFloor = $theBuild.'-'.$theFloor;
			$buildFloor=$buildFloor==""?$thebuildFloor:$buildFloor;
			if($buildFloor==$thebuildFloor){
				echo"<option value='$thebuildFloor' selected>$theBuild 栋 $theFloor 层</option>";
				$SearchRows .=" AND P.cName like '$buildFloor-%'";
				}
			 else{
			 	echo"<option value='$thebuildFloor'>$theBuild 栋 $theFloor 层</option>";
				}
			}while($myrow = mysql_fetch_array($result));
		}
	echo"</select>&nbsp; ";

	echo "<select name='ProductType' id='ProductType' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT T.TypeId,T.Letter,T.TypeName
	FROM $DataIn.producttype T 
	LEFT JOIN $DataIn.productdata P ON P.TypeId=T.TypeId 
	LEFT JOIN $DataIn.pands S ON S.ProductId=P.ProductId 
	WHERE P.CompanyId=$CompanyId $SearchRows GROUP BY P.TypeId order by T.mainType,T.Letter",$link_id);
	echo "<option value='all' selected>全部类型</option>";
	while ($myrow = mysql_fetch_array($result)){
			$TypeId=$myrow["TypeId"];
			$TypeName = $myrow["TypeName"];
			if ($ProductType==$TypeId){
				echo "<option value='$TypeId' selected>$TypeName</option>";
				}
			else{
				echo "<option value='$TypeId' >$TypeName</option>";
				}
			}
		echo"</select>&nbsp; ";
	$TypeIdSTR=$ProductType==""?"":" AND P.TypeId=".$ProductType;
	$SearchRows.=$TypeIdSTR;
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
$helpFile=1;
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;$tId=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT A.ProductId,P.cName,P.eCode,P.TestStandard,P.Weight,P.MainWeight,P.Code,C.Forshort
FROM $DataIn.pands A
LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
where 1 $SearchRows GROUP BY A.ProductId order by A.ProductId DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$dp=anmaIn("download/productfile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
        $ClientForshort=$myRow["Forshort"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$Weight=zerotospace($myRow["Weight"]);
		$MainWeight=$myRow["MainWeight"]==0?"&nbsp;":$myRow["MainWeight"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getProductImage.php";
        $Code=$myRow["Code"];
        include "../model/subprogram/code_compare.php";
		//echo strlen($TempCodeNum);
		//读取配件数
		$PO_Temp=mysql_query("select count(*) from $DataIn.pands where ProductId=$ProductId",$link_id);
		$PO_myrow = mysql_fetch_array($PO_Temp);
		$numrows=$PO_myrow[0]+1;
		//高清图片检查
		$ProductIdOut=$ProductId;
		$checkImgSQL=mysql_query("SELECT Id FROM $DataIn.productimg WHERE ProductId=$ProductId",$link_id);
		if($checkImgRow=mysql_fetch_array($checkImgSQL)){
			$fp=anmaIn($ProductId,$SinkOrder,$motherSTR);
			$ProductIdOut="<span onClick='OpenOrLoad(\"$dp\",\"$fp\",\"\",\"product\")' style='CURSOR: pointer;color:#FF6633'>$ProductIdOut</span>";
			}
		//超重检查
		include "../model/subprogram/box_loadbearing.php";
		
		$bomflowFile="../download/bomflow/" . $ProductId . ".png";
		$onclickStr="&nbsp;";
		if (file_exists($bomflowFile)){
			$onclickStr="<div onclick='createBomflow($ProductId) ' style='color:#FF0000'>View</div>";
		}

		echo"<table class='ListTableUd' width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
		echo"<td rowspan='$numrows' scope='col' height='21' width='$Field[$m]' class='A0111' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$ProductId'>
		</td>";
		$m=$m+2;
        $unfrist=$Field[$m]+1;
		echo"<td class='A0101' width='$unfrist' rowspan='$numrows' scope='col' align='center'>$j</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$ProductIdOut<BR>$ClientForshort</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$TestStandard</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$eCode<br>$TempCodeNum</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='right'>$MainWeight</td>";
		$m=$m+2;		
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='right'>$Weight</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$LoadBearingINFO</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$CaseReport</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$onclickStr</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$BoxFile</td>";
		$m=$m+2;
		if($numrows>0){
			//从配件表和配件关系表中提取配件数据	  
			$StuffResult = mysql_query("SELECT D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate,D.Gremark,
			D.StuffId,D.TypeId,D.SendFloor,A.Relation,A.Id,MT.TypeColor,MT.Id AS MTID,U.Name AS UnitName,S.uName  AS bpRateName,D.Estate 
				FROM $DataIn.pands A
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
				LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
				LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
				LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id=ST.mainType
				LEFT JOIN $DataIn.standbyrate  S  ON S.Id=A.bpRate
				WHERE A.ProductId='$ProductId'  
				ORDER BY MT.SortId,A.Id",$link_id);
			$k=1;
			$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
				do{	
					$n=$m;
					$PandsId=$StuffMyrow["Id"];
					$StuffId=$StuffMyrow["StuffId"];
					$StuffCname=$StuffMyrow["StuffCname"];
					$UnitName=$StuffMyrow["UnitName"]==""?"&nbsp;":$StuffMyrow["UnitName"];
					$TypeId=$StuffMyrow["TypeId"];
					$Price=$StuffMyrow["Price"];
					$MTID=$StuffMyrow["MTID"];
				    $bpRateName=$StuffMyrow["bpRateName"] ==""?"&nbsp;":$StuffMyrow["bpRateName"] ;
				    if($bpRateName!="")$bpRateName="<a href='standbyrate_read.php'   target='_blank'>$bpRateName</a>";
					$TypeColor=$StuffMyrow["TypeColor"];
					$Diecut=$StuffMyrow["Diecut"]==""?"&nbsp;":$StuffMyrow["Diecut"];
		            $Cutrelation=$StuffMyrow["Cutrelation"]==0?"&nbsp;":$StuffMyrow["Cutrelation"];
					$Relation=$StuffMyrow["Relation"];
					$SSMMyrow = mysql_fetch_array(mysql_query("SELECT M.Name,P.Forshort 
					FROM $DataIn.bps B
					LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId 
					LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId
					LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
					WHERE B.StuffId='$StuffId'",$link_id));
					$Name=$SSMMyrow["Name"]==""?"&nbsp;":$SSMMyrow["Name"];
					$Forshort=$SSMMyrow["Forshort"]==""?"&nbsp;":$SSMMyrow["Forshort"];
					//配件名称
					$theDefaultColor=$TypeColor;
					if($MTID>0){
						$MTCOLOR="bgcolor='$theDefaultColor'";
						}
					else{
						$MTCOLOR="";
						}
						$Estate=$StuffMyrow["Estate"];
						$EstateColor=$Estate>0?$MTCOLOR:"bgcolor='#FF0000'";
					    //二级BOM表
					    $showSemiStr="&nbsp;";$showSemiTable="";$colspan=13;
					    $CheckSemiSql=mysql_query("SELECT A.Id FROM $DataIn.semifinished_bom A  WHERE  A.mStuffId='$StuffId' LIMIT 1",$link_id);
	                    if($CheckSemiRow=mysql_fetch_array($CheckSemiSql)){
	                          $showSemiStr="<img onClick='ShowDropTable(SemiTable$tId,showtable$tId,SemiDiv$tId,\"semifinishedbom_ajax\",\"$StuffId|$k\",\"admin\");'  src='../images/showtable.gif'  title='显示或隐藏二级BOM资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='showtable$tId'>";
			                  $showSemiTable="<tr id='SemiTable$tId' style='display:none;background:#83b6b9;'><td colspan='$colspan'><div id='SemiDiv$tId' width='720'></div></td></tr>"; 
	                          $tId++;
	                        }
                        
                    include"../model/subprogram/stuff_Property.php";//配件属性
					if($k>1){echo"<tr  bgcolor='$theDefaultColor'>";}
                    echo"<td  class='A0100' align='center' width='$Field[$n]'  $EstateColor>$showSemiStr</td>";
                    $n=$n+2;
                    echo "<td class='A0101' width='$Field[$n]' align='center' $EstateColor>$k</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' $MTCOLOR>$StuffId</td>";
					$n=$n+2;
					$Picture=$StuffMyrow["Picture"];
					$Gfile=$StuffMyrow["Gfile"];
					$Gstate=$StuffMyrow["Gstate"]; 
					$Gremark=$StuffMyrow["Gremark"];
					include "../model/subprogram/stuffimg_Gfile.php";	//图档显示		
					//检查是否有图片
					include "../model/subprogram/stuffimg_model.php";
				    $SendFloor=$StuffMyrow["SendFloor"];
				    include "../model/subprogram/stuff_GetFloor.php";
				    $SendFloor=$SendFloor=""?"&nbsp":$SendFloor;
                    $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId' target='_blank'>查看</a>";
		
					echo"<td class='A0101' width='$Field[$n]' $MTCOLOR>$StuffCname</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' $MTCOLOR>$Gfile</td>";
                    $n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' $MTCOLOR>$OrderQtyInfo</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' $MTCOLOR>$Price</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' $MTCOLOR>$UnitName</td>";
					$n=$n+2;					
					echo"<td class='A0101' width='$Field[$n]' align='center' $MTCOLOR>$Relation</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' $MTCOLOR>$bpRateName</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' $MTCOLOR>$Name</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' $MTCOLOR>$Forshort</td>";
					$n=$n+2;
					echo"<td class='A0101' width='' align='center' $MTCOLOR>$SendFloor</td>";
					echo"</tr>";
                    echo $showSemiTable . $ComboxTable;
					$k++;
					$i++;
					} while ($StuffMyrow = mysql_fetch_array($StuffResult));
					echo "</table>";
				}//if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
			}//结束存在配件表
		$j++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
// $ChooseFun="N";
$ColsNumber = 11;//
include "../model/subprogram/read_model_menu.php";
?>
<script language = "JavaScript"> 
function createBomflow(ProductId)
{
	window.open("bomflow/createbomflow.php?FromPage=pands&ProductId="+ProductId+"&r="+Math.random(),"BackData","dialogHeight =650px;dialogWidth=1200px;center=yes;scroll=yes");	       
}
//增加td点击选择table的功能 add by ckt 2018-01-04
jQuery('.ListTableUd').each(function(){
	var theMerge = <?php echo $ColsNumber?>;
	var that = this;
	jQuery(this).find('td:lt('+theMerge+')').click(function(){
		var host = jQuery(this).parent().find('td:first input')[0];
		var tds = jQuery(that).find('td:lt('+theMerge+')');
		if(host.checked){
			host.checked = false;
			tds.removeAttr('bgcolor');
		}else{
			host.checked = true;
			tds.attr('bgcolor', '#FFCC99');
		}	
	})		
})
</script>
