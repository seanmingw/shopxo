<?php
namespace app\admin\controller;

use app\service\ArticleService;
use app\service\NavigationService;
use app\service\GoodsService;

/**
 * 导航管理
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Navigation extends Common
{
	private $nav_type;

	/**
	 * 构造方法
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2016-12-03T12:39:08+0800
	 */
	public function __construct()
	{
		// 调用父类前置方法
		parent::__construct();

		// 登录校验
		$this->Is_Login();

		// 权限校验
		$this->Is_Power();

		// 导航类型
		$this->nav_type = input('nav_type', 'header');
	}

	/**
     * [Index 导航列表]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-06T21:31:53+0800
     */
	public function Index()
	{
		// 获取导航列表
		$this->assign('data_list', NavigationService::NavList(['nav_type'=>$this->nav_type]));

		// 一级分类
		$this->assign('nav_header_pid_list', NavigationService::LevelOneNav(['nav_type'=>$this->nav_type]));

		// 获取分类和文章
		$this->assign('article_list', ArticleService::ArticleCategoryList());

		// 商品分类
		$this->assign('goods_category_list', GoodsService::GoodsCategory());

		// 自定义页面
		$this->assign('customview_list', db('CustomView')->field(array('id', 'title'))->where(array('is_enable'=>1))->select());

		// 是否新窗口打开
		$this->assign('common_is_new_window_open_list', lang('common_is_new_window_open_list'));

		// 是否显示
		$this->assign('common_is_show_list', lang('common_is_show_list'));

		$this->assign('nav_type', $this->nav_type);
		return $this->fetch();
	}

	/**
     * [Save 添加/编辑]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-07T21:58:19+0800
     */
	public function Save()
	{
		// 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 开始处理
        $params = input();
        $params['nav_type'] = $this->nav_type;
        $ret = NavigationService::NavSave($params);
        return json($ret);
	}

	

	/**
	 * [Delete 删除]
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2016-12-09T21:13:47+0800
	 */
	public function Delete()
	{
		// 是否ajax请求
		if(!IS_AJAX)
		{
			return $this->error('非法访问');
		}

		// 开始处理
        $params = input();
        $ret = NavigationService::NavDelete($params);
        return json($ret);
	}

	/**
	 * [StatusUpdate 状态更新]
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2017-01-12T22:23:06+0800
	 */
	public function StatusUpdate()
	{
		// 是否ajax请求
		if(!IS_AJAX)
		{
			return $this->error('非法访问');
		}

		// 开始处理
        $params = input();
        $ret = NavigationService::NavStatusUpdate($params);
        return json($ret);
	}
}
?>