<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NGramController extends Controller
{


    public function index()
    {
        $sentimen = "POSITIF";
        $tweet = DB::Table('tbl_hasil_stemming')->where('sentimen', $sentimen)->get();

        $hasil = [];
        $ls_ngram_word = [];  /* list array keseluruhan word */
        foreach($tweet as $i_doc => $t) {
            echo "Kalimat [" . ($i_doc+1) . "] " . $t->tweet . "<br />";

            $ls_kata = explode(" ", $t->tweet);
            $n_kata = count($ls_kata);
            // $hasil = [];
            $n = 0;
            for ($i=0; $i < $n_kata; $i++) { 
                $n++;

                echo "[" . $n . "] " . $ls_kata[$i]. "<br />";
                
                $strlength = strlen($ls_kata[$i]);
                $hasil = $this->Ngrams($ls_kata[$i], 3);
                foreach ($hasil as $key => $value) {
                    /* simpan setian word hasil ngram kedalam ls_array */
                    $ls_ngram_word[] = $value;

                    echo "------[" . ($key + 1) . "] : " . $value . "<br />";

                    $hasil[] = [
                        // "id" => "",
                        "key" => $value,
                        "kata" => $ls_kata[$i],
                        "id_tweet" => $t->id,
                    ];

                }
            }
            echo "<br />";
        }   

        $ls_ngram_frequensi = array_count_values($ls_ngram_word);
        // $resp = $this->store($ls_ngram_frequensi, $sentimen); // uncomment untuk menyimpan data ke database
        dd($ls_ngram_frequensi);
    }

    public function store($ls_ngram, $sentimen)
    {
        $dt = [];
        foreach ($ls_ngram as $key => $value) {
            $dt[] = [
                "keyword" => $key,
                "frequensi" => $value,
                "probabilitas_sentimen" => 0,
                "probabilitas" => 0,
                "sentimen" => $sentimen
            ];
        }

        $res = DB::Table('tbl_trigram_test')->insert($dt);
    }

    
    public function saveNgram()
    {
        $tweet = DB::Table('tbl_hasil_stemming')->get();

        $ngram_data = [];

        foreach($tweet as $key => $t) {

            $ls_kata = explode(" ", $t->tweet);
            $n_kata = count($ls_kata);
            // $hasil = [];
            $n = 0;
            for ($i=0; $i < $n_kata; $i++) { 
                $n++;
                
                $strlength = strlen($ls_kata[$i]);
                $hasil = $this->Ngrams($ls_kata[$i], 3);
                foreach ($hasil as $key => $value) {

                    // $ngram_data[] = [
                    //     // "id" => "",
                    //     "key" => $value,
                    //     "kata" => $ls_kata[$i],
                    //     "id_tweet" => $t->id,
                    // ];

                    DB::Table('tbl_trigram_test')->insert(
                        [
                            // "id" => "",
                            "trigram" => $value,
                            "id_tweet" => $t->id,
                        ]
                    );
                }
                
            }

        }
        // $collection = collect($ngram_data);

            
        

        // $resp = DB::Table('tbl_trigram_test')->insert($ngram_data);
        // return $resp;
    }
    
    

    function Ngrams($word, $n){
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

}
