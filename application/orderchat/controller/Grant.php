<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\LogService;
use service\DataService;
use think\Db;

/**
 * 补贴信息管理控制器
 * Class Company
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class Grant extends BasicAdmin {

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'emp_grant_main';
    public $detailtable = 'emp_grant_detail';

    /**
     * 补贴列表
     */
    public function index() {
        // 设置页面标题
        $this->title = '补贴信息管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name($this->table)
            ->alias('a')
            ->field('a.*,dept_name')
            ->join('dept_info c', 'a.Dept_Id = c.dept_no','left')
            ->where('a.company_id',session('user.company_id'));
        // 应用搜索条件
        if (isset($get['tag']) && $get['tag'] !== '') {
            $db->where('dept_no',$get['tag']);
        }
        // 实例化并显示
        return parent::_list($db);
    }

    /**
     * 列表数据处理
     * @param type $list
     */
    protected function _data_filter(&$list) {
        $tags = Db::name('dept_info')->where('company_id', session('company_id'))->column('dept_no,dept_name');
        $this->assign('tags', $tags);
    }

    /**
     * 补贴添加
     */
    public function add() {
        LogService::write('订餐管理', '执行补贴添加操作');
        $extendData = [];
        if ($this->request->isPost()) {
            $sqlstr = "exec [up_get_max_id] ?,?,?,?";
            $data = Db::query($sqlstr, ['', 'grant', 'GTID', 0]);
            $extendData['grant_no'] = $data[0][0]['id'];
            $extendData['company_id'] = session('user.company_id');
            $extendData['status'] = 0; //审核状态
            $extendData['grant_datetime'] = date('Y-m-d h:i:s',time());
            $extendData['grant_operid'] = session('user.username');
        }
        return $this->_form($this->table, 'form','grant_no','',$extendData);
    }


    /**
     * 补贴人员管理
     */
    public function editemp() {

        LogService::write('订餐管理', '执行补贴人员管理操作');

        $this->title = '编辑补贴人员状态';

        $info = Db::name($this->table)
            ->alias('a')
            ->field('a.*,dept_name')
            ->join('dept_info c', 'a.dept_id = c.dept_no','left')
            ->where(['grant_no'=>$_GET['grant_no'],'a.company_id'=>session('user.company_id')])
            ->find();
        $db = Db::name('emp_grant_detail')
            ->field('emp_grant_detail.*,emp_name')
            ->join('Employee_List e', 'emp_grant_detail.emp_id = e.Emp_Id and e.Emp_Status = 1','left')
            ->where(['emp_grant_detail.company_id'=>session('user.company_id'),'emp_grant_detail.grant_no'=>$info['grant_no']]);
        // 应用搜索条件
            if (isset($_GET['emp_name']) && $_GET['emp_name'] !== '') {
                $db->where('e.Emp_Name', 'like', "%{$_GET['emp_name']}%");
            }

        $this->assign('info',$info);

        return parent::_list($db);
    }


    /**
     * 补贴人员审核
     */
    public function audit() {

        LogService::write('订餐管理', '执行审核补贴操作');

        $this->title = '审核补贴';

        $info = Db::name($this->table)
            ->alias('a')
            ->field('a.*,dept_name')
            ->join('dept_info c', 'a.dept_id = c.dept_no','left')
            ->where(['grant_no'=>$_GET['grant_no'],'a.company_id'=>session('user.company_id')])
            ->find();
        $db = Db::name('emp_grant_detail')
            ->field('emp_grant_detail.*,emp_name')
            ->join('Employee_List e', 'emp_grant_detail.emp_id = e.Emp_Id and e.Emp_Status = 1','left')
            ->where(['emp_grant_detail.company_id'=>session('user.company_id'),'emp_grant_detail.grant_no'=>$info['grant_no']]);
        // 应用搜索条件
        if (isset($_GET['emp_name']) && $_GET['emp_name'] !== '') {
            $db->where('e.Emp_Name', 'like', "%{$_GET['emp_name']}%");
        }

        $this->assign('info',$info);

        return parent::_list($db);
    }

    /**
     * 补贴状态编辑
     */
    public function editaudit() {
        LogService::write('订餐管理', '执行补贴状态编辑操作');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $post['grant_checktime'] = date('Y-m-d h:i:s',time());
            $post['grant_checkoperid'] = session('user.username');
            $result = DataService::save($this->table, $post, 'grant_no');
            $data = [];
            if($result){
                $this->success('恭喜, 审核成功!', '');
            }else{
                $this->error('审核保存失败, 请稍候再试!');
            }
        }
    }

    /**
     * 人员金额编辑
     */
    public function save() {
        LogService::write('订餐管理', '执行人员金额编辑操作');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $result = DataService::save('emp_grant_detail', $post, 'id');
            $data = [];
            if($result){
                $data['flag'] = 1;
            }else{
                $data['flag'] = 0;
            }
            return json($data);
        }
    }

    /**
     * 补贴人员查看
     */
    public function view() {

        LogService::write('订餐管理', '执行补贴人员查看操作');

        $this->title = '查看补贴人员';

        $info = Db::name($this->table)
            ->alias('a')
            ->field('a.*,dept_name')
            ->join('dept_info c', 'a.dept_id = c.dept_no','left')
            ->where(['grant_no'=>$_GET['grant_no'],'a.company_id'=>session('user.company_id')])
            ->find();
        $db = Db::name('emp_grant_detail')
            ->field('emp_grant_detail.*,emp_name')
            ->join('Employee_List e', 'emp_grant_detail.emp_id = e.Emp_Id and e.Emp_Status = 1','left')
            ->where(['emp_grant_detail.company_id'=>session('user.company_id'),'emp_grant_detail.grant_no'=>$info['grant_no']]);
        // 应用搜索条件
        if (isset($_GET['emp_name']) && $_GET['emp_name'] !== '') {
            $db->where('e.Emp_Name', 'like', "%{$_GET['emp_name']}%");
        }

        $this->assign('info',$info);

        return parent::_list($db);
    }

    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data) {
        if ($this->request->isPost()) {
            if (Db::name($this->table)->where('company_id', session('user.company_id'))->where('grant_no', $data['grant_no'])->find()) {
                unset($data['grant_month']);unset($data['dept_id']);
            } elseif (isset($data['grant_month']) and Db::name($this->table)->where(['grant_month'=>$data['grant_month'],'dept_id'=>$data['dept_id']])->where('company_id', session('user.company_id'))->find()) {
                $this->error('该部门此月份已经存在，请使用其它月份！');
            }
        }else{
            $this->assign('dept_infos', Db::name("dept_info")->where('company_id',session('user.company_id'))->select());
        }
    }

    /**
     * 删除补贴
     */
    public function del() {
        LogService::write('订餐管理', '执行删除补贴操作');
        if (DataService::update($this->table,'','grant_no')) {
            $this->success("补贴删除成功！", '');
        }
        $this->error("补贴删除失败，请稍候再试！");
    }

    /**
     * 人员禁用
     */
    public function forbid()
    {
        LogService::write('订餐管理', '执行人员禁用操作');
        if (DataService::update($this->detailtable,'','id')) {
            $this->success("人员禁用成功！", '');
        }
        $this->error("人员禁用失败，请稍候再试！");
    }

    /**
     * 人员启用
     */
    public function resume()
    {
        LogService::write('订餐管理', '执行人员启用操作');
        if (DataService::update($this->detailtable,'','id')) {
            $this->success("人员启用成功！", '');
        }
        $this->error("人员启用失败，请稍候再试！");
    }



}
