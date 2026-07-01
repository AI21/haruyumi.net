<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models;

class AdminModel extends BaseQueryModel {

	protected $db;
	
	/**
	 * お知らせ管理フラグ取得
	 * @param	string $memberId
	 */ 
	// public function get_notice_flg(string $memberId) : ?object
	// {

	// 	$sql = '
	// 		SELECT
	// 			notice_flg
	// 		FROM m_member
	// 		WHERE
	// 			member_id = :memberId:
	// 	';

	// 	$bind = [
	// 		'memberId' => $memberId,
	// 	];

	// 	return $this->get_first_row($sql, $bind);
	// }

}