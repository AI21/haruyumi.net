<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models\Admin;

use App\Models\BaseQueryModel;

class AdminShinsaModel extends BaseQueryModel {

	protected $db;
	
	/**
	 * 審査会場リスト取得
     * @param int $shinsaClassId    審査区分ID
     * @param int $areaGroupId      地域グループID
     * @return bool
	 */
	public function get_shinsa_kaijo_list(int $shinsaClassId, int $areaGroupId) : array
	{
		$sqlWhere = '';
		if ($areaGroupId > 0) {
			$sqlWhere = ' AND sck.area_group_id = :areaGroupId:';
		}

		$sql = '
            SELECT
                mka.kaijo_id,
                CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name ELSE mka.kaijo_name END kaijo_name,
                CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name_short ELSE mka.kaijo_name_short END kaijo_name_short,
                CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name_abb ELSE mka.kaijo_name_abb END kaijo_name_abb
            FROM m_shinsa_class_kaijo sck
                INNER JOIN m_kaijo mka ON
                    mka.kaijo_id = sck.kaijo_id
                	AND mka.use_flg = :useFlg:
                LEFT JOIN m_kaijo_name_history knh ON
                    knh.kaijo_id = mka.kaijo_id
                    AND NOW() BETWEEN knh.change_date_st AND knh.change_date_ed
            WHERE
                sck.shinsa_class_id = :shinsaClassId:
				' . $sqlWhere . '
            ORDER BY
                sck.order_no = 0 ASC,
                sck.order_no ASC,
                mka.pref_id ASC
		';

		$bind = array(
			'shinsaClassId' => $shinsaClassId,
			'useFlg' => DB_FLG_ON,
		);
		if ($sqlWhere !== '') {
			$bind['areaGroupId'] = $areaGroupId;
		}

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 審査種別リスト取得
     * @param   int     $shinsaClassId     審査区分ID
     * @return bool
	 */
	public function get_shinsa_shubetsu_list(int $shinsaClassId) : array
	{
		$sql = '
            SELECT
                mhg.holder_grade_id,
                mhg.holder_grade_name
            FROM m_shinsa_holder_grade shg
                INNER JOIN m_holder_grade mhg ON
                    mhg.holder_grade_id = shg.holder_grade_id
            WHERE
                shg.shinsa_class_id = :shinsaClassId:
            ORDER BY
                shg.order_no = 0 ASC,
                shg.order_no ASC
		';

		$bind = array(
			'shinsaClassId' => $shinsaClassId,
		);

		return $this->get_result_array($sql, $bind);
	}

	/**
	 * 審査種別の対象メンバーリスト取得
	 * @param   int     $fiscalYearId      年度ID
	 * @param   int     $shinsaId          審査ID
	 * @param   int     $shinsaTargetId    審査種別ID
	 * @return bool
	 */
	public function get_shinsa_target_member_list(int $fiscalYearId, int $shinsaId, int $shinsaTargetId) : array
	{
		$sql = '
            SELECT
                mem.member_id,
                mem.name_f,
                mem.name_s,
                mgh.holder_acquired_day,
                mgh.grade_acquired_day
            FROM
                m_shinsa_target mst 
                INNER JOIN m_grade_group mgg ON
                    mgg.grade_group_id = mst.grade_group_id
                INNER JOIN t_member_grade_holder mgh ON
                    mgh.holder_id = mst.trial_holder_id
                    AND mgh.grade_id = mgg.grade_id
                INNER JOIN m_member mem ON
                    mem.member_id = mgh.member_id
                    AND mem.aiti_renmei_regist_flg = :dbFlgOn:
                INNER JOIN t_member_regist_fiscal mrf ON
                    mrf.member_id = mem.member_id
                    AND mrf.fiscal_year_id = :fiscalYearId:
            WHERE
                mst.shinsa_target_id = :shinsaTargetId:
				-- 審査申請者は対象外
                AND NOT EXISTS ( 
                    SELECT 1 FROM t_shinsa_offer_member WHERE shinsa_id = :shinsaId: AND member_id = mem.member_id
                )
            ORDER BY 
                mgh.holder_acquired_day ASC
                ,mgh.grade_acquired_day ASC
		';

		$bind = array(
			'fiscalYearId' => $fiscalYearId,
			'shinsaTargetId' => $shinsaTargetId,
			'shinsaId' => $shinsaId,
			'dbFlgOn' => DB_FLG_ON,
		);

		return $this->get_result_array($sql, $bind);
	}

	/**
	 * 添付資料の最大資料ID取得
	 */
	public function get_max_document_id(int $shinsaId) : int
	{
		$sql = '
			SELECT
				IFNULL(MAX(document_id), 0) AS max_document_id
			FROM
				t_document_shinsa
			WHERE
				shinsa_id = :shinsaId:
		';

		$bind = array(
			'shinsaId' => $shinsaId,
		);
		
		if ($row = $this->get_first_row($sql, $bind, 'array')) {
			return (int)$row['max_document_id'];
		} else {
			return 0;
		}
	}
	
	// 審査情報登録
	public function insert_shinsa_info(int $fiscalYearId, array $shinsaData, int &$insertShinsaId) : bool
	{

		$ret = array();

		$sql = '
			INSERT INTO t_shinsa_info (
				category_id
				,fiscal_year_id
				,uketuke_limit_zenkyuren
				,uketuke_limit_tokairengo
				,uketuke_limit_aikyuren
				,uketuke_limit_aikyuren_st
				,uketuke_limit_aikyuren_ed
				,uketuke_limit_kasugai
				,all_holder_grade_id
				,gender_cd
				,shinsa_name_id
				,shinsa_name_other
				,shinsa_class_id
				,area_group_id
				,kyokai_officer_id
				,created
			) VALUES (
				:categoryId:,
				:fiscal_yearId:,
				:uketukeLimitZenkyuren:,
				:uketukeLimitTokairengo:,
				:uketukeLimitAikyuren:,
				:uketukeLimitAikyurenSt:,
				:uketukeLimitAikyurenEd:,
				:uketukeLimitKasugai:,
				:allHolderGradeId:,
				:genderCd:,
				:shinsaNameId:,
				:shinsaNameOther:,
				:shinsaClassId:,
				:areaGroupId:,
				:kyokaiOfficerId:,
				NOW()
			)
		';

		$bind = array(
			'categoryId' => $shinsaData['category_id'] ?? null,
			'fiscal_yearId' => $fiscalYearId,
			'uketukeLimitZenkyuren' => $shinsaData['uketuke_limit_zenkyuren'] ?? null,
			'uketukeLimitTokairengo' => $shinsaData['uketuke_limit_tokairengo'] ?? null,
			'uketukeLimitAikyuren' => $shinsaData['uketuke_limit_aikyuren_ed'] ?? null,
			'uketukeLimitAikyurenSt' => $shinsaData['uketuke_limit_aikyuren_st'] ?? null,
			'uketukeLimitAikyurenEd' => $shinsaData['uketuke_limit_aikyuren_ed'] ?? null,
			'uketukeLimitKasugai' => $shinsaData['uketuke_limit_kasugai'] ?? null,
			'allHolderGradeId' => $shinsaData['all_holder_grade_id'] ?? null,
			'genderCd' => $shinsaData['gender_cd'] ?? null,
			'shinsaNameId' => $shinsaData['shinsa_name_id'] ?? null,
			'shinsaNameOther' => $shinsaData['shinsa_name_other'] ?? null,
			'shinsaClassId' => $shinsaData['shinsa_class_id'] ?? null,
			'areaGroupId' => $shinsaData['area_group_id'] ?? null,
			'kyokaiOfficerId' => $shinsaData['kyokai_officer_id'] ?? null,
		);

		$result = $this->get_result_query($sql, $bind);
		if ($result === true) {
			$insertShinsaId = $this->get_insert_id();
		}

		return $result;
	}
	
	// 関連ファイル情報登録
	public function insert_document_shinsa(int $shinsaId, int $documentId, int $documentTypeId, string $documentName, string $documentExt, string $documentPath) {

		$ret = array();

		$sql = '
			INSERT INTO t_document_shinsa (
				shinsa_id
				,document_id
				,document_type_id
				,document_name
				,document_ext
				,document_path
				,created
			) VALUES (
				:shinsaId:,
				:documentId:,
				:documentTypeId:,
				:documentName:,
				:documentExt:,
				:documentPath:,
				NOW()
			)
		';

		$bind = array(
			'shinsaId' => $shinsaId,
			'documentId' => $documentId,
			'documentTypeId' => $documentTypeId,
			'documentName' => $documentName,
			'documentExt' => $documentExt,
			'documentPath' => $documentPath,
		);

		return $this->get_result_query($sql, $bind);
	}

	// お知らせ関連ファイル登録
	public function insert_notice_rerlation_document_shinsa(int $shinsaId, int $noticeInfoId) : bool
	{
		$sql = '
			INSERT INTO t_document_shinsa ( 
				shinsa_id
				, document_id
				, document_type_id
				, document_name
				, document_ext                              -- ファイル拡張子
				, document_path
				, notice_info_id                            -- お知らせ投稿ID
				, created
			) 
			SELECT
				:shinsaId:
        		, @rownum := @rownum + 1 AS document_id
				, :dbFlgOff:
				, document_name                             -- document_name
				, document_ext                              -- ファイル拡張子
				, document_path                             -- document_path
				, notice_info_id                            -- notice_info_id
				, NOW() 
			FROM
				t_document_notice,
        		(SELECT @rownum := (SELECT IFNULL(MAX(document_id), 0) FROM t_document_shinsa WHERE shinsa_id = :shinsaId:)) AS init
			WHERE
				notice_info_id = :noticeInfoId:
			ORDER BY
				document_id
		';

		$bind = array(
			'shinsaId' => $shinsaId,
			'dbFlgOff' => DB_FLG_OFF,
			'noticeInfoId' => $noticeInfoId,
		);

		return $this->get_result_query($sql, $bind);
	}

	// 審査日程情報登録
	public function insert_shinsa_date_target(int $shinsaId, array $shinsaDateTarget) : bool
	{
		$sql = '
			INSERT INTO t_shinsa_date_target (
				shinsa_id
				, shinsa_date
				, holder_grade_id
			) VALUES (
				:shinsaId:
				, :targetDate:
				, :holderGradeId:
			)
		';
		

		$bind = array(
			'shinsaId' => $shinsaId,
			'targetDate' => $shinsaDateTarget['shinsa_date'] ?? null,
			'holderGradeId' => $shinsaDateTarget['holder_grade_id'] ?? null,
		);

		return $this->get_result_query($sql, $bind);
	}

	// 審査会場情報登録
	public function insert_shinsa_kaijo(int $shinsaId, array $shinsaKaijo) : bool
	{
		$sql = '
			INSERT INTO t_shinsa_kaijo (
				shinsa_id
				, kaijo_id
				, kaijo_other_name
				, kaijo_other_name_abb
				, order_no
				, additional_info
			) VALUES (
				:shinsaId:
				, :kaijoId:
				, :kaijoOtherName:
				, :kaijoOtherNameAbb:
				, :orderNo:
				, :additionalInfo:
			)
		';
		

		$bind = array(
			'shinsaId' => $shinsaId,
			'kaijoId' => $shinsaKaijo['kaijo_id'] ?? null,
			'kaijoOtherName' => $shinsaKaijo['kaijo_other_name'] ?? null,
			'kaijoOtherNameAbb' => $shinsaKaijo['kaijo_other_name_abb'] ?? null,
			'orderNo' => $shinsaKaijo['order_no'] ?? null,
			'additionalInfo' => $shinsaKaijo['additional_info'] ?? null,
		);

		return $this->get_result_query($sql, $bind);
	}

	/**
	 * 審査情報更新
	 * @param array $shinsaData		審査データ
	 * @return bool
	 */
	public function update_shinsa_info(array $shinsaData) : bool
	{
		$sql = '
			UPDATE
				t_shinsa_info
			SET
				all_holder_grade_id = :allHolderGradeId:,
				gender_cd = :genderCd:,
				uketuke_limit_zenkyuren = :uketuke_limit_zenkyuren:,
				uketuke_limit_aikyuren_st = :uketuke_limit_aikyurenSt:,
				uketuke_limit_aikyuren_ed = :uketuke_limit_aikyurenEd:,
				shinsa_class_id = :shinsaClassId:,
				modified = NOW()
			WHERE
				shinsa_id = :shinsaId:
		';

		$bind = array(
			'shinsaId' => $shinsaData['shinsa_id'] ?? null,
			'allHolderGradeId' => $shinsaData['all_holder_grade_id'] ?? null,
			'genderCd' => $shinsaData['gender_cd'] ?? null,
			'uketuke_limit_zenkyuren' => $shinsaData['uketuke_limit_zenkyuren'] ?? null,
			'uketuke_limit_aikyurenSt' => $shinsaData['uketuke_limit_aikyuren_st'] ?? null,
			'uketuke_limit_aikyurenEd' => $shinsaData['uketuke_limit_aikyuren_ed'] ?? null,
			'shinsaClassId' => $shinsaData['shinsa_class_id'] ?? null,
		);

		return $this->get_result_query($sql, $bind);
	}

	// 関連ファイル情報更新
	public function update_document_shinsa(int $beforeShinsaId, int $afterShinsaId, int $noticeInfoId) : bool
	{
		$sql = '
			UPDATE t_document_shinsa
			SET
				shinsa_id = :afterShinsaId:
			WHERE
				shinsa_id = :beforeShinsaId:
				AND notice_info_id = :noticeInfoId:
		';

		$bind = array(
			'beforeShinsaId' => $beforeShinsaId,
			'afterShinsaId' => $afterShinsaId,
			'noticeInfoId' => $noticeInfoId,
		);
		
		return $this->get_result_query($sql, $bind);
	}

	// 審査日程情報削除
	public function delete_shinsa_date_target(int $shinsaId) : bool
	{
		$sql = '
			DELETE FROM t_shinsa_date_target
			WHERE
				shinsa_id = :shinsaId:
		';

		$bind = array(
			'shinsaId' => $shinsaId,
		);
		
		return $this->get_result_query($sql, $bind);
	}

	// 審査会場情報削除
	public function delete_shinsa_kaijo(int $shinsaId) : bool
	{
		$sql = '
			DELETE FROM t_shinsa_kaijo
			WHERE
				shinsa_id = :shinsaId:
		';

		$bind = array(
			'shinsaId' => $shinsaId,
		);
		
		return $this->get_result_query($sql, $bind);
	}

	// お知らせ関連ファイル情報削除
	public function delete_relation_notice_document_shinsa(int $noticeInfoId) : bool
	{
		$sql = '
			DELETE FROM t_document_shinsa
			WHERE
				notice_info_id = :noticeInfoId:
		';

		$bind = array(
			'noticeInfoId' => $noticeInfoId,
		);
		
		return $this->get_result_query($sql, $bind);
	}

}