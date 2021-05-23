<?php
//电信-zxq 2012-08-01
//代码共享-EWEN
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/subprogram/sys_parameters.php";
//USD汇率
$checkCurrency = mysql_fetch_array(mysql_query("SELECT Symbol,Rate FROM $DataIn.currencydata WHERE Symbol='USD' ORDER BY Id LIMIT 1", $link_id));
$USDstr = $checkCurrency["Symbol"];
$USDRate = sprintf("%.2f", $checkCurrency["Rate"]);
$USDInfo = $USDstr . "汇率:" . $USDRate;
$HzRate = $HzRate * 100;

// 需分内员工还是外员工
if ($Login_uType == 1) { // 内部员工
    $pResult = mysql_fetch_array(mysql_query("SELECT
                    A.Name,A.ExtNo,B.Name AS Branch,C.WorkNote,C.WorkTime,D.CShortName
                    FROM $DataIn.staffmain A
                    INNER JOIN $DataIn.branchdata B ON B.Id=A.BranchId
                    INNER JOIN $DataIn.jobdata C ON C.Id=A.JobId
                    INNER JOIN $DataIn.companys_group D ON D.cSign=A.cSign
                    WHERE A.Number='$Login_P_Number' ORDER BY A.Id LIMIT 1
                    ", $link_id));

    $WorkNote = $pResult["WorkNote"];
    $WorkTime = $pResult["WorkTime"];
    $Name = $pResult["Name"];
    $Branch = $pResult["CShortName"] . '-' . $pResult["Branch"];

    //部门小组 by.lwh
    $_SESSION["Login_GroupName"] = $pResult["Branch"];
    //人员姓名 by.lwh
    $_SESSION["Login_Name"] = $Name;

    if ($WorkNote != '' && $WorkNote != ' ') {
    }

    if ($WorkTime != "" && $WorkTime != ' ') {
    }
}
else {                //外部员工
    $FromBranch = "外部人员";
    $pResult = mysql_fetch_array(mysql_query("SELECT S.Name FROM $DataIn.ot_staff S WHERE S.Number='$Login_P_Number' ORDER BY S.Id LIMIT 1", $link_id));
    $Name = $pResult["Name"];
    $Branch = "外部人员";

    //部门小组 by.lwh
    $_SESSION["Login_GroupName"] = $Branch;
    //人员姓名 by.lwh
    $_SESSION["Login_Name"] = $Name;

}
?>
<html>
<head>
  <META content="MSHTML 6.00.2900.2722" name=GENERATOR>
    <?php
    include "../model/characterset.php";
    include "../model/modelfunction.php";
    echo "<link rel='stylesheet' href='../model/css/read_line.css'>";
    echo "<link rel='stylesheet' href='../model/css/sharing.css'>";
    echo "<link rel='stylesheet' href='../model/css/topwin.css'>";
    echo "<link rel='stylesheet' href='../model/css/ac_ln.css'>";
    echo "<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
    ?>
  <style>
    .top-td > i + a {
      color: #48bbcb !important;
    }

    a {
      font: 14px 思源黑体;
    }

    table {
      padding: 0;
      -webkit-border-horizontal-spacing: 0;
      -webkit-border-vertical-spacing: 0;
    }

    .top-td {
      width: 76px;
      height: 76px;
      text-align: center;
      padding: 0;
    }
  </style>
</head>
<body style='padding:0' onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;" style="height:76px">
<table width="100%" height='76' border="0" cellspacing="0" bgcolor="#fff" style="position: absolute">
  <tr>
    <td rowspan="1" scope="col">
      <table>
        <tr>
            <?php
            $lMenuResult = mysql_query("SELECT A.ModuleId,B.ModuleName,B.icon
                        FROM $DataIn.upopedom A
                        INNER JOIN $DataPublic.funmodule B ON B.ModuleId=A.ModuleId
                        WHERE 1 AND A.Action>0 AND B.TypeId=0 AND A.UserId='$Login_Id' AND B.Estate=1 ORDER BY B.OrderId", $link_id);

            if ($lMenuRow = mysql_fetch_array($lMenuResult)) {
                do {
                    $ModuleId = $lMenuRow["ModuleId"];//加密
                    $icon = $lMenuRow['icon'];
                    $Mid = anmaIn($ModuleId, $SinkOrder, $motherSTR);
                    $ModuleName = $lMenuRow["ModuleName"];

                    echo "<td class='top-td'><i style='font-size:24px;color:#48bbcb' class='iconfont icon-$icon'></i>
<a href='mcright.php?Mid=$Mid' target='rightFrame' style='display:inline-block;height:20px'>$ModuleName</a></td>";
                } while ($lMenuRow = mysql_fetch_array($lMenuResult));
            }
            ?>
        </tr>
      </table>
    <td class="top-td" style="margin-left: 10px;">
      <a href='../public/loginlog_read.php' target='_blank' style="line-height: 74px;height:74px;display:inline-block">登录记录</a>
    </td>
    <td class="top-td" style="margin-left: 10px;">
      <a href="../exit.php" target="_parent" style="line-height: 74px;height:74px;display:inline-block">退出</a>
    </td>
    <td style="margin-left: 10px;height: 74px;line-height: 74px;width:74px;font: 14px 思源黑体;">
      |　&nbsp;<?php echo $Name ?>
    </td>
    <td style="text-align: center;vertical-align: middle;display:table-cell;">
      <div style="display:inline-block;width:38px;height:38px;overflow: hidden;border-radius: 100%">
        <img src="https://ww2.sinaimg.cn/large/005BYqpggy1fys2f2j8a3j30dv0dv3z4.jpg" alt="" width="38" height="38">
      </div>
    </td>
  </tr>
</table>
<?php
include "../desk/showcalendar.php";
?>
</body>
</html>
<script src="/plugins/js/jquery.min.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
//toOnline(); //需先更新online_count.php 暂时禁用(20180518 by xfy)
//var getOnlone = function () {
//    $.ajax({
//        url: '/desk/online_count.php',
//        success: function (result) {
//            var records = result.split('`'),
//                $showNews = $('#show_news'),
//                $showSms = $('#show_sms'),
//                $workInfo = $('#myWorkInfo');
//            $showNews.html(records[0] == 0 ? '0' : records[0]);
//            $showSms.html(records[1] == 0 ? '0' : records[1]);
//            //$workInfo.html(records[2] == 0 ? ''  : records[2]);
//            //alert(result);
//            if (records[2] != 0) {
//                if (records[2] == 1) {//被踢出
//                    alert("系统更新或网络掉线等原因!你的帐号将退出！如有问题请跟管理员反映!");
//                } else {//重复登录
//                    alert("你的帐号在 " + records[2] + " 重新登录!当前窗口将退出！");
//                }
//                parent.location.href = "http://" + window.location.host;
//            } else {
//                setTimeout(function () {
//                    getOnlone();
//                }, 100000)
//            }
//        }
//    });
//    return true;
//};//eo getOnline();

$(document).ready(function () {
//    getOnlone();

    $('a').mouseover(function () {
        $(this).parent().css({'background-color': '#EFF5F5'});
    });
    $('a').mouseout(function () {
        $(this).parent().css({'background-color': '#fff'});
    });

    $('i').mouseover(function () {
        $(this).css({'cursor': 'pointer'});
        $(this).parent().css({'background-color': '#EFF5F5'});

    });
    $('i').mouseout(function () {
        $(this).css({'cursor': 'default'});
        $(this).parent().css({'background-color': '#fff'});
    })

    $('i').click(function () {
        $(this).parent().find('a')[0].click();
        $('td').css('border', '0');
        $(this).parent().css({'border-bottom': '3px solid #48bbcb'});
    });

    $('a').click(function () {
        $('td').css('border', '0');
        $(this).parent().css({'border-bottom': '3px solid #48bbcb'});
    });

});

</script>
