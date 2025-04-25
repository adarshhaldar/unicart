<?php

namespace Unicart\Classes;

use Unicart\Unicart;
use Unicart\Classes\Locale;
use Unicart\Validators\InvoiceValidator;

final class Invoice
{
    use InvoiceValidator;

    /**
     * Cart instance
     * @var Unicart
     */
    private $cart = null;

    /**
     * Company name
     * @var null|string
     */
    private $companyName = null;

    /**
     * Company logo link
     * @var null|string
     */
    private $logoLink = null;

    /**
     * Bill from party name
     * @var null|string
     */
    private $billFromName = null;

    /**
     * Bill from party address
     * @var null|string
     */
    private $billFromAddress = null;

    /**
     * Bill from party email
     * @var null|string
     */
    private $billFromEmail = null;

    /**
     * Bill from party contact
     * @var null|string
     */
    private $billFromContact = null;

    /**
     * Bill to party name
     * @var null|string
     */
    private $billToName = null;

    /**
     * Bill to party address
     * @var null|string
     */
    private $billToAddress = null;

    /**
     * Bill to party email
     * @var null|string
     */
    private $billToEmail = null;

    /**
     * Bill to party contact
     * @var null|string
     */
    private $billToContact = null;

    /**
     * Date
     * @var null|string
     */
    private $date = null;

    /**
     * Invoice number
     * @var null|string
     */
    private $invoiceNumber = null;

    /**
     * Order number
     * @var null|string
     */
    private $orderNumber = null;

    /**
     * Currency symbol
     * @var null|string
     */
    private $currency = null;

    /**
     * Flag to set currency symbol position
     * @var bool
     */
    private $isCurrencyPrefix = true;

    /**
     * Constructor to initate invoice generation
     * 
     * @param Unicart $cart The Unicart instance.
     * @param null|string $invoiceNumber Invoice number.
     * @param null|string $orderNumber Order number.
     * @param null|string $currency The currency symbol.
     * @param bool $isCurrencyPrefix Flag for currency position.
     * 
     */
    public function __construct(Unicart $cart, null|string $date, null|string $invoiceNumber = null, null|string $orderNumber = null, null|string $currency = null, bool $isCurrencyPrefix = true)
    {
        $this->cart = $cart;
        $this->date = $date ? htmlspecialchars($date) : date('d/m/Y, h:i:s a');
        $this->invoiceNumber = $invoiceNumber ? htmlspecialchars($invoiceNumber) : $this->generateInvoiceNumber();
        $this->orderNumber = $orderNumber ? htmlspecialchars($orderNumber) : null;
        $this->currency = $currency ? htmlspecialchars($currency) : null;
        $this->isCurrencyPrefix = $isCurrencyPrefix;
    }

    /**
     * Get current locale
     * 
     * @return string
     */
    public function getLocale(): string
    {
        return Locale::getLocale();
    }

    /**
     * Get list of locales
     * 
     * @return array
     */
    public function getLocales(): array
    {
        return Locale::getLocales();
    }

    /**
     * Set locale
     * 
     * @param string $locale The locale that has to be set. Default set to en.
     * 
     * @return void
     */
    public function setLocale(string $locale = 'en'): void
    {
        Locale::setLocale($locale);
    }

    /**
     * Generate random invoice number
     * 
     * @return string
     */
    private function generateInvoiceNumber(): string
    {
        $length = 5;
        $invoiceNumberInBytes = random_bytes($length);
        return bin2hex($invoiceNumberInBytes);
    }

    /**
     * Set company detail
     * 
     * @param string $companyName The company name.
     * @param null|string $logoLink Company's logo link.
     * 
     * @return void
     */
    public function setCompanyDetail(string $companyName, null|string $logoLink = null): void
    {
        $this->companyName = htmlspecialchars($companyName);
        $this->logoLink = $logoLink ? htmlspecialchars($logoLink) : null;
    }

    /**
     * Set bill from party detail
     * 
     * @param string $billFromName The bill from party name.
     * @param null|string $billFromAddress Bill from party's address.
     * @param null|string $billFromEmail Bill from party's email.
     * @param null|string $billFromContact Bill from party's contact.
     * 
     * @return void
     */
    public function setBillFromDetails(string $billFromName, string $billFromAddress, null|string $billFromEmail = null, null|string $billFromContact = null): void
    {
        $this->billFromName = htmlspecialchars($billFromName);
        $this->billFromAddress = htmlspecialchars($billFromAddress);
        $this->billFromEmail = $billFromEmail ? htmlspecialchars($billFromEmail) : null;
        $this->billFromContact = $billFromContact ? htmlspecialchars($billFromContact) : null;
    }

