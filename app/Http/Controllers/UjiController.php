<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UjiController extends Controller
{

    public function index()
    {
        $data = DB::Table('tbl_datauji')->get();

        return view('uji.index', ['data' => $data]);
    }

    public function tesTrigram($id)
    {

        $table_name = "tbl_trigram_test";
        $n_positif  = DB::Table($table_name)->where("sentimen", "POSITIF")->sum('frequensi');
        $n_negatif  = DB::Table($table_name)->where("sentimen", "NEGATIF")->sum('frequensi');

        $n_keyword_negatif = DB::Table($table_name)->where("sentimen", "NEGATIF")->count();
        $n_keyword_positif = DB::Table($table_name)->where("sentimen", "POSITIF")->count();

        $n_keywords = DB::Table($table_name)->count();

        /* ------------------------------------------------------------------------------------- */
        
        $data = DB::Table('tbl_datauji')->where('id', $id)->first();
        
        $string_uji = $data->tweet;
        $probabilitas_negatif = 0;
        $probabilitas_positif = 0;
        $ls_prob_negatif = [];
        $ls_prob_positif = [];

        $dt_trigrams = $this->getListTrigram($string_uji);

        $dt_freq_trigram = array_count_values($dt_trigrams);

        /**
         * mencari probabilitas keyword trigram
        */

        foreach ($dt_freq_trigram as $key => $value) {
            $probabilitas_negatif= $this->getProbabilitasTrigram($key, 'negatif');
            if($probabilitas_negatif == null){
                $probabilitas_negatif = $this->hitungProbabilitas(0, $n_negatif, $n_keywords);
            }
            $ls_prob_negatif[] = [
                "key" => $key,
                "frequensi" => $value,
                "probabilitas" => $probabilitas_negatif
            ];

            $probabilitas_positif =$this->getProbabilitasTrigram($key, 'positif');
            if($probabilitas_positif == null){
                $probabilitas_positif = $this->hitungProbabilitas(0, $n_positif, $n_keywords);
            }
            $ls_prob_positif[] = [
                "key" => $key,
                "frequensi" => $value,
                "probabilitas" => $probabilitas_positif
            ];
        }


        /**
         * menghitung nilai Vmap 
         */
        // $vmap_negatif = $Pxv * $Pv;
        $vpam['negatif'] = $this->getVmap($ls_prob_negatif, 0.5);
        $vpam['positif'] = $this->getVmap($ls_prob_positif, 0.5);
        
        if($vpam['negatif'] > $vpam['positif']){
            dd("negatif");
        }
        else {
            dd("positif");
        }
        // dd($ls_prob_negatif);
    }

    

    public function getListTrigram($data)
    {
        $ls_grams = [];
        
        $ls_words = explode(" ",$data);
        $n_words = count($ls_words);

        for ($i=0; $i < $n_words; $i++) { 
            $hasil = $this->Ngrams($ls_words[$i], 3);
            foreach ($hasil as $k => $v) {
                $ls_grams[] = $v;
            }
        }
        return $ls_grams;
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

    /**
     * getProbabilitas
     *
     * function untuk mengetahui probabilitas suatu keyword pada sentimen tertentu
     * function akan mengembalikan nilai probabilitas jika terdapat keyword yg cocok
     * dan jika tidak ada keyword yg cocok akan mengembalikan nilai 0
     *
     * @param String $keyword 
     * @param String $sentimen
     * @return float 
     **/
    public function getProbabilitasTrigram(String $keyword, String $sentimen)
    {
        $probabilitas = DB::Table('tbl_trigram_test')->where([
                            'sentimen' => $sentimen,
                            'keyword' => $keyword
                        ])
                        ->value('probabilitas');
        
        return $probabilitas;
    }

    /**
     * getVmap
     *
     * function untuk mencari nilai vmap
     * dengan mengimplementasikan rumus :
     * 
     *      Vmap = P(x#i|V#j) P(V#j)
     * 
     * dengan keterangan sebagai berikut : 
     * Vmap = semua kategori yang diujikan;
     * Vj = kategori(sentimen) tweet ['positif', 'negatif'];
     * P(Xi|Vj) = probabilitas Xi pada kategori Vj
     * P(Vj) = probabilitas dari Vj
     *
     * @param Array $data 
     * @param Float $pvj
     * @return double $vmap
     **/
    public function getVmap($data, $pvj)
    {
        $pxv = 1;
        // dd($data);
        foreach ($data as $key => $value) {
            $pxv *= $value['probabilitas']; 
        }

        $vmap = $pxv * $pvj;
        
        return $vmap;
    }

    public function Ngrams($word, $n){
        $ln_word = strlen($word);
        $ls_ngrams = [];
        for ($i=0; $i+($n-1) < $ln_word; $i++) { 

            $ngrams = "";
            // looping sebanyak n
            for ($j=$i; $j < $n+$i; $j++) { 
                $ngrams = $ngrams . $word[$j];
            }

            $ls_ngrams[] = $ngrams;
        }

        return $ls_ngrams;
    }

    public function tesTf($id)
    {
        $table_name = "tbl_tf";
        $n_positif  = DB::Table($table_name)->where("sentimen", "POSITIF")->sum('frequensi');
        $n_negatif  = DB::Table($table_name)->where("sentimen", "NEGATIF")->sum('frequensi');

        $n_keyword_negatif = DB::Table($table_name)->where("sentimen", "NEGATIF")->count();
        $n_keyword_positif = DB::Table($table_name)->where("sentimen", "POSITIF")->count();

        $n_keywords = DB::Table($table_name)->count();

        /* ------------------------------------------------------------------------------------- */
        
        $data = DB::Table('tbl_datauji')->where('id', $id)->first();
        
        $string_uji = $data->tweet;
        $probabilitas_negatif = 0;
        $probabilitas_positif = 0;
        $ls_prob_negatif = [];
        $ls_prob_positif = [];

        $dt_freq_tf = $this->getListTf($string_uji);

        /**
         * mencari probabilitas keyword trigram
        */

        foreach ($dt_freq_tf as $key => $value) {
            $probabilitas_negatif= $this->getProbabilitasTf($key, 'negatif');
            if($probabilitas_negatif == null){
                $probabilitas_negatif = $this->hitungProbabilitas(0, $n_negatif, $n_keywords);
            }
            $ls_prob_negatif[] = [
                "key" => $key,
                "frequensi" => $value,
                "probabilitas" => $probabilitas_negatif
            ];

            $probabilitas_positif =$this->getProbabilitasTf($key, 'positif');
            if($probabilitas_positif == null){
                $probabilitas_positif = $this->hitungProbabilitas(0, $n_positif, $n_keywords);
            }
            $ls_prob_positif[] = [
                "key" => $key,
                "frequensi" => $value,
                "probabilitas" => $probabilitas_positif
            ];
        }


        /**
         * menghitung nilai Vmap 
         */
        // $vmap_negatif = $Pxv * $Pv;
        $vpam['negatif'] = $this->getVmap($ls_prob_negatif, 0.5);
        $vpam['positif'] = $this->getVmap($ls_prob_positif, 0.5);
        
        if($vpam['negatif'] > $vpam['positif']){
            dd("negatif");
        }
        else {
            dd("positif");
        }
        // dd($ls_prob_negatif);   
    }

    public function getListTf($data)
    {
        $words = explode(" ", $data);
        $ln_words = count($words);
        $ls_words = [];
        for ($i=0; $i < $ln_words; $i++) { 
            $ls_words[] = $words[$i];
        }

        return array_count_values($ls_words);
    }

    /**
     * getProbabilitas
     *
     * function untuk mengetahui probabilitas suatu keyword pada sentimen tertentu
     * function akan mengembalikan nilai probabilitas jika terdapat keyword yg cocok
     * dan jika tidak ada keyword yg cocok akan mengembalikan nilai 0
     *
     * @param String $keyword 
     * @param String $sentimen
     * @return float 
     **/
    public function getProbabilitasTf(String $keyword, String $sentimen)
    {
        $probabilitas = DB::Table('tbl_tf')->where([
                            'sentimen' => $sentimen,
                            'keyword' => $keyword
                        ])
                        ->value('probabilitas');
        
        return $probabilitas;
    }

    
}
