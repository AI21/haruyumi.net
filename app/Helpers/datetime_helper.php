<?php

// 日付フォーマット：日本語
function date_format_jp($date, $weekFlg=false, $format=DATE_FORMAT_YYMMDD)
{
    $nengo = '';

    switch ($format) {
        case DATE_FORMAT_YYMMDD_NENGO :
            $fm = '年n月j日';
            break;
        case DATE_FORMAT_YMD :
            $fm = 'Y年n月j日';
            break;
        case DATE_FORMAT_YM :
            $fm = 'Y年n月';
            break;
        case DATE_FORMAT_MMDD :
            $fm = 'm月d日';
            break;
        case DATE_FORMAT_MD :
            $fm = 'n月j日';
            break;
        case DATE_FORMAT_D :
            $fm = 'j日';
            break;
        default :
            $fm = 'Y年m月d日';
    }

    $dt = new DateTime($date);
    $ret = $dt->format($fm);

    // 年号表記
    if ($format === DATE_FORMAT_YYMMDD_NENGO) {
        $year = $dt->format('Y');
        $nengo = '令和' . ($year - 2018);
    }

    // 曜日
    if ($weekFlg === true) {
        $week = array('日', '月', '火', '水', '木', '金', '土');
        $ret .= '(' . $week[$dt->format('w')] . ')';
    }

    return $nengo . $ret;
}

// 日付フォーマット：英語
function date_format_en($date, $weekFlg=false, $format=DATE_FORMAT_YYMMDD)
{
    switch ($format) {
        case DATE_FORMAT_YMD :
            $fm = 'Y/n/j';
            break;
        case DATE_FORMAT_MMDD :
            $fm = 'm/d';
            break;
        case DATE_FORMAT_MD :
            $fm = 'n/j';
            break;
        case DATE_FORMAT_D :
            $fm = 'j';
            break;
        default :
            $fm = 'Y/m/d';
    }

    // 曜日
    if ($weekFlg === true) {
        $fm .= ' D';
    }

    $dt = new DateTime($date);
    return $dt->format($fm);
}

// 時間フォーマット：日本語
function time_format_jp($time, $format=TIME_FORMAT_HI)
{
    switch ($format) {
        case TIME_FORMAT_HIS :
            $fm = 'H時i分s秒';
            break;
        case TIME_FORMAT_HI :
            $fm = 'H時i分';
            break;
        case TIME_FORMAT_GI :
            $fm = 'G時i分';
            break;
        case TIME_FORMAT_GIS :
            $fm = 'G時i分s秒';
            break;
        default :
            $fm = 'H時i分';
    }

    $now = new DateTime('now');
    $dt = new DateTime($now->format('Y-m-d ') . $time);
    return $dt->format($fm);
}

// 時間フォーマット：英語
function time_format_en($time, $format=TIME_FORMAT_HI)
{
    switch ($format) {
        case TIME_FORMAT_HIS :
            $fm = 'H:i:s';
            break;
        case TIME_FORMAT_HI :
            $fm = 'H:i';
            break;
        case TIME_FORMAT_GI :
            $fm = 'G:i';
            break;
        case TIME_FORMAT_GIS :
            $fm = 'G:i:s';
            break;
        default :
            $fm = 'H:i';
    }

    $now = new DateTime('now');
    $dt = new DateTime($now->format('Y-m-d ') . $time);
    return $dt->format($fm);
}

// 期間内チェック
function date_period_check(?string $dateSt=NULL, ?string $dateEd=NULL)
{
    if (empty($dateSt) === true || empty($dateEd) === true) {
        return PERIOD_ID_UNDEFINED;
    }
    
    $dtSt = new DateTime($dateSt);
    $dtEd = new DateTime($dateEd . ' 23:59:59');
    $dtNow = new DateTime('now');

    // 期間前
    if ($dtNow < $dtSt) {
        return PERIOD_ID_BEFORE;
    }
    // 期間終了
    if ($dtEd < $dtNow) {
        return PERIOD_ID_END;
    }
    
    return PERIOD_ID_NOW;
}

// 終了期間チェック
function date_limit_check(?string $dateLimit)
{
    if (empty($dateLimit) === true) {
        return PERIOD_ID_UNDEFINED;
    }
    
    $dtLimit = new DateTime($dateLimit . ' 23:59:59');
    $dtNow = new DateTime('now');

    // 期間終了
    if ($dtLimit < $dtNow) {
        return PERIOD_ID_END;
    }
    
    return PERIOD_ID_NOW;
}

// 期間表示
function date_period_format(string $dateSt=NULL, string $dateEd=NULL, $weekFlg=false, $formatSt=DATE_FORMAT_YYMMDD, $formatEd=DATE_FORMAT_YYMMDD, $lang=LANG_JP)
{
    $ret = '';

    // 開始日
    if (empty($dateSt) === false) {
        
        switch ($lang) {
            case LANG_JP :
                $ret .= date_format_jp($dateSt, $weekFlg, $formatSt);
                break;
            case LANG_EN :
                $ret .= date_format_en($dateSt, $weekFlg, $formatSt);
                break;
        }
        // 開始日と終了日が同じ場合はここまで
        if ($dateSt === $dateEd) {
            return $ret;
        }
    }


    // 終了日
    if (empty($dateEd) === false) {

        if (empty($ret) === false) {
            $ret .= ' ～ ';
        }
        
        switch ($lang) {
            case LANG_JP :
                $ret .= date_format_jp($dateEd, $weekFlg, $formatEd);
                break;
            case LANG_EN :
                $ret .= date_format_en($dateEd, $weekFlg, $formatEd);
                break;
        }
    }
    
    if (empty($ret) === true) {
        $ret = PERIOD_TEXT_UNDEFINED;
    }
    
    return $ret;
}

