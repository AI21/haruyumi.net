<?php

namespace App\Libraries\Admin;
use App\Libraries\Admin\AdminLibrarie;
use App\Models\AdminModel;
use App\Models\MemberModel;
use App\Models\NoticeModel;

class AdminMemberLibrarie
{
    private $adminLibrarie;
	private $adminModel;
	private $memberModel;
	private $noticeModel;
    protected $_session;
    protected $errorMessage = "";

	public function __construct(){
        $this->adminLibrarie = new AdminLibrarie();
		$this->adminModel = model(AdminModel::class);
        $this->memberModel = model(MemberModel::class);
        $this->noticeModel = model(NoticeModel::class);
        $this->_session = session();
	}

	/**
	 * 会員登録・更新処理
     * @param   array   $memberData     会員情報
     * @param   int     $memberId       登録メンバーID
     * @param   int     &$memberId      会員ID
	 * @return bool
	 */
    public function member_regist_proc(array $memberData, int $fiscalYearId, int &$memberId): bool
    {
        $result = false;

        // トランザクション開始
        $this->adminModel->trans_start();

        switch ($memberData['regist_mode']) {
            case MODE_REGIST :
                // 会員データ登録
                $result = $this->memberModel->insert_member_data($memberData, $memberId);
                if ($result === true) {
                    // 会計年度会員データ登録
                    $result = $this->memberModel->insert_member_regist_fiscal($memberData, $memberId, $fiscalYearId);
                    if ($result === true) {
                        $holderId = 0;
                        $gradeId = 0;
                        if (empty($memberData['holder_grade_cd']) === false) {
                            // 称号と段位・級位を取得
                            list($holderId, $gradeId) = explode('|', $memberData['holder_grade_cd']);
                        }
                        // 称号と段位・級位データ登録
                        $result = $this->memberModel->insert_member_grade_holder($memberData, $memberId, $holderId, $gradeId);
                    }
                }
                break;
            case MODE_REVISION :
                // 会員データ更新
                $result = $this->memberModel->update_member_data($memberData);
                if ($result === true) {
                    if (empty($memberData['holder_grade_cd']) === false) {
                        // 称号と段位・級位を取得
                        list($holderId, $gradeId) = explode('|', $memberData['holder_grade_cd']);
                        // 称号と段位・級位データ更新
                        $result = $this->memberModel->update_member_grade_holder($memberData, $holderId, $gradeId);
                    }
                }
                break;
        }

        // トランザクション完了
        $this->adminModel->trans_complete();

        // 失敗時はロールバックされる
        if ($this->adminModel->trans_status() === false) {
            $result = false;
        }

        return $result;
    }

	/**
	 * 会員名簿更新メール配信処理
     * @param   array   $mailData     メール情報
     * @param   int     $memberId     登録メンバーID
     * @param   string  $errorMsg     エラーメッセージ
	 * @return bool
	 */
    public function member_list_files_upload_mail_proc(array $mailData, int $memberId, string &$errorMsg): bool
    {
        $result = false;
        $noticeSeqNo = 0;
        $mailBccList = array();
        
        $noticeInfoId = 0;
        $noticeCategoryId = NOTICE_CATEGORY_ID_KASUGAI;
        $noticeTitle = $mailData['member_list_title'];
        $noticeBody = $mailData['member_list_body'];

        // お知らせ基本データ登録
        $result = $this->noticeModel->insert_notice_info($noticeCategoryId, $noticeTitle, $noticeBody, DB_FLG_OFF, DB_FLG_OFF, '', $memberId, $noticeInfoId);
        
        if ($result === true) {
            // メール配信対象者リスト取得
            $mailMemberList = $this->memberModel->get_all_mail_member_list();
            if (empty($mailMemberList) === true) {
                // メール配信対象者なし
                $errorMsg = 'メール配信対象者がありませんでした';
                return $result;
            }
            foreach ($mailMemberList['result'] as $idx => $data) {
                $mailBccList[] = $data['mail_address'];
            }
            
            // メール配信
            $result = $this->adminLibrarie->send_mail_proc($mailBccList, $noticeTitle, $noticeBody);
            if ($result === false) {
                $errorMsg = 'メール配信に失敗しました';
            }
        }

        return $result;
    }

