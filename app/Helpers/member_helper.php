<?php

// 会員名簿HTML生成
function get_kaiin_list_file_html($memberData, $memberListFile) {
    
    $date = new DateTime($memberListFile->created);
    $fileTime = $date->format('YmdHis');
    $memberFilePath = SITE_ROOT . KASUGAI_MEMBER_LIST_PATH . $memberListFile->member_list_file_name . '?upd=' . $fileTime;
    $filemtime = '';
    // ファイルが存在する場合、更新日を取得
    if (file_exists('./' . KASUGAI_MEMBER_LIST_PATH . $memberListFile->member_list_file_name) === true) {
        $filemtime = date ("Y年n月d日", filemtime('./' . KASUGAI_MEMBER_LIST_PATH . $memberListFile->member_list_file_name));
    }
    $html = '';
    $html .= '<section class="bg-light p-3">';
    $html .= '<div>';
    // 会員管理者はリンク表示
    if ($memberData['member_admin_flg'] === DB_FLG_ON) {
        $html .= '<div id="admin-regist-link">';
        $html .= '<a href="' . SITE_ROOT . 'admin/member_list_file_regist/">リスト更新</a>';
        $html .= '</div>';
    }
    $html .= '<dl>';
    $html .= '<dt><a href="' . $memberFilePath . '" target="_blank">会員リスト</a><dt>';
    $html .= '<dd>更新日：' . $filemtime . '<dd>';
    $html .= '</dl>';
    $html .= '</div>';
    $html .= '</section>';
    
    return $html;
}

// 会員名簿HTML生成
function get_kaiin_list_html($memberData, $memberList) {
    
    $html = '';

    // 会員管理者以外は表示しない
    // if ($memberData['member_admin_flg'] !== DB_FLG_ON) {
    //     return $html;
    // }

    if ($memberList['numRows'] > 0) {
        $html .= '<section class="bg-light p-2">';
        $html .= '<p class="m-0"><span class="member-new">今年度の新入会員</span></p>';
        $html .= '<p class="m-0"><span class="rankup">今年度の昇格・昇段・昇級者</span></p>';
        $html .= '</section>';
        $breakRank = 0;
        foreach ($memberList['result'] as $idx => $data) {
            // 今年度会員かどうかで表示切替
            $addCss = '';
            $dataMemberId = '';
            if (is_this_fiscal_year($data['kasugai_regist_date']) === true) {
                $addCss = ' member-new';
            }
            // 今年度に昇段・昇級した場合は表示切替（昇段・昇級が協会入会日以降が対象）
            if ($data['grade_id'] > 0 && 
                is_this_fiscal_year($data['rank_acquired_day']) === true &&
                $data['rank_acquired_day'] > $data['kasugai_regist_date'])
            {
                $addCss .= ' rankup';
            }
            // 会員管理者の場合、会員編集JSクラス追加
            if ($memberData['member_admin_flg'] === DB_FLG_ON) {
                $addCss .= ' member-revision';
                $dataMemberId = 'data-member-id="' . $data['member_id'] . '"';
            }
            $name = $data['name_f'] . ' ' . $data['name_s'];
            $kana = $data['kana_f'] . ' ' . $data['kana_s'];
            $rank = $data['holder_name'] . $data['grade_name'];
            $gender = ($data['gender_cd'] == 1) ? 'men' : 'wem';

            if ($breakRank > 0 && $breakRank != $data['holder_grade_calc']) {
                $html .= '</div>';
                $html .= '</section>';
            }
            if ($breakRank != $data['holder_grade_calc']) {
                $html .= '<section class="grade">';
                $html .= '<div>';
                $html .= '<h6>' . $rank . '</h6>';
            }
            $html .= '<p class="member ' . $gender . $addCss . '" ' . $dataMemberId . '>';
            $html .= '<ruby>' . $name . '<rt>' . $kana . '</rt></ruby>';
            $html .= '</p>';
            $breakRank = (int)$data['holder_grade_calc'];

        }
        if ($breakRank > 0) {
            $html .= '</section>';
        }
    }
    // 会員管理者の場合、会員追加ボタン表示
    if ($memberData['member_admin_flg'] === DB_FLG_ON) {
        $html .= '<div class="text-center">';
        $html .= '<button type="button" id="member-regist" class="btn btn-warning">会員追加</button>';
        $html .= '</div>';
    }

    return $html;
}

// 会員選択セレクトボックスHTML生成
function get_member_list_tom_select_html($memberList, $taikaiOfferMemberList) : string
{
    // 参加者IDリスト作成
    $offerMemberIdList = array();
    if ($taikaiOfferMemberList['numRows'] > 0) {
        foreach ($taikaiOfferMemberList['result'] as $idx => $data) {
            $offerMemberIdList[] = $data['member_id'];
        }
    }

    $html = '';
    $html = '<select name="add-member" id="add-member" multiple="multiple">';
    if ($memberList['numRows'] > 0) {
        foreach ($memberList['result'] as $idx => $data) {
            $nameF = htmlspecialchars($data['name_f'], ENT_QUOTES, 'UTF-8');
            $nameS = htmlspecialchars($data['name_s'], ENT_QUOTES, 'UTF-8');
            $kanaF = htmlspecialchars($data['kana_f'], ENT_QUOTES, 'UTF-8');
            $kanaS = htmlspecialchars($data['kana_s'], ENT_QUOTES, 'UTF-8');
            $holderName = htmlspecialchars($data['holder_name'], ENT_QUOTES, 'UTF-8');
            $gradeName = htmlspecialchars($data['grade_name'], ENT_QUOTES, 'UTF-8');
            $name = $nameF . $nameS . '|' . $kanaF . $kanaS;
            
            // 参加者一覧に存在する場合はスキップ
            if (in_array($data['member_id'], $offerMemberIdList) === true) {
                $html .= '<option value="' . $data['member_id'] . '" data-label="' . $nameF . $nameS . '（参加済み）" data-name="' . $name . '" data-holder_grade="' . $holderName . $gradeName . '" disabled="disabled"></option>';
            } else {
                $html .= '<option value="' . $data['member_id'] . '" data-label="' . $nameF . $nameS . '" data-name="' . $name . '" data-holder_grade="' . $holderName . $gradeName . '"></option>';
            }
        }
    }
    $html .= '</select>';

    return $html;
}