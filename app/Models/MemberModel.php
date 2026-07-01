<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models;

class MemberModel extends BaseQueryModel {

	protected $db;

	/**
	 * 会員登録年度一覧取得
	 * @return array
	 */
	public function get_member_regist_nendo_list() : array
	{
		$sql = '
			SELECT 
				mrf.fiscal_year_id
				,mfn.year
				,mfn.wareki
			FROM 
				t_member_regist_fiscal mrf
				INNER JOIN m_fiscal_nendo mfn ON
					mfn.fiscal_year_id = mrf.fiscal_year_id
			GROUP BY
				fiscal_year_id
		';

		$bind = array(
		);

		return $this->get_result_array($sql, $bind);
	}

	/**
	 * 弓道協会役員一覧情報取得
     * @param int $kyokaiOfficerId     協会役員ID
	 */ 
	public function get_kyokai_officer_member_list(int $kyokaiOfficerId=0, int $memberId=0, int $officerLevel=0) : array{

		$ret = array();

		$sql = '
			SELECT
				mko.kyokai_officer_id
				,mko.kyokai_officer_name
				,kom.member_id
				,kom.officer_level
				,kom.mail_cc_flg
                ,mem.name_f
                ,mem.name_s
                ,mem.mail_address
			FROM
				m_kyokai_officer_member kom
			INNER JOIN m_kyokai_officer mko ON
				mko.kyokai_officer_id = kom.kyokai_officer_id
			INNER JOIN m_member mem ON
				mem.member_id = kom.member_id
			WHERE
				mko.use_flg = :useFlg: 
		';
		if ($kyokaiOfficerId > 0) {
			$sql .= ' AND mko.kyokai_officer_id = :kyokaiOfficerId: ';
		}
		if ($memberId > 0) {
			$sql .= ' AND kom.member_id = :memberId: ';
		}
		if ($officerLevel > 0) {
			$sql .= ' AND kom.officer_level = :officerLevel: ';
		}

		$bind = array(
			'useFlg' => DB_FLG_ON
		);
		if ($kyokaiOfficerId > 0) {
			$bind['kyokaiOfficerId'] = $kyokaiOfficerId;
		}
		if ($memberId > 0) {
			$bind['memberId'] = $memberId;
		}
		if ($officerLevel > 0) {
			$bind['officerLevel'] = $officerLevel;
		}

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 弓道協会員一覧情報取得
     * @param int $fiscalYearId     年度ID
	 */ 
	public function get_member_list(int $fiscalYearId) : array
	{
		// 弓道協会員一覧情報を検索
		$sql = "
			SELECT
				mem.member_id
				,mem.name_f
				,mem.name_s
				,mem.kana_f
				,mem.kana_s
				,hol.holder_name
				,gra.grade_name
				,mem.gender_cd
				,mem.aiti_renmei_regist_flg
				,mgh.holder_acquired_day
				,mgh.grade_acquired_day
                ,mgh.grade_id
				,mem.kasugai_regist_date
				,(hol.calc + gra.calc) AS holder_grade_calc
				,CASE WHEN(
					(CASE WHEN hgo.holder_priority_flg = 0 THEN mgh.grade_acquired_day ELSE mgh.holder_acquired_day END) 
					> (CASE WHEN mem.kasugai_regist_date IS NULL THEN '0000-00-00' ELSE mem.kasugai_regist_date END))
					THEN (CASE WHEN hgo.holder_priority_flg = 0 THEN mgh.grade_acquired_day ELSE mgh.holder_acquired_day END) 
					ELSE mem.kasugai_regist_date
				END acquired_day
                ,CASE WHEN hgo.holder_priority_flg = 0 THEN mgh.grade_acquired_day ELSE mgh.holder_acquired_day END rank_acquired_day
				,hgo.hg_order
			FROM
				t_member_regist_fiscal mrf
			INNER JOIN m_member mem ON
				mem.member_id = mrf.member_id
			LEFT JOIN t_member_grade_holder mgh ON
				mgh.member_id = mem.member_id
			LEFT JOIN m_holder hol ON
				hol.holder_id = mgh.holder_id
			LEFT JOIN m_grade gra ON
				gra.grade_id = mgh.grade_id
			LEFT JOIN m_holder_grade_order hgo ON
				hgo.holder_id = mgh.holder_id 
				AND hgo.grade_id = mgh.grade_id
			WHERE
				mrf.fiscal_year_id = :fiscalYearId: 
				AND mrf.withdrawal_day IS NULL
			ORDER BY
				hgo.hg_order IS NULL ASC,
				hgo.hg_order,
				acquired_day,
				mem.member_id
		";

		$bind = array(
			'fiscalYearId' => $fiscalYearId
		);

		return $this->get_result_array($sql, $bind);
	}
	
    /**
     * 会員情報取得
     * @param string $memberId 会員ID
     * @return array 会員情報
     */
	public function get_member_data(string $memberId) : object
	{

		$sql = "
			SELECT
				mem.member_id
				,mem.name_f
				,mem.name_s
				,mem.kana_f
				,mem.kana_s
				,mem.gender_cd
				,mem.kasugai_regist_flg
				,mem.kasugai_regist_date
				,mem.renmei_adjourning_flg
				,mem.aiti_renmei_regist_flg
				,mem.renmei_id
				,mem.notice_send_flg
				,mem.mail_address
				,mem.login_id
				,mem.member_admin_flg
				,mgh.holder_id
				,mgh.holder_acquired_day
				,mgh.grade_id
				,mgh.grade_acquired_day
			FROM 
				m_member mem
				INNER JOIN t_member_regist_fiscal mrf ON
					mrf.member_id = mem.member_id
					AND (mrf.withdrawal_day IS NULL OR mrf.withdrawal_day = '0000-00-00')
				INNER JOIN t_member_grade_holder mgh ON
					mgh.member_id = mem.member_id
			WHERE
				mem.member_id = :memberId:
		";
		
		$bind = array(
			'memberId' => $memberId,
		);

		return $this->get_first_row($sql, $bind);
	}
	
	/**
	 * 管理者情報取得
	 * @param	int		$memberId
	 */ 
	// public function get_admin_category_list(int $memberId) : array
	// {

	// 	$sql = '
	// 		SELECT
	// 			tma.fiscal_year_id
	// 			, tma.menu_id
	// 			, tma.category_id
	// 		FROM t_menu_admin tma
	// 		WHERE
	// 			tma.member_id = :memberId:
	// 			AND tma.fiscal_year_id = ::
	// 		ORDER BY
	// 			tma.fiscal_year_id
	// 	';

	// 	$bind = array(
	// 		'memberId' => $memberId,
	// 	);

	// 	return $this->get_result_array($sql, $bind);
	// }
	
	/**
	 * 会員名簿ファイル情報取得
	 */ 
	public function get_member_list_file() : object
	{

		$sql = '
			SELECT
				member_list_file_name
				,created
			FROM t_member_list_file
			ORDER BY
				member_list_file_id DESC
			LIMIT 1
		';

		$bind = array(
		);

		return $this->get_first_row($sql, $bind);
	}
	
	// 会員情報登録
	public function insert_member_data(array $memberData, int &$memberId) {

		$ret = array();

        // パスワード生成
        $options = [
            'cost' => 12,
        ];
        $registPassword = password_hash(MEMBER_DATA_DEFAULT_PASSWD, PASSWORD_BCRYPT, $options);

		$sql = '
			INSERT INTO m_member (
				name_f,
				name_s,
				kana_f,
				kana_s,
				gender_cd,
				kasugai_regist_flg,
				kasugai_regist_date,
				aiti_renmei_regist_flg,
				notice_send_flg,
				mail_address,
				login_pw,
				created,
				modified
			)
			VALUES(
				:nameF:,
				:nameS:,
				:kanaF:,
				:kanaS:,
				:genderCd:,
				:kasugaiRegistFlg:,
				:kasugaiRegistDate:,
				:aitiRenmeiRegistFlg:,
				:noticeSendFlg:,
				:mailAddress:,
				:loginPw:,
				NOW(),
				NOW()
			)
		';

		$bind = array(
			'nameF' => $memberData['member_name_f'],
			'nameS' => $memberData['member_name_s'],
			'kanaF' => $memberData['member_kana_f'],
			'kanaS' => $memberData['member_kana_s'],
			'genderCd' => $memberData['gender_cd'],
			'kasugaiRegistFlg' => $memberData['kasugai_regist_flg'],
			'kasugaiRegistDate' => $memberData['kasugai_regist_date'],
			'aitiRenmeiRegistFlg' => $memberData['aiti_renmei_regist_flg'],
			// 'noticeSendFlg' => $memberData['notice_send_flg'],
			'noticeSendFlg' => DB_FLG_ON,
			'mailAddress' => $memberData['mail_address'],
			'loginPw' => $registPassword,
		);

		$result = $this->get_result_query($sql, $bind);
		if ($result === true) {
			$memberId = $this->get_insert_id();
		}

		return $result;
	}
	
	// 会員登録年度の登録
	public function insert_member_regist_fiscal(array $memberData, int $memberId, int $fiscalYearId) {

		$ret = array();

		$sql = '
			INSERT INTO t_member_regist_fiscal (
				fiscal_year_id,
				member_id,
				created,
				modified
			)
			VALUES(
				:fiscalYearId:,
				:memberId:,
				NOW(),
				NOW()
			)
		';

		$bind = array(
			'fiscalYearId' => $fiscalYearId,
			'memberId' => $memberId,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	// 会員の称号と段位・級位情報登録
	public function insert_member_grade_holder(array $memberData, int $memberId, int $holderId, int $gradeId) {

		$ret = array();

		$sql = '
			INSERT INTO t_member_grade_holder (
				member_id,
				holder_id,
				holder_acquired_day,
				grade_id,
				grade_acquired_day
			)
			VALUES(
				:memberId:,
				:holderId:,
				:holderAcquiredDay:,
				:gradeId:,
				:gradeAcquiredDay:
			)
		';

		$bind = array(
			'memberId' => $memberId,
			'holderId' => $holderId,
			'holderAcquiredDay' => $memberData['holder_acquired_day'],
			'gradeId' => $gradeId,
			'gradeAcquiredDay' => $memberData['grade_acquired_day'],
		);

		return $this->get_result_query($sql, $bind);
	}
	
	// 会員情報更新
	public function update_member_data(array $memberData) {

		$ret = array();

		$sql = '
			UPDATE 
				m_member 
			SET 
				name_f = :nameF:,
				name_s = :nameS:,
				kana_f = :kanaF:,
				kana_s = :kanaS:,
				gender_cd = :genderCd:,
				kasugai_regist_flg = :kasugaiRegistFlg:,
				kasugai_regist_date = :kasugaiRegistDate:,
				aiti_renmei_regist_flg = :aitiRenmeiRegistFlg:,
				notice_send_flg = :noticeSendFlg:,
				mail_address = :mailAddress:,
				modified = NOW()
			WHERE
				member_id = :memberId:
		';

		$bind = array(
			'nameF' => $memberData['member_name_f'],
			'nameS' => $memberData['member_name_s'],
			'kanaF' => $memberData['member_kana_f'],
			'kanaS' => $memberData['member_kana_s'],
			'genderCd' => $memberData['gender_cd'],
			'kasugaiRegistFlg' => $memberData['kasugai_regist_flg'],
			'kasugaiRegistDate' => $memberData['kasugai_regist_date'],
			'aitiRenmeiRegistFlg' => $memberData['aiti_renmei_regist_flg'],
			// 'noticeSendFlg' => $memberData['notice_send_flg'],
			'noticeSendFlg' => DB_FLG_ON,
			'mailAddress' => $memberData['mail_address'],
			'memberId' => $memberData['member_id'],
		);

		return $this->get_result_query($sql, $bind);
	}
	
	// 会員の称号と段位・級位情報更新
	public function update_member_grade_holder(array $memberData, int $holderId, int $gradeId) {

		$ret = array();

		$sql = '
			UPDATE 
				t_member_grade_holder 
			SET 
				holder_id = :holderId:,
				holder_acquired_day = :holderAcquiredDay:,
				grade_id = :gradeId:,
				grade_acquired_day = :gradeAcquiredDay:
			WHERE
				member_id = :memberId:
				AND (
					holder_id != :holderId:
					OR holder_acquired_day != :holderAcquiredDay:
					OR grade_id != :gradeId:
					OR grade_acquired_day != :gradeAcquiredDay:
				)
		';

		$bind = array(
			'memberId' => $memberData['member_id'],
			'holderId' => $holderId,
			'holderAcquiredDay' => $memberData['holder_acquired_day'],
			'gradeId' => $gradeId,
			'gradeAcquiredDay' => $memberData['grade_acquired_day'],
		);

		return $this->get_result_query($sql, $bind);
	}
	
	// 会員の称号情報更新
	public function update_member_holder(int $memberId, int $holderId, string $acquiredDay) {

		$ret = array();

		$sql = '
			UPDATE 
				t_member_grade_holder 
			SET 
				holder_id = :holderId:,
				holder_acquired_day = :acquiredDay:
			WHERE
				member_id = :memberId:
		';

		$bind = array(
			'memberId' => $memberId,
			'holderId' => $holderId,
			'acquiredDay' => $acquiredDay,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	// 会員の段位・級位情報更新
	public function update_member_grade(int $memberId, int $gradeId, string $acquiredDay) {

		$ret = array();

		$sql = '
			UPDATE 
				t_member_grade_holder 
			SET 
				grade_id = :gradeId:,
				grade_acquired_day = :acquiredDay:
			WHERE
				member_id = :memberId:
		';

		$bind = array(
			'memberId' => $memberId,
			'gradeId' => $gradeId,
			'acquiredDay' => $acquiredDay,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	// 審査申込テーブルの昇段登録フラグ更新
	public function update_shinsa_offer_member_rankup_flg(int $shinsaId, int $memberId) {

		$ret = array();

		$sql = '
			UPDATE 
				t_shinsa_offer_member 
			SET 
				rankup_flg = :rankupFlg:
			WHERE
				shinsa_id = :shinsaId:
				AND member_id = :memberId:
		';

		$bind = array(
			'rankupFlg' => DB_FLG_ON,
			'shinsaId' => $shinsaId,
			'memberId' => $memberId,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	// 会員名簿ファイル情報登録
	public function insert_member_list_file(string $membreListFileName, int $memberId) {

		$ret = array();

		$sql = '
			INSERT INTO t_member_list_file (
				member_list_file_name,
				created_member_id
			) VALUES (
				:membreListFileName:,
				:memberId:
			)
		';

		$bind = array(
			'membreListFileName' => $membreListFileName,
			'memberId' => $memberId,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	/**
	 * 会員名簿ファイル更新メール配信対象会員一覧情報取得
	 */ 
	public function get_all_mail_member_list() : array
	{
		// メール配信の弓道協会員一覧情報を検索
		$sql = "
			SELECT
				mem.mail_address
				,mem.name_f
				,mem.name_s
			FROM
				m_member mem
			WHERE
				mem.kasugai_regist_flg = :kasugaiRegistFlg:
				AND mem.notice_send_flg = :noticeSendFlg:
				AND mem.mail_address != ''
		";

		$bind = array(
			'kasugaiRegistFlg' => DB_FLG_ON,
			'noticeSendFlg' => DB_FLG_ON,
		);

		return $this->get_result_array($sql, $bind);
	}

}