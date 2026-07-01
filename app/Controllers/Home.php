<?php

namespace App\Controllers;
use App\Libraries\NoticeLibrarie;

class Home extends CommonController
{
    private $noticeHelper;
    protected $_useNoticeIdList = array(NOTICE_CATEGORY_ID_KASUGAI, NOTICE_CATEGORY_ID_OTHER);

	public function __construct() {
        parent::__construct();
	}

    public function index()
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

		$data = [
            'fiscalYearId' => $fiscalYearId,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'memberData' => $this->_memberData,
			'useNoticeIdList' => $this->_useNoticeIdList,
			'noticeList' => $this->noticeLibrarie->get_notice_list(),
			'officerFlg' => FLG_FALSE,
            'page' => array(),
            'headerCss' => array('home'),
            'footerJs' => array(),
		];
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view('home/index');
        echo view('common/modal');
        return view('common/footer');
    }

    /**
     * Ajax：お知らせ詳細取得
     */
    public function ajax_get_notice_detail(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'notice_info_id' => ['label' => 'お知らせID', 'rules' => 'required|integer'],
        ];
        if ($this->validate($rule) === true) {
            // データ取得
            $noticeInfoId = $this->request->getPost('notice_info_id');
            $result = $this->noticeLibrarie->get_notice_detail($noticeInfoId, $this->_memberData['member_id']);
        } else {
            $error = $this->validator->getErrors();
        }

        $ret = [
            'result' => $result,
            'error' => $error
        ];
        return json_encode($ret);
    }
}
