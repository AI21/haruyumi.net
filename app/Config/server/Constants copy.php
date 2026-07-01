<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
 |
 | This defines the default Namespace that is used throughout
 | CodeIgniter to refer to the Application directory. Change
 | this constant to change the namespace that all application
 | classes should use.
 |
 | NOTE: changing this will require manually modifying the
 | existing namespaces of App\* namespaced-classes.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | The path that Composer's autoload file is expected to live. By default,
 | the vendor folder is in the Root directory, but you can customize that here.
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Provide simple ways to work with the myriad of PHP functions that
 | require information to be in seconds.
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2_592_000);
defined('YEAR')   || define('YEAR', 31_536_000);
defined('DECADE') || define('DECADE', 315_360_000);

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
 |
 | Used to indicate the conditions under which the script is exit()ing.
 | While there is no universal standard for error codes, there are some
 | broad conventions.  Three such conventions are mentioned below, for
 | those who wish to make use of them.  The CodeIgniter defaults were
 | chosen for the least overlap with these conventions, while still
 | leaving room for others to be defined in future versions and user
 | applications.
 |
 | The three main conventions used for determining exit status codes
 | are as follows:
 |
 |    Standard C/C++ Library (stdlibc):
 |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
 |       (This link also contains other GNU-specific conventions)
 |    BSD sysexits.h:
 |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
 |    Bash scripting:
 |       http://tldp.org/LDP/abs/html/exitcodes.html
 |
 */
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0);        // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1);          // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3);         // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4);   // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5);  // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7);     // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8);       // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9);      // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125);    // highest automatically-assigned error code

/**
 * @deprecated 王子道場システム用
 */
define('SCHEJULE_MAX', 14);		// スケジュール表示の日数
// 管理系
define('LOGIN_LIMIT_DAY', 14);	// ログイン可能日数
define('OPEN_SET_LOGIN', 'open_set_login.php');
define('SCHEJULE_SET_LOGIN', 'schejule_set_login.php');
define('ADMIN_PW_CHANGE', 'admin_pw_change.php');
define('MAIL_SET', 'mail_set.php');
define('SET_TIME_MORNING', array('9:00', '9:30','10:00', '10:30','11:00'));	// 開場時間選択項目（午前）
define('SET_TIME_AFTERNOON', array('13:00', '13:30','14:00', '14:30','15:00'));	// 開場時間選択項目（午後）
define('SET_TIME_NIGHT', array('17:00', '17:30','18:00', '18:30','19:00'));	// 開場時間選択項目（夜間）
define('SETTING_NG_TEXT', '設定不可');
define('SETTING_DIFF_HOURS', 1);	// 開場時間が未経過の場合、設定可能な前時間
define('GOOGLE_CALENDAR_API_KEY', 'AIzaSyDXR_EQikaIYu-Pg-0YlQxLVMPN6aQTYlQ');
define('WEEK_JP', ['日', '月', '火', '水', '木', '金', '土']);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_LOW instead.
 */
define('EVENT_PRIORITY_LOW', 200);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_NORMAL instead.
 */
define('EVENT_PRIORITY_NORMAL', 100);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_HIGH instead.
 */
define('EVENT_PRIORITY_HIGH', 10);

define('SITE_ROOT', '/');
define('SITE_URL', 'https://haruyumi.net/');

define('APP_CONTROLLER', '\\App\\Controllers\\');

define('SEND_MAIL_FROM', 'teras@haruyumi.net');
define('SEND_MAIL_TO', 'teras@haruyumi.net');
define('SEND_MAIL_FROM_NAME', '春弓テラス');

// 会員リストPDFファイル
define('KASUGAI_MEMBER_LIST_PATH', 'assets/document/kasugai/member/');
define('KASUGAI_MEMBER_LIST_FILE', 'member.pdf');

define('FLG_OFF', '0');
define('FLG_ON', '1');

define('FLG_TRUE', true);
define('FLG_FALSE', false);

define('DB_FLG_OFF', '0');
define('DB_FLG_ON', '1');

define('FORM_CHECKBOX_TRUE', 'true');
define('FORM_CHECKBOX_FALSE', 'false');

define('LANG_JP', 'jp');
define('LANG_EN', 'en');

