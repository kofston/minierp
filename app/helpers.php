<?php
if (!function_exists('pre')) {
    function pre($string = '')
    {
        echo '<pre>' . print_r($string, TRUE) . '</pre>';
    }
}
if (!function_exists('checkAccess')) {
    function checkAccess($HP=NULL)
    {
        if(Auth::check())
            if($_SERVER['REQUEST_URI'] == '/')
                echo redirect('/home');
            else
                return 1;
        else
            return redirect('/');
    }
}
