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
class DinnerBase extends BasicAdmin {

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'dinner_base_info';

    /**
     * 餐次列表
     */
    public function index() {
        // 设置页面标题
        $this->title = '餐次列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name($this->table)->where(['company_id'=>session('user.company_id')])->order('dinner_flag asc');
        // 应用搜索条件
        foreach (['dinner_name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where($key, 'like', "%{$get[$key]}%");
            }
        }
        // 实例化并显示
        return parent::_list($db);
    }



    /**
     * 餐次添加
     */
    public function add() {
        LogService::write('订餐管理', '执行餐次添加操作');
        $extendData = [];
        if ($this->request->isPost()) {
            $sqlstr = "exec [up_get_max_id] ?,?,?,?";
            $data = Db::query($sqlstr, ['', 'dinner_base', 'DINI', 0]);
            $extendData['dinner_no'] = $data[0][0]['id'];
            $extendData['company_id'] = session('user.company_id');
            $post = $this->request->post();
            if (Db::name($this->table)->where('company_id', session('user.company_id'))->where('dinner_flag', $post['dinner_flag'])->find()) {
                $this->error('餐次编号已经存在，请使用其它编号！');
            }
        }
        return $this->_form($this->table, 'form','dinner_no','',$extendData);
    }

    /**
     * 餐次编辑
     */
    public function edit() {
        LogService::write('订餐管理', '执行餐次编辑操作');
        return $this->_form($this->table, 'form','dinner_no');
    }



    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data) {
        if ($this->request->isPost()) {
            if (Db::name($this->table)->where('company_id', session('user.company_id'))->where('dinner_no', $data['dinner_no'])->find()) {
                unset($data['dinner_name']);
            } elseif (isset($data['dinner_name']) and Db::name($this->table)->where('dinner_name', $data['dinner_name'])->where('company_id', session('user.company_id'))->find()) {
                $this->error('餐次名称已经存在，请使用其它名称！');
            }
        }
    }

    /**
     * 删除餐次
     */
    public function del() {
        LogService::write('订餐管理', '执行删除餐次操作');
        $where = [];
        $where['company_id'] = session('user.company_id');
        if (DataService::update($this->table,$where,'dinner_no')) {
            $this->success("餐次删除成功！", '');
        }
        $this->error("餐次删除失败，请稍候再试！");
    }

}
