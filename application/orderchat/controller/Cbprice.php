<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\LogService;
use service\DataService;
use think\Db;

/**
 * 系统用户管理控制器
 * Class User
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class CbPrice extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'canteen_cookbook_price';

    /**
     * 菜谱列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '菜谱列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();

        $db = Db::name($this->table)
            ->alias('a')
            ->field('a.sale_datetime,a.canteen_no,a.dinner_flag,canteen_name,dinner_name,sum(isnull(status,0)) as dinner_count')
            ->join('canteen_base_info c', 'a.canteen_no = c.canteen_no', 'left')
            ->join('dinner_base_info b', 'a.dinner_flag = b.dinner_flag and a.company_id = b.company_id', 'left')
            ->group('a.sale_datetime,a.canteen_no,a.dinner_flag,canteen_name,dinner_name')
            ->where('a.company_id', session('user.company_id'))
            ->order('sale_datetime desc');

        foreach (['sale_datetime'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where('a.sale_datetime', $get[$key]);
            }
        }

        if (isset($get['canteen_no']) && $get['canteen_no'] !== '') {
            //$db->where("concat(',',tagid_list,',') like :tag", ['tag' => "%,{$get['tag']},%"]);   //mysql存在contcat内置函数
            $db->where("',' +''+a.canteen_no+''+',' like :canteen_no", ['canteen_no' => "%,{$get['canteen_no']},%"]);
        }
        if (isset($get['dinner_flag']) && $get['dinner_flag'] !== '') {
            //$db->where("concat(',',tagid_list,',') like :tag", ['tag' => "%,{$get['tag']},%"]);   //mysql存在contcat内置函数
            $db->where('a.dinner_flag', $get['dinner_flag']);
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

    }


    /**
     * 菜谱生成
     */
    public function generate()
    {
        LogService::write('订餐管理', '执行菜谱生成操作');
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $data = [];
            if ($post['canteen_no'] == '0' || $post['dinner_flag'] == '0' || $post['date_str'] == '') {
                $data['flag'] = '0';
                return json($data);
            }
            $where = [];
            if (!empty($post['cookbook_name'])) {
                $where['c.cookbook_name'] = array('like', '%' . $post['cookbook_name'] . '%');
            }
            $where['a.company_id'] = session('user.company_id');
            $where['a.canteen_no'] = $post['canteen_no'];
            $where['a.sale_datetime'] = $post['date_str'];
            $where['a.dinner_flag'] = $post['dinner_flag'];
            $sqlstr = "exec [up_create_canteen_cookbook_price] ?,?,?,?";
            $result = Db::query($sqlstr, [session('user.company_id'), $post['canteen_no'], $post['date_str'], $post['dinner_flag']]);
            if ($result[0][0]['msg'] == '执行成功') {
                $db = Db::name($this->table)
                    ->alias('a')
                    ->field('a.*,cookbook_name,dinner_name')
                    ->join('cookbook_base_info c', 'a.cookbook_no = c.cookbook_no and a.company_id = c.company_id', 'left')
                    ->join('dinner_base_info b', 'a.dinner_flag = b.dinner_flag and a.company_id = b.company_id', 'left')
                    ->where($where)
                    ->select();
                if (!empty($db)) {
                    $data['flag'] = '1';
                    $data['list'] = $db;
                } else {
                    $data['flag'] = '2';
                }
            } else {
                $data['flag'] = '0';
            }
            return json($data);
        } else {
            return $this->_form($this->table, 'form');
        }
    }

    /**
     * 菜谱编辑
     */
    public function editGenerate()
    {
        LogService::write('订餐管理', '执行菜谱编辑操作');
        if ($this->request->isGet()) {
            $data = [];
            $get = $this->request->get();
            if (!empty($get['canteen_no']) and !empty($get['sale_datetime']) and !empty($get['dinner_flag'])) {
                $where['a.company_id'] = session('user.company_id');
                $where['a.canteen_no'] = $get['canteen_no'];
                $where['a.sale_datetime'] = date('Y-m-d', strtotime($get['sale_datetime']));
                $where['a.dinner_flag'] = $get['dinner_flag'];
            } else {
                $this->error('编辑失败,餐次不存在!');
            }
            if (!empty($where)) {
                $data['flag'] = '1';
                $db = Db::name($this->table)
                    ->alias('a')
                    ->field('a.*,cookbook_name,dinner_name')
                    ->join('cookbook_base_info c', 'a.cookbook_no = c.cookbook_no and a.company_id = c.company_id', 'left')
                    ->join('dinner_base_info d', 'a.dinner_flag = d.dinner_flag and a.company_id = d.company_id', 'left')
                    ->where($where)
                    ->order('status desc')
                    ->select();
                $data['list'] = $db;
            }
            $canteen_info = Db::name('canteen_base_info')->where(['canteen_no' => $get['canteen_no'], 'company_id' => session('user.company_id')]);
            if (session('user.create_by') != '10001') {
                $canteen_info = $canteen_info->where(' exists (select 1 from t_user_manager_dept_id b where canteen_base_info.canteen_no=b.dept_id and canteen_base_info.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')])->find();
            }else{
                $canteen_info = $canteen_info->find();
            }
            $dinner_info = Db::name('dinner_base_info')->where(['dinner_flag' => $get['dinner_flag'], 'company_id' => session('user.company_id')])->find();
            $info['canteen_name'] = $canteen_info['canteen_name'];
            $info['dinner_name'] = $dinner_info['dinner_name'];
            $info['sale_datetime'] = date('Y-m-d', strtotime($get['sale_datetime']));
            $this->assign('info', $info);
            $this->assign('list', $data['list']);
            $this->assign('data', $get);
            return $this->_form($this->table, 'editform');
        }
    }


    /**
     * 售卖列表
     */
    public function positionindex()
    {
        // 设置页面标题
        $this->title = '菜谱列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();

        $db = Db::name($this->table)
            ->alias('a')
            ->field('a.sale_datetime,a.canteen_no,a.dinner_flag,a.cookbook_no,canteen_name,cookbook_name,dinner_name,position_id,sale_window_name,count(dinner_status) as dinner_count')
            ->join('canteen_base_info c', 'a.canteen_no = c.canteen_no and a.company_id = c.company_id', 'left')
            ->join('dinner_base_info b', 'a.dinner_flag = b.dinner_flag and a.company_id = b.company_id', 'left')
            ->join('canteen_cookbook_window_position p', 'a.cookbook_no = p.cookbook_no and a.canteen_no = p.canteen_no and a.sale_datetime = p.sale_datetime and a.dinner_flag = p.dinner_flag and a.company_id = p.company_id', 'left')
            ->join('canteen_sale_window_base_info s', 'p.windows_no = s.sale_window_no and a.company_id = s.company_id', 'left')
            ->join('cookbook_base_info i', 'a.cookbook_no = i.cookbook_no and a.company_id = i.company_id', 'left')
            ->join('emp_cookbook_dinner_info d', 'a.cookbook_no = d.cookbook_no and a.canteen_no = d.canteen_no and a.dinner_flag = d.dinner_flag and a.sale_datetime = d.dinner_datetime and a.company_id = d.company_id ', 'left')
            ->group('a.sale_datetime,a.canteen_no,a.cookbook_no,a.dinner_flag,canteen_name,cookbook_name,dinner_name,position_id,sale_window_name')
            ->having('count(dinner_status)>0')
            ->where(['a.company_id' => session('user.company_id'), 'a.status' => '1'])
            ->order('sale_datetime desc,canteen_no,dinner_flag,dinner_count desc');

        foreach (['sale_datetime'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where('a.sale_datetime', $get[$key]);
            }
        }

        if (isset($get['canteen_no']) && $get['canteen_no'] !== '') {
            //$db->where("concat(',',tagid_list,',') like :tag", ['tag' => "%,{$get['tag']},%"]);   //mysql存在contcat内置函数
            $db->where("',' +''+a.canteen_no+''+',' like :canteen_no", ['canteen_no' => "%,{$get['canteen_no']},%"]);
        }
        if (isset($get['dinner_flag']) && $get['dinner_flag'] !== '') {
            //$db->where("concat(',',tagid_list,',') like :tag", ['tag' => "%,{$get['tag']},%"]);   //mysql存在contcat内置函数
            $db->where('a.dinner_flag', $get['dinner_flag']);
        }
        if (session('user.create_by') != '10001') {
            $db->where(' exists (select 1 from t_user_manager_dept_id b where a.canteen_no=b.dept_id and a.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
        }
        // 实例化并显示
        return parent::_list($db);
    }


    /**
     * 售卖窗口绑定
     */
    public function position()
    {
        LogService::write('订餐管理', '执行售卖窗口绑定操作');
        if ($this->request->isPost()) {
            $extendData = [];
            $extendData['company_id'] = session('user.company_id');
            $where = [];
            if (isset($_POST['id'])) {
                $where['id'] = $_POST['id'];
            }
            return $this->_form('canteen_cookbook_window_position', 'form', 'id', $where, $extendData);
        }
        $get = $this->request->get();
        $info = Db::name('canteen_cookbook_window_position')
            ->where(['canteen_no' => $get['canteen_no'], 'sale_datetime' => $get['sale_datetime'], 'dinner_flag' => $get['dinner_flag'], 'cookbook_no' => $get['cookbook_no']])
            ->find();
        $this->assign('info', $info);
        $this->assign('data', $get);
        return $this->_form('canteen_cookbook_window_position', 'positionform');
    }

    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data)
    {
        $db = Db::name('canteen_base_info')->where('company_id', session('user.company_id'));
        if (session('user.create_by') != '10001') {
            $db->where(' exists (select 1 from t_user_manager_dept_id b where canteen_base_info.canteen_no=b.dept_id and canteen_base_info.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
        }
        $this->assign('canteens', $db->select());  //餐厅列表
        $this->assign('dinnerbases', Db::name('dinner_base_info')->where('company_id', session('user.company_id'))->select()); //菜谱列表
        if (isset($_GET['canteen_no'])) {
            $this->assign('windowbases', Db::name('canteen_sale_window_base_info')->where('company_id', session('user.company_id'))->where('canteen_no', $_GET['canteen_no'])->select());//窗口列表
        } else {
            $this->assign('windowbases', Db::name('canteen_sale_window_base_info')->where('company_id', session('user.company_id'))->select());//窗口列表

        }
    }

    /**
     * 菜品编辑价格
     */
    public function save()
    {
        LogService::write('订餐管理', '执行菜品编辑价格操作');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $result = DataService::save($this->table, $post, 'price_id');
            $data = [];
            if ($result) {
                $data['flag'] = 1;
            } else {
                $data['flag'] = 0;
            }
            return json($data);
        }
    }


    /**
     * 菜谱编辑
     */
    public function edit()
    {
        LogService::write('订餐管理', '执行菜谱编辑操作');
        return $this->_form($this->table, 'form', 'price_id');
    }

}
