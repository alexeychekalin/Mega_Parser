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
        exec("cd /home/mega_parser; echo megaparser13 | sudo -S -k python3 /home/mega_parser/SberParserStart.py 2>&1", $output);

        if($output[0] == '[sudo] password for www-data:'){
            return response()->json([
                'answer' => 1,
            ]);
        }
        else{
            return response()->json([
                'output' => $output,
                'answer' => 0
            ]);
        }

    }
}
