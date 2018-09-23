<?php

namespace App\Traits;

trait CityWordsTransfom
{

    public function getAttributeValue($key)
    {
        if(empty($_SERVER) || !isset($_SERVER['REQUEST_URI']) || preg_match('/admin/', $_SERVER['REQUEST_URI']) ||  !isset($this->CityWordsTransfom_attr) || empty($this->CityWordsTransfom_attr) || !in_array($key, $this->CityWordsTransfom_attr)){

            return parent::getAttributeValue($key);

        }

        $value = $this->getAttributeFromArray($key);

        if ($this->hasGetMutator($key)) {
            $value = $this->mutateAttribute($key, $value);

            $value = $this->transformCityWords($value);

            return $value;
        }

        if ($this->hasCast($key)) {
            $value = $this->castAttribute($key, $value);
        }
        elseif (in_array($key, $this->getDates())) {
            if (! is_null($value)) {
                return $this->asDateTime($value);
            }
        }


        if(is_array($value)){

            foreach($value as $k => &$v){
                if(in_array($k, $this->CityWordsTransfom_attr)){
                    $v = $this->transformCityWords($v);
                }
            }

        }else{

            $value = $this->transformCityWords($value);

        }


        return $value;
    }

    protected function transformCityWords($value){

        return app('region')->replace($value);

    }

}
