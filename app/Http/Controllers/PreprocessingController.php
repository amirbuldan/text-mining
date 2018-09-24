<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

/**
 * Preprocessing class
 * 
 * melakukan proses preprocessing
 * 1. Case Folding (termasuk cleansing)
 * 2. Tokenizing
 * 3. Filtering 
 *      normalisasi -> mengubah kata tidak baku ke kata baku
 *      stop word (function filtering) -> menghilangkan stop word
 * 4. Stemming
 */

class PreprocessingController extends Controller
{

    public function show()
    {
        $data = DB::Table('twitter')->limit(20)->get();
        // echo "<pre>";
        // var_dump($data);
        // echo "</pre>";
        foreach($data as $dt){
            echo "<strong>Tweet Asli : </strong>".$dt->tweet . "<br />";
            echo "<strong>Case Folding : </strong>". $this->caseFolding($dt->tweet) . "<br />";
            echo "<strong>Tokenizing : </strong><br />" ;
                $arr_token = $this->tokenizing($this->caseFolding($dt->tweet));
                $no = 0;
                for ($i=0; $i < count($arr_token); $i++) { 
                    $no++;
                    echo $no. ". " . $arr_token[$i] . "<br />";
                }

            $cln = $this->normalisasi($this->caseFolding($dt->tweet));
            echo "<strong>hasil normalisasi : </strong>".$cln . "<br />";
            $filtered = $this->filtering($cln);
            echo "<strong>hasil filtering (stopword) : </strong>" . $filtered. "<br />";
            echo "<strong>hasil stemming sastrawi:</strong>" . $this->stemming($filtered) . "<br />";
            echo "<strong>hasil stemming (preprocessing): </strong>" . $this->preprocessing($dt->tweet) . "<br /><br />";
            echo "-----------------------------------------------------------------------<br />";

        }
    }

    public function index()
    {
        $datas = DB::Table('twitter')->get();

        $arr_to_save = [];
        foreach ($datas as $data) {
            $hasil_preprocessing = $this->preprocessing($data->tweet);
            // echo "Hasil : ". $hasil_preprocessing . "<br />";
            $arr_to_save[] = [
                'id' => $data->id,
                'tweet' => $hasil_preprocessing,
                'sentimen' => $data->sentiment
            ];

        }

        $resp = DB::table('tbl_hasil_stemming')->insert(
            $arr_to_save
        );

        if($resp){
            dd($resp);
        }

        
    }

    /**
     * function untuk menjalankan proses preprocessing
     *
     * @param String $data kalimat yang akan dipreprocessing
     * @return String
     **/
    public function preprocessing($data)
    {
        /** @var Object $datas object data testing yang akan diproses 
         * data yang digunakan adalah data twitter 
        */
        
        /* 1. case folding */
        $cf = $this->caseFolding($data);

        /** 
         * 2. Tokenizing
         * @var Array $arr_kata array hasil tokenizing 
         * */
        $arr_kata = $this->tokenizing($cf);

        /**
         * 3. Normalisasi 
         * @var String $normalisasi  hasil proses normalisasi 
         * */
        $normalisasi = $this->normalisasi($cf);

        /** 
         * 4. Filtering (menghilangkan Stop word)
         * @var String $filter hasil filtering  
         * */
        $filter = $this->filtering($normalisasi);

        /**
         * 5. Stemming menggunakan library sastrawi (algoritma Nazief)
         *  @var String $stem  output dari function stemming
        */
        $stem = $this->stemming($filter);

        return $stem;
        
    }

    /**
     * stemming function 
     *
     * stemming function melakukan stemming dengan dibantu
     * libary sastrawi 
     * https://github.com/sastrawi/sastrawi
     *
     * @param String $kalimat 
     * @return String
     **/
    public function stemming($kalimat)
    {
        /* Create stemmer */
        $stemmerFactory  = new \Sastrawi\Stemmer\StemmerFactory();
        $stemmer = $stemmerFactory->createStemmer();

        /* stem */
        $output = $stemmer->stem($kalimat);
        return $output;
    }

    


    /**
     * filtering function 
     *
     * funtion untuk menghilangkan kata yang ada di stop-word
     *
     * @param String $data 
     * @return String
     **/
    public function filtering($data)
    {
        /** @var Array $arr_kata array dari string $data */
        $arr_kata = explode(" ", $data);

        /** @var Int $n_kata panjang array $arr_kata */
        $n_kata = count($arr_kata);

        for ($i=0; $i < $n_kata; $i++) { 
            $result = DB::Table('tbl_stopword')->where(DB::raw('BINARY `stopword`'), $arr_kata[$i])->first();
            if($result) {
                unset($arr_kata[$i]); /* hapus data dari array */
            }
        }

        $arr_kata = array_values($arr_kata); /* reorder array setelah difiltering */

        $filtered_string = implode(" ", $arr_kata);
        return $filtered_string;
    }

