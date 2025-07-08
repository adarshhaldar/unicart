<?php

namespace Unicart\Validators;

use Unicart\Classes\Locale;
use Unicart\Exceptions\InvoiceException;

trait InvoiceValidator
{
    /**
     * Check for bill to address
     * 
     * @return void
     */
    private function checkBillToAddress(): void
    {
        if ($this->billToAddress == null) {
            throw new InvoiceException(Locale::translate('invoice_exceptions.bill_to_address_requried'));
        }
    }

    /**
     * Check for bill to name
     * 
     * @return void
     */
    private function checkBillToName(): void
    {
        if ($this->billToName == null) {
            throw new InvoiceException(Locale::translate('invoice_exceptions.bill_to_name_requried'));
        }
    }

    /**
     * Check for bill from address
     * 
     * @return void
     */
    private function checkBillFromAddress(): void
    {
        if ($this->billFromAddress == null) {
            throw new InvoiceException(Locale::translate('invoice_exceptions.bill_from_address_requried'));
        }
    }

    /**
     * Check for bill from name
     * 
     * @return void
     */
    private function checkBillFromName(): void
    {
        if ($this->billFromName == null) {
            throw new InvoiceException(Locale::translate('invoice_exceptions.bill_from_name_requried'));
        }
    }

    /**
     * Check company name
     * 
     * @return void
     */
    private function checkCompanyName(): void
    {
        if ($this->companyName == null) {
            throw new InvoiceException(Locale::translate('invoice_exceptions.company_name_requried'));
        }
    }

    /**
     * Validate the invoice details
     * 
     * @return void
     */
    private function validate()
    {
        $this->checkCompanyName();
        $this->checkBillFromName();
        $this->checkBillFromAddress();
        $this->checkBillToName();
        $this->checkBillToAddress();
    }
}
