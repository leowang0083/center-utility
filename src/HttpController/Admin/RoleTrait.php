<?php

namespace WonderGame\EsUtility\HttpController\Admin;

use EasySwoole\ORM\AbstractModel;

trait RoleTrait
{
	protected function _search()
	{
		$where = [];
		if ( ! empty($this->get['name'])) {
			$where['name'] = ["%{$this->get['name']}%", 'like'];
		}
		if (isset($this->get['status']) && $this->get['status'] !== '') {
			$where['status'] = $this->get['status'];
		}
		return $where;
	}
	
	protected function _afterIndex($items, $total)
	{
		// 处理超级管理员菜单权限
		/** @var AbstractModel $Menu */
		$Menu = model('Menu');
		$allMenu = $Menu->column('id');
		
		foreach ($items as $key => &$val) {
			if ($val instanceof AbstractModel) {
				$val = $val->toArray();
			}
			if (is_super($val['id'])) {
				$val['menu'] = $allMenu;
			} else {
				if (is_string($val['menu'])) {
					$val['menu'] = explode(',', $val['menu']);
				}
				// 转int
				$val['menu'] = array_map('intval', $val['menu']);
				// 过滤0值(数据库menu字段默认值)
				$val['menu'] = array_filter($val['menu']);
			}
		}
		return parent::_afterIndex($items, $total);
	}
}
