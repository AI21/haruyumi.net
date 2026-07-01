<?php

namespace App\Controllers;
use App\Libraries\SeminarLibrarie;

class Seminar extends CommonController
{
    private $seminarLibrarie;
    private $seminarHelper;
    protected $_useNoticeIdList = array(NOTICE_CATEGORY_ID_SEMINAR);
    protected $noticeCategoryId = NOTICE_CATEGORY_ID_SEMINAR;

	public function __construct() {
        parent::__construct(CONTROLLER_NAME_SEMINAR);
		$this->seminarLibrarie = new SeminarLibrarie();
        helper('seminar');
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

        // 講習会登録年度一覧取得
        $SelectfiscalYearData = null;
        $seminarRegistNendoList = $this->seminarLibrarie->get_seminar_regist_nendo_list($fiscalYearId, $SelectfiscalYearData);
        
		$data = [
            'fiscalYearId' => $fiscalYearId,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'memberData' => $this->_memberData,
			'useNoticeIdList' => $this->_useNoticeIdList,
			'noticeCategoryId' => $this->noticeCategoryId,
            'seminarRegistNendoList' => $seminarRegistNendoList,
            'SelectfiscalYearData' => $SelectfiscalYearData,
			'seminarList' => $this->seminarLibrarie->get_seminar_list($fiscalYearId, $this->_memuData['subMenu']),
			'noticeList' => $this->noticeLibrarie->get_notice_list($this->noticeCategoryId),
			'officerFlg' => FLG_FALSE,
            'page' => array(),
            'headerCss' => array('seminar'),
            'footerJs' => array('seminar'),
		];
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view('seminar/index');
        echo view('common/modal');
        return view('common/footer');
    }

    /**
     * 講習会詳細
     */
    public function detail($seminarId)
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        // 講習会情報取得
        $seminarDetail = $this->seminarLibrarie->get_seminar_detail($seminarId);
        
        // 年度ID取得
        if (empty($seminarDetail) === false && $seminarDetail['fiscal_year_id'] !== null) {
            $fiscalYearId = $seminarDetail['fiscal_year_id'];
        } else {
            $fiscalYearId = $this->_settingData->fiscal_year_id;
        }
        
		$data = [
            'fiscalYearId' => $fiscalYearId,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'seminarDetail' => $seminarDetail,
			'officerFlg' => FLG_FALSE,
            'page' => array($seminarDetail['seminar_sub_name']),
            'headerCss' => array(),
            'footerJs' => array('seminar'),
		];
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view('seminar/detail');
        echo view('seminar/detail_modal');
        return view('common/footer');
    }
}
