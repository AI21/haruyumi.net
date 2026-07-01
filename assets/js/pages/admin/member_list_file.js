$(function(){
    
    // 登録完了ボタン押下イベント
    $("#member-list-mail-send").click(function() {
        if ($(this).prop('checked') == true) {
            $("#send-mail-detail").removeClass('d-none');
            $("#member-list-mail-text").text('する');
        } else {
            $("#send-mail-detail").addClass('d-none');
            $("#member-list-mail-text").text('しない');
        }
    });

    
});