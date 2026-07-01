$(function(){

    let site_root = $("head").data("site-root");

    // 改行コード置換
    function replace_br(str) {
        return str.replace(/\r?\n/g, '<br>');
    }

    // HOMEに戻る
    $(".modal-back-home").click(function() {
        // メインページにリダイレクト
        window.location.href = site_root;
    });

    // 画面リロード
    $(".modal-reload").click(function() {
        location.reload();
    });

    // お知らせモーダル表示
    $(".notice-view").click(function() {

        let notice_info_id = $(this).data('notice-info-id');

        // Ajax通信
        $.ajax({
            type: "POST",
            url: site_root + "home/get_notice_detail",
            dataType: "json",
            data: {
                notice_info_id: notice_info_id
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
                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('ajax-error'));
                myModal.show();
            } else {
                let notice_detail = data.result.notice_detail;
                let notice_title = notice_detail.notice_title;
                let notice_body = replace_br(notice_detail.notice_body);
                let notice_created = notice_detail.created;
                let notice_create_mame = notice_detail.cre_mame_f + ' ' + notice_detail.cre_mame_s;
                let notice_document_list = data.result.notice_document_list;
                let modified_flg = notice_detail.modified_flg;

                // 関連イベントリンク表示
                if (notice_detail.relation_menu_id != null && notice_detail.relation_menu_id != 0) {
                    let controller = notice_detail.controller;
                    let relation_link = site_root + controller + '/detail/' + notice_detail.relation_event_id;
                    notice_body += '<hr>';
                    notice_body += '<h2>関連行事</h2>';
                    notice_body += '<p><a href="' + relation_link + '">' + notice_detail.relation_event_name + '</a></p>';
                }

                // 添付資料表示
                if (notice_document_list.numRows > 0) {
                    notice_body += '<hr>';
                    notice_body += '<ul id="notice-document">';
                    
                    $.each(notice_document_list.result, function(idx, data) {
                        notice_body += '<li>';
                        notice_body += '<img src="' + site_root + data.ext_file + '" alt="' + data.document_name + '">';
                        notice_body += '<a href=".' + data.document_path + '" target="_blank">' + data.document_name + '</a>';
                        notice_body += '</li>';
                    })
                    notice_body += '</ul>';
                }

                // 更新可能者は更新・削除ボタン表示
                if (modified_flg == '1') {
                    $("#admin-revision").removeClass("d-none");
                } else {
                    $("#admin-revision").addClass("d-none");
                }
                
                // データ表示
                $("#modal-title-common-notice").text(notice_title);
                $("#modal-body-common-notice").html(notice_body);
                $("#notice-datetime").html(notice_created);
                $("#notice-create-name").html(notice_create_mame);
                $("#admin-revision").data('notice-info-id', notice_detail.notice_info_id);

                // モーダル表示
                myModal = new bootstrap.Modal(document.getElementById('commonNoticeModal'));
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

    // 更新ボタン押下イベント
    $("#notice-revision").click(function() {

        // 画面遷移先URL
        let url = site_root + "admin/notice_revision";

        // 送信データ取得
        let notice_info_id = $("#admin-revision").data('notice-info-id');

        // パラメータ付与
        let params = [
            ["notice_info_id", notice_info_id]
        ];

        // POST遷移
        formSubmit(url, params);
    });

    // 資料削除ボタン押下イベント
    $("#notice-delete").click(function() {

        let myModal;

        if (confirm('お知らせを削除してもよろしいですか？')) {
            
            // 送信データセット
            let notice_info_id = $("#admin-revision").data('notice-info-id');
            
            // Ajax通信
            $.ajax({
                type: "POST",
                url: site_root + "admin/delete_notice_info",
                dataType: "json",
                data: {
                    notice_info_id: notice_info_id
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
                    $('#modal-body-common-regist-comp').text("お知らせの削除が完了しました");
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

    $("#notice-list").tablesorter({
    //     textExtraction: function(node){
    //         var attr = $(node).attr('data-value');
    //         if(typeof attr !== 'undefined' && attr !== false){
    //             return attr;
    //         }
    //         return $(node).text();
    //     }
     })
     .tablesorterPager({container: $("#pager")});
});

// データ送信
function formSubmit(url, params) {

    // 既存の #frm が残っている場合は削除（ブラウザバック対策）
    $("#frm").remove();

    // パラメータを付与する場合
    let inputs = '';
    for(let i = 0, n = params.length; i < n; i++) {
        // 値をエスケープして安全に埋め込む
        let name = $('<div>').text(params[i][0]).html();
        let value = $('<div>').text(params[i][1]).html();
        inputs += '<input type="hidden" name="' + name + '" value="' + value + '">';
    }

    // POST遷移（安全にフォームを生成して append）
    let $form = $('<form>', { action: url, method: 'post', id: 'frm' }).html(inputs);
    $('body').append($form);
    $form.submit();
}

//ローディング表示
function show_loading() {
    $('#loading').removeClass('d-none');
}

//ローディング非表示
function hide_loading() {
    $('#loading').addClass('d-none');
}

/**
 * 日付文字列を指定フォーマットに整形します。
 *
 * @param {string} dateString - 日付文字列（例: "2026-03-12" または "2026/03/12"）
 * @param {boolean} useLocal - ローカル表記（現状は常に有効）
 * @param {string} format - フォーマット文字列（例: "Y/m/d" や "Y-m-d"）
 * @param {string} [defaultValue] - dateString が無効な場合の返却値
 * @returns {string}
 */
function dateFormatJP(dateString, useLocal, format, defaultValue) {
    if (!dateString) {
        return defaultValue !== undefined ? defaultValue : '';
    }

    // Date コンストラクタでパースできない場合（例: "2026/03/12"）にも対応
    let d = new Date(dateString);
    if (isNaN(d.getTime())) {
        const m = dateString.toString().match(/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})/);
        if (m) {
            d = new Date(parseInt(m[1], 10), parseInt(m[2], 10) - 1, parseInt(m[3], 10));
        }
    }
    if (isNaN(d.getTime())) {
        return defaultValue !== undefined ? defaultValue : '';
    }

    const pad = (n) => n.toString().padStart(2, '0');
    const replacements = {
        Y: d.getFullYear(),
        m: pad(d.getMonth() + 1),
        d: pad(d.getDate()),
    };

    return format.replace(/Y|m|d/g, (match) => replacements[match] || match);
}

/**
 * 日付から曜日を取得します (例: "月")
 *
 * @param {string|Date} dateValue
 * @returns {string}
 */
function getWeek(dateValue) {
    let d = dateValue instanceof Date ? dateValue : new Date(dateValue);
    if (isNaN(d.getTime())) {
        const m = dateValue.toString().match(/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})/);
        if (m) {
            d = new Date(parseInt(m[1], 10), parseInt(m[2], 10) - 1, parseInt(m[3], 10));
        }
    }
    if (isNaN(d.getTime())) {
        return '';
    }

    const week = ['日', '月', '火', '水', '木', '金', '土'];
    return week[d.getDay()];
}

/**
 * 日付を相対的に調整します（例: 1週間前、3日後、2か月前）
 *
 * @param {string|Date} dateValue - 調整元の日付
 * @param {number} offset - 調整値（負値で過去、正値で未来）
 * @param {'day'|'week'|'month'|'d'|'w'|'m'} unit - 調整単位
 * @returns {Date|null}
 */
function adjustDate(dateValue, offset, unit) {
    let d = dateValue instanceof Date ? new Date(dateValue.getTime()) : new Date(dateValue);
    if (isNaN(d.getTime())) {
        const m = dateValue.toString().match(/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})/);
        if (m) {
            d = new Date(parseInt(m[1], 10), parseInt(m[2], 10) - 1, parseInt(m[3], 10));
        }
    }
    if (isNaN(d.getTime())) {
        return null;
    }

    const normalizedUnit = (unit || '').toString().toLowerCase();
    switch (normalizedUnit) {
        case 'd':
        case 'day':
        case 'days':
            d.setDate(d.getDate() + offset);
            break;
        case 'w':
        case 'week':
        case 'weeks':
            d.setDate(d.getDate() + offset * 7);
            break;
        case 'm':
        case 'month':
        case 'months':
            d.setMonth(d.getMonth() + offset);
            break;
        default:
            // unit 未指定 or 不正値の場合は日単位として扱う
            d.setDate(d.getDate() + offset);
            break;
    }

    return d;
}
