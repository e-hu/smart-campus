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
class CbPricelist extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'canteen_cookbook_price';

    /**
     * 周菜谱列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '周菜谱列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();

        if (isset($get['canteen_no']) && $get['canteen_no'] !== '') {
            $canteen_no = $get['canteen_no'];
        } else {
            $canteen_no = ' ';
        }

        if (isset($get['week_id']) && $get['week_id'] !== '') {
            $week_id = $get['week_id'];
        } else {
            $week_id = ' ';
        }
        $sqlstr = "exec [up_canteen_week_count] ?,?,?,?";
        $return = Db::query($sqlstr, [session('user.company_id'), $canteen_no, $week_id, session('user.id')]);
        if (empty($return[0])) {
            $this->error('数据不存在');
        }
        $canteens = Db::name('canteen_base_info')->where('company_id', session('user.company_id'));
        if (session('user.create_by') != '10001') {
            $canteens->where(' exists (select 1 from t_user_manager_dept_id b where canteen_base_info.canteen_no=b.dept_id and canteen_base_info.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
        }
        $this->assign('canteens', $canteens->column('canteen_no,canteen_name'));
        $weeks = Db::name('week_day_list')->column('id,week_num,start_datetime,end_datetime');
        $this->assign('weeks', $weeks);
        $this->assign('list', $return[0]);
        return $this->fetch();
    }


    /**
     * 周菜谱编辑
     */
    public function edit()
    {
        LogService::write('订餐管理', '执行周菜谱编辑操作');
        if ($this->request->isGet()) {
            $sqlstr = "exec [up_canteen_week_detail] ?,?,?,?";
            $data = Db::query($sqlstr, [session('user.company_id'), $_GET['canteen_no'], $_GET['week_id'], session('user.id')]);
            if (empty($data)) {
                $this->error('数据不存在');
            }
            $list = [];
            foreach ($data[0] as $val) {
                $list[$val['week_name']][$val['dinner_name']][$val['meal_id']] = $val;
            }
            $canteens = Db::name('canteen_base_info')->where(['canteen_no' => $_GET['canteen_no'], 'company_id' => session('user.company_id')])->find();
            $weeks = Db::name('week_day_list')->where(['id' => $_GET['week_id']])->find();
            $dt_start = strtotime($weeks['start_datetime']);
            $dt_end = strtotime($weeks['end_datetime']);
            while ($dt_start <= $dt_end) {
                $date[] = date('Y-m-d', $dt_start);
                $dt_start = strtotime('+1 day', $dt_start);
            }
            $weekarray = array("日", "一", "二", "三", "四", "五", "六");
            foreach ($date as $val) {
                $week[] = '周' . $weekarray[date("w", strtotime($val))];
            }
            $date_time = [];
            foreach ($date as $val) {
                $date_time['周' . $weekarray[date("w", strtotime($val))]] = $val;
            }
            $this->assign('canteens', $canteens);
            $this->assign('weeks', $weeks);
            $this->assign('date_time', $date_time);
            $this->assign('list', $list);
        } else {
            if (!empty($_POST)) {
                $dt_start = strtotime($_POST['start_datetime']);
                $dt_end = strtotime($_POST['end_datetime']);
                while ($dt_start <= $dt_end) {
                    $date[] = date('Y-m-d', $dt_start);
                    $dt_start = strtotime('+1 day', $dt_start);
                }
                $weekarray = array("日", "一", "二", "三", "四", "五", "六");
                foreach ($date as $val) {
                    $week[] = '周' . $weekarray[date("w", strtotime($val))];
                }
                $date_time = [];
                foreach ($date as $val) {
                    $date_time['周' . $weekarray[date("w", strtotime($val))]] = $val;
                }
                $canteen_no = $_POST['canteen_no'];

                Db::table('canteen_cookbook_price')->where(['company_id' => session('user.company_id'), 'canteen_no' => $canteen_no])->where('sale_datetime', 'between', [$_POST['start_datetime'], $_POST['end_datetime']])->delete();

                $dinner_list = Db::table('dinner_base_info')->where('company_id', session('user.company_id'))->order('dinner_flag asc')->select();
                $meal_list = Db::table('cookbook_meal_type')->where(['company_id' => session('user.company_id'), 'meal_flag' => '0'])->select();
                foreach ($week as $val) {
                    foreach ($dinner_list as $value) {
                        foreach ($meal_list as $meal) {
                            if ($_POST[$val . $value['dinner_name'] . $meal['meal_id']]) {
                                $data = [];
                                $data['canteen_no'] = $canteen_no;
                                $data['cookbook_no'] = $_POST[$val . $value['dinner_name'] . $meal['meal_id']];
                                $cookbook_info = Db::table('cookbook_base_info')->where(['cookbook_no' => $data['cookbook_no'], 'company_id' => session('user.company_id')])->find();
                                $data['cookbook_price'] = $cookbook_info['price'];
                                $data['sale_datetime'] = $date_time[$val];
                                $price_id = Db::query('select dbo.a_get_datetimestrguid()');
                                $data['price_id'] = $price_id[0][''];
                                $data['status'] = '1';
                                $data['price_flag'] = $cookbook_info['price_flag'];
                                $data['dinner_flag'] = $value['dinner_flag'];
                                $data['cookbook_info'] = $cookbook_info['cookbook_info'];
                                $data['company_id'] = session('user.company_id');
                                $data['meal_id'] = $meal['meal_id'];
                                Db::table('canteen_cookbook_price')->insert($data);
                            }
                        }
                    }
                }
                $this->success('发布周菜谱成功', '');
            }
        }
        return $this->_form($this->table, 'form', 'id');
    }

    /**
     * 周菜谱查看
     */
    public function view()
    {
        LogService::write('订餐管理', '执行周菜谱编辑操作');
        if ($this->request->isGet()) {
            $sqlstr = "exec [up_canteen_week_detail] ?,?,?,?";
            $data = Db::query($sqlstr, [session('user.company_id'), $_GET['canteen_no'], $_GET['week_id'], session('user.id')]);
            if (empty($data)) {
                $this->error('数据不存在');
            }
            $list = [];
            foreach ($data[0] as $val) {
                $list[$val['week_name']][$val['dinner_name']][$val['meal_id']] = $val;
            }
            $canteens = Db::name('canteen_base_info')->where(['canteen_no' => $_GET['canteen_no'], 'company_id' => session('user.company_id')])->find();
            $weeks = Db::name('week_day_list')->where(['id' => $_GET['week_id']])->find();
            $meals = Db::name('cookbook_meal_type')->where(['company_id' => session('user.company_id'), 'meal_flag' => '0'])->select();
            foreach ($meals as $val) {
                $cookbooks = Db::name('cookbook_base_info')->where('meal_id', $val['meal_id'])->where('company_id', session('user.company_id'))->select();
                $this->assign('cookbooks' . $val['meal_id'], $cookbooks);
            }
            $dt_start = strtotime($weeks['start_datetime']);
            $dt_end = strtotime($weeks['end_datetime']);
            while ($dt_start <= $dt_end) {
                $date[] = date('Y-m-d', $dt_start);
                $dt_start = strtotime('+1 day', $dt_start);
            }
            $weekarray = array("日", "一", "二", "三", "四", "五", "六");
            foreach ($date as $val) {
                $week[] = '周' . $weekarray[date("w", strtotime($val))];
            }
            $date_time = [];
            foreach ($date as $val) {
                $date_time['周' . $weekarray[date("w", strtotime($val))]] = $val;
            }
            $this->assign('meals', $meals);
            $this->assign('canteens', $canteens);
            $this->assign('weeks', $weeks);
            $this->assign('date_time', $date_time);
            $this->assign('list', $list);
        }
        return $this->fetch();
    }

    /**
     * 周菜谱复制
     */
    public function copy()
    {
        LogService::write('订餐管理', '执行周菜谱复制操作');
        $this->title = '周菜谱复制';
        if ($this->request->isGet()) {
            $canteens = Db::name('canteen_base_info')->where(['canteen_no' => array('neq', $_GET['canteen_no']), 'company_id' => session('user.company_id')])->select();
            $this->assign('canteens', $canteens);
            $this->assign('week_id', $_GET['week_id']);
            $this->assign('copy_canteen_no', $_GET['canteen_no']);
        } else {
            if (empty($_POST['copy_canteen_no'])|| empty($_POST['week_id']) || empty($_POST['canteen_no'])) {
               $this->error('复制餐厅参数错误');
            }else{
              //存储过程
                $sqlstr = "exec [up_canteen_cookbook_dinner_price_copy] ?,?,?";
                $data = Db::query($sqlstr, [$_POST['copy_canteen_no'], $_POST['week_id'], $_POST['canteen_no']]);
                if ($data[0][0]['ok'] == '1') {
                    $this->success('复制成功');
                }else{
                    $this->error('复制失败');
                }
            }
        }
        return $this->fetch();
    }

    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data)
    {
        $meals = Db::name('cookbook_meal_type')->where(['company_id' => session('user.company_id'), 'meal_flag' => '0'])->select();
        foreach ($meals as $val) {
            $cookbooks = Db::name('cookbook_base_info')->where('meal_id', $val['meal_id'])->where('company_id', session('user.company_id'))->select();
            $this->assign('cookbooks' . $val['meal_id'], $cookbooks);
        }
        $this->assign('meals', $meals);
    }

    /**
     * 周菜谱时间
     */
    public function starttime()
    {
        LogService::write('订餐管理', '执行周菜谱时间操作');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            if (empty($post['start_time'])) {
                $post['start_time'] = null;
            }
            $data['dinner_choose_start_datetime'] = $post['start_time'];
            $result = Db::table('canteen_week_cookbook_main')->where('id', $post['id'])->update($data);
            $data = [];
            if ($result) {
                $data['flag'] = 1;
            } else {
                $data['flag'] = 0;
            }
            return json($data);
        }
    }

    public function endtime()
    {
        LogService::write('订餐管理', '执行周菜谱时间操作');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            if (empty($post['end_time'])) {
                $post['end_time'] = null;
            }
            $data['dinner_choose_end_datetime'] = $post['end_time'];
            $result = Db::table('canteen_week_cookbook_main')->where('id', $post['id'])->update($data);
            $data = [];
            if ($result) {
                $data['flag'] = 1;
            } else {
                $data['flag'] = 0;
            }
            return json($data);
        }
    }

}
