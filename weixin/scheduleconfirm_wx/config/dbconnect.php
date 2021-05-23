<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2018/11/24
 * Time: 22:17
 */

class DbConnect
{
    public $conn;

    function __construct()
    {
        $config = parse_ini_file("config.ini");
        $this->conn = new mysqli(
            $config["resources.database.dev.hostname"],
            $config["resources.database.dev.username"],
            $config["resources.database.dev.password"],
            $config["resources.database.dev.database"],
            $config["resources.database.dev.port"]);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }


#region 进度追踪

    /**
     * 获取生产进度信息
     * @param $CompanyId
     * @param $BuildingNo
     * @return mixed
     */
    function getProductSchedule($CompanyId, $BuildingNo)
    {
        $sql = "call Schedule($CompanyId, $BuildingNo)";
        $result = $this->conn->query($sql);
        $ss = $result->fetch_all();
        return $ss;
    }

    /**
     * 获取所有构件的项目信息
     */
    public function get_company_forshort()
    {
        $sql = "SELECT I.TradeId TradeId,O.CompanyId, I.TradeNo TradeNo,O.Forshort Forshort FROM trade_info I INNER JOIN trade_object O ON O.Id = I.TradeId";
        return $this->format($sql);
    }

    /**
     *
     */
    public function getProductStateSpecify($CompanyId, $BuildingNo,$FloorNo,$TypeId,$Status)
    {
        $sql = "call ScheduleDetails($CompanyId, $BuildingNo,$FloorNo,$TypeId,$Status)";
        $result = $this->conn->query($sql);
        return $result->fetch_all();
    }
    /**
     * 获取项目楼栋数
     * @param $tradeId      公司ID
     * @return mixed|null   楼栋号
     */
    public function get_company_building($companyId)
    {
        $sql = "SELECT DISTINCT BuildingNo FROM productdata WHERE CompanyId =$companyId ORDER BY BuildingNo";
        return $this->format($sql);
    }



#endregion
#region 权限校验
    public function apiAuth($openId,$tag){
        $sql = "SELECT
                A.uName,
                A.Number,
                A.openid,
                B.NAME,
                D.NAME AS Branch,
                E.NAME AS RoleName
            FROM
                UserTable A
                LEFT JOIN staffmain B ON B.Number = A.Number
                LEFT JOIN companys_group C ON C.cSign = B.cSign
                LEFT JOIN branchdata D ON D.Id = B.BranchId
                LEFT JOIN ac_roles E ON E.id = A.roleId 
            WHERE
                1 
                AND A.uType = '1'
            AND A.openid='$openId' LIMIT 1";

        $result = $this->format($sql);

        switch ($tag) {
            //盘点
            case 1:return is_null($result)?false:true;
                break;
            //出货
            case 2:

                if(!is_null($result)&& ($result[0]["Branch"]=="IT中心" || $result[0]["Branch"]=="资材部")){
                    return true;
                }else{
                    return false;
                }
        }
    }

#endregion




//region 通用方法
    public function format($sql)
    {
        $result_query = $this->conn->query($sql);
        $result = array();
        if ($result_query->num_rows > 0) {
            while ($row = $result_query->fetch_assoc()) {
                $result[] = $row;
            }
            return $result;
        } else {
            return null;
        }

    }
//endregion
}
