$(function () {

    // 開場時間設定ログインボタンクリックイベント
    $('#open_set_login').on('click', function () {
        // フォーム送信
        var frm = $('#open_set_login_check');
        frm.attr('action', './open_set_login_check.php');
        frm.submit();
    });

    // 開場時間設定ボタンクリックイベント
    $('#btn_open_set_confrim').on('click', function () {
        // フォーム送信
        var frm = $('#open_set_confrim');
        frm.submit();
    });

    // 開場時間設定確認ボタンクリックイベント
    $('#btn_open_set_complete').on('click', function () {
        // フォーム送信
        var frm = $('#open_set_complete');
        frm.attr('action', './open_set_regist.php');
        frm.submit();
    });

    // 開場設定画面に戻るボタンクリックイベント
    $('#back_open_set').on('click', function () {
        var frm = $('#frm_back');
        frm.attr('action', './open_set.php');
        frm.submit();
    });

    // 開場設定画面に戻るボタンクリックイベント
    $('#move_schejule_main').on('click', function () {
        var frm = $('#frm_schejule_main');
        frm.attr('action', './');
        frm.submit();
    });

    // パスワード変更ボタンクリックイベント
    $('#btn_pw_change').on('click', function () {
        // フォーム送信
        var frm = $('#pw_change');
        frm.submit();
    });

    // パスワード変更ボタンクリックイベント
    $('#btn_pw_change2').on('click', function () {
        // フォーム送信
        var frm = $('#pw_change');
        frm.submit();
    });


});
