<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Twitter;

class CrawlController extends Controller
{
    public function index()
    {
        $data = Twitter::paginate(15);

        return view('crawl.index', ["data" => $data]);
        
    }

    public function runCrawling()
    {
        // get lowest id from database
        $low = DB::Table('twitters')->min('tweet_id');
        $sentimen = "positif";
        for ($i=0; $i < 10; $i++) { 
            $tw = $this->getTweets($low);
            if($i > 5) {
                $sentimen = "positif";
            }
            else {
                $sentimen = "negatif";
            }
            foreach ($tw->statuses as $key => $value) {
                $data['user_id'] = $value->user->id; // id tweet
                $data['username'] = $value->user->name;
                $data['tweet_id'] = $value->id; // id tweet
                $data['tweet'] = $value->full_text;
                $data['sentiment'] = $sentimen;

                // cek apakah id user belum ada di database
                $userId = DB::Table('twitters')->where('user_id', $value->user->id)->doesntExist();
                $tweetId = DB::Table('twitters')->where('tweet_id', $value->id)->doesntExist();
                if($userId && $tweetId) {
                    $this->saveTweet($data);
                }
                

                if($low > $value->id) {
                    $low = $value->id;
                }
            }

        }
    }

    public function getTweets($lowest_id = null)
    {
        /* 
        To get the previous 100 tweets:

        - find the the lowest id in the set that you just retrieved with your query
        - perform the same query with the max_id option set to the id you just found.

        To get the next 100 tweets:

        - find the the highest id in the set that you just retrieved with your query
        - perform the same query with the since_id option set to the id you just found. 
        */


        $consumer_key = "ndx4lGTcW1FGeXkqedSB3N1fN";
        $consumer_secret = "WFzACoomEkpWzZ3awiJcUmCsLGAbc4eiDcAQ2DGur8sMPpvEQd";
        $access_token = "1162760762-FjOsqKnVG8YQyxkUTkKOqXoYAMmrERmPp3joxy7";
        $access_token_secret = "JwzdRrQSHYPT9Oc8fZn03E6h4rVUES6ZUlvwEo66DlHPn";

        $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
        $content = $connection->get("search/tweets", [
            "q" => "#2019GantiPresiden -filter:retweets", 
            "count" => 1000,
            "result_type" => "recent",
            "include_entities" => false,
            "max_id" => $lowest_id ? $lowest_id : '',
            "tweet_mode" => "extended"
        ]);

        return $content;


    }

    public function saveTweet($data)
    {
        $tweet = new Twitter([
            'username' => $data['username'],
            'user_id' => $data['user_id'],
            'tweet_id' => $data['tweet_id'],
            'tweet' => $data['tweet'],
            'sentiment' => $data['sentiment']
        ]);

        $tweet->save();
    }

    public function test()
    {
        $data =  DB::Table('twitters')->where('id','1234')->doesntExist();
        dd($data);
    }
}
