<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models;

use CodeIgniter\Model;

class BaseQueryModel extends Model {

	protected $db;

    public function __construct($db = null)
    {
        parent::__construct();
        if ($db !== null) {
            $this->db = $db;
        }
    }

    /**
     * トランザクション開始
     */
    public function trans_start()
    {
        $this->db->transStart();
    }

    /**
     * トランザクションコミット/ロールバック
     */
    public function trans_complete()
    {
        $this->db->transComplete();
    }

    /**
     * トランザクションの状態取得（true:成功, false:失敗）
     */
    public function trans_status()
    {
        return $this->db->transStatus();
    }

    /**
     * 明示的にロールバック
     */
    public function trans_rollback()
    {
        $this->db->transRollback();
    }

    /**
     * 明示的にコミット
     */
    public function trans_commit()
    {
        $this->db->transCommit();
    }
	
	/**
	 * SQL結果取得
	 */
	public function get_result_array(string $sql, array $bind) : array
	{
		$ret = array(
			'numRows' => 0,
			'result' => array()
		);

		// SQL実行
		$query = $this->db->query($sql, $bind);

		// データ数
		$ret['numRows'] = $query->getNumRows();

		// 結果取得
		if ($ret['numRows'] > 0) {
			$ret['result'] = $query->getResultArray();
		}

		return $ret;
	}
	
	/**
	 * SQL結果取得
	 */
	public function get_first_row(string $sql, array $bind, string $type = 'object')
	{
		$ret = [];

		// SQL実行
		$query = $this->db->query($sql, $bind);

		// データ数
		$numRows = $query->getNumRows();

		// 結果取得
		if ($numRows > 0) {
			$ret = $query->getFirstRow($type);
		}

		return $ret;
	}
	
	/**
	 * SQL結果取得：実行結果のみ
	 */
	public function get_result_query(string $sql, array $bind)
	{
		$ret = [];

		// SQL実行
		$ret = $this->db->query($sql, $bind);

		return $ret;
	}
	
	/**
	 * INSERTのID取得：
	 */
	public function get_insert_id()
	{
		return $this->db->insertID();
	}

}