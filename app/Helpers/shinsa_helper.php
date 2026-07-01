<?php

// 審査リストHTML生成
function get_shinsa_tab_html($setting, $SelectfiscalYearData, $shinsaList)
{
    $html = '';

    if (empty($shinsaList) === false) {
        $i = 0;
        foreach ($shinsaList as $tabName => $tabData) {
            $html_active = '';
            if ($i === 0) {
                $html_active = ' active show';
            }
            if ($tabName == TAB_NAME_OTHER) {
                // $html .= '<div id="' . $tabName . '" class="tab-pane table' . $html_active .'" role="tabpanel">';
                // $html .= '<p class="bg-light">aaaaaaaaa</p>';
                // $html .= '</div>';
            } else {
                $html .= '<div id="' . $tabName . '" class="tab-pane' . $html_active .'" role="tabpanel">';
                // if (empty($tabData[SHINSA_INFORMATION]) === false) {
                //     $html .= '<p class="bg-light p-2">' . nl2br($tabData[SHINSA_INFORMATION]['category_info']) . '</p>';
                // }

                // 未来の審査
                if ($tabData[DATA_MIRAI]['numRows'] > 0) {
                    $html .= '<table class="table caption-top shisa-list">';
                    $html .= '<caption>' . $SelectfiscalYearData['wareki'] . '(' . $SelectfiscalYearData['year'] . ')年度：未開催';
                    $html .= get_shinsa_regist_button_html($tabData[DATA_OFFICER_FLG], $tabName);
                    $html .= '</caption>';
                    $html .= '<thead>';
                    $html .= '<tr>';
                    $html .= '<th>審査日</th>';
                    $html .= '<th>審査会名</th>';
                    $html .= '<th>審査会場</th>';
                    $html .= '<th>審査種別</th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody>';
                    foreach ($tabData[DATA_MIRAI]['result'] as $idx => $data) {
                        $kaijoName = '';
                        if ($data['kaijo_list']['numRows'] > 0) {
                            foreach ($data['kaijo_list']['result'] as $kaijoData) {
                                $kaijoName .= $kaijoData['kaijo_name_short'];
                                $kaijoName .= '<br>';
                            }
                            // 最後の改行削除
                            $kaijoName = rtrim($kaijoName, '<br>');
                        }
                        $html .= '<tr>';
                        $html .= '<td>' . date_period_short_format($data['shinsa_date_min'], $data['shinsa_date_max']) . '</td>';
                        $html .= '<td><a href="' . SITE_ROOT . 'shinsa/detail/' . $data['shinsa_id'] . '">' . shinsa_name_short_html($data['area_group_name'], $data['shinsa_name']) . '</a></td>';
                        $html .= '<td>' . $kaijoName . '</td>';
                        $html .= '<td>' . $data['all_holder_grade_name_short'] . '</td>';
                        // $html .= '<td>' . shinsa_target_name_html($data['date_holder_grade']) . '</td>';
                        $html .= '</tr>';
                    }
                    $html .= '</tbody>';
                    $html .= '</table>';
                } else {
                    // 未開催の審査なし：現在年度の場合のみ表示
                    if ($setting->fiscal_year_id == $SelectfiscalYearData['fiscal_year_id']) {
                        $html .= '<table class="table caption-top shisa-list">';
                        $html .= '<caption>' . $SelectfiscalYearData['wareki'] . '(' . $SelectfiscalYearData['year'] . ')年度：未開催</caption>';
                        $html .= '<tbody>';
                        $html .= '<tr>';
                        $html .= '<td>現在、開催予定の審査はありません</td>';
                        $html .= '</tr>';
                        $html .= '</tbody>';
                        $html .= '</table>';
                    }
                }

                // 過去の審査
                if ($tabData[DATA_KAKO]['numRows'] > 0) {
                    $html .= '<table class="table caption-top shisa-list">';
                    $html .= '<caption>' . $SelectfiscalYearData['wareki'] . '(' . $SelectfiscalYearData['year'] . ')年度：終了</caption>';
                    $html .= '<thead>';
                    $html .= '<tr>';
                    $html .= '<th>審査日</th>';
                    $html .= '<th>審査会名</th>';
                    $html .= '<th>審査会場</th>';
                    $html .= '<th>審査種別</th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody>';
                    foreach ($tabData[DATA_KAKO]['result'] as $idx => $data) {
                        $kaijoName = '';
                        if ($data['kaijo_list']['numRows'] > 0) {
                            foreach ($data['kaijo_list']['result'] as $kaijoData) {
                                $kaijoName .= $kaijoData['kaijo_name_short'];
                                $kaijoName .= '<br>';
                            }
                            // 最後の改行削除
                            $kaijoName = rtrim($kaijoName, '<br>');
                        }
                        $html .= '<tr>';
                        $html .= '<td>' . date_period_short_format($data['shinsa_date_min'], $data['shinsa_date_max']) . '</td>';
                        $html .= '<td><a href="' . SITE_ROOT . 'shinsa/detail/' . $data['shinsa_id'] . '">' . shinsa_name_short_html($data['area_group_name'], $data['shinsa_name']) . '</a></td>';
                        $html .= '<td>' . $kaijoName . '</td>';
                        $html .= '<td>' . shinsa_target_name_html($data['date_holder_grade']) . '</td>';
                        $html .= '</tr>';
                    }
                    $html .= '</tbody>';
                    $html .= '</table>';
                }
                $html .= '</div>';
            }
            $i++;
        }
    }
    
    return $html;
}

