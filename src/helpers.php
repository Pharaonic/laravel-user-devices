<?php


if (!function_exists('getCurrentDeviceSignature')) {
    function getCurrentDeviceSignature()
    {
        return session()->isStarted() ?
            session()->get('X-DEVICE-SIGNATURE') :
            request()->headers->get('X-DEVICE-SIGNATURE');
    }
}