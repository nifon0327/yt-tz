<?php
//电信-zxq 2012-08-01
//代码-EWEN
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
?>
<html>
<head>
  <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
  <link rel="stylesheet" href="../public/css/bootstrap.min.css">
  <link rel='stylesheet' href='../model/css/ac_ln.css'>
  <script src='../model/jquery_corners/jquery-1.8.3.js'></script>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <META content="MSHTML 6.00.2900.2722" name=GENERATOR>
  <style type="text/css">
    <!--
    BODY {
      MARGIN: 0;
      FONT: 12px 思源黑体;
      background: #4a5864;
      overflow-x: hidden;
    }

    TABLE {
      border: 0;
    }

    TD {
      FONT: 12px 思源黑体;
    }

    IMG {
      border: 0;
      VERTICAL-ALIGN: bottom;
    }

    A {
      FONT: 14px 思源黑体;
      COLOR: #C9D7E2;
      TEXT-DECORATION: none;
    }

    A:hover {
      FONT: 14px 思源黑体;
      COLOR: #C9D7E2;
      TEXT-DECORATION: none;
    }

    A:active {
      FONT: 14px 思源黑体;
      COLOR: #C9D7E2;
      TEXT-DECORATION: none;
    }

    a:link {
      FONT: 14px 思源黑体;
      COLOR: #C9D7E2;
      text-decoration: none;
    }

    a:visited {
      FONT: 14px 思源黑体;
      COLOR: #C9D7E2;
      text-decoration: none;
    }

    .link-font-style:hover {
      FONT: 14px 思源黑体;
      COLOR: #FFFFFF;
      TEXT-DECORATION: none;
    }

    .sec_menu {
      BORDER-RIGHT: white 1px solid;
      BACKGROUND: #CCCCCC;
      OVERFLOW-X: hidden;
      BORDER-LEFT: white 1px solid;
      BORDER-BOTTOM: white 1px solid
    }

    .menu_title {
    }

    .menu_title SPAN {
      FONT: 16px 思源黑体;
      LEFT: 8px;
      COLOR: #FFFFFF;
      POSITION: relative;
      TOP: 2px
    }

    .menu_title2 {
    }

    .menu_title2 SPAN {
      LEFT: 8px;
      COLOR: #FF9966;
      POSITION: relative;
      TOP: 2px
    }

    .Qj {
      color: #F00
    }

    .sub-list {
      clear: both;
      position: relative;
      margin: 0;
      float: left;
      text-align: left;
      width: 100%;
      height: 100%;
      overflow: auto;
    }

    .link-font-style {
      font-size: 14px !important;
      color: #FFF !important;
    }

    .sub-list h2 {
      font-weight: normal;
      margin-bottom: 0;
    }

    .sub-list ul {
      margin: 0;
      width: 100%;
      /*border-top: 1px solid #ccc;*/
      /*margin-bottom: 10px;*/
      padding: 0;
      float: left;
      list-style: none;
    }

    .sub-list li {
      border-bottom: 1px solid #ccc;
      padding: 0;
      vertical-align: middle;
      background: #ddd;
      width: 100%;
    }

    .sub-list a {
      text-decoration: none;
      display: block;
      font-weight: normal;
      font-size: 12px;
      color: #3f3f3f;
      text-shadow: none;
    }

    /* 滚动条样式*/
    ::-webkit-scrollbar {
      width: 10px;
      height: 14px;
    }

    ::-webkit-scrollbar-thumb {
      border-radius: 999px;
      border: 5px solid transparent;
    }

    ::-webkit-scrollbar-track {
      background-color: rgba(74, 88, 100, 1);
    }

    ::-webkit-scrollbar-thumb {
      min-height: 15px;
      background-clip: content-box;
      box-shadow: 0 0 0 3px rgba(0, 0, 0, .2) inset;
      border-left: 0;
    }

    ::-webkit-scrollbar-corner {
      background: transparent;
    }

    /*背景色及边框*/
    .showbgc {
      background-color: #37414a;
      border-left: 2px solid #48bbcb !important;
    }

    .activebgc {
      background-color: #37414a;
      border-left: 2px solid #48bbcb !important;
    }
  </STYLE>
  <script type="text/javascript">
  function liClick(e) {
      var lists = document.getElementsByName("mainLi");
      for (var i = 0; i < lists.length; i++) {
          lists[i].style.backgroundColor = "#EEEEEE";
      }
      lists = document.getElementsByName("subLi");
      for (var i = 0; i < lists.length; i++) {
          lists[i].style.backgroundColor = "#DDDDDD";
      }
      var lists = document.getElementsByName("weekLi");
      for (var i = 0; i < lists.length; i++) {
          lists[i].style.backgroundColor = "#CCFF99";
      }
      // var weekLi=document.getElementById("weekLi");
      // if (weekLi!= undefined)  weekLi.style.backgroundColor="#CCFF99";
      e.style.backgroundColor = "#FFFFFF";
  }
  </script>
