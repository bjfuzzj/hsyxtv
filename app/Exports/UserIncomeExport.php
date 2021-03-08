<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class UserIncomeExport implements FromView
{
    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.incomeuser', [
            'users' => $this->data
        ]);
    }
}
