<?php
defined('IN_COMMON') || include '../basic/common.php';

/*
$DataIn.tasklistdata	特殊功能表
$DataIn.taskuserdata    特殊功能权限
功能：列出有权限的桌面任务列表项目
*/
if ($DataIn==""){
    include "../basic/chksession.php";
    include "../basic/parameter.inc";
    include "../basic/config.inc";
    include "../model/modelfunction.php";
    include "../model/subprogram/sys_parameters.php";
    $checkCurrency=mysql_fetch_array(mysql_query("SELECT Symbol,Rate FROM $DataIn.currencydata WHERE Symbol='USD' ORDER BY Id LIMIT 1",$link_id));
    $USDstr=$checkCurrency["Symbol"];
    $USDRate=sprintf("%.2f",$checkCurrency["Rate"]);
    $USDInfo=$USDstr."汇率:".$USDRate;
    $pcValue=$pcValue*100;
}
$i=1;
$OutputInfo="";
$TResult = mysql_query("SELECT U.ItemId,L.Title,L.Extra FROM $DataIn.tasklistdata L LEFT JOIN $DataIn.taskuserdata U ON U.ItemId=L.ItemId WHERE L.TypeId>1 AND L.Estate=1 AND L.TypeId<6 AND U.UserId=$Login_P_Number   ORDER BY L.TypeId,L.Oby",$link_id);
if($TRow = mysql_fetch_array($TResult)){
	do{
		$ItemId=$TRow["ItemId"];
		$Title=$TRow["Title"];
		$Extra=$TRow["Extra"];

		if(stripos($Extra,"http:") || stripos($Extra,"SELECT")){
			$Extra = $Extra;
		}else{
			$Extra="../" . $Extra;
		}
		switch($ItemId){
			case 120://现金结存
			case 121://审核通过未结付总额
			case 122://未收客户货款总额
			case 123://未出货订单总额
			case 125://损益表
			case 128: ////末下采购单/采购单审核
			case 189://订单净利分类统计
			case 190://产品净利分类统计
			case 198://本月已出货电子类产品所占比例
				$linkmodule="";
                include "subtask/subtask-get_right.php";  //是否有相应的审核权限，则可以链接
				$newSubtask=0;
				$subtask_File="subtask/subtask-" .$ItemId . "$linkmodule".".inc";
                if(file_exists($subtask_File) && $RefreshDesk!=1){
                	$ftime=date("Y-m-d",filemtime($subtask_File));
                    if ($ftime<date("Y-m-d")){
                    	$newSubtask=1;
                        }
                    else{
                        include $subtask_File;
                        }
                    }
               	else{
               		$newSubtask=1;
                    }
				if ($newSubtask==1){
                	$contentSTR="";
		            include "subtask/subtask-".$ItemId.".php";
                    $OutputInfo.=$contentSTR;
                    $contentSTR="<?php \$OutputInfo.=\"" . $contentSTR . "\";?>";
                    $fp = fopen($subtask_File, "w");
                    fwrite($fp, $contentSTR);
                    fclose($fp);
                    }
                break;

		   	case 219://标准图
			case 179://未上传配件图档
		   	case 142://用户负责的未出订单总额
            case 218://
				include "subtask/subtask-".$ItemId.".php";
				break;
			case 124:
			case 155:
			case 162:
            case 223:
            case 245:
				$OutputInfo.= "<li class=TitleBL>$Title</li>
				<li class=TitleBR><a href='$Extra' target='_blank' style='CURSOR: pointer;color:#FF6633'>详情</a></li>";
				break;
			case 143:
				$checkSql=mysql_fetch_array(mysql_query("SELECT count(*) FROM $DataIn.cg1_stocksheet S WHERE S.Mid=0 AND (S.FactualQty>0 OR S.AddQty>0) AND S.BuyerId=$Login_P_Number",$link_id));
				$OutputInfo.=$checkSql[0]>0? "<li class=DataBL>$Title</li><li class=DataBR><span class='yellowN'>".$checkSql[0]."</span></li>":"";
				break;
			case 153:
				$checkSql=mysql_fetch_array(mysql_query("SELECT count(*) FROM $DataIn.ot1_service S WHERE S.Estate=1 AND S.Servicer=$Login_P_Number",$link_id));
				$OutputInfo.=$checkSql[0]>0? "<li class=DataBL>$Title</li><li class=DataBR><span class='yellowN'>".$checkSql[0]."</span>)</li>":"";
				break;
			case 206:
				$checkSFSql=mysql_fetch_array(mysql_query("SELECT count(*) FROM $DataIn.ch1_shipmain S LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId WHERE S.cwSign>'0' AND C.PayType='1'",$link_id));
				$OutputInfo.=$checkSFSql[0]>0? "<li class=DataBL>$Title</li><li class=DataBR><span class='yellowN'>".$checkSFSql[0]."</span></li>":"";
				break;
			default://审核项目提醒153
				if($Extra!=""){
					$linkmodule="";
					include "subtask/subtask-get_right.php";  //是否有相应的审核权限，则可以链接
					$speContent="";  //特殊审核
					switch($ItemId){  //末下采购单/采购单审核
						default:
							$checkResult=mysql_query($Extra,$link_id);
							if ($checkResult){
							   $checkSql=mysql_fetch_array($checkResult);
								if ($linkmodule!="" && count($checkSql)>0) {
									$OutputInfo.=  count($checkSql)>0?"<li class=DataBL>$Title</li><li class=DataBR><A onfocus=this.blur(); href='../desk/mainFrame.php?Id=$SubModuleId' target='mainFrame' onclick='ClickTotal(this,1,$SubModuleIdTemp)' style='CURSOR: pointer;color:#FF6633'>".$checkSql[0]."</A></li>":"";
									}
								else { //原来不带链接的
									$OutputInfo.=  count($checkSql)>0?"<li class=DataBL>$Title</li><li class=DataBR><span class='yellowN'>".$checkSql[0]."</span></li>":"";
									}
							}  //if
							break;
						} // switch
				} //if
				break;
			}   //switch
		if($TRow["Extra"]>1){
			$i++;
			}
		}while ($TRow = mysql_fetch_array($TResult));
	}  //if
$OutputInfo="<div id=TaskTB><ul>".$OutputInfo."</ul></div>";
echo $OutputInfo;
$desk2_File="desk2/desk2_" . $Login_P_Number . ".inc";
$fp = fopen($desk2_File, "w");
fwrite($fp, $OutputInfo);
fclose($fp);
?>