</head>
<body>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" frame="vsides" rules="cols">
  <tr>
    <td align="center" valign="top" id="menubg" style="border-right-width: 0;border-left-width: 0;">
        <?php
        $Mid = _get("Mid");

        // 模块ID，对应表 funmodule 字段 ModuleId
        if ($Mid != "") {
            //解密
            echo "<TABLE cellSpacing=0 cellPadding=0 width='200' align=center><TBODY>";
            $outArray = explode("|", $Mid);
            $RuleStr = $outArray[0];
            $EncryptStr = $outArray[1];

            // 真实的 ModuleId
            $Mid = anmaOut($RuleStr, $EncryptStr);
            $result = mysql_query("SELECT F.ModuleName,F.ModuleId FROM $DataPublic.modulenexus M LEFT JOIN $DataIn.upopedom U ON U.ModuleId =M.dModuleId LEFT JOIN $DataPublic.funmodule F ON F.ModuleId=U.ModuleId WHERE U.UserId =$Login_Id and M.ModuleId=$Mid and U.Action>0 and F.Estate=1 and (F.cSign=0 or F.cSign='$Login_cSign') order by M.OrderId", $link_id);
//            echo "SELECT F.ModuleName,F.ModuleId FROM $DataPublic.modulenexus M LEFT JOIN $DataIn.upopedom U ON U.ModuleId =M.dModuleId LEFT JOIN $DataPublic.funmodule F ON F.ModuleId=U.ModuleId WHERE U.UserId =$Login_Id and M.ModuleId=$Mid and U.Action>0 and F.Estate=1 and (F.cSign=0 or F.cSign='$Login_cSign') order by M.OrderId";die;
            $Count1 = @mysql_num_rows($result);

            if ($Mid == 1006) {
                $Count1 = 5;
            }

            if ($myrow = mysql_fetch_array($result)) {
                $i = 1;
                $Style = "";
                $SignStr = "";

                do {
                    //子功能
                    $ModuleName = $myrow["ModuleName"];
                    $Rid = $myrow["ModuleId"];
                    if ($Rid == 9126 || $Rid == 9117 || $Rid == 9112 || $Rid == 9109 || $Rid == 9101) {

                        $Rid = substr($Rid, 1, 3);
                        $subResult = mysql_query("SELECT F.ModuleId,F.ModuleName,F.Parameter,F.OrderId,P.Action
					FROM $DataPublic.sc4_modulenexus M
					LEFT JOIN $DataIn.sc4_upopedom P ON M.dModuleId=P.ModuleId 
					LEFT JOIN $DataPublic.sc4_funmodule F ON F.ModuleId=P.ModuleId 
					LEFT JOIN $DataIn.usertable U ON U.Id=P.UserId
					WHERE M.ModuleId='$Rid' AND U.Number='$Login_P_Number' 
					AND P.Action>0 AND F.Estate=1 GROUP BY F.ModuleId ORDER BY M.OrderId", $link_id);
                    }
                    else {
                        $subResult = mysql_query("SELECT F.ModuleName,F.ModuleId,F.Parameter,F.KeyWebPage FROM $DataPublic.modulenexus M LEFT JOIN $DataIn.upopedom U ON U.ModuleId =M.dModuleId LEFT JOIN $DataPublic.funmodule F ON F.ModuleId=U.ModuleId WHERE U.UserId =$Login_Id and M.ModuleId=$Rid and U.Action>0 and F.Estate=1  and (F.cSign=0 or F.cSign='$Login_cSign') order by M.OrderId", $link_id);
                    }


                    $subCount = @mysql_num_rows($subResult);
                    if ($Count1 > 1) {
                        $Style = "style='CURSOR: pointer'";
                        $Mouses = "onmouseover='this.className=\"menu_title2\";' onmouseout='this.className=\"menu_title\";' onclick=menuSign($i,this,$Count1);";
                        if ($i == 1) {
                            $backimg = 'jiantouxia';
                            $SignStr = "";
                        }
                        else {
                            $backimg = 'jiantouyou';
                            $SignStr = "style='DISPLAY: none;'";
                        }
                    }
                    else {
                        $backimg = '';
                        $Mouses = "";
                    }

                    if ($i == 1) echo "<div style='height=20px'>　</div>";

                    echo "<TR $Style $Mouses><td id='Menu$i' class='menu_title' style='height:30px;line-height: 30px;'><span><i class=\"iconfont icon-$backimg\"></i>&nbsp;&nbsp;$ModuleName</span>
</td></TR>";
                    echo "<TR $SignStr id=subMenu$i><TD>";
                    echo "<TABLE style='POSITION: relative;margin:0;width:200px' cellSpacing=0 cellPadding=0><TBODY>";
                    if ($subRow = mysql_fetch_array($subResult)) {
                        do {
                            $SubMenu = $subRow["ModuleName"];
                            $SubModuleIdTemp = $subRow["ModuleId"];
                            $KeyWebPage = $subRow["KeyWebPage"];
                            $Parameter = $subRow["Parameter"];
                            include "mcright_shcout.php";
                            $SubModuleId = anmaIn($SubModuleIdTemp, $SinkOrder, $motherSTR);//加密
                            if (stristr($SubMenu, "$ModuleName-")) {
                                $SubMenu = str_replace("$ModuleName-", "", $SubMenu);
                            }
                            if ($Rid == 126 || $Rid == 117 || $Rid == 112 || $Rid == 109 || $Rid == 101) {
                                echo "<TR><TD style='text-indent:16px;height:30px;line-height: 30px;border-left:2px solid #4a5864'><A CLASS='link-font-style' style='width:200px;display:inline-block;' onfocus=this.blur(); href='../cjgl/mainFrame.php?Id=$SubModuleId' target='mainFrame' onclick='ClickTotal(this,1,$SubModuleIdTemp)'>$SubMenu</A></TD></TR>";
                            }elseif ($KeyWebPage == "link") {
                              echo "<TR><TD style='text-indent:16px;height:30px;line-height: 30px;border-left:2px solid #4a5864'><A CLASS='link-font-style' style='width:200px;display:inline-block;' onfocus=this.blur(); href='$Parameter' target='mainFrame' onclick='ClickTotal(this,1,$SubModuleIdTemp)'>$SubMenu</A></TD></TR>";
                            }
                            else {
                                echo "<TR><TD style='text-indent:16px;height:30px;line-height: 30px;border-left:2px solid #4a5864'><A CLASS='link-font-style' style='width:200px;display:inline-block;' onfocus=this.blur(); href='mainFrame.php?Id=$SubModuleId' target='mainFrame' onclick='ClickTotal(this,1,$SubModuleIdTemp)'>$SubMenu</A></TD></TR>";
                            }
                        } while ($subRow = mysql_fetch_array($subResult));
                    }
                    echo "</TBODY></TABLE>";
                    echo "</TD></TR>";
                    $i++;
                    if ($Rid == 1007) {
                        // include "mcright_cgsheet.php";
                    }
                } while ($myrow = mysql_fetch_array($result));
                echo "</TBODY></TABLE>";
            } // 结束有权限模块
        }
        else { // 初始化，显示分机
//            $extFile = "exttel.inc";
//            if (file_exists($extFile)) {
//                $ftime = date("Y-m-d", filemtime($extFile));
//                if ($ftime < date("Y-m-d")) {
//                    include "mcright_exttel.php";
//                }
//                else {
//                    include $extFile;
//                }
//            }
//            else {
//                include "mcright_exttel.php";
//            }
        }

        // 获取列表参数
        function _get($str)
        {
            $val = !empty($_GET[$str]) ? $_GET[$str] : null;

            return $val;
        }

        ?>

      <!-- 测试代码 -->
      <!--<table width="142" align="center" cellspacing="0" cellpadding="0">-->
      <!--    <tbody>-->
      <!--        <tr>-->
      <!--            <td valign="bottom" height="42">-->
      <!--                <img src="../images/title.gif" width="142" height="38">-->
      <!--            </td>-->
      <!--        </tr>-->
      <!--        <tr style="CURSOR: pointer" onmouseover="this.className=&quot;menu_title2&quot;;" onmouseout="this.className=&quot;menu_title&quot;;" onclick="menuSign(1,this,7);" class="menu_title" bgcolor="#cccccc">-->
      <!--            <td id="Menu1" class="menu_title" height="23" background="../images/title_bg_hide.gif">-->
      <!--                <span>设计</span>-->
      <!--            </td>-->
      <!--        </tr>-->
      <!--        <tr id="subMenu1">-->
      <!--            <td bgcolor="#cccccc">-->
      <!--                <table style="POSITION: relative;" width="130" align="center" cellspacing="0" cellpadding="0">-->
      <!--                    <tbody>-->
      <!--                        <tr>-->
      <!--                            <td height="20">-->
      <!--                                <a onfocus="this.blur();" href="mainFrame.php?Id=1|1"-->
      <!--                                        target="mainFrame" onclick="ClickTotal(this,1,1)">项目列表</a>-->
      <!--                            </td>-->
      <!--                        </tr>-->
      <!--                        <tr>-->
      <!--                            <td height="20">-->
      <!--                                <a onfocus="this.blur();" href="mainFrame.php?Id=1|2"-->
      <!--                                        target="mainFrame" onclick="ClickTotal(this,1,2)">图纸</a>-->
      <!--                            </td>-->
      <!--                        </tr>-->
      <!--                        <tr>-->
      <!--                            <td height="20">-->
      <!--                                <a onfocus="this.blur();" href="mainFrame.php?Id=1|3"-->
      <!--                                        target="mainFrame" onclick="ClickTotal(this,1,3)">钢筋</a>-->
      <!--                            </td>-->
      <!--                        </tr>-->
      <!--                        <tr>-->
      <!--                            <td height="20">-->
      <!--                                <a onfocus="this.blur();" href="mainFrame.php?Id=1|4"-->
      <!--                                        target="mainFrame" onclick="ClickTotal(this,1,4)">预埋件</a>-->
      <!--                            </td>-->
      <!--                        </tr>-->
      <!--                        <tr>-->
      <!--                            <td height="20">-->
      <!--                                <a onfocus="this.blur();" href="mainFrame.php?Id=1|5"-->
      <!--                                        target="mainFrame" onclick="ClickTotal(this,1,5)">数据导入</a>-->
      <!--                            </td>-->
      <!--                        </tr>-->
      <!--                        <tr>-->
      <!--                            <td height="20">-->
      <!--                                <a onfocus="this.blur();" href="mainFrame.php?Id=1|6"-->
      <!--                                        target="mainFrame" onclick="ClickTotal(this,1,6)">数据导出</a>-->
      <!--                            </td>-->
      <!--                        </tr>-->
      <!--                        <tr>-->
      <!--                            <td height="20">-->
      <!--                                <a onfocus="this.blur();" href="mainFrame.php?Id=1|7"-->
      <!--                                        target="mainFrame" onclick="ClickTotal(this,1,7)">校核</a>-->
      <!--                            </td>-->
      <!--                        </tr>-->
      <!--                        <tr>-->
      <!--                            <td height="20">-->
      <!--                                <a onfocus="this.blur();" href="mainFrame.php?Id=1|8"-->
      <!--                                        target="mainFrame" onclick="ClickTotal(this,1,8)">审核</a>-->
      <!--                            </td>-->
      <!--                        </tr>-->
      <!--                    </tbody>-->
      <!--                </table>-->
      <!--            </td>-->
      <!--        </tr>-->
      <!--    </tbody>-->
      <!--</table>-->

    </td>
  </tr>
</table>
<!--底部信息-->
<!--<div style="position:absolute;bottom;color:#6F7D88;width:240px;text-align: center">-->
<!--  <div style="height:30px;line-height: 30px;color:#C9D7E2"><em class="iconfont icon-ren"></em><span id="show_news"></span>　<em class="iconfont icon-liaotianjilu"></em><span id="show_sms"></span>　</div>-->
<!--  <div style="height:30px;line-height: 30px;">-->
<!--    <a href='../desk/calendar.php' target='_blank' style="font:12px">行事历</a>&nbsp;|&nbsp;-->
<!--    <a href="../public/oprationlog_read.php?From=mcmain" target="mainFrame" style="font:12px">操作日志</a>-->
<!--  </div>-->
<!--  <div style="border-top:1px solid #6F7D88;height:30px;line-height: 30px;">　--><?php //echo $Login_IP ?><!--　上次离线：--><?php //echo $Login_LastTime ?><!--</div>-->
<!--</div>-->
</body>
</html>
<script src="../model/jquery_corners/jquery-1.8.3.js"></script>
<script>
// 禁用(20180518 by xfy)
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
$(function () {
//    getOnlone();
    // 显示移入样式
    $(".link-font-style").mouseover(function () {
        $(this).parent().addClass('showbgc')
    });
    // 去除移入样式
    $(".link-font-style").mouseout(function () {
        $(this).parent().removeClass('showbgc')
    });
    // 显示选中样式
    $(".link-font-style").click(function () {
        $('.activebgc').removeClass('activebgc');
        $(this).parent().addClass('activebgc')
    });

    $('.menu_title').click(function () {
        if ($(this).children().children('i').attr("class") == 'iconfont icon-jiantouxia') {
            $(this).children().children('i').attr("class", 'iconfont icon-jiantouyou')
        } else {
            $('i').attr('class', 'iconfont icon-jiantouyou');
            $(this).children().children('i').attr("class", 'iconfont icon-jiantouxia')
        }
    });
});
function RefreshTel() {
    var show = document.getElementById("menubg");
    show.innerHTML = "<div style='overflow-y:auto ;width:100%;height:100%;color:#f00000;'>更新数据中...</div>";
    url = "mcright_exttel.php";
    var ajax = InitAjax();
    ajax.open("GET", url, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4) {
            show.innerHTML = ajax.responseText;
        }
    };
    ajax.send(null);
}

