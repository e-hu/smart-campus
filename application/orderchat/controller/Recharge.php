<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\LogService;
use service\DataService;
use think\Db;
use PHPExcel_IOFactory;
use PHPExcel;

/**
 * 系统用户管理控制器
 * Class User
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class Recharge extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'Employee_list';

    /**
     * 可充值人员列表
     */
    public function index()
    {

        // 设置页面标题
        $this->title = '账户充值';
        // 获取到所有GET参数
        $get = $this->request->get();

        // 实例Query对象
        $where = [];
        $where['a.company_id'] = session('user.company_id');
        $where['a.Emp_Status'] = '1';
        $db = Db::name($this->table)
            ->alias('a')
            ->join('dept_info b', 'a.Dept_Id=b.dept_no and a.company_id = b.company_id', 'left')
            ->join('emp_account_remain c', 'a.Emp_Id=c.emp_id and a.company_id = c.company_id', 'left')
            ->join('cookbook_meal_type m', 'c.account_typeid = m.meal_id and a.company_id=m.company_id', 'left')
            ->field('a.Emp_Name,a.Emp_Id,a.Emp_MircoMsg_Uid,b.dept_name,a.Ic_Card,c.account_remain_money,c.account_remain,c.account_freeze,c.account_freeze_money,c.account_typeflag,m.meal_name')
            ->where($where);


        // 应用搜索条件

        if (isset($get['Emp_Name']) && $get['Emp_Name'] !== '') {
            $db->where('Emp_Name', 'like', "%{$get['Emp_Name']}%");
        }
        if (isset($get['Ic_Card']) && $get['Ic_Card'] !== '') {
            $db->where('Ic_Card', 'like', "%{$get['Ic_Card']}%");
        }
        if (isset($get['Emp_MircoMsg_Uid']) && $get['Emp_MircoMsg_Uid'] !== '') {
            $db->where('Emp_MircoMsg_Uid', 'like', "%{$get['Emp_MircoMsg_Uid']}%");
        }
        if (isset($get['tag']) && $get['tag'] !== '') {
            $db->where('dept_no', $get['tag']);
        }

        // 实例化并显示
        return parent::_list($db);
    }

    /**
     * 列表数据处理
     * @param type $list
     */
    protected function _data_filter(&$list)
    {
        $tags = Db::name('dept_info')->where('company_id', session('user.company_id'))->column('dept_no,dept_name');
        $this->assign('tags', $tags);
    }

    /**
     * 充值记录
     */
    public function record()
    {
        // 设置页面标题
        $this->title = '充值记录';
        // 获取到所有GET参数
        $get = $this->request->get();

        $where = [];
        $where['a.company_id'] = session('user.company_id');
        $where['c.id'] = session('user.id');
        $where['a.upload_flag'] = '0';
        $db = Db::name('emp_account_get_money_detail')
            ->alias('a')
            ->join('Employee_list b', 'a.emp_id=b.Emp_Id and a.company_id=b.company_id', 'left')
            ->join('cookbook_meal_type m', 'a.account_typeid = m.meal_id and a.company_id=m.company_id', 'left')
            ->join('system_user c', 'a.case_operator=c.id and a.company_id=b.company_id', 'left')
            ->field('a.*,Emp_Name,c.username as operator_name,meal_name')
            ->where($where);

        // 应用搜索条件

        if (isset($get['Emp_Name']) && $get['Emp_Name'] !== '') {
            $db->where('Emp_Name', 'like', "%{$get['Emp_Name']}%");
        }
        if (isset($get['Ic_Card']) && $get['Ic_Card'] !== '') {
            $db->where('Ic_Card', 'like', "%{$get['Ic_Card']}%");
        }
        if (isset($get['tag']) && $get['tag'] !== '') {
            //$db->where("concat(',',tagid_list,',') like :tag", ['tag' => "%,{$get['tag']},%"]);   //mysql存在contcat内置函数
            $db->where("',' +''+dept_no+''+',' like :tag", ['tag' => "%,{$get['tag']},%"]);
        }

        // 实例化并显示
        return parent::_list($db);
    }

    /**
     * 充值
     */
    public function charge()
    {
        // 实例Query对象
        $get = $this->request->get();
        $where = [];
        $where['a.company_id'] = session('user.company_id');
        $where['a.Emp_Status'] = 1;
        $where['a.Emp_Id'] = $get['Emp_Id'];

        $formdata = Db::name($this->table)
            ->alias('a')
            ->join('dept_info b', 'a.Dept_Id=b.dept_no', 'left')
            ->join('emp_account_remain c', 'a.Emp_Id=c.emp_id', 'left')
            ->field('a.Emp_Name,a.Emp_Id,b.dept_name,a.Ic_Card,c.account_remain_money,c.account_freeze_money')
            ->where($where)
            ->find();

        // 非POST请求, 获取数据并显示表单页面
        if (!$this->request->isPost()) {
            $this->title = '账户充值';
            $this->assign('title', $this->title);
            $tags = Db::name('cookbook_meal_type')->where('company_id', session('user.company_id'))->select();
            $this->assign('tags', $tags);
            return $this->fetch('form', ['vo' => $formdata]);
        }
        // POST请求, 存储相关数据表1、emp_account_remain;2、emp_account_get_money_detail
        $post = $this->request->post();//form提交的数据


        $detaildata = [];//明细表数据插入
        if (!isset($post['account_from'])) {
            $this->error("请选择充值类型！", '');
        }

        $sqlstr = "exec [up_get_max_id] ?,?,?,?";
        $id_data = Db::query($sqlstr, ['', 'emp_account_get_money_detail', 'ECGMD', 0]);
        $detaildata['id'] = $id_data[0][0]['id'];
        $detaildata['company_id'] = session('user.company_id');
        $detaildata['emp_id'] = $formdata['Emp_Id'];
        $detaildata['account_typeid'] = $post['account_from'];
        if ($post['account_from'] == '1') {
            $detaildata['get_account_degree'] = '';
        } else {
            $detaildata['get_account_degree'] = $post['account_remain'];
        }
        $detaildata['get_account_money'] = $post['get_account_money'];
        $detaildata['case_date'] = ['exp', 'GETDATE()'];
        $detaildata['case_operator'] = session('user.id');
        $detaildata['get_account_from'] = '';

        LogService::write('订餐管理', '执行充值操作');

        $result = Db::name('emp_account_get_money_detail')->insert($detaildata);

        $this->success("充值成功！", '');

    }


    /**
     * 上报记录
     */
    public function push()
    {
        LogService::write('订餐管理', '执行上报记录操作');
        $data = [];
        $sqlstr = "exec [up_get_max_id] ?,?,?,?";
        $id = Db::query($sqlstr, ['', 'account', 'ACCOUNT', 0]);
        $data['upload_no'] = $id[0][0]['id'];
        $data['upload_flag'] = '1';
        $data['upload_datetime'] = date('Y-m-d H:i:s', time());
        Db::name('emp_account_get_money_detail')->where(['upload_flag' => '0', 'case_operator' => session('user.id')])->update($data);
        $this->success("上报成功！", '');
    }

    /**
     * 充值审核
     */
    public function aduit()
    {
        // 设置页面标题
        $this->title = '充值审核';
        // 获取到所有GET参数
        $get = $this->request->get();

        $where = [];
        $where['a.company_id'] = session('user.company_id');
        $where['c.id'] = session('user.id');
        $where['a.upload_flag'] = array('gt', '0');
        $db = Db::name('emp_account_get_money_detail')
            ->alias('a')
            ->field('a.upload_datetime,a.upload_no,a.check_operator,a.upload_flag,a.check_datetime,username,sum(get_account_money) as money_count')
            ->join('system_user c', 'a.case_operator=c.id', 'left')
            ->group('a.upload_datetime,a.upload_no,a.check_operator,a.upload_flag,a.check_datetime,username')
            ->where($where);

        // 应用搜索条件

        if (isset($get['Emp_Name']) && $get['Emp_Name'] !== '') {
            $db->where('Emp_Name', 'like', "%{$get['Emp_Name']}%");
        }
        if (isset($get['Ic_Card']) && $get['Ic_Card'] !== '') {
            $db->where('Ic_Card', 'like', "%{$get['Ic_Card']}%");
        }
        if (isset($get['tag']) && $get['tag'] !== '') {
            //$db->where("concat(',',tagid_list,',') like :tag", ['tag' => "%,{$get['tag']},%"]);   //mysql存在contcat内置函数
            $db->where("',' +''+dept_no+''+',' like :tag", ['tag' => "%,{$get['tag']},%"]);
        }

        // 实例化并显示
        return parent::_list($db);
    }

    /**
     * 审核上报
     */
    public function aduit_push()
    {

        LogService::write('订餐管理', '执行审核上报操作');
        $data = [];
        $data['upload_flag'] = '2';
        $data['check_operator'] = session('user.username');
        $data['check_datetime'] = date('Y-m-d H:i:s', time());
        $result = Db::name('emp_account_get_money_detail')->where(['upload_flag' => '1', 'upload_no' => $_POST['upload_no']])->update($data);
        $this->success("审核成功！", '');
    }

    /**
     * 审核记录详情
     */
    public function view()
    {
        // 设置页面标题
        $this->title = '审核记录详情';
        // 获取到所有GET参数
        $get = $this->request->get();

        $db = Db::name('emp_account_get_money_detail')
            ->alias('a')
            ->join('Employee_list b', 'a.emp_id=b.Emp_Id and a.company_id=b.company_id', 'left')
            ->join('cookbook_meal_type m', 'a.account_typeid = m.meal_id and a.company_id=m.company_id', 'left')
            ->join('system_user c', 'a.case_operator=c.id and a.company_id=b.company_id', 'left')
            ->field('a.*,Emp_Name,c.username as operator_name,meal_name')
            ->where(['a.company_id' => session('user.company_id'), 'a.upload_no' => $get['upload_no']])
            ->order('operate_datetime desc');
        // 实例化并显示
        return parent::_list($db);
    }

    /**
     * 浙农信对账
     */
    public function payorder()
    {
        // 设置页面标题
        $this->title = '充值对账';
        // 获取到所有GET参数
        $get = $this->request->get();

        // 实例Query对象
        $where = [];
        $where['a.company_id'] = session('user.company_id');
        $where['a.status'] = '1';
        $db = Db::name('recharge_order_list')
            ->alias('a')
            ->join('Employee_list l', 'a.emp_id = l.emp_id and a.company_id = l.company_id', 'left')
            ->join('dept_info k', 'l.Dept_Id = k.dept_no and a.company_id = k.company_id', 'left')
            ->field('a.*,Emp_Name,dept_name')
            ->order('create_time desc,OrderId desc')
            ->where($where);
        // 应用搜索条件
        if (isset($get['status']) && $get['status'] !== '') {
            $db->where('a.status', $get['status']);
        }
        if (isset($get['is_recon']) && $get['is_recon'] !== '') {
            $db->where('a.is_recon', $get['is_recon']);
        }

        if (isset($get['create_time']) && $get['create_time'] !== '') {
            list($start, $end) = explode('-', str_replace(' ', '', $get['create_time']));
            $db->whereBetween('a.create_time', ["{$start} 00:00:00", "{$end} 23:59:59"]);
        }

        if (isset($get['Emp_Name']) && $get['Emp_Name'] !== '') {
            $db->where('l.Emp_Name', 'like', "%{$get['Emp_Name']}%");
        }

        if (isset($get['MobileNo']) && $get['MobileNo'] !== '') {
            $db->where('a.MobileNo', 'like', "%{$get['MobileNo']}%");
        }
        // 实例化并显示
        return parent::_list($db);
    }

    /**
     * 浙农信单笔交易查询
     */
    public function search($OrderId)
    {
        $where = [];
        $where['company_id'] = session('user.company_id');
        $where['OrderId'] = $OrderId;
        $db = Db::name('recharge_order_list')
            ->where($where)->find();
        $data = paySearch('IQSR', $db['MerchantId'], $db['SubMerchantId'], $db['MerSeqNo'], $db['MerDateTime'], $db['TransAmt']);
        if (empty($data)) {
            $this->error('数据接口错误，请稍后再试');
        } else {
            if ($data['TransStatus'] == '0001') {
                Db::name('recharge_order_list')
                    ->where($where)->update(['status' => '1', 'ClearDate' => $data['ClearDate'], 'TransSeqNo' => $data['TransSeqNo']]);
            }
            $this->success($data['RespMessage']);
        }
    }

    /**
     * 浙农信对账查询
     */
    public function order()
    {
        $where = [];
        $where['company_id'] = session('user.company_id');
        $db = Db::name('recharge_order_list')
            ->where($where)->order('synch_time desc')->find();
        if (empty($db['synch_time'])) {
            $end_time = date("Y-m-d", strtotime("-1 day"));
            $start_time = date("Y-m-d", strtotime("-8 day"));
        } else {
            $end_time = date("Y-m-d", strtotime("-1 day"));
            if (strtotime($db['synch_time']) > strtotime("-8 day")) {
                $start_time = date("Y-m-d", strtotime($db['synch_time']));
            } else {
                $start_time = date("Y-m-d", strtotime("-8 day"));
            }
        }
        $pagesize = '40';
        $type = '1';
        for ($x = 1; $x <= 100; $x++) {
            $data = payOrder('QDZF', paysConf('MerSeqNo'), paysConf('SubMerSeqNo'), 'transseqnbr,mernbr,merseqnbr,cleardate,transtime,mertransdatetime,transstatus,transamt,feeamt,origmerseqnbr,origmerdate,payeracctnbr,transtypcd,currencycd,checkstatus,memo1,memo2', $start_time, $end_time, $type, $x, $pagesize);
            if(!empty($data)&&$data['RespCode']='000000'){
               if(empty($data['ClearTransList'])){
                   $this->error('同步订单接口无数据,未跑批,联系管理员!');
               }else{
                   foreach($data['ClearTransList'] as $key=>$val){
                       if($val['transstatus'] == '0' && Db::name('recharge_order_list')->where($where)->where(['MerSeqNo'=>$val['merseqnbr']])->find()){
                           Db::name('recharge_order_list')->where($where)
                               ->where(['MerchantId'=>$val['mernbr'],'MerSeqNo'=>$val['merseqnbr'],'is_recon'=>'0','TransAmt'=>$val['transamt']])
                               ->update(['is_recon'=>'1','status'=>'1','TransSeqNo'=>$val['transseqnbr'],'payeracctnbr'=>$val['payeracctnbr'],'synch_time'=>date('Y-m-d H:i:s',time())]);
                       }
                    }
                   if ($data['TransCount'] < $pagesize) {
                       break;
                   }
               }
            }else{
                $this->error('同步订单接口错误,联系管理员!');
            }
        }
        $count = Db::name('recharge_order_list')->where($where)->where(['is_recon'=>'0','status'=>'1'])->count();
        $this->success('对账成功,还有'.$count.'条记录未成功对账');
    }

    /*充值报表*/
    public function report()
    {
        // 设置页面标题
        $this->title = '充值统计报表管理';
        $company_id = session('user.company_id');
        $user_id = session('user.id');

        $reportType = Db::name('report_param_detail')->where(['company_id' => $company_id, 'report_type' => '充值统计报表'])->column('report_id,report_name');
        $this->assign('reportTypes', $reportType);

        $canteens = Db::name('canteen_base_info')->where('company_id', $company_id);
        if (session('user.create_by') != '10001') {
            $canteens->where(' exists (select 1 from t_user_manager_dept_id b where canteen_base_info.canteen_no=b.dept_id and canteen_base_info.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
        }
        $this->assign('canteens', $canteens->column('canteen_no,canteen_name'));

        $dinnerBases = Db::name('dinner_base_info')->where('company_id', $company_id)->column('dinner_flag,dinner_name');
        $this->assign('dinnerbases', $dinnerBases);

        $list = [];
        $name = [];
        $param = ['start'=>'','end'=>'','report_id'=>'','keyword'=>''];
        // 应用搜索条件
        if ($this->request->get() && isset($_GET['dinner_datetime']) && isset($_GET['report_id']) && isset($_GET['key'])) {
            if ($_GET['dinner_datetime'] !== '') {
                list($start, $end) = explode('-', str_replace(' ', '', $_GET['dinner_datetime']));
            } else {
                $this->error('请选择时间区间');
            }
            if ($_GET['report_id'] !== '') {
                $report_id = $_GET['report_id'];
            } else {
                $this->error('请选择统计方式');
            }
            if ($_GET['key'] !== '') {
                $key = $_GET['key'];
            } else {
                $key = ' ';
            }
            $list = [];
            $sqlStr = "exec [up_report] ?,?,?,?,?,?";
            $data = Db::query($sqlStr, [$company_id, $start, $end, $report_id, $user_id,$key]);
            if (!empty($data[0])) {
                $list = $data[0];
                $name = array_keys($list[0]);
                $param = ['start'=>$start,'end'=>$end,'report_id'=>$report_id,'keyword'=>$key];
            }
        }
        $this->assign('list', $list);
        $this->assign('name', $name);
        $this->assign('param', $param);
        return $this->fetch();
    }

    public function export()
    {
        if (!isset($_GET['start']) && !isset($_GET['end'])&& !isset($_GET['report_id']) && !isset($_GET['keyword'])) {
            $this->error('条件不正确');
        }
        $company_id = session('user.company_id');
        $user_id = session('user.id');
        $start = $_GET['start'];
        $end = $_GET['end'];
        $report_id = $_GET['report_id'];
        $keyword = $_GET['keyword'];
        $sqlStr = "exec [up_report] ?,?,?,?,?,?";
        $data = Db::query($sqlStr, [$company_id, $start, $end, $report_id, $user_id,$keyword]);
        $name = array_keys($data[0][0]);
        $objPHPExcel=new PHPExcel();
        $objPHPExcel->getProperties()->setCreator('http://mywx.znmya.com')
            ->setLastModifiedBy('http://mywx.znmya.com')
            ->setTitle('Office 2007 XLSX Document')
            ->setSubject('Office 2007 XLSX Document')
            ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Result file');
        $array=['0'=>'A','1'=>'B','2'=>'C','3'=>'D','4'=>'E','5'=>'F','6'=>'G','7'=>'H','8'=>'I','9'=>'J','10'=>'K','11'=>'L','12'=>'M','13'=>'N','14'=>'O','15'=>'P','16'=>'Q','17'=>'R','18'=>'S','19'=>'T','20'=>'U','21'=>'V','22'=>'W','23'=>'X','24'=>'Y','25'=>'Z'];
        foreach ($name as $key=>$val){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($array[$key].'1',$val);
        }
        $i=2;
        foreach($data[0] as $k=>$v){
            foreach ($name as$key=>$val) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($array[$key] . $i, $v[$val]);
            }
            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('报表统计');
        $objPHPExcel->setActiveSheetIndex(0);
        $report = Db::name($this->table)->where(['company_id' => $company_id, 'report_id'=>$_GET['report_id'],'report_type' => '套餐统计报表'])->find();
        $filename=urlencode($report['report_name']).'_'.date('Y-m-d');


        /*
        *生成xlsx文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
        */

        //生成xls文件
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');


        $objWriter->save('php://output');
        exit;
    }
}
