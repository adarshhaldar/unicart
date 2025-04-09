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

    private function formatter(string $as = 'array', $data)
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
     * @return array
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
