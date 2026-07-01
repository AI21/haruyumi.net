<?php

// メインメニューHTML生成
function get_kyokai_event_tab_html($setting, $SelectfiscalYearData, $kyokaiEventList, $memberData) {
    
    $html = '';

    if (empty($kyokaiEventList) === false) {
        $i = 0;
        foreach ($kyokaiEventList as $tabName => $kakoMitai) {
            $html_active = '';
            if ($i === 0) {
                $html_active = ' active show';
            }
            $html .= '<div id="' . $tabName . '" class="tab-pane' . $html_active .'" role="tabpanel">';
            // 未開催は今年度以降のみ表示
            if ($setting->fiscal_year_id <= $SelectfiscalYearData['fiscal_year_id']) {
                if ($kakoMitai[DATA_MIRAI]['numRows'] > 0) {
                    $html .= '<table class="table event-list caption-top">';
                    $html .= '<caption>' . $SelectfiscalYearData['wareki'] . '(' . $SelectfiscalYearData['year'] . ')年度：未開催</caption>';
                    $html .= '<thead>';
                    $html .= '<tr>';
                    $html .= '<th>日程</th>';
                    $html .= '<th>時間</th>';
                    $html .= '<th>行事名</th>';
                    $html .= '<th>会場</th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody>';
                    foreach ($kakoMitai[DATA_MIRAI]['result'] as $idx => $data) {
                        $kaijoOtherName = (empty($data['kaijo_other_name_short']) === false) ? $data['kaijo_other_name_short'] : $data['kaijo_other_name'];
                        $kaijoName = (empty($data['kaijo_name_short']) === false) ? $data['kaijo_name_short'] : $kaijoOtherName;
                        $eventName = kyokai_event_name($data['kyokai_event_name'], $data['event_sub_name'], $data['event_no']);
                        $html .= '<tr>';
                        if ($data['abort_flg'] === DB_FLG_OFF) {
                            if ($data['event_date_ambiguous_flg'] === DB_FLG_ON) {
                                // 日程があいまいな場合は月予定で表示
                                $html .= '<td>' . date("n月", strtotime($data['event_date_st'])) . '予定</td>';
                            } else {
                                // 日程が確定している場合は日付で表示
                                $html .= '<td>' . date("n/d", strtotime($data['event_date_st'])) . '</td>';
                            }
                            $html .= '<td>' . event_time($data['event_open_time'], $data['event_time_st'], $data['event_time_ed']) . '</td>';
                            $html .= '<td><a href="' . SITE_ROOT . 'kyokai/detail/' . $data['event_id'] . '">' . $eventName . '</a></td>';
                            // $html .= '<td>' . $eventName . '</td>';
                            $html .= '<td>' . $kaijoName . '</td>';
                        } else {
                            $html .= '<td>-</td>';
                            $html .= '<td>-</td>';
                            $html .= '<td>' . $eventName . ABORT_VIEW_ON . '</td>';
                            $html .= '<td>-</td>';
                        }
                        $html .= '</tr>';
                    }
                    $html .= '</tbody>';
                    $html .= '</table>';
                } else {
                    $html .= '<table class="table event-list caption-top">';
                    $html .= '<caption>' . $SelectfiscalYearData['wareki'] . '(' . $SelectfiscalYearData['year'] . ')年度：未開催</caption>';
                    $html .= '<tbody>';
                    $html .= '<tr>';
                    $html .= '<td>情報が登録されていません</td>';
                    $html .= '</tbody>';
                    $html .= '</table>';
                }
            }
            if ($kakoMitai[DATA_KAKO]['numRows'] > 0) {
                $html .= '<table class="table event-list caption-top">';
                $html .= '<caption>' . $SelectfiscalYearData['wareki'] . '(' . $SelectfiscalYearData['year'] . ')年度：終了</caption>';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th>日程</th>';
                $html .= '<th>時間</th>';
                $html .= '<th>行事名</th>';
                $html .= '<th>会場</th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                foreach ($kakoMitai[DATA_KAKO]['result'] as $idx => $data) {
                    $html .= '<tr>';
                    $kaijoOtherName = (empty($data['kaijo_other_name_short']) === false) ? $data['kaijo_other_name_short'] : $data['kaijo_other_name'];
                    $kaijoName = (empty($data['kaijo_name_short']) === false) ? $data['kaijo_name_short'] : $kaijoOtherName;
                    $eventName = kyokai_event_name($data['kyokai_event_name'], $data['event_sub_name'], $data['event_no']);
                    $html .= '<tr>';
                    if ($data['abort_flg'] === DB_FLG_OFF) {
                        if ($data['event_date_ambiguous_flg'] === DB_FLG_ON) {
                            // 日程があいまいな場合は月予定で表示
                            $html .= '<td>' . date("n月", strtotime($data['event_date_st'])) . '予定</td>';
                        } else {
                            // 日程が確定している場合は日付で表示
                            $html .= '<td>' . date("n/d", strtotime($data['event_date_st'])) . '</td>';
                        }
                        $html .= '<td>' . event_time($data['event_open_time'], $data['event_time_st'], $data['event_time_ed']) . '</td>';
                        $html .= '<td><a href="' . SITE_ROOT . 'kyokai/detail/' . $data['event_id'] . '">' . $eventName . '</a></td>';
                        // $html .= '<td>' . $eventName . '</td>';
                        $html .= '<td>' . $kaijoName . '</td>';
                    } else {
                        $html .= '<td>-</td>';
                        $html .= '<td>-</td>';
                        $html .= '<td>' . $eventName . ABORT_VIEW_ON . '</td>';
                        $html .= '<td>-</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody>';
                $html .= '</table>';
            }
            $html .= '</div>';
            $i++;
        }
    }
    
    return $html;
}

// 協会イベント詳細HTML生成
function get_kyokai_event_detail_html($eventDetail)
{
    $html = '';
    $eventDateSt = '';
    $eventDateEd = '';

    if (empty($eventDetail) === false) {

        // 場所
        $kaijo = '未定';
        if (empty($eventDetail['kaijo_name']) === false) {
            $kaijo = $eventDetail['kaijo_name'];
        } else {
            // 会場マスタにない会場はその他会場を表示
            if (empty($eventDetail['kaijo_other_name']) === false) {
                $kaijo = nl2br($eventDetail['kaijo_other_name']);
            }
        }

        $html .= '<h2>' . $eventDetail['event_sub_name'] . ' 詳細</h2>';
        $html .= '<div class="tab-content">';
        $html .= '<table class="table table-detail">';
		$html .= '<tbody>';
        // 日程
		$html .= '<tr>';
        $html .= '<th>日程</th>';
        if ($eventDetail['event_date_ambiguous_flg'] === DB_FLG_ON) {
            // 日程があいまいな場合は月予定で表示
            $html .= '<td>' . date("n月", strtotime($eventDetail['event_date_st'])) . '予定</td>';
        } else {
            // 日程が確定している場合は日付で表示
            $html .= '<td>' . date_period_format($eventDetail['event_date_st'], $eventDetail['event_date_ed']) . '</td>';
        }
        // 時間
		$html .= '<tr>';
        $html .= '<th>時間</th>';
        if (empty($eventDetail['event_time_st']) === true && empty($eventDetail['event_time_ed']) === true) {
            // 開始時間と終了時間の双方が空の場合は未定
            $html .= '<td>未定</td>';
        } else {
            // 時間が確定している場合は時間帯で表示
            $html .= '<td>' . time_period_format($eventDetail['event_time_st'], $eventDetail['event_time_ed']) . '</td>';
        }
        // 開場時間
		$html .= '<tr>';
        $html .= '<th>開場時間</th>';
        if (empty($eventDetail['event_open_time']) === true) {
            // 開始時間と終了時間の双方が空の場合は未定
            $html .= '<td>未定</td>';
        } else {
            // 時間が確定している場合は時間帯で表示
            $html .= '<td>' . time_format_jp($eventDetail['event_open_time'], TIME_FORMAT_HI) . '</td>';
        }
		$html .= '</tr>';
        // 会場名
		$html .= '<tr>';
        $html .= '<th>会場</th>';
		$html .= '<td>' . $kaijo . '</td>';
		$html .= '</tr>';
        // 料金
        if (empty($eventDetail['entry_fee']) === false) {
            // 料金が設定されている場合は表示
            $html .= '<tr>';
            $html .= '<th>料金</th>';
            $html .= '<td>' . nl2br($eventDetail['entry_fee']) . '</td>';
		$html .= '</tr>';
        }
        // 添付資料
        // if (empty($eventDetail['document_list']) === false && $eventDetail['document_list']['numRows'] > 0) {
        //     // 添付資料がある場合は表示
        //     $html .= '<tr>';
        //     $html .= '<th>資料</th>';
        //     $html .= '<td>';
        //     $html .= '<ul class="list-group list-group-flush document-list">';
        //     foreach ($eventDetail['document_list']['result'] as $idx => $data) {
        //         $html .= '<li class="list-group-item">';
        //         $html .= '<img src="' . SITE_ROOT . get_file_ext_icon_path($data['document_ext']) . '" alt="' . $data['document_ext'] . '"> ';
        //         $html .= '<a href="' . SITE_URL . $data['document_path'] . '" target="_blank">' . $data['document_name'] . '</a>';
        //         $html .= '</li>';
        //     }
        //     $html .= '</ul>';
        //     $html .= '</td>';
		//     $html .= '</tr>';
        // }
        // 受付期間内の場合
        if (date_period_check($eventDetail['event_uketuke_date_st'], $eventDetail['event_uketuke_date_ed']) === PERIOD_ID_NOW) {
            if ($eventDetail['organizer_flg'] === false) {
                // 申込
                if ($eventDetail['recruit_flg'] === FLG_ON) {
                    // 参加者募集している場合のみ行表示
                    $html .= '<tr>';
                    $html .= '<th>申込</th>';
                    $html .= '<td>';
                    $html .= get_event_offer_html($eventDetail);
                    $html .= '</td>';
                    $html .= '</tr>';
                };
            } else {
                // 幹事の場合はイベント修正可能
                $html .= '<foot>';
                $html .= '<tr>';
                $html .= '<td colspan="2" class="text-center">';
                $html .= '<button type="button" id="event-revision" class="btn btn-warning">内容修正</button>';
                $html .= '</td>';
                $html .= '</tr>';
                $html .= '</foot>';
            }
        }
        
        // 内容修正ボタン
        $html .= get_event_revision_button_html($eventDetail);

		$html .= '</table>';
        $html .= '</div>';
    }

    // 添付資料HTML生成
    $html .= get_relation_document_html($eventDetail['event_document_list']);
    
    return $html;
}

// 内容修正ボタンHTML生成
function get_event_revision_button_html($eventDetail)
{
    $html = '';

    // イベント日程が未来で、かつ幹事がメイン幹事の場合は内容修正ボタンを表示
    if ($eventDetail['event_date_st'] >= date("Y-m-d") && $eventDetail['organizer_main_flg'] == ORGANIZER_LEVEL_MAIN) {
        $html .= '<foot>';
        $html .= '<tr>';
        $html .= '<td colspan="2" class="text-center">';
        $html .= '<button type="button" id="event-revision" class="btn btn-secondary btn-sm">内容修正</button>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</foot>';
    }
    return $html;
}

// 行事名
function kyokai_event_name($eventName, $eventSubName, $eventNo)
{
    $val = '';

    if (empty($eventNo) === false) {
        $val .= '第' . $eventNo . '回 ';
    }
    if (empty($eventSubName) === false) {
        $val .= $eventSubName;
    } else {
        $val .= $eventName;
    }
    
    return $val;
}

// 時間表示
function event_time($openTime, $timeSt, $timeEd)
{
    $val = '';

    if (empty($openTime) === false) {
        $val .= time_format_en($openTime);
    } else {
        if (empty($timeSt) === false) {
            $val .= time_format_en($timeSt);
        }
    }
    if (empty($timeEd) === false) {
        if (empty($val) === false) {
            $val .= ' ～ ';
        }
        $val .= time_format_en($timeEd);
    }
    
    return $val;
}

// 申込ボタンHTML生成
function get_event_offer_html($eventDetail)
{
    $html = '';

    // 参加期日チェック
    $datePeriodFlg = date_period_check($eventDetail['event_uketuke_date_st'], $eventDetail['event_uketuke_date_ed']);
    switch ($datePeriodFlg) {
        case PERIOD_ID_BEFORE :
            $html .= '参加受付前';
            break;
        case PERIOD_ID_NOW :
            if ($eventDetail['sanka_flg'] == DB_FLG_OFF) {
                $html .= '　<button type="button" id="event-request" class="btn btn-primary btn-sm" data-request-mode="join">参加申込</button>';
            } else {
                $html .= '　<button type="button" id="event-already" class="btn btn-success btn-sm">参加申込済</button>';
                $html .= '　<button type="button" id="event-request" class="btn btn-danger btn-sm event-cancel" data-request-mode="cancel">参加キャンセル</button>';
            }
            break;
        case PERIOD_ID_END :
            if ($eventDetail['sanka_flg'] == DB_FLG_OFF) {
                $html .= '受付終了';
            } else {
                $html .= '　<button type="button" id="event-already" class="btn btn-success btn-sm">参加申込済</button>';
                $html .= '　<button type="button" id="event-request" class="btn btn-danger btn-sm event-cancel" data-request-mode="cancel">参加キャンセル</button>';
            }
            break;
        case PERIOD_ID_UNDEFINED :
            $html .= '対象外です';
            break;
    }
    
    return $html;
}

/**
 * イベントお知らせ一覧
 */

// イベント参加者一覧
function event_detail_notice_list_html($eventDetail)
{
    $html = '';
    
    $html .= '<h2>イベントお知らせ一覧</h2>';
    // イベント幹事はリンク表示
    if ($eventDetail['organizer_flg'] === true) {
        $html .= '<div id="admin-regist-link">';
        $html .= '<a href="admin/notice_regist/5">新規投稿</a>';
        $html .= '</div>';
    }
    // if ($memberData['notice_posting_flg'] === true) {
    //     $html .= '<div id="admin-regist-link">';
    //     $html .= '<a href="admin/notice_regist/' . $noticeCategoryId . '">新規投稿</a>';
    //     $html .= '</div>';
    // }
    // if ($noticeList['numRows'] > 0) {
    //     $html .= '<table id="notice-list">';
    //     $html .= '<thead>';
    //     $html .= '<tr>';
    //     $html .= '<th>カテゴリー</th>';
    //     $html .= '<th>タイトル</th>';
    //     $html .= '<th>登録日</th>';
    //     $html .= '</tr>';
    //     $html .= '</thead>';
    //     $html .= '<tbody>';
    //     foreach ($noticeList['result'] as $idx => $data) {
    //         $html .= '<tr>';
    //         $html .= '<td class="mode"><span class="item btn btn-' . $data['theme_coller'] . '">' . $data['notice_category_name'] . '</span></td>';
    //         $html .= '<td class="title"><a href="#" class="notice-view" data-notice-info-id="' . $data['notice_info_id'] . '">' . $data['notice_title'] . '</a></td>';
    //         $html .= '<td class="date">' . date_format_jp($data['created'], false, DATE_FORMAT_MD) . '</td>';
    //         $html .= '</tr>';
    //     }
    //     $html .= '</tbody>';
    //     $html .= '</table>';
    //     // ページネーション
    //     $html .= '<nav id="pager" aria-label="Page navigation example">';
    //     $html .= '<ul class="pagination">';
    //     $html .= '<li class="page-item"><a class="page-link first" href="#">最初</a></li>';
    //     $html .= '<li class="page-item"><a class="page-link prev" href="#" rel="prev">前へ</a></li>';
    //     // $html .= '<li class="page-item"><a class="page-link direct p_1" page="1" href="#">1</a></li>';
    //     // $html .= '<li class="page-item"><a class="page-link" href="#">2</a></li>';
    //     // $html .= '<li class="page-item"><a class="page-link" href="#">3</a></li>';
    //     $html .= '<li class="page-item"><a class="page-link next" href="#" rel="next">次へ</a></li>';
    //     $html .= '<li class="page-item"><a class="page-link last" href="#">最後</a></li>';
    //     $html .= '</ul>';
    //     $html .= '</nav>';
    // } else {
    //     $html .= '<p>お知らせはありません</p>';
    // }

    return $html;
}

/**
 * 管理者・幹事用
 */

// イベント参加者一覧
function event_offer_member_list_html($eventDetail, $eventOfferMemberList)
{
    $html = '';
    
    $html .= '<h2>イベント参加者一覧</h2>';
    if ($eventOfferMemberList['numRows'] > 0) {
        $html .= '<table class="table offer-ilst">';
        $html .= '<tr>';
        $html .= '<th>氏名</th>';
        if ($eventDetail['recruit_rank_flg'] === FLG_ON) {
            $html .= '<th>段位</th>';
        }
        $html .= '<th>申込日</th>';
        $html .= '</tr>';
        foreach ($eventOfferMemberList['result'] as $idx => $data) {            
            $html .= '<tr>';
            $html .= '<td>' . $data['name_f'] . ' ' . $data['name_s'] . '</td>';
            if ($eventDetail['recruit_rank_flg'] === FLG_ON) {
                $rank = (empty($data['holder_name']) === false) ? $data['holder_name'] : '';
                $rank .= ' ' . $data['grade_name'];
                $html .= '<td>' . $rank . '</td>';
            }
            $html .= '<td>' . date_format_jp($data['modified'], true, DATE_FORMAT_MD) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
    } else {
        $html .= '参加者はいません';
    }

    return $html;
}