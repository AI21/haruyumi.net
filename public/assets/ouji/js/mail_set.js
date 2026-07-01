$(function () {

    // メール修正ボタンクリックイベント
    $('#btn_mail_redo').on('click', function () {
        // メールフォームを読み取り解除にしてアドレス検索ボタン表示
        $('#mail').attr('readonly',false);
        $('#btn_mail_search').css("display", "inline");
        // メールアドレスやり直しボタン非表示
        $('#btn_mail_redo').css("display", "none");
        // メール設定枠を非表示
        $('#mail_setting').css("display", "none");
        $('#set_mail_area').css("display", "none");
    });

    // ユーザー登録完了ボタンクリックイベント
    $('#btn_mail_set_complete').on('click', function () {

        var mail = $('#mail').val();
        var mail_change = $('#mail_change').val();
        $('#error').text("");

        // メール形式チェック
        if (checkMail(mail) == false) {
            $('#error').text("メールアドレスの形式が違います");
            $('p#error').css("display", "block");
            return;
        }
        if (checkMail(mail_change) == false) {
            $('#error').text("変更するメールアドレスの形式が違います");
            $('p#error').css("display", "block");
            return;
        }
        var frm = $('#mail_set_complete');
        frm.attr('action', './mail_set_complete.php');
        frm.submit();
    });

    // 開場設定画面に戻るボタンクリックイベント
    $('#back_mail_set').on('click', function () {
        var frm = $('#frm_back');
        frm.attr('action', './mail_set.php');
        frm.submit();
    });

    // メール変更ボタンクリックイベント
    $('#btn_mail_change').on('click', function () {
        // メールアドレス変更フォーム表示
        $('#mail_change_area').css("display", "block");
        $('#btn_mail_non_change').css("display", "block");
    });
    // 変更しないボタンクリックイベント
    $('#btn_mail_non_change').on('click', function () {
        // メールアドレス変更フォーム表示
        $('#mail_change_area').css("display", "none");
        $('#btn_mail_non_change').css("display", "none");
        $('#btn_mail_change').css("display", "block");
        $('#mail_change').val("");
        $('#error').text("");
    });

    // メール検索ボタンクリックイベント
    $('#btn_mail_search').on('click', function () {

        var mail = $('#mail').val();
        $('#error').text("");
        if ($('#set_mail').val() == 0){
            $('#error_conf').text("");
        }

        // フォームクリア
        $('#user_id').val('');
        $("input[name^='name_']").val('');
        $("input[name^='send_']").prop('checked', false);
        $("input[name^='mail_']").prop('checked', false);

        // メール空チェック
        if (mail.length == 0) {
            $('#error').text("メールアドレスを入力してください");
            $('p#error').css("display", "block");
            $('#mail_setting').css("display", "none");
            return;
        }

        // メール形式チェック
        if (checkMail(mail) == false) {
            $('#error').text("メールアドレスの形式が違います");
            $('p#error').css("display", "block");
            $('#mail_setting').css("display", "none");
            return;
        }

        $.ajax({
            type: "POST",
            url: "ajax_mail_search.php",
            dataType: "json",
            data: { mail: mail }
        }).done(function( data ) {
            // alert( "データ保存: " + data );
            // console.log(data);
            // console.log(data.data_count);
            // 氏名と曜日選択フォームを表示
            if ($('#set_mail').val() == 0) {
                $('p#error_conf').css("display", "none");
            }
            $('#mail_setting').css("display", "block");
            $('#set_mail_area').css("display", "block");

            // データHIT
            if (data.data_count > 0) {
                // メールアドレス変更ボタン表示
                $('#btn_mail_change').css("display", "block");
                // メッセージ
                $('#mail_confrim').text("");
                $('p#mail_confrim').css("display", "none");
                // ユーザー名のフォーム設定
                $('#name_f').css("display", "none");
                $('#name_s').css("display", "none");
                $('#user_name').css("display", "block");
                $('#user_name').text(data.name_f + " " + data.name_s);
                if (data.ouji_admin == 1) {
                    $('#btn_mail_delete').css("display", "none");
                    $('#ouji_admin').val("1");
                } else {
                    $('#ouji_admin').val("0");
                }
                // ユーザーIDセット
                $('#user_id').val(data.user_id);
                // 氏名入力
                $('#name_f').val(data.name_f);
                $('#name_s').val(data.name_s);
                // メール希望日セット
                if (data.mail_m_1 > 0) { $('#mail_m_1').prop("checked",true).change(); }
                if (data.mail_a_1 > 0) { $('#mail_a_1').prop("checked",true).change(); }
                if (data.mail_n_1 > 0) { $('#mail_n_1').prop("checked",true).change(); }
                if (data.mail_m_2 > 0) { $('#mail_m_2').prop("checked",true).change(); }
                if (data.mail_a_2 > 0) { $('#mail_a_2').prop("checked",true).change(); }
                if (data.mail_n_2 > 0) { $('#mail_n_2').prop("checked",true).change(); }
                if (data.mail_m_3 > 0) { $('#mail_m_3').prop("checked",true).change(); }
                if (data.mail_a_3 > 0) { $('#mail_a_3').prop("checked",true).change(); }
                if (data.mail_n_3 > 0) { $('#mail_n_3').prop("checked",true).change(); }
                if (data.mail_m_4 > 0) { $('#mail_m_4').prop("checked",true).change(); }
                if (data.mail_a_4 > 0) { $('#mail_a_4').prop("checked",true).change(); }
                if (data.mail_n_4 > 0) { $('#mail_n_4').prop("checked",true).change(); }
                if (data.mail_m_5 > 0) { $('#mail_m_5').prop("checked",true).change(); }
                if (data.mail_a_5 > 0) { $('#mail_a_5').prop("checked",true).change(); }
                if (data.mail_n_5 > 0) { $('#mail_n_5').prop("checked",true).change(); }
                if (data.mail_m_6 > 0) { $('#mail_m_6').prop("checked",true).change(); }
                if (data.mail_a_6 > 0) { $('#mail_a_6').prop("checked",true).change(); }
                if (data.mail_n_6 > 0) { $('#mail_n_6').prop("checked",true).change(); }
                if (data.mail_m_0 > 0) { $('#mail_m_0').prop("checked",true).change(); }
                if (data.mail_a_0 > 0) { $('#mail_a_0').prop("checked",true).change(); }
                if (data.mail_n_0 > 0) { $('#mail_n_0').prop("checked",true).change(); }
                if (data.mail_m_99 > 0) { $('#mail_m_99').prop("checked",true).change(); }
                if (data.mail_a_99 > 0) { $('#mail_a_99').prop("checked",true).change(); }
                if (data.mail_n_99 > 0) { $('#mail_n_99').prop("checked",true).change(); }

            // データなし：新規
            } else {
                // メールアドレス変更ボタン非表示
                $('#btn_mail_change').css("display", "none");
                // メッセージ
                $('#mail_confrim').text("上記メールの登録がありませんでした。");
                $('p#mail_confrim').css("display", "block");
                $('#name_f').css("display", "inline");
                $('#name_s').css("display", "inline");
                $('#user_name').css("display", "none");
                $('#btn_mail_delete').css("display", "none");
                $('#ouji_admin').val("0");
            }
            // メールフォームを読み取りにしてアドレス検索ボタン非表示
            $('#mail').attr('readonly',true);
            $('#btn_mail_search').css("display", "none");
            // メールアドレスやり直しボタン表示
            $('#btn_mail_redo').css("display", "inline");
        })
        // Ajaxリクエストが失敗した場合
        .fail(function(XMLHttpRequest, textStatus, errorThrown){
            // alert(errorThrown);
            // 氏名と曜日選択フォームを表示
            $('#error').text("データ確認に失敗しました");
            $('p#error').css("display", "block");
            $('#mail_setting').css("display", "none");
        });
    });

    // メール検索ボタンクリックイベント
    $('#btn_mail_delete').on('click', function () {
        if (confirm('送信してもいいですか？')) {

            var user_id = $('#user_id').val();

            $.ajax({
                type: "POST",
                url: "ajax_mail_delete.php",
                dataType: "json",
                data: { user_id: user_id }
            }).done(function( data ) {
                window.location.href = "./mail_set.php";
            })
            // Ajaxリクエストが失敗した場合
            .fail(function(XMLHttpRequest, textStatus, errorThrown){
                alert("データ削除に失敗しました");
            });
        }
    });

    // 全選択
    // $('#send_all').on('click', function() {
    //     $("input[name^='mail_']").prop('checked', this.checked);
    //     $("input[name='send_m_all']").prop('checked', this.checked);
    //     $("input[name='send_a_all']").prop('checked', this.checked);
    //     $("input[name='send_n_all']").prop('checked', this.checked);
    // });
    // 午前選択
    $('#send_m_all').on('click', function() {
        $("input[name^='mail_m']").prop('checked', this.checked);
    });
    // 午後選択
    $('#send_a_all').on('click', function() {
        $("input[name^='mail_a']").prop('checked', this.checked);
    });
    // 夜間選択
    $('#send_n_all').on('click', function() {
        $("input[name^='mail_n']").prop('checked', this.checked);
    });
    // 「午前」チェックボックスのクリックイベント
    $("input[name^='mail_m']").on('change', function() {
        changeCheckbox("m")
    });
    // 「午後」チェックボックスのクリックイベント
    $("input[name^='mail_a']").on('change', function() {
        changeCheckbox("a")
    });
    // 「夜間」チェックボックスのクリックイベント
    $("input[name^='mail_n']").on('change', function() {
        changeCheckbox("n")
    });

    // メール検索イベント発火
    if ($('#set_mail').val() == 1){

        // 登録完了時はダイアログ出力
        if ($('#complete_flg').val() == 1){
            alert("メール配信設定の登録が完了しました");
        }
        $('#btn_mail_search').click();
    }

    function changeCheckbox(mode) {
        if ($('input[name^="mail_' + mode + '"]:checked').length == $('input[name^="mail_' + mode + '"]:input').length) {
            // 全てのチェックボックス：「全チェック」 = ON
            $('#send_' + mode + '_all').prop('checked', true);
        } else {
            // 1つでもチェックOFF：「全チェック」と「全チェック」 = OFF
            $('#send_' + mode + '_all').prop('checked', false);
            // $('#send_all').prop('checked', false);
        }
    }

});
