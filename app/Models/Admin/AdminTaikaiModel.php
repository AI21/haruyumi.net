<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models\Admin;

use App\Models\BaseQueryModel;

class AdminTaikaiModel extends BaseQueryModel {

	protected $db;

	/**
	 * 添付資料の最大資料ID取得
	 */
	public function get_max_document_id(int $taikaiId) : int
	{
		$sql = '
			SELECT
				IFNULL(MAX(document_id), 0) AS max_document_id
			FROM
				t_document_taikai
			WHERE
				taikai_id = :taikaiId:
		';

		$bind = array(
			'taikaiId' => $taikaiId,
		);
		
		if ($row = $this->get_first_row($sql, $bind, 'array')) {
			return (int)$row['max_document_id'];
		} else {
			return 0;
		}
	}

	/**
	 * 大会情報更新
	 * @param array $taikaiData		大会データ
	 * @return bool
	 */
	public function update_taikai_info(array $taikaiData) : bool
	{
		$sql = '
			UPDATE
				t_taikai_info
			SET
				taikai_no = :taikai_no:,
				taikai_sub_name = :taikaiSubName:,
				taikai_date_st = :taikaiDateSt:,
				taikai_date_ed = :taikaiDateEd:,
				kaijo_id = :kaijoId:,
				kaijo_other_name = :kaijoOtherName:,
				taikai_open_time = :taikaiOpenTime:,
				taikai_uketuke_time = :taikaiUketukeTime:,
				taikai_time_st = :taikaiTimeSt:,
				taikai_time_ed = :taikaiTimeEd:,
				taikai_uketuke_st = :taikaiUketukeSt:,
				taikai_uketuke_ed = :taikaiUketukeEd:,
				web_apply_flg = :webApplyFlg:,
				indi_apply_flg = :indiApplyFlg:,
				gender_cd = :genderCd:,
				age_limit_min = :ageLimitMin:,
				age_limit_max = :ageLimitMax:,
				eligibility = :eligibility:,
				competition_rules = :competitionRules:,
				awards = :awards:,
				contact_info = :contactInfo:,
				modified = NOW()
			WHERE
				taikai_id = :taikaiId:
		';

		$bind = array(
			'taikaiId' => $taikaiData['taikai_id'] ?? null,
			'taikai_no' => $taikaiData['taikai_no'] ?? null,
			'taikaiSubName' => $taikaiData['taikai_sub_name'] ?? null,
			'taikaiDateSt' => $taikaiData['taikai_date_st'] ?? null,
			'taikaiDateEd' => $taikaiData['taikai_date_ed'] ?? null,
			'kaijoId' => $taikaiData['kaijo_id'] ?? null,
			'kaijoOtherName' => $taikaiData['kaijo_other_name'] ?? null,
			'taikaiOpenTime' => $taikaiData['taikai_open_time'] ?? null,
			'taikaiUketukeTime' => $taikaiData['taikai_uketuke_time'] ?? null,
			'taikaiTimeSt' => $taikaiData['taikai_time_st'] ?? null,
			'taikaiTimeEd' => $taikaiData['taikai_time_ed'] ?? null,
			'taikaiUketukeSt' => $taikaiData['taikai_uketuke_st'] ?? null,
			'taikaiUketukeEd' => $taikaiData['taikai_uketuke_ed'] ?? null,
			'webApplyFlg' => $taikaiData['web_apply_flg'] ?? null,
			'indiApplyFlg' => $taikaiData['indi_apply_flg'] ?? null,
			'genderCd' => $taikaiData['gender_cd'] ?? null,
			'ageLimitMin' => $taikaiData['age_limit_min'] ?? null,
			'ageLimitMax' => $taikaiData['age_limit_max'] ?? null,
			'eligibility' => $taikaiData['eligibility'] ?? null,
			'competitionRules' => $taikaiData['competition_rules'] ?? null,
			'awards' => $taikaiData['awards'] ?? null,
			'contactInfo' => $taikaiData['contact_info'] ?? null,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	// 関連ファイル情報登録
	public function insert_document_taikai(int $taikaiId, int $documentId, int $documentTypeId, string $documentName, string $documentExt, string $documentPath) {

		$ret = array();

		$sql = '
			INSERT INTO t_document_taikai (
				taikai_id
				,document_id
				,document_type_id
				,document_name
				,document_ext
				,document_path
				,created
			) VALUES (
				:taikaiId:,
				:documentId:,
				:documentTypeId:,
				:documentName:,
				:documentExt:,
				:documentPath:,
				NOW()
			)
		';

		$bind = array(
			'taikaiId' => $taikaiId,
			'documentId' => $documentId,
			'documentTypeId' => $documentTypeId,
			'documentName' => $documentName,
			'documentExt' => $documentExt,
			'documentPath' => $documentPath,
		);

		return $this->get_result_query($sql, $bind);
	}

	// お知らせ関連ファイル登録
	public function insert_notice_rerlation_document_taikai(int $taikaiId, int $noticeInfoId) : bool
	{
		$sql = '
			INSERT INTO t_document_taikai ( 
				taikai_id
				, document_id
				, document_type_id
				, document_name
				, document_ext                              -- ファイル拡張子
				, document_path
				, notice_info_id                            -- お知らせ投稿ID
				, created
			) 
			SELECT
				:taikaiId:
        		, @rownum := @rownum + 1 AS document_id
				, :dbFlgOff:
				, document_name                             -- document_name
				, document_ext                              -- ファイル拡張子
				, document_path                             -- document_path
				, notice_info_id                            -- notice_info_id
				, NOW() 
			FROM
				t_document_notice,
        		(SELECT @rownum := (SELECT IFNULL(MAX(document_id), 0) FROM t_document_taikai WHERE taikai_id = :taikaiId:)) AS init
			WHERE
				notice_info_id = :noticeInfoId:
			ORDER BY
				document_id
		';

		$bind = array(
			'taikaiId' => $taikaiId,
			'dbFlgOff' => DB_FLG_OFF,
			'noticeInfoId' => $noticeInfoId,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	// 大会ファイル情報削除
	public function delete_taikai_document(int $taikaiId, int $documentId) {

		$ret = array();

		$sql = '
			DELETE FROM 
				t_document_taikai 
			WHERE
				taikai_id = :taikaiId:
				AND document_id = :documentId:
		';

		$bind = array(
			'taikaiId' => $taikaiId,
			'documentId' => $documentId,
		);

		return $this->get_result_query($sql, $bind);
	}

	// お知らせ関連ファイル情報削除
	public function delete_relation_notice_document_taikai(int $noticeInfoId) : bool
	{
		$sql = '
			DELETE FROM t_document_taikai
			WHERE
				notice_info_id = :noticeInfoId:
		';

		$bind = array(
			'noticeInfoId' => $noticeInfoId,
		);
		
		return $this->get_result_query($sql, $bind);
	}

}