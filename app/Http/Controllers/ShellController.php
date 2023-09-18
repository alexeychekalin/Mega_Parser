<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class ShellController extends Controller
{
    public function status(): \Illuminate\Http\JsonResponse
    {
        $output = shell_exec('systemctl is-active --quiet SberMegaParser && echo Service is running');
        $output2 = shell_exec('systemctl status SberMegaParser');

        return response()->json([
            'answer' => $output == '' ? 0 : 1,
            'output' => $output2
        ]);
    }

    public function run(): \Illuminate\Http\JsonResponse
    {
        $output2 = shell_exec('cd /home/mega_parser && python3 /home/mega_parser/SberParserStart.py');
        return response()->json([
            'output' => $output2
        ]);
    }
}
