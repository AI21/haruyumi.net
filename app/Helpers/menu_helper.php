<?php

// メインメニューHTML生成
function get_menu_html($mainMenu, $controllerName) {

    $html = '';

    if ($mainMenu['numRows'] > 0) {
        foreach ($mainMenu['result'] as $idx => $data) {
            // HOMEのみ表示フラグがONの場合はHOMEのみメニュー表示
            if ($controllerName !== CONTROLLER_NAME_HOME && $data['home_only_flg'] === FLG_ON) {
                continue;
            }
            $active = "";
            if ($controllerName === $data['controller']) {
                $active = "active";
            }
            $html .= '<li class="nav-item ' . $active . '">';
            $html .= '<a class="nav-link ' . $active . '" href="'. SITE_ROOT . $data['controller'] . '/">' . $data['menu_name'] . '</a>';
            $html .= '</li>';
        }
    }
    
    return $html;
}

// HOMEメニューHTML生成
function get_menu_home_html($mainMenu) {

    $html = '';
    if ($mainMenu['numRows'] > 0) {
        $html .= '<section id="main-menu-home">';
        $html .= '<h2>メニュー</h2>';
        $html .= '<ul>';
        foreach ($mainMenu['result'] as $idx => $data) {
            $html .= '<li>';
            $html .= '<a href="'. SITE_ROOT . $data['controller'] . '/">' . $data['menu_name'] . '</a>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '</section>';
    }
    
    return $html;
}

// パンくずHTML生成
function get_breadcrumb_html($memuInfo, $page=array(), $fiscalYearId = null) {
    
    $html = '';
    if (empty($memuInfo->menu_name) === true) {
        return $html;
    }
    $fiscalYearUrl = '';
    if (empty($fiscalYearId) === false) {
        $fiscalYearUrl = '/' . $fiscalYearId;
    }
    $html .= '<div class="container-md bg-dark roots-main-title">';
    $html .= '<nav aria-label="breadcrumb" class="">';
    $html .= '<ol class="breadcrumb">';
    $html .= '<li class="breadcrumb-item"><a href="' . SITE_ROOT . '">メイン</a></li>';
    if (empty($page) === false) {
        $active = '';
        $html .= '<li class="breadcrumb-item" aria-current="page"><a href="' . SITE_ROOT . $memuInfo->controller . $fiscalYearUrl . '">' . $memuInfo->menu_name . '</a></li>';
        foreach ($page as $idx => $data) {
            if ($idx === array_key_last($page)) {
                $active = 'active';
            }
            $html .= '<li class="breadcrumb-item ' . $active . '" aria-current="page">' . $data . '</li>';
        }
    } else {
        $html .= '<li class="breadcrumb-item active" aria-current="page">' . $memuInfo->menu_name . '</li>';
    }
    $html .= '</ol>';
    $html .= '</nav>';
    $html .= '</div>';
    
    return $html;
}

// タブメニューHTML生成
function get_menu_tab_html($subMenu) {
    
    $html = '';

    if ($subMenu['numRows'] > 0) {
        $html .= '<ul class="nav nav-tabs nav-fill" role="tablist">';
        foreach ($subMenu['result'] as $idx => $data) {
            $active = "";
            if ($idx === 0) {
                $active = "active";
            }
            $html .= '<li class="nav-item" role="presentation">';
            $html .= '<button class="nav-link ' . $active . '" data-bs-toggle="tab" data-bs-target="#' . $data['tab_name'] . '" type="button" aria-selected="false" role="tab" tabindex="-1">';
            $html .= $data['category_name'];
            $html .= '</button>';
            $html .= '</li>';
        }
        $html .= '</ul>';
    }

    return $html;
}
