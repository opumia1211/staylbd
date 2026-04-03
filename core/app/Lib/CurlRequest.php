<?php

namespace App\Lib;

class CurlRequest
{

    /**
    * GET request using curl
    *
    * @return mixed
    */
	public static function curlContent($url,$header = null)
	{
	    // External calls disabled for security
	    return null;
	}


    /**
    * POST request using curl
    *
    * @return mixed
    */
	public static function curlPostContent($url, $postData = null,$header = null)
	{
	    // External calls disabled for security
	    return null;
	}
}
