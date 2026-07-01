$(function(){

    let site_root = $("head").data("site-root");

    // 会員名簿に戻る
    $(".modal-back-member").click(function() {
        // 会員名簿ページにリダイレクト
        window.location.href = site_root + 'member';
    });
    
    // 春日井協会メイン会員ボタン押下イベント
    $("#kasugai-regist-flg").click(function() {
        if ($(this).prop('checked') == true) {
            $("#send-mail-detail").removeClass('d-none');
            $("#kasugai-regist-main-text").text('主会員');
        } else {
            $("#send-mail-detail").addClass('d-none');
            $("#kasugai-regist-main-text").text('他支部・他協会会員');
        }
    });
    
    // 愛弓連登録ボタン押下イベント
    $("#aiti-renmei-regist-flg").click(function() {
        if ($(this).prop('checked') == true) {
            $("#send-mail-detail").removeClass('d-none');
            $("#aiti-renmei-regist-text").text('している');
        } else {
            $("#send-mail-detail").addClass('d-none');
            $("#aiti-renmei-regist-text").text('していない');
        }
    });
    
    // 	お知らせメール受信ボタン押下イベント
    $("#notice-send-flg").click(function() {
        if ($(this).prop('checked') == true) {
            $("#send-mail-detail").removeClass('d-none');
            $("#notice-send-text").text('する');
        } else {
            $("#send-mail-detail").addClass('d-none');
            $("#notice-send-text").text('しない');
        }
    });

    // 会員名簿：登録確認ボタン押下イベント
    $("#member-list-regist-check").click(function() {

        let files_num = 0;
        let myModal;
        
        // 送信データセット
        let fd = new FormData();
        let member_list_files = $('#member-list-file').prop('files')[0];
        fd.append('member_list_file', member_list_files);
        fd.append('member_list_mail_send', $('#member-list-mail-send').prop('checked'));
        fd.append('member_list_title', $('#member-list-title').val());
        fd.append('member_list_body', $('#member-list-body').val());

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/member_list_file_conf",
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
                let memberlistMailSend = $('#member-list-mail-send').prop('checked');
                let memberlistTitle = $('#member-list-title').val();
                let memberlistBody = $('#member-list-body').val();
                let memberListFile = '';
                memberListFile += '<ul>';
                memberListFile += '<li>';
                memberListFile += '<img src="' + site_root + data.memberListFile.file_ext_path + '" alt="' + data.memberListFile.file_name + '">';
                memberListFile += '<span>' + data.memberListFile.file_name + '</span>';
                memberListFile += '</li>';
                memberListFile += '</ul>';
                $('#memberlist-title-confrim').text('');
                $('#memberlist-body-confrim').html('');
                $("#memberlist-mail-conf-body").addClass('d-none');
                if (memberlistMailSend == true) {
                    $('#memberlist-title-confrim').text(memberlistTitle);
                    $('#memberlist-body-confrim').html(memberlistBody.replaceAll('\n', '<br>'));
                    $("#memberlist-mail-conf-body").removeClass('d-none');
                }
                $('#memberlist-file-confrim').html(memberListFile);
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('memberListConfrimModal'));
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

    // 会員名簿：登録完了ボタン押下イベント
    $("#memberlist-regist-complete").click(function() {

        let files_num = 0;
        let myModal;
        
        // 送信データセット
        let fd = new FormData();
        let member_list_files = $('#member-list-file').prop('files')[0];
        fd.append('member_list_file', member_list_files);
        fd.append('member_list_mail_send', $('#member-list-mail-send').prop('checked'));
        fd.append('member_list_title', $('#member-list-title').val());
        fd.append('member_list_body', $('#member-list-body').val());

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/member_list_file_proc",
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
                $('#modal-title-common-regist-comp').text("登録完了");
                $('#modal-body-common-regist-comp').text("会員名簿の登録が完了しました");
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('commonRegistCompModal'));
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

    // 称号・段位・級位選択イベント
    $("#holder-grade-cd").change(function() {
        const val = $(this).val();
        // 称号と段位・級位は「称号ID|段位・級位ID」の形式で保存されているため、分割して使用
        const arr = val.split('|');
        // 左側（称号ID）が1以上なら表示
        if (arr.length > 1 && parseInt(arr[0], 10) >= 1) {
            $("#holder-acquired-day-area").removeClass('d-none');
        } else {
            $("#holder-acquired-day-area").addClass('d-none');
            $("#holder-acquired-day").val(''); // 称号取得日をクリア
        }
        // 右側（段級位ID）が1以上なら表示
        if (arr.length > 1 && parseInt(arr[1], 10) >= 1) {
            $("#grade-acquired-day-area").removeClass('d-none');
        } else {
            $("#grade-acquired-day-area").addClass('d-none');
            $("#grade-acquired-day").val(''); // 段級位取得日をクリア
        }
    });

    // 会員登録：登録確認ボタン押下イベント
    $("#member-regist-check").click(function() {

        let myModal;
        let modal_title = '';
        
        // 送信データセット
        let fd = new FormData();
        fd.append('member_id', $('#member-id').val());
        fd.append('member_name_f', $('#member-name-f').val());
        fd.append('member_name_s', $('#member-name-s').val());
        fd.append('member_kana_f', $('#member-kana-f').val());
        fd.append('member_kana_s', $('#member-kana-s').val());
        fd.append('gender_cd', $('input[name="gender-cd"]:checked').attr('id') ? $('input[name="gender-cd"]:checked').attr('id').replace('gender-cd-', '') : '');
        fd.append('holder_grade_cd', $('#holder-grade-cd').val());
        fd.append('holder_acquired_day', $('#holder-acquired-day').val());
        fd.append('grade_acquired_day', $('#grade-acquired-day').val());
        fd.append('kasugai_regist_flg', $('#kasugai-regist-flg').prop('checked') ? 1 : 0);
        fd.append('kasugai_regist_date', $('#kasugai-regist-date').val());
        fd.append('aiti_renmei_regist_flg', $('#aiti-renmei-regist-flg').prop('checked') ? 1 : 0);
        // fd.append('notice_send_flg', $('#notice-send-flg').prop('checked') ? 1 : 0);
        fd.append('mail_address', $('#mail-address').val());
        fd.append('regist_mode', $('#regist-mode').val());

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/member_regist_conf",
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
                if ($('#regist-mode').val() == 'regist') {
                    modal_title = '会員登録：内容確認';
                } else {
                    modal_title = '会員情報変更：内容確認';
                }
        
                // 送信データセット
                $('#member-title-member-regist').text(modal_title);
                $('#member-body-member-regist').html(data.postData.replaceAll('\n', '<br>'));
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('memberRegistModal'));
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

    // 会員登録：登録完了ボタン押下イベント
    $("#member-regist-complete").click(function() {

        let files_num = 0;
        let myModal;
        
        // 送信データセット
        let fd = new FormData();
        fd.append('member_id', $('#member-id').val());
        fd.append('member_name_f', $('#member-name-f').val());
        fd.append('member_name_s', $('#member-name-s').val());
        fd.append('member_kana_f', $('#member-kana-f').val());
        fd.append('member_kana_s', $('#member-kana-s').val());
        fd.append('gender_cd', $('input[name="gender-cd"]:checked').attr('id') ? $('input[name="gender-cd"]:checked').attr('id').replace('gender-cd-', '') : '');
        fd.append('holder_grade_cd', $('#holder-grade-cd').val());
        fd.append('holder_acquired_day', $('#holder-acquired-day').val());
        fd.append('grade_acquired_day', $('#grade-acquired-day').val());
        fd.append('kasugai_regist_flg', $('#kasugai-regist-flg').prop('checked') ? 1 : 0);
        fd.append('kasugai_regist_date', $('#kasugai-regist-date').val());
        fd.append('aiti_renmei_regist_flg', $('#aiti-renmei-regist-flg').prop('checked') ? 1 : 0);
        // fd.append('notice_send_flg', $('#notice-send-flg').prop('checked') ? 1 : 0);
        fd.append('mail_address', $('#mail-address').val());
        fd.append('regist_mode', $('#regist-mode').val());

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/member_regist_proc",
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
                $('#modal-title-member-regist-comp').text("登録完了");
                $('#modal-body-member-regist-comp').text("会員名簿の登録が完了しました");
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('memberRegistCompModal'));
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