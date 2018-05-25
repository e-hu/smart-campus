<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\LogService;
use service\DataService;
use think\Db;

/**
 * 人员信息管理控制器
 * Class Company
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class Face extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'emp_att_record';

    /**
     * 人员列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '人脸考勤管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name($this->table)
            ->alias('a')
            ->field('a.*,dept_name,Emp_Name')
            ->join('Employee_List e', 'e.emp_id = a.emp_id and e.company_id = a.company_id', 'left')
            ->join('dept_info c', 'e.Dept_Id = c.dept_no and a.company_id = c.company_id', 'left')
            ->where('a.company_id', session('user.company_id'));
        // 应用搜索条件ss
        foreach (['Emp_Name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where($key, 'like', "%{$get[$key]}%");
            }
        }
        if (isset($get['tag']) && $get['tag'] !== '') {
            //$db->where("concat(',',tagid_list,',') like :tag", ['tag' => "%,{$get['tag']},%"]);   //mysql存在contcat内置函数
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
        $tags = Db::name('dept_info')->where('company_id', session('company_id'))->column('dept_no,dept_name');
        $this->assign('tags', $tags);
    }

}
