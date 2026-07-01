<?php

namespace App\Libraries\Admin;
use App\Models\AdminModel;
use App\Models\TaikaiModel;
use App\Models\Admin\AdminTaikaiModel;

class AdminTaikaiLibrarie
{
	private $adminModel;
	private $taikaiModel;
    private $adminTaikaiModel;
    protected $_session;
    protected $errorMessage = "";

	public function __construct(){
		$this->adminModel = model(AdminModel::class);
		$this->taikaiModel = model(TaikaiModel::class);
        $this->adminTaikaiModel = model(AdminTaikaiModel::class);
        $this->_session = session();
	}

    /**
     * 大会添付ファイル情報取得
     * @param   int     $taikaiId   大会ID
     * @return array
     */
    public function get_taikai_document_list(int $taikaiId): array
    {
        return $this->taikaiModel->get_taikai_document_list($taikaiId);
    }

    /**
     * 大会資料ID採番
     * @param   int     $taikaiId   大会ID
     * @return int
     */
    private function get_next_document_id(int $taikaiId): int
    {
        $nextDocumentId = 1;
        $documentList = array();
        
        $maxDocumentId = $this->adminTaikaiModel->get_max_document_id($taikaiId);

        // 資料ID採番
        if ($maxDocumentId > 0) {
            $nextDocumentId = $maxDocumentId + 1;
        }
        return $nextDocumentId;
    }

