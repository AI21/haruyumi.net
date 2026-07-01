<?php

// 大会リストHTML生成
function get_taikai_tab_html($setting, $SelectfiscalYearData, $taikaiList)
{
    $html = '';

    // 大会タブ
    if (empty($taikaiList) === false) {
        $i = 0;
        foreach ($taikaiList as $tabName => $kakoMitai) {
            $html_active = '';
            if ($i === 0) {
                $html_active = ' active show';
            }
            $html .= '<div id="' . $tabName . '" class="tab-pane' . $html_active .'" role="tabpanel">';
            // 未開催は今年度以降のみ表示
            if ($setting->fiscal_year_id <= $SelectfiscalYearData['fiscal_year_id']) {
                if ($kakoMitai[DATA_MIRAI]['numRows'] > 0) {
                    $html .= '<table id="future-area" class="table caption-top">';
                    $html .= '<caption>' . $SelectfiscalYearData['wareki'] . '(' . $SelectfiscalYearData['year'] . ')年度：未開催</caption>';
                    $html .= '<thead>';
                    $html .= '<tr>';
                    $html .= '<th>開催日</th>';
                    $html .= '<th>大会名</th>';
                    if ($tabName === TAB_NAME_OTHER) {
                        $html .= '<th>会場</th>';
                    }
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody>';
                    foreach ($kakoMitai[DATA_MIRAI]['result'] as $idx => $data) {
                        $kaijoName = (empty($data['kaijo_name_short']) === false) ? $data['kaijo_name_short'] : $data['kaijo_other_name'];
                        $html .= '<tr>';
                        if ($data['abort_flg'] === DB_FLG_OFF) {
                            $html .= '<td>' . date("n/d", strtotime($data['taikai_date_st'])) . '</td>';
                            $html .= '<td><a href="' . SITE_ROOT . 'taikai/detail/' . $data['taikai_id'] . '">' . $data['taikai_name'] . '</a></td>';
                            if ($tabName === TAB_NAME_OTHER) {
                                $html .= '<td>' . $kaijoName . '</td>';
                            }
                        } else {
                            $html .= '<td>-</td>';
                            $html .= '<td>' . $data['taikai_name'] . ABORT_VIEW_ON . '</td>';
                            if ($tabName === TAB_NAME_OTHER) {
                                $html .= '<td>' . $kaijoName . '</td>';
                            }
                        }
                        $html .= '</tr>';
                    }
                    $html .= '</tbody>';
                    $html .= '</table>';
                } else {
                    $html .= '<table class="table caption-top">';
                    $html .= '<caption>' . $SelectfiscalYearData['wareki'] . '(' . $SelectfiscalYearData['year'] . ')年度：未開催</caption>';
                    $html .= '<tr>';
                    $html .= '<td>該当する大会はありません。</td>';
                    $html .= '</tr>';
                    $html .= '</table>';
                }
            }
            if ($kakoMitai[DATA_KAKO]['numRows'] > 0) {
                $html .= '<table id="past-area" class="table caption-top">';
                $html .= '<caption>' . $SelectfiscalYearData['wareki'] . '(' . $SelectfiscalYearData['year'] . ')年度：終了</caption>';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th>大会名</th>';
                $html .= '<th>開催日</th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                foreach ($kakoMitai[DATA_KAKO]['result'] as $idx => $data) {
                    $html .= '<tr>';
                    if ($data['abort_flg'] === DB_FLG_OFF) {
                        $html .= '<td><a href="' . SITE_ROOT . 'taikai/detail/' . $data['taikai_id'] . '">' . $data['taikai_name'] . '</a></td>';
                    } else {
                        $html .= '<td>' . $data['taikai_name'] . ABORT_VIEW_ON . '</td>';
                    }
                    $html .= '<td>' . $data['taikai_date_st'] . '</td>';
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

// 協会主催大会詳細HTML生成
function get_kasugai_taikai_detail_html($taikaiDetail, $noticeCategoryId)
{
    $html = '';

    if (empty($taikaiDetail) === false) {

        // 場所
        $kaijo = '未定';
        if (empty($taikaiDetail['kaijo_name']) === false) {
            $kaijo = $taikaiDetail['kaijo_name'];
        } else {
            // 会場マスタにない会場はその他会場を表示
            if (empty($taikaiDetail['kaijo_other_name']) === false) {
                $kaijo = $taikaiDetail['kaijo_other_name'];
            }
        }
        
        // タイトル
        $taikaiName = '';
        if (empty($taikaiDetail['taikai_sub_name']) === true) {
            if (empty($taikaiDetail['taikai_no']) === false) {
                $taikaiName = '第' . $taikaiDetail['taikai_no'] . '回 ' . $taikaiDetail['taikai_name'];
            }
        } else {
            $taikaiName = $taikaiDetail['taikai_sub_name'];
        }
        $html .= '<h2>' . $taikaiName . ' 詳細</h2>';
        $html .= '<div class="tab-content">';
        $html .= '<table class="table table-detail">';
		$html .= '<tbody>';
        // 日程
		$html .= '<tr>';
        $html .= '<th>日程</th>';
		$html .= '<td>';
        $html .= date_period_format($taikaiDetail['taikai_date_st'], $taikaiDetail['taikai_date_ed'], true, DATE_FORMAT_YMD, DATE_FORMAT_YMD);
        if (empty($taikaiDetail['taikai_open_time']) === false && $taikaiDetail['taikai_open_time'] !== '00:00:00') {
            $html .= '<br>開場時間：' . time_format_jp($taikaiDetail['taikai_open_time'], TIME_FORMAT_GI);
        }
        if (empty($taikaiDetail['taikai_uketuke_time']) === false && $taikaiDetail['taikai_uketuke_time'] !== '00:00:00') {
            $html .= '<br>受付時間：' . time_format_jp($taikaiDetail['taikai_uketuke_time'], TIME_FORMAT_GI);
        }
        if (empty($taikaiDetail['taikai_time_st']) === false && $taikaiDetail['taikai_time_st'] !== '00:00:00') {
            $html .= '<br>競技時間：' . time_format_jp($taikaiDetail['taikai_time_st'], TIME_FORMAT_GI) . ' ～ ';
        }
        if (empty($taikaiDetail['taikai_time_ed']) === false && $taikaiDetail['taikai_time_ed'] !== '00:00:00') {
            $html .= time_format_jp($taikaiDetail['taikai_time_ed'], TIME_FORMAT_GI);
        }
		$html .= '</td>';
		$html .= '</tr>';
        // 場所
		$html .= '<tr>';
        $html .= '<th>場所</th>';
		$html .= '<td>' . $kaijo . '</td>';
		$html .= '</tr>';
        // 参加資格
        if (empty($taikaiDetail['eligibility']) === false) {
            $html .= '<tr>';
            $html .= '<th>参加資格</th>';
            $html .= '<td>' . nl2br(htmlspecialchars($taikaiDetail['eligibility'])) . '</td>';
            $html .= '</tr>';
        }
        // 競技ルール
        if (empty($taikaiDetail['competition_rules']) === false) {
            $html .= '<tr>';
            $html .= '<th>競技規則</th>';
            $html .= '<td>' . nl2br(htmlspecialchars($taikaiDetail['competition_rules'])) . '</td>';
            $html .= '</tr>';
        }
        // 表彰
        if (empty($taikaiDetail['awards']) === false) {
            $html .= '<tr>';
            $html .= '<th>表彰</th>';
            $html .= '<td>' . nl2br(htmlspecialchars($taikaiDetail['awards'])) . '</td>';
            $html .= '</tr>';
        }
        // 連絡事項
        if (empty($taikaiDetail['contact_info']) === false) {
            $html .= '<tr>';
            $html .= '<th>連絡事項</th>';
            $html .= '<td>' . nl2br(htmlspecialchars($taikaiDetail['contact_info'])) . '</td>';
            $html .= '</tr>';
        }
        // 大会が未開催の場合は締切と申込ボタン表示
        if ($taikaiDetail['taikai_date_st'] >= date("Y-m-d")) {
            // 締切
            $html .= '<tr>';
            $html .= '<th>締切</th>';
            $html .= '<td>' . date_period_format($taikaiDetail['taikai_uketuke_st'], $taikaiDetail['taikai_uketuke_ed'], true, DATE_FORMAT_YMD, DATE_FORMAT_MD) . '</td>';
            $html .= '</tr>';
            if (empty($taikaiDetail['officer_level']) === true) {
                // 担当役員以外の場合は申込・キャンセルボタン表示
                $html .= '<tr>';
                $html .= '<th>申込</th>';
                $html .= '<td>';
                $datePeriodFlg = date_period_check($taikaiDetail['taikai_uketuke_st'], $taikaiDetail['taikai_uketuke_ed']);
                switch ($datePeriodFlg) {
                    case PERIOD_ID_BEFORE :
                        $html .= '申込受付前';
                        break;
                    case PERIOD_ID_NOW :
                        if ($taikaiDetail['sanka_flg'] == DB_FLG_OFF) {
                            $html .= '<button type="button" id="taikai-request" class="btn btn-primary btn-sm" data-request-mode="join">申込</button>';
                        } else {
                            $html .= '<button type="button" id="taikai-already" class="btn btn-success btn-sm">申込済</button>';
                            $html .= '<button type="button" id="taikai-request" class="btn btn-danger btn-sm application-cancel" data-request-mode="cancel">キャンセル</button>';
                        }
                        break;
                    case PERIOD_ID_END :
                        if ($taikaiDetail['sanka_flg'] == DB_FLG_ON) {
                            $html .= '<button type="button" class="btn btn-sm px-0">受付終了</button>';
                            $html .= '<button type="button" id="taikai-request" class="btn btn-danger btn-sm application-cancel" data-request-mode="cancel">キャンセル</button>';
                        }
                        break;
                    case PERIOD_ID_UNDEFINED :
                        $html .= '受付' . PERIOD_TEXT_UNDEFINED;
                        break;
                }
                $html .= '</td>';
                $html .= '</tr>';
            } else {
                // 大会日が未経過、かつ担当役員(主担当と副担当)の場合は大会情報修正可能
                if ($taikaiDetail['taikai_date_st'] > date("Y-m-d") && 
                    ($taikaiDetail['officer_level'] == KYOKAI_OFFICER_LEVEL_BOSS || $taikaiDetail['officer_level'] == KYOKAI_OFFICER_LEVEL_SUB)
                ) {
                    $html .= '<foot>';
                    $html .= '<tr>';
                    $html .= '<td colspan="2" class="text-center">';
                    $html .= '<button type="button" id="taikai-revision" class="btn btn-warning">内容修正</button>';
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '</foot>';
                }
            }
        } else {
            // 大会が終了している場合は「大会終了」を表示
            $html .= '<tr>';
            $html .= '<th></th>';
            $html .= '<td>大会は終了しました</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
		$html .= '</table>';
        $html .= '</div>';
    }

    // 関連するお知らせ一覧
    $html .= get_relation_notice_html($taikaiDetail['relation_notice_list'], $taikaiDetail['officer_level'], $noticeCategoryId, $taikaiDetail['taikai_id']);

    // 添付資料HTML生成
    $html .= get_relation_document_html($taikaiDetail['taikai_document_list'], true);
    
    return $html;
}

/**
 * 管理者用
 */

// 大会参加者一覧
function taikai_offer_member_list_html($taikaiDetail, $taikaiOfferMemberList)
{
    // 大会が未経過かどうか判定
    $taikaiProgressFlg = false;
    if ($taikaiDetail['taikai_date_ed'] > date("Y-m-d")) {
        $taikaiProgressFlg = true;
    }
    
    $html = '';
    
    $html .= '<hr>';
    $html .= '<h2>参加者一覧</h2>';
    // 大会未開催の場合、かつ担当役員(主担当と副担当)の場合は参加者代理登録ボタン表示
    if ($taikaiDetail['taikai_date_st'] >= date("Y-m-d")) {
        if ($taikaiProgressFlg === true &&
            ($taikaiDetail['officer_level'] === KYOKAI_OFFICER_LEVEL_BOSS || $taikaiDetail['officer_level'] === KYOKAI_OFFICER_LEVEL_SUB)) {
            $html .= '<div class="btn-area-officer">';
            $html .= '<button type="button" id="add-member-btn" class="btn btn-secondary btn-sm">参加者代理登録</button>';
            $html .= '</div>';
        }
    }
    if ($taikaiOfferMemberList['numRows'] > 0) {

        $html .= '<table id="offer-ilst" class="table offer-ilst">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>氏名</th>';
        $html .= '<th>段位</th>';
        $html .= '<th>申込日</th>';
        $html .= '<th class="sorter-false">合計 ' . $taikaiOfferMemberList['numRows'] . ' 名</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach ($taikaiOfferMemberList['result'] as $idx => $data) {
            // 称号・段位
            $holderGrade = '';
            if (empty($data['holder_name']) === false) {
                $holderGrade .= $data['holder_name'] . ' ';
            }
            $holderGrade .= $data['grade_name'];
            // 非表示でソートに使う値を data-text に入れる
            $nameKana = '';
            if (empty($data['kana_f']) === false) {
                $nameKana .= $data['kana_f'];
            }
            if (empty($data['kana_s']) === false) {
                $nameKana .= $data['kana_s'];
            }
            // 段位は称号ID（holder_id）と段位ID（grade_id）を結合して比較用キーにする
            $hgOrder = '';
            if (isset($data['hg_order'])) {
                $hgOrder .= $data['hg_order'];
            }

            $html .= '<tr>';
            $html .= '<td data-text="' . htmlspecialchars($nameKana, ENT_QUOTES, 'UTF-8') . '">' . $data['name_f'] . ' ' . $data['name_s'] . '</td>';
            $html .= '<td data-text="' . htmlspecialchars($hgOrder, ENT_QUOTES, 'UTF-8') . '">' . $holderGrade . '</td>';
            $html .= '<td>' . date_format_jp($data['created'], true, DATE_FORMAT_MD) . '</td>';
            $html .= '<td>';
            // 大会未開催の場合、かつ担当役員(主担当と副担当)の場合は代理辞退ボタン表示
            if ($taikaiDetail['taikai_date_st'] >= date("Y-m-d")) {
                if ($taikaiProgressFlg === true &&
                    ($taikaiDetail['officer_level'] === KYOKAI_OFFICER_LEVEL_BOSS || $taikaiDetail['officer_level'] === KYOKAI_OFFICER_LEVEL_SUB)) {
                    // 大会未経過の場合のみ代理辞退ボタン表示
                    $html .= '<button type="button" class="btn btn-danger btn-sm taikai-offer-cancel-proxy" data-member-id="' . $data['member_id'] . '" data-holder-grade="' . $holderGrade . '" data-member-name="' . $data['name_f'] . ' ' . $data['name_s'] . '">辞退登録</button>';
                } else {
                    $html .= '&nbsp;';
                }
            } else {
                $html .= '&nbsp;';
            }
            $html .= '</td>';
            $html .= '</tr>';
        }
        if ($taikaiProgressFlg === true &&
            ($taikaiDetail['officer_level'] === KYOKAI_OFFICER_LEVEL_BOSS || $taikaiDetail['officer_level'] === KYOKAI_OFFICER_LEVEL_SUB)) {
            $html .= '<tfoot>';
            $html .= '<tr>';
            $html .= '<td colspan="4" class="text-center p-2">';
            $html .= '<button type="button" id="taikai-offer-member-list-csv" class="btn btn-primary btn-sm">参加者一覧CSVダウンロード</button>';
            $html .= '</tr>';
            $html .= '</tfoot>';
        }
        $html .= '</table>';
    } else {
        $html .= '申請者はいません';
    }

    return $html;
}
