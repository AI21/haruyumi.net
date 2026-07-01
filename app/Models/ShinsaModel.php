<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models;

class ShinsaModel extends BaseQueryModel {

	protected $db;

	/**
	 * 審査登録年度一覧取得
	 * @return array
	 */
	public function get_shinsa_regist_nendo_list() : array
	{
		$sql = '
			SELECT 
				tsi.fiscal_year_id
				,mfn.year
				,mfn.wareki
			FROM 
				t_shinsa_info tsi
				INNER JOIN m_fiscal_nendo mfn ON
					mfn.fiscal_year_id = tsi.fiscal_year_id
			GROUP BY
				fiscal_year_id
		';

		$bind = array(
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 称号リスト取得
     * @param   int     $holderId     称号ID
     * @return bool
	 */
	public function get_holder_list(int $holderId) : array
	{
		$sql = '
			SELECT
				hol.holder_id,
				hol.holder_name
			FROM m_holder hol
			WHERE
				hol.holder_id = :holderId:
				AND hol.view_flg = :viewFlg:
			ORDER BY
				hol.order_no ASC
		';

		$bind = array(
			'holderId' => $holderId,
			'viewFlg' => DB_FLG_ON,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 指定審査の最終日の取得
     * @param   int     $shisaId     審査ID
     * @return bool
	 */
	public function get_shinsa_last_date(int $shisaId) : string
	{
		$sql = '
			SELECT 
				MAX(sdt.shinsa_date) AS shinsa_last_date
			FROM t_shinsa_date_target sdt
			WHERE
				sdt.shinsa_id = :shinsa_id:
		';

		$bind = array(
			'shinsa_id' => $shisaId,
		);

		$ret = $this->get_first_row($sql, $bind, 'array');
		return $ret['shinsa_last_date'];
	}
	
	/**
	 * 地域リスト取得
     * @param int $areaGroupLevel		審査区分レベル（1:定期、2:中央審査、3:連合審査）
     * @return bool
	 */
	public function get_area_group_list(int $areaGroupLevel) : array
	{
		$sqlWhere = '1 = 1';
		switch ($areaGroupLevel) {
			case SHINSA_AREA_GROUP_TEIKI:
				$sqlWhere .= ' AND teiki_flg = :areaGroupFlg:';
				break;
			case SHINSA_AREA_GROUP_CHUOU:
				$sqlWhere .= ' AND chuou_flg = :areaGroupFlg:';
				break;
			case SHINSA_AREA_GROUP_RENGO:
				$sqlWhere .= ' AND rengou_flg = :areaGroupFlg:';
				break;
			default:
				$areaGroupIdList = [];
		}

		$sql = '
			SELECT
				area_group_id,
				area_group_name
			FROM m_area_group
			WHERE
				' . $sqlWhere . '
			ORDER BY
				area_group_id ASC
		';

		$bind = array(
			'areaGroupFlg' => DB_FLG_ON,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 審査名称リスト取得
     * @param int $areaGroupLevel		審査区分レベル（1:定期、2:中央審査、3:連合審査、4:地方審査）
     * @return bool
	 */
	public function get_shinsa_name_list(int $areaGroupLevel) : array
	{
		$sqlWhere = '1 = 1';
		switch ($areaGroupLevel) {
			case SHINSA_AREA_GROUP_CHUOU:
				$sqlWhere .= ' AND chuou_flg = :areaGroupFlg:';
				break;
			case SHINSA_AREA_GROUP_RENGO:
				$sqlWhere .= ' AND rengou_flg = :areaGroupFlg:';
				break;
			case SHINSA_AREA_GROUP_CHIHO:
				$sqlWhere .= ' AND chihou_flg = :areaGroupFlg:';
				break;
			default:
				$areaGroupIdList = [];
		}

		$sql = '
			SELECT
				shinsa_name_id,
				shinsa_name,
				shinsa_name_short
			FROM m_shinsa_name
			WHERE
				' . $sqlWhere . '
			ORDER BY
				shinsa_name_id ASC
		';

		$bind = array(
			'areaGroupFlg' => DB_FLG_ON,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 段位グループリスト取得
     * @param int $shinsaClassId		審査区分ID
     * @return bool
	 */
	public function get_shinsa_holder_grade_list(int $shinsaClassId) : array
	{
		$sql = '
			SELECT
				shg.shinsa_class_id,
				shg.holder_grade_id,
				mhg.holder_grade_name,
				mhg.holder_grade_name_short
			FROM
				m_shinsa_holder_grade shg 
				INNER JOIN m_holder_grade mhg 
					ON mhg.holder_grade_id = shg.holder_grade_id
			WHERE 
				shg.shinsa_class_id = :shinsaClassId:
			ORDER BY
				shg.order_no
		';

		$bind = array(
			'shinsaClassId' => $shinsaClassId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 段位グループリスト取得
     * @param int $gradeGroupId		段位グループID
     * @return bool
	 */
	public function get_grade_group_list(int $gradeGroupId) : array
	{
		$sql = '
			SELECT
				gre.grade_id,
				gre.grade_name
			FROM m_grade_group mgg
                INNER JOIN m_grade gre ON
                	gre.grade_id = mgg.grade_id
			WHERE
				mgg.grade_group_id = :gradeGroupId:
			ORDER BY
				gre.order_no ASC
		';

		$bind = array(
			'gradeGroupId' => $gradeGroupId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 段位・連盟登録情報取得
	 * 
	 * @param	int	$memberId		会員ID
	 */ 
	public function get_member_grade_data(int $memberId) : array
	{
		$sql = '
			SELECT
				mem.kasugai_regist_flg
				,mem.kasugai_regist_date
                ,mem.renmei_adjourning_flg
				,mem.aiti_renmei_regist_flg	
				,mem.renmei_id
                ,hol.holder_name
				,mgh.holder_id
				,mgh.holder_acquired_day
                ,gre.grade_name
				,mgh.grade_id
				,mgh.grade_acquired_day
			FROM m_member mem
                LEFT JOIN t_member_grade_holder mgh ON
                	mgh.member_id = mem.member_id
                INNER JOIN m_holder hol ON
                	hol.holder_id = mgh.holder_id
                INNER JOIN m_grade gre ON
                	gre.grade_id = mgh.grade_id
			WHERE
				mem.member_id = :memberId: 
		';

		$bind = array(
			'memberId' => $memberId
		);

		return $this->get_first_row($sql, $bind, 'array');
	}
	
	/**
	 * カテゴリー連絡情報取得
	 * 
	 */ 
	public function get_shinsa_category_information(int $categoryId) : array
	{
		$sql = '
			SELECT
				category_info
				,created
				,modified
			FROM m_shinsa_category_information 
			WHERE
				category_id = :categoryId: 
		';

		$bind = array(
			'categoryId' => $categoryId
		);

		return $this->get_first_row($sql, $bind, 'array');
	}
	
	/**
	 * 審査一覧情報取得
     * @param int $fiscalYearId		年度ID
     * @param int $categoryId		カテゴリーID
     * @param bool $pastFlg			過去フラグ
     * @return array
	 */
	public function get_shinsa_list(int $fiscalYearId, int $categoryId, bool $pastFlg=false) : array
	{
		// 過去未来判定
		$sqlShinsaDateCalc = '>=';
		$sqlShinsaDateOrder = 'ASC';
		if ($pastFlg == true) {
			$sqlShinsaDateCalc = '<';
			$sqlShinsaDateOrder = 'DESC';
		}

		$sql = '
			SELECT
				*
			FROM
				(
				SELECT
					tsi.shinsa_id,
					tsi.uketuke_limit_zenkyuren,
					tsi.uketuke_limit_tokairengo,
					tsi.uketuke_limit_aikyuren_st,
					tsi.uketuke_limit_aikyuren_ed,
					tsi.uketuke_limit_kasugai,
					(
						SELECT 
							MIN(sdt.shinsa_date)
						FROM t_shinsa_date_target sdt
						WHERE
							sdt.shinsa_id = tsi.shinsa_id
					) AS shinsa_date_min,
					(
						SELECT 
							MAX(sdt.shinsa_date)
						FROM t_shinsa_date_target sdt
						WHERE
							sdt.shinsa_id = tsi.shinsa_id
					) AS shinsa_date_max,
					mag.area_group_name,
					msn.shinsa_name,
					tsi.shinsa_name_other,
					tsi.shinsa_class_id,
                	mhg.holder_grade_name as all_holder_grade_name,
                	mhg.holder_grade_name_short as all_holder_grade_name_short,
					tsi.gender_cd,
					tsi.shinsa_name_id,
					tsi.area_group_id,
					tsi.created,
					tsi.modified
				FROM
					t_shinsa_info tsi
					INNER JOIN m_shinsa_name msn ON
						msn.shinsa_name_id = tsi.shinsa_name_id
					INNER JOIN m_holder_grade mhg ON
						mhg.holder_grade_id = tsi.all_holder_grade_id
					LEFT JOIN m_area_group mag ON
						mag.area_group_id = tsi.area_group_id
				WHERE
					tsi.fiscal_year_id = :fiscalYearId: 
					AND tsi.category_id = :categoryId: 
			) A
			WHERE
				A.shinsa_date_min ' . $sqlShinsaDateCalc . ' CURDATE()
			ORDER BY
				A.shinsa_date_min ' . $sqlShinsaDateOrder . '
		';

		$bind = array(
			'fiscalYearId' => $fiscalYearId,
			'categoryId' => $categoryId,
		);
		$ret = $this->get_result_array($sql, $bind);

		if ($ret['numRows'] > 0) {
			foreach ($ret['result'] as $idx => $data) {
				// 審査日程・対象種別情報取得
				$ret['result'][$idx]['date_holder_grade'] = $this->get_date_holder_grade($data['shinsa_id']);
				// 審査会場情報取得
            	$ret['result'][$idx]['kaijo_list'] = $this->get_shinsa_kaijo($data['shinsa_id']);
			}
		}

		return $ret;
	}
	
	/**
	 * 未経過の審査一覧情報取得
     * @param int $fiscalYearId		年度ID
     * @param int $categoryId		カテゴリーID
     * @param bool $pastFlg			過去フラグ
     * @return array
	 */
	public function get_unexpired_shinsa_list() : array
	{
		$sql = '
            SELECT
                *
            FROM
                (
                SELECT
                    tsi.shinsa_id,
                    (
                        SELECT 
                            MIN(sdt.shinsa_date)
                        FROM t_shinsa_date_target sdt
                        WHERE
                            sdt.shinsa_id = tsi.shinsa_id
                    ) AS shinsa_date_min,
                    mag.area_group_name,
                    msn.shinsa_name,
                    tsi.shinsa_name_other,
                    msc.shinsa_class_id,
                    msc.shinsa_class_name
                FROM
                    t_shinsa_info tsi
                    INNER JOIN m_shinsa_name msn ON
                        msn.shinsa_name_id = tsi.shinsa_name_id
                    INNER JOIN m_shinsa_class msc ON
                        msc.shinsa_class_id = tsi.shinsa_class_id
                    LEFT JOIN m_area_group mag ON
                        mag.area_group_id = tsi.area_group_id
            ) A
			WHERE
				shinsa_date_min >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
            ORDER BY
                shinsa_class_id,
                shinsa_date_min
		';

		$bind = array(
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 審査詳細情報取得
     * @param int $shinsaId			審査ID
     * @param int $memberId			会員ID
     * @return array
	 */
	public function get_shinsa_detail(int $shinsaId, int $memberId) : array
	{
		$sql = '
            SELECT
                tsi.shinsa_id,
				tsi.fiscal_year_id,
                tsi.category_id,
                tsi.all_holder_grade_id,
                tsi.uketuke_limit_zenkyuren,
                tsi.uketuke_limit_tokairengo,
                tsi.uketuke_limit_aikyuren,
                tsi.uketuke_limit_aikyuren_st,
                tsi.uketuke_limit_aikyuren_ed,
                tsi.uketuke_limit_kasugai,
				mag.area_group_name,
                msn.shinsa_name,
                msn.shinsa_name_short,
                msc.shinsa_class_name,
                msc.shinsa_class_name_short,
                tsi.shinsa_class_id,
                mhg.holder_grade_name as all_holder_grade_name,
                tsi.gender_cd,
                tsi.shinsa_name_id,
                tsi.area_group_id,
                tsi.created,
                tsi.modified,
                sht.shinsa_target_id,
                sht.shinsa_target_name,
                sht.pass_grade_group_id,
				CASE WHEN sof.shinsa_offer_id IS NOT NULL THEN 1 ELSE 0 END sanka_flg,
                sof.result_flg,
				CASE 
					WHEN ( 
						SELECT
							MIN(shinsa_date) 
						FROM
							t_shinsa_date_target 
						WHERE
							shinsa_id = tsi.shinsa_id
					) > CURDATE() 
					THEN :flgOn:
					ELSE :flgOff:
				END future_flg,
				kom.officer_level
            FROM t_shinsa_info tsi
            	INNER JOIN m_shinsa_name msn ON
            		msn.shinsa_name_id = tsi.shinsa_name_id
            	INNER JOIN m_shinsa_class msc ON
            		msc.shinsa_class_id = tsi.shinsa_class_id
                INNER JOIN m_kyokai_officer mko ON
                    mko.kyokai_officer_id = msc.kyokai_officer_id
                    AND mko.use_flg = :flgOn:
                LEFT JOIN m_kyokai_officer_member kom ON
                    kom.kyokai_officer_id = mko.kyokai_officer_id
                    AND kom.member_id = :memberId:
				INNER JOIN m_holder_grade mhg ON
					mhg.holder_grade_id = tsi.all_holder_grade_id
                LEFT JOIN t_shinsa_offer_member sof ON
                    sof.shinsa_id = tsi.shinsa_id
                    AND sof.member_id = :memberId:
                LEFT JOIN m_shinsa_target sht ON
                    sht.shinsa_target_id = sof.shinsa_target_id
				LEFT JOIN m_area_group mag ON
					mag.area_group_id = tsi.area_group_id
            WHERE
            	tsi.shinsa_id = :shinsaId:
		';

		$bind = array(
			'shinsaId' => $shinsaId,
			'memberId' => $memberId,
			'flgOn' => DB_FLG_ON,
			'flgOff' => DB_FLG_OFF,
		);
		$ret = $this->get_first_row($sql, $bind, 'array');

		if (empty($ret) === false) {
			$ret['date_holder_grade'] = $this->get_date_holder_grade($ret['shinsa_id']);
			$ret['kaijo_list'] = $this->get_shinsa_kaijo($ret['shinsa_id']);
		}

		return $ret;
	}
	
	/**
	 * 審査簡易詳細情報取得
     * @param int $shinsaId		審査ID
     * @return array
	 */
	public function get_shinsa_detail_convenience(int $shinsaId) : array
	{
		$sql = '
			SELECT
				(
					SELECT 
						MIN(sdt.shinsa_date)
					FROM t_shinsa_date_target sdt
					WHERE
						sdt.shinsa_id = tsi.shinsa_id
				) AS shinsa_date_min,
				(
					SELECT 
						MAX(sdt.shinsa_date)
					FROM t_shinsa_date_target sdt
					WHERE
						sdt.shinsa_id = tsi.shinsa_id
				) AS shinsa_date_max,
				mag.area_group_name,
				msn.shinsa_name,
				tsi.shinsa_name_other
			FROM
				t_shinsa_info tsi
				INNER JOIN m_shinsa_name msn ON
					msn.shinsa_name_id = tsi.shinsa_name_id
				INNER JOIN m_holder_grade mhg ON
					mhg.holder_grade_id = tsi.all_holder_grade_id
				LEFT JOIN m_area_group mag ON
					mag.area_group_id = tsi.area_group_id
			WHERE
				tsi.shinsa_id = :shinsaId: 
		';

		$bind = array(
			'shinsaId' => $shinsaId,
		);
		
		return $this->get_first_row($sql, $bind, 'array');
	}

	/**
	 * 審査関連資料一覧情報取得
	 * @param int $shinsaId			審査ID
	 * @return array
	 */
	public function get_shinsa_document_list(int $shinsaId) : array
	{
		$sql = '
            SELECT
                tds.document_id,
                tds.document_name,
                tds.document_ext,
                tds.document_path,
                tds.created,
                tds.modified,
                mdt.document_type_name
            FROM 
                t_document_shinsa tds
                INNER JOIN m_document_type mdt ON
                    mdt.document_type_id = tds.document_type_id
			WHERE
				tds.shinsa_id = :shinsaId:
            ORDER BY
                mdt.order_no,
                tds.document_id
		';

		$bind = array(
			'shinsaId' => $shinsaId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 審査日程・対象種別情報取得
     * @param int $shinsaId			審査ID
     * @return array
	 */
	private function get_date_holder_grade(int $shinsaId) : array
	{
		$sql = '
			SELECT
				sdt.shinsa_date,
				mhg.holder_grade_id,
				mhg.holder_grade_name
			FROM
				t_shinsa_date_target sdt
			INNER JOIN m_holder_grade mhg ON
				mhg.holder_grade_id = sdt.holder_grade_id
			WHERE
				sdt.shinsa_id = :shinsaId:
			ORDER BY
				sdt.shinsa_date ASC
		';

		$bind = array(
			'shinsaId' => $shinsaId
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 審査会場情報取得
     * @param int $shinsaId			審査ID
     * @return array
	 */
	private function get_shinsa_kaijo(int $shinsaId) : array
	{
		$sql = '
		    SELECT
                tsk.kaijo_id
                ,tsk.kaijo_other_name
                ,tsk.kaijo_other_name_abb
                ,tsk.additional_info
                ,CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name ELSE mka.kaijo_name END kaijo_name
                ,CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name_short ELSE mka.kaijo_name_short END kaijo_name_short
                ,CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name_abb ELSE mka.kaijo_name_abb END kaijo_name_abb
            FROM
                t_shinsa_kaijo tsk
                INNER JOIN m_kaijo mka ON
                    mka.kaijo_id = tsk.kaijo_id
				-- 審査日の最小値
                INNER JOIN (SELECT MIN(shinsa_date) AS shinsa_date, shinsa_id FROM t_shinsa_date_target WHERE shinsa_id = :shinsaId:) sdt ON
                    sdt.shinsa_id = tsk.shinsa_id
				-- 審査日からネーミングライツ権利のある会場の名称を取得
                LEFT JOIN m_kaijo_name_history knh ON
                    knh.kaijo_id = mka.kaijo_id
                    AND sdt.shinsa_date BETWEEN knh.change_date_st AND knh.change_date_ed
            WHERE
                tsk.shinsa_id = :shinsaId:
            ORDER BY
                tsk.order_no
		';

		$bind = array(
			'shinsaId' => $shinsaId
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 審査申込対象一覧情報取得
     * @param	array	$shinsaDetail		審査詳細情報
     * @param	array	$memberGradeDeta	会員段位・連盟登録情報
     * @return	array
	 */
	public function get_shinsa_target_list(array $shinsaDetail, array $memberGradeDeta) : array
	{
		$sql = '
            SELECT 
				sht.shinsa_target_id,
				sht.shinsa_target_name,
				hol.holder_name,
				gra.grade_name
			FROM 
				m_shinsa_target sht
				LEFT JOIN m_holder hol
					ON hol.holder_id = sht.holder_id
				LEFT JOIN m_grade_group mgg
					ON mgg.grade_group_id = sht.grade_group_id
				LEFT JOIN m_grade gra
					ON gra.grade_id = mgg.grade_id
			WHERE
				sht.shinsa_class_id = :shinsaClassId:
				AND hol.holder_id = :holderId:
				AND mgg.grade_id = :gradeId:
			ORDER BY
				sht.order_no
		';

		$bind = array(
			'shinsaClassId' => $shinsaDetail['shinsa_class_id'],
			'holderId' => $memberGradeDeta['holder_id'],
			'gradeId' => $memberGradeDeta['grade_id'],
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 審査申込対象一覧情報取得：五段以上対応
     * @param	array	$shinsaDetail		審査詳細情報
     * @param	array	$memberGradeDeta	会員段位・連盟登録情報
     * @return	array
	 */
	public function get_shinsa_target_holder_list(array $shinsaDetail, array $memberGradeDeta) : array
	{
		$sql = '
            SELECT 
				sht.shinsa_target_id,
				sht.shinsa_target_name,
				hol.holder_name,
				gra.grade_name
			FROM 
				m_shinsa_target sht
				LEFT JOIN m_holder hol
					ON hol.holder_id = sht.holder_id
				LEFT JOIN m_grade_group mgg
					ON mgg.grade_group_id = sht.grade_group_id
				LEFT JOIN m_grade gra
					ON gra.grade_id = mgg.grade_id
			WHERE
				sht.shinsa_class_id = :shinsaClassId:
				AND hol.holder_id = :holderId:
				AND mgg.grade_id = :gradeId:
                AND (
                    sht.pass_holder_id IN (
                        SELECT
                            hol.holder_id
                        FROM t_shinsa_date_target tsdt
                            INNER JOIN m_holder_grade mhga ON
                                mhga.holder_grade_id = tsdt.holder_grade_id
                            INNER JOIN m_holder_group mhgr ON
                                mhgr.holder_group_id = mhga.holder_group_id
                            INNER JOIN m_holder hol ON
                                hol.holder_id = mhgr.holder_id
                        WHERE
                            tsdt.shinsa_id = :shinsaId:
                    )
                    OR sht.pass_grade_group_id IN (
                        SELECT
                            gra.grade_id
                        FROM t_shinsa_date_target tsdt
                            INNER JOIN m_holder_grade mhga ON
                                mhga.holder_grade_id = tsdt.holder_grade_id
                            INNER JOIN m_grade_group mgg ON
                                mgg.grade_group_id = mhga.grade_group_id
                            INNER JOIN m_grade gra ON
                                gra.grade_id = mgg.grade_id
                        WHERE
                            tsdt.shinsa_id = :shinsaId:
                    )
                )
			ORDER BY
				sht.order_no
		';

		$bind = array(
			'shinsaId' => $shinsaDetail['shinsa_id'],
			'shinsaClassId' => $shinsaDetail['shinsa_class_id'],
			'holderId' => $memberGradeDeta['holder_id'],
			'gradeId' => $memberGradeDeta['grade_id'],
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 審査申込対象情報取得
     * @param	int		$shinsaId			審査ID
     * @param	int     $shinsaTargetId     審査対象ID
     * @return	array
	 */
	public function get_shinsa_target(int $shinsaId, int $shinsaTargetId) : array
	{
		$sql = '
            SELECT 
				msc.shinsa_class_id,
				msc.shinsa_class_name,
				mst.shinsa_target_name
			FROM 
				t_shinsa_info tsi
				INNER JOIN m_shinsa_class msc
					ON msc.shinsa_class_id = tsi.shinsa_class_id
				INNER JOIN m_shinsa_target mst
					ON mst.shinsa_class_id = msc.shinsa_class_id
					AND mst.shinsa_target_id = :shinsaTargetId:
			WHERE
				tsi.shinsa_id = :shinsaId:
		';

		$bind = array(
			'shinsaId' => $shinsaId,
			'shinsaTargetId' => $shinsaTargetId,
		);

		return $this->get_first_row($sql, $bind, 'array');
	}
	
	/**
	 * 審査申込対象一覧情報取得
     * @param	int	$gradeGroupId
     * @return	array
	 */
	public function get_grade_group(int $gradeGroupId) : array
	{
		$sql = '
            SELECT 
				gra.grade_id,
				gra.grade_name
			FROM 
				m_grade_group mgg
				INNER JOIN m_grade gra
					ON gra.grade_id = mgg.grade_id
			WHERE
				mgg.grade_group_id = :gradeGroupId:
			ORDER BY
				gra.order_no
		';

		$bind = array(
			'gradeGroupId' => $gradeGroupId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 管理者用：審査可能クラス一覧情報取得
	 * @param	array	$holderGradeIdList	称号段位IDリスト
     * @return	array
	 */
	public function get_shinsa_target_all_list(array $holderGradeIdList) : array
	{
		// プレースホルダーと bind 配列を動的に生成
		$placeholders = [];
		$bind = [];
		foreach ($holderGradeIdList as $index => $gradeId) {
			$key = 'gradeId' . $index;
			$placeholders[] = ':' . $key . ':';
			$bind[$key] = $gradeId;
		}
		$placeholderStr = implode(',', $placeholders);
		
		$sql = '
			SELECT
				mst.shinsa_target_id
				,mst.shinsa_target_name
			FROM
				m_holder_grade hgra 
				INNER JOIN m_holder_group hgro 
					ON hgro.holder_group_id = hgra.holder_group_id 
				INNER JOIN m_shinsa_target mst 
					ON mst.pass_holder_id = hgro.holder_id
			WHERE
				hgra.holder_grade_id IN (' . $placeholderStr . ')

			UNION ALL

			SELECT
				mst.shinsa_target_id
				,mst.shinsa_target_name
			FROM
				m_holder_grade hgra 
				INNER JOIN m_grade_group ggr
					ON ggr.grade_group_id = hgra.grade_group_id
				INNER JOIN m_shinsa_target mst 
					ON mst.pass_grade_group_id = ggr.grade_id 
			WHERE
				hgra.holder_grade_id IN (' . $placeholderStr . ')

        	ORDER BY shinsa_target_id ASC
		';

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 審査参加登録
     * @param int $shinsaId			審査ID
     * @param int $shinsaTargetId	審査対象ID
     * @param int $memberId			会員ID
     * @return bool
	 */
	public function shinsa_join_member(int $shinsaId, int $shinsaTargetId, int $memberId) : bool
	{
		$sql = '
			INSERT INTO t_shinsa_offer_member (
				shinsa_id,
				shinsa_target_id,
				member_id,
				created
			) VALUES (
				:shinsaId:,
				:shinsaTargetId:,
				:memberId:,
				NOW()
			)
			ON DUPLICATE KEY UPDATE
    			created = NOW()
		';

		$bind = array(
			'shinsaId' => $shinsaId,
			'memberId' => $memberId,
			'shinsaTargetId' => $shinsaTargetId,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	/**
	 * 審査キャンセル登録
     * @param int $shinsaId			審査ID
     * @param int $memberId			会員ID
     * @return bool
	 */
	public function shinsa_cancel_member(int $shinsaId, int $memberId) : bool
	{
		$sql = '
			DELETE FROM t_shinsa_offer_member 
			WHERE 
				shinsa_id = :shinsaId:
				AND member_id = :memberId:
		';

		$bind = array(
			'shinsaId' => $shinsaId,
			'memberId' => $memberId,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	/**
	 * 審査結果登録
     * @param int $shinsaId			審査ID
     * @param int $resultFlg		審査結果フラグ
     * @param int $memberId			会員ID
     * @return bool
	 */
	public function shinsa_result_report(int $shinsaId, int $resultFlg, int $memberId) : bool
	{
		$sql = '
			UPDATE t_shinsa_offer_member 
			SET
				result_flg = :resultFlg:
			WHERE
				shinsa_id = :shinsaId:
				AND member_id = :memberId:
		';

		$bind = array(
			'shinsaId' => $shinsaId,
			'memberId' => $memberId,
			'resultFlg' => $resultFlg,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	/**
	 * 審査申請者一覧情報取得
     * @param int $shinsaId				審査ID
	 * @param bool $shinsaDateAfterFlg	審査日以降フラグ
     * @return array
	 */
	public function get_shinsa_offer_member_list(int $shinsaId, bool $shinsaDateAfterFlg) : array
	{
		$order = 'som.created';
		if ($shinsaDateAfterFlg === true) {
			$order = 'som.result_flg, som.created';
		}

		$sql = '
			SELECT
				som.created,
				som.result_flg,
				som.rankup_flg,
				mem.member_id,
				mem.name_f,
				mem.name_s,
				mem.mail_address,
				mgh.holder_id,
				mgh.grade_id,
				hol.holder_name,
				gra.grade_name,
				mst.shinsa_target_name,
				mst.pass_grade_group_id,
				mst.pass_holder_id
			FROM t_shinsa_offer_member som
				INNER JOIN m_member mem ON
					mem.member_id = som.member_id
				INNER JOIN t_member_grade_holder mgh ON
					mgh.member_id = mem.member_id
				INNER JOIN m_holder hol ON
					hol.holder_id = mgh.holder_id
				INNER JOIN m_grade gra ON
					gra.grade_id = mgh.grade_id
				INNER JOIN m_shinsa_target mst ON
					mst.shinsa_target_id = som.shinsa_target_id
			WHERE
				som.shinsa_id = :shinsaId:
			ORDER BY ' . $order . '
		';

		$bind = array(
			'shinsaId' => $shinsaId,
		);
		
		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 昇段対象者一覧情報取得
     * @param int $shinsaId				審査ID
     * @return array
	 */
	public function get_shinsa_rankup_member_list(int $shinsaId) : array
	{
		$sql = '
			SELECT
				mem.name_f,
				mem.name_s,
				hol.holder_name,
				gra.grade_name
			FROM
				t_shinsa_offer_member som
			INNER JOIN m_member mem ON
				mem.member_id = som.member_id
			INNER JOIN t_member_grade_holder mgh ON
				mgh.member_id = mem.member_id
			INNER JOIN m_holder hol ON
				hol.holder_id = mgh.holder_id
			INNER JOIN m_grade gra ON
				gra.grade_id = mgh.grade_id
			WHERE
				som.shinsa_id = :shinsaId: 
				AND som.result_flg = :resultFlg:
			ORDER BY
				hol.holder_id,
				gra.grade_id,
				som.modified
		';

		$bind = array(
			'shinsaId' => $shinsaId,
			'resultFlg' => DB_FLG_ON,
		);
		
		return $this->get_result_array($sql, $bind);
	}

}