// 期間表示(短縮)
function date_period_short_format(string $dateSt=NULL, string $dateEd=NULL, $lang=LANG_JP)
{
    $ret = '';

    // 開始日
    if (empty($dateSt) === false) {
        
        switch ($lang) {
            case LANG_JP :
                $ret .= date_format_jp($dateSt, false, DATE_FORMAT_MD);
                break;
            case LANG_EN :
                $ret .= date_format_en($dateSt, false, DATE_FORMAT_MD);
                break;
        }
        // 開始日と終了日が同じ場合はここまで
        if ($dateSt === $dateEd) {
            return $ret;
        }
    }

    // 終了日
    if (empty($dateEd) === false) {

        if (empty($ret) === false) {
            // 開始日の日本語表記の日を削除
            $ret = str_replace("日", "", $ret);
            $ret .= '～';
        }

        $dtSt = new DateTime($dateSt);
        $dateStMonth = (int)$dtSt->format('n');
        $dtEd = new DateTime($dateEd);
        $dateEdMonth = (int)$dtEd->format('n');
        $format = DATE_FORMAT_D;
        if ($dateStMonth < $dateEdMonth) {
            // 月替わりは月日表示に変更
            $format = DATE_FORMAT_MD;
        }
        
        switch ($lang) {
            case LANG_JP :
                $ret .= date_format_jp($dateEd, false, $format);
                break;
            case LANG_EN :
                $ret .= date_format_en($dateEd, false, $format);
                break;
        }
    }
    
    if (empty($ret) === true) {
        $ret = PERIOD_TEXT_UNDEFINED;
    }
    
    return $ret;
}

// 期間表示（時間）
function time_period_format(string $timeSt=NULL, string $timeEd=NULL, $weekFlg=false, $formatSt=TIME_FORMAT_HIS, $formatEd=TIME_FORMAT_HIS, $lang=LANG_JP)
{
    $ret = '';

    // 開始日
    if (empty($timeSt) === false) {
        
        switch ($lang) {
            case LANG_JP :
                $ret .= time_format_jp($timeSt, $weekFlg, $formatSt);
                break;
            case LANG_EN :
                $ret .= time_format_en($timeSt, $weekFlg, $formatSt);
                break;
        }
        // 開始日と終了日が同じ場合はここまで
        if ($timeSt === $timeEd) {
            return $ret;
        }
        $ret .= ' ～ ';
    }

    // 終了日
    if (empty($timeEd) === false) {
        
        switch ($lang) {
            case LANG_JP :
                $ret .= time_format_jp($timeEd, $weekFlg, $formatEd);
                break;
            case LANG_EN :
                $ret .= time_format_en($timeEd, $weekFlg, $formatEd);
                break;
        }
    }
    
    if (empty($ret) === true) {
        $ret = PERIOD_TEXT_UNDEFINED;
    }
    
    return $ret;
}

/**
 * 日付に月数を加減算する
 * @param string $date 基準日
 * @param int $amount 加減する数（負数で減算）
 * @return string
 */
function add_month($date, $amount)
{
    // 日付部分のみ抽出
    $dateOnly = preg_replace('/^(\d{4}-\d{2}-\d{2}).*$/', '$1', $date);
    $dt = new DateTime($dateOnly);
    $dt->modify("{$amount} month");
    return $dt->format('Y-m-d');
}

/**
 * 日付に日数または週数を加減算する
 * @param string $date 基準日
 * @param int $amount 加減する数（負数で減算）
 * @param string $type 'day' または 'week'
 * @return string
 */
function add_date($date, $amount, $type='week')
{
    // 日付部分のみ抽出
    $dateOnly = preg_replace('/^(\d{4}-\d{2}-\d{2}).*$/', '$1', $date);
    $dt = new DateTime($dateOnly);
    if ($type === 'week') {
        $dt->modify("{$amount} week");
    } else {
        $dt->modify("{$amount} day");
    }
    return $dt->format('Y-m-d');
}

/**
 * 今年度かどうかチェック
 * @param string $date チェック日付
 * @return bool 今年度の場合true、それ以外false
 */
function is_this_fiscal_year($date)
{
    // 日付がNULLの場合はfalse
    if (empty($date) === true) {
        return false;
    }
    $dt = new DateTime($date);
    $year = (int)$dt->format('Y');
    $month = (int)$dt->format('n'); // 先頭の0なし月
    $fiscalYear = ($month >= 4) ? $year : $year - 1;
    $now = new DateTime('now');
    $nowYear = (int)$now->format('Y');
    $nowMonth = (int)$now->format('n'); // 先頭の0なし月
    $nowFiscalYear = ($nowMonth >= 4) ? $nowYear : $nowYear - 1;
    return ($fiscalYear === $nowFiscalYear);
}