<?php

namespace App\Libraries\Admin;
use App\Models\AdminModel;
use App\Models\ShinsaModel;
use App\Models\Admin\AdminShinsaModel;

class AdminShinsaLibrarie
{
	private $adminModel;
	private $shinsaModel;
    private $adminShinsaModel;
    protected $_session;
    protected $errorMessage = "";

	public function __construct(){
		$this->adminModel = model(AdminModel::class);
		$this->shinsaModel = model(ShinsaModel::class);
        $this->adminShinsaModel = model(AdminShinsaModel::class);
        $this->_session = session();
	}

    /**
     * 審査資料ID採番
     * @param   int     $shinsaId   審査ID
     * @return int
     */
    private function get_next_document_id(int $shinsaId): int
    {
        $nextDocumentId = 1;
        $documentList = array();
        
        $maxDocumentId = $this->adminShinsaModel->get_max_document_id($shinsaId);

        // 資料ID採番
        if ($maxDocumentId > 0) {
            $nextDocumentId = $maxDocumentId + 1;
        }
        return $nextDocumentId;
    }

    /**
     * 審査会場一覧情報取得
     * @param int $shinsaClassId    審査区分ID
     * @param int $areaGroupId      地域グループID
     * @return array 会場一覧情報
     */
    public function get_shinsa_kaijo_list($shinsaClassId, $areaGroupId=0): array
    {
        return $this->adminShinsaModel->get_shinsa_kaijo_list($shinsaClassId, $areaGroupId);
    }

    /**
     * 審査種別一覧情報取得
     * @param int $shinsaClassId 審査区分ID    
     * @return array 会場一覧情報
     */
    public function get_shinsa_shubetsu_list($shinsaClassId): array
    {
        return $this->adminShinsaModel->get_shinsa_shubetsu_list($shinsaClassId);
    }

    /**
     * 審査添付ファイル情報取得
     * @param   int     $shinsaId   審査ID
     * @return array
     */
    public function get_shinsa_document_list(int $shinsaId): array
    {
        return $this->shinsaModel->get_shinsa_document_list($shinsaId);
    }

    /**
     * 審査対象者一覧情報取得
     * @param int $fiscalYearId     年度ID
     * @param int $shinsaId         審査ID
     * @param int $shinsaTargetId   審査種別ID
     * @return array 審査対象者一覧情報
     */
    public function get_shinsa_target_member_list(int $fiscalYearId, int $shinsaId, int $shinsaTargetId): array
    {
        return $this->adminShinsaModel->get_shinsa_target_member_list($fiscalYearId, $shinsaId, $shinsaTargetId);
    }

