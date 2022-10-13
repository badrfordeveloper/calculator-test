<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CalculatorController extends Controller
{
    public function index()
    {
        return view('calculator');
    }

    public function calculate(Request $request)
    {
        try {
            //validation
            $request->validate([
                'textOperation' => [
                    "required",
                    'regex:/^[-+]?[0-9]+(\.\d+(?!\.))?([-+\/*](?![-+\/*])|([-+\/*][0-9]+(\.\d+(?!\.))?))*$/',
                ]
            ]);
            //remove empty items
            $arrItems = $this->convertStringToArray($request->textOperation);
            $result = null;
            $startWithMinus = false;
            $operation = "";
            //start the calculation
            foreach (array_values($arrItems) as $key => $item) {
                if ($key == 0) {
                    if ($item == "-") {
                        $startWithMinus = true;
                    } else if (in_array($item, ["*", "/", "+"])) {
                        $startWithMinus = false;
                    } else {
                        $startWithMinus = false;
                        $result = $item;
                    }
                }
                //first number
                else if ($result == null) {
                    $result = $startWithMinus ? -1 * (float)$item : (float)$item;
                }
                //catch an operation
                else if (in_array((string)$item, ["-", "*", "/", "+"])) {
                    $operation = (string)$item;
                }
                //make the calculation
                else {
                    $result = (float)$result;
                    switch ($operation) {
                        case "-":
                            $result -= (float)$item;
                            break;
                        case "+":
                            $result += (float)$item;
                            break;
                        case "*":
                            $result *= (float)$item;
                            break;
                        case "/":
                            $result /= (float)$item;
                            break;
                    }
                }
            }
            // 1* remove ,00 and number_format keep only 2 digit after the coma
            return  1 * number_format((float)$result, 2, '.', '');
        } catch (ValidationException $exception) {
            return response()->json(['msg' => $exception->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['msg' => "something went wrong"], 500);
        }
    }

    /* $value is string */
    public function convertStringToArray($value)
    {
        $arrItems = [];
        $collector = "";
        $arrChars = str_split($value);
        $charsCount = count($arrChars);
        foreach ($arrChars as $key => $char) {
            if ($key == $charsCount - 1) {
                //if is number
                if (is_numeric($char)) {
                    $collector .= $char;
                    array_push($arrItems, $collector);
                    $collector = "";
                } else if (in_array($char, ["-", "*", "/", "+"])) {
                    array_push($arrItems, $collector);
                    $collector = "";
                }
            } else if (in_array($char, ["-", "*", "/", "+"])) {
                array_push($arrItems, $collector);
                $collector = "";
                array_push($arrItems, $char);
            } else {
                $collector .= $char;
            }
        }
        $arrItems = array_filter($arrItems);
        return $arrItems;
    }
}
