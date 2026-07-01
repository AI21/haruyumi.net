$(function(){

    let site_root = $("head").data("site-root");

    // 年度切り替えセレクトボックス変更イベント
    $("#shinsa-change-nendo").change(function() {
        // 画面遷移先URL
        let url = site_root + "shinsa/" + $(this).val(); 
        // GET遷移
        window.location.href = url;
    });

    // 新規登録ボタン押下イベント
    $(".shinsa-regist").click(function() {

        // 画面遷移先URL
        let url = site_root + "admin/shinsa_regist";

        // 送信データ取得
        let tab_name = $(this).data('tab-name');


        // パラメータ付与
        let params = [
            ["tab_name", tab_name]
        ];

        // POST遷移
        formSubmit(url, params);
    });

    // 申込・キャンセルボタン押下イベント
    $("#shinsa-request").click(function() {

        let request_title = "";
        let request_body = "";
        let request_btn_name = "";
        let request_mode = $(this).data('request-mode');
        switch (request_mode) {
            case 'join' :
                request_title = "審査申込確認";
                request_body = "審査種別 ： " + $('[name=shinsa_target_id] option:selected').text();
                request_body += "<br>審査の申込をしますか？";
                request_btn_name = "審査申込する";
                break;
            case 'cancel' :
                request_title = "審査申込キャンセル確認";
                request_body = "審査キャンセルの申込をしますか？";
                request_btn_name = "審査申込キャンセルする";
                break;
        }
        $("#modal-title-shinsa-request-confrim").text(request_title);
        $("#modal-body-shinsa-request-confrim").html(request_body);
        $("#shinsa-request-offer").text(request_btn_name);
        $("#request_mode").val(request_mode);

        // モーダル表示
        let myModal;
        myModal = new bootstrap.Modal(document.getElementById('shinsaRequestConfrim'));
        myModal.show();
    });

    // 申込ボタン押下イベント
    $("#shinsa-request-offer").click(function() {

        // 送信データ取得
        let shinsa_id = $('#shinsa_id').val();
        let shinsa_target_id;
        let request_mode = $('#request_mode').val();
        if (request_mode === 'join') {
            $('#shinsa_target_id').val($('[name=shinsa_target_id] option:selected').val());
        }
        shinsa_target_id = $('#shinsa_target_id').val();

        // ローディング表示
        show_loading();

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "shinsa_request",
            dataType: "json",
            data: {
                shinsa_id: shinsa_id,
                shinsa_target_id: shinsa_target_id,
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
                $('#shinsa-error-title').html("審査申込・キャンセルエラー");
                // モーダル表示
                let myModal;
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                let request_title = "";
                let request_body = "";
                switch (request_mode) {
                    case 'join' :
                        request_title = "審査申込完了";
                        request_body = "審査申込を受け付けました";
                        break;
                    case 'cancel' :
                        request_title = "審査申込キャンセル完了";
                        request_body = "審査申込キャンセルを受け付けました";
                        break;
                }
                $("#modal-title-shinsa-request-complete").text(request_title);
                $("#modal-body-shinsa-request-complete").text(request_body);
                // 登録完了モーダル表示
                let myModal;
                myModal = new bootstrap.Modal(document.getElementById('shinsaRequestComplete'));
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

    // 審査結果プルダウン選択状態で報告ボタンの有効/無効を切り替え
    function toggleReportButton() {
        let selected = $('[name=result_flg]').val();
        if (selected > 0) {
            $("#shinsa-result-report").removeClass("disabled");
        } else {
            $("#shinsa-result-report").addClass("disabled");
        }
    }

    // 初期状態チェック
    toggleReportButton();

    // プルダウン変更時にチェック
    $('[name=result_flg]').on('change', function() {
        toggleReportButton();
    });

    // 審査結果報告ボタン押下イベント
    $("#shinsa-result-report").click(function() {

        let request_body = "";
        let request_result = $('[name=result_flg] option:selected').text();

        request_body = "審査結果 ： " + request_result;
        request_body += "<br>";
        request_body += "<br>審査結果の報告をしますか？";
        request_body += '<br><span class="text-danger">※訂正できませんのでご注意ください</span>';
        
        $("#modal-body-shinsa-result-report-confrim").html(request_body);

        // モーダル表示
        let myModal;
        myModal = new bootstrap.Modal(document.getElementById('shinsaResultReportConfrim'));
        myModal.show();
    });

    // 審査結果報告ボタン押下イベント
    $("#shinsa-result-report-submit").click(function() {

        // 送信データ取得
        let shinsa_id = $('#shinsa_id').val();
        let shinsa_target_id = $('#shinsa_target_id').val();
        let result_flg = $('[name=result_flg] option:selected').val();

        // ローディング表示
        show_loading();

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "shinsa_result_report",
            dataType: "json",
            data: {
                shinsa_id: shinsa_id,
                shinsa_target_id: shinsa_target_id,
                result_flg: result_flg,
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
                $('#shinsa-error-title').html("審査結果報告エラー");
                // モーダル表示
                let myModal;
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                // 登録完了モーダル表示
                let myModal;
                myModal = new bootstrap.Modal(document.getElementById('shinsaResultReportComplete'));
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

    // 登録完了確認ボタン押下イベント
    $("#shinsa-request-complete, #shinsa-result-report-complete").click(function() {
        // 登録完了時はリロード
        window.location.reload();
    });

});