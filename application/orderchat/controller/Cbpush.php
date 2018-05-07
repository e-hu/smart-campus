<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\DataService;
use service\LogService;
use think\Db;

/**
 * 菜谱推送管理控制器
 * Class Company
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class Cbpush extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'cookbook_recommend_detail';

    /**
     * 菜谱推送列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '菜谱推送列表管理';
        $this->assign('title', $this->title);
        $tags = Db::name('dinner_base_info')->where('company_id', session('company_id'))->column('dinner_flag,dinner_name');
        $company_lists = Db::name('company_list')->where('status', 1)->column('company_id,Company_Name');
        $weeks = Db::name('week_day_list')->column('id,week_num,start_datetime,end_datetime');
        $this->assign('tags', $tags);
        $this->assign('weeks', $weeks);
        $this->assign('company_lists', $company_lists);

        $list = [];
        // 应用搜索条件
        $get = $this->request->get();
        if ($this->request->get() && isset($_GET['company_id']) && isset($_GET['dinner_flag']) && isset($_GET['week_id'])) {
            if ($get['company_id'] == '' || $get['dinner_flag'] == '' || $get['week_id'] == '') {
                $this->error('条件不正确');
            }
            $weekinfo = Db::name('week_day_list')->where(['id'=>$get['week_id']])->find();
            $sqlstr = "exec [up_create_recommend_detail] ?,?,?,?,?";
            $result = Db::query($sqlstr, [$weekinfo['year'], $weekinfo['week_num'], $_GET['company_id'], $_GET['dinner_flag'],'1']);
            $list = $result[0];
        }
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 推荐菜谱生成
     */
    public function add()
    {
        LogService::write('订餐管理', '执行推荐菜谱生成操作');
        $tags = Db::name('dinner_base_info')->where('company_id', session('company_id'))->column('dinner_flag,dinner_name');
        $company_lists = Db::name('company_list')->where('status', 1)->column('company_id,Company_Name');
        $weeks = Db::name('week_day_list')->column('id,week_num,start_datetime,end_datetime');
        $cbinfos = Db::name('cookbook_base_info')->where('company_id', session('company_id'))->column('cookbook_no,cookbook_name');
        $this->assign('tags', $tags);
        $this->assign('weeks', $weeks);
        $this->assign('company_lists', $company_lists);
        $this->assign('cbinfos', $cbinfos);
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if ($post['company_id'] == '' || $post['dinner_flag'] == '' || $post['week_id'] == '') {
                $this->error('条件不正确');
            }
            $weekinfo = Db::name('week_day_list')->where(['id'=>$post['week_id']])->find();
            $where['a.company_id'] = $post['company_id'];
            $where['a.week_num'] = $weekinfo['week_num'];
            $where['a.dinner_flag'] = $post['dinner_flag'];
            $sqlstr = "exec [up_create_recommend_detail] ?,?,?,?,?";
            $result = Db::query($sqlstr, [$weekinfo['year'], $weekinfo['week_num'], $post['company_id'], $post['dinner_flag'],'1']);
            if (!empty($result)) {
                $list = Db::name($this->table)
                    ->alias('a')
                    ->field('a.*,cookbook_name,dinner_name')
                    ->join('cookbook_base_info c', 'a.cookbook_no = c.cookbook_no and a.company_id = c.company_id', 'left')
                    ->join('dinner_base_info b', 'a.dinner_flag = b.dinner_flag and a.company_id = b.company_id', 'left')
                    ->where($where)
                    ->order('week_no asc')
                    ->select();
            } else {
                $this->error('数据错误');
            }
            $this->assign('company_id', $post['company_id']);
            $this->assign('dinner_flag', $post['dinner_flag']);
            $this->assign('week_id', $post['week_id']);
            $this->assign('list', $list);
            return $this->fetch('cbpush/form');
        } else {
            return $this->_form('', 'form');
        }
    }

    /**
     * 推荐菜谱编辑
     */
    public function edit()
    {
        LogService::write('订餐管理', '执行推荐菜谱编辑操作');
        if ($_GET['company_id'] == '' || $_GET['dinner_flag'] == '' || $_GET['week_id'] == '') {
            $this->error('条件不正确');
        }
        $dinner_info = Db::name('dinner_base_info')->where(['company_id'=>session('company_id'),'dinner_flag'=>$_GET['dinner_flag']])->find();
        $company_info = Db::name('company_list')->where(['status'=> 1,'company_id'=>$_GET['company_id']])->find();
        $week_info = Db::name('week_day_list')->where(['id'=>$_GET['week_id']])->find();
        $cbinfos = Db::name('cookbook_base_info')->where('company_id', session('company_id'))->column('cookbook_no,cookbook_name');
        $where['a.company_id'] = $_GET['company_id'];
        $where['a.week_num'] = $week_info['week_num'];
        $where['a.dinner_flag'] = $_GET['dinner_flag'];
        $list = Db::name($this->table)
            ->alias('a')
            ->field('a.*,cookbook_name,dinner_name')
            ->join('cookbook_base_info c', 'a.cookbook_no = c.cookbook_no and a.company_id = c.company_id', 'left')
            ->join('dinner_base_info b', 'a.dinner_flag = b.dinner_flag and a.company_id = b.company_id', 'left')
            ->where($where)
            ->order('week_no asc')
            ->select();
        $this->assign('list', $list);
        $this->assign('dinner_name', $dinner_info['dinner_name']);
        $this->assign('week_info', $week_info);
        $this->assign('company_name', $company_info['Company_Name']);
        $this->assign('cbinfos', $cbinfos);
        return $this->_form('', 'editform');
    }

    /**
     * 绑定菜谱
     */
    public function save()
    {
        LogService::write('订餐管理', '执行绑定菜谱操作');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $result = DataService::save($this->table, $post, 'id');
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
