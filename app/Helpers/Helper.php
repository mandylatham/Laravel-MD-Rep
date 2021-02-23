<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Container\Container;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Queue\SerializableClosure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Foundation\Bus\PendingDispatch;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Cookie\Factory as CookieFactory;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;

if (! function_exists('commonDate')) {
    
    function commonDate($date)
    {
    	$format = config("site.date_format.back");
    	return formatDate($date, $format);
    }
}

if (! function_exists('formatDate')) {
    
    function formatDate($date, $format)
    {
    	$date = Carbon::parse($date);
    	return $date->format($format);
    }
}


if (! function_exists('changeDateformat')) {
    
    function changeDateformat($date, $from, $format)
    {
    	$date = Carbon::createFromFormat($from, $date);
    	return $date->format($format);
    }
}

if (! function_exists('avatar')) {
    
    function avatar($name)
    {
    	return Avatar::create($name)->toBase64();
    }
}

if (! function_exists('limit')) {
    
    function limit($name)
    {
    	return config("site.limit.".$name);
    }
}

if (! function_exists('maxLimit')) {
    
    function maxLimit($name)
    {
    	return "maxlength='".limit($name)."'";
    }
}


if (! function_exists('convertBase64')) {
    
    function convertBase64($path)
    {
        return base64_encode(file_get_contents($path));
    }
}

if (! function_exists('isJson')) {
    function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (! function_exists('s3Url')) {
    function s3Url($url) {
        return Storage::disk('s3')->url($url);
    }
}