function InitAjax() {
    var ajax = false;
    try {
        ajax = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
            ajax = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
            ajax = false;
        }
    }
    if (!ajax && typeof XMLHttpRequest != 'undefined') {
        ajax = new XMLHttpRequest();
    }
    return ajax;
}

function menuSign(Row, the, Alls) {
    //add by zx 2012-1018
    if (document.getElementById("RealCount")) {
        Alls = document.getElementById("RealCount").value;
    }

    for (var i = 1; i <= Alls; i++) {
        var e = eval("subMenu" + i);
        var f = eval("Menu" + i);
        if (i == Row) {
            e.style.display = e.style.display == "none" ? "" : "none";
            f.background = e.style.display == "none" ? "../images/title_bg_show.gif" : "images/title_bg_hide.gif";
        } else {
            e.style.display = "none";
            f.background = "../images/title_bg_show.gif";
        }
    }
}

// 点击计数
function ClickTotal(e, ComeFrom, FunctionId) {
    var url = "clicktotal.php?ComeFrom=" + ComeFrom + "&FunctionId=" + FunctionId;
    var ajax = InitAjax();
    ajax.open("GET", url, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4 && ajax.status == 200) {
            //var BackData=ajax.responseText;
            //e.title="点击数:"+BackData;
        }
    };
    //发送空
    ajax.send(null);
}

// 关闭或打开右侧菜单栏
var KeyWord = "open;"

function openOrclose() {
    if (KeyWord == "open") {
        KeyWord = "close";
        parent.frmMain.cols = '*,8';
        arrowhead.src = "../images/arrowhead1.gif";
    } else {
        KeyWord = "open";
        parent.frmMain.cols = '*,152';
        arrowhead.src = "../images/arrowhead2.gif";
    }
}

function checkIE(strMail) {
    var msnUrl = "msnim:chat?contact=" + strMail;
    if (!-[1,] && strMail != "") {
        window.open(msnUrl);
    }
}
</script>
