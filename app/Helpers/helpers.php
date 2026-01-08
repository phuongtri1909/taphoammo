<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;


function cleanDescription($content, $limit = null)
{
    $text = strip_tags($content);

    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

    $text = preg_replace('/\s+/', ' ', $text);

    $text = trim($text);

    if ($limit === null) {
        return $text;
    }

    return Str::limit($text, $limit);
}

function generateRandomOTP($length = 6)
{
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= rand(0, 9);
    }
    return $otp;
}
