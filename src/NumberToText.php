<?php

/**
 * 
 * ===== CONVERTIR NÚMERO A TEXTO ====
 * 
 * La presente clase transforma un número entero a su equivalente en palabras.
 * Permite un rango de trabajo desde 0 hasta 10^126-1, o sea, 126 dígitos.
 * 
 * requiere PHP >= 7.1
 * 
 * @date 2022-09-24
 * @version 1.0
 * @author Darvin Farfan <darvinjosuefarfanlinarez@gmail.com>
 * @license MIT
 * 
 */


class NumberToText
{
    
    private const units            =   [
                                        "mil", "millón", "billón",
                                        "trillón", "cuatrillón", "quintillón",
                                        "sextillón", "septillón", "octillón",
                                        "nonillón", "decillón", "undecillón",
                                        "duodecillón", "tredecillón", "cuatordecillón",
                                        "quindecillón", "sexdecillón", "septendecillón",
                                        "octodecillón", "novendecillón", "vigintillón"
                                    ];

    private const ones             =   ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];

    private const ones_acute       =   [1 => "ún", "dós", "trés", 6 => "séis"];

    private const tens             =   [1 => "diez", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"];
    private const ten_with_units   =   ["diez", "once", "doce", "trece", "catorce", "quince", "dieciséis", "diecisiete", "dieciocho", "diecinueve"];

    private const hundreds         =   [1 => "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos"];
    private const hundreds_female  =   [1 => "ciento", "doscientas", "trescientas", "cuatrocientas", "quinientas", "seiscientas", "setecientas", "ochocientas", "novecientas"];

    private const max_length            =   126;

    private string      $number;
    private string|bool $number_text    =   false;

    private bool $female_mode           =   false;
    private bool $apocope_mode          =   false;

    
    /**
     * __construct
     *
     * @param  string|bool $stringNumber
     * @param  bool $femaleMode
     * @param  bool $apocopeMode
     * @return void
     */
    function __construct(string|bool $stringNumber = false, bool $femaleMode = false, bool $apocopeMode = false)
    {
        if ($stringNumber !== false && !preg_match(sprintf("/^[0-9]{1,%s}$/", $this::max_length), $stringNumber))
            throw new ParseError('Wrong parameter');

        $this->number = $stringNumber;
        $this->female_mode = $femaleMode;
        $this->apocope_mode = $apocopeMode;
    }
    

    /**
     * setNumber
     *
     * @param  string|int $stringNumber
     * @return void
     */
    function setNumber(string|int $stringNumber)
    {
        if (!preg_match(sprintf("/^[0-9]{1,%s}$/", $this::max_length), $stringNumber))
            throw new ParseError('Wrong parameter');

        $this->number = $stringNumber;
        $this->number_text = false;
    }
    

    /**
     * femaleMode
     *
     * @return void
     */
    function femaleMode()
    {
        $this->female_mode = true;
    }
    

    /**
     * maleMode
     *
     * @return void
     */
    function maleMode()
    {
        $this->female_mode = false;
    }
    

    /**
     * setApocopeMode
     *
     * @param  bool $apocopeMode
     * @return void
     */
    function setApocopeMode(bool $apocopeMode)
    {
        $this->apocope_mode = $apocopeMode;
    }


    /**
     * getText
     *
     * @return string
     */
    function getText():string
    {
        if ($this->number_text === false)
            $this->processText();

        return $this->number_text;
    }
    

    /**
     * isEven
     *
     * @param  string|int $number
     * @return bool
     */
    private function isEven(string|int $number):bool
    {
        return (($number % 2) == 0);
    }
    

    /**
     * isOdd
     *
     * @param  string|int $number
     * @return bool
     */
    private function isOdd(string|int $number):bool
    {
        return (($number % 2) != 0);
    }
    

    /**
     * getUnits
     *
     * @param  string|int $group_index
     * @param  bool|int $units_plural
     * @return string
     */
    private function getUnits(string|int $group_index, bool|int $units_plural = false):string
    {
        if ($group_index == 0)             return "";
        if ($this->isOdd($group_index))    return $this::units[0];
        if ($units_plural === false)       return $this::units[$group_index / 2];

        return str_replace("ó", "o", $this::units[$group_index / 2]) . "es";
    }
    

    /**
     * getOnes
     *
     * @param  string|int $number
     * @param  bool $acute
     * @param  int $unit
     * @return string
     */
    private function getOnes(string|int $number, bool $acute = false, int $unit = 0):string
    {
        if ($acute && in_array($number, array_keys($this::ones_acute)))
            return $this::ones_acute[$number];

        if ($this->female_mode && $number == 1 && $unit == 0) return "una";

        return $this::ones[$number];
    }
    

    /**
     * getTens
     *
     * @param  string|int $number
     * @param  bool|int $unitAcuteForTwenty
     * @return string
     */
    private function getTens(string|int $number, bool|int $unitAcuteForTwenty = false):string
    {
        if ($number[0] == 1) return $this::ten_with_units[$number[1]];
        if ($number[1] == 0) return $this::tens[$number[0]];
        if ($number[0] == 2) {
            if ($number[1] == 1 && !$unitAcuteForTwenty)
                return "veinti" . ($this->female_mode && $unitAcuteForTwenty < 1 ? "una" : "uno");

            return sprintf("veinti%s", $this->getOnes($number[1], true));
        }

        return sprintf("%s y %s", $this::tens[$number[0]], $this->getOnes($number[1]));
    }


    /**
     * getUnitGroup
     *
     * @param  int $current_group
     * @param  array $array_group
     * @return int
     */
    private function getUnitGroup(int $current_group, array $array_group):int
    {
        return count($array_group) - $current_group + 1;
    }


    /**
     * textGroups
     *
     * @param  string|int $group
     * @param  array $array_group
     * @param  int $current_group
     * @param  int $unit
     * @param  bool|int $realUnit
     * @return string
     */
    private function textGroups(string|int $group, array $array_group, int $current_group, int $unit, bool|int $realUnit = false):string
    {
        $group = ltrim($group, '0');

        if ($group == "")
            return $this->processGroupVoid($group, $array_group, $current_group, $unit);

        if (strlen($group) == 1)
            return $this->processGroupOnes($group, $array_group, $current_group, $unit);

        if (strlen($group) == 2)
            return $this->processGroupTens($group, $array_group, $current_group, $unit, $realUnit);

        return $this->processGroupHundreds($group, $array_group, $current_group, $unit);
    }
    

    /**
     * processGroupVoid
     *
     * @param  string|int $group
     * @param  array $array_group
     * @param  int $current_group
     * @param  int $unit
     * @return string
     */
    private function processGroupVoid(string|int $group, array $array_group, int $current_group, int $unit):string
    {
        if ($unit == 0 && $current_group == 0) return "cero";

        if ($this->isEven($unit)) {
            $next_index = $current_group - 1;
            if ($next_index >= 0 && $array_group[$next_index] > 0)
                return $this->getUnits($unit, true);
        }

        return "";
    }
    

    /**
     * processGroupOnes
     *
     * @param  string|int $group
     * @param  array $array_group
     * @param  int $current_group
     * @param  int $unit
     * @return string
     */
    private function processGroupOnes(string|int $group, array $array_group, int $current_group, int $unit):string
    {
        $display_unit = $this->getUnits($unit);
        $display_digit = $this->getOnes($group);

        $isEven = $this->isEven($unit);
        $isOdd  = !$isEven;

        if ($isEven) {
            $next_index = $current_group - 1;
            if ($next_index >= 0 && $array_group[$next_index] > 0)
                $display_unit = $this->getUnits($unit, true);
        }

        if ($isOdd && $group == 1)
            $display_digit = "";

        else if ($unit > 0 && $isEven && $group == 1)
            $display_digit = "un";

        else if ($group > 1)
            $display_unit = $this->getUnits($unit, true);

        return sprintf("%s %s", $display_digit, $display_unit);
    }
    

    /**
     * processGroupTens
     *
     * @param  string|int $group
     * @param  array $array_group
     * @param  int $current_group
     * @param  int $unit
     * @param  bool|int $realUnit
     * @return string
     */
    private function processGroupTens(string|int $group, array $array_group, int $current_group, int $unit, bool|int $realUnit = false):string
    {
        $display_unit = $this->getUnits($unit, $unit > 1);

        $unit_not_recursive = $realUnit !== false ? $realUnit : $this->getUnitGroup($current_group, $array_group);

        if ($group[0] != 2 && $group[1] == 1 && ($this->isOdd($unit_not_recursive) || ($realUnit !== false && $realUnit > 0)))
            return sprintf("%s y un %s", $this->getTens($group[0] . "0"), $display_unit);

        if ($group == "21" && $realUnit !== false && $realUnit > 0)
            return sprintf("veintiún %s", $display_unit);

        return sprintf("%s %s", $this->getTens($group, $unit), $display_unit);
    }
    

    /**
     * processGroupHundreds
     *
     * @param  string|int $group
     * @param  array $array_group
     * @param  int $current_group
     * @param  int $unit
     * @return string
     */
    private function processGroupHundreds(string|int $group, array $array_group, int $current_group, int $unit):string
    {
        $display_unit = $this->getUnits($unit, $unit > 1);
        $display_digit_one = $unit <= 0 ? "uno" : "un";
        $display_digit_hundred = $this::hundreds[$group[0]];

        $only_tens = $group - ($group[0] * 100);

        if ($this->female_mode) {

            $display_digit_hundred = $this::hundreds_female[$group[0]];

            if ($unit <= 0)
                $display_digit_one = "una";
        }

        if ($group[0] == 1) {

            if ($only_tens <= 0)
                return sprintf("cien %s", $display_unit);

            if ($only_tens == 1)
                return sprintf("ciento %s %s", $display_digit_one, $display_unit);

            $subGroup = $this->textGroups($only_tens, $array_group, count($array_group) - 1, 0, $unit);
            return sprintf("ciento %s %s", $subGroup, $display_unit);
        }

        if ($only_tens <= 0)
            return sprintf("%s %s", $display_digit_hundred, $display_unit);

        if ($only_tens == 1)
            return sprintf("%s %s %s", $display_digit_hundred, $display_digit_one, $display_unit);

        $subGroup = $this->textGroups($only_tens, $array_group, count($array_group) - 1, 0, $unit);
        return sprintf("%s %s %s", $display_digit_hundred, $subGroup, $display_unit);
    }
    

    /**
     * processText
     *
     * @return void
     */
    private function processText()
    {
        if ($this->number === false)
            throw new ParseError('Wrong parameter');

        $this->number = ltrim($this->number, "0");

        $groups = array_map("strrev", array_reverse(str_split(strrev($this->number), 3)));

        for ($i = count($groups) - 1, $j = 0, $response = ""; $i >= 0; $i--, $j++)
            $response = $this->textGroups($groups[$i], $groups, $i,  $j) . " " . $response;

        $response = trim($response);

        if ($this->apocope_mode && str_ends_with($response, $this->female_mode ? "una" : "uno"))
            $response = rtrim($response, $this->female_mode ? "a" : "o");

        $this->number_text = $response;
    }
}
