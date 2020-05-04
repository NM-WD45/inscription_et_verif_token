<?php
function str_random($length){
    $alphabet = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
    return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
}

function str_token($length) {
    $caract ="0123456789azertyuiopmlkjhgfdsqwxcvbnAZERTYUIOPMLKJHGFDSQWXCVBN";
    return substr(str_shuffle(str_repeat($caract, $length)),0, $length);
}
