<?php

namespace Unicart\Validators;

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
            throw new InvoiceException('Bill to address is required');
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
            throw new InvoiceException('Bill to name is required');
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
            throw new InvoiceException('Bill from address is required');
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
            throw new InvoiceException('Bill from name is required');
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
            throw new InvoiceException('Comapany name is required');
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
