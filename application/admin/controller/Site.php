<?php
namespace app\admin\controller;

use app\service\ConfigService;

/**
 * 站点设置
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Site extends Common
{
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
	}

	/**
     * [Index 配置列表]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-06T21:31:53+0800
     */
	public function Index()
	{
		// 时区
		$this->assign('site_timezone_list', lang('site_timezone_list'));

		// 站点状态
		$this->assign('site_site_state_list', lang('site_site_state_list'));

		// 是否开启用户注册
		$this->assign('site_user_reg_state_list', lang('site_user_reg_state_list'));

		// 是否开启用户登录
		$this->assign('site_user_login_state_list', lang('site_user_login_state_list'));

		// 获取验证码-开启图片验证码
		$this->assign('site_img_verify_state_list', lang('site_img_verify_state_list'));

		// 配置信息
		$this->assign('data', ConfigService::ConfigList());
		
		return $this->fetch();
	}

	/**
	 * [Save 配置数据保存]
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2017-01-02T23:08:19+0800
	 */
	public function Save()
	{
		// logo存储
		$this->FileSave('home_site_logo', 'home_site_logo_img');
		$this->FileSave('home_site_logo_wap', 'home_site_logo_wap_img');
		$this->FileSave('home_site_desktop_icon', 'home_site_desktop_icon_img');

		// 站点状态值处理
		if(!isset($_POST['home_user_reg_state']))
		{
			$_POST['home_user_reg_state'] = '';
		}

		// 基础配置
		return ConfigService::ConfigSave($_POST);
	}
}
?>