<?php
/**
 * Created by PhpStorm.
 * User: Kyle
 */

include "./Config/DbConnect.php";
class CommonSql
{
    public $db;

    function __construct()
    {
        $this->db = new DbConnect();
    }

    /**
     * 获取所有构件的项目信息
     */
    public function get_company_forshort()
    {
        $sql = "SELECT I.TradeId TradeId,O.CompanyId, I.TradeNo TradeNo,O.Forshort Forshort FROM trade_info I INNER JOIN trade_object O ON O.Id = I.TradeId";
        return $this->db->format($sql);
    }

    /**
     * 获取生产线
     * @return array|null
     */
    public function get_work_shop()
    {

        $sql = "SELECT Id,`Name` FROM workshopdata ;";
        return $this->db->format($sql);
    }

    /**
     * 获取项目楼栋数
     * @param $tradeId      公司ID
     * @return mixed|null   楼栋号
     */
    public function get_company_building($tradeId)
    {
        $sql = "SELECT BuildingNo FROM trade_drawing WHERE TradeId = $tradeId  GROUP BY BuildingNo";
        return $this->db->format($sql);
    }

    /**
     * 获取楼栋层数
     * @param $tradeId      项目ID
     * @param $buildingNo   楼栋ID
     * @return mixed|null   层数信息
     */
    public function get_building_floor($tradeId, $buildingNo)
    {
        $sql = "SELECT FloorNo FROM trade_drawing WHERE TradeId = $tradeId AND buildingNo = '$buildingNo' GROUP BY FloorNo";
        return $this->db->format($sql);
    }

    /**
     * 获取构件类型
     * @param $tradeId
     * @param $buildingNo
     * @param $floorNo
     * @return array|null
     */
    public function get_cmpttype($tradeId, $buildingNo, $floorNo)
    {
        $sql = "SELECT
                    P.TypeId,
                    D.CmptType 
                FROM
                    trade_drawing D
                    INNER JOIN producttype P ON P.TypeName = D.CmptType 
                WHERE
                    D.TradeId = $tradeId 
                    AND D.buildingNo = '$buildingNo' 
                    AND D.FloorNo = '$floorNo' 
                GROUP BY
                    D.CmptType";
        return $this->db->format($sql);
    }

    /**
     * 获取扫码用户昵称
     */
    public function get_operator_name($openId)
    {
        $sql = "SELECT SM.name uName FROM usertable U INNER JOIN staffmain SM ON SM.Number = U.Number WHERE U.openid='$openId';";
        return $this->db->format($sql);
    }

    /**
     * 获取台车信息
     */
    public function get_trolley_info(){
        $sql = "SELECT
            DISTINCT S.RealLining as trolleyNo
        FROM
            yw1_scsheet SC
            INNER JOIN yw1_ordersheet S ON S.POrderId = SC.POrderId
            INNER JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
            LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
            INNER JOIN productdata P ON P.ProductId = S.ProductId
            INNER JOIN productunit U ON U.Id = P.Unit
            INNER JOIN cg1_stocksheet G ON G.StockId = SC.StockId
            INNER JOIN stuffdata D ON D.StuffId = G.StuffId
        WHERE
            1 
            AND SC.ActionId = '110' 
            AND SC.Estate = '1'
            and SC.WorkShopId in(101,104)
						GROUP BY S.RealLining
            ";
        return $this->db->format($sql);
    }


    public function init_kiln(){
        $arr = array("A","B","C","D","E");
        $workShopArr = array(101,104);
        $typeArr = array(1,2);
        for($n=0;$n<count($workShopArr);$n++){
            for($m=0;$m<count($typeArr);$m++){
                for($i = 1;$i<11;$i++){
                    for($j=0;$j<count($arr);$j++){
                        $sql = "INSERT INTO maintan_kiln_bits VALUES(NULL,'','','$arr[$j]','$i',$workShopArr[$n],$typeArr[$m],1)";
                        $this->db->conn->query($sql);
                    }
                }
            }
        }
    }
}