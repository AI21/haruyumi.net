$(function(){

    let site_root = $("head").data("site-root");

    // 資料ページに戻る
    $(".modal-back-document").click(function() {
        // 資料ページにリダイレクト
        window.location.href = site_root + 'document';
    });

    // 資料追加ボタン押下イベント
    $(".document-file-regist").click(function() {

        // 画面遷移先URL
        let url = site_root + "admin/document_file_regist";

        // 送信データ取得
        let tab_name = $(this).data('tab-name');


        // パラメータ付与
        let params = [
            ["tab_name", tab_name]
        ];

        // POST遷移
        formSubmit(url, params);
    });
    
    // 	お知らせメール配信チェック押下イベント
    $("#document-mail-send").click(function() {
        if ($(this).prop('checked') == true) {
            $("#send-mail-detail").removeClass('d-none');
            $("#document-mail-text").text('する');
        } else {
            $("#send-mail-detail").addClass('d-none');
            $("#document-mail-text").text('しない');
        }
    });

    // 資料：登録確認ボタン押下イベント
    $("#document-regist-check").click(function() {

        let files_num = 0;
        let myModal;
        
        // 送信データセット
        let fd = new FormData();
        let document_files = $('#document-file').prop('files')[0];
        fd.append('document_file', document_files);
        fd.append('document_mail_send', $('#document-mail-send').prop('checked'));
        fd.append('document_title', $('#document-title').val());
        fd.append('document_body', $('#document-body').val());
        fd.append('document_category_id', $('#document-category-id').val());

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/document_file_regist_conf",
            dataType: "json",
            processData: false,
            contentType: false,
            data: fd
        }).done(function( data ) {

            if (data.result == false) {
                // エラーメッセージ
                let error_msg = "<ul>";
                $.each(data.error, function(index, value) {
                     error_msg += "<li>" + value + "</li>";
                })
                error_msg += "</ul>";
                $('#error-message').html(error_msg);
                $('#error-title').text("登録内容にエラーがあります");
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
        
                // 送信データセット
                let documentMailSend = $('#document-mail-send').prop('checked');
                let documentTitle = $('#document-title').val();
                let documentBody = $('#document-body').val();
                let documentFile = '';
                documentFile += '<ul>';
                documentFile += '<li>';
                documentFile += '<img src="' + site_root + data.documentFile.file_ext_path + '" alt="' + data.documentFile.file_name + '">';
                documentFile += '<span>' + data.documentFile.file_name + '</span>';
                documentFile += '</li>';
                documentFile += '</ul>';
                $('#document-title-confrim').text('');
                $('#document-body-confrim').html('');
                $("#document-mail-conf-body").addClass('d-none');
                if (documentMailSend == true) {
                    $('#document-title-confrim').text(documentTitle);
                    $('#document-body-confrim').html(documentBody.replaceAll('\n', '<br>'));
                    $("#document-mail-conf-body").removeClass('d-none');
                }
                $('#document-file-confrim').html(documentFile);
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('documentConfirmModal'));
                myModal.show();
            }

        }).fail(function() {
            alert("エラー発生");
            myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
            // alert( "complete" );
        });

    });

    // 資料：登録完了ボタン押下イベント
    $("#document-regist-complete").click(function() {

        let files_num = 0;
        let myModal;
        
        // 送信データセット
        let fd = new FormData();
        let document_files = $('#document-file').prop('files')[0];
        fd.append('document_file', document_files);
        fd.append('document_mail_send', $('#document-mail-send').prop('checked'));
        fd.append('document_title', $('#document-title').val());
        fd.append('document_body', $('#document-body').val());
        fd.append('document_category_id', $('#document-category-id').val());

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/document_file_regist_proc",
            dataType: "json",
            processData: false,
            contentType: false,
            data: fd
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
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                $('#modal-title-document-regist-comp').text("登録完了");
                $('#modal-body-document-regist-comp').text("資料の登録が完了しました");
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('documentRegistCompModal'));
                myModal.show();
            }

        }).fail(function() {
            alert("エラー発生");
            myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
            // alert( "complete" );
        });

    });
    
});