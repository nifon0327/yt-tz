<?php 
include "../model/modelhead.php";
$ColsNumber=8;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="cw_otherin";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="其他收入";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理		
		$sumCols="5";			//求和列,需处理
		$MergeRows=8;
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
			$Th_Col="更新|35|结付日期|70|结付凭证|35|结付回执|35|结付备注|35|对帐单|40|实付金额|60|结付银行|100|选项|50|序号|35|类别|110|收款日期|70|收款单号|100|收款金额|55|币种|55|收款备注|200|状态|40";
		$EstateSTR0="selected";
		$ActioToS="1,15,16,26";
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
	    $ColsNumber=8;			
		$sumCols="5";			//求和列,需处理
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
		$Th_Col="选项|60|序号|40|类别|110|收款日期|80|收款单号|100|收款金额|80|币种|50|收款备注|380|状态|40|操作人|70";
		$Estate=3;
		$EstateSTR3="selected";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="1,18,15,26";}
		else{$ActioToS="1,18,26";}
		include $funFrom."_cw3.php";
		break;
	}
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
			var url="cw_otherin_ajax.php?Mid="+Mid+"&RowId="+RowId; 
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