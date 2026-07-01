<?php
namespace App\Models;

class MenuModel extends BaseQueryModel {

	protected $db;

	/**
	 * メニュー情報取得
	 * @param string $controller	コントローラー名
	 * @return object
	 */ 
	public function get_menu_info(string $controller) : object
	{

		$ret = array();

		$sql = '
			SELECT
				men.menu_id,
				men.menu_name,
				men.controller
			FROM
				m_menu men
			WHERE
				men.controller = :controller:
				AND men.use_flg = :useFlg:
		';

		$bind = array(
			'controller' => $controller,
			'useFlg' => DB_FLG_ON
		);

		return $this->get_first_row($sql, $bind);
	}

	/**
	 * メインメニュー一覧情報取得
	 * @return array
	 */
	public function get_main_menu() : array
	{

		$ret = array();

		$sql = '
			SELECT
				menu_name,
				controller,
				home_only_flg
			FROM
				m_menu
			WHERE
				use_flg = :useFlg:
			ORDER BY
				order_no ASC
		';

		$bind = array(
			'useFlg' => DB_FLG_ON
		);

		return $this->get_result_array($sql, $bind);
	}

	/**
	 * サブメニュー一覧情報取得
	 * @param string $controller	コントローラー名
	 * @return array
	 */ 
	public function get_sub_menu(string $controller) : array
	{

		$ret = array();

		$sql = '
			SELECT
				mmc.category_id
                ,cat.category_name
				,tab.tab_name
			FROM
				m_menu men
                INNER JOIN m_menu_category mmc ON
                	mmc.menu_id = men.menu_id
					AND mmc.use_flg = :useFlg:
                INNER JOIN m_category cat ON
                	cat.category_id = mmc.category_id
				INNER JOIN m_menu_tab tab ON
					tab.tab_id = mmc.tab_id 
			WHERE
				men.controller = :controller:
				AND men.use_flg = :useFlg:
			ORDER BY
				mmc.order_no ASC
		';

		$bind = array(
			'controller' => $controller,
			'useFlg' => DB_FLG_ON
		);

		return $this->get_result_array($sql, $bind);
	}

}