<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models;

use CodeIgniter\Model;

class OujiModel extends BaseQueryModel {

	protected $db;
    
    public function __construct($db = null)
    {
        parent::__construct($db);
    }
	
	/**
	 * 王子道場の解放予定情報を取得
     * @return array
	 */
	public function getOujiSchedule() : array
	{
		$sql = '
			SELECT
				DATE_FORMAT(use_day,\'%Y%m%d\') AS use_day
				,morning
				,afternoon
				,night
				,event
			FROM
				t_ouji_schedule
			WHERE
				use_day >= CURRENT_DATE()
				AND use_day <= DATE_ADD(CURRENT_DATE(),INTERVAL :scmax: DAY) 
		';

		$bind = array(
			'scmax' => SCHEJULE_MAX,
		);

		return $this->get_result_array($sql, $bind);
	}

    /**
     * 王子道場の確定予定情報を取得
     */
    public function getOujiReserve() {

		$sql = '
			SELECT
			DATE_FORMAT(use_day,\'%Y%m%d\') AS use_day
			,time_zone
			,TIME_FORMAT(open_time, \'%k:%i\') AS open_time
			,m_user.name_f
			,m_user.user_id
			FROM
			(
				SELECT 
					A.use_day
					,A.time_zone
					,A.open_time
					,A.user_id
					,A.del_flg
				FROM t_ouji_reserve A
				INNER JOIN (
					SELECT
						use_day
						,time_zone
						,MIN(open_time) AS open_time
					FROM
						t_ouji_reserve
					WHERE 
						use_day >= CURRENT_DATE()
						AND use_day <= DATE_ADD(CURRENT_DATE(),INTERVAL :scmax: DAY)
						AND del_flg = 0
					GROUP BY
						use_day
						,time_zone
				) B 
				ON A.use_day = B.use_day 
				AND A.time_zone = B.time_zone 
				AND A.open_time = B.open_time
			) AS TOR
			INNER JOIN m_user 
				ON TOR.user_id = m_user.user_id
		';

		$bind = array(
			'scmax' => SCHEJULE_MAX,
		);

		return $this->get_result_array($sql, $bind);
    }
	
}