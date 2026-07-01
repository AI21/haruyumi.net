<?php

namespace App\Libraries;
use App\Models\CalendarModel;

class CalendarLibrarie
{
	private $calendarModel;

	public function __construct(){
		$this->calendarModel = model(CalendarModel::class);
	}

    /**
     * ドキュメント一覧情報取得
     * @return array ドキュメント一覧情報
     */
    public function get_document_list(): array
    {
        $result = array();
		$result = $this->calendarModel->get_document_list();
		
        return $result;
    }
}