	/**
	 * 審査データ登録・更新処理
     * @param   int     $fiscalYearId   年度ID
     * @param   array   $shinsaData     審査情報
     * @param   int     $memberId       登録メンバーID
     * @param   int     &$shinsaId      審査ID
	 * @return bool
	 */
    public function shinsa_info_proc(int $fiscalYearId, array $shinsaData, int $memberId, int &$shinsaId): bool
    {
        $result = false;
        $shinsaDateTargetList = array();
        $shinsaKaijoList = array();

        // トランザクション開始
        $this->adminModel->trans_start();

        // カテゴリーIDセット
        switch ($shinsaData['shinsa_class_id']) {
            case SHINSA_CLASS_ID_CHUOU:
                $shinsaData['category_id'] = CATEGORY_ID_SHINSA_CHUOU;
                break;
            case SHINSA_CLASS_ID_RENGO:
                $shinsaData['category_id'] = CATEGORY_ID_SHINSA_RENGO;
                break;
            case SHINSA_CLASS_ID_CHIHO:
                $shinsaData['category_id'] = CATEGORY_ID_SHINSA_CHIHO;
                break;
        }

        // 未定：特設会場クリア
        // if (empty($shinsaData['kaijo_id_1']) === true) {
        //     $shinsaData['kaijo_other_name'] = null;
        // }
        // 未定：愛弓連申込期間クリア
        if ($shinsaData['uketuke_limit_aikyuren_set'] === FLG_OFF) {
            $shinsaData['uketuke_limit_aikyuren_st'] = null;
            $shinsaData['uketuke_limit_aikyuren_ed'] = null;
        }

        // 審査日程リスト
        for ($i = 1; $i <= 3; $i++) {
            if (empty($shinsaData['shinsa_date_' . $i]) === false) {
                $shinsaDateTargetList[] = array(
                    'shinsa_date' => $shinsaData['shinsa_date_' . $i],
                    'holder_grade_id' => $shinsaData['holder_grade_id_' . $i]
                );
            }
        }

        // 会場リスト
        for ($i = 1; $i <= 3; $i++) {
            if (empty($shinsaData['kaijo_id_' . $i]) === false) {
                $shinsaKaijoList[] = array(
                    'kaijo_id' => $shinsaData['kaijo_id_' . $i],
                    'additional_info' => $shinsaData['additional_info_' . $i]
                );
            }
        }

        // 審査情報登録・更新
        if ($shinsaData['regist_mode'] === MODE_REVISION) {
            // 更新：審査基本情報
            $result = $this->adminShinsaModel->update_shinsa_info($shinsaData);
            if ($result === false) {
                $this->errorMessage = '審査情報の更新ができませんでした';
            } else {
                // 削除：審査日程情報
                $result = $this->adminShinsaModel->delete_shinsa_date_target($shinsaData['shinsa_id']);
                if ($result === false) {
                    $this->errorMessage = '審査日程情報の削除ができませんでした';
                } else {
                    // 更新：審査日程情報
                    if (empty($shinsaDateTargetList) === false) {
                        foreach ($shinsaDateTargetList as $shinsaDateTarget) {
                            $result = $this->adminShinsaModel->insert_shinsa_date_target($shinsaData['shinsa_id'], $shinsaDateTarget);
                            if ($result === false) {
                                $this->errorMessage = '審査日程情報の登録ができませんでした';
                                break;
                            }
                        }
                    }
                }
                // 削除：審査会場情報
                $result = $this->adminShinsaModel->delete_shinsa_kaijo($shinsaData['shinsa_id']);
                if ($result === false) {
                    $this->errorMessage = '審査会場情報の削除ができませんでした';
                } else {
                    // 登録：審査会場情報
                    if (empty($shinsaKaijoList) === false) {
                        $orderNo = 1;
                        foreach ($shinsaKaijoList as $shinsaKaijo) {
                            $shinsaKaijo['order_no'] = $orderNo;
                            $result = $this->adminShinsaModel->insert_shinsa_kaijo($shinsaData['shinsa_id'], $shinsaKaijo);
                            if ($result === false) {
                                $this->errorMessage = '審査会場情報の登録ができませんでした';
                                break;
                            }
                            $orderNo++;
                        }
                    }
                }
            }
        } else {
            // 登録
            $result = $this->adminShinsaModel->insert_shinsa_info($fiscalYearId, $shinsaData, $shinsaId);
            if ($result === false) {
                $this->errorMessage = '審査情報の登録ができませんでした';
            } else {
                // 登録：審査日程情報
                if (empty($shinsaDateTargetList) === false) {
                    foreach ($shinsaDateTargetList as $shinsaDateTarget) {
                        $result = $this->adminShinsaModel->insert_shinsa_date_target($shinsaId, $shinsaDateTarget);
                        if ($result === false) {
                            $this->errorMessage = '審査日程情報の登録ができませんでした';
                            break;
                        }
                    }
                }
                // 登録：審査会場情報
                if (empty($shinsaKaijoList) === false) {
                    $orderNo = 1;
                    foreach ($shinsaKaijoList as $shinsaKaijo) {
                        $shinsaKaijo['order_no'] = $orderNo;
                        $result = $this->adminShinsaModel->insert_shinsa_kaijo($shinsaId, $shinsaKaijo);
                        if ($result === false) {
                            $this->errorMessage = '審査会場情報の登録ができませんでした';
                            break;
                        }
                        $orderNo++;
                    }
                }
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
	 * 審査添付ファイル登録処理
     * @param   int     $shinsaId       審査ID
     * @param   array   $shinsaData     ファイル情報
     * @param   array   $shinsaFiles    添付ファイル情報
	 * @return bool
	 */
    public function shinsa_files_proc(int $shinsaId, array $shinsaData, array $shinsaFiles) : bool
    {
        $result = true;
        // 添付ファイル登録
        $filesCnt = count($shinsaFiles);
        for ($i=1; $i<=$filesCnt; $i++) {
            $fileKey = 'shinsa_files' . $i;
            if (isset($shinsaFiles[$fileKey]) && $shinsaFiles[$fileKey]->isValid() && ! $shinsaFiles[$fileKey]->hasMoved()) {
                // ファイル情報登録
                $documentId = self::get_next_document_id($shinsaId);
                $documentName = $shinsaFiles[$fileKey]->getClientName();
                $documentExt = $shinsaFiles[$fileKey]->getClientExtension();
                $documentPath = '/uploads/shinsa/' . $shinsaId . '/';
                // ディレクトリ作成
                if (is_dir(FCPATH . $documentPath) === false) {
                    mkdir(FCPATH . $documentPath, 0755, true);
                }
                // ファイル移動
                $shinsaFiles[$fileKey]->move(FCPATH . $documentPath, $documentId . '.' . $documentExt);
                // 審査添付ファイル情報登録
                $insertResult = $this->adminShinsaModel->insert_document_shinsa(
                    $shinsaId,
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
     * 代理審査申請者登録
     * @param int       $shinsaId           審査ID
     * @param int       $shinsaTargetId     審査対象ID
     * @param int       $memberId           会員ID
     * @return bool
     */
    public function shinsa_add_member_proxy(int $shinsaId, int $shinsaTargetId, int $memberId): bool
    {
        $result = false;

        // トランザクション開始
        $this->adminModel->trans_start();

        // 審査申請登録
        $result = $this->shinsaModel->shinsa_join_member($shinsaId, $shinsaTargetId, $memberId);
        if ($result === false) {
            $this->errorMessage = '審査申請登録ができませんでした';
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
     * 代理審査キャンセル登録
     * @param int     $shinsaId   審査ID
     * @param int     $memberId   会員ID
     * @return 
     */
    public function shinsa_cancel_member_proxy(int $shinsaId, int $memberId): bool
    {
        $result = false;

        // トランザクション開始
        $this->adminModel->trans_start();

        // 大会キャンセル登録
        $result = $this->shinsaModel->shinsa_cancel_member($shinsaId, $memberId);

        // トランザクション完了
        $this->adminModel->trans_complete();

        // 失敗時はロールバックされる
        if ($this->adminModel->trans_status() === false) {
            $result = false;
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
