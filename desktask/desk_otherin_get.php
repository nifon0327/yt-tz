<?php 
include "../model/modelhead.php";
$ColsNumber=8;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="desk_otherin";			//必选参数：功能模块
$nowWebPage=$funFrom."_get";		//必选参数：功能页面
$Log_Item="其他收入";
$ColsNumber=8;			
$sumCols="5";			//求和列,需处理
$Th_Col="选项|60|序号|40|类别|110|收款日期|80|收款单号|100|收款金额|80|币种|50|收款备注|380|状态|50|操作人|70";
$ActioToS="1";
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
include "../model/subprogram/read_model_3.php";
//过滤条件
if($From!="slist"){
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo"$CencalSstr";

//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.getmoneyNO, S.Amount, C.Symbol AS Currency, S.payDate, S.Remark, S.Estate, S.Locks, S.Operator,T.Name AS TypeName
 	FROM $DataIn.cw4_otherinsheet S 
   LEFT JOIN $DataPublic.cw4_otherintype T ON T.Id=S.TypeId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1 $SearchRows  ORDER BY S.getmoneyNO DESC ";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myResult  && $myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/otherin/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$Id=$myRow["Id"];
		$getmoneyNO=$myRow["getmoneyNO"];
		$Amount=$myRow["Amount"];
		$Currency=$myRow["Currency"];	
		$payDate=$myRow["payDate"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];		
		$Estate=$myRow["Estate"];		
		$TypeName=$myRow["TypeName"];	
        switch($Estate){
              case "0":
		                $Estate="<div class='greenB'>已结付</div>";
                 break;
              case "3":
		                $Estate="<div class='greenB'>未结付</div>";
                 break;
              case "2":
		                $Estate="<div class='blueB'>审核中...</div>";
                 break;

            }
		$Locks=$myRow["Locks"];	
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";   
		$f1=anmaIn($getmoneyNO,$SinkOrder,$motherSTR);
		$getmoneyNO="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$getmoneyNO</a>";
		$showPurchaseorder="<img onClick='sOrhOtherIn(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

		$ValueArray=array(
			array(0=>$TypeName,1=>"align='center'"),
			array(0=>$payDate,1=>"align='center'"),
			array(0=>$getmoneyNO,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Currency,1=>"align='center'"),
			array(0=>$Remark),
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
if ($myResult )  $RecordToTal= mysql_num_rows($myResult); else $RecordToTal=0;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
ChangeWtitle($SubCompany.$Log_Item."其它收入明细");
include "../model/subprogram/read_model_menu.php";
?>
<script>
function sOrhOtherIn(e,f,Order_Rows,Mid,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(Mid!=""){			
			var url="../public/cw_otherin_ajax.php?Mid="+Mid+"&RowId="+RowId; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;					
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
		}
	}

</script>