<?php
//电信-zxq 2012-08-01
//代码共享-EWEN
defined('IN_COMMON') || include '../basic/common.php';

if ($DataIn==""){
    include "../basic/chksession.php";
    include "../basic/parameter.inc";
    include "../model/subprogram/sys_parameters.php";
	}
ob_start(); //生成页面开始
include "../desktask/desk1_total.php";  // 加入一些要显示数字的统计在这里，可以公用
?>
<table width="100%" >
	<tr>
		<td width="33%" valign="top" style="border-right:1px dashed #cccccc;">
                       <table cellpadding="0" cellspacing="0" border="0" width="100%">
		<?php
		//快捷方式第一列权限
	  	$TResult01 = mysql_query("SELECT A.ItemId,A.Title,A.Extra FROM $DataPublic.tasklistdata A LEFT JOIN $DataIn.taskuserdata B ON B.ItemId=A.ItemId WHERE A.TypeId=1 AND A.Estate=1 AND A.InCol=1 AND B.UserId='$Login_P_Number' AND (A.cSign=0 OR A.cSign='$Login_cSign') ORDER BY A.Oby",$link_id);
	  	if($TRow01 = mysql_fetch_array($TResult01)){
			do{
				$ItemId=$TRow01["ItemId"];
				$Title=$TRow01["Title"];
				$Extra=$TRow01["Extra"];
				if ( stripos($Extra,"http:")===false ){
					$Extra="../" . $Extra;
				}
              $tmpTitle="";
			 switch($ItemId){
                    case 101:
                    case 173://新单
					case 193:  //未确定订单
					case 194:  //客户加急订单
                    case 205://客户库存
					case 212://可备料
					case 213://已备料
                    case 215://已送料配件(全检)
                    case 216://待出订单
                    case 228://已送料配件(抽检)
                    case 229://检讨报告
					case 233://客户退回
					case 236://PI逾期
					case 237://PI未传
					case 238://当月预出
						include "subtask/subtask-".$ItemId.".php";
						break;
				   }
                /* if($ItemId==101){
                        $outputinfo1.="<tr><td align='left' >&nbsp;<a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)'>◆$Title</a></td><td align='right' >$tmpTitle</td></tr>";
                       }
                else{*/
                        $outputinfo1.="<tr><td align='left' >&nbsp;<a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)'>◆$Title</a></td><td align='right' ><a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)'>$tmpTitle</a></td></tr>";
                    //  }
				 // $outputinfo1.="<li><a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)'>◆$Title</a><span class='dataright'>$tmpTitle</span></li>";
				}while ($TRow01 = mysql_fetch_array($TResult01));
			}
               echo $outputinfo1;
           // echo "<ul class='ullist'>$outputinfo1</ul>";
		?>
             </table>
		</td>
		<td width="33%" valign="top" style="border-right:1px dashed #cccccc;">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
		<?php
		//echo "filecount: $fileCount103";
		//快捷方式第二列权限
	  	$TResult02 = mysql_query("SELECT A.ItemId,A.Title,A.Extra 
			FROM $DataPublic.tasklistdata A
			LEFT JOIN $DataIn.taskuserdata B ON B.ItemId=A.ItemId
			WHERE A.TypeId=1 AND A.Estate=1 AND A.InCol=2 AND B.UserId='$Login_P_Number' ORDER BY A.Oby",$link_id);
		if($TRow02 = mysql_fetch_array($TResult02)){
			do{
				$Title=$TRow02["Title"];
				$Extra=$TRow02["Extra"];
				if ( stripos($Extra,"http:")===false ){
					$Extra="../" . $Extra;
				}
				$ItemId=$TRow02["ItemId"];
                $tmpTitle="";
				switch($ItemId){
                    case 165://采购交期
				    case 110://配件报废统计
                    case 218://禁用配件
                    case 200://配件图档初审
				    case 220://备品转入统计
                    case 227://未付货款统计
				//    case 221://建议禁用配件
				    case 224://建议禁用产品
				    case 180://生产工期
				       include "subtask/subtask-".$ItemId.".php";
				        break;
					case  103:  //表示打印任务  include "Admin/desk1_total.php";
					    if ($fileCount103!="")
						//$tmpTitle="<font color='red'>未传:$fileCount103</font>";
                         $tmpTitle="<a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)' title='未上传'><font color='red'>$fileCount103</font></a>";
						break;

					}
                if($ItemId==165||$ItemId==103){
                         $outputinfo2.="<tr><td align='left' >&nbsp;<a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)'>◆$Title</a></td><td align='right' >$tmpTitle</td></tr>";
                         }
                 else{
                 /*
                        if($ItemId==110 || $ItemId==220){//特别长的分行显示。
                               $outputinfo2.="<tr><td colspan='2'>&nbsp;<a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)'>◆$Title</a></td></tr><tr><td colspan='2' align='right'><a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)'>$tmpTitle</a></td></tr>";
                                }
                        else{
                        */
                         $outputinfo2.="<tr><td align='left' >&nbsp;<a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)'>◆$Title</a></td><td align='right' ><a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)'>$tmpTitle</a></td></tr>";
                         //      }
                        }
			 //  $outputinfo2.="<li><a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)'>◆$Title</a><span class='dataright'>$tmpTitle</span></li>";
				}while ($TRow02 = mysql_fetch_array($TResult02));
			}
               echo $outputinfo2;
          //  echo "<ul class='ullist'>$outputinfo2</ul>";
		?>
             </table>
		</td>
		<td width="34%" valign="top" >
            <table cellpadding="0" cellspacing="0" border="0" width="90%">
		<?php
		//快捷方式第三列权限
	  	$TResult03 = mysql_query("SELECT A.ItemId,A.Title,A.Extra 
			FROM $DataPublic.tasklistdata A
			LEFT JOIN $DataIn.taskuserdata B ON B.ItemId=A.ItemId
			WHERE A.TypeId=1 AND A.Estate=1 AND A.InCol=3 AND B.UserId='$Login_P_Number' ORDER BY A.Oby",$link_id);
		if($TRow03 = mysql_fetch_array($TResult03)){
			do{
				$Title=$TRow03["Title"];
				$Extra=$TRow03["Extra"];
				if ( stripos($Extra,"http:")===false ){
					$Extra="../" . $Extra;
				}
				$ItemId=$TRow03["ItemId"];
                $tmpTitle="";
				switch($ItemId){
					  case 203:
					        include "subtask/subtask-203.php";
					     break;
					}
                $outputinfo3.="<tr> <td align='left' >&nbsp;<a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)'>◆$Title</a></td><td align='right'><a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)'>$tmpTitle</a></td></tr>";
			  // $outputinfo3.="<li><a href='$Extra' target='_blank' onclick='ClickTotal(this,0,$ItemId)'>◆$Title</a><span class='dataright'>$tmpTitle</span></li>";
				}while ($TRow03 = mysql_fetch_array($TResult03));
			}
         echo $outputinfo3;
		   ?>
            </table>
		</td>
	</tr>
</table>
<?php
$desk1_File="desk1/desk1_" . $Login_P_Number . ".inc";
$content = ob_get_contents();//取得php页面输出的全部内容
$fp = fopen($desk1_File, "w");
fwrite($fp, $content);
fclose($fp);