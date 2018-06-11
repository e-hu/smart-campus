<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\LogService;
use service\DataService;
use think\Db;
use think\Request;

/**
 * 系统用户管理控制器
 * Class User
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class Cbdinnerinfo extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'emp_cookbook_dinner_info';
    public $table2 = 'emp_cookbook_dinner_info_modi';


    /**
     * 订单列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '订单列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name($this->table)
            ->alias('a')
            ->join('Employee_list e', 'a.emp_id = e.emp_id and a.company_id = e.company_id', 'left')
            ->join('dept_info k', 'e.Dept_Id = k.dept_no and a.company_id = k.company_id', 'left')
            ->join('canteen_base_info c', 'a.canteen_no = c.canteen_no and a.company_id = c.company_id', 'left')
            ->join('dinner_base_info b', 'a.dinner_flag = b.dinner_flag and a.company_id=b.company_id', 'left')
            ->join('canteen_sale_window_base_info s', 'a.dinner_machine_no = s.sale_window_no and a.company_id=s.company_id', 'left')
            ->join('cookbook_base_info i', 'a.cookbook_no = i.cookbook_no and a.company_id=i.company_id', 'left')
            ->field('a.*,dept_name,canteen_name,cookbook_name,dinner_name,sale_window_name,emp_name')
            ->where(['a.company_id' => session('user.company_id'), 'Emp_Status' => '1'])
            ->order('dinner_createtime desc');
        // 应用搜索条件
        if (isset($get['dinner_datetime']) && $get['dinner_datetime'] !== '') {
            list($start, $end) = explode('-', str_replace(' ', '', $get['dinner_datetime']));
            $db->whereBetween('a.dinner_datetime', ["{$start} 00:00:00", "{$end} 23:59:59"]);
        }
        if (isset($get['canteen_no']) && $get['canteen_no'] !== '') {
            $db->where('a.canteen_no',$get['canteen_no']);
        }
        if (isset($get['dinner_flag']) && $get['dinner_flag'] !== '') {
            $db->where('a.dinner_flag', $get['dinner_flag']);
        }

        foreach (['cookbook_name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where('i.cookbook_name', $get[$key]);
            }
        }

        foreach (['Emp_Name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where('e.Emp_Name', 'like', "%{$get[$key]}%");
            }
        }
        if (isset($get['tag']) && $get['tag'] !== '') {
            //$db->where("concat(',',tagid_list,',') like :tag", ['tag' => "%,{$get['tag']},%"]);   //mysql存在contcat内置函数
            $db->where('k.dept_no', $get['tag']);
        }
        if (session('user.create_by') != '10001') {
            $db->where(' exists (select 1 from t_user_manager_dept_id b where a.canteen_no=b.dept_id and a.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
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
        $canteens = Db::name('canteen_base_info')->where('company_id', session('company_id'));
        if (session('user.create_by') != '10001') {
            $canteens->where(' exists (select 1 from t_user_manager_dept_id b where canteen_base_info.canteen_no=b.dept_id and canteen_base_info.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
        }
        $this->assign('canteens', $canteens->column('canteen_no,canteen_name'));

        $dinnerbases = Db::name('dinner_base_info')->where('company_id', session('company_id'))->column('dinner_flag,dinner_name');
        $this->assign('dinnerbases', $dinnerbases);

        $tags = Db::name('dept_info')->where('company_id', session('company_id'))->column('dept_no,dept_name');
        $this->assign('tags', $tags);

    }

    /**
     * 审核订单列表
     */
    public function refund()
    {
        // 设置页面标题
        $this->title = '审核列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name('emp_cookbook_dinner_info_modi')
            ->alias('a')
            ->join('Employee_list e', 'a.emp_id = e.emp_id and a.company_id = e.company_id', 'left')
            ->join('dept_info k', 'e.Dept_Id = k.dept_no and a.company_id = k.company_id', 'left')
            ->join('canteen_base_info c', 'a.canteen_no = c.canteen_no and a.company_id = c.company_id', 'left')
            ->join('dinner_base_info b', 'a.dinner_flag = b.dinner_flag and a.company_id=b.company_id', 'left')
            ->join('canteen_sale_window_base_info s', 'a.dinner_machine_no = s.sale_window_no and a.company_id=s.company_id', 'left')
            ->join('cookbook_base_info i', 'a.cookbook_no = i.cookbook_no and a.company_id=i.company_id', 'left')
            ->join('Employee_list h', 'h.emp_id = a.checker1_id and a.company_id = h.company_id', 'left')
            ->join('system_user g', 'g.id = a.checker2_id and g.company_id = a.company_id', 'left')
            ->field('a.*,dept_name,canteen_name,cookbook_name,dinner_name,sale_window_name,e.emp_name,h.emp_name as shenhe_name,g.username')
            ->where(['a.company_id' => session('user.company_id'), 'e.Emp_Status' => '1','checker1_id'=>array('NEQ','null')])
            ->order('check_status asc');
        // 应用搜索条件

        if(!empty($get['source_id'])&&$get['source_id'] == '1'){
            $db->where('source_id is not null');
        }elseif(!empty($get['source_id'])&&$get['source_id'] == '2'){
            $db->where('source_id', 'null');
        }

        if(!empty($get['status'])&&$get['status'] == '1'){
            $db->where('check_status != 2');
        }elseif(!empty($get['status'])&&$get['status'] == '2'){
            $db->where('check_status', '2');
        }

        if (isset($get['dinner_datetime']) && $get['dinner_datetime'] !== '') {
            list($start, $end) = explode('-', str_replace(' ', '', $get['dinner_datetime']));
            $db->whereBetween('a.dinner_datetime', ["{$start} 00:00:00", "{$end} 23:59:59"]);
        }
        if (isset($get['canteen_no']) && $get['canteen_no'] !== '') {
            $db->where('a.canteen_no',$get['canteen_no']);
        }
        if (isset($get['dinner_flag']) && $get['dinner_flag'] !== '') {
            $db->where('a.dinner_flag', $get['dinner_flag']);
        }

        foreach (['cookbook_name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where('i.cookbook_name', $get[$key]);
            }
        }
        foreach (['Emp_Name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where('e.Emp_Name', 'like', "%{$get[$key]}%");
            }
        }
        if (isset($get['tag']) && $get['tag'] !== '') {
            //$db->where("concat(',',tagid_list,',') like :tag", ['tag' => "%,{$get['tag']},%"]);   //mysql存在contcat内置函数
            $db->where('k.dept_no', $get['tag']);
        }
        if (session('user.create_by') != '10001') {
            $db->where(' exists (select 1 from t_user_manager_dept_id b where a.canteen_no=b.dept_id and a.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
        }
        // 实例化并显示
        return parent::_list($db);
    }

    /**
     * 删除餐厅
     */
    public function shenhe()
    {
        LogService::write('订餐管理', '执行订单审核操作');
        $where = [];
        $where['company_id'] = session('user.company_id');
        if (DataService::update($this->table2, $where, 'id')) {
            $request = Request::instance();
            $ids = explode(',', $request->post('id', ''));
            $where['a.id'] = ['in', $ids];
            Db::table('emp_cookbook_dinner_info_modi')->alias('a')->where($where)->update(['checker2_id'=>session('user.id'),'checker2_datetime'=>date("Y-m-d H:i:s")]);
            $modi_list = Db::table('emp_cookbook_dinner_info_modi')
                ->alias('a')
                ->join('Employee_List b','a.emp_id = b.Emp_Id','left')
                ->field('Emp_MircoMsg_Id')
                ->where($where)->select();
            foreach ($modi_list as $val){
                patchMSC($val['Emp_MircoMsg_Id']);
            }
            $this->success("订单审核成功！", '');
        }
        $this->error("订单审核失败，请稍候再试！");
    }
}
