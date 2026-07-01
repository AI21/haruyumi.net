<?php

// お知らせ一覧HTML生成
function get_notice_list_html($noticeList, $useNoticeIdList, $memberData, $noticeCategoryId=NULL) {
    
    
    $html = '';
    $html .= '<div id="notice-area">';
    $html .= '<h2 class="p-1">更新情報・お知らせ</h2>';
    // お知らせ管理者はリンク表示
    if ($memberData['notice_posting_flg'] === true) {
        $html .= '<div id="admin-regist-link">';
        $html .= '<a href="' . SITE_ROOT . 'admin/notice_regist/' . $noticeCategoryId . '">新規投稿</a>';
        $html .= '</div>';
    }
    $html .= '<section id="notice-area" class="bg-light">';
    if ($noticeList['numRows'] > 0) {
        $html .= '<table id="notice-list">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>カテゴリー</th>';
        $html .= '<th>タイトル</th>';
        $html .= '<th>登録日</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach ($noticeList['result'] as $idx => $data) {
            $html .= '<tr>';
            $html .= '<td class="mode"><span class="item btn btn-' . $data['theme_coller'] . '">' . $data['notice_category_name'] . '</span></td>';
            $html .= '<td class="title"><a href="#" class="notice-view" data-notice-info-id="' . $data['notice_info_id'] . '">' . $data['notice_title'] . '</a></td>';
            $html .= '<td class="date">' . date_format_jp($data['created'], false, DATE_FORMAT_MD) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        // ページネーション
        $html .= '<nav id="pager" aria-label="Page navigation example">';
        $html .= '<ul class="pagination">';
        $html .= '<li class="page-item"><a class="page-link first" href="#">最初</a></li>';
        $html .= '<li class="page-item"><a class="page-link prev" href="#" rel="prev">前へ</a></li>';
        // $html .= '<li class="page-item"><a class="page-link direct p_1" page="1" href="#">1</a></li>';
        // $html .= '<li class="page-item"><a class="page-link" href="#">2</a></li>';
        // $html .= '<li class="page-item"><a class="page-link" href="#">3</a></li>';
        $html .= '<li class="page-item"><a class="page-link next" href="#" rel="next">次へ</a></li>';
        $html .= '<li class="page-item"><a class="page-link last" href="#">最後</a></li>';
        $html .= '</ul>';
        $html .= '</nav>';
    } else {
        $html .= '<p>お知らせはありません</p>';
    }
    $html .= '</section>';
    $html .= '</div>';

    return $html;
}
