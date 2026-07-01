<?php

namespace App\Libraries\Admin;
use App\Libraries\Admin\AdminLibrarie;
use App\Models\AdminModel;
use App\Models\NoticeModel;
use App\Models\ShinsaModel;
use App\Models\TaikaiModel;
use App\Models\SeminarModel;
use App\Models\Admin\AdminKyokaiModel;
use App\Models\Admin\AdminShinsaModel;
use App\Models\Admin\AdminTaikaiModel;
use App\Models\Admin\AdminSeminarModel;
use CodeIgniter\HTTP\Files\UploadedFile;

class AdminNoticeLibrarie
{
    private $adminLibrarie;
    private $adminModel;
    private $noticeModel;
    private $shinsaModel;
    private $taikaiModel;
    private $seminarModel;
    private $adminKyokaiModel;
    private $adminShinsaModel;
    private $adminTaikaiModel;
	private $adminSeminarModel;
    private $_session;
    private $errorMessage = "";

	public function __construct(){
		$this->adminLibrarie = new AdminLibrarie();
		$this->adminModel = model(AdminModel::class);
		$this->noticeModel = model(NoticeModel::class);
        $this->shinsaModel = model(ShinsaModel::class);
        $this->taikaiModel = model(TaikaiModel::class);
        $this->seminarModel = model(SeminarModel::class);
        $this->adminKyokaiModel = model(AdminKyokaiModel::class);
        $this->adminShinsaModel = model(AdminShinsaModel::class);
        $this->adminTaikaiModel = model(AdminTaikaiModel::class);
        $this->adminSeminarModel = model(AdminSeminarModel::class);
        $this->_session = session();
	}

