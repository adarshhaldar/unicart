<?php

namespace Unicart\Formats;

use Exception;

trait OutputFormat
{
    const FORMATS = [
        'array',
        'object',
        'json'
    ];

    /**
     * Rounds the value according to the mode
     * 
     * @param string $mode The mode in which the value will be calculated. Default set to round.
     * @param int|float $value The to be calculated.
     * 
     * @return int|float
     */
    private function roundValue(string $mode = 'round', int|float $value): int|float
    {
        switch ($mode) {
            case 'round':
                $value = round($value, 2);
                break;
            case 'floor':
                $value = floor($value);
                break;
            case 'ceil':
                $value = ceil($value);
                break;
            default:
                $value = round($value, 2);
        }

        return $value;
    }

    /**
     * Formats the data into desirable mode
     * 
     * @param string $as The mode in which the output will be given. Default set to array.
     * @param mixed $data The The data to be converted.
     * 
     * @return mixed
     */
    private function formatter(string $as = 'array', mixed $data): mixed
    {
        $oldAs = $as;
        $as = strtolower($as);

        if (!in_array($as, self::FORMATS)) {
            throw new Exception($oldAs . ' is not a valid formatter. Only ' . implode(',', self::FORMATS) . ' are allowed formats.');
        }

        switch ($as) {
            case 'array':
                $data = $data ? (array) $data : null;
                break;
            case 'object':
                $data = $data ? (object) $data : null;
                break;
            case 'json':
                $data = $data ? json_encode($data, JSON_PRETTY_PRINT) : null;
                break;
            default:
                $data = $data ? (array) $data : null;
        }

        return $data;
    }

    /**
     * Retrieves applied discounts.
     * 
     * @param string $as Flag to fetch data in different formats. Accepted formats are array, object, json
     *
     * @return mixed
     */
    public function discounts(string $as = 'array'): mixed
    {
        return $this->formatter($as, count($this->discounts) ? $this->discounts : null);
    }

    /**
     * Retrieves applied delivery charge.
     * 
     * @param string $as Flag to fetch data in different formats. Accepted formats are array, object, json
     *
     * @return mixed
     */
    public function deliveryCharge(string $as = 'array'): mixed
    {
        return $this->formatter($as, count($this->deliveryCharge) ?  $this->deliveryCharge : null);
    }

    /**
     * Retrieves applied taxes.
     *
     * @param string $as Flag to fetch data in different formats. Accepted format are array, object, json
     * 
     * @return mixed
     */
    public function taxes(string $as = 'array'): mixed
    {
        return $this->formatter($as, count($this->taxes) ? $this->taxes : null);
    }

    /**
     * Converts the detail into an array format.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->getDetail();
    }

    /**
     * Converts the detail into an object format.
     *
     * @return object
     */
    public function toObject(): object
    {
        return json_decode($this->toJson());
    }

    /**
     * Converts the detail into a json format.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->getDetail(), JSON_PRETTY_PRINT);
    }
}
