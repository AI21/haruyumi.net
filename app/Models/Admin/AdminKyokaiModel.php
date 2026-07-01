<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models\Admin;

use App\Models\BaseQueryModel;

class AdminKyokaiModel extends BaseQueryModel {

	protected $db;

	/**
	 * 添付資料の最大資料ID取得
	 */
	public function get_max_document_id(int $eventId) : int
	{
		$sql = '
			SELECT
				IFNULL(MAX(document_id), 0) AS max_document_id
			FROM
				t_document_event
			WHERE
				event_id = :eventId:
		';

		$bind = array(
			'eventId' => $eventId,
		);
		
		if ($row = $this->get_first_row($sql, $bind, 'array')) {
			return (int)$row['max_document_id'];
		} else {
			return 0;
		}
	}
	
	// 関連ファイル情報登録
	public function insert_document_event(int $eventId, int $documentId, int $documentTypeId, string $documentName, string $documentExt, string $documentPath) {

		$ret = array();

		$sql = '
			INSERT INTO t_document_event (
				event_id
				,document_id
				,document_type_id
				,document_name
				,document_ext
				,document_path
				,created
			) VALUES (
				:eventId:,
				:documentId:,
				:documentTypeId:,
				:documentName:,
				:documentExt:,
				:documentPath:,
				NOW()
			)
		';

		$bind = array(
			'eventId' => $eventId,
			'documentId' => $documentId,
			'documentTypeId' => $documentTypeId,
			'documentName' => $documentName,
			'documentExt' => $documentExt,
			'documentPath' => $documentPath,
		);

		return $this->get_result_query($sql, $bind);
	}

	// お知らせ関連ファイル登録
	public function insert_notice_rerlation_document_event(int $eventId, int $noticeInfoId) : bool
	{
		$sql = '
			INSERT INTO t_document_event ( 
				event_id
				, document_id
				, document_type_id
				, document_name
				, document_ext                              -- ファイル拡張子
				, document_path
				, notice_info_id                            -- お知らせ投稿ID
				, created
			) 
			SELECT
				:eventId:
				, @rownum := @rownum + 1 AS document_id
				, :dbFlgOff:
				, document_name                             -- document_name
				, document_ext                              -- ファイル拡張子
				, document_path                             -- document_path
				, notice_info_id                            -- notice_info_id
				, NOW() 
			FROM
				t_document_notice,
				(SELECT @rownum := (SELECT IFNULL(MAX(document_id), 0) FROM t_document_event WHERE event_id = :eventId:)) AS init
			WHERE
				notice_info_id = :noticeInfoId:
			ORDER BY
				document_id
		';

		$bind = array(
			'eventId' => $eventId,
			'dbFlgOff' => DB_FLG_OFF,
			'noticeInfoId' => $noticeInfoId,
		);

		return $this->get_result_query($sql, $bind);
	}

	// 関連ファイル情報更新
	public function update_document_event(int $beforeEventId, int $afterEventId, int $noticeInfoId) : bool
	{
		$sql = '
			UPDATE t_document_event
			SET
				event_id = :afterEventId:
			WHERE
				event_id = :beforeEventId:
				AND notice_info_id = :noticeInfoId:
		';

		$bind = array(
			'beforeEventId' => $beforeEventId,
			'afterEventId' => $afterEventId,
			'noticeInfoId' => $noticeInfoId,
		);

		return $this->get_result_query($sql, $bind);
	}

	// お知らせ関連ファイル情報削除
	public function delete_relation_notice_document_event(int $noticeInfoId) : bool
	{
		$sql = '
			DELETE FROM t_document_event
			WHERE
				notice_info_id = :noticeInfoId:
		';

		$bind = array(
			'noticeInfoId' => $noticeInfoId,
		);
		
		return $this->get_result_query($sql, $bind);
	}

}