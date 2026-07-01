<?php

// メインメニューHTML生成
function get_document_tab_html($documentList) {
    
    $html = '';

    if (empty($documentList) === false) {
        $i = 0;
        foreach ($documentList as $tabName => $data) {
            $html_active = '';
            if ($i === 0) {
                $html_active = ' active show';
            }
            $html .= '<div id="' . $tabName . '" class="tab-pane' . $html_active .'" role="tabpanel">';
            if ($data['numRows'] > 0) {
                $html .= '<table class="table">';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th>資料</th>';
                $html .= '<th>登録日</th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                foreach ($data['result'] as $idx => $data) {
                    // notice_body += '<img src="' + site_root + data.ext_file + '" alt="' + data.document_name + '">';
                    
                    $html .= '<tr>';
                    $html .= '<td>';
                    $html .= '<img src="' . SITE_ROOT . get_file_ext_icon_path($data['document_ext']) . '" alt="' . $data['document_ext'] . '">';
                    $html .= '<a href=".' . $data['document_path'] . '" target="_blank">' . $data['document_name'] . '</a>';
                    $html .= '</td>';
                    $html .= '<td>' . date("n/d", strtotime($data['created'])) . '</td>';
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
