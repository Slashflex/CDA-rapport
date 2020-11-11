<?php

function debug($value)
{
    echo '<pre>';
    if (is_array($value) || is_object($value)) {
        var_dump($value);
    } else {
        echo $value;
    }
    echo '</pre>';
}
