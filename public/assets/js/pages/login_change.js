$(function(){

    // ログイン情報変更ボタン押下イベント
    $("#login-data-change").click(function() {

        // 送信データ取得
        let login_id = $('#login_id').val();
        let mail_address = $('#mail_address').val();
        let password_old = $('#password_old').val();
        let password_new = $('#password_new').val();
        let password_conf = $('#password_conf').val();

        // Ajax通信
        $.ajax({
            type: "POST",
            url: "./login_change_check",
            dataType: "json",
            data: {
                login_id: login_id,
                mail_address: mail_address,
                password_old: password_old,
                password_new: password_new,
                password_conf: password_conf,
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
                // エラーモーダル表示
                let myModal;
                myModal = new bootstrap.Modal(document.getElementById('loginDataChangeError'));
                myModal.show();
            } else {

                // 変更確認モーダル表示
                let myModal;
                myModal = new bootstrap.Modal(document.getElementById('loginDataChangeConfrim'));
                myModal.show();
            }

        }).fail(function() {
            alert("エラー発生");
            let myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
            // alert( "complete" );
        });
    });

    // ログイン情報変更完了確認ボタン押下イベント
    $("#login-data-change-complete").click(function() {
        // 登録完了時はリロード
        window.location.reload();
    });

    // 申込ボタン押下イベント
    $("#login-data-change-confrim").click(function() {

        // 送信データ取得
        let login_id = $('#login_id').val();
        let mail_address = $('#mail_address').val();
        let password_new = $('#password_new').val();

        // Ajax通信
        $.ajax({
            type: "POST",
            url: "./login_change_process",
            dataType: "json",
            data: {
                login_id: login_id,
                mail_address: mail_address,
                password_new: password_new,
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
                let myModal;
                myModal = new bootstrap.Modal(document.getElementById('loginDataChangeError'));
                myModal.show();
            } else {
                // 登録完了モーダル表示
                let myModal;
                myModal = new bootstrap.Modal(document.getElementById('loginDataChangeComplete'));
                myModal.show();
            }

        }).fail(function() {
            alert("エラー発生");
            let myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
            // alert( "complete" );
        });

    });

});