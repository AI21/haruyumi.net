<?php

namespace App\Controllers\Admin;
use App\Controllers\CommonController;
use App\Libraries\Admin\AdminLibrarie;
use App\Libraries\Admin\AdminNoticeLibrarie;

class AdminNotice extends CommonController
{
    private $adminLibrarie;
    private $adminNoticeLibrarie;
    private $admineHelper;

	public function __construct() {
        parent::__construct();

		$this->adminLibrarie = new AdminLibrarie();
		$this->adminNoticeLibrarie = new AdminNoticeLibrarie();
        helper('admin');
        helper('Admin/notice');
	}

    // お知らせ投稿
    public function notice_regist($noticeCategoryId=null, $relationEventId=null)
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

        // お知らせ管理者チェック
        $noticeAdminIdList = $this->_memberData['notice_admin_id_list'];
        if (empty($noticeAdminIdList) === true) {
            // お知らせ管理ページがない場合はメインに移動
            return redirect()->to('home');
        }

        // 関連イベントの添付資料取得
        $relationDocumentList = array();
        if (empty($relationEventId) === false) {
            $relationDocumentList = $this->adminNoticeLibrarie->get_relation_document_list($noticeCategoryId, $relationEventId);
        }

        // ページ
        $page = array();

		$data = [
            'fiscalYearId' => $fiscalYearId,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
            'categoryNoticeList' => $this->noticeLibrarie->get_category_notice_list($noticeAdminIdList),
            'noticeInfoId' => "",
            'noticeCategoryId' => $noticeCategoryId,
            'noticeTitle' => "",
            'noticeBody' => "",
            'noticeDocumentList' => $relationDocumentList,
            'relationEventId' => $relationEventId,
            'relationDocumentList' => $relationDocumentList,
			'officerFlg' => FLG_FALSE,
            'uploadFileNum' => UPLOAD_FILE_NUM,
            'page' => $page,
            'mode' => MODE_REGIST,
            'headerCss' => array('admin/form'),
            'footerJs' => array('admin/notice'),
		];