	/**
	 * 会員称号更新処理
     * @param   int     $shinsaId       審査ID
     * @param   int     $memberId       会員ID
     * @param   int     $passHolderId   称号ID
     * @param   string  $acquiredDay    認許日
	 * @return bool
	 */
    public function rankup_holder_proc(int $shinsaId, int $memberId, int $passHolderId, string $acquiredDay): bool
    {
        $result = false;

        // トランザクション開始
        $this->adminModel->trans_start();

        // 段位・級位データ更新
        $result = $this->memberModel->update_member_holder($memberId, $passHolderId, $acquiredDay);
        if ($result === true) {
            // 昇段フラグ更新
            $result = $this->update_shinsa_offer_member_rankup_flg($shinsaId, $memberId);
            if ($result === true) {
                // トランザクション完了
                $this->adminModel->trans_complete();
            }
        }

        // 失敗時はロールバックされる
        if ($this->adminModel->trans_status() === false) {
            $result = false;
        }

        return $result;
    }

	/**
	 * 会員段位・級位更新処理
     * @param   int     $shinsaId       審査ID
     * @param   int     $memberId       会員ID
     * @param   int     $passGradeId    昇段段位ID
     * @param   string  $acquiredDay    認許日
	 * @return bool
	 */
    public function rankup_grade_proc(int $shinsaId, int $memberId, int $passGradeId, string $acquiredDay): bool
    {
        $result = false;

        // トランザクション開始
        $this->adminModel->trans_start();

        // 段位・級位データ更新
        $result = $this->memberModel->update_member_grade($memberId, $passGradeId, $acquiredDay);
        if ($result === true) {
            // 昇段フラグ更新
            $result = $this->update_shinsa_offer_member_rankup_flg($shinsaId, $memberId);
            if ($result === true) {
                // トランザクション完了
                $this->adminModel->trans_complete();
            }
        }

        // 失敗時はロールバックされる
        if ($this->adminModel->trans_status() === false) {
            $result = false;
        }

        return $result;
    }

	/**
	 * 昇段フラグ更新
     * @param   int     $shinsaId          審査ID
     * @param   int     $memberId          会員ID
	 * @return bool
	 */
    private function update_shinsa_offer_member_rankup_flg(int $shinsaId, int $memberId): bool
    {
        // 昇段フラグ更新
        return $this->memberModel->update_shinsa_offer_member_rankup_flg($shinsaId, $memberId);
    }

	/**
	 * 会員リストファイル登録処理
     * @param   object  $membreListFile     会員リストファイル情報
     * @param   int     $memberId           登録メンバーID
	 * @return bool
	 */
    public function member_list_files_proc(object $objMembreListFile, int $memberId): bool
    {
        $membreListFileNameOld = "";
        
        // 会員リストファイル名
        $membreListFileNameNew = $objMembreListFile->getName();

        // 最新の会員名簿ファイル情報を取得
        $membreListFile = $this->memberModel->get_member_list_file();
        if (empty($membreListFile) === false) {
            $membreListFileNameOld = $membreListFile->member_list_file_name;
        } else {
            session()->setFlashdata('msg', '最新の会員名簿ファイル情報取得に失敗しました。');
            return false;
        }
        
        // 会員リストファイルデータ登録
        $result = $this->memberModel->insert_member_list_file($membreListFileNameNew, $memberId);
        if ($result === false) {
            session()->setFlashdata('msg', '会員リストファイルデータ登録に失敗しました。');
            return false;
        }
        
        // アップロード先ディレクトリ
        $uploadDir = FCPATH . MEMBER_LIST_FILE_DIR;
        
        // 古い会員名簿をバックアップ：ファイルリネーム
        $backupFile = FCPATH . MEMBER_LIST_FILE_DIR . '/' . $membreListFileNameOld;
        $renameFile = FCPATH . MEMBER_LIST_FILE_BACKUP_DIR . '/' . $membreListFileNameOld;
        if (rename($backupFile, $renameFile) === true) {
            // 新しい会員名簿を保存
            $result = $objMembreListFile->move($uploadDir, $membreListFileNameNew);
            if ($result === false) {
                session()->setFlashdata('msg', '新しい会員名簿のアップロードに失敗しました。');
                return false;
            }
        } else {
            session()->setFlashdata('msg', '古い会員名簿のバックアップに失敗しました。');
            return false;
        }

        return true;
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
