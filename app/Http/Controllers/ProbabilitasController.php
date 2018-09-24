<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProbabilitasController extends Controller
{
    
    public function trigram()
    {
        $table_name = "tbl_trigram_test";
        $n_positif  = DB::Table($table_name)->where("sentimen", "POSITIF")->sum('frequensi');
        $n_negatif  = DB::Table($table_name)->where("sentimen", "NEGATIF")->sum('frequensi');

        $n_keyword_negatif = DB::Table($table_name)->where("sentimen", "NEGATIF")->count();
        $n_keyword_positif = DB::Table($table_name)->where("sentimen", "POSITIF")->count();

        $n_keywords = DB::Table($table_name)->count();

        /**
         * Menampilkan data 
        */
        echo "Jumlah Keyword : " . $n_keywords . "<br />";
        echo "Jumlah Keyword Negatif : " . $n_keyword_negatif . "<br />";
        echo "Jumlah Keyword Positif : " . $n_keyword_positif . "<br />";
        echo "Jumlah Frequensi(n) Negatif : " . $n_positif . "<br />";
        echo "Jumlah Frequensi(n) Positif : " . $n_negatif . "<br />";

        $datas = [];
        $ls_ngrams = DB::Table($table_name)->get();
        foreach ($ls_ngrams as $key => $value) {
            $n = '';
            $kk = '';
            if ($value->sentimen == "NEGATIF") {
                $n = $n_negatif;
                $kk = $n_keyword_negatif;
            }
            else {
                $n = $n_positif;
                $kk = $n_keyword_positif;
            }
            $datas[] = [
                'keyword' => $value->keyword,
                'frequensi' => $value->frequensi,
                'probabilitas_sentimen' => $this->hitungProbabilitas($value->frequensi, $n, $kk),
                'probabilitas' => $this->hitungProbabilitas($value->frequensi, $n, $n_keywords),
                'sentimen' => $value->sentimen
            ];
        }

        dd($datas);
    }

    public function tf()
    {
        $table_name = "tbl_tf";
        $n_positif  = DB::Table($table_name)->where("sentimen", "POSITIF")->sum('frequensi');
        $n_negatif  = DB::Table($table_name)->where("sentimen", "NEGATIF")->sum('frequensi');

        $n_keyword_negatif = DB::Table($table_name)->where("sentimen", "NEGATIF")->count();
        $n_keyword_positif = DB::Table($table_name)->where("sentimen", "POSITIF")->count();

        $n_keywords = DB::Table($table_name)->count();

        /**
         * Menampilkan data 
        */
        echo "Jumlah Keyword : " . $n_keywords . "<br />";
        echo "Jumlah Keyword Negatif : " . $n_keyword_negatif . "<br />";
        echo "Jumlah Keyword Positif : " . $n_keyword_positif . "<br />";
        echo "Jumlah Frequensi(n) Negatif : " . $n_positif . "<br />";
        echo "Jumlah Frequensi(n) Positif : " . $n_negatif . "<br />";

        $datas = [];
        $ls_ngrams = DB::Table($table_name)->get();
        foreach ($ls_ngrams as $key => $value) {
            $n = '';
            $kk = '';
            if ($value->sentimen == "NEGATIF") {
                $n = $n_negatif;
                $kk = $n_keyword_negatif;
            }
            else {
                $n = $n_positif;
                $kk = $n_keyword_positif;
            }
            $datas[] = [
                'keyword' => $value->keyword,
                'frequensi' => $value->frequensi,
                'probabilitas_sentimen' => $this->hitungProbabilitas($value->frequensi, $n, $kk),
                'probabilitas' => $this->hitungProbabilitas($value->frequensi, $n, $n_keywords),
                'sentimen' => $value->sentimen
            ];
        }

        dd($datas);
    }

    
    /**
     * Update nilai probabilitas trigram
     */
    public function saveTrigram()
    {
        $table_name = "tbl_trigram_test";
        $n_positif  = DB::Table($table_name)->where("sentimen", "POSITIF")->sum('frequensi');
        $n_negatif  = DB::Table($table_name)->where("sentimen", "NEGATIF")->sum('frequensi');

        $n_keyword_negatif = DB::Table($table_name)->where("sentimen", "NEGATIF")->count();
        $n_keyword_positif = DB::Table($table_name)->where("sentimen", "POSITIF")->count();

        $n_keywords = DB::Table($table_name)->count();

        /**
         * Menampilkan data 
        */
        // echo "Jumlah Keyword : " . $n_keywords . "<br />";
        // echo "Jumlah Keyword Negatif : " . $n_keyword_negatif . "<br />";
        // echo "Jumlah Keyword Positif : " . $n_keyword_positif . "<br />";
        // echo "Jumlah Frequensi(n) Negatif : " . $n_positif . "<br />";
        // echo "Jumlah Frequensi(n) Positif : " . $n_negatif . "<br />";

        $datas = [];
        $ls_ngrams = DB::Table($table_name)->get();
        foreach ($ls_ngrams as $key => $value) {
            $n = '';
            $kk = '';
            if ($value->sentimen == "NEGATIF") {
                $n = $n_negatif;
                $kk = $n_keyword_negatif;
            }
            else {
                $n = $n_positif;
                $kk = $n_keyword_positif;
            }
            $resp = DB::Table($table_name)->where('id', $value->id)->update([
                    'probabilitas_sentimen' => $this->hitungProbabilitas($value->frequensi, $n, $kk),
                    'probabilitas' => $this->hitungProbabilitas($value->frequensi, $n, $n_keywords),
                ]);
            if($resp) { echo "berhasil update". $value->id . "<br />"; }
        }

    }


    /**
     * Update nilai probabilitas tf
     */
    public function saveTf()
    {
        $table_name = "tbl_tf";
        $n_positif  = DB::Table($table_name)->where("sentimen", "POSITIF")->sum('frequensi');
        $n_negatif  = DB::Table($table_name)->where("sentimen", "NEGATIF")->sum('frequensi');

        $n_keyword_negatif = DB::Table($table_name)->where("sentimen", "NEGATIF")->count();
        $n_keyword_positif = DB::Table($table_name)->where("sentimen", "POSITIF")->count();

        $n_keywords = DB::Table($table_name)->count();

        /**
         * Menampilkan data 
        */
        // echo "Jumlah Keyword : " . $n_keywords . "<br />";
        // echo "Jumlah Keyword Negatif : " . $n_keyword_negatif . "<br />";
        // echo "Jumlah Keyword Positif : " . $n_keyword_positif . "<br />";
        // echo "Jumlah Frequensi(n) Negatif : " . $n_positif . "<br />";
        // echo "Jumlah Frequensi(n) Positif : " . $n_negatif . "<br />";

        $datas = [];
        $ls_ngrams = DB::Table($table_name)->get();
        foreach ($ls_ngrams as $key => $value) {
            $n = '';
            $kk = '';
            if ($value->sentimen == "NEGATIF") {
                $n = $n_negatif;
                $kk = $n_keyword_negatif;
            }
            else {
                $n = $n_positif;
                $kk = $n_keyword_positif;
            }
            
            $resp = DB::Table($table_name)->where('id', $value->id)->update([
                    'probabilitas_sentimen' => $this->hitungProbabilitas($value->frequensi, $n, $kk),
                    'probabilitas' => $this->hitungProbabilitas($value->frequensi, $n, $n_keywords),
                ]);
            if($resp) { echo "berhasil update". $value->id . "<br />"; }
        }

    }

    public function hitungProbabilitas($nk, $n, $kosakata)
    {
        /*
        nk  = Jumlah frekuensi kemunculan setiap keyword
        n = total frekuensi(nk) dari setiap kategori(sentimen)
        kosakata = Jumlah semua keyword dari semua kategori(sentimen)
        $prob = $nk + 1 / $n + |$kosakata|;

        */

        $probabilitas = ($nk + 1) / ($n + $kosakata);
        return $probabilitas;
    }
}
