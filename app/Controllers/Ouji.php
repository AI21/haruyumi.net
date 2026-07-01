<?php

namespace App\Controllers;
use App\Libraries\OujiLibrarie;

class Ouji extends BaseController
{
    protected $oujiLibrarie;

	public function __construct() {
        $this->dbOuji = \Config\Database::connect('ouji');
		$this->oujiLibrarie = new OujiLibrarie($this->dbOuji);
        helper('ouji');
	}

    public function index()
    {
        // 王子道場の解放予定情報取得
        $oujiSchedule = $this->oujiLibrarie->getOujiSchedule();

        // 祝日取得
        $arrayHoliday = getArrayHolidays(date("Y"));
        
		$data = [
            'title' => "王子道場　開場予定表",
            'headerCss' => array('ouji'),
            'footerJs' => array(),
            'oujiSchedule' => $oujiSchedule,
            'arrayHoliday' => $arrayHoliday,
		];
        
        echo view('ouji/header', $data);
        echo view('ouji/index');
        return view('ouji/footer');
    }
}
