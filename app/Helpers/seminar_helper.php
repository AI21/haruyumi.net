<?php

// メインメニューHTML生成
function get_seminar_tab_html($seminarList) {
    
    $html = '';

    if (empty($seminarList) === false) {
        $i = 0;
        foreach ($seminarList as $tabName => $data) {
            $html_active = '';
            if ($i === 0) {
                $html_active = ' active show';
            }
            if ($data['numRows'] > 0) {
            $html .= '<div id="' . $tabName . '" class="tab-pane' . $html_active .'" role="tabpanel">';
                $html .= '<table class="table">';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th>開催日</th>';
                $html .= '<th>講習会名</th>';
                $html .= '<th>会場</th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                foreach ($data['result'] as $idx => $data) {
                    $kaijoName = (empty($data['kaijo_name_short']) === false) ? $data['kaijo_name_short'] : $data['kaijo_other_name'];
                    $html .= '<tr>';
                    $html .= '<td>' . date("n/d", strtotime($data['seminar_date_st'])) . '</td>';
                    $html .= '<td><a href="./seminar/detail/' . $data['seminar_id'] . '">' . $data['seminar_sub_name'] . '</a></td>';
                    $html .= '<td>' . $kaijoName . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</tbody>';
                $html .= '</table>';
            } else {
                $html .= '<div id="' . $tabName . '" class="tab-pane' . $html_active .'" role="tabpanel">';
                $html .= '<table class="table">';
                $html .= '<tbody>';
                $html .= '<tr>';
                $html .= '<td>情報が登録されていません</td>';
                $html .= '</tbody>';
                $html .= '</table>';
            }
            $html .= '</div>';
            $i++;
        }
    }
    
    return $html;
}

