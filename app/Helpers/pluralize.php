<?php

if (!function_exists('trans_choice')) {
    function trans_choice($variants, $count)
    {
        $variants = explode('|', $variants);
        $count = abs((int) $count);

        if ($count % 100 >= 11 && $count % 100 <= 14) {
            return $variants[2] ?? end($variants);
        }

        switch ($count % 10) {
            case 1:
                return $variants[0];
            case 2:
            case 3:
            case 4:
                return $variants[1] ?? end($variants);
            default:
                return $variants[2] ?? end($variants);
        }
    }
}
