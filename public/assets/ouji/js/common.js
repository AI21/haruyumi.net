$(function () {

    // エンターキー無効
    $("input"). keydown(function(e) {
        if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
            return false;
        }
    });

    // 画面を閉じるボタンクリックイベント
    $('#btn_widow_close').on('click', function () {
        window.close();
    });

    // メイン画面に戻るボタンクリックイベント
    $('#back_schejule_main').on('click', function () {
        var frm = $('#frm_back');
        frm.attr('action', './');
        frm.submit();
    });

    // メール受信設定ボタンクリックイベント
    $('#btn_mail_set').on('click', function () {
        // フォーム送信
        var frm = $('#mail_set');
        frm.submit();
    });

    // 表示切り替えボタンクリックイベント
    $('#btn_change_view').on('click', function () {
        // フォーム送信
        var frm = $('#change_view');
        var action = $('#view').val();
        frm.attr('action', action);
        frm.submit();
    });

});

// バリデーション：メールアドレス
function checkMail(email) {
    // 空は未判定
    if (email == "") {
        return true;
    }
    if (!email.match(/^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]+.[A-Za-z0-9]+$/)) {
        return false;
    }
    return true;
}
