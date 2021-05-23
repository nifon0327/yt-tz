<?php 
/*电信---yang 20120801
$DataIn.trade_object
$DataPublic.currencydata
$DataIn.trade_object
$DataIn.producttype
$DataIn.pands
$DataIn.productdata
$DataIn.stuffdata
$DataIn.bps
$DataIn.taskuserdata
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=17;
$tableMenuS=500;
ChangeWtitle("$SubCompany 未审核产品检验标准图");
$funFrom="productdata";
$From=$From==""?"ts":$From;
$Th_Col="选项|40|序号|35|客户|50|产品ID|50|中文名|200|Product Code|160|订单<br>下限|50|参考<br>售价|60|货币<br>符号|30|毛利|60|描述|30|所属分类|70|业务初审|60|操作员|50";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//$ActioToS="17";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消
//步骤3：
$nowWebPage=$funFrom."_rs";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT C.CompanyId,C.Forshort,D.Rate,D.Symbol 
	FROM $DataIn.Productdata P 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId 
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE C.Estate=1 AND P.Estate=1 AND P.TestStandard=2 
	GROUP BY P.CompanyId order by C.OrderBY DESC,C.Id",$link_id);
	if($myrow = mysql_fetch_array($result)){
		echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
		do{
			$theCompanyId=$myrow["CompanyId"];
			$Forshort=$myrow["Forshort"];
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$Forshort</option>";
				$Client=$Forshort;
				$Rate=$myrow["Rate"];
				$Symbol=$myrow["Symbol"];
				$SearchRows=" and P.CompanyId=".$CompanyId;
				}
			 else{
			 	echo"<option value='$theCompanyId'>$Forshort</option>";
				}
			}while($myrow = mysql_fetch_array($result));
		echo"</select>";
		}
	
	$result = mysql_query("SELECT T.TypeId,T.Letter,T.TypeName 
	FROM $DataIn.productdata P 
	LEFT JOIN $DataIn.ProductType T ON T.TypeId=P.TypeId
	WHERE T.Estate=1 and P.Estate=1 AND P.TestStandard=2 $SearchRows GROUP BY P.TypeId ORDER BY T.TypeId
	",$link_id);
	if($myrow = mysql_fetch_array($result)){
		echo"<select name='ProductType' id='ProductType' onchange='ResetPage(this.name)'>";
		do{
			$TypeId=$myrow["TypeId"];
			$ProductType=$ProductType==""?$TypeId:$ProductType;
			if ($ProductType==$TypeId){
				echo "<option value='$TypeId' selected>$myrow[Letter]-$myrow[TypeName]</option>";
				$SearchRows.=" and P.TypeId=".$ProductType;
				}
			else{
				echo "<option value='$TypeId'>$myrow[Letter]-$myrow[TypeName]</option>";
				}
			} while ($myrow = mysql_fetch_array($result));
		echo"</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);

$mySql= "SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Unit,P.Moq,P.CompanyId,P.Description,P.Remark,P.pRemark,
	P.TestStandard,P.Date,P.PackingUnit,P.Locks,P.Code,P.Operator,T.TypeName,S.Estate 
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
        LEFT JOIN $DataIn.productstandimg S ON S.ProductId=P.ProductId 
	where 1 $SearchRows AND P.TestStandard=2 order by Estate DESC,Id DESC";
	//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$ProductId=$myRow["ProductId"];$Id=$ProductId;
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$pRemark=trim($myRow["pRemark"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[pRemark]' width='18' height='18'>";
		$Description=$myRow["Description"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Description]' width='18' height='18'>";
		$Price=$myRow["Price"];
		$Moq=$myRow["Moq"]==0?"&nbsp;":$myRow["Moq"];
		$TestStandard=$myRow["TestStandard"];//include"subprogram/teststandard_b.php";
		$TestStandard="<span onClick='viewImage(\"$ProductId\",1,1)' style='CURSOR: pointer; color:#FF00FF; font-weight:bold' title='需更改标准图!!'>$cName</span>";
		$Code=$myRow["Code"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Code]' width='18' height='18'>";
		$Estate=$myRow["Estate"];
                if ($Estate==2){
                    $Estate="<img src='../images/register.gif'  width='30px'/>";
                    $EstateClick="onclick='tsPass(this,$ProductId)'";
                }else{
                    $Estate="<div class='greenB'>√</div>";
                    $EstateClick="";
                }
		$PackingUnit=$myRow["PackingUnit"];
		$uResult = mysql_query("SELECT Name FROM $DataPublic.packingunit WHERE Id=$PackingUnit order by Id Limit 1",$link_id);
		if($uRow = mysql_fetch_array($uResult)){
			$PackingUnit=$uRow["Name"];
			}			
		$Unit=$myRow["Unit"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		//操作员姓名
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$thisCId=$myRow["CompanyId"];		
		$TypeName=$myRow["TypeName"];
		$saleRMB=sprintf("%.4f",$Price*$Rate);//产品销售RMB价格
		
		$StuffResult = mysql_query("SELECT A.Relation,B.Price,E.Rate
		FROM $DataIn.pands A
		LEFT JOIN $DataIn.stuffdata B ON B.StuffId=A.StuffId
		LEFT JOIN $DataIn.bps C ON C.StuffId=B.StuffId
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=C.CompanyId		
		LEFT JOIN $DataPublic.currencydata E ON E.Id=D.Currency		
		where A.ProductId=$ProductId order by A.Id",$link_id);
		if($StuffmyRow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
			$buyRMB=0;
			do{	
				$stuffPrice=$StuffmyRow["Price"];
				$stuffRelation=$StuffmyRow["Relation"];
				$stuffRate=$StuffmyRow["Rate"];
				//成本
				$OppositeRelation=explode("/",$stuffRelation);
				if ($OppositeRelation[1]!=""){//非整数对应关系
					$thisRMB=sprintf("%.4f",$stuffRate*$stuffPrice*$OppositeRelation[0]/$OppositeRelation[1]);
					}
				else{//整数对应关系
					$thisRMB=sprintf("%.4f",$stuffRate*$stuffPrice*$OppositeRelation[0]);
					}
				$buyRMB=$buyRMB+$thisRMB;
				}while($StuffmyRow=mysql_fetch_array($StuffResult));
			$profitRMB=sprintf("%.4f",$saleRMB-$buyRMB);
			$profitRMB=$profitRMB<=0.3?"<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='redB'>$profitRMB</sapn></a>":$profitRMB=$profitRMB<=0.7?"<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='yellowB'>$profitRMB</sapn></a>":"<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='greenB'>$profitRMB</sapn></a>";
			}
		else{
			$profitRMB="<div class='redB'>未设定</div>";
			}
			$ValueArray=array(
				array(0=>$Client),
				array(0=>$ProductId,			1=>"align='center'"),
				array(0=>$TestStandard,				2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$eCode,				3=>"..."),
				array(0=>$Moq,				1=>"align='center'"),
				array(0=>$Price."&nbsp;", 	1=>"align='right'"),
				array(0=>$Symbol,			1=>"align='center'"),
				array(0=>$profitRMB,			1=>"align='center'"),
				array(0=>$Description,		1=>"align='center'"),
				array(0=>$TypeName,			1=>"align='center'"),
				array(0=>$Estate,			1=>"align='center'", 2=>"$EstateClick"),
				array(0=>$Operator,			1=>"align='center'")
				);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
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
include "../model/subprogram/read_model_menu.php";
?>
<script language="javascript">
function tsPass(e,Pid){
 
  if(confirm("初审通过确认?")) {
        var url="Productdata_updated_ajax.php?ProductId="+Pid+"&ActionId=ts"; 
        var ajax=InitAjax(); 
	    ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
		 if(ajax.readyState==4){// && ajax.status ==200
			 if(ajax.responseText=="Y"){//更新成功
                             e.innerHTML="<div class='greenB'>√</div>";
			     e.onclick="";
			     }
			 else{
			    alert ("审核失败！"); 
			  }
			}
		 }
	   ajax.send(null); 
	 }
}
</script>