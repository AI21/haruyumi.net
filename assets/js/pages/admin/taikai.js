$(function(){

    let site_root = $("head").data("site-root");

    // 更新ボタン押下イベント
    $("#taikai-revision").click(function() {

        // 画面遷移先URL
        let url = site_root + "admin/taikai_revision";

        // 送信データ取得
        let taikai_id = $('#taikai_id').val();

        // パラメータ付与
        let params = [
            ["taikai_id", taikai_id]
        ];

        // POST遷移
        formSubmit(url, params);
    });

    // 大会詳細に戻る
    $(".modal-back-taikai-detail").click(function() {
        let taikai_id = $("#taikai-id").val();
        let url = site_root + "taikai/detail/" + taikai_id;
        window.location.href = url;
    });
    
    // 大会登録・更新：大会名チェックボックス押下イベント
    $("#taikai-name-set").click(function() {
        if ($(this).prop('checked') == true) {
            $("#taikai-main-name").addClass('d-none');
            $("#taikai-sub-name-area").removeClass('d-none');
            $("#taikai-name-text").text('別名表示する');
        } else {
            $("#taikai-main-name").removeClass('d-none');
            $("#taikai-sub-name-area").addClass('d-none');
            $("#taikai-name-text").text('別名表示しない');
        }
    });
    
    // 大会登録・更新：会場チェックボックス押下イベント
    $("#kaijo-other-name-set").click(function() {
        if ($(this).prop('checked') == true) {
            $("#kaijo-id").addClass('d-none');
            $("#kaijo-other-name").removeClass('d-none');
            $("#kaijo-other-name-text").text('特設会場等を設定する');
        } else {
            $("#kaijo-id").removeClass('d-none');
            $("#kaijo-other-name").addClass('d-none');
            $("#kaijo-other-name-text").text('特設会場等を設定しない');
        }
    });
    
    // 大会登録・更新：開場時間チェックボックス押下イベント
    $("#taikai-open-time-set").click(function() {
        if ($(this).prop('checked') == true) {
            $("#taikai-open-time").removeClass('d-none');
            $("#taikai-open-time-text").text('設定');
        } else {
            $("#taikai-open-time").addClass('d-none');
            $("#taikai-open-time-text").text('未定');
        }
    });
    
    // 大会登録・更新：受付時間チェックボックス押下イベント
    $("#taikai-uketuke-time-set").click(function() {
        if ($(this).prop('checked') == true) {
            $("#taikai-uketuke-time").removeClass('d-none');
            $("#taikai-uketuke-time-text").text('設定');
        } else {
            $("#taikai-uketuke-time").addClass('d-none');
            $("#taikai-uketuke-time-text").text('未定');
        }
    });
    
    // 大会登録・更新：大会時間チェックボックス押下イベント
    $("#taikai-time-set").click(function() {
        if ($(this).prop('checked') == true) {
            $("#taikai-time-area").removeClass('d-none');
            $("#taikai-time-text").text('設定');
        } else {
            $("#taikai-time-area").addClass('d-none');
            $("#taikai-time-text").text('未定');
        }
    });
    
    // 大会登録・更新：参加受付日程チェックボックス押下イベント
    $("#taikai-uketuke-set").click(function() {
        if ($(this).prop('checked') == true) {
            $("#taikai-uketuke-area").removeClass('d-none');
            $("#taikai-uketuke-text").text('設定');
        } else {
            $("#taikai-uketuke-area").addClass('d-none');
            $("#taikai-uketuke-text").text('未定');
        }
    });
    
    // 大会登録・更新：参加可能年齢チェックボックス押下イベント
    $("#age-limit-set").click(function() {
        if ($(this).prop('checked') == true) {
            $("#taikai-age-area").removeClass('d-none');
            $("#taikai-age-text").text('設定');
        } else {
            $("#taikai-age-area").addClass('d-none');
            $("#taikai-age-text").text('年齢不問');
        }
    });

    // 参加者代理登録用TomSelect初期化
    if (document.getElementById('add-member')) {
        var tomSettings = {
            valueField: "value",       // value値
            labelField: "label",        // 表示ラベル
            searchField: ["name", "label", "holder_grade"], // 検索対象
            plugins: ['remove_button'],
            render:{
                option:function(data,escape){
                    return '<div class="d-flex"><span>' + escape(data.label) + '</span><span class="ms-auto text-muted">' + escape(data.holder_grade) + '</span></div>';
                },
                item:function(data,escape){
                    return '<div>' + escape(data.label) + '</div>';
                }
            }
        };
        new TomSelect('#add-member', tomSettings);
    }
    
    // 登録完了ボタン押下イベント
    if (document.getElementById('web-apply-flg')) {
        $("#web-apply-flg").click(function() {
            if ($(this).prop('checked') == true) {
                $("#web-apply-flg-text").text('する');
            } else {
                $("#web-apply-flg-text").text('しない');
            }
        });
    }

    $("#offer-ilst").tablesorter({
        headerTemplate: '{content} {icon}',
        
        // セルに data-text があればそれを優先してソートに使う
        textExtraction: function(node) {
            var attr = $(node).attr('data-text');
            if (typeof attr !== 'undefined' && attr !== false && attr !== '') {
                return attr;
            }
            return $(node).text().trim();
        },
        // tfoot をソート対象から除外
        selectorRemove: "tfoot tr",
        // デフォルトは「段位」(3列目=インデックス1) の昇順ソート
        sortList: [[1,0]]
    });

    // 代理参加登録確認ボタン押下イベント
    $("#taikai-add-member-proxy").click(function() {

        let fd = new FormData();
        fd.append('taikai_id', $('#taikai_id').val());
        fd.append('member_id_list', $('#add-member').val());
        console.log(fd);
        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/taikai_add_member_proxy",
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
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('proxySelectMemberCompModal'));
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

    // 登録確認ボタン押下イベント
    $("#taikai-regist-check").click(function() {

        let files_num = 0;
        let myModal;

        // データ取得
        let taikai_date_ed = $('#taikai-date-ed').val();
        let upload_file_num = $('#upload-file-num').val();
        if (taikai_date_ed === '') {
            taikai_date_ed = $('#taikai-date-st').val();
        }
        
        // 送信データセット
        let fd = new FormData();
        fd.append('taikai_id', $('#taikai-id').val());
        fd.append('taikai_no', $('#taikai-no').val());
        fd.append('taikai_no_flg', $('#taikai-no-flg').val());
        fd.append('taikai_name', $('#taikai-name').val());
        fd.append('taikai_sub_name', $('#taikai-sub-name').val());
        fd.append('taikai_name_set', $('#taikai-name-set').prop('checked') ? 1 : 0);
        fd.append('taikai_date_st', $('#taikai-date-st').val());
        fd.append('taikai_date_ed', taikai_date_ed);
        fd.append('kaijo_id', $('#kaijo-id').val());
        fd.append('kaijo_name', $('#kaijo-id option:selected').text());
        fd.append('kaijo_other_name', $('#kaijo-other-name').val());
        fd.append('kaijo_other_name_set', $('#kaijo-other-name-set').prop('checked') ? 1 : 0);
        fd.append('taikai_open_time_set', $('#taikai-open-time-set').prop('checked') ? 1 : 0);
        fd.append('taikai_open_time', $('#taikai-open-time').val());
        fd.append('taikai_uketuke_time_set', $('#taikai-uketuke-time-set').prop('checked') ? 1 : 0);
        fd.append('taikai_uketuke_time', $('#taikai-uketuke-time').val());
        fd.append('taikai_time_set', $('#taikai-time-set').prop('checked') ? 1 : 0);
        fd.append('taikai_time_st', $('#taikai-time-st').val());
        fd.append('taikai_time_ed', $('#taikai-time-ed').val());
        fd.append('web_apply_flg', $('#web-apply-flg').prop('checked') ? 1 : 0);
        fd.append('taikai_uketuke_set', $('#taikai-uketuke-set').prop('checked') ? 1 : 0);
        fd.append('taikai_uketuke_st', $('#taikai-uketuke-st').val());
        fd.append('taikai_uketuke_ed', $('#taikai-uketuke-ed').val());
        fd.append('gender_cd', $('input[name=gender-cd]:checked').val());
        fd.append('age_limit_set', $('#age-limit-set').prop('checked') ? 1 : 0);
        fd.append('age_limit_min', $('#age-limit-min').val());
        fd.append('age_limit_max', $('#age-limit-max').val());
        fd.append('eligibility', $('#eligibility').val());
        fd.append('competition_rules', $('#competition-rules').val());
        fd.append('awards', $('#awards').val());
        fd.append('contact_info', $('#contact-info').val());
        fd.append('upload_file_num', upload_file_num);
        fd.append('kasugai_flg', $('#kasugai-flg').val());
        for (let i=1; i<=upload_file_num; i++) {
            if ($('#taikai-files' + i).prop('files') != undefined) {
                let taikai_files = $('#taikai-files' + i).prop('files')[0];
                if (taikai_files !== undefined) {
                    files_num++;
                    fd.append('taikai_files' + files_num, taikai_files);
                }
            }
        }
        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/taikai_regist_conf",
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
                let noticeFiles = '';
                if (data.files.length > 0) {
                    noticeFiles += '<div>';
                    noticeFiles += '<ul>';
                    $.each(data.files, function(idx, value) {
                        noticeFiles += '<li>';
                        noticeFiles += '<img src="' + site_root + data.files[idx].file_ext_path + '" alt="' + data.files[idx].file_name + '">';
                        noticeFiles += '<span>' + data.files[idx].file_name + '</span>';
                        noticeFiles += '</li>';
                    });
                    noticeFiles += '</ul>';
                } else {
                    noticeFiles += '添付資料なし';
                }
                $('#modal-title-taikai-regist-confrim').text('大会情報 更新確認');
                $('#modal-body-taikai-regist-confrim').html(data.html);
                $('#notice-file-confrim').html(noticeFiles);
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('taikaiRegistConfrimModal'));
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
    $("#taikai-regist-complete").click(function() {

        let files_num = 0;
        let myModal;

        // データ取得
        let taikai_date_ed = $('#taikai-date-ed').val();
        let upload_file_num = $('#upload-file-num').val();
        if (taikai_date_ed === '') {
            taikai_date_ed = $('#taikai-date-st').val();
        }
        
        // 送信データセット
        let fd = new FormData();
        fd.append('taikai_id', $('#taikai-id').val());
        fd.append('taikai_no', $('#taikai-no').val());
        fd.append('taikai_no_flg', $('#taikai-no-flg').val());
        fd.append('taikai_name', $('#taikai-name').val());
        fd.append('taikai_sub_name', $('#taikai-sub-name').val());
        fd.append('taikai_name_set', $('#taikai-name-set').prop('checked') ? 1 : 0);
        fd.append('taikai_date_st', $('#taikai-date-st').val());
        fd.append('taikai_date_ed', taikai_date_ed);
        fd.append('kaijo_id', $('#kaijo-id').val());
        fd.append('kaijo_name', $('#kaijo-id option:selected').text());
        fd.append('kaijo_other_name', $('#kaijo-other-name').val());
        fd.append('kaijo_other_name_set', $('#kaijo-other-name-set').prop('checked') ? 1 : 0);
        fd.append('taikai_open_time_set', $('#taikai-open-time-set').prop('checked') ? 1 : 0);
        fd.append('taikai_open_time', $('#taikai-open-time').val());
        fd.append('taikai_uketuke_time_set', $('#taikai-uketuke-time-set').prop('checked') ? 1 : 0);
        fd.append('taikai_uketuke_time', $('#taikai-uketuke-time').val());
        fd.append('taikai_time_set', $('#taikai-time-set').prop('checked') ? 1 : 0);
        fd.append('taikai_time_st', $('#taikai-time-st').val());
        fd.append('taikai_time_ed', $('#taikai-time-ed').val());
        fd.append('web_apply_flg', $('#web-apply-flg').prop('checked') ? 1 : 0);
        fd.append('taikai_uketuke_set', $('#taikai-uketuke-set').prop('checked') ? 1 : 0);
        fd.append('taikai_uketuke_st', $('#taikai-uketuke-st').val());
        fd.append('taikai_uketuke_ed', $('#taikai-uketuke-ed').val());
        fd.append('gender_cd', $('input[name=gender-cd]:checked').val());
        fd.append('age_limit_set', $('#age-limit-set').prop('checked') ? 1 : 0);
        fd.append('age_limit_min', $('#age-limit-min').val());
        fd.append('age_limit_max', $('#age-limit-max').val());
        fd.append('eligibility', $('#eligibility').val());
        fd.append('competition_rules', $('#competition-rules').val());
        fd.append('awards', $('#awards').val());
        fd.append('contact_info', $('#contact-info').val());
        fd.append('upload_file_num', upload_file_num);
        fd.append('kasugai_flg', $('#kasugai-flg').val());
        fd.append('regist_mode', $('#regist-mode').val());
        for (let i=1; i<=upload_file_num; i++) {
            if ($('#taikai-files' + i).prop('files') != undefined) {
                let taikai_files = $('#taikai-files' + i).prop('files')[0];
                if (taikai_files !== undefined) {
                    files_num++;
                    fd.append('taikai_files' + files_num, taikai_files);
                }
            }
        }

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/taikai_regist_proc",
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
                if ($('#regist-mode').val() == 'regist') {
                    $('#modal-title-taikai-regist-comp').text("登録完了");
                    $('#modal-body-taikai-regist-comp').text("大会登録が完了しました");
                } else {
                    $('#modal-title-taikai-regist-comp').text("更新完了");
                    $('#modal-body-taikai-regist-comp').text("大会更新が完了しました");
                }
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('taikaiRegistCompModal'));
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
    $(".taikai-document-delete").click(function() {

        let myModal;

        let document_name = $(this).data("document-name");

        if (confirm('「' + document_name + '」を削除してもよろしいですか？')) {
            
            // 送信データセット
            let taikai_id = $("#taikai-id").val();
            let document_id = $(this).data('document-id');
            
            // Ajax通信
            $.ajax({
                type: "POST",
                url: site_root + "admin/delete_taikai_document",
                dataType: "json",
                data: {
                    taikai_id: taikai_id,
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
                    $('#modal-body-common-regist-comp').text(" 大会資料の削除が完了しました");
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

    // 参加者代理登録ボタン押下イベント
    $("#add-member-btn").click(function() {

        let myModal;

        // モーダル表示
        myModal = new bootstrap.Modal(document.getElementById('proxyAddMemberTaikaiModal'));
        myModal.show();
    });

    // 辞退登録ボタン押下イベント
    $(".taikai-offer-cancel-proxy").click(function() {

        let myModal;

        let member_name = $(this).data("member-name");
        let member_id = $(this).data("member-id");
        let holder_grade = $(this).data("holder-grade");
        
        // 登録内容セット
        $('#cancel-member-name').text(member_name);
        $('#cancel-member-holdergrade').text(holder_grade);
        $('#cancel-member-id').val(member_id);

        // モーダル表示
        myModal = new bootstrap.Modal(document.getElementById('proxyCancelMemberModal'));
        myModal.show();
    });

    // 大会辞退確定ボタン押下イベント
    $("#proxy-cancel-member-complete").click(function() {

        let myModal;
            
        // 送信データセット
        let taikai_id = $("#taikai_id").val();
        let member_id = $("#cancel-member-id").val();
        
        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/taikai_cancel_member_proxy",
            dataType: "json",
            data: {
                taikai_id: taikai_id,
                member_id: member_id,
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
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('proxyCancelMemberCompModal'));
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

    // 大会参加者CSVダウンロードボタン押下イベント
    $("#taikai-offer-member-list-csv").click(function() {
        let taikai_id = $("#taikai_id").val();
        let url = site_root + "admin/taikai_member_csv_download/" + taikai_id;
        window.open(url, '_blank');
    });
    
});