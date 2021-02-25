<?php


namespace App\Service;


class Slugify
{
    protected $toReplacementList = [
        'à',
        'é',
        'è',
        ' ',
        '  ',
        '°',
        'l\''
    ];

    protected $replacementList = [
        'a',
        'e',
        'e',
        '-',
        ' ',
        '',
        ''
    ];

    function generateTitle($array)
    {
        return $array["position"]. ' ' . $array["grabs"] . ' à ' . $array["rotation"] . '° ' . $array["flip"] . ' ' . $array["slide"];
    }

    function generateSlug($string)
    {
        return str_replace($this->toReplacementList, $this->replacementList, $string);
    }
}