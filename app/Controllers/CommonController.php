<?php

namespace App\Controllers;
use App\Libraries\CommonLibrarie;
use App\Libraries\LoginLibrarie;
use App\Libraries\MenuLibrarie;
use App\Libraries\MemberLibrarie;
use App\Libraries\NoticeLibrarie;
use App\Libraries\KyokaiLibrarie;
use App\Libraries\TaikaiLibrarie;
use App\Libraries\ShinsaLibrarie;
use App\Libraries\CalendarLibrarie;

class CommonController extends BaseController
{
    protected $commonLibrarie;
    protected $loginLibrarie;
    protected $menuLibrarie;
    protected $memberLibrarie;
    protected $noticeLibrarie;
    protected $kyokaiLibrarie;
    protected $taikaiLibrarie;
    protected $shinsaLibrarie;
    protected $calendarLibrarie;

    protected $_settingData;
    protected $_memberData;
    protected $_fiscalYearId;
    protected $_memuInfo;
    protected $_memuData;
    protected $_controllerName;
    protected $_useNoticeIdList = [];

	public function __construct($controllerName='') {
        $this->_session = session();
		$this->commonLibrarie = new CommonLibrarie();
		$this->loginLibrarie = new LoginLibrarie();
		$this->menuLibrarie = new MenuLibrarie();
		$this->memberLibrarie = new MemberLibrarie();
		$this->noticeLibrarie = new NoticeLibrarie();
		$this->kyokaiLibrarie = new KyokaiLibrarie();
        $this->taikaiLibrarie = new TaikaiLibrarie();
        $this->shinsaLibrarie = new ShinsaLibrarie();
        $this->calendarLibrarie = new CalendarLibrarie();
        
        $this->get_controller_name();
        $this->get_setting();
        $this->get_member_data();
        $this->get_menu_info($controllerName);
        $this->get_menu_list($controllerName);
        helper('notice');
	}

	/**
	 * コントローラー名取得
	 * @return void
	 */
    public function get_controller_name(): void
    {
        $router = \Config\Services::router();
        $controllerName = $router->controllerName();
        $this->_controllerName = strtolower(str_replace(APP_CONTROLLER, "", $controllerName));
    }

	/**
	 * 設定情報取得
	 * @return void
	 */
    public function get_setting(): void
    {
        $this->_settingData = $this->commonLibrarie->get_setting_data();
    }

	/**
	 * 設定情報取得
	 * @return void
	 */
    public function get_member_data(): void
    {
        
        $memberData = $this->_session->get('memberData');
        if (empty($memberData['member_id']) === false) {
            $memberDataObj = $this->loginLibrarie->get_member_data($memberData['member_id']);
            $memberData['kasugai_regist_flg'] = $memberDataObj->kasugai_regist_flg;
            $memberData['kasugai_regist_date'] = $memberDataObj->kasugai_regist_date;
            $memberData['renmei_adjourning_flg'] = $memberDataObj->renmei_adjourning_flg;
            $memberData['aiti_renmei_regist_flg'] = $memberDataObj->aiti_renmei_regist_flg;
            $memberData['notice_send_flg'] = $memberDataObj->notice_send_flg;
            $memberData['mail_address'] = $memberDataObj->mail_address;
            $memberData['member_admin_flg'] = $memberDataObj->member_admin_flg;

            // メニュー管理リスト取得
            $kyokaiOfferMenuData = $this->loginLibrarie->get_officer_menu_id_list($memberData['member_id']);
            $memberData['officer_menu_id_list'] = $kyokaiOfferMenuData;

            // メニューカテゴリー管理リスト取得
            $kyokaiOfferCategoryData = $this->loginLibrarie->get_officer_category_id_list($memberData['member_id']);
            $memberData['officer_category_id_list'] = $kyokaiOfferCategoryData;

            // お知らせ管理IDリスト取得
            $noticeAdminIdList = $this->loginLibrarie->get_notice_admin_id_list($memberData['member_id']);
            $memberData['notice_admin_id_list'] = $noticeAdminIdList;

            // お知らせ投稿権限確認
            $memberData['notice_posting_flg'] = false;
            $intersectList = array_intersect($this->_useNoticeIdList, $noticeAdminIdList);
            if (empty($intersectList) === false) {
                $memberData['notice_posting_flg'] = true;
            }

        }
        $this->_memberData = $memberData;
    }

	/**
	 * メニュー情報取得
	 * @param string $controllerName	コントローラー名
	 * @return void
	 */
    public function get_menu_info(string $controllerName): void
    {
        $this->_memuInfo = $this->menuLibrarie->get_menu_info($controllerName);
    }

	/**
	 * メニュー一覧情報取得
	 * @param string $controllerName	コントローラー名
	 * @return void
	 */
    public function get_menu_list(string $controllerName): void
    {
        $this->_memuData = [
            'mainMenu' => $this->menuLibrarie->get_main_menu(),
            'subMenu' => $this->menuLibrarie->get_sub_menu($controllerName),
            'controllerName' => $this->_controllerName
        ];
    }
}
