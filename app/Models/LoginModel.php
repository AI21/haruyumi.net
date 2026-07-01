<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models;

class LoginModel extends BaseQueryModel {

	protected $db;
	
	/**
	 * ログイン
	 * @param	string $memberIdMail
	 */ 
	public function login(string $memberIdMail)
	{

		$sql = '
			SELECT
				mem.member_id
                ,mem.name_f
                ,mem.name_s
                ,mem.kana_f
                ,mem.kana_s
                ,mem.gender_cd
                ,mem.login_pw
                ,mem.login_id
			FROM m_member mem
			WHERE
				mem.mail_address = :memberIdMail:
				OR mem.login_id = :memberIdMail:
		';

		$bind = [
			'memberIdMail' => $memberIdMail,
		];

		return $this->get_first_row($sql, $bind);
	}
	
	/**
	 * 会員情報取得
	 * @param	string $memberId	ユーザーID
	 */ 
	public function get_member_data(string $memberId) : ?object
	{

		$sql = '
			SELECT
				mem.kasugai_regist_flg
				,mem.kasugai_regist_date
				,mem.renmei_adjourning_flg
				,mem.aiti_renmei_regist_flg
				,mem.renmei_id
				,mem.notice_send_flg
				,mem.mail_address
				,mem.member_admin_flg
            FROM m_member mem
			WHERE
				member_id = :memberId:
		';

		$bind = [
			'memberId' => $memberId,
		];

		return $this->get_first_row($sql, $bind);
	}
	
	/**
	 * メニュー管理情報取得
     * @param   int  $memberId   ユーザーID
     * @return array
	 */
	public function get_officer_menu_id_list(int $memberId) : array
	{
		$sql = '
            SELECT
                mmc.menu_id
            FROM
                m_kyokai_officer_member kom 
                INNER JOIN m_kyokai_officer mko 
                    ON mko.kyokai_officer_id = kom.kyokai_officer_id
                    AND mko.use_flg = :useFlg:
                INNER JOIN m_kyokai_officer_menu_category mkomc 
                    ON mkomc.kyokai_officer_id = kom.kyokai_officer_id
                INNER JOIN m_menu_category mmc 
                    ON mmc.menu_category_id = mkomc.menu_category_id
            WHERE
                kom.member_id = :memberId:
            GROUP BY
                menu_id
		';

		$bind = array(
			'useFlg' => DB_FLG_ON,
			'memberId' => $memberId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * メニューカテゴリー管理情報取得
     * @param   int  $memberId   ユーザーID
     * @return array
	 */
	public function get_officer_category_id_list(int $memberId) : array
	{
		$sql = '
            SELECT
                mmc.category_id
            FROM
                m_kyokai_officer_member kom 
                INNER JOIN m_kyokai_officer mko 
                    ON mko.kyokai_officer_id = kom.kyokai_officer_id
                    AND mko.use_flg = :useFlg:
                INNER JOIN m_kyokai_officer_menu_category mkomc 
                    ON mkomc.kyokai_officer_id = kom.kyokai_officer_id
                INNER JOIN m_menu_category mmc 
                    ON mmc.menu_category_id = mkomc.menu_category_id
            WHERE
                kom.member_id = :memberId:
            GROUP BY
                category_id
		';

		$bind = array(
			'useFlg' => DB_FLG_ON,
			'memberId' => $memberId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * お知らせ管理情報取得
     * @param   int  $memberId   ユーザーID
     * @return array
	 */
	public function get_notice_admin_id_list(int $memberId) : array
	{
		$sql = '
			SELECT
				mkon.notice_category_id
			FROM
				m_kyokai_officer_member kom 
                INNER JOIN m_kyokai_officer mko 
                    ON mko.kyokai_officer_id = kom.kyokai_officer_id
                    AND mko.use_flg = :useFlg:
				INNER JOIN m_kyokai_officer_notice mkon 
					ON mkon.kyokai_officer_id = kom.kyokai_officer_id
			WHERE
				kom.member_id = :memberId:
			GROUP BY
				mkon.notice_category_id
		';

		$bind = array(
			'useFlg' => DB_FLG_ON,
			'memberId' => $memberId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * ログインID重複チェック
	 * @param	int		$memberId
	 * @param	string	$loginId
     * @return bool
	 */ 
	public function duplicate_check_login_id(int $memberId, string $loginId) : array
	{

		$sql = '
			SELECT
				member_id
			FROM m_member
			WHERE
				member_id != :memberId:
				AND login_id = :loginId:
		';

		$bind = [
			'memberId' => $memberId,
			'loginId' => $loginId,
		];

		return $this->get_first_row($sql, $bind, 'array');
	}
	
	/**
	 * ログインID重複チェック
	 * @param	int		$memberId
	 * @param	string	$mailAddress
     * @return bool
	 */ 
	public function duplicate_check_mail_address(int $memberId, string $mailAddress) : array
	{

		$sql = '
			SELECT
				member_id
			FROM m_member
			WHERE
				member_id != :memberId:
				AND mail_address = :mailAddress:
		';

		$bind = [
			'memberId' => $memberId,
			'mailAddress' => $mailAddress,
		];

		return $this->get_first_row($sql, $bind, 'array');
	}
	
	/**
	 * ログイン情報更新
	 * @param	int		$memberId
	 * @param	string	$loginId
	 * @param	string	$mailAddress
	 * @param	string	$registPassword
     * @return bool
	 */
	public function login_change_process($memberId, $loginId, $mailAddress, $registPassword) : bool
	{
		$addSql = '';
		if (empty($registPassword) === false) {
			$addSql = ',login_pw = :loginPw:';
		}

		$sql = '
			UPDATE m_member 
			SET
				member_id = :memberId:
				,mail_address = :mailAddress:
				,login_id = :loginId:
				' . $addSql . '
			WHERE 
				member_id = :memberId:
		';

		$bind = [
			'memberId' => $memberId,
			'mailAddress' => $mailAddress,
			'loginId' => $loginId,
		];
		if (empty($registPassword) === false) {
			$bind['loginPw'] = $registPassword;
		}

		return $this->get_result_query($sql, $bind);
	}

}