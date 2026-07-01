<?php

namespace App\Controllers;
use App\Libraries\NoticeLibrarie;

class Shinsa extends CommonController
{
    private $shinsaHelper;
    protected $_useNoticeIdList = array(NOTICE_CATEGORY_ID_SHINSA);
    protected $noticeCategoryId = NOTICE_CATEGORY_ID_SHINSA;

    public function __construct() {
        parent::__construct(CONTROLLER_NAME_SHINSA);
        helper('shinsa');
        helper('member');
    }

    public function index($fiscalYearId=NULL)
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        // 管理役員チェック
        $officerFlg = $this->loginLibrarie->chk_officer_menu($this->_memberData, $this->_memuInfo);

        // 年度切り替え
        if (empty($fiscalYearId) === true) {
            $fiscalYearId = $this->_settingData->fiscal_year_id;
        }

        // 審査登録年度一覧取得
        $SelectfiscalYearData = null;
        $shinsaRegistNendoList = $this->shinsaLibrarie->get_shinsa_regist_nendo_list($fiscalYearId, $SelectfiscalYearData);

        // 審査一覧取得
        $shinsaList = $this->shinsaLibrarie->get_shinsa_list($fiscalYearId, $this->_memuData['subMenu'], $this->_memberData['member_id']);

        // お知らせ一覧取得
        $noticeList = $this->noticeLibrarie->get_notice_list($this->noticeCategoryId);
        