        echo view('common/header', $data);
        echo view('common/menu');
        echo view('admin/notice/notice_regist');
        echo view('common/modal');
        echo view('admin/modal_admin');
        return view('common/footer');
    }

    // お知らせ更新
    public function notice_revision()
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        // お知らせ管理者チェック
        $noticeAdminIdList = $this->_memberData['notice_admin_id_list'];
        if (empty($noticeAdminIdList) === true) {
            // お知らせ管理ページがない場合はメインに移動
            return redirect()->to('home');
        }

        // データ取得
        $noticeData = $this->request->getPost();
        
        $noticeInfoId = null;
        $noticeCategoryId = null;
        $noticeCategoryName = null;
        $noticeTitle = null;
        $noticeBody = null;
        $relationEventId = null;
        $noticeDocumentList = array();

        // お知らせ投稿情報を取得
        $noticeDetail = $this->noticeLibrarie->get_notice_detail($noticeData['notice_info_id'], $this->_memberData['member_id']);
        if (empty($noticeDetail['notice_detail']) === false) {
            if ($noticeDetail['notice_detail']['modified_flg'] !== DB_FLG_ON) {
                // お知らせ管理更新権限がない場合はメインに移動
                return redirect()->to('home');
            }
            $noticeInfoId = $noticeDetail['notice_detail']['notice_info_id'];
            $noticeCategoryName = $noticeDetail['notice_detail']['notice_category_name'];
            $noticeCategoryId = $noticeDetail['notice_detail']['notice_category_id'];
            $noticeTitle = $noticeDetail['notice_detail']['notice_title'];
            $noticeBody = $noticeDetail['notice_detail']['notice_body'];
            $relationEventId = $noticeDetail['notice_detail']['relation_event_id'];
        }
        if (empty($noticeDetail['notice_document_list']) === false) {
            $noticeDocumentList = $noticeDetail['notice_document_list'];
        }
        
        // ページ
        $page = array();

		$data = [
            'fiscalYearId' => $this->_settingData->fiscal_year_id,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
            'categoryNoticeList' => $this->noticeLibrarie->get_category_notice_list($noticeAdminIdList),
            'noticeInfoId' => $noticeInfoId,
            'noticeCategoryName' => $noticeCategoryName,
            'noticeCategoryId' => $noticeCategoryId,
            'noticeTitle' => $noticeTitle,
            'noticeBody' => $noticeBody,
            'relationEventId' => $relationEventId,
            'noticeDocumentList' => $noticeDocumentList,
			'officerFlg' => FLG_FALSE,
            'uploadFileNum' => UPLOAD_FILE_NUM,
            'page' => $page,
            'mode' => MODE_REVISION,
            'headerCss' => array('admin/form'),
            'footerJs' => array('admin/notice'),
		];

        echo view('common/header', $data);
        echo view('common/menu');
        echo view('admin/notice/notice_regist');
        echo view('common/modal');
        echo view('admin/modal_admin');
        return view('common/footer');
    }

    /**
     * Ajax：お知らせ登録確認
     */
    public function ajax_notice_regist_conf(): string
    {
        $result = false;
        $postData = array();
        $uploadFiles = [];
        $existingDocumentList = array();
        $ret = [];
        $error = [];
        $existingNoticeDocumentList = [];

        $rule = [
            'notice_category_id' => ['label' => 'カテゴリー', 'rules' => 'required'],
            'notice_title' => ['label' => 'タイトル', 'rules' => 'required'],
            'notice_body' => ['label' => '本文', 'rules' => 'required'],
            'regist_mode' => ['label' => 'モード', 'rules' => 'required'],
        ];

        $fileList = $this->request->getFiles();
        $fileCnt = count($fileList);
        for ($i=1; $i<=$fileCnt; $i++) {
            // ルール追加
            $name = 'notice_files' . $i;
            $addRule = [$name => [
                'label' => '添付' . $i,
                'rules' => 'uploaded[' . $name . ']|max_size[' . $name . ', ' . UPLOAD_FILE_MAX_SIZE . ']|ext_in[' . $name . ',jpg,jpeg,pdf,xls,xlsx,doc,docx,zip,txt]'
            ]];
            $rule = array_merge($rule, $addRule);
        }

        if ($this->validate($rule) === true) {
            // エラーがなければOK
            $noticeData = $this->request->getPost();

            // アップロードされたファイルの情報を取得
            foreach ($fileList as $key => $file) {
                $fileExtPath = get_file_ext_icon_path($file->guessExtension());
                $uploadFiles[] = array(
                    'file_name' => $file->getName(),
                    'file_ext_path' => $fileExtPath
                );
            }
            // お知らせ情報IDを取得
            $noticeInfoId = $this->request->getPost('notice_info_id');
            if (empty($noticeInfoId) === true) {
                $noticeInfoId = 0;
            }
            
            // 既存の添付資料リスト取得
            $existingDocumentList = $this->adminNoticeLibrarie->get_notice_document_list($noticeInfoId);

            // 確認画面用データ作成
            $postData = notice_regist_confirm($noticeData, $existingDocumentList, $uploadFiles);

            $result = true;
        } else {
            $error = $this->validator->getErrors();
        }

        $ret = [
            'result' => $result,
            'postData' => $postData,
            'files' => $uploadFiles,
            'existing_notice_document_list' => $existingDocumentList,
            'error' => $error
        ];
        return json_encode($ret);
    }

    /**
     * Ajax：お知らせ登録・更新処理
     */
    public function ajax_notice_regist_proc(): string
    {
        $result = false;
        $ret = [];
        $error = [];

        $rule = [
            'notice_category_id' => ['label' => 'カテゴリー', 'rules' => 'required'],
            'notice_title' => ['label' => 'タイトル', 'rules' => 'required'],
            'notice_body' => ['label' => '本文', 'rules' => 'required'],
            'regist_mode' => ['label' => 'モード', 'rules' => 'required'],
        ];

        $filesCnt = count($this->request->getFiles());
        for ($i=1; $i<=$filesCnt; $i++) {
            // ルール追加
            $name = 'notice_files' . $i;
            $addRule = [$name => [
                'label' => '添付' . $i,
                'rules' => 'uploaded[' . $name . ']|max_size[' . $name . ', ' . UPLOAD_FILE_MAX_SIZE . ']|ext_in[' . $name . ',jpg,jpeg,pdf,xls,xlsx,doc,docx,zip,txt]'
            ]];
            $rule = array_merge($rule, $addRule);
        }

        if ($this->validate($rule) === true) {
            // データ取得
            $noticeData = $this->request->getPost();
            $noticeFiles = $this->request->getFiles();

            // お知らせ基本データ登録・更新
            $result = $this->adminNoticeLibrarie->notice_info_proc($noticeData, $noticeFiles, $this->_memberData['member_id'], $this->_settingData->fiscal_year_id);
            if ($result === false) {
                $error = $this->adminNoticeLibrarie->errorMessage;
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
     * Ajax：お知らせ削除
     */
    public function ajax_delete_notice_info(): string
    {
        $result = false;
        $ret = [];
        $error = [];

        $rule = [
            'notice_info_id' => ['label' => 'お知らせID', 'rules' => 'required'],
        ];

        if ($this->validate($rule) === true) {
            // データ取得
            $notice_info_id = $this->request->getPost('notice_info_id');

            // お知らせ資料削除
            $result = $this->adminNoticeLibrarie->delete_notice_info($notice_info_id, $this->_memberData['member_id']);
            
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
     * Ajax：お知らせ資料削除
     */
    public function ajax_delete_notice_document(): string
    {
        $result = false;
        $ret = [];
        $error = [];

        $rule = [
            'notice_info_id' => ['label' => 'お知らせID', 'rules' => 'required'],
            'document_id' => ['label' => '資料ID', 'rules' => 'required'],
        ];

        if ($this->validate($rule) === true) {
            // データ取得
            $notice_info_id = $this->request->getPost('notice_info_id');
            $document_id = $this->request->getPost('document_id');

            // お知らせ資料削除
            $result = $this->adminNoticeLibrarie->delete_notice_document($notice_info_id, $document_id);
            
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
     * Ajax：未経過大会・審査等の一覧情報取得
     */
    public function ajax_unexpired_event_list(): string
    {
        $result = false;
        $ret = [];
        $error = [];

        $rule = [
            'notice_category_id' => ['label' => 'お知らせカテゴリーID', 'rules' => 'required'],
        ];

        if ($this->validate($rule) === true) {
            // データ取得
            $noticeCategoryId = $this->request->getPost('notice_category_id');

            // 未経過大会・審査等の一覧情報取得
            $result = $this->adminLibrarie->get_unexpired_event_list($noticeCategoryId);
            
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
