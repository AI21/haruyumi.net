<?php

namespace App\Libraries;
use App\Models\DocumentModel;
use App\Libraries\MemberLibrarie;

class DocumentLibrarie
{
	private $documentModel;
    protected $errorMessage = "";

	public function __construct(){
		$this->documentModel = model(DocumentModel::class);
		$this->memberLibrarie = new MemberLibrarie();
	}

    /**
     * 資料一覧情報取得
     * @param array $subMemuData        サブメニュー情報
     * @param int   $memberId           会員ID
     * @return array 資料一覧情報
     */
    public function get_document_list(array $subMemuData, int $memberId): array
    {
        $result = array();

        if (empty($subMemuData) === false) {
            if ($subMemuData['numRows'] > 0) {
                // カテゴリー毎の資料一覧情報を取得
                foreach ($subMemuData['result'] as $idx => $data) {
                    $result[$data['tab_name']] = $this->documentModel->get_document_list($data['category_id']);
                    switch ($data['category_id']) {
                        case CATEGORY_ID_KYOKAI :
                            $result[$data['tab_name']][DATA_OFFICER_FLG] = $this->memberLibrarie->chk_kyokai_officer_level(KYOKAI_OFFICER_ID_KASUGAI_kAICHO, $memberId, KYOKAI_OFFICER_LEVEL_BOSS);
                            break;
                        case CATEGORY_ID_USEGYM :
                            $result[$data['tab_name']][DATA_OFFICER_FLG] = $this->memberLibrarie->chk_kyokai_officer_level(KYOKAI_OFFICER_ID_OFFICE_WORK, $memberId, KYOKAI_OFFICER_LEVEL_BOSS);
                            break;
                    }
                }
            }
        }
		
        return $result;
    }
}
