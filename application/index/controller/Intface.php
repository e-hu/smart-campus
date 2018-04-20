<?php

namespace app\index\controller;

use think\Db;
use controller\ApiBase;

/**
 * 网站入口控制器
 * Class Index
 * @package app\index\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/04/05 10:38
 */
class Intface extends ApiBase
{

    protected $checkAuth = false;

    public function _initialize()
    {
    }

    /**
     * pC端数据交互接口
     * @param $no
     */
    public function up_machine_info($no)
    {
        $sql = "exec up_machine_info :no";
        $result = Db::query($sql, ['no' => $no]);
        return $result[0][0]['return_msg'];
    }

    /**
     * 浙农信支付结果通知接口
     */
    public function payResult(){
        $xml = file_get_contents("php://input");
        $data = xml_to_json($xml);
        $arr = json_decode($data,true);
        if($arr['Message']['RespCode']="000000"){
           Db::table("rechargeOrderList")
                ->where(['MerSeqNo'=>$arr['Message']['MerSeqNo'],'TransAmt'=>$arr['Message']['TransAmt'],'status'=>'0'])
                ->update(['status'=>'1','TransSeqNo'=>$arr['Message']['TransSeqNo'],'ClearDate'=>$arr['Message']['ClearDate']]);
            return json(['RespCode'=>'000000']);
        }else{
            return json(['RespCode'=>'000001']);
        }
    }

}
