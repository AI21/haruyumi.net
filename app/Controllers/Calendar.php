<?php

namespace App\Controllers;
use App\Libraries\CalendarLibrarie;

class Calendar extends CommonController
{
	public function __construct() {
        parent::__construct(CONTROLLER_NAME_CALENDAR);
        helper('calendar');
	}

    public function index()
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }
        // 年度切り替え
        // if (empty($fiscalYearId) === true) {
        //     $fiscalYearId = $this->_fiscalYearId;
        // }

        // ページ
        $page = array();
        
		$data = [
            'fiscalYearId' => $this->_settingData->fiscal_year_id,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'documentList' => $this->calendarLibrarie->get_document_list(),
			'officerFlg' => FLG_FALSE,
            'page' => array(),
            'headerCss' => array(),
            'footerJs' => array(),
		];
		// print nl2br(print_r($data,1));
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view('calendar/index');
        return view('common/footer');
    }
}
