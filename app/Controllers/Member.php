<?php

namespace App\Controllers;

class Member extends CommonController
{
	public function __construct() {
        parent::__construct(CONTROLLER_NAME_MEMBER);
        helper('member');
	}

    public function index($fiscalYearId=NULL)
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }
        
        // 年度切り替え
        if (empty($fiscalYearId) === true) {
            $fiscalYearId = $this->_settingData->fiscal_year_id;
        }
        
        // 会員登録年度一覧取得
        $SelectfiscalYearData = null;
        $memberRegistNendoList = $this->memberLibrarie->get_member_regist_nendo_list($fiscalYearId, $SelectfiscalYearData);

		$data = [
            'fiscalYearId' => $fiscalYearId,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'memberData' => $this->_memberData,
            'memberRegistNendoList' => $memberRegistNendoList,
            'SelectfiscalYearData' => $SelectfiscalYearData,
			'memberList' => $this->memberLibrarie->get_member_list($fiscalYearId),
            'memberListFile' => $this->memberLibrarie->get_member_list_file(),
			'officerFlg' => FLG_FALSE,
            'page' => array(),
            'headerCss' => array('member'),
            'footerJs' => array('member'),
		];
		// print nl2br(print_r($this->memuData,1));
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view('member/index');
        return view('common/footer');
    }
}
