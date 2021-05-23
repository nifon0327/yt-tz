<?php 
//电信-zxq 2012-08-01
/*
MC、DP共享代码
*/
//步骤1
include "../model/modelhead.php";
echo "<SCRIPT src='../model/pagefun_Sc.js' type=text/javascript></script>";


//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
$sumCols="6,10";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 供应商税款列表");
$funFrom="cw_gyssk";
$Th_Col="选项|80|序号|40|请款日期|75|货款月份|60|供应商|80|货币|40|税款金额|70|加税率|50|说明|300|发票号|80|发票金额|70|收到发票日期|75|会计确认|50|状态|40|请款人|50";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,14,4,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付
$TempEstateSTR="EstateSTR".strval($Estate); 
$$TempEstateSTR="selected";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){	
	$SearchRows=$Estate==""?"":"and S.Estate=$Estate";
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.cw2_gyssksheet S group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateText</option>";
				$SearchRows.=" and DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";
		}
	//结付状态
	$EstateResult = mysql_query("SELECT S.Estate FROM $DataIn.cw2_gyssksheet S WHERE 1 $SearchRows GROUP BY S.Estate ORDER BY S.Estate DESC",$link_id);
	if($EstateRow = mysql_fetch_array($EstateResult)) {
		echo"<select name='Estate' id='Estate' onchange='document.form1.submit()'>";
		echo"<option value='' $EstateSTR>全  部</option>";
		do{
			$Estate=$EstateRow["Estate"];
			
			switch($Estate){
				case "0":
					echo"<option value='0' $EstateSTR0>已结付</option>";
				break;
				case "1":
					echo"<option value='1' $EstateSTR1>未处理</option>";
				break;
				case "2":
					echo"<option value='2' $EstateSTR2>请款中</option>";
				break;
				case "3":
					echo"<option value='3' $EstateSTR3>请款通过</option>";
				break;
				}
			}while($EstateRow = mysql_fetch_array($EstateResult));
		echo"</select>&nbsp;";
		}
		
		// BOM、非BOM分类
		 $TempEstateSTR="GysTypeSTR".strval($GysType); 
         $$TempEstateSTR="selected";

		echo"<select name='GysType' id='GysType' onchange='document.form1.submit()'>";
		echo"<option value='' $GysTypeSTR>全  部</option>";
		echo"<option value='1' $GysTypeSTR1>BOM</option>";
		echo"<option value='2' $GysTypeSTR2>非BOM</option>";
		echo"</select>&nbsp;";
		
		switch($GysType){
		   case 1: $SearchRows.=" and S.Remark NOT LIKE '%非BOM%'";break;
		   case 2: $SearchRows.=" and S.Remark  LIKE '%非BOM%'";break;
		   default:break;
		}
		
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Mid,S.Forshort,S.PayMonth,S.InvoiceNUM,S.InvoiceFile,S.InvoiceCollect,S.Amount,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,
C.Symbol,S.Rate,S.Getdate,S.Fpamount
 	FROM $DataIn.cw2_gyssksheet S 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1 $SearchRows order by S.Date DESC,S.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"];
		$PayMonth=$myRow["PayMonth"];
		$Forshort="<a href='cw_gyssk_company.php?From=task&chooseMonth=$chooseMonth' target='_blank'><span style='color:#FF6633'>$Forshort</span></a>";
		$Symbol=$myRow["Symbol"];
		$Amount=$myRow["Amount"];
		$Rate=$myRow["Rate"];
		if($Rate!=0)
		   {
		   	 $Rate*=100;
		     $Rate=$Rate."%";
		     }
		else{
             $Rate="&nbsp";
		     }
		$Fpamount=$myRow["Fpamount"]=="0"?"&nbsp;":$myRow["Fpamount"];
		$Getdate=$myRow["Getdate"]=="0000-00-00"?"&nbsp":$myRow["Getdate"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$InvoiceNUM=$myRow["InvoiceNUM"];
		$InvoiceFile=$myRow["InvoiceFile"];
		if ($Login_P_Number==10383 || $Login_P_Number==10868){
		    $InvoiceCollect=$myRow["InvoiceCollect"]==0?"<div style='CURSOR: pointer'  onclick='upCollect(this,$Id)'><img src='../images/edit.gif' title='确认操作' width='13' height='13'></div>":"<div align='center' class='greenB' title='会计已确认'>√</div>";
		}
		else{
		    $InvoiceCollect=$myRow["InvoiceCollect"]==0?"<div align='center' class='yellowB' title='会计未确认...'>×.</div>":"<div align='center' class='greenB' title='会计已确认'>√</div>";	
		}
		$InvoiceCollect=$InvoiceFile==0?"<div align='center' class='redB' title='无发票'>×</div>":$InvoiceCollect;
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
 		$Estate=$myRow["Estate"];
		switch($Estate){
			case "1":
				$Estate="<div align='center' class='redB' title='未处理'>×</div>";
				$LockRemark="";
				break;
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "0":
				$Estate="<div align='center' class='greenB' title='已结付'>√</div>";
				$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
				$Locks=0;
				break;
			}
		if($InvoiceFile==1){
			$InvoiceFile="S".$Id;
			$Dir=anmaIn("download/cwgyssk/",$SinkOrder,$motherSTR);
			$InvoiceFile=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
			//$InvoiceNUM="<span onClick='OpenOrLoad(\"$Dir\",\"$InvoiceFile\",7)' style='CURSOR: pointer;color:#FF6633'>$InvoiceNUM</span>";
			$InvoiceNUM="<a href=\"openorload.php?d=$Dir&f=$InvoiceFile&Type=&Action=7\" target=\"download\">$InvoiceNUM</a>
			 <A onfocus=this.blur();  onclick='ActionToUpFile($Id)' style='CURSOR: pointer;color:#FF6633'> 
							<img src='../images/upFile.gif' style='background:#F00' title='上传' width='9' height='9'>
							</A>
			";
			}
		 else{
		             // $Getdate="&nbsp;";
                        $InvoiceNUM="<div style='color:#3E8F6B' onclick='ActionToUpFile($Id)'>$InvoiceNUM</div>";
             }
			 
		$URL="nonbom6_relation_ajax.php";
		//$URL="test.php";
        $theParam="Id=$Id";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"nonbom\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏关联非BOM采购单.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			
		//财务强制锁:非未处理皆锁定
		$ValueArray=array(
			array(0=>$Date,1=>"align='center'"),
			array(0=>$PayMonth,1=>"align='center'"),
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$Symbol,1=>"align='center'"),				
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Rate,1=>"align='center'"),
			array(0=>$Remark,3=>"..."),
			array(0=>$InvoiceNUM,1=>"align='center'"),
			array(0=>$Fpamount,1=>"align='center'"),
			array(0=>$Getdate,1=>"align='center'"),
			array(0=>$InvoiceCollect,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
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
<script language="JavaScript" type="text/JavaScript">
function ActionToUpFile(upId){
	var funFrom=document.form1.funFrom.value;
        document.form1.action=funFrom+"_upfile.php?ActionId=84&Id="+upId;
        document.form1.target="_self";
        document.form1.submit();		
        document.form1.target="_self";
        document.form1.action="";
}

function upCollect(e,upId){
 if(confirm("您确定发票文件已收到？")) {
	    var funFrom=document.form1.funFrom.value;
		myurl="cw_gyssk_ajax.php?ActionId=801&Id="+upId;
		var ajax=InitAjax(); 
　		ajax.open("GET",myurl,true);
		ajax.onreadystatechange =function(){
　			if(ajax.readyState==4){// && ajax.status ==200
				    if(ajax.responseText=="Y"){//更新成功
				         e.innerHTML="<div align='center' class='greenB' title='会计已确认'>√</div>";
				    }
				    else{
					    alert("更新失败!");
				    }
				}
			}
		ajax.send(null); 	
	}
}
</script>