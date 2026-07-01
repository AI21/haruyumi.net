$(function(){

    let site_root = $("head").data("site-root");

    // 年度切り替えセレクトボックス変更イベント
    $("#member-change-nendo").change(function() {
        // 画面遷移先URL
        let url = site_root + "member/" + $(this).val(); 
        // GET遷移
        window.location.href = url;
    });

    // 更新ボタン押下イベント
    $("#member-regist").click(function() {

        // 画面遷移先URL
        let url = site_root + "admin/member_regist";

        // パラメータ付与
        let params = [
        ];

        // POST遷移
        formSubmit(url, params);
    });
    
    // 春日井協会メイン会員ボタン押下イベント
    $("#kasugai-regist-flg").click(function() {
        if ($(this).prop('checked') == true) {
            $("#send-mail-detail").removeClass('d-none');
            $("#kasugai-regist-main-text").text('する');
        } else {
            $("#send-mail-detail").addClass('d-none');
            $("#kasugai-regist-main-text").text('しない');
        }
    });

    // 会員情報変更
    $(".member-revision").click(function() {

        // 画面遷移先URL
        let url = site_root + "admin/member_revision"; 

        // 送信データ取得
        let member_id = $(this).data('member-id');

        // パラメータ付与
        let params = [
            ["member_id", member_id]
        ];
        // POST遷移
        formSubmit(url, params);
    });
});