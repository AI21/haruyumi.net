$(function(){

    let site_root = $("head").data("site-root");
    let shinsa_class_id = $("#shinsa-class-id").val();

    // 審査登録：審査地区の変更イベント
    $('#area-group-id').change(function() {

        // 地方・ビデオ審査は対象外
        if (shinsa_class_id == 3 || shinsa_class_id == 4) {
            return;
        }

        // 審査会場のセレクトボックスを変更
        let area_group_id = $(this).val();
        $.ajax({
            type: "POST",
            url: site_root + "admin/get_shinsa_kaijo_list",
            dataType: "json",
            data: {
                shinsa_class_id: shinsa_class_id,
                area_group_id: area_group_id
            }
        }).done(function( data ) {
            if (data.result == true) {
                let kaijoOptions = '<option value="">選択してください</option>';
                if (data.shinsaKaijoList.numRows > 0) {
                    $.each(data.shinsaKaijoList.result, function(index, value) {
                        kaijoOptions += '<option value="' + value.kaijo_id + '">' + value.kaijo_name + '</option>';
                    });
                }
                $('#kaijo-id-1').html(kaijoOptions);
                $('#kaijo-id-2').html(kaijoOptions);
                $('#kaijo-id-3').html(kaijoOptions);
            } else {
                // エラーメッセージ
                let error_msg = "<ul>";
                $.each(data.error, function(index, value) {
                     error_msg += "<li>" + value + "</li>";
                })
                error_msg += "</ul>";
                $('#error-message').html(error_msg);
                $('#shinsa-error-title').html("審査会場取得エラー");
                // モーダル表示
                let myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
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

    // 審査登録・更新：審査日の変更イベント
    $('#shinsa-date-1').change(function() {
        // 連合審査は対象外
        if (shinsa_class_id != 2) {
            return;
        }
        // WEB申込開始日は審査日の3ヶ月前
        setWebApplyStartDay($(this).val(), -3, 'month');
    });

    // 審査登録・更新：愛弓連申込期間（開始）の変更イベント
    $('#uketuke-limit-aikyuren-st').change(function() {
        // 連合審査は対象外
        if (shinsa_class_id == 2) {
            return;
        }
        // WEB申込開始日は愛弓連申込開始日の1週間前
        setWebApplyStartDay($(this).val(), -1, 'week');
    });

    // WEB申込開始日のセット（愛弓連申込開始日の1週間前、もしくは審査日の3ヶ月前）
    function setWebApplyStartDay(days, offset, unit) {
        let webApplyStartDay = '';
        const adjusted = adjustDate(days, offset, unit);
        const formatted = dateFormatJP(adjusted, true, 'Y/m/d');
        if (days !== '') {
            // YYYY/MM/DD 曜日形式で表示（1週間前に調整）
            webApplyStartDay = formatted + '（' + getWeek(adjusted) + '）';
        } else {
            webApplyStartDay = '未定';
        }
        $('#web-apply-start-day-view').html(webApplyStartDay);
        $('#web-apply-start-day').val(dateFormatJP(adjusted, true, 'Y-m-d', ''));
    }
    
    // 審査登録・更新：参加受付日程チェックボックス押下イベント
    $("#uketuke-limit-aikyuren-set").click(function() {
        if ($(this).prop('checked') == true) {
            $("#uketuke-limit-aikyuren-area").removeClass('d-none');
            $("#uketuke-limit-aikyuren-text").text('設定');
        } else {
            $("#uketuke-limit-aikyuren-area").addClass('d-none');
            $("#uketuke-limit-aikyuren-text").text('未定');
        }
    });

    // 更新ボタン押下イベント
    $("#shinsa-revision").click(function() {

        // 画面遷移先URL
        let url = site_root + "admin/shinsa_revision";

        // 送信データ取得
        let shinsa_id = $('#shinsa_id').val();

        // パラメータ付与
        let params = [
            ["shinsa_id", shinsa_id]
        ];

        // POST遷移
        formSubmit(url, params);
    });

    // 審査詳細に戻る
    $(".modal-back-shinsa-detail").click(function() {
        let shinsa_id = $("#shinsa-id").val();
        let url = site_root + "shinsa";
        // 審査IDがある場合は詳細画面へ遷移
        if (shinsa_id !== undefined && shinsa_id !== "0") {
            url += "/detail/" + shinsa_id;
        }
        window.location.href = url;
    });

    // 登録確認ボタン押下イベント
    $("#shinsa-regist-check").click(function() {

        let files_num = 0;
        let myModal;

        // データ取得
        let upload_file_num = $('#upload-file-num').val();
        let regist_mode = $('#regist-mode').val();
        let area_group_id = "";
        let shinsa_name_id = "";
        let shinsa_class_id = $('#shinsa-class-id').val();
        let shinsa_name = "";
        if (regist_mode == 'regist') {
            if (shinsa_class_id != 3) {
                shinsa_name = "【" + $('#area-group-id').find('option:selected').text() + "】";
            }
            shinsa_name += $('#shinsa-name-id').find('option:selected').text();
            area_group_id = $('#area-group-id').val();
            shinsa_name_id = $('#shinsa-name-id').val();
        } else if (regist_mode == 'revision') {
            shinsa_name = $('#shinsa-name').val();
        }
        
        // 送信データセット
        let fd = new FormData();
        fd.append('shinsa_id', $('#shinsa-id').val());
        fd.append('shinsa_name', shinsa_name);
        fd.append('area_group_id', area_group_id);
        fd.append('shinsa_name_id', shinsa_name_id);
        fd.append('all_holder_grade_id', $('#all-holder-grade-id').val());
        fd.append('all_holder_grade_name', $('#all-holder-grade-id option:selected').text());
        fd.append('gender_cd', $('input[name=gender-cd]:checked').val());
        fd.append('uketuke_limit_zenkyuren', $('#uketuke-limit-zenkyuren').val());
        fd.append('uketuke_limit_aikyuren_set', $('#uketuke-limit-aikyuren-set').prop('checked') ? 1 : 0);
        fd.append('uketuke_limit_aikyuren_st', $('#uketuke-limit-aikyuren-st').val());
        fd.append('uketuke_limit_aikyuren_ed', $('#uketuke-limit-aikyuren-ed').val());
        fd.append('web_apply_start_day', $('#web-apply-start-day').val());
        fd.append('shinsa_date_1', $('#shinsa-date-1').val());
        fd.append('shinsa_date_2', $('#shinsa-date-2').val());
        fd.append('shinsa_date_3', $('#shinsa-date-3').val());
        fd.append('holder_grade_id_1', $('#holder-grade-id-1').val());
        fd.append('holder_grade_id_2', $('#holder-grade-id-2').val());
        fd.append('holder_grade_id_3', $('#holder-grade-id-3').val());
        fd.append('holder_grade_name_1', $('#holder-grade-id-1 option:selected').text());
        fd.append('holder_grade_name_2', $('#holder-grade-id-2 option:selected').text());
        fd.append('holder_grade_name_3', $('#holder-grade-id-3 option:selected').text());
        fd.append('kaijo_id_1', $('#kaijo-id-1').val());
        fd.append('kaijo_id_2', $('#kaijo-id-2').val());
        fd.append('kaijo_id_3', $('#kaijo-id-3').val());
        fd.append('kaijo_name_1', $('#kaijo-id-1 option:selected').text());
        fd.append('kaijo_name_2', $('#kaijo-id-2 option:selected').text());
        fd.append('kaijo_name_3', $('#kaijo-id-3 option:selected').text());
        fd.append('additional_info_1', $('#additional-info-1').val());
        fd.append('additional_info_2', $('#additional-info-2').val());
        fd.append('additional_info_3', $('#additional-info-3').val());
        fd.append('shinsa_class_id', shinsa_class_id);
        fd.append('upload_file_num', upload_file_num);
        fd.append('regist_mode', regist_mode);
        for (let i=1; i<=upload_file_num; i++) {
            if ($('#shinsa-files' + i).prop('files') != undefined) {
                let shinsa_files = $('#shinsa-files' + i).prop('files')[0];
                if (shinsa_files !== undefined) {
                    files_num++;
                    fd.append('shinsa_files' + files_num, shinsa_files);
                }
            }
        }
        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/shinsa_regist_conf",
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
                $('#modal-title-shinsa-regist-confrim').text('大会情報 更新確認');
                $('#modal-body-shinsa-regist-confrim').html(data.html);
                $('#notice-file-confrim').html(noticeFiles);
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('shinsaRegistConfrimModal'));
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
    $("#shinsa-regist-complete").click(function() {

        let files_num = 0;
        let myModal;

        // データ取得
        let shinsa_date_ed = $('#shinsa-date-ed').val();
        let upload_file_num = $('#upload-file-num').val();
        if (shinsa_date_ed === '') {
            shinsa_date_ed = $('#shinsa-date-st').val();
        }
        
        // 送信データセット
        let fd = new FormData();
        fd.append('shinsa_id', $('#shinsa-id').val());
        fd.append('shinsa_name', $('#shinsa-name').val());
        fd.append('area_group_id', $('#area-group-id').val());
        fd.append('shinsa_name_id', $('#shinsa-name-id').val());
        fd.append('all_holder_grade_id', $('#all-holder-grade-id').val());
        fd.append('all_holder_grade_name', $('#all-holder-grade-id option:selected').text());
        fd.append('gender_cd', $('input[name=gender-cd]:checked').val());
        fd.append('uketuke_limit_zenkyuren', $('#uketuke-limit-zenkyuren').val());
        fd.append('uketuke_limit_aikyuren_set', $('#uketuke-limit-aikyuren-set').prop('checked') ? 1 : 0);
        fd.append('uketuke_limit_aikyuren_st', $('#uketuke-limit-aikyuren-st').val());
        fd.append('uketuke_limit_aikyuren_ed', $('#uketuke-limit-aikyuren-ed').val());
        fd.append('regist_mode', $('#regist-mode').val());
        fd.append('shinsa_date_1', $('#shinsa-date-1').val());
        fd.append('shinsa_date_2', $('#shinsa-date-2').val());
        fd.append('shinsa_date_3', $('#shinsa-date-3').val());
        fd.append('holder_grade_id_1', $('#holder-grade-id-1').val());
        fd.append('holder_grade_id_2', $('#holder-grade-id-2').val());
        fd.append('holder_grade_id_3', $('#holder-grade-id-3').val());
        fd.append('holder_grade_name_1', $('#holder-grade-id-1 option:selected').text());
        fd.append('holder_grade_name_2', $('#holder-grade-id-2 option:selected').text());
        fd.append('holder_grade_name_3', $('#holder-grade-id-3 option:selected').text());
        fd.append('kaijo_id_1', $('#kaijo-id-1').val());
        fd.append('kaijo_id_2', $('#kaijo-id-2').val());
        fd.append('kaijo_id_3', $('#kaijo-id-3').val());
        fd.append('kaijo_name_1', $('#kaijo-id-1 option:selected').text());
        fd.append('kaijo_name_2', $('#kaijo-id-2 option:selected').text());
        fd.append('kaijo_name_3', $('#kaijo-id-3 option:selected').text());
        fd.append('additional_info_1', $('#additional-info-1').val());
        fd.append('additional_info_2', $('#additional-info-2').val());
        fd.append('additional_info_3', $('#additional-info-3').val());
        fd.append('shinsa_class_id', $('#shinsa-class-id').val());
        fd.append('upload_file_num', upload_file_num);
        fd.append('regist_mode', $('#regist-mode').val());
        for (let i=1; i<=upload_file_num; i++) {
            if ($('#shinsa-files' + i).prop('files') != undefined) {
                let shinsa_files = $('#shinsa-files' + i).prop('files')[0];
                if (shinsa_files !== undefined) {
                    files_num++;
                    fd.append('shinsa_files' + files_num, shinsa_files);
                }
            }
        }

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/shinsa_regist_proc",
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
                    $('#modal-title-shinsa-regist-comp').text("登録完了");
                    $('#modal-body-shinsa-regist-comp').text("審査の登録が完了しました");
                } else {
                    $('#modal-title-shinsa-regist-comp').text("更新完了");
                    $('#modal-body-shinsa-regist-comp').text("審査の更新が完了しました");
                }
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('shinsaRegistCompModal'));
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

    // 申請者代理登録ボタン押下イベント
    $("#add-member-btn").click(function() {

        let myModal;

        // モーダル表示
        myModal = new bootstrap.Modal(document.getElementById('proxyAddMemberShinsaModal'));
        myModal.show();
    });

    // 申請者代理登録モーダル：審査種別変更イベント
    $("#shinsa-target-id").change(function() {

        // 選択された審査種別のIDを取得
        let shinsa_target_id = $(this).val();
        let shinsa_id = $('#shinsa_id').val();

        // 申請者リストを更新
        $.ajax({
            type: "POST",
            url: site_root + "admin/shinsa_target_member_list",
            dataType: "json",
            data: {
                shinsa_target_id: shinsa_target_id,
                shinsa_id: shinsa_id
            }
        }).done(function( data ) {
            
            // 選択肢をクリア
            $('#add-member').empty();

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

                const sel = $('#add-member-id');
                sel.empty(); // OPTION 全削除

                if (data.shinsaTargetList.numRows == 0) {
                    sel.append('<option value="">対象者なし</option>');
                    return;
                }
                
                sel.append('<option value="">選択してください</option>');

                let names = "";
                // 新しい選択肢を追加
                $.each(data.shinsaTargetList.result, function(index, value) {
                    names = value.name_f + " " + value.name_s;
                    sel.append('<option value="' + value.member_id + '">' + names + '</option>');
                });
            }
        });
    });

    // 代理申請登録確認ボタン押下イベント
    $("#shinsa-add-member-proxy").click(function() {

        let fd = new FormData();
        fd.append('shinsa_id', $('#shinsa_id').val());
        fd.append('member_id', $('#add-member-id').val());
        fd.append('shinsa_target_id', $('#shinsa-target-id').val());

        console.log(fd);
        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/shinsa_add_member_proxy",
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
                myModal = new bootstrap.Modal(document.getElementById('proxySelectMemberShinsaCompModal'));
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

    // 代理審査辞退登録ボタン押下イベント
    $(".shinsa-offer-cancel-proxy").click(function() {

        let myModal;

        let member_id = $(this).data('member-id');
        let member_name = $(this).data("member-name");
        let shinsa_target_name = $(this).data("shinsa-target-name");
        
        // 登録内容セット
        $('#cancel-member-id').val(member_id);
        $('#cancel-member-name').text(member_name);
        $('#cancel-shinsa-target-name').text(shinsa_target_name);

        // モーダル表示
        myModal = new bootstrap.Modal(document.getElementById('proxyCancelMemberShinsaModal'));
        myModal.show();
    });

    // 審査辞退確定ボタン押下イベント
    $("#proxy-cancel-member-complete").click(function() {

        let myModal;
            
        // 送信データセット
        let shinsa_id = $("#shinsa_id").val();
        let member_id = $("#cancel-member-id").val();
        
        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/shinsa_cancel_member_proxy",
            dataType: "json",
            data: {
                shinsa_id: shinsa_id,
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
                myModal = new bootstrap.Modal(document.getElementById('proxyCancelMemberShinsaCompModal'));
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

    // 合否代理登録ボタン押下イベント
    $(".shinsa-result-report-proxy").click(function() {

        // データ取得
        let member_name = $(this).data('member-name');
        let member_id = $(this).data('member-id');

        // データ表示
        $("#shinsa-result-report-proxy-member-name").html(member_name);
        $("#shinsa-result-report-proxy-member-id").val(member_id);

        // モーダル表示
        myModal = new bootstrap.Modal(document.getElementById('shinsaResultReportProxyConfrim'));
        myModal.show();

    });

    $("#offer-ilst").tablesorter({});

    // 審査結果代理登録ボタン押下イベント
    $("#shinsa-result-report-proxy-submit").click(function() {

        // データ取得
        let shinsa_id = $('#shinsa_id').val();
        let member_id = $('#shinsa-result-report-proxy-member-id').val();
        let result_flg = $('[name=shinsa_result_report_proxy]:checked').val();
        console.log("result_flg:" + result_flg);

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/shinsa_result_report_proxy",
            dataType: "json",
            data: {
                shinsa_id: shinsa_id,
                member_id: member_id,
                result_flg: result_flg
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
                $('#shinsa-error-title').html("審査結果代理登録エラー");
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('shinsaResultReportProxyComplete'));
                myModal.show();
            }
        }).fail(function() {
            alert("エラー発生");
            myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
            // $("#admin-revision").data('select-notice-info-id', notice_info_id);
        });

    });

    // 昇段登録モーダル表示
    $(".rankup-confrim").click(function() {

        // データ取得
        let member_id = $(this).data('member-id');
        let pass_holder_id = $(this).data('pass-holder-id');
        let pass_grade_group_id = $(this).data('pass-grade-group-id');
        let member_name = $(this).data('member-name');
        let holder_grade = $(this).data('holder-grade');

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/get_pass_grade_group",
            dataType: "json",
            data: {
                pass_holder_id: pass_holder_id,
                pass_grade_group_id: pass_grade_group_id
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
                $('#shinsa-error-title').html("昇段登録エラー");
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                
                // データセット
                $('#rankup-member-id').val(member_id);
                let shinsa_date_ed = $('#shinsa-date-ed').val();
                $('#acquired-day').val(shinsa_date_ed);
                
                let passHolderGradeOptions = '';

                // 段位グループリスト取得
                let checkedHolder = 'checked="checked"';
                if (data.result.holderList.numRows > 0) {
                    $.each(data.result.holderList.result, function(index, value) {
                        if (index > 0) {
                            checkedHolder = '';
                        }
                        passHolderGradeOptions += '<input type="radio" name="pass_holder_id" value="' + value.holder_id + '" ' + checkedHolder + '>';
                        passHolderGradeOptions += '<label for="' + value.grade_id + '">' + value.holder_name + '</label><br>';
                    });
                } else {
                    passHolderGradeOptions += '<input type="hidden" name="pass_holder_id" value="-1">';
                }
                // 段位グループリスト取得
                checkedGrade = 'checked="checked"';
                if (data.result.gradeList.numRows > 0) {
                    $.each(data.result.gradeList.result, function(index, value) {
                        if (index > 0) {
                            checkedGrade = '';
                        }
                        passHolderGradeOptions += '<input type="radio" name="pass_grade_id" value="' + value.grade_id + '" ' + checkedGrade + '>';
                        passHolderGradeOptions += '<label for="' + value.grade_id + '">' + value.grade_name + '</label><br>';
                    });
                } else {
                    passHolderGradeOptions += '<input type="hidden" name="pass_grade_id" value="-1">';
                }

                // データ表示
                $("#rankup-member-name").html(member_name);
                $("#rankup-current-grade").html(holder_grade);
                $("#rankup-new-holder-grade").html(passHolderGradeOptions);

                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('rankupConfrimModal'));
                myModal.show();
            }
        }).fail(function() {
            alert("エラー発生");
            myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
            // $("#admin-revision").data('select-notice-info-id', notice_info_id);
        });
    });

    // 昇段登録
    $("#rankup-result").click(function() {

        // 送信データ取得
        let shinsa_id = $('#shinsa_id').val();
        let member_id = $('#rankup-member-id').val();
        let pass_holder_id = $('[name=pass_holder_id]:checked').val();
        let pass_grade_id = $('[name=pass_grade_id]:checked').val();
        let acquired_day = $('#acquired-day').val();

        if (pass_holder_id === null || pass_holder_id === undefined) {
            pass_holder_id = -1;
        }
        if (pass_grade_id === null || pass_grade_id === undefined) {
            pass_grade_id = -1;
        }

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "admin/rankup_result",
            dataType: "json",
            data: {
                shinsa_id: shinsa_id,
                member_id: member_id,
                pass_holder_id: pass_holder_id,
                pass_grade_id: pass_grade_id,
                acquired_day: acquired_day,
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
                $('#shinsa-error-title').html("昇段登録エラー");
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('rankupCompleteModal'));
                myModal.show();
            }

        }).fail(function() {
            alert("エラー発生");
            myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
            myModal.show();
        }).always(function() {
            // $("#admin-revision").data('select-notice-info-id', notice_info_id);
        });
    });

    // 合格者昇段お知らせ投稿ボタン押下イベント
    $("#shinsa-result-pass-notice").click(function() {
        // お知らせ投稿フォームに移動
        let shinsa_id = $('#shinsa_id').val();
        let url = site_root + "admin/notice_regist_shinsa_promotion/" + shinsa_id;
        window.location.href = url;
    });

});