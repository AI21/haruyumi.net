<?php

namespace App\Libraries;
use App\Models\LoginModel;

class LoginLibrarie
{
	private $loginModel;
    protected $_session;
    protected $errorMessage = "";

	public function __construct(){
		$this->loginModel = model(LoginModel::class);
        $this->_session = session();
	}

	/**
	 * ログインチェック
	 * @return bool
	 */
    public function login_check(): bool
    {
        $ret = false;

        $loginStateFlg = $this->_session->get('loginStateFlg');
        if (empty($loginStateFlg) === false && $loginStateFlg === FLG_ON) {
            $memberData = $this->_session->get('memberData');
            // セッション情報を上書き
            $this->_session->remove('loginStateFlg');
            $this->_session->remove('memberData');
            $this->_session->set('loginStateFlg', FLG_ON);
            $this->_session->set('memberData', $memberData);
            $ret = true;
        }
        return $ret;
    }

    /**
     * ログイン処理
     * @param   string  $memberIdMail   ユーザーID or メールアドレス
     * @param   string  $password       パスワード
     * @return  bool    true：ログインOK
     */
    public function login_process(string $memberIdMail, string $password): bool
    {
        $ret = false;

        // ユーザー情報取得
        $memberData = $this->loginModel->login($memberIdMail);
        if (empty($memberData) === false) {
            // 認証処理
            if (password_verify($password, $memberData->login_pw) === true) {

                // // 審査申込可能チェック
                // $shinsaOfferChuou = true;   // 中央審査
                // $shinsaOfferRengo = true;   // 連合審査
                // $shinsaOfferChiho = true;   // 地方審査
                // // 審査共通：全弓連休会中、全弓連IDなしの場合はNG
                // if (
                //     $memberData->renmei_adjourning_flg === FLG_ON ||
                //     empty($memberData->renmei_id) === true
                // ) {
                //     $shinsaOfferChuou = false;
                //     $shinsaOfferRengo = false;
                //     $shinsaOfferChiho = false;
                // }

                // // 地方審査は愛弓連登録をしていない場合はNG
                // if (empty($memberData->aiti_renmei_regist_flg) === true) {
                //     $shinsaOfferChiho = false;
                // }

                // 認証の場合はセッションにユーザー情報を登録
                $this->_session->set('loginStateFlg', FLG_ON);
                $this->_session->set('memberData', [
                    'member_id' => $memberData->member_id,
                    'name_f' => $memberData->name_f,
                    'name_s' => $memberData->name_s,
                    'kana_f' => $memberData->kana_f,
                    'kana_s' => $memberData->kana_s,
                    'gender_cd' => $memberData->gender_cd
                ]);
                
                $ret = true;

            } else {
                $this->errorMessage = "パスワードが違っています";
            }
        } else {
            $this->errorMessage = "会員情報が取得できませんでした";
        }

        return $ret;
    }

	/**
	 * 会員情報取得
	 * @return ?object
	 */
    public function get_member_data(int $memberId): ?object
    {
        return $this->loginModel->get_member_data($memberId);
    }

	/**
	 * メニュー管理リスト取得
     * @param   int  $memberId   ユーザーID
	 * @return ?array
	 */
    public function get_officer_menu_id_list(int $memberId): ?array
    {
        $result = array();

        $memberNoticeAdminList = $this->loginModel->get_officer_menu_id_list($memberId);
        if (empty($memberNoticeAdminList) === false) {
            if ($memberNoticeAdminList['numRows'] > 0) {
                foreach ($memberNoticeAdminList['result'] as $idx => $data) {
                    $result[] = $data['menu_id'];
                }
            }
        }

        return $result;
    }

	/**
	 * メニューカテゴリー管理リスト取得
     * @param   int  $memberId   ユーザーID
	 * @return ?array
	 */
    public function get_officer_category_id_list(int $memberId): ?array
    {
        $result = array();

        $memberNoticeAdminList = $this->loginModel->get_officer_category_id_list($memberId);
        if (empty($memberNoticeAdminList) === false) {
            if ($memberNoticeAdminList['numRows'] > 0) {
                foreach ($memberNoticeAdminList['result'] as $idx => $data) {
                    $result[] = $data['category_id'];
                }
            }
        }

        return $result;
    }

