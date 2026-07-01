<?php

namespace App\Libraries;
use App\Models\MenuModel;

class MenuLibrarie
{
	private $menuModel;
	private $_mainMenuName;

	public function __construct(){
		$this->menuModel = model(MenuModel::class);
	}

    /**
     * メインメニュー取得
	 * @return array
     */
    public function get_main_menu(): array
    {		
        return $this->menuModel->get_main_menu();
    }

    /**
     * サブメニュー取得
	 * @param string $controllerName	コントローラー名
	 * @return array
     */
    public function get_sub_menu(string $controllerName): array
    {
        $ret = array();
        if (empty($controllerName) === false) {
            $ret = $this->menuModel->get_sub_menu($controllerName);
        }
        return $ret;
    }

    /**
     * メニュー情報取得
	 * @param string $controllerName	コントローラー名
	 * @return array
     */
    public function get_menu_info(string $controllerName) : object
    {
        $ret = (object)[];
        if (empty($controllerName) === false) {
            $ret = $this->menuModel->get_menu_info($controllerName);
        }
        return $ret;
    }
}