define('MODE_REGIST', 'regist');        // 登録
define('MODE_REVISION', 'revision');    // 更新
define('MODE_DELETE', 'delete');        // 削除

define('GENDER_ALL', 0);            // 性別：問わず
define('GENDER_MALE', 1);           // 性別：男性
define('GENDER_FEMALE', 2);         // 性別：女性

define('PERIOD_ID_BEFORE', 0);      // 期間前
define('PERIOD_ID_NOW', 1);         // 期間中
define('PERIOD_ID_END', 2);         // 終了
define('PERIOD_ID_UNDEFINED', 9);   // 未定

define('PERIOD_TEXT_BEFORE', '前');
define('PERIOD_TEXT_NOW', '中');
define('PERIOD_TEXT_END', '終了');
define('PERIOD_TEXT_UNDEFINED', '未定');

define('DATA_MIRAI', 'mirai');
define('DATA_KAKO', 'kako');
define('DATA_OFFICER_FLG', 'officer_flg');

define('TAB_NAME_KASUGAI', 'kasugai');
define('TAB_NAME_ZENKYUREN', 'zenkyuren');
define('TAB_NAME_AIKYUREN', 'aikyuren');
define('TAB_NAME_CHUOU', 'chuou');
define('TAB_NAME_RENGO', 'rengo');
define('TAB_NAME_CHIHO', 'chiho');
define('TAB_NAME_TOKAIRENGO', 'tokairengo');
define('TAB_NAME_OTHER', 'other');
define('TAB_NAME_USEGYM', 'usegym');

define('CONTROLLER_NAME_HOME', 'home');
define('CONTROLLER_NAME_DOCUMENT', 'document');
define('CONTROLLER_NAME_SHINSA', 'shinsa');
define('CONTROLLER_NAME_TAIKAI', 'taikai');
define('CONTROLLER_NAME_SEMINAR', 'seminar');
define('CONTROLLER_NAME_KYOKAI', 'kyokai');
define('CONTROLLER_NAME_MEMBER', 'member');
define('CONTROLLER_NAME_CALENDAR', 'calendar');
define('CONTROLLER_NAME_LOGIN_CHANGE', 'login/change');

define('MENU_ID_MAIN', '0');
define('MENU_ID_SHINSA', '1');
define('MENU_ID_TAIKAI', '2');
define('MENU_ID_SEMINAR', '3');
define('MENU_ID_KYOKAI', '4');
define('MENU_ID_CALENDAR', '5');
define('MENU_ID_DOCUMENT', '6');
define('MENU_ID_MEMBER', '7');
define('MENU_ID_LOGIN', '8');

define('CATEGORY_ID_KYOKAI', '1');
define('CATEGORY_ID_ZENKYUREN', '2');
define('CATEGORY_ID_AIKYUREN', '3');
define('CATEGORY_ID_TOKAIRENGO', '4');
define('CATEGORY_ID_SHINSA_CHUOU', '5');
define('CATEGORY_ID_SHINSA_RENGO', '6');
define('CATEGORY_ID_SHINSA_CHIHO', '7');
define('CATEGORY_ID_OTHER', '8');
define('CATEGORY_ID_RELATION_SHINSA', '9');
define('CATEGORY_ID_RELATION_TAIKAI', '10');
define('CATEGORY_ID_MONTHLY', '11');
define('CATEGORY_ID_KYOSHITU', '12');
define('CATEGORY_ID_USEGYM', '13');

define('NOTICE_CATEGORY_ID_KASUGAI', '1');  // 春日井弓道協会
define('NOTICE_CATEGORY_ID_SHINSA', '2');   // 審査
define('NOTICE_CATEGORY_ID_TAIKAI', '3');   // 大会
define('NOTICE_CATEGORY_ID_SEMINAR', '4');  // 講習会
define('NOTICE_CATEGORY_ID_TRAINING', '5'); // 研修会
define('NOTICE_CATEGORY_ID_OTHER', '6');    // その他

