<?php

namespace App\Libraries\Admin;
use App\Libraries\Admin\AdminLibrarie;
use App\Models\AdminModel;
use App\Models\MemberModel;
use App\Models\NoticeModel;
use App\Models\Admin\AdminDocumentModel;
use CodeIgniter\HTTP\Files\UploadedFile;

class AdminDocumentLibrarie
{
    private $adminLibrarie;
	private $adminModel;
	private $adminDocumentModel;
	private $noticeModel;
	private $memberModel;
    protected $_session;
    protected $errorMessage = "";

	public function __construct(){
        $this->adminLibrarie = new AdminLibrarie();
		$this->adminModel = model(AdminModel::class);
        $this->adminDocumentModel = model(AdminDocumentModel::class);
        $this->noticeModel = model(NoticeModel::class);
        $this->memberModel = model(MemberModel::class);
        $this->_session = session();
	}

	/**
	 * 資料ファイル登録処理
     * @param   object  $documentFile       資料ファイル情報
     * @param   array   $documentData       資料データ
     * @param   int     $memberId           登録メンバーID
	 * @return bool
	 */
    public function document_files_proc($objDocumentFile, array $documentData, int $memberId, int $fiscalYearId = 0): bool
    {
        $result = false;
        $errorMsg = '';

        if (is_array($objDocumentFile) === true) {
            $objDocumentFile = $objDocumentFile[0] ?? reset($objDocumentFile);
        }

        if ($objDocumentFile instanceof UploadedFile === false) {
            session()->setFlashdata('msg', '資料ファイルの形式が正しくありません。');
            return false;
        }
        
        // アップロード先ディレクトリ
        $uploadDir = FCPATH . UPLOAD_FILE_DIR . '/' . DOCUMENT_MAIN_FILE_DIR . '/' . DOCUMENT_USEGYM_FILE_DIR;

        // 資料ファイルを保存
        $result = $objDocumentFile->move($uploadDir, $objDocumentFile->getName());
        if ($result === false) {
            session()->setFlashdata('msg', '新しい資料ファイルのアップロードに失敗しました。');
            return false;
        }

        // トランザクション開始
        $this->adminModel->trans_start();
        
        // 資料ファイル名
        $documentName = $objDocumentFile->getName();
        // 資料ファイル拡張子
        $documentExt = $objDocumentFile->getClientExtension();
        // 資料ファイル保存パス
        $documentPath = '/' . UPLOAD_FILE_DIR . '/' . DOCUMENT_MAIN_FILE_DIR . '/' . DOCUMENT_USEGYM_FILE_DIR . '/' . $documentName;
        
        // 資料ファイルデータ登録
        $result = $this->adminDocumentModel->insert_document_info($documentData['document_category_id'], $documentName, $documentExt, $documentPath);
        if ($result === false) {
            $errorMsg = '資料ファイルデータ登録に失敗しました。';
        } else {
            // メール配信：資料ファイル更新お知らせ
            if ($documentData['document_mail_send'] == FORM_CHECKBOX_TRUE) {
                // メール配信：資料ファイル更新お知らせ
                $result = $this->document_files_upload_mail_proc($documentData, $objDocumentFile, $documentPath, $memberId, $errorMsg);
                if ($result === false) {
                    session()->setFlashdata('msg', $errorMsg);
                }
            }
        }

        // 失敗時はロールバックされる
        if ($result === false) {
            $this->adminModel->trans_rollback();
        }

        // トランザクション完了
        $this->adminModel->trans_complete();

        // 失敗時はロールバックされる
        if ($this->adminModel->trans_status() === false) {
            // アップロードした資料ファイルを削除
            unlink($uploadDir . '/' . $documentName);
            session()->setFlashdata('msg', $errorMsg);
            $result = false;
        }

        return $result;
    }

	/**
	 *  資料ファイル更新メール配信処理
     * @param   array   $mailData     メール情報
     * @param   object  $noticeFiles  お知らせファイル情報
     * @param   string  $documentPath 資料ファイル保存パス
     * @param   int     $memberId     登録メンバーID
     * @param   string  $errorMsg     エラーメッセージ
	 * @return bool
	 */
    private function document_files_upload_mail_proc(array $mailData, $noticeFiles, string $documentPath, int $memberId, string &$errorMsg): bool
    {
        $result = false;
        $noticeSeqNo = 0;
        $mailBccList = array();

        if (is_array($noticeFiles) === true) {
            $noticeFiles = $noticeFiles[0] ?? reset($noticeFiles);
        }

        if ($noticeFiles instanceof UploadedFile === false) {
            $errorMsg = '資料ファイルの形式が正しくありません。';
            return false;
        }
        
        $noticeInfoId = 0;
        $noticeCategoryId = NOTICE_CATEGORY_ID_KASUGAI;
        $noticeTitle = $mailData['document_title'];
        $noticeBody = $mailData['document_body'];

        // お知らせ基本データ登録
        $result = $this->noticeModel->insert_notice_info($noticeCategoryId, $noticeTitle, $noticeBody, DB_FLG_OFF, DB_FLG_OFF, '', $memberId, $noticeInfoId);
        
        if ($result === true) {
            // ファイル情報
            $documentName = $noticeFiles->getName();
            $documentExt = $noticeFiles->getClientExtension();
            // ファイル登録
            $result = $this->noticeModel->insert_document_notice($noticeInfoId, 1, $documentName, $documentExt, $documentPath);
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
