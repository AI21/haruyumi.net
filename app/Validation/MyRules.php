<?php
namespace App\Validation;

class MyRules
{
    protected $db;
    private $data = [];

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // 検証データをセット
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    // 数値チェック（空は許可）
    public function is_integer_on_empty(string $str): bool
    {
        if ($str === '') {
            return true;
        }
        return is_numeric($str);
    }
    // ひらがなチェック
    public function is_hiragana(string $str): bool
    {
        return (bool)preg_match('/^[ぁ-ゞー]+$/u', $str);
    }
    // パスワードチェック（半角英数字記号）
    public function is_password(string $str): bool
    {
        return (bool)preg_match('/^[!-~]+$/', $str);
    }
    // メールアドレス重複チェック
    public function is_unique_mail(string $mailAddress, string $member_id = null): bool
    {
        $db = \Config\Database::connect();
        $sql = "
            SELECT COUNT(member_id) AS cnt 
            FROM m_member 
            WHERE 
                mail_address = :mailAddress:
                AND member_id != :memberId:
        ";
		$bind = array(
			'mailAddress' => $mailAddress,
            'memberId' => $member_id
		);
        $query = $this->db->query($sql, $bind);
        $row = $query->getRow();
        return ($row->cnt == 0);
    }

    // 日付フォーマット検証（例: valid_date[Y-m-d]）
    public function valid_date(string $value, string $format = null): bool
    {
        if ($value === '') {
            return true;
        }
        $fmt = $format ?: 'Y-m-d';
        $d = \DateTime::createFromFormat($fmt, $value);
        return ($d && $d->format($fmt) === $value);
    }

    // 時刻フォーマット検証（例: valid_time[H:i]）
    public function valid_time(string $value, string $format = null): bool
    {
        if ($value === '') {
            return true;
        }
        $fmt = $format ?: 'H:i';
        $d = \DateTime::createFromFormat($fmt, $value);
        return ($d && $d->format($fmt) === $value);
    }

    // 日付の前後関係チェック（前>後はエラー）
    public function date_before(string $dateSt, string $dateEd): bool
    {
        if ($dateSt === '' || $dateEd === '') {
            return true;
        }
        $dSt = \DateTime::createFromFormat('Y-m-d', $dateSt);
        $dEd = \DateTime::createFromFormat('Y-m-d', $dateEd);
        if ($dSt !== false && $dEd !== false) {
            return ($dSt <= $dEd);
        }
        return false;
    }

    // 時刻の前後関係チェック（前>後はエラー）
    public function time_before(string $timeSt, string $timeEd): bool
    {
        if ($timeSt === '' || $timeEd === '') {
            return true;
        }
        
        $tSt = \DateTime::createFromFormat('H:i', $timeSt);
        $tEd = \DateTime::createFromFormat('H:i', $timeEd);

        if ($tEd !== false && $tSt !== false) {
            return ($tSt < $tEd);
        }
        return false;
    }
}