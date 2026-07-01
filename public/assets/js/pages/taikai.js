$(function(){

    let site_root = $("head").data("site-root");

    // 年度切り替えセレクトボックス変更イベント
    $("#taikai-change-nendo").change(function() {
        // GETパラメータ付与
        let url = site_root + "taikai/" + $(this).val(); 
        // GET遷移
        window.location.href = url;
    });

    // 申込・キャンセルボタン押下イベント
    $("#taikai-request").click(function() {

        let request_title = "";
        let request_body = "";
        let request_btn_name = "";
        let request_mode = $(this).data('request-mode');
        switch (request_mode) {
            case 'join' :
                request_title = "大会参加確認";
                request_body = "大会参加の申込をしますか？";
                request_btn_name = "大会申込する";
                break;
            case 'cancel' :
                request_title = "大会キャンセル確認";
                request_body = "大会キャンセルの申込をしますか？";
                request_btn_name = "大会申込キャンセルする";
                break;
        }
        $("#modal-title-taikai-request-confrim").text(request_title);
        $("#modal-body-taikai-request-confrim").text(request_body);
        $("#taikai-request-offer").text(request_btn_name);
        $("#request_mode").val(request_mode);

        // モーダル表示
        let myModal;
        myModal = new bootstrap.Modal(document.getElementById('taikaiRequestConfrim'));
        myModal.show();
    });

    // 登録完了確認ボタン押下イベント
    $("#taikai-request-complete").click(function() {
        // 登録完了時はリロード
        window.location.reload();
    });

    // 参加ボタン押下イベント
    $("#taikai-request-offer").click(function() {

        // 送信データ取得
        let taikai_id = $('#taikai_id').val();
        let request_mode = $('#request_mode').val();

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "taikai_request",
            dataType: "json",
            data: {
                taikai_id: taikai_id,
                request_mode: request_mode,
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
                $('#error-title').text("審査申込・キャンセルエラー");
                // モーダル表示
                let myModal;
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                let request_title = "";
                let request_body = "";
                switch (request_mode) {
                    case 'join' :
                        request_title = "参加申し込み完了";
                        request_body = "大会参加申し込みを受け付けました";
                        break;
                    case 'cancel' :
                        request_title = "参加キャンセル申し込み完了";
                        request_body = "大会キャンセル申し込みを受け付けました";
                        break;
                }
                $("#modal-title-taikai-request-complete").text(request_title);
                $("#modal-body-taikai-request-complete").text(request_body);
                // 登録完了モーダル表示
                let myModal;
                myModal = new bootstrap.Modal(document.getElementById('taikaiRequestComplete'));
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