	/**
	 * お知らせ管理IDリスト取得
     * @param   int  $memberId   ユーザーID
	 * @return ?array
	 */
    public function get_notice_admin_id_list(int $memberId): ?array
    {
        $result = array();

        $memberNoticeAdminList = $this->loginModel->get_notice_admin_id_list($memberId);
        if (empty($memberNoticeAdminList) === false) {
            if ($memberNoticeAdminList['numRows'] > 0) {
                foreach ($memberNoticeAdminList['result'] as $idx => $data) {
                    $result[] = $data['notice_category_id'];
                }
            }
        }

        return $result;
    }

    /*
     * 管理役員チェック
     * @param   array   $memberData     ユーザー情報
     * @param   object  $memuInfo       メニュー情報
    * return bool
    */
    public function chk_officer_menu(array $memberData, object $memuInfo) :bool
    {
        $ret = false;

        // 許可されたメニューがあるか
        if (in_array($memuInfo->menu_id, $memberData['officer_menu_id_list']) === true) {
            $ret = true;
        }
        return $ret;
    }

    /*
     * 管理役員チェック
     * @param   array   $memberData     ユーザー情報
     * @param   object  $memuInfo       メニュー情報
     * @param   int     $categoryId     カテゴリーID
    * return bool
    */
    public function chk_officer_menu_category(array $memberData, object $memuInfo, int $categoryId) :bool
    {
        $ret = false;

        // 許可されたメニューとカテゴリーがあるか
        if (
            in_array($memuInfo->menu_id, $memberData['officer_menu_id_list']) === true
            && in_array($categoryId, $memberData['officer_category_id_list']) === true
        ) {
            $ret = true;
        }
        return $ret;
    }

