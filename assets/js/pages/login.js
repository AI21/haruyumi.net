$(function(){

    // ログインボタン押下イベント
    $("#login-check").click(function() {

        // 送信データ取得
        let member_id_mail = $('#member_id_mail').val();
        let password = $('#password').val();

        // Ajax通信
        $.ajax({
            type: "POST",
            url: "./login/login_process",
            dataType: "json",
            data: {
                member_id_mail: member_id_mail,
                password: password
            }
        }).done(function( data ) {

            if (data.result == false) {
                // エラーメッセージ
                let error_msg = "<ul>";
                $.each(data.error, function(index, value) {
                     error_msg += "<li>" + value + "</li>";
                })
                error_msg += "</ul>";
                $('#error-message').html(error_msg);
                // モーダル表示
                var myModal;
                myModal = new bootstrap.Modal(document.getElementById('notLogin'));
                myModal.show();
            } else {
                // ログインOKはメインページにリダイレクト
                window.location.href = "./";
            }

        }).fail(function() {
            alert("エラー発生");
            var myModal = new bootstrap.Modal(document.getElementById('notLogin'));
            myModal.show();
        }).always(function() {
            // alert( "complete" );
        });

    });

    // データ送信
    function formSubmit(url, params) {

        // パラメータを付与する場合
        var inputs = '';
        for(var i = 0, n = params.length; i < n; i++) {
            inputs += '<input type="hidden" name="' + params[i][0] + '" value="' + params[i][1] + '">';
        }

        // POST遷移
        $("body").append('<form action="'+url+'" method="post" id="frm">' + inputs + '</form>');
        $("#frm").submit();
    }
});