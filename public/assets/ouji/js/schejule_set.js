$(function () {

    // 開場時間設定ログインボタンクリックイベント
    $('#schejule_set_login').on('click', function () {
        // フォーム送信
        var frm = $('#schejule_set_login_check');
        frm.attr('action', './schejule_set_login_check.php');
        frm.submit();
    });

    $(document).ready(function(){
        $('.select_sc').each( function(){
            var css;
            var sc = $(this).val();
            switch (sc) {
                case '0' : css = 'nouse'; break;
                case '1' : css = 'use'; break;
                case '2' : css = 'taiiku'; break;
            }
            $(this).removeClass();
            $(this).addClass("select_sc");
            $(this).addClass(css);
        });
    });

    $('.select_sc').bind('change', function(){
        var css;
        var sc = $(this).val();
        switch (sc) {
            case '0' : css = 'nouse'; break;
            case '1' : css = 'use'; break;
            case '2' : css = 'taiiku'; break;
        }
        $(this).removeClass();
        $(this).addClass("select_sc");
        $(this).addClass(css);
    });

    // 体育館の基本スケジュールを反映
    $('#set_default_schejule_taiiku').on('click', function () {

        // 火曜
        if ($("[week = 'n2']").val() == 0) { $("[week = 'n2']").val(2); }
        // 水曜
        if ($("[week = 'n3']").val() == 0) { $("[week = 'n3']").val(2); }
        // 木曜
        // if ($("[week = 'm4']").val() == 0) { $("[week = 'm4']").val(2); }
        // 土曜
        if ($("[week = 'n6']").val() == 0) { $("[week = 'n6']").val(2); }
        // 日曜
        if ($("[week = 'm0']").val() == 0) { $("[week = 'm0']").val(2); }
        if ($("[week = 'a0']").val() == 0) { $("[week = 'a0']").val(2); }

        // プルダウン変更イベントを起動
        $('.select_sc').trigger('change');
    });

    // 王子道場の基本スケジュールを反映
    $('#set_default_schejule_ouji').on('click', function () {

        // 月曜
        if ($("[week = 'm1']").val() == 0) { $("[week = 'm1']").val(1); }
        if ($("[week = 'a1']").val() == 0) { $("[week = 'a1']").val(1); }
        if ($("[week = 'n1']").val() == 0) { $("[week = 'n1']").val(1); }
        // 水曜
        if ($("[week = 'm3']").val() == 0) { $("[week = 'm3']").val(1); }
        if ($("[week = 'a3']").val() == 0) { $("[week = 'a3']").val(1); }
        // 木曜
        if ($("[week = 'a4']").val() == 0) { $("[week = 'a4']").val(1); }
        if ($("[week = 'n4']").val() == 0) { $("[week = 'n4']").val(1); }
        // 金曜
        if ($("[week = 'n5']").val() == 0) { $("[week = 'n5']").val(1); }
        // 土曜
        if ($("[week = 'm6']").val() == 0) { $("[week = 'm6']").val(1); }
        if ($("[week = 'a6']").val() == 0) { $("[week = 'a6']").val(1); }

        // プルダウン変更イベントを起動
        $('.select_sc').trigger('change');
    });

    // 前月ボタンクリックイベント
    $('#scejule_prev').on('click', function () {

        var prev_year = Number($('#select_year').val());
        var prev_month = Number($('#select_month').val()) - 1;

        // 月が1月より前の場合は前年の12月にする
        if (prev_month < 1) {
            prev_year = prev_year - 1;
            prev_month = 12;
        }

        // フォーム送信
        var frm = $('#change_month');
        $('#select_year').val(prev_year);
        $('#select_month').val(prev_month);

        frm.submit();
    });

    // 当月ボタンクリックイベント
    $('#scejule_now').on('click', function () {

        var date = new Date();
        var prev_year = Number(date.getFullYear());
        var prev_month = Number(date.getMonth()) + 1;

        // フォーム送信
        var frm = $('#change_month');
        $('#select_year').val(prev_year);
        $('#select_month').val(prev_month);

        frm.submit();
    });

    // 翌月ボタンクリックイベント
    $('#scejule_next').on('click', function () {

        var prev_year = Number($('#select_year').val());
        var prev_month = Number($('#select_month').val()) + 1;

        // 月が12月より後の場合は翌年の1月にする
        if (prev_month > 12) {
            prev_year = prev_year + 1;
            prev_month = 1;
        }

        // フォーム送信
        var frm = $('#change_month');
        $('#select_year').val(prev_year);
        $('#select_month').val(prev_month);

        frm.submit();
    });

    // 設定確認（月間予定）ボタンクリックイベント
    $('#set_schejule_confrim').on('click', function () {
        // フォーム送信
        var frm = $('#schejule_set_confrim');
        frm.submit();
    });

    // 確認画面：戻るボタンクリックイベント
    $('#set_schejule_back').on('click', function () {
        // フォーム送信
        var frm = $('#schejule_set_complete');
        frm.attr('action', './schejule_set.php');
        frm.submit();
    });

    // 設定確認（月間予定）ボタンクリックイベント
    $('#set_schejule_complete').on('click', function () {
        // フォーム送信
        var frm = $('#schejule_set_complete');
        frm.attr('action', './schejule_set_regist.php');
        frm.submit();
    });

    // 設定画面に戻るボタンクリックイベント
    $('#back_set_schejule').on('click', function () {
        // フォーム送信
        var frm = $('#frm_back');
        frm.attr('action', './schejule_set.php');
        frm.submit();
    });

});
