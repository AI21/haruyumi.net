<?php

namespace App\Libraries;
use App\Models\OujiModel;

class OujiLibrarie
{
    protected $db;
	private $oujiModel;

	public function __construct($db = null){
        $this->db = $db ?? \Config\Database::connect();
		$this->oujiModel = new OujiModel($this->db);
	}

    /**
     * 王子道場の解放予定情報を取得
     * @return array 解放予定情報
     */
    public function getOujiSchedule(): array
    {
        $result = array();
		$oujiSchedule = $this->oujiModel->getOujiSchedule();
        // 日付単位の配列に変換
        if ($oujiSchedule['numRows'] > 0) {
            $useDaySchedule = array();
            foreach ($oujiSchedule['result'] as $data) {
                $useDaySchedule[$data['use_day']] = $data;
            }
            // 確定予定情報を取得
    		$oujiReserve = $this->oujiModel->getOujiReserve();
            if ($oujiReserve['numRows'] > 0) {
                foreach ($oujiReserve['result'] as $data) {
                    // if () {

                    // }
                    // $useDaySchedule[$data['use_day']] = $data;
                }
            }
            $result = $useDaySchedule;
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
