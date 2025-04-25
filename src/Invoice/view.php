<?php
function formatNumber(float $number, int $decimals = 2): string
{
    return str_contains($number, '.') ? number_format($number, $decimals) : number_format($number);
}
?>

<html lang="<?php echo $lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        @font-face {
            font-family: 'Manrope';
            font-style: normal;
            font-weight: 200;
            src: url('https://fonts.gstatic.com/s/manrope/v9/xn7gYHE41ni1AdIRkgpOjs0bCw.woff2') format('woff2');
        }

        @font-face {
            font-family: 'Manrope';
            font-style: normal;
            font-weight: 800;
            src: url('https://fonts.gstatic.com/s/manrope/v9/xn7gYHE41ni1AdIRmgpOjs0bCw.woff2') format('woff2');
        }
    </style>
</head>

<body style="font-family:'Manrope',sans-serif; background: #fafafa; padding: 0px 0;">
    <?php
    $rtl ? require __DIR__ . '/rtl.php' : require __DIR__ . '/ltr.php';
    ?>
</body>

<script>
    function downloadInvoice() {
        const invoiceHtml = document.getElementById('invoice-content').outerHTML;
        const blob = new Blob([invoiceHtml], {
            type: 'text/html'
        });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = '<?php echo 'invoice_' . $invoiceNumber . '.html'; ?>';
        a.click();
        URL.revokeObjectURL(url);
    }
</script>

</html>