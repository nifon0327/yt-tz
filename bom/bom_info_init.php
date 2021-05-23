<?php
include_once "../basic/parameter.inc";
include_once "../model/modelfunction.php";
include "../basic/chksession.php";

$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
set_time_limit(0);

@$proId = addslashes($_POST['id']);

//开始一个事务
@mysql_query("BEGIN");

//清空BOM信息
$DelSql = "DELETE FROM $DataIn.bom_info WHERE tradeId = $proId";
@mysql_query($DelSql);

// 获取贸易编号
$tradeNoSql = mysql_query("SELECT TradeNo FROM $DataIn.trade_info where tradeId = $proId",$link_id);
$TradeNo=mysql_result($tradeNoSql,0,"TradeNo");

// 配件类型取得
$stufftype =array();
$result = mysql_query("select TypeId, TypeName From $DataIn.stufftype");
if($result && $myRow = mysql_fetch_array($result)){
    do {
        $stufftype[$myRow["TypeName"]] = $myRow["TypeId"];

    }while($myRow = mysql_fetch_array($result));
}

//主配件分类
$stuffmaintype =array();
$result = mysql_query("select Id, TypeName From $DataIn.stuffmaintype");
if($result && $myRow = mysql_fetch_array($result)){
    do {
        $stuffmaintype[$myRow["TypeName"]] = $myRow["Id"];

    }while($myRow = mysql_fetch_array($result));
}

//配件StuffId取得   杂费统计  钢筋下料 C35 等相关的配件
$stuffArr =array();
$stuffUnitArr =array();
$result = mysql_query("select a.StuffId, a.StuffCname,b.name as unit From $DataIn.stuffdata a 
  left join stuffunit b on a.unit = b.id  
   where a.StuffCname = '杂费统计' or a.StuffCname = '钢筋下料' or a.StuffCname = '骨架搭建' 
 or a.StuffCname = '浇捣养护'  or a.StuffCname = '脱模入库' ");
if($result && $myRow = mysql_fetch_array($result)){
    do {
        $stuffArr[$myRow["StuffCname"]] = $myRow["StuffId"];
        $stuffUnitArr[$myRow["StuffCname"]] = $myRow["unit"];
    }while($myRow = mysql_fetch_array($result));
}

//钢筋半成品
include_once "./bom_info_init_steel.php";

//预埋件
include_once "./bom_info_init_embedded.php";

//骨架半成品
include_once "./bom_info_init_frame.php";

//构件半成品
// include_once "./bom_info_init_cmpt.php";

//成品
include_once "./bom_info_init_product.php";

//模具 非bom配件生成
include_once "./bom_info_init_mould.php";

$InSql="update $DataIn.bom_object
set Operator='$Operator',
BomCreated='$DateTime', isBomInit= 1
where TradeId = $proId";

//echo $InSql;
$InRecode=@mysql_query($InSql);

mysql_query("COMMIT");
mysql_query("END");
echo json_encode(array(
        'rlt'=> true
));

class Spec {
    public $title;
    public $spec;
    public $qty;

    public $sizeArr = array();
    public $typeArr = array();

    public function __construct($spec)
    {
        $this->spec = $spec;
        $this->qty = 0;
    }

    public static function getObjBySpec($arr, $value) {
        foreach($arr as $obj) {
            if ($obj->spec == $value) {
                return $obj;
            }
        }
        return null;
    }
}

class Size {
    public $size;
    public $qty;

    public $typeArr = array();

    public function __construct($size)
    {
        $this->size = $size;
        $this->qty = 0;
    }

    public static function getObjBySize($arr, $value) {
        foreach($arr as $obj) {
            if ($obj->size == $value) {
                return $obj;
            }
        }
        return null;
    }
}