    /**
     * お知らせ基本データ登録・更新処理
     * @param   array   $noticeData     お知らせ情報
     * @param   array   $noticeFiles    添付ファイル情報
     * @param   int     $memberId       登録メンバーID
     * @param   int     $fiscalYearId   年度ID
     * @return bool
     */
    public function notice_info_proc(array $noticeData, $noticeFiles, int $memberId, int $fiscalYearId): bool
    {
        $result = false;

        // トランザクション開始
        $this->adminModel->trans_start();
        
        $noticeInfoId = $noticeData['notice_info_id'];
        $noticeCategoryId = $noticeData['notice_category_id'];
        $noticeTitle = $noticeData['notice_title'];
        $noticeBody = $noticeData['notice_body'];
        $relationEventId = $noticeData['relation_event_id'];
        $relationEventName = $noticeData['relation_event_name'];
        $registMode = $noticeData['regist_mode'];

        if (empty($noticeInfoId) === true) {
            $noticeInfoId = 0;
        }
        if (empty($relationEventId) === true) {
            $relationEventId = 0;
        }
        if ($relationEventName === "選択してください") {
            $relationEventName = "";
        }
        
        // 関連メニューID
        $relationMenuId = 0;
        if (empty($relationEventId) === false) {
            switch ($noticeCategoryId) {
                case NOTICE_CATEGORY_ID_KASUGAI :
                    $relationMenuId = MENU_ID_KYOKAI;
                    break;
                case NOTICE_CATEGORY_ID_SHINSA :
                    $relationMenuId = MENU_ID_SHINSA;
                    break;
                case NOTICE_CATEGORY_ID_TAIKAI :
                    $relationMenuId = MENU_ID_TAIKAI;
                    break;
                case NOTICE_CATEGORY_ID_SEMINAR :
                    $relationMenuId = MENU_ID_SEMINAR;
                    break;
                case NOTICE_CATEGORY_ID_TRAINING :
                    $relationMenuId = MENU_ID_KYOKAI;
                    break;
                case NOTICE_CATEGORY_ID_OTHER :
                    $relationMenuId = MENU_ID_KYOKAI;
                    break;
            }
        }
        
        switch ($registMode) {
            case MODE_REGIST :
                // お知らせ基本データ登録
                $result = $this->noticeModel->insert_notice_info($noticeCategoryId, $noticeTitle, $noticeBody, $relationMenuId, $relationEventId, $relationEventName, $memberId, $noticeInfoId);
                break;
            case MODE_REVISION :
                // お知らせ基本データ更新
                $result = $this->noticeModel->update_notice_info($noticeInfoId, $noticeCategoryId, $noticeTitle, $noticeBody, $relationMenuId, $relationEventId, $relationEventName, $memberId);
                break;
        }
        
        // 関連イベントの資料情報更新
        if ($result === true) {
            // ファイル登録
            $result = $this->notice_files_proc($noticeInfoId, $noticeData, $noticeFiles);
            if ($result === true) {
                if ($registMode === MODE_REGIST) {
                    // メール配信：新規登録時のみ
                    $this->notice_mail_proc($noticeData, $noticeFiles, $fiscalYearId);
                }
            }
        } else {
            $this->errorMessage = 'お知らせ基本データの登録・更新に失敗しました';
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
     * 添付ファイル情報取得
     * @param   int     $noticeInfoId   お知らせID
     * @return array
     */
    public function get_notice_document_list(int $noticeInfoId): array
    {
        $existingNoticeDocumentList = array();

        // お知らせ添付資料リスト取得
        $noticeDocumentList = $this->noticeModel->get_notice_document_list($noticeInfoId);
        if ($noticeDocumentList !== false && $noticeDocumentList['numRows'] > 0) {
            foreach ($noticeDocumentList['result'] as $key => $file) {
                $fileExtPath = get_file_ext_icon_path($noticeDocumentList['result'][$key]['document_ext']);
                $existingNoticeDocumentList[] = array(
                    'file_name' => $noticeDocumentList['result'][$key]['document_name'],
                    'file_ext_path' => $fileExtPath
                );
            }
        }
        
        return $existingNoticeDocumentList;
    }

    /**
     * 関連する添付ファイル情報取得
     * @param   int     $noticeCategoryId   お知らせカテゴリーID
     * @param   int     $relationEventId    関連イベントID
     * @return array
     */
    public function get_relation_document_list(int $noticeCategoryId, int $relationEventId): array
    {
        $documentList = array();

        if (empty($relationEventId) === false) {
            // 関連イベントの添付資料リスト取得
            switch ($noticeCategoryId) {
                case NOTICE_CATEGORY_ID_KASUGAI :
                    $documentList = $this->kyokaiModel->get_event_document_list($relationEventId);
                    break;
                case NOTICE_CATEGORY_ID_SHINSA :
                    $documentList = $this->shinsaModel->get_shinsa_document_list($relationEventId);
                    break;
                case NOTICE_CATEGORY_ID_TAIKAI :
                    $documentList = $this->taikaiModel->get_taikai_document_list($relationEventId);
                    break;
                case NOTICE_CATEGORY_ID_SEMINAR :
                    $documentList = $this->seminarModel->get_seminar_document_list($relationEventId);
                    break;
                case NOTICE_CATEGORY_ID_TRAINING :
                    $documentList = $this->kyokaiModel->get_event_document_list($relationEventId);
                    break;
                case NOTICE_CATEGORY_ID_OTHER :
                    $documentList = $this->kyokaiModel->get_event_document_list($relationEventId);
                    break;
            }
            if (empty($documentList) === false) {
                for ($i=0; $i<$documentList['numRows']; $i++) {
                    $documentList['result'][$i]['ext_file'] = get_file_ext_icon_path($documentList['result'][$i]['document_ext']);
                }
            }
        }
        
        return $documentList;
    }

    /**
     * お知らせ添付ファイル登録処理
     * @param   int     $noticeInfoId   お知らせID
     * @param   array   $noticeData     ファイル情報
     * @param   array   $noticeFiles    添付ファイル情報
     * @return bool
     */
    private function notice_files_proc(int $noticeInfoId, array $noticeData, $noticeFiles): bool
    {
        $result = true;

        if ($noticeFiles instanceof UploadedFile) {
            $noticeFiles = [$noticeFiles];
        } elseif (is_array($noticeFiles) === true && array_key_exists('name', $noticeFiles) && is_string($noticeFiles['name'])) {
            $noticeFiles = [$noticeFiles];
        }

        if (is_array($noticeFiles) === false) {
            return false;
        }

        // アップロード先のフォルダ名取得
        $noticeCategoryId = $noticeData['notice_category_id'];
        $categoryNoticeDetail = $this->noticeModel->get_category_notice_detail($noticeCategoryId);
        $uploadCategoryDir = $categoryNoticeDetail['file_directory'];
        $relationEventId = $noticeData['relation_event_id'];
        $beforeRelationEventId = $noticeData['set_relation_event_id'];

        if (empty($uploadCategoryDir) === false) {

            // 既存の関連イベントファイル情報削除
            switch ($noticeCategoryId) {
                case NOTICE_CATEGORY_ID_KASUGAI :
                    $this->adminKyokaiModel->delete_relation_notice_document_event($noticeInfoId);
                    break;
                case NOTICE_CATEGORY_ID_SHINSA :
                    $this->adminShinsaModel->delete_relation_notice_document_shinsa($noticeInfoId);
                    break;
                case NOTICE_CATEGORY_ID_TAIKAI :
                    $this->adminTaikaiModel->delete_relation_notice_document_taikai($noticeInfoId);
                    break;
                case NOTICE_CATEGORY_ID_SEMINAR :
                    $this->adminSeminarModel->delete_relation_notice_document_seminar($noticeInfoId);
                    break;
                case NOTICE_CATEGORY_ID_TRAINING :
                    $this->adminKyokaiModel->delete_relation_notice_document_event($noticeInfoId);
                    break;
                case NOTICE_CATEGORY_ID_OTHER :
                    $this->adminKyokaiModel->delete_relation_notice_document_event($noticeInfoId);
                    break;
            }

            // 関連イベントが変わった場合、既存の添付ファイル情報を関連イベントから削除
            if (empty($relationEventId) === false && $beforeRelationEventId != $relationEventId) {
                // 既存の関連イベントファイル情報更新
                switch ($noticeCategoryId) {
                    case NOTICE_CATEGORY_ID_KASUGAI :
                        $this->adminKyokaiModel->insert_notice_rerlation_document_event($relationEventId, $noticeInfoId);
                        break;
                    case NOTICE_CATEGORY_ID_SHINSA :
                        $this->adminShinsaModel->insert_notice_rerlation_document_shinsa($relationEventId, $noticeInfoId);
                        break;
                    case NOTICE_CATEGORY_ID_TAIKAI :
                        $this->adminTaikaiModel->insert_notice_rerlation_document_taikai($relationEventId, $noticeInfoId);
                        break;
                    case NOTICE_CATEGORY_ID_SEMINAR :
                        $this->adminSeminarModel->insert_notice_rerlation_document_seminar($relationEventId, $noticeInfoId);
                        break;
                    case NOTICE_CATEGORY_ID_TRAINING :
                        $this->adminKyokaiModel->insert_notice_rerlation_document_event($relationEventId, $noticeInfoId);
                        break;
                    case NOTICE_CATEGORY_ID_OTHER :
                        $this->adminKyokaiModel->insert_notice_rerlation_document_event($relationEventId, $noticeInfoId);
                        break;
                }
            }

            // アップロード先ディレクトリ
            $uploadDir = FCPATH . UPLOAD_FILE_DIR . '/' . $uploadCategoryDir . '/' . $noticeInfoId;

            // 追加の添付ファイル登録
            if (empty($noticeFiles) === false) {
                // 既存の添付ファイル情報取得
                $noticeDocumentList = $this->get_notice_document_list($noticeInfoId);
                $documentId = 1;
                if (empty($noticeDocumentList) === false && $noticeDocumentList['numRows'] > 0) {
                    // 資料ID採番
                    $documentId = $noticeDocumentList['numRows'] + 1;
                }
                foreach ($noticeFiles as $key => $files) {
                    if ($files instanceof UploadedFile === false) {
                        continue;
                    }

                    // ファイル保存
                    $fileName = $files->getName();
                    $fileExt = $files->getExtension();
                    if ($files->move($uploadDir, $fileName) === true) {
                        // お知らせファイル情報登録
                        $documentPath = '/' . UPLOAD_FILE_DIR . '/' . $uploadCategoryDir . '/' . $noticeInfoId . '/' . $fileName;
                        $result = $this->noticeModel->insert_document_notice($noticeInfoId, $documentId, $fileName, $fileExt, $documentPath);
                        // 関連イベントファイル情報登録
                        if ($relationEventId > 0) {
                            $nextDocumentId = $this->get_next_document_id($noticeCategoryId, $relationEventId);
                            switch ($noticeCategoryId) {
                                case NOTICE_CATEGORY_ID_KASUGAI :
                                    $this->adminKyokaiModel->insert_document_event($relationEventId, $nextDocumentId, DB_FLG_OFF, $fileName, $fileExt, $documentPath);
                                    break;
                                case NOTICE_CATEGORY_ID_SHINSA :
                                    $this->adminShinsaModel->insert_document_shinsa($relationEventId, $nextDocumentId, DB_FLG_OFF, $fileName, $fileExt, $documentPath);
                                    break;
                                case NOTICE_CATEGORY_ID_TAIKAI :
                                    $this->adminTaikaiModel->insert_document_taikai($relationEventId, $nextDocumentId, DB_FLG_OFF, $fileName, $fileExt, $documentPath);
                                    break;
                                case NOTICE_CATEGORY_ID_SEMINAR :
                                    $this->adminSeminarModel->insert_document_seminar($relationEventId, $nextDocumentId, DB_FLG_OFF, $fileName, $fileExt, $documentPath);
                                    break;
                                case NOTICE_CATEGORY_ID_TRAINING :
                                    $this->adminKyokaiModel->insert_document_event($relationEventId, $nextDocumentId, DB_FLG_OFF, $fileName, $fileExt, $documentPath);
                                    break;
                                case NOTICE_CATEGORY_ID_OTHER :
                                    $this->adminKyokaiModel->insert_document_event($relationEventId, $nextDocumentId, DB_FLG_OFF, $fileName, $fileExt, $documentPath);
                                    break;
                            }
                        }
                    } else {
                        session()->setFlashdata('msg', 'アップロードに失敗しました。');
                    }
                    $documentId++;
                }
            }
        }

        return $result;
    }

    /**
     * 資料ID採番
     * @param   int     $noticeCategoryId   お知らせカテゴリーID
     * @return int
     */
    private function get_next_document_id(int $noticeCategoryId, int $relationEventId): int
    {
        $nextDocumentId = 1;
        $maxDocumentId = 0;

        switch ($noticeCategoryId) {
            case NOTICE_CATEGORY_ID_KASUGAI :
                $maxDocumentId = $this->adminKyokaiModel->get_max_document_id($relationEventId);
                break;
            case NOTICE_CATEGORY_ID_SHINSA :
                $maxDocumentId = $this->adminShinsaModel->get_max_document_id($relationEventId);
                break;
            case NOTICE_CATEGORY_ID_TAIKAI :
                $maxDocumentId = $this->adminTaikaiModel->get_max_document_id($relationEventId);
                break;
            case NOTICE_CATEGORY_ID_SEMINAR :
                $maxDocumentId = $this->adminSeminarModel->get_max_document_id($relationEventId);
                break;
            case NOTICE_CATEGORY_ID_TRAINING :
                $maxDocumentId = $this->adminKyokaiModel->get_max_document_id($relationEventId);
                break;
            case NOTICE_CATEGORY_ID_OTHER :
                $maxDocumentId = $this->adminKyokaiModel->get_max_document_id($relationEventId);
                break;
        }
        // 資料ID採番
        if ($maxDocumentId > 0) {
            $nextDocumentId = $maxDocumentId + 1;
        }
        return $nextDocumentId;
    }

    /**
     * お知らせ削除処理（論理削除）
     * @param   int   $notice_info_id   お知らせID
     * @param   int   $memberId         登録メンバーID
     * @return bool
     */
    public function delete_notice_info(int $notice_info_id, int $memberId): bool
    {
        $result = false;

        // お知らせ情報削除
        $result = $this->noticeModel->delete_notice_info($notice_info_id, $memberId);

        return $result;
    }

    /**
     * お知らせ資料削除処理
     * @param   int   $notice_info_id   お知らせID
     * @param   int   $document_id      添付ファイルID
     * @return bool
     */
    public function delete_notice_document(int $notice_info_id, int $document_id): bool
    {
        $result = false;

        // お知らせ資料詳細情報取得
        $noticeDocumentDetail = $this->noticeModel->get_notice_document_detail($notice_info_id, $document_id);
        if (empty($noticeDocumentDetail) === false) {

            // 削除ファイルパス
            $deleteFilePath = FCPATH . $noticeDocumentDetail['document_path'];
            if (file_exists($deleteFilePath) === true) {
                // データ削除
                $result = $this->noticeModel->delete_notice_document($notice_info_id, $document_id);
                if ($result === true) {
                    // ファイル削除
                    $result = @unlink($deleteFilePath);
                    if ($result === false) {
                        session()->setFlashdata('msg', 'お知らせ資料の削除に失敗しました。');
                    }
                } else {
                    session()->setFlashdata('msg', 'お知らせ資料情報の削除に失敗しました。');
                }
            }
        }

        return $result;
    }

    /**
     * お知らせメール配信処理
     * @param   array   $noticeData     ファイル情報
     * @param   array   $noticeFiles    添付ファイル情報
     * @param   int     $fiscalYearId   年度ID
     * @return bool
     */
    private function notice_mail_proc(array $noticeData, array $noticeFiles, int $fiscalYearId): bool
    {
        $result = false;
        $noticeSeqNo = 0;
        $noticeMailMemberList = array();
        $mailBccList = array();

        // メール配信対象者リスト取得
        if (empty($noticeData['set_relation_event_id']) == false) {
            $eventId = $noticeData['set_relation_event_id'];
            // 関連イベントがある場合は、お知らせカテゴリーに応じたメール配信対象者リストを取得
            switch ($noticeData['set_notice_category_id']) {
                case NOTICE_CATEGORY_ID_KASUGAI :
                    $noticeMailMemberList = $this->kyokaiModel->get_event_offer_member_list($eventId);
                    break;
                case NOTICE_CATEGORY_ID_SHINSA :
                    $noticeMailMemberList = $this->shinsaModel->get_shinsa_offer_member_list($eventId);
                    break;
                case NOTICE_CATEGORY_ID_TAIKAI :
                    $noticeMailMemberList = $this->taikaiModel->get_taikai_offer_member_list($eventId);
                    break;
                case NOTICE_CATEGORY_ID_SEMINAR :
                    $noticeMailMemberList = $this->kyokaiModel->get_event_offer_member_list($eventId);
                    break;
                case NOTICE_CATEGORY_ID_TRAINING :
                    $noticeMailMemberList = $this->kyokaiModel->get_event_offer_member_list($eventId);
                    break;
                case NOTICE_CATEGORY_ID_OTHER :
                    $noticeMailMemberList = $this->kyokaiModel->get_event_offer_member_list($eventId);
                    break;
            }
        } else {
            // 関連イベントがない場合、全会員に配信
            $noticeMailMemberList = $this->noticeModel->get_notice_mail_member_list();
        }
        if (empty($noticeMailMemberList) === true) {
            // メール配信対象者なし
            return $result;
        }

        // メール配信対象者のメールアドレスをBCCリストにセット
        foreach ($noticeMailMemberList['result'] as $idx => $data) {
            // メールアドレスがある会員のみBCCリストにセット
            if (empty($data['mail_address']) === false) {
                $mailBccList[] = $data['mail_address'];
            }
        }

        // お知らせカテゴリー情報取得
        $categoryNoticeDetail = $this->noticeModel->get_category_notice_detail($noticeData['notice_category_id']);
        
        $noticeCategoryId = $noticeData['notice_category_id'];
        $noticeTitle = $noticeData['notice_title'];
        $noticeBody = $noticeData['notice_body'];
        
        if (empty($noticeFiles) === false) {
            $noticeBody .= "\n\n" . '添付資料あり';
        }
        $noticeBody .= "\n\n" . '詳細は【' . KASUGAI_KYOKAI_NAME . '】でご確認ください'. "\n";
        $noticeBody .= SITE_URL;
        
        // お知らせシーケンスNo取得
        $result = $this->noticeModel->get_notice_seq_no($fiscalYearId, $noticeCategoryId, $noticeSeqNo);
        if ($result === true) {
            
            // メールタイトル
            $noticeTitle = $noticeTitle.' 【お知らせ投稿：' . $categoryNoticeDetail['notice_category_name'] . ' '  . $fiscalYearId . '-' . $noticeSeqNo . '】';

            // メール配信
            $this->adminLibrarie->send_mail_proc($mailBccList, $noticeTitle, $noticeBody);
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
