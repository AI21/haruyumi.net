<?php

namespace App\Libraries;
use App\Models\NoticeModel;

class NoticeLibrarie
{
	private $noticeModel;
    protected $errorMessage = "";

	public function __construct(){
		$this->noticeModel = model(NoticeModel::class);
	}

    /**
     * お知らせカテゴリー 一覧情報取得
     * @return array お知らせカテゴリー 一覧情報
     */
    public function get_category_notice_list(array $noticeAdminIdList): array
    {
        $result = array();
		$result = $this->noticeModel->get_category_notice_list($noticeAdminIdList);
		
        return $result;
    }

    /**
     * お知らせ一覧情報取得
     * @return array お知らせ一覧情報
     */
    public function get_notice_list($noticeCategoryId=NULL): array
    {
        $result = array();
		$result = $this->noticeModel->get_notice_list($noticeCategoryId);
		
        return $result;
    }

    /**
     * 関連するお知らせ一覧情報取得
     * @param int $relationMenuId 関連メニューID
     * @param int $relationEventId 関連イベントID
     * @return array 関連するお知らせ一覧情報
     */
    public function get_relation_notice_list($relationMenuId, $relationEventId): array
    {
        $result = array();
		$result = $this->noticeModel->get_relation_notice_list($relationMenuId, $relationEventId);
		
        return $result;
    }

    /**
     * お知らせ詳細情報取得
     * @return array お知らせ詳細情報
     */
    public function get_notice_detail(int $noticeInfoId, int $memberId): array
    {
        $result = array();

        // お知らせ詳細情報取得
		$noticeDetail = $this->noticeModel->get_notice_detail($noticeInfoId, $memberId);
        if (empty($noticeDetail) === false) {
            $result['notice_detail'] = $noticeDetail;
        }
        
        // お知らせ添付資料リスト取得
		$noticeDocumentList = $this->noticeModel->get_notice_document_list($noticeInfoId);
        if (empty($noticeDocumentList) === false) {
            for ($i=0; $i<$noticeDocumentList['numRows']; $i++) {
                $noticeDocumentList['result'][$i]['ext_file'] = get_file_ext_icon_path($noticeDocumentList['result'][$i]['document_ext']);
            }
            $result['notice_document_list'] = $noticeDocumentList;
        }

        return $result;
    }
}
