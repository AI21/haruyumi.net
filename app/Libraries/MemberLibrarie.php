<?php

namespace App\Libraries;
use App\Models\MemberModel;

class MemberLibrarie
{
	private $memberModel;

	public function __construct(){
		$this->memberModel = model(MemberModel::class);
	}

    /**
     * 会員登録年度一覧取得
     * @return array 会員登録年度一覧
     */
    public function get_member_regist_nendo_list($fiscalYearId, &$SelectfiscalYearData): array
    {
        $result = $this->memberModel->get_member_regist_nendo_list();
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
     * 弓道協会役員メンバー一覧情報取得
     * @param int $kyokaiOfficerId     協会役員ID
     * @return array 弓道協会役員一覧情報
     */
    public function get_kyokai_officer_member_list(int $kyokaiOfficerId=0): array
    {
        $result = array();
		$kyokaiOfficerList = $this->memberModel->get_kyokai_officer_member_list($kyokaiOfficerId);
        if ($kyokaiOfficerList['numRows'] > 0) {
            foreach ($kyokaiOfficerList['result'] as $idx => $data) {
                if ($data['kyokai_officer_id'] == $kyokaiOfficerId) {
                    $result[] = $data;
                }
            }
        }
		
        return $result;
    }

    /**
     * 弓道協会役員の主担当チェック
     * @param int $kyokaiOfficerId     協会役員ID
     * @param int $memberId            会員ID
     * @param int $officerLevel        役員レベル
     * @return bool true:主担当フラグ、false:主担当フラグ以外
     */
    public function chk_kyokai_officer_level(int $kyokaiOfficerId, int $memberId, int $officerLevel=0): bool
    {
        $result = false;
		$kyokaiOfficerList = $this->memberModel->get_kyokai_officer_member_list($kyokaiOfficerId, $memberId, $officerLevel);
        if ($kyokaiOfficerList['numRows'] > 0) {
            $result = true;
        }
		
        return $result;
    }

    /**
     * 会員一覧情報取得
     * @param int $fiscalYearId     年度ID
     * @return array 会員一覧情報
     */
    public function get_member_list(int $fiscalYearId): array
    {
        $result = array();
		$result = $this->memberModel->get_member_list($fiscalYearId);
		
        return $result;
    }

    /**
     * 会員名簿ファイル情報取得
     * @return array 会員名簿ファイル情報
     */
    public function get_member_list_file(): object
    {
        $result = array();
		$result = $this->memberModel->get_member_list_file();
		
        return $result;
    }

    /**
     * 会員情報取得
     * @param string $memberId 会員ID
     * @return array 会員情報
     */
    public function get_member_data(string $memberId): object
    {
        $result = [];
        $result = $this->memberModel->get_member_data($memberId);
        return $result;
    }

}