define('KYOKAI_EVENT_ID_OTHER', '0');           // その他
define('KYOKAI_EVENT_ID_GETUREI', '1');         // 月例会
define('KYOKAI_EVENT_ID_KENSYU', '2');          // 参段以下研修会
define('KYOKAI_EVENT_ID_KYOSHITU', '3');        // 弓道教室
define('KYOKAI_EVENT_ID_SOUKAI', '4');          // 定期総会
define('KYOKAI_EVENT_ID_YAKUIN', '5');          // 役員会
define('KYOKAI_EVENT_ID_KAIKEI', '6');          // 会計監査
define('KYOKAI_EVENT_ID_MARASON', '7');         // 新春春日井マラソン大会
define('KYOKAI_EVENT_ID_SENSYUKEN', '8');       // 春日井市弓道選手権大会
define('KYOKAI_EVENT_ID_SHITYOUHAI', '9');      // 春日井市長杯弓道大会
define('KYOKAI_EVENT_ID_SHIMIN', '10');         // 春日井市民大会
define('KYOKAI_EVENT_ID_HIGH_SCHOOL', '11');    // 春日井市高校団体選手権
define('KYOKAI_EVENT_ID_NEWYEAR', '12');        // 新年射会

define('SHINSA_INFORMATION', 'information');

define('SHINSA_CLASS_ID_CHUOU', '1');
define('SHINSA_CLASS_ID_RENGO', '2');
define('SHINSA_CLASS_ID_CHIHO', '3');
define('SHINSA_CLASS_ID_VIDEO', '4');

define('SHINSA_AREA_GROUP_TEIKI', '1');       // 定期審査
define('SHINSA_AREA_GROUP_CHUOU', '2');       // 中央審査
define('SHINSA_AREA_GROUP_RENGO', '3');       // 連合審査
define('SHINSA_AREA_GROUP_CHIHO', '4');       // 地方審査

// 審査結果
define('SHINSA_RESULT_FLG_PASS', '1');      // 合格
define('SHINSA_RESULT_FLG_FAIL', '2');      // 不合格
define('SHINSA_RESULT_FLG_ABSTAIN', '9');   // 棄権

define('SHINSA_RESULT_VIEW_PASS', '合格');
define('SHINSA_RESULT_VIEW_FAIL', '不合格');
define('SHINSA_RESULT_VIEW_ABSTAIN', '棄権');

// 段位ID
define('GRADE_ID_5DAN', 6);     // 五段

define('FISCAL_YEAR_ID', 25);

define('KASUGAI_KYOKAI_NAME', '春弓-haruyumi-テラス');
define('KASUGAI_KYOKAI_NAME_SHORT', '春弓テラス');
define('SYSTEM_NAME', '春弓協掲示板');

define('URL_AIKYUREN', 'http://www.aikyuren.com/');
define('URL_ZENKYUREN', 'https://www.kyudo.jp/');

define('VIEW_MIKAISAI', '未開催');
define('VIEW_END', '終了');

define('ABORT_VIEW_ON', '（中止）');

define('REQUEST_JOIN', 'join');
define('REQUEST_CANCEL', 'cancel');

define('REQUEST_NAME_JOIN', '申込');
define('REQUEST_NAME_CANCEL', 'キャンセル');

define('DATE_FORMAT_YYMMDD_NENGO', 'nengommdd');
define('DATE_FORMAT_YYMMDD', 'yyyymmdd');
define('DATE_FORMAT_YMD', 'ymd');
define('DATE_FORMAT_YM', 'ym');
define('DATE_FORMAT_MMDD', 'mmdd');
define('DATE_FORMAT_DD', 'dd');
define('DATE_FORMAT_MD', 'md');
define('DATE_FORMAT_D', 'd');

define('TIME_FORMAT_HI', 'Hi');
define('TIME_FORMAT_HIS', 'His');
define('TIME_FORMAT_GI', 'Gi');
define('TIME_FORMAT_GIS', 'Gis');

define('VALIDATION_LOGIN_PW_MIN', 4);    // パスワード最小文字数
define('VALIDATION_LOGIN_PW_MAX', 16);    // パスワード最大文字数

define('UPLOAD_FILE_NUM', 10);              // 添付ファイル数
define('UPLOAD_FILE_DIR', 'upload');
define('UPLOAD_FILE_MAX_SIZE', 1024 * 40);  // MB単位

