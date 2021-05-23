<?php
/*电信---yang 20120801
代码-EWEN
*/
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$outArray = explode("|", $_GET["Id"]);
$RuleStr = $outArray[0];
$EncryptStr = $outArray[1];
$ModuleId = anmaOut($RuleStr, $EncryptStr);

if ($ModuleId == 0) {
    if (!isset($_SESSION["Keys"])) {
        $_SESSION["Keys"] = $Keys;
    }
    if (!isset($_SESSION["S_From"])) {
        $_SESSION["S_From"];

    }
    //清除页面搜索条件
    if (isset($_SESSION["SearchRows"])) {
        unset($_SESSION["SearchRows"]);
    }
    $S_From = 0;
    if ($BuyerId == 0) $Parameter = "public/Cg_cgdmain_client.php?Weeks=$Weeks";//客供配件
    else $Parameter = "public/Cg_cgdmain_tj.php?BuyerId=$BuyerId&Weeks=$Weeks";
    echo"<meta http-equiv=\"Refresh\" content='0;url=../".$Parameter."'>";
} else {

    $result = mysql_query("SELECT F.Parameter,U.Action FROM $DataIn.funmodule F,$DataIn.upopedom U WHERE U.UserId='$Login_Id' and U.ModuleId='$ModuleId' and F.ModuleId=U.ModuleId LIMIT 1", $link_id);
    if ($myrow = mysql_fetch_array($result)) {
        $Parameter = $myrow["Parameter"];
        $Keys = $myrow["Action"];

        if (!isset($_SESSION["Keys"])) {
            $_SESSION["Keys"] = $Keys;
        }
        if (!isset($_SESSION["S_From"])) {
            $_SESSION["S_From"];

        }
        //清除页面搜索条件
        if (isset($_SESSION["SearchRows"])) {
            unset($_SESSION["SearchRows"]);
        }

        $S_From = 0;
        echo "<meta http-equiv=\"Refresh\" content='0;url=../" . $Parameter . "'>";

        //echo $Parameter;
    } else {
        echo "此页面不存在";

        //测试代码   上面echo代码 为正式代码
        //if ($ModuleId == 1) {
        //    echo "<meta http-equiv=\"Refresh\" content='0;url=../design/trade_object_read.php'>";
        //} else if ($ModuleId == 2) {
        //    echo "<meta http-equiv=\"Refresh\" content='0;url=../design/trade_drawing_read.php'>";
        //} else if ($ModuleId == 3) {
        //    echo "<meta http-equiv=\"Refresh\" content='0;url=../design/trade_steel_read.php'>";
        //} else if ($ModuleId == 4) {
        //    echo "<meta http-equiv=\"Refresh\" content='0;url=../design/trade_embedded_read.php'>";
        //} else if ($ModuleId == 5) {
        //    echo "<meta http-equiv=\"Refresh\" content='0;url=../design/trade_cmpt_add.php'>";
        //} else if ($ModuleId == 6) {
        //    echo "<meta http-equiv=\"Refresh\" content='0;url=../design/trade_cmpt_export.php'>";
        //} else if ($ModuleId == 7) {
        //    echo "<meta http-equiv=\"Refresh\" content='0;url=../design/trade_proofreade.php'>";
        //} else if ($ModuleId == 8) {
        //    echo "<meta http-equiv=\"Refresh\" content='0;url=../design/trade_check.php'>";
        //}

    }
}
?>