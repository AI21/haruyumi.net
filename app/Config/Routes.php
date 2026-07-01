<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// 一般系
$routes->get('/', 'Home::index');
$routes->get('home/', 'Home::index');
$routes->get('home/index', 'Home::index');
$routes->get('login/', 'Login::index');
$routes->get('login/change', 'Login::change');
$routes->get('shinsa/', 'Shinsa::index');
$routes->get('shinsa/(:num)', 'Shinsa::index/$1');
$routes->get('shinsa/detail/(:num)', 'Shinsa::detail/$1');
$routes->get('taikai/', 'Taikai::index');
$routes->get('taikai/(:num)', 'Taikai::index/$1');
$routes->get('taikai/detail/(:num)', 'Taikai::detail/$1');
$routes->get('seminar/', 'Seminar::index');
$routes->get('seminar/(:num)', 'Seminar::index/$1');
$routes->get('seminar/detail/(:num)', 'Seminar::detail/$1');
$routes->get('kyokai/', 'Kyokai::index');
$routes->get('kyokai/(:num)', 'Kyokai::index/$1');
$routes->get('kyokai/detail/(:num)', 'Kyokai::detail/$1');
$routes->get('calendar/', 'Calendar::index');
$routes->get('member/', 'Member::index');
$routes->get('member/(:num)', 'Member::index/$1');
$routes->get('document/', 'Document::index');

// Ajax系
$routes->post('home/get_notice_detail', 'Home::ajax_get_notice_detail');
$routes->post('login/login_process', 'Login::ajax_login_process');
$routes->post('login/login_change_check', 'Login::ajax_login_change_check');
$routes->post('login/login_change_process', 'Login::ajax_login_change_process');
$routes->post('shinsa/shinsa_request', 'Shinsa::ajax_shinsa_request');
$routes->post('shinsa/shinsa_result_report', 'Shinsa::ajax_shinsa_result_report');
$routes->post('taikai/taikai_request', 'Taikai::ajax_taikai_request');
$routes->post('kyokai/event_request', 'Kyokai::ajax_event_request');

/**
 * 管理系
 * @var RouteCollection $routes
 */
// 管理系：共通（お知らせ）
$routes->get('admin/mail_test', 'Admin\AdminNotice::mail_test');
$routes->get('admin/notice_regist', 'Admin\AdminNotice::notice_regist');
$routes->get('admin/notice_regist/(:num)', 'Admin\AdminNotice::notice_regist/$1');
$routes->get('admin/notice_regist/(:num)/(:num)', 'Admin\AdminNotice::notice_regist/$1/$2');
$routes->post('admin/notice_revision', 'Admin\AdminNotice::notice_revision');
$routes->post('admin/notice_regist_conf', 'Admin\AdminNotice::ajax_notice_regist_conf');
$routes->post('admin/notice_regist_proc', 'Admin\AdminNotice::ajax_notice_regist_proc');
$routes->post('admin/delete_notice_info', 'Admin\AdminNotice::ajax_delete_notice_info');
$routes->post('admin/delete_notice_document', 'Admin\AdminNotice::ajax_delete_notice_document');
$routes->post('admin/unexpired_event_list', 'Admin\AdminNotice::ajax_unexpired_event_list');
// 管理系：審査
$routes->post('admin/shinsa_regist', 'Admin\AdminShinsa::shinsa_regist');
$routes->post('admin/shinsa_revision', 'Admin\AdminShinsa::shinsa_revision');
$routes->post('admin/shinsa_regist_conf', 'Admin\AdminShinsa::ajax_shinsa_regist_conf');
$routes->post('admin/shinsa_regist_proc', 'Admin\AdminShinsa::ajax_shinsa_regist_proc');
$routes->get('admin/notice_regist_shinsa_promotion/(:num)', 'Admin\AdminShinsa::notice_regist_shinsa_promotion/$1');
$routes->post('admin/shinsa_result_report_proxy', 'Admin\AdminShinsa::ajax_shinsa_result_report_proxy');
$routes->post('admin/get_pass_grade_group', 'Admin\AdminShinsa::ajax_get_pass_grade_group');
$routes->post('admin/rankup_result', 'Admin\AdminShinsa::ajax_rankup_result');
$routes->post('admin/get_shinsa_kaijo_list', 'Admin\AdminShinsa::ajax_get_shinsa_kaijo_list');
$routes->post('admin/shinsa_add_member_proxy', 'Admin\AdminShinsa::ajax_shinsa_add_member_proxy');
$routes->post('admin/shinsa_target_member_list', 'Admin\AdminShinsa::ajax_get_shinsa_target_member_list');
$routes->post('admin/shinsa_cancel_member_proxy', 'Admin\AdminShinsa::ajax_shinsa_cancel_member_proxy');
// 管理系：大会
$routes->post('admin/taikai_revision', 'Admin\AdminTaikai::taikai_revision');
$routes->post('admin/taikai_regist_conf', 'Admin\AdminTaikai::ajax_taikai_regist_conf');
$routes->post('admin/taikai_regist_proc', 'Admin\AdminTaikai::ajax_taikai_regist_proc');
$routes->post('admin/delete_taikai_document', 'Admin\AdminTaikai::ajax_delete_taikai_document');
$routes->post('admin/taikai_add_member_proxy', 'Admin\AdminTaikai::ajax_taikai_add_member_proxy');
$routes->post('admin/taikai_cancel_member_proxy', 'Admin\AdminTaikai::ajax_taikai_cancel_member_proxy');
$routes->get('admin/taikai_member_csv_download/(:num)', 'Admin\AdminTaikai::ajax_taikai_member_csv_download/$1');
// 管理系：協会行事
$routes->post('admin/event_revision', 'Admin\AdminEvent::event_revision');
// 管理系：資料
$routes->post('admin/document_file_regist', 'Admin\AdminDocument::document_file_regist');
$routes->post('admin/document_file_regist_conf', 'Admin\AdminDocument::ajax_document_file_regist_conf');
$routes->post('admin/document_file_regist_proc', 'Admin\AdminDocument::ajax_document_file_regist_proc');
// 管理系：会員管理
$routes->get('admin/member_list_file_regist', 'Admin\AdminMember::member_list_file_regist');
$routes->post('admin/member_regist', 'Admin\AdminMember::member_regist');
$routes->post('admin/member_revision', 'Admin\AdminMember::member_revision');
$routes->post('admin/member_list_file_conf', 'Admin\AdminMember::ajax_member_list_file_conf');
$routes->post('admin/member_list_file_proc', 'Admin\AdminMember::ajax_member_list_file_proc');
$routes->post('admin/member_regist_conf', 'Admin\AdminMember::ajax_member_regist_conf');
$routes->post('admin/member_regist_proc', 'Admin\AdminMember::ajax_member_regist_proc');

// マスタ系
$routes->get('master/', 'Master\MasterIndex::index');
$routes->get('master/index', 'Master\MasterIndex::index');
$routes->get('master/kaijo', 'Master\MasterKaijo::index');
$routes->get('master/kaijo/(:num)', 'Master\MasterKaijo::index/$1');

// 王子道場系
$routes->get('ouji/', 'Ouji::index');