	/**
	 * 大会データ登録・更新処理
     * @param   array   $taikaiData     大会情報
     * @param   int     $memberId       登録メンバーID
     * @param   int     &$taikaiInfoId  大会ID
	 * @return bool
	 */
    public function taikai_info_proc(array $taikaiData, int $memberId, int &$taikaiInfoId): bool
    {
        $result = false;

        // トランザクション開始
        $this->adminModel->trans_start();

        // 未定：大会別名クリア
        if ($taikaiData['taikai_name_set'] === FLG_OFF) {
            $taikaiData['taikai_sub_name'] = null;
        }
        // 未定：特設会場クリア
        if ($taikaiData['kaijo_other_name_set'] === FLG_OFF) {
            $taikaiData['kaijo_other_name'] = null;
        }
        // 未定：開場時間クリア
        if ($taikaiData['taikai_open_time_set'] === FLG_OFF) {
            $taikaiData['taikai_open_time'] = null;
        }
        // 未定：受付時間クリア
        if ($taikaiData['taikai_uketuke_time_set'] === FLG_OFF) {
            $taikaiData['taikai_uketuke_time'] = null;
        }
        // 未定：大会時間クリア
        if ($taikaiData['taikai_time_set'] === FLG_OFF) {
            $taikaiData['taikai_time_st'] = null;
            $taikaiData['taikai_time_ed'] = null;
        }
        // 未定：大会受付期間クリア
        if ($taikaiData['taikai_uketuke_set'] === FLG_OFF) {
            $taikaiData['taikai_uketuke_st'] = null;
            $taikaiData['taikai_uketuke_ed'] = null;
        }
        // 未定：年齢制限クリア
        if ($taikaiData['age_limit_set'] === FLG_OFF) {
            $taikaiData['age_limit_min'] = null;
            $taikaiData['age_limit_max'] = null;
        }

        // 大会情報登録・更新
        if ($taikaiData['regist_mode'] === MODE_REVISION) {
            // 更新
            $result = $this->adminTaikaiModel->update_taikai_info($taikaiData);
            if ($result === false) {
                $this->errorMessage = '大会情報の更新ができませんでした';
            } else {
                $taikaiInfoId = $taikaiData['taikai_id'];
            }
        } else {
            // 登録
            $taikaiInfoId = $this->adminTaikaiModel->insert_taikai_info($taikaiData, $memberId);
            if ($taikaiInfoId === 0) {
                $this->errorMessage = '大会情報の登録ができませんでした';
            } else {
                $result = true;
            }
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
	 * 大会添付ファイル登録処理
     * @param   int     $taikaiId   大会ID
     * @param   array   $taikaiData     ファイル情報
     * @param   array   $taikaiFiles    添付ファイル情報
	 * @return bool
	 */
    public function taikai_files_proc(int $taikaiId, array $taikaiData, array $taikaiFiles) : bool
    {
        $result = true;
        // 添付ファイル登録
        $filesCnt = count($taikaiFiles);
        for ($i=1; $i<=$filesCnt; $i++) {
            $fileKey = 'taikai_files' . $i;
            if (isset($taikaiFiles[$fileKey]) && $taikaiFiles[$fileKey]->isValid() && ! $taikaiFiles[$fileKey]->hasMoved()) {
                // ファイル情報登録
                $documentId = self::get_next_document_id($taikaiId);
                $documentName = $taikaiFiles[$fileKey]->getClientName();
                $documentExt = $taikaiFiles[$fileKey]->getClientExtension();
                $documentPath = '/uploads/taikai/' . $taikaiId . '/';
                // ディレクトリ作成
                if (is_dir(FCPATH . $documentPath) === false) {
                    mkdir(FCPATH . $documentPath, 0755, true);
                }
                // ファイル移動
                $taikaiFiles[$fileKey]->move(FCPATH . $documentPath, $documentId . '.' . $documentExt);
                // 大会添付ファイル情報登録
                $insertResult = $this->adminTaikaiModel->insert_document_taikai(
                    $taikaiId,
                    $documentId,
                    DB_FLG_OFF,
                    $documentName,
                    $documentExt,
                    $documentPath . $documentId . '.' . $documentExt
                );
                if ($insertResult === false) {
                    $result = false;;
                }
            }
        }
        return $result;
    }

    /**
     * 代理大会参加者登録
     * @param int       $taikaiId      大会ID
     * @param string    $memberIdList  会員IDリスト（カンマ区切り）
     * @return 
     */
    public function taikai_add_member_proxy(int $taikaiId, string $memberIdList): bool
    {
        $result = false;

        // トランザクション開始
        $this->adminModel->trans_start();

        // カンマ区切りで配列に変換
        $memberIdArray = explode(',', $memberIdList);

        // 大会参加登録
        foreach ($memberIdArray as $idx => $memberId) {
            $result = $this->taikaiModel->taikai_join_member($taikaiId, $memberId);
            if ($result === false) {
                $this->errorMessage = '大会参加登録ができませんでした';
                break;
            }
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
     * 代理大会キャンセル登録
     * @param int       $taikaiId   大会ID
     * @param int     $memberId   会員ID
     * @return 
     */
    public function taikai_cancel_member_proxy(int $taikaiId, int $memberId): bool
    {
        $result = false;

        // 大会キャンセル登録
        $result = $this->taikaiModel->taikai_cancel_member($taikaiId, $memberId);
		
        return $result;
    }

	/**
	 * 大会資料削除処理
     * @param   int   $taikai_id   大会ID
     * @param   int   $document_id      添付ファイルID
	 * @return bool
	 */
    public function delete_taikai_document(int $taikai_id, int $document_id): bool
    {
        $result = false;

        // 大会資料詳細情報取得
        $taikaiDocumentDetail = $this->taikaiModel->get_taikai_document_detail($taikai_id, $document_id);
        if (empty($taikaiDocumentDetail) === false) {

            // 削除ファイルパス
            $deleteFilePath = FCPATH . $taikaiDocumentDetail['document_path'];
            if (file_exists($deleteFilePath) === true) {
                // データ削除
                $result = $this->adminTaikaiModel->delete_taikai_document($taikai_id, $document_id);
                if ($result === true) {
                    // ファイル削除
                    $result = @unlink($deleteFilePath);
                    if ($result === false) {
                        session()->setFlashdata('msg', '大会資料の削除に失敗しました。');
                    }
                } else {
                    session()->setFlashdata('msg', '大会資料情報の削除に失敗しました。');
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
