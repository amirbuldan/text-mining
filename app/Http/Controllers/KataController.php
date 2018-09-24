<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// contoller kata slang
class KataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // ----------------------------------------------------------------------------- //

    public function insert_slang_dict()
    {
        $inserts = [];
        $data = $this->_get_word_dict();
        foreach($data as $d){
            $inserts[] = [
                "kata_tidak_baku" => $d['slang'],
                "kata_baku" => $d['baku'] 
            ];
        };

        $response = DB::table('tbl_slangword')->insert($inserts);
        return $response;
    }

    public function _get_word_dict()
    {
        // read file
        $contents = Storage::disk('public')->get('files/kata-tidak-baku-dict.txt');
        // <correct word 1>|<wrong word 1>

        
        preg_match_all('/[a-z]+\|/', $contents, $ls_kata_baku);
        preg_match_all('/\|[a-z]+/', $contents, $ls_kata_slang);
        
        
        $p_baku= preg_replace('/\|/', '', $ls_kata_baku[0]);
        $p_slang= preg_replace('/\|/', '', $ls_kata_slang[0]);
        
        $dict=[];
        $numb = count($ls_kata_slang[0]);

        for ($i=0; $i < $numb; $i++) { 
        $dict[] = [
                'baku' => $p_baku[$i],
                'slang' => $p_slang[$i]
            ] ;    
        }
        
        return $dict;
    }

    public function select_insert_latih()
    {
        $inserts = [];
        $data = DB::table('twitter')
            ->limit(950)
            ->get();

        foreach($data as $d){
            $inserts[] = [
                'user_id' => $d->user_id,
                'username' => $d->username,
                'tweet_id' => $d->tweet_id,
                'tweet' => $d->tweet,
                'sentiment' => $d->sentiment
            ];
        }

        $response  = DB::table('tbl_datatraining')->insert($inserts);
        dd($response);
    }
    public function select_insert_tes()
    {
        $inserts = [];
        $data = DB::table('twitter')
            ->offset(950)
            ->limit(50)
            ->get();

        foreach($data as $d){
            $inserts[] = [
                'user_id' => $d->user_id,
                'username' => $d->username,
                'tweet_id' => $d->tweet_id,
                'tweet' => $d->tweet,
                'sentiment' => $d->sentiment
            ];
        }

        $response  = DB::table('tbl_datatesting')->insert($inserts);
        dd($response);

    }
}
