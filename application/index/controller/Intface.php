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
        $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
        $txt = '01212222';
        fwrite($myfile, $txt);
        $txt = $_POST;
        fwrite($myfile, $txt);
        $txt = $_GET;
        fwrite($myfile, $txt);
        fclose($myfile);
       if($_POST['Transld']="PSNR"){
           if($_POST['RespCode'] = '000000'){
               Db::table("rechargeOrder_list")->where(['MerSeqNo'=>$_POST['MerSeqNo'],'TransAmt'=>$_POST['TransAmt'],'status'=>'0'])->update(['status'=>'1']);
           }
           return json(['RespCode'=>'000000']);
       }else{
           return json(['RespCode'=>'000001']);
       }
    }

    /**
     *浙农信支付查询
     */

    private function paySearch($MerSeqNo){
        $order_info = Db::table("rechargeOrder_list")->where(['MerSeqNo'=>$MerSeqNo])->find();
        $data = [];
        $data['TransId'] = 'IQSR';
        $data['MerchantId'] = $order_info['MerchantId'];
        $data['SubMerchantId'] = $order_info['MerchantId'];
        $data['MerSeqNo'] = $order_info['MerSeqNo'];
        $data['MerTransDate'] = $order_info['MerDateTime'];
        $html = post_curls('http://158.222.29.118:8080/merchant/*.do?TransName=prePWDReq',$data);
        preg_match_all('|value="(.*)"|isU',$html,$arr); //匹配到数组$arr中；
        $data2 = [];
        $data2['Plain'] = $arr[1][2];
        $data2['Signature'] = $arr[1][3];
        $data2['signedData'] = [];
        $html = post_curls('http://158.222.29.118:8080/paygate/main',$data2);
        $data = xml_to_json($html);
        print_r($data);exit;
    }

}
