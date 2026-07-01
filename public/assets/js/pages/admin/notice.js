$(function(){

    let site_root = $("head").data("site-root");

    // ロード時処理
    // $(window).on('load', function() {
    $(document).ready(function() {
        load_or_notice_category_id_change();
    });

    // 大会・審査等プルダウン取得イベント
    $("#notice-category-id").change(function() {
        load_or_notice_category_id_change();
    });
    function load_or_notice_category_id_change() {
        // カテゴリーID取得
        let notice_category_id = $('#notice-category-id').val();
        let set_relation_event_id = $('#set-relation-event-id').val();

        // カテゴリーに紐づく大会・審査等プルダウン取得
        switch (notice_category_id) {
            case '1': // 春日井弓道会関連
                load_unexpired_kyokai_list(notice_category_id);
                break;
            case '2': // 審査関連
                load_unexpired_shinsa_list(notice_category_id);
                break;
            case '3': // 大会関連
                load_unexpired_taikai_list(notice_category_id);
                break;
            case '4': // 講習会関連
                load_unexpired_seminar_list(notice_category_id);
                break;
            case '5': // 研修会関連
                load_unexpired_training_list(notice_category_id);
                break;
            case '6': // その他関連
                load_unexpired_other_list(notice_category_id);
                break;
            default :
                let option_html = '<option value="">選択してください</option>';
                $('#relation-event-id').html(option_html);
                $('#relation-event-id').removeAttr('name');
        }
        // 既に関連イベントが紐づいている場合は関連イベントのプルダウンを無効化する
        if (set_relation_event_id != null && set_relation_event_id != undefined && set_relation_event_id > 0) {
             $('#relation-event-id').prop('disabled', true);
        }
    }
    
    // 参加者のみにメール配信ボタン押下イベント
    if (document.getElementById('regist-user-mail-flg')) {
        $("#regist-user-mail-flg").click(function() {
            if ($(this).prop('checked') == true) {
                $("#regist-user-mail-text").text('する');
            } else {
                $("#regist-user-mail-text").text('しない');
            }
        });
    }

    // 協会行事プルダウン取得イベント
    function load_unexpired_kyokai_list(notice_category_id) {

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/unexpired_event_list",
            dataType: "json",
            data: {
                notice_category_id: notice_category_id
            }
        }).done(function( data ) {

            let myModal;

            if (data.result == false) {
                // エラーメッセージ
                let error_msg = "<ul>";
                $.each(data.error, function(index, value) {
                     error_msg += "<li>" + value + "</li>";
                })
                error_msg += "</ul>";
                $('#error-message').html(error_msg);
                $('#shinsa-error-title').html("協会行事情報取得エラー");
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                let set_notice_category_id = $('#set-notice-category-id').val();
                let set_relation_event_id = $('#set-relation-event-id').val();
                // プルダウンをイベント一覧に変更
                let option_html = '<option value="">選択してください</option>';
                $.each(data.result.result, function(index, value) {
                    let view = value.event_date_st + ' ';
                    if (value.event_no > 0) {
                        view += '第' + value.event_no + '回 ' + value.kyokai_event_name;
                    } else {
                        view += value.kyokai_event_name;
                    }
                    // カテゴリーが同じ、かつイベントIDが同じ場合はselectedにする
                    if (set_notice_category_id == notice_category_id && set_relation_event_id == value.event_id) {
                        option_html += '<option value="' + value.event_id + '" selected>' + view + '</option>';
                    } else {
                        option_html += '<option value="' + value.event_id + '">' + view + '</option>';
                    }
                });
                $('#relation-event-id').html(option_html);
                // name値を変更
                $('#relation-event-id').attr('name', 'event_id');
            }
        }).fail(function() {
            alert("エラー発生");
            myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
        });
    }
    
    // 審査プルダウン取得イベント
    function load_unexpired_shinsa_list(notice_category_id) {

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/unexpired_event_list",
            dataType: "json",
            data: {
                notice_category_id: notice_category_id
            }
        }).done(function( data ) {

            let myModal;

            if (data.result == false) {
                // エラーメッセージ
                let error_msg = "<ul>";
                $.each(data.error, function(index, value) {
                     error_msg += "<li>" + value + "</li>";
                })
                error_msg += "</ul>";
                $('#error-message').html(error_msg);
                $('#shinsa-error-title').html("審査情報取得エラー");
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                let set_notice_category_id = $('#set-notice-category-id').val();
                let set_relation_event_id = $('#set-relation-event-id').val();
                // プルダウンをイベント一覧に変更
                let option_html = '<option value="">選択してください</option>';
                $.each(data.result.result, function(index, value) {
                    let view = value.shinsa_date_min + ' ' + value.shinsa_name;
                    if (value.area_group_name != 'null' && value.area_group_name != null && value.area_group_name != '') {
                        view += '（' + value.area_group_name + '）';
                    }
                    // カテゴリーが同じ、かつイベントIDが同じ場合はselectedにする
                    if (set_notice_category_id == notice_category_id && set_relation_event_id == value.shinsa_id) {
                        option_html += '<option value="' + value.shinsa_id + '" selected>' + view + '</option>';
                    } else {
                        option_html += '<option value="' + value.shinsa_id + '">' + view + '</option>';
                    }
                });
                $('#relation-event-id').html(option_html);
                // name値を変更
                $('#relation-event-id').attr('name', 'shinsa_id');
            }
        }).fail(function() {
            alert("エラー発生");
            myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
        });
    }
    
    // 大会プルダウン取得イベント
    function load_unexpired_taikai_list(notice_category_id) {

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/unexpired_event_list",
            dataType: "json",
            data: {
                notice_category_id: notice_category_id
            }
        }).done(function( data ) {

            let myModal;

            if (data.result == false) {
                // エラーメッセージ
                let error_msg = "<ul>";
                $.each(data.error, function(index, value) {
                     error_msg += "<li>" + value + "</li>";
                })
                error_msg += "</ul>";
                $('#error-message').html(error_msg);
                $('#shinsa-error-title').html("大会情報取得エラー");
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                let set_notice_category_id = $('#set-notice-category-id').val();
                let set_relation_event_id = $('#set-relation-event-id').val();
                // プルダウンをイベント一覧に変更
                let option_html = '<option value="">選択してください</option>';
                $.each(data.result.result, function(index, value) {
                    let view = value.taikai_date_st + ' ' + value.taikai_name;
                    // カテゴリーが同じ、かつイベントIDが同じ場合はselectedにする
                    if (set_notice_category_id == notice_category_id && set_relation_event_id == value.taikai_id) {
                        option_html += '<option value="' + value.taikai_id + '" selected>' + view + '</option>';
                    } else {
                        option_html += '<option value="' + value.taikai_id + '">' + view + '</option>';
                    }
                });
                $('#relation-event-id').html(option_html);
                // name値を変更
                $('#relation-event-id').attr('name', 'taikai_id');
            }
        }).fail(function() {
            alert("エラー発生");
            myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
        });
    }
    
    // 講習会プルダウン取得イベント
    function load_unexpired_seminar_list(notice_category_id) {

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/unexpired_event_list",
            dataType: "json",
            data: {
                notice_category_id: notice_category_id
            }
        }).done(function( data ) {

            let myModal;

            if (data.result == false) {
                // エラーメッセージ
                let error_msg = "<ul>";
                $.each(data.error, function(index, value) {
                     error_msg += "<li>" + value + "</li>";
                })
                error_msg += "</ul>";
                $('#error-message').html(error_msg);
                $('#shinsa-error-title').html("講習会情報取得エラー");
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                let set_notice_category_id = $('#set-notice-category-id').val();
                let set_relation_event_id = $('#set-relation-event-id').val();
                // プルダウンをイベント一覧に変更
                let option_html = '<option value="">選択してください</option>';
                $.each(data.result.result, function(index, value) {
                    let view = value.seminar_date_st + ' ' + value.seminar_sub_name;
                    // カテゴリーが同じ、かつイベントIDが同じ場合はselectedにする
                    if (set_notice_category_id == notice_category_id && set_relation_event_id == value.seminar_id) {
                        option_html += '<option value="' + value.seminar_id + '" selected>' + view + '</option>';
                    } else {
                        option_html += '<option value="' + value.seminar_id + '">' + view + '</option>';
                    }
                });
                $('#relation-event-id').html(option_html);
                // name値を変更
                $('#relation-event-id').attr('name', 'seminar_id');
            }
        }).fail(function() {
            alert("エラー発生");
            myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
        });
    }
    
    // 研修会プルダウン取得イベント
    function load_unexpired_training_list(notice_category_id) {

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/unexpired_event_list",
            dataType: "json",
            data: {
                notice_category_id: notice_category_id
            }
        }).done(function( data ) {

            let myModal;

            if (data.result == false) {
                // エラーメッセージ
                let error_msg = "<ul>";
                $.each(data.error, function(index, value) {
                     error_msg += "<li>" + value + "</li>";
                })
                error_msg += "</ul>";
                $('#error-message').html(error_msg);
                $('#shinsa-error-title').html("研修会情報取得エラー");
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                let set_notice_category_id = $('#set-notice-category-id').val();
                let set_relation_event_id = $('#set-relation-event-id').val();
                // プルダウンをイベント一覧に変更
                let option_html = '<option value="">選択してください</option>';
                $.each(data.result.result, function(index, value) {
                    let view = value.event_date_st + ' ' + value.kyokai_event_name;
                    // カテゴリーが同じ、かつイベントIDが同じ場合はselectedにする
                    if (set_notice_category_id == notice_category_id && set_relation_event_id == value.event_id) {
                        option_html += '<option value="' + value.event_id + '" selected>' + view + '</option>';
                    } else {
                        option_html += '<option value="' + value.event_id + '">' + view + '</option>';
                    }
                });
                $('#relation-event-id').html(option_html);
                // name値を変更
                $('#relation-event-id').attr('name', 'event_id');
            }
        }).fail(function() {
            alert("エラー発生");
            myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
        });
    }
    
    // その他プルダウン取得イベント
    function load_unexpired_other_list(notice_category_id) {

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/unexpired_event_list",
            dataType: "json",
            data: {
                notice_category_id: notice_category_id
            }
        }).done(function( data ) {

            let myModal;

            if (data.result == false) {
                // エラーメッセージ
                let error_msg = "<ul>";
                $.each(data.error, function(index, value) {
                     error_msg += "<li>" + value + "</li>";
                })
                error_msg += "</ul>";
                $('#error-message').html(error_msg);
                $('#shinsa-error-title').html("その他情報取得エラー");
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                let set_notice_category_id = $('#set-notice-category-id').val();
                let set_relation_event_id = $('#set-relation-event-id').val();
                // プルダウンをイベント一覧に変更
                let option_html = '<option value="">選択してください</option>';
                $.each(data.result.result, function(index, value) {
                    let view = value.event_date_st + ' ' + value.event_sub_name;
                    // カテゴリーが同じ、かつイベントIDが同じ場合はselectedにする
                    if (set_notice_category_id == notice_category_id && set_relation_event_id == value.event_id) {
                        option_html += '<option value="' + value.event_id + '" selected>' + view + '</option>';
                    } else {
                        option_html += '<option value="' + value.event_id + '">' + view + '</option>';
                    }
                });
                $('#relation-event-id').html(option_html);
                // name値を変更
                $('#relation-event-id').attr('name', 'event_id');
            }
        }).fail(function() {
            alert("エラー発生");
            myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
        });
    }

    // 登録・更新確認ボタン押下イベント
    $("#notice-regist-check").click(function() {

        let files_num = 0;
        let myModal;

        // データ取得
        let upload_file_num = $('#upload-file-num').val();
        
        // 送信データセット
        let fd = new FormData();
        fd.append('notice_info_id', $('#notice-info-id').val());
        fd.append('notice_category_id', $('#notice-category-id').val());
        fd.append('notice_category', $('#notice-category-id option:selected').text());
        fd.append('notice_title', $('#notice-title').val());
        fd.append('notice_body', $('#notice-body').val());
        fd.append('relation_event', $('#relation-event-id option:selected').text());
        fd.append('regist_user_mail_flg', $('#regist-user-mail-flg').prop('checked') ? 1 : 0);
        fd.append('regist_mode', $('#regist-mode').val());
        fd.append('upload_file_num', upload_file_num);
        for (let i=1; i<=upload_file_num; i++) {
            if ($('#notice-files' + i).prop('files') != undefined) {
                let notice_files = $('#notice-files' + i).prop('files')[0];
                if (notice_files !== undefined) {
                    files_num++;
                    fd.append('notice_files' + files_num, notice_files);
                }
            }
        }

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/notice_regist_conf",
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
        
                // データセット
                let noticeCategoryName = $('#notice-category-id option:selected').text();
                let noticeTitle = $('#notice-title').val();
                let noticeBody = $('#notice-body').val();
                let noticeRelation = '';
                let noticeFiles = '';
                let relationEventId = $('#relation-event-id').val();
                let registUserMailFlg = $('#regist-user-mail-flg').val();
                
                if (noticeCategoryName == '' || noticeCategoryName == undefined) {
                    noticeCategoryName = $('#notice-category-name').text();
                }
                // 関連イベント
                if (relationEventId != null && relationEventId != undefined && relationEventId > 0) {
                    let relationText = $('#relation-event-id option:selected').text();
                    noticeRelation += '<div>';
                    noticeRelation += '<span>' + relationText + '</span>';
                    noticeRelation += '</div>';
                } else {
                    noticeRelation += 'なし';
                }
                // 既存添付資料
                if (data.existing_notice_document_list.length > 0) {
                    noticeFiles += '<section>';
                    noticeFiles += '<h3>[登録済み]</h3>';
                    noticeFiles += '<ul>';
                    $.each(data.existing_notice_document_list, function(idx, value) {
                        noticeFiles += '<li>';
                        noticeFiles += '<img class="icon" src="' + site_root + data.existing_notice_document_list[idx].file_ext_path + '" alt="' + data.existing_notice_document_list[idx].file_name + '">';
                        noticeFiles += '<span>' + data.existing_notice_document_list[idx].file_name + '</span>';
                        noticeFiles += '</li>';
                    });
                    noticeFiles += '</ul>';
                    noticeFiles += '</section>';
                }
                // 添付資料
                if (data.files.length > 0) {
                    noticeFiles += '<section>';
                    noticeFiles += '<h3>[新規]</h3>';
                    noticeFiles += '<ul>';
                    $.each(data.files, function(idx, value) {
                        noticeFiles += '<li>';
                        noticeFiles += '<img class="icon" src="' + site_root + data.files[idx].file_ext_path + '" alt="' + data.files[idx].file_name + '">';
                        noticeFiles += '<span>' + data.files[idx].file_name + '</span>';
                        noticeFiles += '</li>';
                    });
                    noticeFiles += '</ul>';
                    noticeFiles += '</section>';
                } else {
                    noticeFiles += '<section>';
                    noticeFiles += '<h3>[新規]</h3>';
                    noticeFiles += '添付資料なし';
                    noticeFiles += '</section>';
                }
                // $('#notice-category-confrim').text(noticeCategoryName);
                // $('#notice-title-confrim').text(noticeTitle);
                // $('#notice-body-confrim').html(noticeBody.replaceAll('\n', '<br>'));
                // $('#notice-relation-confrim').html(noticeRelation);
                $('#notice-file-confrim').html(noticeFiles);
                $('#notice-conf-body').html(data.postData.replaceAll('\n', '<br>'));
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('noticeConfrimModal'));
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

    // 登録完了ボタン押下イベント
    $("#notice-regist-complete").click(function() {

        let files_num = 0;
        let myModal;
        
        // 送信データセット
        let fd = new FormData();
        fd.append('notice_info_id', $('#notice-info-id').val());
        fd.append('notice_category_id', $('#notice-category-id').val());
        fd.append('notice_title', $('#notice-title').val());
        fd.append('notice_body', $('#notice-body').val());
        fd.append('relation_event_id', $('#relation-event-id').val());
        fd.append('relation_event_name', $('#relation-event-id option:selected').text());
        fd.append('regist_user_mail_flg', $('#regist-user-mail-flg').prop('checked') ? 1 : 0);
        fd.append('set_notice_category_id', $('#set-notice-category-id').val());
        fd.append('set_relation_event_id', $('#set-relation-event-id').val());
        fd.append('regist_mode', $('#regist-mode').val());
        for (let i=1; i<=10; i++) {
            if ($('#notice-files' + i).prop('files') != undefined) {
                let notice_files = $('#notice-files' + i).prop('files')[0];
                if (notice_files !== undefined) {
                    files_num++;
                    fd.append('notice_files' + files_num, notice_files);
                }
            }
        }

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/notice_regist_proc",
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
                $('#modal-body-common-regist-comp').text("お知らせ登録が完了しました");
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

    // 資料削除ボタン押下イベント
    $(".notice-document-delete").click(function() {

        let myModal;

        let document_name = $(this).data("document-name");

        if (confirm('「' + document_name + '」を削除してもよろしいですか？')) {
            
            // 送信データセット
            let notice_info_id = $("#notice-info-id").val();
            let document_id = $(this).data('document-id');
            
            // Ajax通信
            $.ajax({
                type: "POST",
                url: site_root + "admin/delete_notice_document",
                dataType: "json",
                data: {
                    notice_info_id: notice_info_id,
                    document_id: document_id,
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
                    myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                    myModal.show();
                } else {
                    $('#modal-title-common-regist-comp').text("削除完了");
                    $('#modal-body-common-regist-comp').text("お知らせ資料の削除が完了しました");
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
        }
    });
    
});