<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2019/3/17
 * Time: 16:00
 */
include "./Config/DbConnect.php";

class WCSThirdSql
{
    public $db;

    function __construct()
    {
        $this->db = new DbConnect();
    }


    /**
     * wcs操作反馈成功执行该方法
     * @param $kilnId
     * @param $taskType  1 入窑任务   2出窑任务
     */
    function operateConfirm($kilnId,$taskType,$finishTime){
        //获取窑位下的订单id
        $sql = "select MaintanOrderId from maintan_kiln_bits where Id = $kilnId";
        $sqlRes = $this->db->format($sql);
        if($sqlRes == null){
            throw new Exception("未找到窑位".$kilnId);
        }
        $MaintanOrderId = $sqlRes[0]['MaintanOrderId'];
        if($MaintanOrderId == 0){
            throw new Exception("在该窑位处于空闲状态！");
        }
        if($taskType == 1){
            //入库反馈操作成功
            //更新订单状态
            $updateSql = "UPDATE maintan_order set `Status` = 2,MaintanTime='$finishTime' WHERE ID= $MaintanOrderId";
            $updateSqlRes = $this->db->conn->query($updateSql);
            if(!$updateSqlRes || mysqli_affected_rows($this->db->conn) == 0){
                throw new Exception("订单id为".$MaintanOrderId."入窑状态更新失败,");
            }
        }else if($taskType == 2){
            //出入反馈操作成功
            //更新订单状态
            $updateSql = "UPDATE maintan_order set `Status` = 4,OutMaintanTime='$finishTime' WHERE ID= $MaintanOrderId";
            $updateSqlRes = $this->db->conn->query($updateSql);
            if(!$updateSqlRes){
                throw new Exception("订单id为".$MaintanOrderId."状态更新失败,");
            }
            //将窑位绑定订单解绑
            $updateSql = "UPDATE maintan_kiln_bits set MaintanOrderId = 0 WHERE ID = $kilnId";
            $updateSqlRes = $this->db->conn->query($updateSql);
            if(!$updateSqlRes || mysqli_affected_rows($this->db->conn) == 0){
                throw new Exception("订单id为".$MaintanOrderId."出窑状态更新失败,");
            }

            //更新yw1_scsheet  estate状态置位1
            $updateSCsheetSql  = "UPDATE yw1_scsheet set Estate = 1 WHERE 1 AND ActionId = 101 AND POrderId in (
              SELECT POrderID FROM maintan_order_product_mapping WHERE MainOrderId = $MaintanOrderId 
            )";
            $updateSCsheetSqlRes = $this->db->conn->query($updateSCsheetSql);
            if(!$updateSCsheetSqlRes || mysqli_affected_rows($this->db->conn) == 0){
                throw new Exception("数据更新失败");
            }

            //更新yw1_scsheet  estate状态置位0   养护完成
            $updateSCsheetSql  = "UPDATE yw1_scsheet set Estate = 0 WHERE 1 AND ActionId = 110 AND POrderId in (
              SELECT POrderID FROM maintan_order_product_mapping WHERE MainOrderId = $MaintanOrderId 
            )";
            $updateSCsheetSqlRes = $this->db->conn->query($updateSCsheetSql);
            if(!$updateSCsheetSqlRes || mysqli_affected_rows($this->db->conn) == 0){
                throw new Exception("数据更新失败");
            }
        }
    }


    /**
     * 更新温湿度
     * @param $kilnId
     * @param $temperatureValue
     * @param $humidityValue
     * @return bool
     * @throws Exception
     */
    function updateKilnBitParams($temperatureValue,$humidityValue,$KType,$WorkshopdataId){
        $KRowNo = substr($KType,0,1);
        $updateSql = "UPDATE maintan_kiln_bits
        SET temperatureValue = '$temperatureValue',
         humidityValue = '$humidityValue'
        WHERE
            WorkshopdataId = $WorkshopdataId
        AND KType = $KType
        AND KRowNo LIKE '$KRowNo' ";
        $updateSqlRes = $this->db->conn->query($updateSql);
        if(!$updateSqlRes || mysqli_affected_rows($this->db->conn) == 0){
            throw new Exception("温湿度更新失败");
        }
        return true;
    }

    /**
     * 获取待入窑确认的数据
     * @return array|null
     */
    function getKilnsInfo($WorkshopId,$TralleyNo){
        $sql = "SELECT O.KilnId,O.`Status`,K.KType,CONCAT(K.LineNo,K.KRowNo) LineNo FROM maintan_order O LEFT JOIN maintan_kiln_bits K
ON O.KilnId = K.ID WHERE O.`Status` = 1 AND K.WorkshopdataId ='$WorkshopId'  AND O.TrolleyNo='$TralleyNo'";
        $sqlRes = $this->db->format($sql);
        return $sqlRes;
    }

    /**
     * 获取待出窑确认的数据
     * @return array|null
     */
    function getKilnsOutInfo($WorkshopId){
        $sql = "SELECT O.KilnId,O.TrolleyNo,O.`Status`,K.KType,CONCAT(K.LineNo,K.KRowNo) LineNo FROM maintan_order O LEFT JOIN maintan_kiln_bits K
ON O.KilnId = K.ID WHERE O.`Status` = 3 AND K.WorkshopdataId ='$WorkshopId' ";
        $sqlRes = $this->db->format($sql);
        return $sqlRes;
    }

}