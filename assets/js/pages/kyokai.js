$(function(){

    let site_root = $("head").data("site-root");

    // 年度切り替えセレクトボックス変更イベント
    $("#kyokai-change-nendo").change(function() {
        // 画面遷移先URL
        let url = site_root + "kyokai/" + $(this).val();
        // GET遷移
        window.location.href = url;
    });

    // 申込・キャンセルボタン押下イベント
    $("#event-request").click(function() {

        let request_title = "";
        let request_body = "";
        let request_btn_name = "";
        let request_mode = $(this).data('request-mode');
        switch (request_mode) {
            case 'join' :
                request_title = "参加申込確認";
                request_body = "参加申込をしますか？";
                request_btn_name = "参加申込する";
                break;
            case 'cancel' :
                request_title = "参加キャンセル確認";
                request_body = "参加キャンセルの申込をしますか？";
                request_btn_name = "参加キャンセルする";
                break;
        }
        $("#modal-title-event-request").text(request_title);
        $("#modal-body-event-request").html(request_body);
        $("#event-request-offer").text(request_btn_name);
        $("#request_mode").val(request_mode);

        // モーダル表示
        let myModal;
        myModal = new bootstrap.Modal(document.getElementById('eventRequestConfrim'));
        myModal.show();
    });

    // 登録完了確認ボタン押下イベント
    $("#event-request-complete").click(function() {
        // 画面を再読み込みせず、モーダルのみ閉じる
        $(this).closest('.modal').modal('hide');
    });

    // 申込ボタン押下イベント
    $("#event-request-offer").click(function() {

        // 送信データ取得
        let event_id = $('#event_id').val();
        let request_mode = $('#request_mode').val();
        event_target_id = $('#event_target_id').val();

        // ローディング表示
        show_loading();

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "event_request",
            dataType: "json",
            data: {
                event_id: event_id,
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
                // モーダル表示
                let myModal;
                myModal = new bootstrap.Modal(document.getElementById('eventRequestError'));
                myModal.show();
            } else {
                let request_title = "";
                let request_body = "";
                switch (request_mode) {
                    case 'join' :
                        request_title = "参加申込完了";
                        request_body = "参加申込を受け付けました";
                        break;
                    case 'cancel' :
                        request_title = "参加キャンセル完了";
                        request_body = "参加キャンセルを受け付けました";
                        break;
                }
                $("#modal-title-event-request-comp").text(request_title);
                $("#modal-body-event-request-comp").text(request_body);
                // 登録完了モーダル表示
                let myModal;
                myModal = new bootstrap.Modal(document.getElementById('eventRequestComp'));
                myModal.show();
            }

        }).fail(function() {
            alert("エラー発生");
            let myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
            // alert( "complete" );
            // ローディング非表示
            hide_loading();
        });

    });

    // 更新ボタン押下イベント
    $("#event-revision").click(function() {

        // 画面遷移先URL
        let url = site_root + "admin/event_revision";

        // 送信データ取得
        let event_id = $('#event_id').val();

        // パラメータ付与
        let params = [
            ["event_id", event_id]
        ];

        // POST遷移
        formSubmit(url, params);
    });

});