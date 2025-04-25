<?php
if ($downloadHtml) {
?>
    <button id="download-btn" style="
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-top: 20px;
    display: block;
    margin-left: auto;
" onclick="downloadInvoice()"><?php echo $labels['downloadBtn'] ?></button>
<?php
}
?>

<div id="invoice-content" style="font-family:'Manrope',sans-serif; background: #fafafa; padding: 0px 0;">
    <table border="0" bgcolor="#fff" cellspacing="0" cellpadding="0"
        style="width: 700px;margin: 0 auto;text-align: center;padding: 60px 0 0px 0; color:#000000">
        <tbody>
            <?php
            if ($logoLink) {
            ?>
                <tr>
                    <td>
                        <img src="<?php echo $logoLink; ?>" alt="<?php echo $labels['logo']; ?>" height="35px">
                    </td>
                </tr>
            <?php
            }
            ?>

            <tr>
                <td>
                    <h1 style="font-size: 25px; margin: 20px 0 20px 0px;"><?php echo $labels['invoice']; ?></h1>
                    <div style="text-align: left; padding:0px 0px 0px 25px">
                        <p><span style="font-weight: 600;"><?php echo $labels['invoice_number']; ?>:</span>
                            <?php echo $invoiceNumber; ?></p>
                        <?php
                        if ($orderNumber) {
                        ?>
                            <p><span style="font-weight: 600;"><?php echo $labels['order_number']; ?>:</span>
                                <?php echo $orderNumber; ?>
                            </p>
                        <?php
                        }
                        ?>
                        <p style="margin-top: 0px;"><span style="font-weight: 600;"><?php echo $labels['date']; ?>:</span>
                            <?php echo $date; ?>
                        </p>
                    </div>
                    <hr style="margin-bottom:20px;">
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width:100%">
                        <tr>
                            <td style="width:50%;">
                                <div style="text-align: left;padding:0px 0px 0px 25px">
                                    <h5 style="margin:0px;font-size: 16px;"><?php echo $labels['bill_from']; ?></h5>
                                    <p style="margin: 0px; padding: 0px;"> <?php echo $billFromName; ?></p>
                                    </p>
                                    <p style="margin: 0px; padding: 0px;"> <?php echo nl2br($billFromAddress); ?></p>
                                    </p>
                                    <?php
                                    if ($billFromEmail) {
                                    ?>
                                        <p style="margin: 0px; padding: 0px;"><?php echo $billFromEmail; ?></p>
                                    <?php
                                    }
                                    if ($billFromContact) {
                                    ?>
                                        <p style="margin: 0px; padding: 0px;"><?php echo $billFromContact; ?></p>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </td>
                            <td align="right" style="width:50%; ">
                                <div style="text-align: right;padding:0px 25px 0px 0px">
                                    <h5 style="margin:0px;font-size: 16px;"><?php echo $labels['bill_to']; ?></h5>
                                    <p style="margin: 0px; padding: 0px;"> <?php echo $billToName; ?></p>
                                    </p>
                                    <p style="margin: 0px; padding: 0px;"> <?php echo nl2br($billToAddress); ?></p>
                                    </p>
                                    <?php
                                    if ($billToEmail) {
                                    ?>
                                        <p style="margin: 0px; padding: 0px;"><?php echo $billToEmail; ?></p>
                                    <?php
                                    }
                                    if ($billToContact) {
                                    ?>
                                        <p style="margin: 0px; padding: 0px;"><?php echo $billToContact; ?></p>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <hr>
                </td>
            </tr>
            <tr>
                <td>
                    <table>
                        <thead>
                            <tr style="background:#f2d9cb;">
                                <th style="padding: 10px 25px 10px 25px; width: 100%;"><?php echo $labels['item']; ?></th>
                                <th style="padding: 10px 25px 10px 25px; width: 100%;"><?php echo $labels['qty']; ?></th>
                                <th style="padding: 10px 25px 10px 25px; width: 100%;"><?php echo $labels['unit_rate']; ?></th>
                                <th style="padding: 10px 25px 10px 25px; width: 100%;"><?php echo $labels['discount']; ?></th>
                                <th style="padding: 10px 25px 10px 25px; width: 100%;"><?php echo $labels['delivery_charge']; ?></th>
                                <th style="padding: 10px 25px 10px 25px; width: 100%;"><?php echo $labels['tax']; ?></th>
                                <th style="padding: 10px 25px 10px 25px; width: 100%;"><?php echo $labels['amount']; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($summary['items'] as $item) {
                            ?>
                                <tr style="background:#b5b5b52e;">
                                    <td style="text-align:center; padding: 5px 25px 5px 25px;">
                                        <span style="display:block;width:100px;font-size:12px;font-weight:bolder;">#<?php echo $item['id'] ?></span>
                                        <?php
                                        if (isset($item['metadata'])) {
                                            foreach ($item['metadata'] as $key => $data) {
                                        ?>
                                                <span style="display:block;width:100px;font-size:12px;"><?php echo $key; ?>: <?php echo $data; ?></span>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td style="padding: 5px 25px 5px 25px; text-align: center;"><?php echo number_format($item['quantity'], 0) ?></td>
                                    <td style="padding: 5px 25px 5px 25px; text-align: center;"><?php echo formatNumber($item['price'])  ?></td>
                                    <td style="padding: 5px 25px 5px 25px; text-align: center;"><?php echo formatNumber($item['totalDiscount']) ?></td>
                                    <td style="padding: 5px 25px 5px 25px; text-align: center;"><?php echo formatNumber($item['totalDeliveryCharge']) ?></td>
                                    <td style="padding: 5px 25px 5px 25px; text-align: center;"><?php echo formatNumber($item['totalTax']) ?></td>
                                    <td style="padding: 5px 25px 5px 25px; text-align: center;"><?php echo formatNumber($item['payableAmount']) ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <hr>
                    <table>
                        <thead>
                            <tr style="background:#f2d9cb;">
                                <th style="padding: 10px 25px 10px 25px; width: 100%;"><?php echo $labels['cart'] ?></th>
                                <th style="padding: 10px 25px 10px 25px; width: 100%;"><?php echo $labels['discount']; ?></th>
                                <th style="padding: 10px 25px 10px 25px; width: 100%;"><?php echo $labels['delivery_charge']; ?></th>
                                <th style="padding: 10px 25px 10px 25px; width: 100%;"><?php echo $labels['tax']; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="background:#b5b5b52e;">
                                <td style="padding: 5px 25px 5px 25px; text-align: center;"><?php echo $labels['cart_applicables'] ?></td>
                                <td style="padding: 5px 25px 5px 25px; text-align: center;"><?php echo formatNumber($summary['totalDiscountOnCart']) ?></td>
                                <td style="padding: 5px 25px 5px 25px; text-align: center;"><?php echo formatNumber($summary['totalDeliveryChargeOnCart']) ?></td>
                                <td style="padding: 5px 25px 5px 25px; text-align: center;"><?php echo formatNumber($summary['totalTaxOnCart']) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <hr>
                    <div style="text-align:start; max-width: 200px; margin-left:auto;">
                        <p><?php echo $labels['discount']; ?>: <?php echo formatNumber($summary['totalDiscount']) ?></p>
                        <p><?php echo $labels['delivery_charge']; ?>: <?php echo formatNumber($summary['totalDeliveryCharge']) ?></p>
                        <p><?php echo $labels['tax']; ?>: <?php echo formatNumber($summary['totalTax']) ?></p>

                        <h4 style="margin: 5px 0px; padding: 5px 0px 5px 5px; background: #e8e3e0;"><?php echo $labels['total']; ?>: <?php echo
                                                                                                                                        $currency ?
                                                                                                                                            ($isCurrencyPrefix ?
                                                                                                                                                $currency . formatNumber($summary['payableAmount']) : formatNumber($summary['payableAmount']) . $currency) :
                                                                                                                                            formatNumber($summary['payableAmount'])
                                                                                                                                        ?></h4>
                    </div>
                    <div style="margin:40px 0px;">
                        <h2><?php echo $labels['thank_you']; ?></h2>
                        <p style="color: #e55c28; font-weight:600;"><?php echo $companyName; ?></p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>