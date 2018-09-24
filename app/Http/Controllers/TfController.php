<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TfController extends Controller
{
    public function index()
    {
        $sentimen = "NEGATIF";
        $ls_keywords = [];
        $datas = DB::Table('tbl_datalatih')->where('sentimen', $sentimen)->get();

        foreach ($datas as $key => $value) {
            $words = explode(" ", $value->tweet);
            $ln_words = count($words);

            for ($i=0; $i < $ln_words; $i++) { 
                $ls_keywords[] = $words[$i];
            }

        }

        $freq_keywords = array_count_values($ls_keywords);
        dd($freq_keywords);
    }

    public function save()
    {
        $sentimen = "POSITIF";
        $ls_keywords = [];
        $datas = DB::Table('tbl_datalatih')->where('sentimen', $sentimen)->get();

        foreach ($datas as $key => $value) {
            $words = explode(" ", $value->tweet);
            $ln_words = count($words);

            for ($i=0; $i < $ln_words; $i++) { 
                $ls_keywords[] = $words[$i];
            }

        }

        $freq_keywords = array_count_values($ls_keywords);
        $resp = $this->store($freq_keywords, $sentimen);
        dd($resp);
        // dd($freq_keywords);
    }

    public function store($ls_tf, $sentimen)
    {
        $dt = [];
        foreach ($ls_tf as $key => $value) {
            $dt[] = [
                "keyword" => $key,
                "frequensi" => $value,
                "probabilitas_sentimen" => 0,
                "probabilitas" => 0,
                "sentimen" => $sentimen
            ];
        }

        $res = DB::Table('tbl_tf')->insert($dt);
    }
}
