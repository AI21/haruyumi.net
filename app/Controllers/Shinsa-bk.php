<?php

namespace App\Controllers;
use App\Libraries\ShinsaLibrarie;
use App\Libraries\NoticeLibrarie;

class Shinsa extends CommonController
{
    private $shinsaLibrarie;
    private $shinsaHelper;
    protected $_useNoticeIdList = array(NOTICE_CATEGORY_ID_SHINSA);
    protected $selectNoticeId = NOTICE_CATEGORY_ID_SHINSA;

    public function __construct() {
        parent::__construct(CONTROLLER_NAME_SHINSA);
        $this->shinsaLibrarie = model(ShinsaLibrarie::class);
        helper('shinsa');
    }

    public function index()
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
        
		$data = [
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'memberData' => $this->_memberData,
			'useNoticeIdList' => $this->_useNoticeIdList,
			'selectNoticeId' => $this->selectNoticeId,
			'shinsaList' => $this->shinsaLibrarie->get_shinsa_list($fiscalYearId, $this->_memuData['subMenu']),
			'noticeList' => $this->noticeLibrarie->get_notice_list(NOTICE_CATEGORY_ID_SHINSA),
			'officerFlg' => $officerFlg,
            'page' => array(),
            'headerCss' => array('shinsa'),
            'footerJs' => array(),
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
        $shinsaOfferMemberList = array();

        // 審査情報取得
        $shinsaDetail = $this->shinsaLibrarie->get_shinsa_detail($shinsaId, $this->_memberData['member_id']);

        // 段位・連盟登録情報取得
        $memberGradeDeta = $this->shinsaLibrarie->get_member_grade_data($this->_memberData['member_id']);

        // 審査申込対象一覧情報取得
        $shinsaTarget = $this->shinsaLibrarie->get_shinsa_target_list($shinsaDetail, $memberGradeDeta);

        // 管理役員チェック
        $officerFlg = $this->loginLibrarie->chk_officer_menu_category($this->_memberData, $this->_memuInfo, $shinsaDetail['category_id']);
        if ($officerFlg === true) {
            // 審査申請者一覧情報取得
            $shinsaOfferMemberList = $this->shinsaLibrarie->get_shinsa_offer_member_list($shinsaId);
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
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'memberData' => $this->_memberData,
			'memberGradeDeta' => $memberGradeDeta,
			'shinsaDetail' => $shinsaDetail,
			'shinsaTarget' => $shinsaTarget,
			'officerFlg' => $officerFlg,
			'shinsaOfferMemberList' => $shinsaOfferMemberList,
            'page' => array($shinsaDetail['shinsa_name']),
            'headerCss' => array('shinsa'),
            'footerJs' => array('shinsa'),
		];
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view($viewTemplate);
        echo view('shinsa/detail_modal');
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
                $error['shinsa'] = $this->taikaiLibrarie->_get_error_message();
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