    /**
     * Set bill to party detail
     * 
     * @param string $billToName The bill to party name.
     * @param null|string $billToAddress Bill to party's address.
     * @param null|string $billToEmail Bill to party's email.
     * @param null|string $billToContact Bill to party's contact.
     * 
     * @return void
     */
    public function setBillToDetails(string $billToName, string $billToAddress, null|string $billToEmail = null, null|string $billToContact = null): void
    {
        $this->billToName = htmlspecialchars($billToName);
        $this->billToAddress = htmlspecialchars($billToAddress);
        $this->billToEmail = $billToEmail ? htmlspecialchars($billToEmail) : null;
        $this->billToContact = $billToContact ? htmlspecialchars($billToContact) : null;
    }

    /**
     * Get invoice in HTML
     * 
     * @param bool $downloadHtml The download invoice in HTML button flag.
     * @param bool $rtl The right to left alignment flag.
     * 
     * @return string
     */
    public function getHtmlInvoice(bool $downloadHtml = false): string
    {
        $this->validate();

        $rtl = in_array(Locale::getLocale(), ['ar', 'he', 'fa', 'ur', 'ps', 'sd', 'dv', 'ckb']);

        $summary = $this->cart->toArray();

        list($lang, $title, $labels, $companyName, $logoLink) = $this->getHtmlPageDetail();

        list($invoiceNumber, $orderNumber, $date, $currency, $isCurrencyPrefix) = $this->getInvoiceDetail();

        list($billFromName, $billFromAddress, $billFromEmail, $billFromContact) = $this->getBillFromDetail();

        list($billToName, $billToAddress, $billToEmail, $billToContact) = $this->getBillToDetail();

        ob_start();
        require_once __DIR__ . '/../Invoice/view.php';
        return ob_get_clean();
    }

    /**
     * Get invoice page text labels
     * 
     * @return array
     */
    private function getTextLabels(): array
    {
        return [
            'downloadBtn' => Locale::translate('invoice.download_invoice_btn'),
            'logo' => Locale::translate('invoice.logo'),
            'invoice' => Locale::translate('invoice.title'),
            'invoice_number' => Locale::translate('invoice.invoice_number'),
            'order_number' => Locale::translate('invoice.order_number'),
            'date' => Locale::translate('invoice.date'),
            'bill_from' => Locale::translate('invoice.bill_from'),
            'bill_to' => Locale::translate('invoice.bill_to'),
            'cart' => Locale::translate('invoice.cart'),
            'cart_applicables' => Locale::translate('invoice.cart_applicables'),
            'item'  => Locale::translate('invoice.item'),
            'qty'  => Locale::translate('invoice.qty'),
            'unit_rate'  => Locale::translate('invoice.unit_rate'),
            'discount'  => Locale::translate('invoice.discount'),
            'delivery_charge'  => Locale::translate('invoice.delivery_charge'),
            'tax'  => Locale::translate('invoice.tax'),
            'amount'  => Locale::translate('invoice.amount'),
            'total'  => Locale::translate('invoice.total'),
            'thank_you'  => Locale::translate('invoice.thank_you')
        ];
    }

    /**
     * Get html page detail
     * 
     * @return array
     */
    private function getHtmlPageDetail(): array
    {
        return [
            Locale::getLocale(),
            $this->companyName . '-' . Locale::translate('invoice.title'),
            $this->getTextLabels(),
            $this->companyName,
            $this->logoLink
        ];
    }

    /**
     * Get invoice detail
     * 
     * @return array
     */
    private function getInvoiceDetail(): array
    {
        return [
            $this->invoiceNumber,
            $this->orderNumber,
            $this->date,
            $this->currency,
            $this->isCurrencyPrefix
        ];
    }

    /**
     * Get bill from detail
     * 
     * @return array
     */
    private function getBillFromDetail(): array
    {
        return [
            $this->billFromName,
            $this->billFromAddress,
            $this->billFromEmail,
            $this->billFromContact
        ];
    }

    /**
     * Get bill to detail
     * 
     * @return array
     */
    private function getBillToDetail(): array
    {
        return [
            $this->billToName,
            $this->billToAddress,
            $this->billToEmail,
            $this->billToContact
        ];
    }
}
