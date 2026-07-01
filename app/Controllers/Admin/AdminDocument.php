<?php

namespace App\Controllers\Admin;
use App\Controllers\CommonController;
use App\Libraries\Admin\AdminDocumentLibrarie;

class AdminDocument extends CommonController
{
    private $adminDocumentLibrarie;
    private $admineHelper;

	public function __construct() {
        parent::__construct(CONTROLLER_NAME_DOCUMENT);

		$this->adminDocumentLibrarie = new AdminDocumentLibrarie();
        helper('admin');
	}

    // 資料ファイル登録ページ
    public function document_file_regist()
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        // 管理役員チェック
        $officerFlg = $this->loginLibrarie->chk_officer_menu($this->_memberData, $this->_memuInfo);
        if ($officerFlg === false) {
            // 役員以外は資料メインに移動
            return redirect()->to('document');
        }

        // データ取得
        $documentData = $this->request->getPost();

        // タブ名からViewファイルを判定
        $viewFile = "";
        switch ($documentData['tab_name']) {
            case TAB_NAME_USEGYM:
                $viewFile = 'document_file_usegym_regist';
                break;
            default:
                // 不明な場合は資料メインに移動
                return redirect()->to('document');
        }

        // 2ヶ月後の日付（年月）
        $dateAdd2Month = date_format_jp(date("Ymd", strtotime("+2 months")), false, DATE_FORMAT_YM);
        
        // ページ
        $page = array();

		$data = [
            'fiscalYearId' => $this->_settingData->fiscal_year_id,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
            'noticeInfoId' => "",
            'noticeTitle' => "",
            'noticeBody' => "",
            'noticeDocumentList' => array(),
			'officerFlg' => FLG_FALSE,
            'dateAdd2Month' => $dateAdd2Month,
            'page' => $page,
            'mode' => MODE_REGIST,
            'headerCss' => array('admin/form'),
            'footerJs' => array('admin/document', 'admin/document_file'),
		];

        echo view('common/header', $data);
        echo view('common/menu');
        echo view('admin/document/' . $viewFile);
        echo view('admin/modal_admin');
        return view('common/footer');
    }

    /**
     * Ajax：資料ファイル登録確認
     */
    public function ajax_document_file_regist_conf(): string
    {
        $result = false;
        $documentFile = [];
        $ret = [];
        $error = [];

        $rule = [
            'document_file' => ['label' => '資料ファイル', 'rules' => 'uploaded[document_file]|max_size[document_file, ' . UPLOAD_FILE_MAX_SIZE . ']|ext_in[document_file,pdf]'],
            'document_category_id' => ['label' => '資料カテゴリ', 'rules' => 'required|integer'],
        ];

        $ruleMail = [
            'document_title' => ['label' => 'メールタイトル', 'rules' => 'required'],
            'document_body' => ['label' => 'メール本文', 'rules' => 'required'],
        ];

        if ($this->validate($rule) === true) {
            // エラーがなければOK
            $documentData = $this->request->getPost();
            $fileObj = $this->request->getFiles();

            // メール配信チェック
            if ((bool)$documentData['document_mail_send'] === true) {
                if ($this->validate($ruleMail) === false) {
                    $error = $this->validator->getErrors();
                    $ret = [
                        'result' => $result,
                        'error' => $error
                    ];
                    return json_encode($ret);
                }
            }

            // 資料ファイル情報
            $documentFile = array(
                'file_name' => $fileObj['document_file']->getName(),
                'file_ext_path' => get_file_ext_icon_path($fileObj['document_file']->guessExtension()),
            );
            $result = true;
        } else {
            $error = $this->validator->getErrors();
        }

        $ret = [
            'result' => $result,
            'documentFile' => $documentFile,
            'error' => $error
        ];
        return json_encode($ret);
    }

    /**
     * Ajax：資料ファイル登録処理
     */
    public function ajax_document_file_regist_proc(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        $errorMsg = "";

        $rule = [
            'document_file' => ['label' => '資料ファイル', 'rules' => 'uploaded[document_file]|max_size[document_file, ' . UPLOAD_FILE_MAX_SIZE . ']|ext_in[document_file,pdf]'],
            'document_mail_send' => ['label' => 'メール配信', 'rules' => 'required'],
        ];

        if ($this->validate($rule) === true) {
            // データ取得
            $fileObj = $this->request->getFiles();
            $documentData = $this->request->getPost();

            // 資料ファイルアップロード処理
            $result = $this->adminDocumentLibrarie->document_files_proc($fileObj['document_file'], $documentData, $this->_memberData['member_id'], $this->_settingData->fiscal_year_id);
            if ($result === false) {
                $error[] = session()->getFlashdata('msg');
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
