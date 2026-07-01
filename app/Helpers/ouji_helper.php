<?php

// 王子道場会場予定表
function get_ouji_calendar_html($oujiSchedule, $arrayHoliday) {

    $html = '';

    $html .= '<table id="ouji_shejule" class="table caption-top shisa-list">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>日程</th>';
    $html .= '<th class="times">午前</th>';
    $html .= '<th class="times">午後</th>';
    $html .= '<th class="times">夜間</th>';
    $html .= '<th>備考</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    for ($i=0; $i<SCHEJULE_MAX; $i++) {
        $w = date('w', strtotime("+$i day"));
        $day = date('Ymd', strtotime("+$i day"));
        $sc = $oujiSchedule[$day];

        $class = '';
        if (WEEK_JP[$w] == '土') {
            $class = 'saturday';
        } elseif (WEEK_JP[$w] == '日' || in_array($day, $arrayHoliday)) {
            $class = 'sunday';
        }

        $html .= '<tr class="' . $class . '">';
        $html .= '<td class="day">' . date('m/d', strtotime("+$i day")) . '(' . WEEK_JP[$w] . ')</td>';
        $html .= '<td class=""></td>';
        // {if $sc['morning'] eq '－'}no-use{elseif $sc['morning'] eq '○'}{elseif $sc['morning'] eq '体育館'}taiiku{else}open{/if}">{$sc['morning']}
        //         {if $sc['morning_open_user'] ne ''}<span>({$sc['morning_open_user']})</span>{/if}
        $html .= '<td class=""></td>';
        $html .= '<td class=""></td>';
        $html .= '<td class="event"></td>';
        $html .= '</tr>';
    }
    $html .= '</tbody>';
    $html .= '</table>';

    return $html;
}

// 指定年度の祝日配列取得
function getArrayHolidays($year) {

	$ret = array();

	// カレンダーID
	//$calendar_id = urlencode('japanese__ja@holiday.calendar.google.com');  // Googleの提供する日本の祝日カレンダー
	$calendar_id = urlencode('ja.japanese.official#holiday@group.v.calendar.google.com');  // Googleの提供する日本の祝日カレンダー
	// データの開始日
	$start = date($year . '-01-01\T00:00:00\Z');
	// データの終了日
	$end = date($year + 1 . '-12-31\T00:00:00\Z');

	$url = "https://www.googleapis.com/calendar/v3/calendars/" . $calendar_id . "/events?";
	$query = [
		'key' => GOOGLE_CALENDAR_API_KEY,
		'timeMin' => $start,
		'timeMax' => $end,
		'maxResults' => 50,
		'orderBy' => 'startTime',
		'singleEvents' => 'true'
	];

	// if ($data = file_get_contents($url. http_build_query($query), true)) {
	// 	$data = json_decode($data);
	// 	// $data->itemには日本の祝日カレンダーの"予定"が入ってきます
	// 	foreach ($data->items as $row) {
	// 		$ret[] = str_replace('-', '', $row->start->date);
	// 	}
	// }

	//配列として祝日を返す
	return $ret;
}
