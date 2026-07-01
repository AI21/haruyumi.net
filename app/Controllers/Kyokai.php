<?php

namespace App\Controllers;
use App\Libraries\KyokaiLibrarie;

class Kyokai extends CommonController
{
    private $kyokaiHelper;
    protected $useNoticeIdList = array(NOTICE_CATEGORY_ID_TRAINING);

	public function __construct() {
        parent::__construct(CONTROLLER_NAME_KYOKAI);
        helper('kyokai');
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
        
        // 協会イベント登録年度一覧取得
        $SelectfiscalYearData = null;
        $kyokaiRegistNendoList = $this->kyokaiLibrarie->get_kyokai_event_regist_nendo_list($fiscalYearId, $SelectfiscalYearData);
        
		$data = [
            'fiscalYearId' => $fiscalYearId,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'memberData' => $this->_memberData,
            'kyokaiRegistNendoList' => $kyokaiRegistNendoList,
            'SelectfiscalYearData' => $SelectfiscalYearData,
			'useNoticeIdList' => $this->useNoticeIdList,
			'kyokaiEventList' => $this->kyokaiLibrarie->get_kyokai_event_list($fiscalYearId, $this->_memuData['subMenu']),
			'noticeList' => $this->noticeLibrarie->get_notice_list(NOTICE_CATEGORY_ID_TRAINING),
			'officerFlg' => FLG_FALSE,
            'page' => array(),
            'headerCss' => array('kyokai'),
            'footerJs' => array('kyokai'),
		];
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view('kyokai/index');
        echo view('common/modal');
        return view('common/footer');
    }

    /**
     * イベント詳細
     */
    public function detail($eventId)
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        $viewTemplate = '';
        $eventOfferMemberList = array();

        // イベント詳細情報取得
        $eventDetail = $this->kyokaiLibrarie->get_event_detail($eventId, $this->_memberData['member_id']);
        if ($eventDetail['organizer_flg'] == true) {
            // 協会行事参加者一覧情報取得
            $eventOfferMemberList = $this->kyokaiLibrarie->get_event_offer_member_list($eventId);
        }
        
        // 年度ID取得
        if (empty($eventDetail) === false && $eventDetail['fiscal_year_id'] !== null) {
            $fiscalYearId = $eventDetail['fiscal_year_id'];
        } else {
            $fiscalYearId = $this->_settingData->fiscal_year_id;
        }

		$data = [
            'fiscalYearId' => $fiscalYearId,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'memberData' => $this->_memberData,
			'eventDetail' => $eventDetail,
			'eventOfferMemberList' => $eventOfferMemberList,
			'officerFlg' => FLG_FALSE,
            'page' => array($eventDetail['event_name']),
            'headerCss' => array('kyokai'),
            'footerJs' => array('kyokai'),
		];
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view('kyokai/detail');
        echo view('kyokai/detail_modal');
        return view('common/footer');
    }

    /**
     * 参加申込・キャンセル登録
     */
    public function ajax_event_request(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'event_id' => ['label' => 'イベントID', 'rules' => 'required|integer'],
            'request_mode' => ['label' => '申込区分', 'rules' => 'required'],
        ];
        if ($this->validate($rule) === true) {
            // データ取得
            $eventId = $this->request->getPost('event_id');
            $requestMode = $this->request->getPost('request_mode');
            // 参加申込・キャンセル処理
        // $result = true;
            $result = $this->kyokaiLibrarie->event_request_member($eventId, $this->_session->memberData, $requestMode);
            if ($result === false) {
                $error['event'] = $this->taikaiLibrarie->_get_error_message();
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
