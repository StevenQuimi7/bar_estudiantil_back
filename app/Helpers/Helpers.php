<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('getUsuarioAutenticado')) {
    function getUsuarioAutenticado() {
        return Auth::user();
    }
}

if (!function_exists('getExtesionFile')) {
    function getExtesionFile($path) {
        $ext = explode('.', $path);
        return end($ext);
    }
}


if (!function_exists('getTipoFile')) {
    function getTipoFile($extension) {
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'bmp':
            case 'svg':
            case 'webp':
                return 'imagen';
                break;

            case 'pdf':
            case 'doc':
            case 'docx':
            case 'xls':
            case 'xlsx':
            case 'csv':
                return 'documento';
                break;

            case 'mp3':
            case 'wav':
            case 'm4a':
            case 'aac':
                return 'audio';
                break;

            case 'mp4':
                return 'video';
                break;

            default:
                return 'archivo';
                break;
        }

    }
}