// 中央審査詳細HTML生成
function get_shinsa_chuou_detail_html($shinsaDetail, $shinsaTarget, $memberGradeDeta, $officerFlg)
{
    $html = '';
    $shinsaDateSt = '';
    $shinsaDateEd = '';

    if (empty($shinsaDetail) === false) {

        // 場所
        $kaijo = '未定';
        if ($shinsaDetail['kaijo_list']['numRows'] > 0) {
            $kaijo = '<ul>';
            foreach ($shinsaDetail['kaijo_list']['result'] as $kaijoData) {
                $kaijoName = '';
                if (empty($kaijoData['kaijo_name']) === false) {
                    $kaijoName = $kaijoData['kaijo_name'];
                }
                if (empty($kaijoData['additional_info']) === false) {
                    $kaijoName .= '（' . $kaijoData['additional_info'] . '）';
                }
                $kaijo .= '<li>' . $kaijoName . '</li>';
            }
            $kaijo .= '</ul>';
        }
    
        // 審査締切日
        // $limitKasugai = '未定';
        // if (empty($shinsaDetail['uketuke_limit_kasugai'] && $shinsaDetail['uketuke_limit_kasugai'] !== '0000-00-00') === false) {
        //     $limitKasugai = date_format_jp($shinsaDetail['uketuke_limit_kasugai'], true, DATE_FORMAT_YMD);
        // }
        $limitAikyuren = '未定';
        if (empty($shinsaDetail['uketuke_limit_aikyuren'] && $shinsaDetail['uketuke_limit_aikyuren'] !== '0000-00-00') === false) {
            $limitAikyuren = date_format_jp($shinsaDetail['uketuke_limit_aikyuren'], true, DATE_FORMAT_YMD);
        }
        $limitZenkyuren = '未定';
        if (empty($shinsaDetail['uketuke_limit_zenkyuren'] && $shinsaDetail['uketuke_limit_zenkyuren'] !== '0000-00-00') === false) {
            $limitZenkyuren = date_format_jp($shinsaDetail['uketuke_limit_zenkyuren'], true, DATE_FORMAT_YMD);
        }

        $html .= '<h2>' . shinsa_name_html($shinsaDetail['area_group_name'], $shinsaDetail['shinsa_name']) . ' 詳細</h2>';
        $html .= '<div class="tab-content">';
        $html .= '<table class="table table-detail">';
		$html .= '<tbody>';
        // 審査日
		$html .= '<tr>';
        $html .= '<th>審査日</th>';
        $html .= '<td>' . shinsa_day_html($shinsaDetail['date_holder_grade'], $shinsaDateSt, $shinsaDateEd) . '</td>';
		$html .= '</tr>';
        // 審査会場
		$html .= '<tr>';
        $html .= '<th>審査会場</th>';
		$html .= '<td>' . $kaijo . '</td>';
		$html .= '</tr>';
        // 審査種別
		$html .= '<tr>';
        $html .= '<th>審査種別</th>';
        $html .= '<td>' . $shinsaDetail['all_holder_grade_name'] . '</td>';
		$html .= '</tr>';
        // 全弓連締切
		$html .= '<tr>';
        $html .= '<th>全弓連締切</th>';
		$html .= '<td>' . $limitZenkyuren . '</td>';
		$html .= '</tr>';
        // 未経過の審査の場合
        if ($shinsaDetail['future_flg'] === FLG_ON) {
            // 愛弓連締切日
            $html .= '<tr>';
            $html .= '<th>愛弓連<br>申込期間</th>';
            $html .= '<td>' . date_period_format(
                $shinsaDetail['uketuke_limit_aikyuren_st'], 
                $shinsaDetail['uketuke_limit_aikyuren_ed'], 
                true, DATE_FORMAT_MD, DATE_FORMAT_MD
                ) . '<br>
                <span class="text-danger fw-bold">※申込書は消印有効ではなく必着</span></td>';
            $html .= '</tr>';
            // 申込
            $html .= '<tr>';
            $html .= '<th>申込</th>';
            $html .= '<td>';
            $html .= get_shinsa_offer_html($shinsaDetail, $shinsaTarget, $memberGradeDeta['shinsa_offer_chuou'], $shinsaDateEd);
            $html .= '</td>';
            $html .= '</tr>';
        } else {
            // 申込した場合
            $html .= '<tr>';
            $html .= '<th>申込</th>';
            $html .= '<td>審査は終了しました</td>';
            $html .= '</tr>';
        }
        // // 春日井弓道会締切日
		// $html .= '<tr>';
        // $html .= '<th>春日井弓道会締切日</th>';
		// $html .= '<td>' . $limitKasugai . '</td>';
		// $html .= '</tr>';
        
        // 内容修正ボタン
        if ($shinsaDetail['future_flg'] === FLG_ON && $officerFlg === true) {
           $html .= get_shinsa_revision_button_html($shinsaDetail, $shinsaTarget, $memberGradeDeta['shinsa_offer_chuou'], $shinsaDateSt);
        }

		$html .= '</table>';
        $html .= '</div>';
        $html .= '<input type="hidden" id="shinsa-date-ed" value="' . $shinsaDateEd . '">';
    }

    // 添付資料HTML生成
    $html .= get_relation_document_html($shinsaDetail['shinsa_document_list'], true);
    
    return $html;
}

