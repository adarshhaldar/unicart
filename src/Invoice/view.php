<?php
header("Access-Control-Allow-Origin: *");

function formatNumber(float $number, null|string $currency = null, int $decimals = 2): string
{
    $formatted = str_contains($number, '.') ? number_format($number, $decimals) : number_format($number);

    if (isset($currency)) {
        $formatted = $currency . $formatted;
    }

    return $formatted;
}
?>

<html lang="<?php echo $lang ?>" dir="<?php echo $rtl ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        <?php require __DIR__ . '/view.css' ?>.summary p span {
            float: <?php echo $rtl ? 'left' : 'right'; ?>
        }
    </style>
    <script src="https://kit.fontawesome.com/7e2c349626.js"></script>

</head>

<body>
    <div class="main-container">
        <?php if ($downloadPdf) {
        ?>
            <button id="download-btn" onclick="downloadPdfInvoice()"><i class="fa-solid fa-download"></i></button>
        <?php
        } ?>

        <div id="invoice-content" class="invoice-container">
            <div class="invoice-header">
                <?php
                if ($logoLink) {
                ?>
                    <img src="<?php echo $logoLink; ?>" alt="<?php echo $labels['logo']; ?>">
                <?php }
                ?>
                <h2><?php echo $companyName; ?></h2>
            </div>

            <hr>

            <div class="invoice-detail">
                <p><?php echo $labels['date']; ?> <span> <?php echo $date; ?></span></p>
                <p><?php echo $labels['invoice']; ?> <span>#<?php echo $invoiceNumber; ?></span></p>
                <?php
                if ($orderNumber) {
                ?>
                    <p><?php echo $labels['order']; ?> <span>#<?php echo $orderNumber; ?></span></p>
                <?php
                } ?>
            </div>

            <hr>

            <div class="biller-detail">
                <div class="from-detail">
                    <p><b><?php echo $labels['bill_from']; ?></b></p>
                    <p><?php echo $billFromName; ?></p>
                    <?php
                    if ($billFromEmail) {
                    ?>
                        <p><?php echo $billFromEmail; ?></p>
                    <?php }
                    if ($billFromContact) {
                    ?>
                        <p><?php echo $billFromContact; ?></p>
                    <?php } ?>
                    <p><?php echo nl2br($billFromAddress); ?></p>
                </div>

                <div class="to-detail">
                    <p><b><?php echo $labels['bill_to']; ?></b></p>
                    <p><?php echo $billToName; ?></p>
                    <?php
                    if ($billToEmail) {
                    ?>
                        <p><?php echo $billToEmail; ?></p>
                    <?php }
                    if ($billToContact) {
                    ?>
                        <p><?php echo $billToContact; ?></p>
                    <?php } ?>
                    <p><?php echo nl2br($billToAddress); ?></p>
                </div>
            </div>

            <hr><br>

            <div class="item-level">
                <table class="item-level-table">
                    <thead>
                        <th><?php echo $labels['item']; ?></th>
                        <th><?php echo $labels['price']; ?></th>
                        <th><?php echo $labels['qty']; ?></th>
                        <th><?php echo $labels['discount']; ?></th>
                        <th><?php echo $labels['delivery_charge']; ?></th>
                        <th><?php echo $labels['tax']; ?></th>
                        <th><?php echo $labels['total']; ?></th>
                    </thead>
                    <tbody>
                        <?php
                        if ($summary['items'] != null && count($summary['items'])) {
                            foreach ($summary['items'] as $item) {
                        ?>
                                <tr>
                                    <td><b>#<?php echo $item['id'] ?></b>
                                        <?php
                                        if (isset($item['metadata'])) {
                                            foreach ($item['metadata'] as $key => $data) {
                                        ?>
                                                <span><?php echo $rtl ? $data : $key; ?>: <?php echo $rtl ? $key : $data; ?></span>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo formatNumber($item['price']); ?></td>
                                    <td><?php echo number_format($item['quantity'], 0); ?></td>
                                    <td><?php echo formatNumber($item['totalDiscount']); ?></td>
                                    <td><?php echo formatNumber($item['totalDeliveryCharge']); ?></td>
                                    <td><?php echo formatNumber($item['totalTax']); ?></td>
                                    <td><?php echo formatNumber($item['payableAmount']); ?></td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td></td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <hr>

            <table class="cart-level-table">
                <thead>
                    <th><?php echo $labels['cart']; ?></th>
                    <th><?php echo $labels['discount']; ?></th>
                    <th><?php echo $labels['delivery_charge']; ?></th>
                    <th><?php echo $labels['tax']; ?></th>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $labels['cart_applicables']; ?></td>
                        <td><?php echo formatNumber($summary['totalDiscountOnCart']); ?></td>
                        <td><?php echo formatNumber($summary['totalDeliveryChargeOnCart']); ?></td>
                        <td><?php echo formatNumber($summary['totalTaxOnCart']); ?></td>
                    </tr>
                </tbody>
            </table>

            <hr>

            <div class="invoice-summary">
                <div class="summary">
                    <p><?php echo $labels['total_discount']; ?>: <span><?php echo formatNumber($summary['totalDiscount']) > 0 ? '-' . formatNumber($summary['totalDiscount'], $currency) : formatNumber($summary['totalDiscount'], $currency); ?></span></p>
                    <p><?php echo $labels['total_delivery']; ?>: <span><?php echo formatNumber($summary['totalDeliveryCharge']) > 0 ? '+' . formatNumber($summary['totalDeliveryCharge'], $currency) : formatNumber($summary['totalDeliveryCharge'], $currency); ?></span></p>
                    <p><?php echo $labels['total_tax']; ?>: <span><?php echo formatNumber($summary['totalTax']) > 0 ? '+' . formatNumber($summary['totalTax'], $currency) : formatNumber($summary['totalTax'], $currency); ?></span></p>
                    <hr>
                    <p><?php echo $labels['total']; ?>: <span><?php echo formatNumber($summary['payableAmount'], $currency); ?></span></p>
                </div>
            </div>
        </div>
    </div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    function downloadPdfInvoice() {
        const element = document.getElementById('invoice-content');
        html2pdf()
            .from(element)
            .set({
                margin: 10,
                filename: '<?php echo 'invoice_' . $invoiceNumber . '.pdf'; ?>',
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            })
            .save();
    }
</script>

</html>