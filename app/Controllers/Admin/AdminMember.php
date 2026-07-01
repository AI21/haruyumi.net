<?php

namespace App\Controllers\Admin;
use App\Controllers\CommonController;
use App\Libraries\Admin\AdminMemberLibrarie;

class AdminMember extends CommonController
{
    private $adminMemberLibrarie;
    private $admineHelper;

	public function __construct() {
        parent::__construct();

		$this->adminMemberLibrarie = new AdminMemberLibrarie();
        helper('admin');
        helper('Admin/member');
	}

    // 会員名簿差し替えページ
    public function member_list_file_regist()
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

        // 会員管理者チェック
        if ($this->_memberData['member_admin_flg'] === FLG_OFF) {
            // 会員管理者でない場合はメインに移動
            return redirect()->to('home');
        }
        
        // ページ
        $page = array();

		$data = [
            'fiscalYearId' => $fiscalYearId,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
            'noticeInfoId' => "",
            'noticeTitle' => "",
            'noticeBody' => "",
            'noticeDocumentList' => array(),
			'officerFlg' => FLG_FALSE,
            'page' => $page,
            'mode' => MODE_REGIST,
            'headerCss' => array('admin/form'),
            'footerJs' => array('admin/member', 'admin/member_list_file'),
		];

        echo view('common/header', $data);
        echo view('common/menu');
        echo view('admin/member/member_file_regist');
        echo view('admin/modal_admin');
        return view('common/footer');
    }

    // 会員追加ページ
    public function member_regist()
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        // 会員管理者チェック
        if ($this->_memberData['member_admin_flg'] === FLG_OFF) {
            // 会員管理者でない場合はメインに移動
            return redirect()->to('home');
        }

        // 称号・段位リスト取得
        $holderGradeList = $this->commonLibrarie->get_holder_grade_list();
        
        // ページ
        $page = array();

		$data = [
            'fiscalYearId' => $this->_settingData->fiscal_year_id,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
            'holderGradeList' => $holderGradeList,
            'memberDetail' => NULL,
			'officerFlg' => FLG_FALSE,
            'page' => $page,
            'mode' => MODE_REGIST,
            'headerCss' => array('admin/form'),
            'footerJs' => array('admin/member', 'admin/member_regist'),
		];

        echo view('common/header', $data);
        echo view('common/menu');
        echo view('admin/member/member_regist');
        echo view('admin/modal_admin');
        return view('common/footer');
    }

    // 会員情報変更ページ
    public function member_revision()
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        // 会員管理者チェック
        if ($this->_memberData['member_admin_flg'] === FLG_OFF) {
            // 会員管理者でない場合はメインに移動
            return redirect()->to('home');
        }

        // データ取得
        $memberId = $this->request->getPost('member_id');

        // 会員情報取得
        $memberDetail = $this->memberLibrarie->get_member_data($memberId);
        if (empty($memberDetail) === true) {
            // 会員情報がない場合はメインに移動
            return redirect()->to('member');
        }

        // 称号・段位リスト取得
        $holderGradeList = $this->commonLibrarie->get_holder_grade_list();
        
        // ページ
        $page = array();

		$data = [
            'fiscalYearId' => $this->_settingData->fiscal_year_id,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
            'holderGradeList' => $holderGradeList,
            'memberDetail' => $memberDetail,
			'officerFlg' => FLG_FALSE,
            'page' => $page,
            'mode' => MODE_REVISION,
            'headerCss' => array('admin/form'),
            'footerJs' => array('admin/member', 'admin/member_regist'),
		];

        echo view('common/header', $data);
        echo view('common/menu');
        echo view('admin/member/member_regist');
        echo view('admin/modal_admin');
        return view('common/footer');
    }

    /**
     * Ajax：会員名簿ファイル登録確認
     */
    public function ajax_member_list_file_conf(): string
    {
        $result = false;
        $memberListFile = [];
        $ret = [];
        $error = [];

        $rule = [
            'member_list_file' => ['label' => '会員名簿', 'rules' => 'uploaded[member_list_file]|max_size[member_list_file, ' . UPLOAD_FILE_MAX_SIZE . ']|ext_in[member_list_file,pdf]'],
        ];

        $ruleMail = [
            'member_list_title' => ['label' => 'メールタイトル', 'rules' => 'required'],
            'member_list_body' => ['label' => 'メール本文', 'rules' => 'required'],
        ];

        if ($this->validate($rule) === true) {
            // エラーがなければOK
            $mailData = $this->request->getPost();
            $fileObj = $this->request->getFiles();

            // メール配信チェック
            if ((bool)$mailData['member_list_mail_send'] === true) {
                if ($this->validate($ruleMail) === false) {
                    $error = $this->validator->getErrors();
                    $ret = [
                        'result' => $result,
                        'error' => $error
                    ];
                    return json_encode($ret);
                }
            }

            // 会員名簿ファイル情報
            $memberListFile = array(
                'file_name' => $fileObj['member_list_file']->getName(),
                'file_ext_path' => get_file_ext_icon_path($fileObj['member_list_file']->guessExtension()),
            );
            $result = true;
        } else {
            $error = $this->validator->getErrors();
        }

        $ret = [
            'result' => $result,
            'memberListFile' => $memberListFile,
            'error' => $error
        ];
        return json_encode($ret);
    }

    /**
     * Ajax：会員名簿ファイル登録処理
     */
    public function ajax_member_list_file_proc(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        $errorMsg = "";

        $rule = [
            'member_list_file' => ['label' => '会員名簿', 'rules' => 'uploaded[member_list_file]|max_size[member_list_file, ' . UPLOAD_FILE_MAX_SIZE . ']|ext_in[member_list_file,pdf]'],
            'member_list_mail_send' => ['label' => 'メール配信', 'rules' => 'required'],
        ];

        if ($this->validate($rule) === true) {
            // データ取得
            $fileObj = $this->request->getFiles();
            $mailData = $this->request->getPost();

            // 会員名簿ファイルアップロード処理
            $result = $this->adminMemberLibrarie->member_list_files_proc($fileObj['member_list_file'], $this->_memberData['member_id']);
            if ($result === false) {
                $error[] = session()->getFlashdata('msg');
            } else {
                if ($mailData['member_list_mail_send'] == FORM_CHECKBOX_TRUE) {
                    // メール配信：会員名簿ファイル更新お知らせ
                    $result = $this->adminMemberLibrarie->member_list_files_upload_mail_proc($mailData, $this->_memberData['member_id'], $errorMsg);
                    if ($result === false) {
                        $error[] = $errorMsg;
                    }
                }
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
     * Ajax：会員登録確認
     */
    public function ajax_member_regist_conf(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        $postData = '';

        $rule = [
            'member_id' => ['label' => '会員ID', 'rules' => 'is_integer_on_empty'],
            'member_name_f' => ['label' => '会員名（性）', 'rules' => 'required|min_length[1]|max_length[20]'],
            'member_name_s' => ['label' => '会員名（名）', 'rules' => 'required|min_length[1]|max_length[20]'],
            'member_kana_f' => ['label' => '会員名よみかな（性）', 'rules' => 'required|min_length[1]|max_length[20]|is_hiragana'],
            'member_kana_s' => ['label' => '会員名よみかな（名）', 'rules' => 'required|min_length[1]|max_length[20]|is_hiragana'],
            'gender_cd' => ['label' => '性別', 'rules' => 'required'],
            'kasugai_regist_flg' => ['label' => '春日井弓道協会メイン会員', 'rules' => 'required'],
            'kasugai_regist_date' => ['label' => '春日井弓道協会登録日', 'rules' => 'required'],
            // 'kasugai_regist_date' => ['label' => '春日井弓道協会登録日', 'rules' => 'required|valid_date[Y-m-d]'],
            'aiti_renmei_regist_flg' => ['label' => '愛弓連登録', 'rules' => 'required'],
            // 'notice_send_flg' => ['label' => 'お知らせメール受信', 'rules' => 'required'],
            'mail_address' => ['label' => 'メールアドレス', 'rules' => 'required|valid_email|min_length[6]|max_length[128]|is_unique_mail[{member_id}]'],
            // 'login_pw' => ['label' => 'パスワード', 'rules' => 'required|min_length[4]|max_length[20]|is_password'],
            'regist_mode' => ['label' => 'モード', 'rules' => 'required'],
        ];

        if ($this->validate($rule) === true) {
            // エラーがなければOK
            $memberData = $this->request->getPost();

            // 称号・段位リスト取得
            $holderGradeList = $this->commonLibrarie->get_holder_grade_list();

            // 会員登録確認画面用データ作成
            $postData .= member_regist_confirm($memberData, $holderGradeList);
            
            $result = true;
        } else {
            $error = $this->validator->getErrors();
        }

        $ret = [
            'result' => $result,
            'postData' => $postData,
            'error' => $error
        ];
        return json_encode($ret);
    }

    /**
     * Ajax：会員登録処理
     */
    public function ajax_member_regist_proc(): string
    {
        $result = false;
        $ret = [];
        $error = [];

        $rule = [
            'member_id' => ['label' => '会員ID', 'rules' => 'is_integer_on_empty'],
            'member_name_f' => ['label' => '会員名（性）', 'rules' => 'required|min_length[1]|max_length[20]'],
            'member_name_s' => ['label' => '会員名（名）', 'rules' => 'required|min_length[1]|max_length[20]'],
            'member_kana_f' => ['label' => '会員名よみかな（性）', 'rules' => 'required|min_length[1]|max_length[20]|is_hiragana'],
            'member_kana_s' => ['label' => '会員名よみかな（名）', 'rules' => 'required|min_length[1]|max_length[20]|is_hiragana'],
            'gender_cd' => ['label' => '性別', 'rules' => 'required'],
            'kasugai_regist_flg' => ['label' => '春日井弓道協会メイン会員', 'rules' => 'required'],
            'kasugai_regist_date' => ['label' => '春日井弓道協会登録日', 'rules' => 'required'],
            // 'kasugai_regist_date' => ['label' => '春日井弓道協会登録日', 'rules' => 'required|valid_date[Y-m-d]'],
            'aiti_renmei_regist_flg' => ['label' => '愛弓連登録', 'rules' => 'required'],
            // 'notice_send_flg' => ['label' => 'お知らせメール受信', 'rules' => 'required'],
            'mail_address' => ['label' => 'メールアドレス', 'rules' => 'required|valid_email|min_length[6]|max_length[128]|is_unique_mail[{member_id}]'],
            // 'login_pw' => ['label' => 'パスワード', 'rules' => 'required|min_length[4]|max_length[20]|is_password'],
            'regist_mode' => ['label' => 'モード', 'rules' => 'required'],
        ];

        if ($this->validate($rule) === true) {
            // データ取得
            $memberData = $this->request->getPost();

            // 会計年度ID
            $fiscalYearId = $this->_settingData->fiscal_year_id;

            // 会員登録・更新
            $memberId = 0;
            $result = $this->adminMemberLibrarie->member_regist_proc($memberData, $fiscalYearId, $memberId);
            if ($result === true) {
                // 段位・級位登録
            } else {
                $error[] = '会員登録・更新に失敗しました。';
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