// 連合審査詳細HTML生成
function get_shinsa_rengo_detail_html($shinsaDetail, $shinsaTarget, $memberGradeDeta, $officerFlg)
{
    $html = '';
    $shinsaDateSt = '';
    $shinsaDateEd = '';

    if (empty($shinsaDetail) === false) {

        // 場所
        $kaijo = '未定';
        if ($shinsaDetail['kaijo_list']['numRows'] > 0) {
            $kaijo = '<ul>';
            foreach ($shinsaDetail['kaijo_list']['result'] as $kaijoData) {
                $kaijoName = '';
                if (empty($kaijoData['kaijo_name']) === false) {
                    $kaijoName = $kaijoData['kaijo_name'];
                }
                if (empty($kaijoData['additional_info']) === false) {
                    $kaijoName .= '（' . $kaijoData['additional_info'] . '）';
                }
                $kaijo .= '<li>' . $kaijoName . '</li>';
            }
            $kaijo .= '</ul>';
        }
    
        // 審査締切日
        // $limitKasugai = '未定';
        // if (empty($shinsaDetail['uketuke_limit_kasugai'] && $shinsaDetail['uketuke_limit_kasugai'] !== '0000-00-00') === false) {
        //     $limitKasugai = date_format_jp($shinsaDetail['uketuke_limit_kasugai'], true, DATE_FORMAT_YMD);
        // }
        $limitAikyuren = '未定';
        if (empty($shinsaDetail['uketuke_limit_aikyuren_st'] && $shinsaDetail['uketuke_limit_aikyuren_st'] !== '0000-00-00') === false) {
            // $limitAikyuren = date_format_jp($shinsaDetail['uketuke_limit_aikyuren_st'], true, DATE_FORMAT_YMD);
            $limitAikyuren = date_period_format($shinsaDetail['uketuke_limit_aikyuren_st'], $shinsaDetail['uketuke_limit_aikyuren_ed'], true, DATE_FORMAT_YMD, DATE_FORMAT_YMD);
        }

        $html .= '<h2>' . $shinsaDetail['shinsa_name'] . ' 詳細</h2>';
        $html .= '<div class="tab-content">';
        $html .= '<table class="table table-detail">';
		$html .= '<tbody>';
        // 審査日
		$html .= '<tr>';
        $html .= '<th>審査日</th>';
        $html .= '<td>' . shinsa_day_html($shinsaDetail['date_holder_grade'], $shinsaDateSt, $shinsaDateEd) . '</td>';
		$html .= '</tr>';
        // 審査会場名
		$html .= '<tr>';
        $html .= '<th>審査会場名</th>';
		$html .= '<td>' . $kaijo . '</td>';
		$html .= '</tr>';
        // 審査種別
		$html .= '<tr>';
        $html .= '<th>審査種別</th>';
        $html .= '<td>' . shinsa_target_name_html($shinsaDetail['date_holder_grade']) . '</td>';
		$html .= '</tr>';
        // 未経過の審査の場合
        if ($shinsaDetail['future_flg'] === FLG_ON) {
            // 愛弓連締切日
            $html .= '<tr>';
            $html .= '<th>愛弓連<br>申込期間</th>';
            $html .= '<td>' . date_period_format(
                $shinsaDetail['uketuke_limit_aikyuren_st'], 
                $shinsaDetail['uketuke_limit_aikyuren_ed'], 
                true, DATE_FORMAT_MD, DATE_FORMAT_MD
                ) . '<br>
                <span class="text-danger fw-bold">※申込書は消印有効ではなく必着</span></td>';
            $html .= '</tr>';
            // 申込
            $html .= '<tr>';
            $html .= '<th>申込</th>';
            $html .= '<td>';
            $html .= get_shinsa_offer_html($shinsaDetail, $shinsaTarget, $memberGradeDeta['shinsa_offer_rengo'], $shinsaDateEd);
            $html .= '</td>';
            $html .= '</tr>';
        } else {
            // 申込した場合
            $html .= '<tr>';
            $html .= '<th>申込</th>';
            $html .= '<td>審査は終了しました</td>';
            $html .= '</tr>';
        }
        // // 申込
		// $html .= '<tr>';
        // $html .= '<th>申込</th>';
		// $html .= '<td>';
        // $html .= get_shinsa_offer_html($shinsaDetail, $shinsaTarget, $memberGradeDeta['shinsa_offer_rengo'], $shinsaDateEd);
		// $html .= '</td>';
		// $html .= '</tr>';
        // // 春日井弓道会締切日
		// $html .= '<tr>';
        // $html .= '<th>春日井弓道会締切日</th>';
		// $html .= '<td>' . $limitKasugai . '</td>';
		// $html .= '</tr>';
        // 愛弓連締切日
		$html .= '<tr>';
        $html .= '<th>愛弓連締切日</th>';
		$html .= '<td>' . $limitAikyuren . '</td>';
		$html .= '</tr>';
        
        // 内容修正ボタン
        if ($shinsaDetail['future_flg'] === FLG_ON && $officerFlg === true) {
           $html .= get_shinsa_revision_button_html($shinsaDetail, $shinsaTarget, $memberGradeDeta['shinsa_offer_chuou'], $shinsaDateSt);
        }
		$html .= '</table>';
        $html .= '</div>';
        $html .= '<input type="hidden" id="shinsa-date-ed" value="' . $shinsaDateEd . '">';
    }

    // 添付資料HTML生成
    $html .= get_relation_document_html($shinsaDetail['shinsa_document_list'], true);
    
    return $html;
}