// 講習会詳細HTML生成
function get_seminar_detail_html($seminarDetail)
{
    $html = '';
    $seminarDateSt = '';
    $seminarDateEd = '';

    if (empty($seminarDetail) === false) {

        // 場所
        $kaijo = '未定';
        if (empty($seminarDetail['kaijo_name']) === false) {
            $kaijo = $seminarDetail['kaijo_name'];
        } else {
            // 会場マスタにない会場はその他会場を表示
            if (empty($seminarDetail['kaijo_other_name']) === false) {
                $kaijo = nl2br($seminarDetail['kaijo_other_name']);
            }
        }
    
        // 講習会締切日
        $limitAikyuren = '未定';
        if (empty($seminarDetail['uketuke_limit_aikyuren_st'] && $seminarDetail['uketuke_limit_aikyuren_st'] !== '0000-00-00') === false) {
            $limitAikyuren = date_period_format($seminarDetail['uketuke_limit_aikyuren_st'], $seminarDetail['uketuke_limit_aikyuren_ed'], true, DATE_FORMAT_YMD, DATE_FORMAT_YMD);
        }
    
        // 春日井弓道会締切日
        $limitKasugai = '';
        if (empty($seminarDetail['uketuke_limit_kasugai_st'] && $seminarDetail['uketuke_limit_kasugai_st'] !== '0000-00-00') === false) {
            $limitKasugai = date_period_format($seminarDetail['uketuke_limit_kasugai_st'], $seminarDetail['uketuke_limit_kasugai_ed'], true, DATE_FORMAT_YMD, DATE_FORMAT_YMD);
        }

        $html .= '<h2>' . $seminarDetail['seminar_sub_name'] . ' 詳細</h2>';
        $html .= '<div class="tab-content">';
        $html .= '<table class="table table-detail">';
		$html .= '<tbody>';
        // 日程
		$html .= '<tr>';
        $html .= '<th>日程</th>';
        // 日程が確定している場合は日付で表示
        $html .= '<td>' . date_period_format($seminarDetail['seminar_date_st'], $seminarDetail['seminar_date_ed']) . '</td>';
        // 時間
		$html .= '<tr>';
        $html .= '<th>時間</th>';
        if (empty($seminarDetail['seminar_time_st']) === true && empty($seminarDetail['seminar_time_ed']) === true) {
            // 開始時間と終了時間の双方が空の場合は未定
            $html .= '<td>未定</td>';
        } else {
            // 時間が確定している場合は時間帯で表示
            $html .= '<td>' . time_period_format($seminarDetail['seminar_time_st'], $seminarDetail['seminar_time_ed']) . '</td>';
        }
        // 開場時間
		$html .= '<tr>';
        $html .= '<th>開場時間</th>';
        if (empty($seminarDetail['seminar_open_time']) === true) {
            // 開始時間と終了時間の双方が空の場合は未定
            $html .= '<td>未定</td>';
        } else {
            // 時間が確定している場合は時間帯で表示
            $html .= '<td>' . time_format_jp($seminarDetail['seminar_open_time'], TIME_FORMAT_HI) . '</td>';
        }
        // 受付時間
		$html .= '<tr>';
        $html .= '<th>受付時間</th>';
        if (empty($seminarDetail['seminar_uketuke_time']) === true) {
            // 開始時間と終了時間の双方が空の場合は未定
            $html .= '<td>未定</td>';
        } else {
            // 時間が確定している場合は時間帯で表示
            $html .= '<td>' . time_format_jp($seminarDetail['seminar_uketuke_time'], TIME_FORMAT_HI) . '</td>';
        }
		$html .= '</tr>';
        // 会場名
		$html .= '<tr>';
        $html .= '<th>会場</th>';
		$html .= '<td>' . $kaijo . '</td>';
		$html .= '</tr>';
        // 料金
        if (empty($seminarDetail['entry_fee']) === false) {
            // 料金が設定されている場合は表示
            $html .= '<tr>';
            $html .= '<th>料金</th>';
            $html .= '<td>' . nl2br($seminarDetail['entry_fee']) . '</td>';
		    $html .= '</tr>';
        }
        // 連絡事項
        if (empty($seminarDetail['contact_info']) === false) {
            // 連絡事項が設定されている場合は表示
            $html .= '<tr>';
            $html .= '<th>連絡事項</th>';
            $html .= '<td>' . nl2br($seminarDetail['contact_info']) . '</td>';
		    $html .= '</tr>';
        }
        // 添付資料
        if (empty($seminarDetail['document_list']) === false && $seminarDetail['document_list']['numRows'] > 0) {
            // 添付資料がある場合は表示
            $html .= '<tr>';
            $html .= '<th>資料</th>';
            $html .= '<td>';
            $html .= '<ul class="list-group list-group-flush document-list">';
            foreach ($seminarDetail['document_list']['result'] as $idx => $data) {
                $html .= '<li class="list-group-item">';
                $html .= '<img src="' . SITE_ROOT . get_file_ext_icon_path($data['document_ext']) . '" alt="' . $data['document_ext'] . '"> ';
                $html .= '<a href="' . SITE_URL . $data['document_path'] . '" target="_blank">' . $data['document_name'] . '</a>';
                $html .= '</li>';
            }
            $html .= '</ul>';
            $html .= '</td>';
		    $html .= '</tr>';
        }
        // 愛弓連締切日
		$html .= '<tr>';
        $html .= '<th>愛弓連締切日</th>';
		$html .= '<td>' . $limitAikyuren . '</td>';
		$html .= '</tr>';
        // 春日井弓道会締切日
        if (empty($limitKasugai) === false) {
            $html .= '<tr>';
            $html .= '<th>春日井弓道会締切日</th>';
            $html .= '<td>' . $limitKasugai . '</td>';
            $html .= '</tr>';
        }
        // if ($seminarDetail['organizer_flg'] === false) {
        //     // 申込
        //     if ($seminarDetail['recruit_flg'] === FLG_ON) {
        //         // 参加者募集している場合のみ行表示
        //         $html .= '<tr>';
        //         $html .= '<th>申込</th>';
        //         $html .= '<td>';
        //         $html .= get_seminar_offer_html($seminarDetail);
        //         $html .= '</td>';
        //         $html .= '</tr>';
        //     };
        // } else {
        //     // 幹事の場合はイベント修正可能
        //     $html .= '<foot>';
        //     $html .= '<tr>';
        //     $html .= '<td colspan="2" class="text-center">';
		// 	$html .= '<button type="button" id="event-revision" class="btn btn-warning">内容修正</button>';
        //     $html .= '</td>';
        //     $html .= '</tr>';
        //     $html .= '</foot>';
        // }
		$html .= '</table>';
        $html .= '</div>';
    }

    // 添付資料HTML生成
    $html .= get_relation_document_html($seminarDetail['seminar_document_list']);
    
    return $html;
}
