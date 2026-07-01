<?php

namespace App\Libraries;
use App\Models\SeminarModel;

class SeminarLibrarie
{
	private $seminarModel;
    protected $errorMessage = "";

	public function __construct(){
		$this->seminarModel = model(SeminarModel::class);
	}

    /**
     * 講習会登録年度一覧取得
     * @return array 講習会登録年度一覧
     */
    public function get_seminar_regist_nendo_list($fiscalYearId, &$SelectfiscalYearData): array
    {
        $result = $this->seminarModel->get_seminar_regist_nendo_list();
        if (empty($result) === false && $result['numRows'] > 0) {
            foreach ($result['result'] as $idx => $data) {
                if ($data['fiscal_year_id'] == $fiscalYearId) {
                    // 選択年度がある場合は選択年度を設定
                    $SelectfiscalYearData = $data;
                }
            }
        }
        return $result;
    }

    /**
     * 講習会一覧情報取得
     * @param int $fiscalYearId     年度ID
     * @param array $subMemuData    サブメニュー情報
     * @return array 講習会一覧情報
     */
    public function get_seminar_list(int $fiscalYearId, array $subMemuData): array
    {
        $result = array();

        if (empty($subMemuData) === false) {
            if ($subMemuData['numRows'] > 0) {
                // カテゴリー毎の講習会情報を取得
                foreach ($subMemuData['result'] as $idx => $data) {
                    $result[$data['tab_name']] = $this->seminarModel->get_seminar_list($fiscalYearId, $data['category_id']);
                }
            }
        }
		
        return $result;
    }

    /**
     * 講習会詳細情報取得
     * @param int $seminarId    講習会ID
     * @return array 講習会詳細情報
     */
    public function get_seminar_detail(int $seminarId): array
    {
        $result = array();

        // 講習会詳細情報取得
        $result = $this->seminarModel->get_seminar_detail($seminarId);

        // 講習会関連資料一覧情報取得
        $result['seminar_document_list'] = $this->seminarModel->get_seminar_document_list($seminarId);

        return $result;
    }
}