// 地方審査詳細HTML生成
function get_shinsa_chiho_detail_html($shinsaDetail, $shinsaTarget, $memberGradeDeta, $officerFlg, $noticeCategoryId)
{
    $html = '';
    $shinsaDateSt = '';
    $shinsaDateEd = '';

    if (empty($shinsaDetail) === false) {

        // 場所
        $kaijo = '未定';
        if ($shinsaDetail['kaijo_list']['numRows'] > 0) {
            $kaijo = '<ul>';
            foreach ($shinsaDetail['kaijo_list']['result'] as $kaijoData) {
                $kaijoName = '';
                if (empty($kaijoData['kaijo_name']) === false) {
                    $kaijoName = $kaijoData['kaijo_name'];
                }
                if (empty($kaijoData['additional_info']) === false) {
                    $kaijoName .= '（' . $kaijoData['additional_info'] . '）';
                }
                $kaijo .= '<li>' . $kaijoName . '</li>';
            }
            $kaijo .= '</ul>';
        }
    
        // 審査締切日
        // $limitKasugai = '未定';
        // if (empty($shinsaDetail['uketuke_limit_kasugai'] && $shinsaDetail['uketuke_limit_kasugai'] !== '0000-00-00') === false) {
        //     $limitKasugai = date_format_jp($shinsaDetail['uketuke_limit_kasugai'], true, DATE_FORMAT_YMD);
        // }

        $html .= '<h2>' . $shinsaDetail['shinsa_name'] . ' 詳細</h2>';
        $html .= '<div class="tab-content">';
        $html .= '<table class="table table-detail">';
		$html .= '<tbody>';
        // 審査日
		$html .= '<tr>';
        $html .= '<th>審査日</th>';
        $html .= '<td>' . shinsa_day_html($shinsaDetail['date_holder_grade'], $shinsaDateSt, $shinsaDateEd) . '</td>';
		$html .= '</tr>';
        // 審査会場名
		$html .= '<tr>';
        $html .= '<th>審査会場名</th>';
		$html .= '<td>' . $kaijo . '</td>';
		$html .= '</tr>';
        // 審査種別
		$html .= '<tr>';
        $html .= '<th>審査種別</th>';
        $html .= '<td>' . shinsa_target_name_html($shinsaDetail['date_holder_grade']) . '</td>';
		$html .= '</tr>';
        // 未経過の審査の場合
        if ($shinsaDetail['future_flg'] === FLG_ON) {
            // 愛弓連締切日
            $html .= '<tr>';
            $html .= '<th>愛弓連<br>申込期間</th>';
            $html .= '<td>' . date_period_format(
                $shinsaDetail['uketuke_limit_aikyuren_st'], 
                $shinsaDetail['uketuke_limit_aikyuren_ed'], 
                true, DATE_FORMAT_MD, DATE_FORMAT_MD
                ) . '<br>
                <span class="text-danger fw-bold">※申込書は消印有効ではなく必着</span></td>';
            $html .= '</tr>';
            // 申込
            $html .= '<tr>';
            $html .= '<th>申込</th>';
            $html .= '<td>';
            $html .= get_shinsa_offer_html($shinsaDetail, $shinsaTarget, $memberGradeDeta['shinsa_offer_chiho'], $shinsaDateEd);
            $html .= '</td>';
            $html .= '</tr>';
        } else {
            // 申込した場合
            if ($shinsaDetail['sanka_flg'] === FLG_ON) {
                // 結果登録
                if (check_past_shinsa_date($shinsaDetail, $shinsaTarget) === true) {
                    $html .= '<tr>';
                    $html .= '<th>審査結果</th>';
                    $html .= '<td>';
                    $html .= get_shinsa_result_html($shinsaDetail, $shinsaTarget, $memberGradeDeta['shinsa_offer_chiho'], $shinsaDateEd);
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            } else {
                // 申込していない場合
                $html .= '<tr>';
                $html .= '<th>申込</th>';
                $html .= '<td>審査は終了しました</td>';
                $html .= '</tr>';
            }
        }
        
        // 内容修正ボタン
        if ($shinsaDetail['future_flg'] === FLG_ON && $officerFlg === true) {
           $html .= get_shinsa_revision_button_html($shinsaDetail, $shinsaTarget, $memberGradeDeta['shinsa_offer_chiho'], $shinsaDateSt);
        }

		$html .= '</table>';
        $html .= '</div>';
        $html .= '<input type="hidden" id="shinsa-date-ed" value="' . $shinsaDateEd . '">';
    }

    // 関連するお知らせ一覧
    $html .= get_relation_notice_html($shinsaDetail['relation_notice_list'], $shinsaDetail['officer_level'], $noticeCategoryId, $shinsaDetail['shinsa_id']);

    // 添付資料HTML生成
    $html .= get_relation_document_html($shinsaDetail['shinsa_document_list'], true);
    
    return $html;
}

// 審査会名HTML生成
function shinsa_name_html($areaGroupName, $shinsaName)
{
    $html = '';

    if (empty($areaGroupName) === false) {
        $html .= '【' . $areaGroupName . '】';
    }
    $html .= $shinsaName;
    
    return $html;
}

// 審査会短縮名HTML生成
function shinsa_name_short_html($areaGroupName, $shinsaName)
{
    $html = '';

    if (empty($areaGroupName) === false) {
        $html .= $areaGroupName;
    } else {
        $html .= $shinsaName;
    }
    
    return $html;
}

// 審査日程(審査種別あり)HTML生成
function shinsa_day_target_html($dateHolderGrade)
{
    $html = '';

    if ($dateHolderGrade['numRows'] > 0) {
        foreach ($dateHolderGrade['result'] as $idx => $data) {
            $html .= date_format_jp($data['shinsa_date'], true, DATE_FORMAT_YMD) . '　' . $data['holder_grade_name']. '<br>';
        }
        $html = substr($html, 0, -4);
    }
    
    return $html;
}

// 審査日程HTML生成
function shinsa_day_html($dateHolderGrade, &$shinsaDateSt, &$shinsaDateEd)
{
    $html = '';

    if ($dateHolderGrade['numRows'] > 0) {
        $shinsaDateSt = $dateHolderGrade['result'][0]['shinsa_date'];
        foreach ($dateHolderGrade['result'] as $idx => $data) {
            if ($dateHolderGrade['numRows'] === 0) {
                $html .= date_format_jp($data['shinsa_date'], true, DATE_FORMAT_YMD) . '<br>';
            } else {
                $html .= date_format_jp($data['shinsa_date'], true, DATE_FORMAT_YYMMDD) . '　' . $data['holder_grade_name'] . '<br>';
            }
            $shinsaDateEd = $data['shinsa_date'];
        }
        $html = substr($html, 0, -4);
    }
    
    return $html;
}

// 審査種別HTML生成
function shinsa_target_name_html($dateHolderGrade)
{
    $html = '';

    if ($dateHolderGrade['numRows'] > 0) {
        foreach ($dateHolderGrade['result'] as $idx => $data) {
            $html .= $data['holder_grade_name'] . '<br>';
        }
        $html = substr($html, 0, -4);
    }
    
    return $html;
}

// 申込ボタンHTML生成
function get_shinsa_offer_html($shinsaDetail, $shinsaTarget, $shinsaOfferFlg, $shinsaPeriodEd)
{
    $html = '';

    // 審査種別による受付開始・終了日
    $datePeriodSt = $shinsaDetail['created'];
    $datePeriodEd = add_date($shinsaPeriodEd, '-1');    // 審査締切日の一週間前を受付終了日に設定
    // if (empty($shinsaDetail['uketuke_limit_aikyuren_ed']) === true) {
    //     // 愛弓連の受付終了日の設定がない場合、審査締切日から3日前を受付終了日に設定
    //     $datePeriodEd = add_date($shinsaPeriodEd, '-3');
    // } else {
    //     $datePeriodEd = $shinsaDetail['uketuke_limit_aikyuren_ed'];
    // }
    // // $datePeriodSt = $shinsaDetail['uketuke_limit_aikyuren_st'];
    // $datePeriodEd = $shinsaDetail['uketuke_limit_aikyuren_ed'];
    // // $datePeriodSt = add_month($datePeriodEd, '-1');
    // switch ($shinsaDetail['shinsa_class_id']) {
    //     case SHINSA_CLASS_ID_CHUOU :
    //     case SHINSA_CLASS_ID_RENGO :
    //         // 中央と連合審査は愛弓連受付開始日の1週間前～愛弓連受付終了日
    //         $datePeriodSt = add_date($shinsaDetail['uketuke_limit_aikyuren_st'], '-1');
    //         break;
    //     case SHINSA_CLASS_ID_CHIHO :
    //         // 地方審査は愛弓連受付開始日の1週間前～審査当日
    //         $datePeriodSt = add_date($shinsaDetail['uketuke_limit_aikyuren_st'], '-1');
    //         $datePeriodEd = $shinsaPeriodEd;
    //         break;
    // }
    // // 審査種別による受付開始・終了日が未設定の場合は、審査締切日を使用
    // if (empty($datePeriodEd) === true) {
    //     $datePeriodEd = $shinsaPeriodEd;
    // }

    if ($shinsaTarget['numRows'] > 0) {
        if ($shinsaOfferFlg === false) {
            // 全弓連休会中・愛弓連に登録がなければ受信対象外
            $html .= '申込できません';
        } else {
            $datePeriodFlg = date_period_check($datePeriodSt, $datePeriodEd);
            switch ($datePeriodFlg) {
                case PERIOD_ID_BEFORE :
                    $html .= '申込受付前';
                    break;
                case PERIOD_ID_NOW :
                    if ($shinsaDetail['sanka_flg'] == DB_FLG_OFF) {
                        $html .= '対象：';
                        $html .= form_dropdown_key_unshift('shinsa_target_id', $shinsaTarget['result'], 'shinsa_target_id', 'shinsa_target_name');
                        $html .= '　<button type="button" id="shinsa-request" class="btn btn-primary btn-sm" data-request-mode="join">申込</button>';
                    } else {
                        $html .= $shinsaDetail['shinsa_target_name'];
                        $html .= '　<button type="button" id="shinsa-already" class="btn btn-success btn-sm">申込済</button>';
                        $html .= '　<button type="button" id="shinsa-request" class="btn btn-danger btn-sm application-cancel" data-request-mode="cancel">申込キャンセル</button>';
                    }
                    break;
                case PERIOD_ID_END :
                    if ($shinsaDetail['sanka_flg'] == DB_FLG_OFF) {
                        $html .= '受付終了';
                    } else {
                        $html .= $shinsaDetail['shinsa_target_name'];
                        $html .= '　<button type="button" id="shinsa-already" class="btn btn-success btn-sm">申込済</button>';
                        $html .= '　<button type="button" id="shinsa-request" class="btn btn-danger btn-sm application-cancel" data-request-mode="cancel">申込キャンセル</button>';
                    }
                    break;
                case PERIOD_ID_UNDEFINED :
                    $html .= '対象外です';
                    break;
            }
        }
    } else {
        // 受審資格なし
        $html .= '受審できません';
    }
    
    return $html;
}

// 新規登録ボタンHTML生成
function get_shinsa_regist_button_html($shinsaOfferFlg, $tabName)
{
    $html = '';
    if ($shinsaOfferFlg === true) {
        $html .= '<span class="shinsa-regist-btn"><button type="button" class="shinsa-regist btn btn-primary btn-sm" data-tab-name="' . $tabName . '">新規登録</button></span>';
    }
    return $html;
}

// 内容修正ボタンHTML生成
function get_shinsa_revision_button_html($shinsaDetail, $shinsaTarget, $shinsaOfferFlg, $shinsaDateSt)
{
    $html = '';
    if ($shinsaDateSt >= date("Y-m-d") && 
        ($shinsaDetail['officer_level'] == KYOKAI_OFFICER_LEVEL_BOSS || $shinsaDetail['officer_level'] == KYOKAI_OFFICER_LEVEL_SUB)
    ) {
        if ($shinsaOfferFlg === true) {
            $html .= '<foot>';
            $html .= '<tr>';
            $html .= '<td colspan="2" class="text-center">';
            $html .= '　<button type="button" id="shinsa-revision" class="btn btn-secondary btn-sm">内容修正</button>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</foot>';
        }
    }
    return $html;
}

// 審査日が過去かどうかチェック
function check_past_shinsa_date($shinsaDetail, $shinsaTarget)
{
    // 審査申し込みをしている場合
    if ($shinsaDetail['sanka_flg'] === DB_FLG_ON) {
        // 審査日が過去かどうかチェック
        foreach ($shinsaDetail['date_holder_grade']['result'] as $idx => $data) {
            // 審査日のいづれかが過去日であればtrue
            if (date_period_check(date("Y-m-d"), $data['shinsa_date']) === PERIOD_ID_END) {
                return true;
            }
        }
    }
    
    return false;
}

// 審査結果登録HTML生成
function get_shinsa_result_html($shinsaDetail, $shinsaTarget, $shinsaOfferFlg, $shinsaPeriodEd)
{
    $passOptions = array(
        SHINSA_RESULT_FLG_PASS => SHINSA_RESULT_VIEW_PASS,
        SHINSA_RESULT_FLG_FAIL => SHINSA_RESULT_VIEW_FAIL,
        SHINSA_RESULT_FLG_ABSTAIN => SHINSA_RESULT_VIEW_ABSTAIN,
    );

    $html = '';
    switch ($shinsaDetail['result_flg']) {
        case SHINSA_RESULT_FLG_PASS :
            // 合格
            $html .= '<span class="fs-6 fw-bold text-success">' . SHINSA_RESULT_VIEW_PASS . '</span>';
            break;
        case SHINSA_RESULT_FLG_FAIL :
            // 不合格
            $html .= '<span class="fs-6 fw-bold text-primary">' . SHINSA_RESULT_VIEW_FAIL . '</span>';
            break;
        case SHINSA_RESULT_FLG_ABSTAIN :
            // 棄権
            $html .= '<span class="fs-6 fw-bold text-danger">' . SHINSA_RESULT_VIEW_ABSTAIN . '</span>';
            break;
        default :
            // 審査結果未登録
            if ($shinsaDetail['pass_grade_group']['numRows'] > 1) {
                // 合格対象の段位・級位が複数ある場合
                $gradeOptions = array();
                $firstGradeId = null;
                foreach ($shinsaDetail['pass_grade_group']['result'] as $idx => $data) {
                    if ($idx === 0) {
                        $firstGradeId = $data['grade_id'];
                    }
                    $gradeOptions[$data['grade_id']] = $data['grade_name'];
                }
                $html .= form_dropdown_unshift('pass_grade_id', $gradeOptions);
            }
            $html .= form_dropdown_unshift('result_flg', $passOptions);
            $html .= '　<button type="button" id="shinsa-result-report" class="btn btn-primary btn-sm disabled">報告</button>';
    }
    
    return $html;
}

/**
 * 管理者用
 */

// 審査受信者一覧
function shinsa_offer_member_list_html($shinsaDetail, $shinsaOfferMemberList)
{
    $html = '';
    
    $html .= '<hr>';
    $html .= '<h2>審査申請者一覧</h2>';
    // 審査未開催の場合、かつ担当役員(主担当と副担当)の場合は申請者代理登録ボタン表示
    if ($shinsaDetail['future_flg'] === FLG_ON) {
        if ($shinsaDetail['officer_level'] === KYOKAI_OFFICER_LEVEL_BOSS || $shinsaDetail['officer_level'] === KYOKAI_OFFICER_LEVEL_SUB) {
            $html .= '<div id="">';
            $html .= '<button type="button" id="add-member-btn" class="btn btn-secondary btn-sm">申請者代理登録</button>';
            $html .= '</div>';
        }
    }
    if ($shinsaOfferMemberList['numRows'] > 0) {

        // 昇段登録完了者数
        $shinsaResultCompCnt = 0;

        $html .= '<table id="offer-ilst" class="table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>氏名</th>';
        $html .= '<th>段位</th>';
        $html .= '<th>申込</th>';
        if ($shinsaDetail['future_flg'] === FLG_ON) {
            // 審査が終了していない場合は申請日を表示
            $html .= '<th>申請日</th>';
            $html .= '<th>&nbsp;</th>';
        } else {
            // 審査が終了している場合は結果と昇段登録ボタンを表示
            $html .= '<th>結果</th>';
            $html .= '<th>&nbsp;</th>';
        }
        // $html .= '<th>申請日</th>';
        // $html .= '<th>結果</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach ($shinsaOfferMemberList['result'] as $idx => $data) {
            // 氏名
            $memberName = $data['name_f'] . ' ' . $data['name_s'];
            // 称号・段位
            $holderGrade = '';
            if (empty($data['holder_name']) === false) {
                $holderGrade .= $data['holder_name'] . ' ';
            }
            $holderGrade .= $data['grade_name'];
            // 審査結果（不合格と棄権は結果登録数をカウントアップ）
            $result = '';
            switch ($data['result_flg']) {
                case SHINSA_RESULT_FLG_PASS :
                    // 合格
                    $result = '<span class="fw-bold text-danger">' . SHINSA_RESULT_VIEW_PASS . '</span>';
                    break;
                case SHINSA_RESULT_FLG_FAIL :
                    // 不合格
                    $result .= '<span class="fw-bold text-primary">' . SHINSA_RESULT_VIEW_FAIL . '</span>';
                    break;
                case SHINSA_RESULT_FLG_ABSTAIN :
                    // 棄権
                    $result .= '<span class="fw-bold text-success">' . SHINSA_RESULT_VIEW_ABSTAIN . '</span>';
                    break;
                default :
                    // 審査結果未登録
                    $result = '未登録';
            }
            
            $html .= '<tr>';
            $html .= '<td>' . $memberName . '</td>';
            $html .= '<td>' . $holderGrade . '</td>';
            $html .= '<td>' . $data['shinsa_target_name'] . '</td>';
            if ($shinsaDetail['future_flg'] === FLG_ON) {
                // 審査が終了していない場合は申請日を表示
                $html .= '<td>' . date_format_jp($data['created'], true, DATE_FORMAT_MD) . '</td>';
                // 代理辞退ボタン表示
                $html .= '<td><button type="button" class="btn btn-danger btn-sm shinsa-offer-cancel-proxy" data-member-id="' . $data['member_id'] . '" data-member-name="' . $memberName . '" data-shinsa-target-name="' . $data['shinsa_target_name'] . '">辞退登録</button></td>';
            } else {
                // 昇段登録が完了していない場合
                if ($data['rankup_flg'] === DB_FLG_OFF) {
                    // 審査が終了している場合は結果を表示（代理結果更新リンクも追加）
                    $html .= '<td><span class="shinsa-result-report-proxy"
                                    data-member-id="' . $data['member_id'] . '" 
                                    data-member-name="' . $memberName . '" >' . $result . '</span></td>';
                    // 合格者は昇段登録ボタン表示
                    if ($data['result_flg'] === SHINSA_RESULT_FLG_PASS) {
                        $html .= '<th><button type="button" class="btn btn-warning btn-sm rankup-confrim" 
                                    data-member-id="' . $data['member_id'] . '" 
                                    data-member-name="' . $memberName . '" 
                                    data-grade-id="' . $data['grade_id'] . '" 
                                    data-holder-grade="' . $holderGrade . '" 
                                    data-pass-grade-group-id="' . $data['pass_grade_group_id'] . '"
                                    data-pass-holder-id="' . $data['pass_holder_id'] . '"
                                    >昇段登録</button></th>';
                    } elseif (empty($data['result_flg']) === true) {
                        // 審査結果未登録の場合は代理登録ボタン表示
                        $html .= '<th><button type="button" class="btn btn-secondary btn-sm shinsa-result-report-proxy" 
                                    data-member-id="' . $data['member_id'] . '" 
                                    data-member-name="' . $memberName . '" 
                                    >合否登録</button></th>';
                    } else {
                        $html .= '<th>&nbsp;</th>';
                    }
                } else {
                    $html .= '<td>' . $result . '</td>';
                    $html .= '<th>昇段登録完了</th>';
                    // 昇段登録が完了している場合は結果登録数をカウントアップ
                    $shinsaResultCompCnt++;
                }
            }
            $html .= '</tr>';
        }
        // 昇段登録完了者が1名以上いる場合はお知らせ投稿ボタンを表示
        if ($shinsaResultCompCnt > 0) {
            $html .= '<tr>';
            $html .= '<td colspan="5" class="text-center p-2">';
            $html .= '<button type="button" id="shinsa-result-pass-notice" class="btn btn-success btn-sm">合格者昇段お知らせ投稿</button>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
    } else {
        $html .= '申請者はいません';
    }

    return $html;
}
