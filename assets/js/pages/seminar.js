$(function(){

    let site_root = $("head").data("site-root");

    // 年度切り替えセレクトボックス変更イベント
    $("#seminar-change-nendo").change(function() {
        // 画面遷移先URL
        let url = site_root + "seminar/" + $(this).val(); 
        // GET遷移
        window.location.href = url;
    });
});