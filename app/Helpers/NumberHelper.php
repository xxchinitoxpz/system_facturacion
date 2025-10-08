<?php

namespace App\Helpers;

class NumberHelper
{
    public static function numberToWords($number)
    {
        $number = round($number, 2);
        $whole = floor($number);
        $decimal = round(($number - $whole) * 100);
        
        $units = [
            '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
            'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve'
        ];
        
        $tens = [
            '', '', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'
        ];
        
        $hundreds = [
            '', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 
            'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
        ];
        
        if ($whole == 0) {
            return 'cero';
        }
        
        $result = '';
        
        // Procesar miles
        $thousandGroups = [];
        $temp = $whole;
        while ($temp > 0) {
            $thousandGroups[] = $temp % 1000;
            $temp = floor($temp / 1000);
        }
        
        for ($i = count($thousandGroups) - 1; $i >= 0; $i--) {
            $group = $thousandGroups[$i];
            if ($group > 0) {
                $groupWords = self::convertGroup($group, $units, $tens, $hundreds);
                if ($i > 0) {
                    if ($group == 1) {
                        $result .= 'mil ';
                    } else {
                        $result .= $groupWords . ' mil ';
                    }
                } else {
                    $result .= $groupWords . ' ';
                }
            }
        }
        
        // Agregar decimales
        if ($decimal > 0) {
            $result .= 'con ' . self::convertGroup($decimal, $units, $tens, $hundreds);
        }
        
        return trim($result);
    }
    
    private static function convertGroup($number, $units, $tens, $hundreds)
    {
        if ($number == 0) return '';
        
        $result = '';
        
        // Centenas
        $hundred = floor($number / 100);
        if ($hundred > 0) {
            if ($hundred == 1 && $number % 100 == 0) {
                $result .= 'cien ';
            } else {
                $result .= $hundreds[$hundred] . ' ';
            }
        }
        
        $remainder = $number % 100;
        
        // Decenas y unidades
        if ($remainder > 0) {
            if ($remainder < 20) {
                $result .= $units[$remainder] . ' ';
            } else {
                $ten = floor($remainder / 10);
                $unit = $remainder % 10;
                
                if ($ten == 2 && $unit > 0) {
                    $result .= 'veinti' . $units[$unit] . ' ';
                } else {
                    $result .= $tens[$ten];
                    if ($unit > 0) {
                        $result .= ' y ' . $units[$unit];
                    }
                    $result .= ' ';
                }
            }
        }
        
        return $result;
    }
}
