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


    /**
     * 更新模状态
     * @param $mouldArray
     * @param $status
     * @return bool
     */
    public function update_mould_status($mouldArray, $status)
    {
        foreach ($mouldArray as $item) {
            $Id = $item['Id'];
            $bomId = $item['bomId'];
//            $updateSql = "UPDATE inventory_trolley_mould SET Status = $status WHERE Id=$Id;";
            $updateSql = "UPDATE lining_mould SET Status = $status WHERE id=$Id;";
            if (!$this->conn->query($updateSql))
                return false;
            if ($status == 4) {
                $removeSql = "UPDATE bom_mould SET Completions=Completions-1 WHERE Id = $bomId;";//拆模后模具完成数-1
                if (!$this->conn->query($removeSql))
                    return false;
            }
        }
        return true;
    }

    /**
     * 从台车删除模具
     * @param $mouldArray 台车模具关系表ID集合
     * @return bool
     */
    public function delete_mould_trolley($mouldArray)
    {
        foreach ($mouldArray as $item) {
            $Id = $item['Id'];
            $bomId = $item["bomId"];
            $deleteSql = "DELETE FROM lining_mould WHERE `Status` = 0 AND id = $Id";
            $updateSql = "UPDATE bom_mould SET Completions=Completions-1 WHERE Id = $bomId";
            if (!($this->conn->query($updateSql) && $this->conn->query($deleteSql)))
                return false;
        }
        return true;
    }


    /**
     * 添加模具到台车
     * @param $trolleyId
     * @param $mouldArray
     * @param $openid
     * @return bool
     */
    public function add_mould_trolley($trolleyId, $mouldArray, $openid)
    {
        foreach ($mouldArray as $item) {
            $bomId = $item['bomId'];
            $insertSql = "INSERT INTO lining_mould (liningNo,bomMouldId,`Status`,Creator,Modifier) VALUES ('$trolleyId',$bomId,0,'$openid','$openid');";
            $updateSql = "UPDATE bom_mould SET Completions=Completions + 1 WHERE Id = $bomId;";
            if (!($this->conn->query($insertSql) && $this->conn->query($updateSql)))
                return false;
        }

        return true;
    }

    /**
     * 查询模具
     * @param $tradeId
     * @param $mouldCat
     * @param $mouldNo
     * @return array|null
     */
    public function get_bom($tradeId, $mouldCat, $mouldNo)
    {
        $sql = "SELECT a.Id BomId, a.MouldCat, a.MouldNo, a.ProQty, a.Completions, c.Forshort
                    FROM bom_mould a
                    LEFT JOIN trade_info b ON a.TradeId = b.TradeId
                    LEFT JOIN trade_object c ON c.id = a.TradeId
                    INNER JOIN bom_object d ON d.TradeId = a.TradeId
                    WHERE d.Estate=2 AND a.Completions<a.ProQty ";
        if ($tradeId > 0) {
            $sql .= " AND a.TradeId=$tradeId";
        }
        if ($mouldCat != "") {
            $sql .= " AND a.MouldCat='$mouldCat'";
        }
        if ($mouldNo != "") {
            $sql .= " AND a.MouldNo like '%$mouldNo%'";
        }
        return $this->format($sql);
    }

    /**
     * 获取所有构件的项目信息
     */
    public function get_company_forshort()
    {
        $sql = "SELECT I.TradeId TradeId, I.TradeNo TradeNo,O.Forshort Forshort FROM trade_info I INNER JOIN trade_object O ON O.Id = I.TradeId";
        return $this->format($sql);
    }

    /**
     * 获取模板类型
     * @param $tradeId
     * @return array|null
     */
    public function get_module_cat($tradeId)
    {
        $sql = "select distinct MouldCat from bom_mould where TradeId=$tradeId";
        return $this->format($sql);
    }

    /**
     * 获取台车下模信息
     * @param $trolleyId
     * @return array|null
     */
    public function get_bom_by_trolley($trolleyNo, $openId)
    {

        //判断当前台车是否存在，不存在则创建
        $checkSql = "SELECT * FROM lining_mould WHERE liningNo='$trolleyNo';";
        if (is_null($this->format($checkSql))) {
            $insertSql = "INSERT INTO lining_mould (liningNo,Creator,Modifier) VALUES ('$trolleyNo','$openId','$openId')";
            $this->format($insertSql);
            return null;
        } else {
            $sql = "SELECT i.Id, a.Id BomId, a.MouldCat, a.MouldNo,i.`Status`, c.Forshort, i.liningNo TrolleyId
                    FROM lining_mould i
                    LEFT JOIN bom_mould a ON i.bomMouldId = a.Id
                    LEFT JOIN trade_info b ON a.TradeId = b.TradeId
                    LEFT JOIN trade_object c ON c.id = a.TradeId
                    INNER JOIN bom_object d ON d.TradeId = a.TradeId
                    WHERE 1 AND i.liningNo = '$trolleyNo' AND i.`Status` !=4 AND d.Estate=2";
            return $this->format($sql);
        }
    }

#region 权限校验
    public function apiAuth($openId, $tag)
    {
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
            //盘点 & 布模拆模
            case 1:
                return is_null($result) ? false : true;
                break;
            //出货
            case 2:
                if (!is_null($result) && ($result[0]["Branch"] == "IT中心" || $result[0]["Branch"] == "资材部")) {
                    return true;
                } else {
                    return false;
                }
        }
    }

    /**
     * 获取扫码用户昵称
     */
    public function get_user_name($openId)
    {
        $sql = "SELECT SM.name uName FROM usertable U INNER JOIN staffmain SM ON SM.Number = U.Number WHERE U.openid='$openId';";
        return $this->format($sql);
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