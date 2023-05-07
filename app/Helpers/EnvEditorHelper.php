<?php 

namespace App\Helpers;

use Artisan;

class EnvEditorHelper
{
	public static  function getEnvValues() {

        $data =  array();

        $path = base_path('.env');

        if(file_exists($path)) {

            $values = file_get_contents($path);

            $values = explode("\n", $values);

            foreach ($values as $key => $value) {

                $var = explode('=',$value);

                if(count($var) == 2 ) {
                    if($var[0] != "")
                        $data[$var[0]] = $var[1] ? $var[1] : null;
                } else if(count($var) > 2 ) {
                    $keyvalue = "";
                    foreach ($var as $i => $imp) {
                        if ($i != 0) {
                            $keyvalue = ($keyvalue) ? $keyvalue.'='.$imp : $imp;
                        }
                    }
                    $data[$var[0]] = $var[1] ? $keyvalue : null;
                }else {
                    if($var[0] != "")
                        $data[$var[0]] = null;
                }
            }

            array_filter($data);
        
        }

        return $data;

    }


}