    /**
     * ログイン情報変更チェック
     * @param   array  $postData   POSTデータ
     * @return  bool    true：ログインOK
     */
    public function login_change_check(array $postData): bool
    {
        $ret = true;
        $messageArray = [];
        $pwErrorCnt = 0;
        $registLoginId = null;
        $registMailAddress = "";

        // パラメータ取得
        $loginId = $postData['login_id'];
        $mailAddress = $postData['mail_address'];
        $passwordOld = $postData['password_old'];
        $passwordNew = $postData['password_new'];
        $passwordConf = $postData['password_conf'];
        
        $memberId = $this->_session->memberData['member_id'];
        // 会員情報取得
        $memberData = $this->loginModel->get_member_data($memberId);
        if (empty($memberData) === true) {
            $messageArray[] = "会員情報が取得できませんでした";
            $ret = false;
        }
        $registLoginId = $memberData->login_id;
        $registMailAddress = $memberData->mail_address;

        // ログインIDを変更している場合は重複チェック
        if ($loginId !== $registLoginId) {
            $duplicateData = $this->loginModel->duplicate_check_login_id($memberId, $loginId);
            // 重複データがあればエラー
            if (empty($duplicateData) === false) {
                $messageArray[] = "ログインID：他の方が使用しています";
                $ret = false;
            }
        }

        // メールアドレスを変更している場合は重複チェック
        if ($mailAddress !== $registMailAddress) {
            $duplicateData = $this->loginModel->duplicate_check_mail_address($memberId, $mailAddress);
            // 重複データがあればエラー
            if (empty($duplicateData) === false) {
                $messageArray[] = "メールアドレス：他の方が使用しています";
                $ret = false;
            }
        }

        // パスワードのいずれかに入力がある場合は各種チェック
        if (empty($passwordOld) === false || empty($passwordNew) === false || empty($passwordConf) === false) {
            
            // 現在のパスワード
            // 未入力チェック
            if (empty($passwordOld) == true) {
                $messageArray[] = "現在のパスワード：入力されていません";
                $pwErrorCnt++;
                $ret = false;
            } else {
                // 文字数チェック
                $pwLength = mb_strlen($passwordOld);
                if ($pwLength < VALIDATION_LOGIN_PW_MIN) {
                    $messageArray[] = "現在のパスワード：" . VALIDATION_LOGIN_PW_MIN . "文字以内にしてください";
                    $pwErrorCnt++;
                    $ret = false;
                }
                if ($pwLength > VALIDATION_LOGIN_PW_MAX) {
                    $messageArray[] = "現在のパスワード：" . VALIDATION_LOGIN_PW_MAX . "文字以上にしてください";
                    $pwErrorCnt++;
                    $ret = false;
                }
            }
            // 新しいパスワード
            // 未入力チェック
            if (empty($passwordNew) == true) {
                $messageArray[] = "新しいパスワード：入力されていません";
                $pwErrorCnt++;
                $ret = false;
            } else {
                // 文字数チェック
                $pwLength = mb_strlen($passwordNew);
                if ($pwLength < VALIDATION_LOGIN_PW_MIN) {
                    $messageArray[] = "新しいパスワード：" . VALIDATION_LOGIN_PW_MIN . "文字以内にしてください";
                    $pwErrorCnt++;
                    $ret = false;
                }
                if ($pwLength > VALIDATION_LOGIN_PW_MAX) {
                    $messageArray[] = "新しいパスワード：" . VALIDATION_LOGIN_PW_MAX . "文字以上にしてください";
                    $pwErrorCnt++;
                    $ret = false;
                }
            }
            // 新しいパスワード（確認用）
            // 未入力チェック
            if (empty($passwordConf) == true) {
                $messageArray[] = "新しいパスワード（確認用）：入力されていません";
                $pwErrorCnt++;
                $ret = false;
            } else {
                // 文字数チェック
                $pwLength = mb_strlen($passwordConf);
                if ($pwLength < VALIDATION_LOGIN_PW_MIN) {
                    $messageArray[] = "新しいパスワード（確認用）：" . VALIDATION_LOGIN_PW_MIN . "文字以内にしてください";
                    $pwErrorCnt++;
                    $ret = false;
                }
                if ($pwLength > VALIDATION_LOGIN_PW_MAX) {
                    $messageArray[] = "新しいパスワード（確認用）：" . VALIDATION_LOGIN_PW_MAX . "文字以上にしてください";
                    $pwErrorCnt++;
                    $ret = false;
                }
            }

            if ($pwErrorCnt === 0) {
                // 新しいパスワードと新しいパスワード（確認用）の同一チェック
                if ($passwordNew !== $passwordConf) {
                    $messageArray[] = "新しいパスワード（確認用）：新しいパスワードと同じではありません";
                    $pwErrorCnt++;
                    $ret = false;
                }
            }

            if ($pwErrorCnt === 0) {
                // 認証処理
                $loginPw = $memberData->login_pw;
                if (password_verify($passwordOld, $loginPw) === false) {
                    $messageArray[] = "現在のパスワード：登録されているパスワードと違います";
                    $ret = false;
                } else {
                    // 現在のパスワードと新しいパスワードの同一チェック
                    if ($passwordOld == $passwordNew) {
                        $messageArray[] = "新しいパスワード：現在のパスワードと同じにはできません";
                        $ret = false;
                    }
                }
            }
        }

        if (empty($messageArray) === false) {
            $this->errorMessage = implode("<br>", $messageArray);
        }

        return $ret;
    }

    /**
     * ログイン情報変更チェック
     * @param   array  $postData   POSTデータ
     * @return  bool    true：ログインOK
     */
    public function login_change_process(array $postData): bool
    {
        $result = array();
        $registPassword = "";

        // パラメータ取得
        $loginId = $postData['login_id'];
        $mailAddress = $postData['mail_address'];
        if (empty($postData['password_new']) === false) {
            // パスワード変更は入力がある場合のみ
            $options = [
                'cost' => 12,
            ];
            $registPassword = password_hash($postData['password_new'], PASSWORD_BCRYPT, $options);
        }
        
        $memberId = $this->_session->memberData['member_id'];

        $result = $this->loginModel->login_change_process($memberId, $loginId, $mailAddress, $registPassword);
        if ($result === true) {
            // 変更完了時はセッションを破棄（再ログイン）
            $this->_session->remove('loginStateFlg');
        }
		
        return $result;
    }

	/**
	 * ゲッター：エラーメッセージ
	 * @return string
	 */
    public function _get_error_message(): string
    {
        return $this->errorMessage;
    }
}
