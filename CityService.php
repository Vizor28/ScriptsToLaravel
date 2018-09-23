<?php

namespace App\Services;

use App\Region;
use Cache;

class CityService
{
    public $region = false;
    public $replaces = [];

    public function __construct()
    {
        $host = str_replace('www.', '', request()->getHost());
        $region = Cache::tags(['regions'])->remember($host, \Carbon\Carbon::now()->addDay(2), function() use($host){
            return Region::with(['words'])->where('host', '=', $host)->where('is_enabled', 1)->orderBy('is_enabled')->first();
        });
        if(!empty($region)) {
            $this->replaces = Cache::tags(['regions'])->remember($host . '_replace', \Carbon\Carbon::now()->addDay(2), function () use ($region) {
                $words = $region->words->pluck('translate', 'code')->all();
                $replaces = [];
                foreach ($words as $code => $word) {
                    $replaces['{{' . $code . '}}'] = $word;
                }
                foreach ($region->getAttributes() as $code => $word) {
                    $replaces['{{' . $code . '}}'] = $word;
                }

                return $replaces;
            });
        }
        $this->region = $region;
    }

    public function __get($name){
        return $this->region->{$name};
    }

    public function replace($string)
    {
        if(is_array($this->replaces)){
            $string = str_replace(array_keys($this->replaces), array_values($this->replaces), $string);
        }
        return $string;
    }

    public function word($word)
    {
        $word = '{{'.$word.'}}';
        if(isset($this->replaces[$word])){
            return $this->replaces[$word];
        }
        return '';
    }

}
