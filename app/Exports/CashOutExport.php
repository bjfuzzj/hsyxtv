<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CashOutExport implements FromView
{
    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.cash_out', [
            'recordList' => $this->data
        ]);
    }
}