    /**
     * Proses normalisasi untuk mengubah kata tidak baku ke kata baku
     * 1. cek apakah kata ada di database slang word
     * 2. jika ada ganti dengan kata baku
     * 
     * @param String $data kalimat yang akan dinormalisasi
     * @return String
     **/
    public function normalisasi($data)
    {

        // get array data
        $kata = explode(" ",$data);
        $n_kata = count($kata);
        
        for ($i=0; $i < $n_kata; $i++) { 
            $result = DB::Table('kamus_baku')->where('kata_tidakbaku', 'like', $kata[$i])->first();
            if($result) {
                $kata[$i] = $result->kata_baku;
            }
            
        }
        $kata = implode(" ", $kata);
        return $kata;
    }

    public function tokenizing($kata)
    {
        $tokenizing = explode(" ",$kata);

        // unset($tokenizing[3]);  /* delete array by id */
        // $tokenizing = array_values($tokenizing);  /* reorder array */

        return $tokenizing;
        
    }

    public function caseFolding($text)
    {
        $clean_text = '';
        $clean_text = $this->_cleansing($text);

        return $clean_text;
    }
    
    public function _cleansing($text)
    {
        /* 
        menghilangkan emot
        */
        $clean_text = "";
    
        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, '', $text);
    
        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, '', $clean_text);
    
        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, '', $clean_text);
    
        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, '', $clean_text);
    
        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        /* ------------------------------------------------------------------------- */

        // echo "Tweet Cleansing<br>";
        $tweet = strtolower($clean_text);
		$regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";
		$tweet = preg_replace($regex, ' ', $tweet);
		$tweet = str_replace(',', ' ', $tweet);
		$tweet = str_replace('!', ' ', $tweet);
		$tweet = str_replace('-', ' ', $tweet);
		$tweet = str_replace('…', ' ', $tweet);
		
		
		$tweet = preg_replace('/\@[a-z0-9_]+/i', ' ', $tweet);
		$tweet = preg_replace('/\#[a-z0-9_]+/i', ' ', $tweet);
        $tweet = preg_replace('/[^A-Za-z0-9?!]/', ' ', $tweet);
        $tweet = preg_replace('/\d/', '', $tweet);
		$tweet = str_replace('   ', ' ', $tweet);
		$tweet = str_replace('  ', ' ', $tweet);
        $tweet = preg_replace('!\s+!', ' ', $tweet);
        $tweet = preg_replace('!\s+!', ' ', $tweet);
        $tweet = trim($tweet);

        return $tweet;
    }

    public function insert_into_tbl_latih()
    {
        /* set limit ke 475 */
        $n_negatif = DB::Table('tbl_hasil_stemming')->where('sentimen', 'NEGATIF')->count(); // 504
        $n_positif = DB::Table('tbl_hasil_stemming')->where('sentimen', 'POSITIF')->count(); // 496

        $data_negatif = DB::Table('tbl_hasil_stemming')->where('sentimen', 'NEGATIF')->limit(475)->get(); // 504
        $data_positif = DB::Table('tbl_hasil_stemming')->where('sentimen', 'POSITIF')->limit(475)->get(); // 496

        echo $n_negatif . "<br />";
        echo $n_positif;

        $datas = [];
        foreach ($data_negatif as $key => $value) {
            $datas[] = [
                'id' => $value->id,
                'tweet' => $value->tweet,
                'username' => "",
                'sentimen' => $value->sentimen
            ];
        }
        foreach ($data_positif as $key => $value) {
            $datas[] = [
                'id' => $value->id,
                'tweet' => $value->tweet,
                'username' => "",
                'sentimen' => $value->sentimen
            ];
        }

        $resp = $this->_insert_into("tbl_datalatih", $datas);

        dd($resp);
    }

    public function insert_into_tbl_uji()
    {
        // set offset ke 475
        $data_negatif = DB::Table('tbl_hasil_stemming')->where('sentimen', 'NEGATIF')->offset(475)->limit(200)->get();
        $data_positif = DB::Table('tbl_hasil_stemming')->where('sentimen', 'POSITIF')->offset(475)->limit(200)->get();

        $n_negatif = count($data_negatif); // 21
        $n_positif = count($data_positif);  // 29

        echo "Negatif : " . $n_negatif . "<br />";
        echo "Positif : " . $n_positif;

        $datas = [];
        foreach ($data_negatif as $key => $value) {
            $datas[] = [
                'id' => $value->id,
                'tweet' => $value->tweet,
                'username' => "",
                'sentimen' => ""
            ];
        }
        foreach ($data_positif as $key => $value) {
            $datas[] = [
                'id' => $value->id,
                'tweet' => $value->tweet,
                'username' => "",
                'sentimen' => ""
            ];
        }

        $resp = $this->_insert_into("tbl_datauji", $datas);

        dd($resp);
    }

    public function _insert_into($tbl_name, array $data)
    {
        $resp = DB::Table($tbl_name)->insert($data);

        return $resp;
    }
}
