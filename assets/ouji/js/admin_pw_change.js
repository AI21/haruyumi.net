$(document).ready(function () {
    $('#pw_old').val("");
})

$(function () {

    // パスワード変更ボタンクリックイベント
    $('#pw_change').on('click', function () {
        // フォーム送信
        var frm = $('#admin_pw_change');
        frm.attr('action', './admin_pw_change_regist.php');
        frm.submit();
    });



});