define('UPLOAD_FILE_EXT_ICON_PDF', 'assets/img/icon/pdf_32.png');
define('UPLOAD_FILE_EXT_ICON_EXCEL', 'assets/img/icon/excel_32.png');
define('UPLOAD_FILE_EXT_ICON_WORD', 'assets/img/icon/word_32.png');
define('UPLOAD_FILE_EXT_ICON_JPG', 'assets/img/icon/jpg_32.png');
define('UPLOAD_FILE_EXT_ICON_ZIP', 'assets/img/icon/zip_32.png');
define('UPLOAD_FILE_EXT_ICON_TXT', 'assets/img/icon/txt_32.png');

define('MEMBER_LIST_FILE_DIR', 'assets/document/kasugai/member');
define('MEMBER_LIST_FILE_BACKUP_DIR', 'assets/document/kasugai/member/backup');
define('MEMBER_LIST_FILE_NAME', 'member');
define('MEMBER_LIST_FILE_EXT', '.pdf');

define('DOCUMENT_MAIN_FILE_DIR', 'document');
define('DOCUMENT_USEGYM_FILE_DIR', 'usegym');

// 協会役員
define('KYOKAI_OFFICER_ID_KASUGAI_kAICHO', '3');     // 春日井市弓道協会会長
define('KYOKAI_OFFICER_ID_OFFICE_WORK', '5');       // 事務担当
define('KYOKAI_OFFICER_ID_AIKYU_OFFICER', '7');     // 愛弓連春日井弓道会団体長（役員派遣委託）
define('KYOKAI_OFFICER_ID_AIKYU_SEMINAR', '8');     // 愛弓連春日井弓道会団体長（講習会）
define('KYOKAI_OFFICER_ID_AIKYU_TAIKAI', '9');      // 愛弓連春日井弓道会団体長（大会）
define('KYOKAI_OFFICER_ID_SHINSA_CHUOU', '11');     // 審査担当（中央）
define('KYOKAI_OFFICER_ID_SHINSA_RENGO', '12');     // 審査担当（連合）
define('KYOKAI_OFFICER_ID_SHINSA_CHIHO', '13');     // 審査担当（地方）
define('KYOKAI_OFFICER_ID_CONSULTATION', '15');     // 幹事（相談窓口）
define('KYOKAI_OFFICER_ID_TAIKAI_CHAMP', '20');     // 競技運営部（選手権）
define('KYOKAI_OFFICER_ID_TAIKAI_MAYOR', '21');     // 競技運営部（市長杯）
define('KYOKAI_OFFICER_ID_TAIKAI_SHIMIN', '22');    // 競技運営部（市民大会）
define('KYOKAI_OFFICER_ID_TAIKAI_NEWYEAR', '23');   // 競技運営部（新年射会）

define('KYOKAI_OFFICER_LEVEL_BOSS', '1');           // 主担当幹事
define('KYOKAI_OFFICER_LEVEL_SUB', '2');            // 副担当幹事
define('KYOKAI_OFFICER_LEVEL_OTHER', '3');          // 一般幹事

define('ORGANIZER_LEVEL_MAIN', '1');                // 主幹事
define('ORGANIZER_LEVEL_OTHER', '0');               // 幹事

// 登録初期値
define('MEMBER_DATA_DEFAULT_KASUGAI_REGIST_FLG', '1');       // 春日井弓道協会登録フラグ
define('MEMBER_DATA_DEFAULT_RENMEI_ADJOURNING_FLG', '0');    // 全弓連休会中フラグ
define('MEMBER_DATA_DEFAULT_AICHI_RENMEI_REGIST_FLG', '0');  // 愛弓連登録フラグ
// define('MEMBER_DATA_DEFAULT_RENMEI_ID', 'NULL');             // 全弓連ID
define('MEMBER_DATA_DEFAULT_NOTICE_FLG', '0');               // お知らせ投稿可能フラグ
define('MEMBER_DATA_DEFAULT_MEMBER_ADMIN_FLG', '0');         // 会員管理者フラグ
// define('MEMBER_DATA_DEFAULT_LOGINID', 'NULL');               // ログインID
define('MEMBER_DATA_DEFAULT_PASSWD', 'k-kyudo-2025');        // パスワード