<?php

namespace App\Controllers;
use App\Libraries\DocumentLibrarie;

class Document extends CommonController
{
    private $documentLibrarie;
    private $documentHelper;

	public function __construct() {
        parent::__construct(CONTROLLER_NAME_DOCUMENT);
		$this->documentLibrarie = new DocumentLibrarie();
        helper('document');
	}

    public function index()
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }
        
        $headerCss = array();
        $footerJs = array();

        // 管理役員チェック
        $officerFlg = $this->loginLibrarie->chk_officer_menu($this->_memberData, $this->_memuInfo);
        if ($officerFlg === true) {
            array_push($footerJs, 'admin/document');
        }
        
		$data = [
            'fiscalYearId' => $this->_settingData->fiscal_year_id,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'documentList' => $this->documentLibrarie->get_document_list($this->_memuData['subMenu'], $this->_memberData['member_id']),
			'officerFlg' => $officerFlg,
            'page' => array(),
            'headerCss' => $headerCss,
            'footerJs' => $footerJs,
		];
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view('document/index');
        return view('common/footer');
    }
}