		$data = [
            'fiscalYearId' => $fiscalYearId,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'memberData' => $this->_memberData,
            'shinsaRegistNendoList' => $shinsaRegistNendoList,
            'SelectfiscalYearData' => $SelectfiscalYearData,
			'useNoticeIdList' => $this->_useNoticeIdList,
			'noticeCategoryId' => $this->noticeCategoryId,
			'shinsaList' => $shinsaList,
			'noticeList' => $noticeList,
			'officerFlg' => $officerFlg,
            'page' => array(),
            'headerCss' => array('shinsa'),
            'footerJs' => array('shinsa'),
		];
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view('shinsa/index');
        echo view('common/modal');
        return view('common/footer');
    }

    /**
     * 審査詳細
     */
    public function detail($shinsaId)
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        $viewTemplate = '';
        $memberList = array();
        $shinsaOfferMemberList = array();
        $shinsaTargetList = array();
        $headerCss = array('shinsa');
        $footerJs = array('shinsa');

        // 審査情報取得
        $shinsaDetail = $this->shinsaLibrarie->get_shinsa_detail($shinsaId, $this->_memberData['member_id']);

        // 審査関連お知らせ一覧情報取得
        $shinsaDetail['relation_notice_list'] = $this->noticeLibrarie->get_relation_notice_list(MENU_ID_SHINSA, $shinsaId);
        
        // 年度ID取得
        if (empty($shinsaDetail) === false && $shinsaDetail['fiscal_year_id'] !== null) {
            $fiscalYearId = $shinsaDetail['fiscal_year_id'];
        } else {
            $fiscalYearId = $this->_settingData->fiscal_year_id;
        }

        // 段位・連盟登録情報取得
        $memberGradeDeta = $this->shinsaLibrarie->get_member_grade_data($this->_memberData['member_id']);

        // 審査申込対象一覧情報取得
        $shinsaTarget = $this->shinsaLibrarie->get_shinsa_target_list($shinsaDetail, $memberGradeDeta);

        // 管理役員チェック
        $officerFlg = $this->loginLibrarie->chk_officer_menu_category($this->_memberData, $this->_memuInfo, $shinsaDetail['category_id']);
        if ($officerFlg === true) {
            // 会員一覧情報取得
            $memberList = $this->memberLibrarie->get_member_list($this->_settingData->fiscal_year_id);
            // 審査申請者一覧情報取得
            $shinsaOfferMemberList = $this->shinsaLibrarie->get_shinsa_offer_member_list($shinsaId, $shinsaDetail);
            // 審査申請者の代理登録用の審査種別情報を取得
            $shinsaTargetList = $this->shinsaLibrarie->get_shinsa_target_all_list($shinsaDetail['date_holder_grade']);

            array_push($headerCss, 'admin/form');
            array_push($headerCss, 'admin/common');
            array_push($footerJs, 'admin/shinsa');
        }

        // 審査クラスでViewファイルを切り替え
        if (empty($shinsaDetail) === false) {
            switch ($shinsaDetail['shinsa_class_id']) {
                case SHINSA_CLASS_ID_CHUOU :
                    $viewTemplate = 'shinsa/detail_chuou';
                    break;
                case SHINSA_CLASS_ID_RENGO :
                    $viewTemplate = 'shinsa/detail_rengo';
                    break;
                case SHINSA_CLASS_ID_CHIHO :
                    $viewTemplate = 'shinsa/detail_chiho';
                    break;
                case SHINSA_CLASS_ID_VIDEO :
                    $viewTemplate = 'shinsa/detail_chiho';
                    break;
            }
        }

		$data = [
            'fiscalYearId' => $fiscalYearId,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'memberData' => $this->_memberData,
			'memberGradeDeta' => $memberGradeDeta,
			'shinsaDetail' => $shinsaDetail,
			'shinsaTarget' => $shinsaTarget,
			'officerFlg' => $officerFlg,
            'memberList' => $memberList,
			'shinsaOfferMemberList' => $shinsaOfferMemberList,
            'shinsaTargetList' => $shinsaTargetList,
			'noticeCategoryId' => $this->noticeCategoryId,
            'page' => array($shinsaDetail['shinsa_name']),
            'headerCss' => $headerCss,
            'footerJs' => $footerJs,
		];
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view($viewTemplate);
        echo view('shinsa/detail_modal');
        if ($officerFlg === true) {
            echo view('admin/shinsa/modal_proxy');
        }
        echo view('common/modal');
        return view('common/footer');
    }

    /**
     * 審査申込・キャンセル登録
     */
    public function ajax_shinsa_request(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'shinsa_id' => ['label' => '審査ID', 'rules' => 'required|integer'],
            'shinsa_target_id' => ['label' => '審査対象ID', 'rules' => 'required|integer'],
            'request_mode' => ['label' => '申込区分', 'rules' => 'required'],
        ];
        if ($this->validate($rule) === true) {
            // データ取得
            $shinsaId = $this->request->getPost('shinsa_id');
            $shinsaTargetId = $this->request->getPost('shinsa_target_id');
            $requestMode = $this->request->getPost('request_mode');
            // 審査申込・キャンセル処理
            $result = $this->shinsaLibrarie->shinsa_request_member($shinsaId, $shinsaTargetId, $this->_session->memberData, $requestMode);
            if ($result === false) {
                $error['shinsa'] = $this->shinsaLibrarie->_get_error_message();
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

    /**
     * 審査結果登録
     */
    public function ajax_shinsa_result_report(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'shinsa_id' => ['label' => '審査ID', 'rules' => 'required|integer'],
            'shinsa_target_id' => ['label' => '審査対象ID', 'rules' => 'required|integer'],
            'result_flg' => ['label' => '審査結果', 'rules' => 'required|integer'],
        ];
        if ($this->validate($rule) === true) {
            // データ取得
            $shinsaId = $this->request->getPost('shinsa_id');
            $shinsaTargetId = $this->request->getPost('shinsa_target_id');
            $resultFlg = $this->request->getPost('result_flg');
            // 審査結果登録処理
            $result = $this->shinsaLibrarie->shinsa_result_report($shinsaId, $shinsaTargetId, $resultFlg, $this->_session->memberData);
            if ($result === false) {
                $error['shinsa'] = $this->shinsaLibrarie->_get_error_message();
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
