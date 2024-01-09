<?php

namespace TomatoPHP\TomatoEddy\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TomatoPHP\TomatoEddy\Enums\Services\KeyPairType;
use TomatoPHP\TomatoEddy\Tasks\GenerateEd25519KeyPair;

class RSAToPrivite
{

    public static function openssh2pem($key)
    {
        $filesystem = new Filesystem;
        $filesystem->ensureDirectoryExists(storage_path('app/keygen'));

        $file = Str::random();

        $privatePath = storage_path("app/keygen/{$file}");
        $publicPath = storage_path("app/keygen/{$file}.pub");


        dd('ssh-keygen -f '.$publicPath.' -e -m pem > id_rsa.pub.pem');
        $output = exec('ssh-keygen -f '.$publicPath.' -e -m pem > id_rsa.pub.pem');

        dd($output);


    }
}
