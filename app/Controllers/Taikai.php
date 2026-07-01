<?php

namespace App\Controllers;

class Taikai extends CommonController
{
    private $taikaiHelper;
    protected $_useNoticeIdList = array(NOTICE_CATEGORY_ID_TAIKAI);
    protected $noticeCategoryId = NOTICE_CATEGORY_ID_TAIKAI;

	public function __construct() {
        parent::__construct(CONTROLLER_NAME_TAIKAI);
        helper('taikai');
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

        // 大会登録年度一覧取得
        $SelectfiscalYearData = null;
        $taikaiRegistNendoList = $this->taikaiLibrarie->get_taikai_regist_nendo_list($fiscalYearId, $SelectfiscalYearData);
        
		$data = [
            'fiscalYearId' => $fiscalYearId,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'memberData' => $this->_memberData,
			'useNoticeIdList' => $this->_useNoticeIdList,
			'noticeCategoryId' => $this->noticeCategoryId,
            'taikaiRegistNendoList' => $taikaiRegistNendoList,
            'SelectfiscalYearData' => $SelectfiscalYearData,
			'taikaiList' => $this->taikaiLibrarie->get_taikai_list($fiscalYearId, $this->_memuData['subMenu']),
			'noticeList' => $this->noticeLibrarie->get_notice_list($this->noticeCategoryId),
			'officerFlg' => FLG_FALSE,
            'page' => array(),
            'headerCss' => array(),
            'footerJs' => array('navi', 'taikai'),
		];
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view('taikai/index');
        echo view('common/modal');
        return view('common/footer');
    }

    /**
     * 大会詳細
     */
    public function detail($taikaiId)
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        $viewTemplate = 'taikai/detail';
        $memberList = array();
        $taikaiOfferMemberList = array();
        $headerCss = array();
        $footerJs = array('taikai');

        // 大会情報取得
        $taikaiDetail = $this->taikaiLibrarie->get_taikai_detail($taikaiId, $this->_memberData['member_id']);
        if (empty($taikaiDetail) === false && $taikaiDetail['kasugai_flg'] === DB_FLG_ON) {
            // 春日井弓道会主催の場合はViewファイルを切り替え
            $viewTemplate = 'taikai/detail_kasugai';
        }

        // 大会関連お知らせ一覧情報取得
        $taikaiDetail['relation_notice_list'] = $this->noticeLibrarie->get_relation_notice_list(MENU_ID_TAIKAI, $taikaiId);
        
        // 年度ID取得
        if (empty($taikaiDetail) === false && $taikaiDetail['fiscal_year_id'] !== null) {
            $fiscalYearId = $taikaiDetail['fiscal_year_id'];
        } else {
            $fiscalYearId = $this->_settingData->fiscal_year_id;
        }

        // 管理役員チェック
        // $officerFlg = $this->loginLibrarie->chk_officer_menu_category($this->_memberData, $this->_memuInfo, $taikaiDetail['category_id']);
        // if ($officerFlg === true) {
        $officerFlg = FLG_FALSE;
        if ($taikaiDetail['officer_level'] !== null) {
            $officerFlg = FLG_TRUE;
            // 会員一覧情報取得
            $memberList = $this->memberLibrarie->get_member_list($this->_settingData->fiscal_year_id);
            // 参加者一覧情報取得
            $taikaiOfferMemberList = $this->taikaiLibrarie->get_taikai_offer_member_list($taikaiId);
            array_push($headerCss, 'admin/form');
            array_push($headerCss, 'admin/common');
            array_push($footerJs, 'admin/taikai');
        }
        
		$data = [
            'fiscalYearId' => $fiscalYearId,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'noticeCategoryId' => $this->noticeCategoryId,
			'officerFlg' => $officerFlg,
			'taikaiDetail' => $taikaiDetail,
            'memberList' => $memberList,
			'taikaiOfferMemberList' => $taikaiOfferMemberList,
            'page' => array($taikaiDetail['taikai_name']),
            'headerCss' => $headerCss,
            'footerJs' => $footerJs,
		];
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view($viewTemplate);
        echo view('taikai/detail_modal');
        if ($officerFlg === true) {
            echo view('admin/taikai/modal_proxy');
        }
        echo view('common/modal');
        return view('common/footer');
    }

    /**
     * 大会参加・キャンセル登録
     */
    public function ajax_taikai_request(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'taikai_id' => ['label' => '大会ID', 'rules' => 'required|integer'],
            'request_mode' => ['label' => '申込区分', 'rules' => 'required'],
        ];
        if ($this->validate($rule) === true) {
            // データ取得
            $taikaiId = $this->request->getPost('taikai_id');
            $requestMode = $this->request->getPost('request_mode');
            // 大会参加・キャンセル処理
            $result = $this->taikaiLibrarie->taikai_request_member($taikaiId, $this->_session->memberData, $requestMode);
            if ($result === false) {
                $error['taikai'] = $this->taikaiLibrarie->_get_error_message();
            }
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
