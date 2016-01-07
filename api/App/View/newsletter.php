<?php
$httpPath = LOCALE != '' ? 'http://www.flipit.com/' : 'http://www.kortingscode.nl/';
$httpPathLocale = LOCALE != '' ? 'http://www.flipit.com/'.LOCALE.'/' : 'http://www.kortingscode.nl/';
$publicPathCdn = LOCALE != '' ? 'http://img.flipit.com/public/'.LOCALE.'/' : 'http://img.kortingscode.nl/public/';
$publicLocalePath = LOCALE != '' ? $httpPath.'public/'.LOCALE.'/images/front_end/' : $httpPath.'public/images/front_end/';
$websiteName = LOCALE != '' ? 'Flipit' : 'Kortingscode';
$publicPath = $httpPath.'public/images/front_end/';
if (fopen($publicLocalePath.'emails/email-header-best.png', 'r')) {
    $emailLogo = $publicLocalePath.'emails/email-header-best.png';
} else {
    $emailLogo = LOCALE != '' ? $publicPath.'emails/email-header-best-flipit.png' : $publicPath.'emails/email-header-best.png';
}

echo $emailLogo; exit;
?>

<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#f0f0f0">
<tbody>
<tr>
    <td bgcolor="#32383e" align="center" style="padding:12px 0 15px; font:12px/14px Arial,Helvetica,sans-serif; color:#fffefe">
        <div>Descuentos de <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiNjlHeld1TmkyY3c0dWR5YWQ1T1FWRHdmampJIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwva2lhYmlcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjVkOWFiYzZkMTY5NjViMTc4ODIxYWYzODY0N2NiOWU4ZjAyYThiMzRcIl19In0" target="_blank" style="color:#fff">Kiabi</a>, <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiOVlzV2hBaF81UVNiZzQydUNSTzFuV0VySndRIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvYW1hem9uXCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCJhOWNlMTFmN2I0YzkxMjc3YzkyNjkzMWE3NTIwOGVlNGY4OWY1YTM0XCJdfSJ9" target="_blank" style="color:#fff">Amazon</a>, <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiNy05OUtjSi00WnF2bHg1OWdqSi1ZaTNZb3FrIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbm9ydG9uXCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCJlNjg4ZThmMGVmOGJlZjMyZTMyYzhhMTZlZGNkOTJiMGM1ODJiODdkXCJdfSJ9" target="_blank" style="color:#fff">Norton</a> <br>
            Si tiene problemas para ver este mensaje, haga <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiR0dkMUhvZlBlcS1HZld5QlZYRF9GSmZzS1NJIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcImJlY2IyZGYxOTcxODA5ZmVjNGNkOGE0ZTQzODM0YTY0MzVkNDBhOWVcIl19In0" target="_blank" style="color:#fff">clic aquí para la versión web.</a>.</div>
    </td>
</tr>
<tr>
<td>
<table width="100%" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td valign="top" width="50%">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td height="140" bgcolor="#0077cc">&nbsp;</td>
        </tr>
        </tbody>
    </table>
</td>
<td valign="top" width="600">
<div>
<table width="100%" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td>
<table width="100%" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
    <td bgcolor="#0077cc">
        <table width="600" align="center" bgcolor="#0077cc" cellpadding="0" cellspacing="0" style="">
            <tbody>
            <tr>
                <td colspan="2" height="22"></td>
            </tr>
            <tr>
                <td width="205"></td>
                <td width="431"><a href="<?php echo rtrim($httpPathLocale, '/'); ?>" target="_blank"><img src="<?php echo $emailLogo; ?>" border="0" alt="Flipit" title="<?php echo $websiteName; ?>" style="vertical-align:top"></a> </td>
            </tr>
            <tr>
                <td colspan="2" height="20"></td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<?php
$haderBanner = $newsletterCampaign->getHeaderBanner();
if(!empty($haderBanner)) { ?>
<tr>
    <td bgcolor="#0077cc">
        <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiT2V4ZmNRazJPSDN2WFpPUkNWMWFsbHd2b2JFIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdnVlbGluZz91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNlwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiZWExNGY3YjQwNzAwOTJjMzMxZDFjNDhkNzZjNmU3Y2NlZGFkMGFiYVwiXX0ifQ" target="_blank">
            <img src="http://img.flipit.com/public/es/images/upload/newsletterbannerimages/1452158283_banner vueling.jpg" width="615" alt="<?php echo urldecode($haderBanner); ?>" title="<?php echo urldecode($haderBanner); ?>" style="display:block">
        </a>
    </td>
</tr>
<?php } ?>
<tr>
    <td height="5"></td>
</tr>
<tr>
<td bgcolor="#ffffff" style="border:1px solid #e4e4e4; border-radius:5px">
<table width="100%" cellpadding="0" cellspacing="0">
<tbody>
<tr>
    <td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td width="132">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; border-radius:5px">
                        <tbody>
                        <tr>
                            <td height="66" align="center"><a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiMGFCSTJOVHRjQ2hYYXBOb09mSU5OWXFYNUdBIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdmVuY2E_dXRtX3NvdXJjZT10cmFuc2FjdGlvbmFsJnV0bV9tZWRpdW09ZW1haWwmdXRtX2NhbXBhaWduPTA3LTAxLTIwMTYmdHlwZT1jb2RlIzMxMjQ3XCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCIxNmI2ZjcxNzFhMmZiNTNmZmQxOGVmNDdiZDE3NTZjOGY5MTlmNmI5XCJdfSJ9" target="_blank" style="text-decoration:none; color:#fff"><img src="http://img.flipit.com/public/es/images/upload/shop/thum_big_1379948334_vencashop.png" width="132" height="66" alt="Venca" title="Venca" style="vertical-align:top"></a></td>
                        </tr>
                        <tr>
                            <td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; padding:6px 0 5px; font:11px/17px Arial,Helvetica,sans-serif; color:#fff">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiMGFCSTJOVHRjQ2hYYXBOb09mSU5OWXFYNUdBIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdmVuY2E_dXRtX3NvdXJjZT10cmFuc2FjdGlvbmFsJnV0bV9tZWRpdW09ZW1haWwmdXRtX2NhbXBhaWduPTA3LTAxLTIwMTYmdHlwZT1jb2RlIzMxMjQ3XCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCIxNmI2ZjcxNzFhMmZiNTNmZmQxOGVmNDdiZDE3NTZjOGY5MTlmNmI5XCJdfSJ9" target="_blank" style="text-decoration:none; color:#fff">Código</a></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td width="30"></td>
                <td valign="top">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td style="padding:0 0 2px; font:14px/17px Arial,Helvetica,sans-serif; color:#363636">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiUmM1bEZpdVVoWnNaX1BmenZEUFhkQlNEaExrIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdmVuY2FcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjE2YjZmNzE3MWEyZmI1M2ZmZDE4ZWY0N2JkMTc1NmM4ZjkxOWY2YjlcIl19In0" target="_blank" style="text-decoration:none; color:#363636">Venca</a>&nbsp;<span style="color:#e89438; display:inline-block"></span></td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 15px; font:16px/22px Arial,Helvetica,sans-serif; color:#0077cc">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiMGFCSTJOVHRjQ2hYYXBOb09mSU5OWXFYNUdBIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdmVuY2E_dXRtX3NvdXJjZT10cmFuc2FjdGlvbmFsJnV0bV9tZWRpdW09ZW1haWwmdXRtX2NhbXBhaWduPTA3LTAxLTIwMTYmdHlwZT1jb2RlIzMxMjQ3XCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCIxNmI2ZjcxNzFhMmZiNTNmZmQxOGVmNDdiZDE3NTZjOGY5MTlmNmI5XCJdfSJ9" target="_blank" style="text-decoration:underline; color:#0077cc">Ahorra con este codigo descuento Venca un 30% en tu compra</a></td>
                        </tr>
                        <tr>
                            <td style="font:12px/17px Arial,Helvetica,sans-serif; color:#32383e">Válido desde 29 Diciembre hasta 31 De Enero De 2016</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr>
    <td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td width="132">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; border-radius:5px">
                        <tbody>
                        <tr>
                            <td height="66" align="center"><a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiQzhNemVDUnJ3Sk1nRnM4LWlCeWFVN2NkQl9NIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbmlrZT91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEyOTdcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcImM2MWNiZGFjY2QzMTk0OGY2MWJhYzMyMTUyNTM3YjY0NTJhMzBmNGJcIl19In0" target="_blank" style="text-decoration:none; color:#fff"><img src="http://img.flipit.com/public/es/images/upload/shop/thum_big_1436177616_nike.png" width="132" height="66" alt="Nike" title="Nike" style="vertical-align:top"></a></td>
                        </tr>
                        <tr>
                            <td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; padding:6px 0 5px; font:11px/17px Arial,Helvetica,sans-serif; color:#fff">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiQzhNemVDUnJ3Sk1nRnM4LWlCeWFVN2NkQl9NIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbmlrZT91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEyOTdcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcImM2MWNiZGFjY2QzMTk0OGY2MWJhYzMyMTUyNTM3YjY0NTJhMzBmNGJcIl19In0" target="_blank" style="text-decoration:none; color:#fff">Código</a></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td width="30"></td>
                <td valign="top">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td style="padding:0 0 2px; font:14px/17px Arial,Helvetica,sans-serif; color:#363636">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoicW1WanJGU1Fmb3dqS0Z5UTZCdUtDaFZvMTZ3IiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbmlrZVwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiYzYxY2JkYWNjZDMxOTQ4ZjYxYmFjMzIxNTI1MzdiNjQ1MmEzMGY0YlwiXX0ifQ" target="_blank" style="text-decoration:none; color:#363636">Nike</a>&nbsp;<span style="color:#e89438; display:inline-block"></span></td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 15px; font:16px/22px Arial,Helvetica,sans-serif; color:#0077cc">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiQzhNemVDUnJ3Sk1nRnM4LWlCeWFVN2NkQl9NIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbmlrZT91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEyOTdcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcImM2MWNiZGFjY2QzMTk0OGY2MWJhYzMyMTUyNTM3YjY0NTJhMzBmNGJcIl19In0" target="_blank" style="text-decoration:underline; color:#0077cc">Descuento de 15% extra al comprar tus Nike Trainerendor + 30% en la web con el codigo descuento Nike</a></td>
                        </tr>
                        <tr>
                            <td style="font:12px/17px Arial,Helvetica,sans-serif; color:#32383e">Válido desde 29 Diciembre hasta 10 De Enero De 2016</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr>
    <td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td width="132">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; border-radius:5px">
                        <tbody>
                        <tr>
                            <td height="66" align="center"><a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoicDFVYXd5MWxSMUhRckNkN0VJTzNwVUk0OFVVIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvcHJvbW9jaW9uZXMtZmFybWE_dXRtX3NvdXJjZT10cmFuc2FjdGlvbmFsJnV0bV9tZWRpdW09ZW1haWwmdXRtX2NhbXBhaWduPTA3LTAxLTIwMTYmdHlwZT1jb2RlIzMwNTExXCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCI0M2IwNzYzNmY1NTdhOGIzZTg4YjJlYzkxYTRhNDc5NDNmYTk3YmFiXCJdfSJ9" target="_blank" style="text-decoration:none; color:#fff"><img src="http://img.flipit.com/public/es/images/upload/shop/thum_big_1378725003_promofarma.png" width="132" height="66" alt="PromoFarma" title="PromoFarma" style="vertical-align:top"></a></td>
                        </tr>
                        <tr>
                            <td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; padding:6px 0 5px; font:11px/17px Arial,Helvetica,sans-serif; color:#fff">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoicDFVYXd5MWxSMUhRckNkN0VJTzNwVUk0OFVVIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvcHJvbW9jaW9uZXMtZmFybWE_dXRtX3NvdXJjZT10cmFuc2FjdGlvbmFsJnV0bV9tZWRpdW09ZW1haWwmdXRtX2NhbXBhaWduPTA3LTAxLTIwMTYmdHlwZT1jb2RlIzMwNTExXCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCI0M2IwNzYzNmY1NTdhOGIzZTg4YjJlYzkxYTRhNDc5NDNmYTk3YmFiXCJdfSJ9" target="_blank" style="text-decoration:none; color:#fff">Código</a></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td width="30"></td>
                <td valign="top">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td style="padding:0 0 2px; font:14px/17px Arial,Helvetica,sans-serif; color:#363636">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoia3pzTThQMVpxclpwLTNfMDYwTzFuMkI0aUxjIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvcHJvbW9jaW9uZXMtZmFybWFcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjQzYjA3NjM2ZjU1N2E4YjNlODhiMmVjOTFhNGE0Nzk0M2ZhOTdiYWJcIl19In0" target="_blank" style="text-decoration:none; color:#363636">PromoFarma</a>&nbsp;<span style="color:#e89438; display:inline-block"></span></td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 15px; font:16px/22px Arial,Helvetica,sans-serif; color:#0077cc">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoicDFVYXd5MWxSMUhRckNkN0VJTzNwVUk0OFVVIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvcHJvbW9jaW9uZXMtZmFybWE_dXRtX3NvdXJjZT10cmFuc2FjdGlvbmFsJnV0bV9tZWRpdW09ZW1haWwmdXRtX2NhbXBhaWduPTA3LTAxLTIwMTYmdHlwZT1jb2RlIzMwNTExXCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCI0M2IwNzYzNmY1NTdhOGIzZTg4YjJlYzkxYTRhNDc5NDNmYTk3YmFiXCJdfSJ9" target="_blank" style="text-decoration:underline; color:#0077cc">Ahorra un 5% en tu pedido online con este cupon descuento PromoFarma</a></td>
                        </tr>
                        <tr>
                            <td style="font:12px/17px Arial,Helvetica,sans-serif; color:#32383e">Válido desde 02 Diciembre hasta 1 De Agosto De 2016</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr style="">
    <td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td width="132">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; border-radius:5px">
                        <tbody>
                        <tr>
                            <td height="66" align="center"><a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiRElXdzVBbThYQ2xHY3RSRUpsWGE4MVVXNWVrIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdmlzaW9uLWRpcmVjdD91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEyNTJcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjU4MTgxZWFmMDY4MmJlNmRkOWZjNmQ5YTk2OGExYjZiZmI2MjM4MjRcIl19In0" target="_blank" style="text-decoration:none; color:#fff"><img src="http://img.flipit.com/public/es/images/upload/shop/thum_big_1416582827_visiondirect.png" width="132" height="66" alt="Vision Direct" title="Vision Direct" style="vertical-align:top"></a></td>
                        </tr>
                        <tr>
                            <td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; padding:6px 0 5px; font:11px/17px Arial,Helvetica,sans-serif; color:#fff">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiRElXdzVBbThYQ2xHY3RSRUpsWGE4MVVXNWVrIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdmlzaW9uLWRpcmVjdD91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEyNTJcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjU4MTgxZWFmMDY4MmJlNmRkOWZjNmQ5YTk2OGExYjZiZmI2MjM4MjRcIl19In0" target="_blank" style="text-decoration:none; color:#fff">Código</a></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td width="30"></td>
                <td valign="top">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td style="padding:0 0 2px; font:14px/17px Arial,Helvetica,sans-serif; color:#363636">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiYzVzMG1EVVVRdzRqY0Q1MnF5LWpnV3ZYSURrIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdmlzaW9uLWRpcmVjdFwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiNTgxODFlYWYwNjgyYmU2ZGQ5ZmM2ZDlhOTY4YTFiNmJmYjYyMzgyNFwiXX0ifQ" target="_blank" style="text-decoration:none; color:#363636">Vision Direct</a>&nbsp;<span style="color:#e89438; display:inline-block">Sólo en Flipit</span></td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 15px; font:16px/22px Arial,Helvetica,sans-serif; color:#0077cc">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiRElXdzVBbThYQ2xHY3RSRUpsWGE4MVVXNWVrIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdmlzaW9uLWRpcmVjdD91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEyNTJcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjU4MTgxZWFmMDY4MmJlNmRkOWZjNmQ5YTk2OGExYjZiZmI2MjM4MjRcIl19In0" target="_blank" style="text-decoration:underline; color:#0077cc">Aprovecha las rebajas con el cupón descuento Vision Direct -12% en tus lentillas</a></td>
                        </tr>
                        <tr>
                            <td style="font:12px/17px Arial,Helvetica,sans-serif; color:#32383e">Válido desde 31 Diciembre hasta 31 De Enero De 2016</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr style="">
    <td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td width="132">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; border-radius:5px">
                        <tbody>
                        <tr>
                            <td height="66" align="center"><a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiZklFcjRpaThVQ0l2ZGJMSmQ3ZGRsdlNKUXdzIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbG9va2ZhbnRhc3RpYy1lcz91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEzODFcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjVhMWM0Njg3ZmViNzg4Y2Y4ZTIzODVlNDVkMjM1MWZlYWI1MWJiYzNcIl19In0" target="_blank" style="text-decoration:none; color:#fff"><img src="http://img.flipit.com/public/es/images/upload/shop/thum_big_1406041366_Lookfantastic.png" width="132" height="66" alt="Lookfantastic" title="Lookfantastic" style="vertical-align:top"></a></td>
                        </tr>
                        <tr>
                            <td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; padding:6px 0 5px; font:11px/17px Arial,Helvetica,sans-serif; color:#fff">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiZklFcjRpaThVQ0l2ZGJMSmQ3ZGRsdlNKUXdzIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbG9va2ZhbnRhc3RpYy1lcz91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEzODFcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjVhMWM0Njg3ZmViNzg4Y2Y4ZTIzODVlNDVkMjM1MWZlYWI1MWJiYzNcIl19In0" target="_blank" style="text-decoration:none; color:#fff">Código</a></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td width="30"></td>
                <td valign="top">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td style="padding:0 0 2px; font:14px/17px Arial,Helvetica,sans-serif; color:#363636">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiYzdXd2dfc0pHZGo0Mi05dnFqYzEtVWx6d0g0IiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbG9va2ZhbnRhc3RpYy1lc1wiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiNWExYzQ2ODdmZWI3ODhjZjhlMjM4NWU0NWQyMzUxZmVhYjUxYmJjM1wiXX0ifQ" target="_blank" style="text-decoration:none; color:#363636">Lookfantastic</a>&nbsp;<span style="color:#e89438; display:inline-block">Sólo en Flipit</span></td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 15px; font:16px/22px Arial,Helvetica,sans-serif; color:#0077cc">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiZklFcjRpaThVQ0l2ZGJMSmQ3ZGRsdlNKUXdzIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbG9va2ZhbnRhc3RpYy1lcz91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEzODFcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjVhMWM0Njg3ZmViNzg4Y2Y4ZTIzODVlNDVkMjM1MWZlYWI1MWJiYzNcIl19In0" target="_blank" style="text-decoration:underline; color:#0077cc">Código promocional Lookfantastic -17% registrándote en la web</a></td>
                        </tr>
                        <tr>
                            <td style="font:12px/17px Arial,Helvetica,sans-serif; color:#32383e">Válido desde 04 Enero hasta 10 De Enero De 2016</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr>
    <td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td width="132">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; border-radius:5px">
                        <tbody>
                        <tr>
                            <td height="66" align="center"><a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiZUZxSm0wWlRMMVBCS0Z5dzNzNUVEVkFZSk9BIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvcHJpdmF0ZW91dGxldD91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzE0NjdcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjVhZmQwZmYwZWYzNzk3MGU4NDg2ZmY2NjE5MDhkODIxMTk3NmI1NThcIl19In0" target="_blank" style="text-decoration:none; color:#fff"><img src="http://img.flipit.com/public/es/images/upload/shop/thum_big_1420183743_brandalley.png" width="132" height="66" alt="BrandAlley" title="BrandAlley" style="vertical-align:top"></a></td>
                        </tr>
                        <tr>
                            <td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; padding:6px 0 5px; font:11px/17px Arial,Helvetica,sans-serif; color:#fff">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiZUZxSm0wWlRMMVBCS0Z5dzNzNUVEVkFZSk9BIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvcHJpdmF0ZW91dGxldD91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzE0NjdcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjVhZmQwZmYwZWYzNzk3MGU4NDg2ZmY2NjE5MDhkODIxMTk3NmI1NThcIl19In0" target="_blank" style="text-decoration:none; color:#fff">Código</a></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td width="30"></td>
                <td valign="top">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td style="padding:0 0 2px; font:14px/17px Arial,Helvetica,sans-serif; color:#363636">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiN1R4emkwdjZQa0c1bzIxaXRvR1l1M2ZXc3ZRIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvcHJpdmF0ZW91dGxldFwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiNWFmZDBmZjBlZjM3OTcwZTg0ODZmZjY2MTkwOGQ4MjExOTc2YjU1OFwiXX0ifQ" target="_blank" style="text-decoration:none; color:#363636">BrandAlley</a>&nbsp;<span style="color:#e89438; display:inline-block"></span></td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 15px; font:16px/22px Arial,Helvetica,sans-serif; color:#0077cc">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiZUZxSm0wWlRMMVBCS0Z5dzNzNUVEVkFZSk9BIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvcHJpdmF0ZW91dGxldD91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzE0NjdcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjVhZmQwZmYwZWYzNzk3MGU4NDg2ZmY2NjE5MDhkODIxMTk3NmI1NThcIl19In0" target="_blank" style="text-decoration:underline; color:#0077cc">Cupón descuento BrandAlley -11€ en tu pedido de moda y más</a></td>
                        </tr>
                        <tr>
                            <td style="font:12px/17px Arial,Helvetica,sans-serif; color:#32383e">Válido desde 05 Enero hasta 3 De Marzo De 2016</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr style="">
    <td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td width="132">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; border-radius:5px">
                        <tbody>
                        <tr>
                            <td height="66" align="center"><a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiNW1Mc2RmYno0UDk4ejhJWXZmSHhCSnNEdzNNIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbXktcHJvdGVpbj91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEyNDhcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcImU1OTVhNzhmZGRkZjk4OTEwMTcyZTI0N2U1YjM3YzYzYjQzM2QwMjhcIl19In0" target="_blank" style="text-decoration:none; color:#fff"><img src="http://img.flipit.com/public/es/images/upload/shop/thum_big_1373275255_myprtein_logo.png" width="132" height="66" alt="Myprotein" title="Myprotein" style="vertical-align:top"></a></td>
                        </tr>
                        <tr>
                            <td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; padding:6px 0 5px; font:11px/17px Arial,Helvetica,sans-serif; color:#fff">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiNW1Mc2RmYno0UDk4ejhJWXZmSHhCSnNEdzNNIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbXktcHJvdGVpbj91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEyNDhcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcImU1OTVhNzhmZGRkZjk4OTEwMTcyZTI0N2U1YjM3YzYzYjQzM2QwMjhcIl19In0" target="_blank" style="text-decoration:none; color:#fff">Código</a></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td width="30"></td>
                <td valign="top">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td style="padding:0 0 2px; font:14px/17px Arial,Helvetica,sans-serif; color:#363636">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoidlN5Y0Zia2ZiSlJQaE1FQkNpTjUzX1VpTjVrIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbXktcHJvdGVpblwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiZTU5NWE3OGZkZGRmOTg5MTAxNzJlMjQ3ZTViMzdjNjNiNDMzZDAyOFwiXX0ifQ" target="_blank" style="text-decoration:none; color:#363636">Myprotein</a>&nbsp;<span style="color:#e89438; display:inline-block">Sólo en Flipit</span></td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 15px; font:16px/22px Arial,Helvetica,sans-serif; color:#0077cc">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiNW1Mc2RmYno0UDk4ejhJWXZmSHhCSnNEdzNNIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbXktcHJvdGVpbj91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEyNDhcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcImU1OTVhNzhmZGRkZjk4OTEwMTcyZTI0N2U1YjM3YzYzYjQzM2QwMjhcIl19In0" target="_blank" style="text-decoration:underline; color:#0077cc">Código descuento Myprotein: -15% de descuento en toda la tienda</a></td>
                        </tr>
                        <tr>
                            <td style="font:12px/17px Arial,Helvetica,sans-serif; color:#32383e">Válido desde 27 Diciembre hasta 31 De Enero De 2016</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr style="">
    <td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td width="132">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; border-radius:5px">
                        <tbody>
                        <tr>
                            <td height="66" align="center"><a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiYUQ5SjNqX2ptb2JHWktNUlRZWVJJX0pGYTNjIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbWktcm9wYS1wcmVtYW1hP3V0bV9zb3VyY2U9dHJhbnNhY3Rpb25hbCZ1dG1fbWVkaXVtPWVtYWlsJnV0bV9jYW1wYWlnbj0wNy0wMS0yMDE2JnR5cGU9Y29kZSMzMTUwMVwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiN2IyMzg3NTA1NDJjYzNhMDY2ZGY0MjA4YzdjNGI3ZjJlZjFjYTczMVwiXX0ifQ" target="_blank" style="text-decoration:none; color:#fff"><img src="http://img.flipit.com/public/es/images/upload/shop/thum_big_1434460126_miropapremama.jpg" width="132" height="66" alt="Mi Ropa Premamá" title="Mi Ropa Premamá" style="vertical-align:top"></a></td>
                        </tr>
                        <tr>
                            <td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; padding:6px 0 5px; font:11px/17px Arial,Helvetica,sans-serif; color:#fff">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiYUQ5SjNqX2ptb2JHWktNUlRZWVJJX0pGYTNjIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbWktcm9wYS1wcmVtYW1hP3V0bV9zb3VyY2U9dHJhbnNhY3Rpb25hbCZ1dG1fbWVkaXVtPWVtYWlsJnV0bV9jYW1wYWlnbj0wNy0wMS0yMDE2JnR5cGU9Y29kZSMzMTUwMVwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiN2IyMzg3NTA1NDJjYzNhMDY2ZGY0MjA4YzdjNGI3ZjJlZjFjYTczMVwiXX0ifQ" target="_blank" style="text-decoration:none; color:#fff">Código</a></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td width="30"></td>
                <td valign="top">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td style="padding:0 0 2px; font:14px/17px Arial,Helvetica,sans-serif; color:#363636">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiVFNfOG1HMEhyNDZoak9rNkN4RWlzY1FPdU9zIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbWktcm9wYS1wcmVtYW1hXCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCI3YjIzODc1MDU0MmNjM2EwNjZkZjQyMDhjN2M0YjdmMmVmMWNhNzMxXCJdfSJ9" target="_blank" style="text-decoration:none; color:#363636">Mi Ropa Premamá</a>&nbsp;<span style="color:#e89438; display:inline-block">Sólo en Flipit</span></td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 15px; font:16px/22px Arial,Helvetica,sans-serif; color:#0077cc">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiYUQ5SjNqX2ptb2JHWktNUlRZWVJJX0pGYTNjIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbWktcm9wYS1wcmVtYW1hP3V0bV9zb3VyY2U9dHJhbnNhY3Rpb25hbCZ1dG1fbWVkaXVtPWVtYWlsJnV0bV9jYW1wYWlnbj0wNy0wMS0yMDE2JnR5cGU9Y29kZSMzMTUwMVwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiN2IyMzg3NTA1NDJjYzNhMDY2ZGY0MjA4YzdjNGI3ZjJlZjFjYTczMVwiXX0ifQ" target="_blank" style="text-decoration:underline; color:#0077cc">Codigo descuento Mi Ropa Premamá -10% en todas tus compras</a></td>
                        </tr>
                        <tr>
                            <td style="font:12px/17px Arial,Helvetica,sans-serif; color:#32383e">Válido desde 06 Enero hasta 31 De Enero De 2016</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr>
    <td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td width="132">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; border-radius:5px">
                        <tbody>
                        <tr>
                            <td height="66" align="center"><a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiWnJQUk8tTlVxUTJyZXF2bkxTODdpUS1veWRJIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbGV0c2JvbnVzP3V0bV9zb3VyY2U9dHJhbnNhY3Rpb25hbCZ1dG1fbWVkaXVtPWVtYWlsJnV0bV9jYW1wYWlnbj0wNy0wMS0yMDE2JnR5cGU9Y29kZSMzMDY4NVwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiMmQ5OTg1ZjhjZWYzNmIyZTg1ODgxYmJlNTVkZmRkN2I1ZjMwNzVmZVwiXX0ifQ" target="_blank" style="text-decoration:none; color:#fff"><img src="http://img.flipit.com/public/es/images/upload/shop/thum_big_1380102334_letsbonus.png" width="132" height="66" alt="LetsBonus" title="LetsBonus" style="vertical-align:top"></a></td>
                        </tr>
                        <tr>
                            <td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; padding:6px 0 5px; font:11px/17px Arial,Helvetica,sans-serif; color:#fff">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiWnJQUk8tTlVxUTJyZXF2bkxTODdpUS1veWRJIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbGV0c2JvbnVzP3V0bV9zb3VyY2U9dHJhbnNhY3Rpb25hbCZ1dG1fbWVkaXVtPWVtYWlsJnV0bV9jYW1wYWlnbj0wNy0wMS0yMDE2JnR5cGU9Y29kZSMzMDY4NVwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiMmQ5OTg1ZjhjZWYzNmIyZTg1ODgxYmJlNTVkZmRkN2I1ZjMwNzVmZVwiXX0ifQ" target="_blank" style="text-decoration:none; color:#fff">Código</a></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td width="30"></td>
                <td valign="top">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td style="padding:0 0 2px; font:14px/17px Arial,Helvetica,sans-serif; color:#363636">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiVnVTTk84bzhKXzktVTl5TGJQREdDYThydzJjIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbGV0c2JvbnVzXCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCIyZDk5ODVmOGNlZjM2YjJlODU4ODFiYmU1NWRmZGQ3YjVmMzA3NWZlXCJdfSJ9" target="_blank" style="text-decoration:none; color:#363636">LetsBonus</a>&nbsp;<span style="color:#e89438; display:inline-block"></span></td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 15px; font:16px/22px Arial,Helvetica,sans-serif; color:#0077cc">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiWnJQUk8tTlVxUTJyZXF2bkxTODdpUS1veWRJIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbGV0c2JvbnVzP3V0bV9zb3VyY2U9dHJhbnNhY3Rpb25hbCZ1dG1fbWVkaXVtPWVtYWlsJnV0bV9jYW1wYWlnbj0wNy0wMS0yMDE2JnR5cGU9Y29kZSMzMDY4NVwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiMmQ5OTg1ZjhjZWYzNmIyZTg1ODgxYmJlNTVkZmRkN2I1ZjMwNzVmZVwiXX0ifQ" target="_blank" style="text-decoration:underline; color:#0077cc">Disfruta de -10% en los planes de Salud y Belleza con el código promocional Letsbonus</a></td>
                        </tr>
                        <tr>
                            <td style="font:12px/17px Arial,Helvetica,sans-serif; color:#32383e">Válido desde 07 Diciembre hasta 10 De Enero De 2016</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr style="">
    <td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td width="132">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; border-radius:5px">
                        <tbody>
                        <tr>
                            <td height="66" align="center"><a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiNTRCZWRWNWxELS1TNGczYU1ZTTNCSDBNMTdNIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvZGVzaWd1YWw_dXRtX3NvdXJjZT10cmFuc2FjdGlvbmFsJnV0bV9tZWRpdW09ZW1haWwmdXRtX2NhbXBhaWduPTA3LTAxLTIwMTYmdHlwZT1jb2RlIzMwNDk0XCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCIzNGZmM2ZmOGI1NjBhYzg0NzcyNDdjNWIwYTk2ZDlhNjBmMjFiZTQ4XCJdfSJ9" target="_blank" style="text-decoration:none; color:#fff"><img src="http://img.flipit.com/public/es/images/upload/shop/thum_big_1369065324_desigual.png" width="132" height="66" alt="Desigual" title="Desigual" style="vertical-align:top"></a></td>
                        </tr>
                        <tr>
                            <td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; padding:6px 0 5px; font:11px/17px Arial,Helvetica,sans-serif; color:#fff">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiNTRCZWRWNWxELS1TNGczYU1ZTTNCSDBNMTdNIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvZGVzaWd1YWw_dXRtX3NvdXJjZT10cmFuc2FjdGlvbmFsJnV0bV9tZWRpdW09ZW1haWwmdXRtX2NhbXBhaWduPTA3LTAxLTIwMTYmdHlwZT1jb2RlIzMwNDk0XCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCIzNGZmM2ZmOGI1NjBhYzg0NzcyNDdjNWIwYTk2ZDlhNjBmMjFiZTQ4XCJdfSJ9" target="_blank" style="text-decoration:none; color:#fff">Código</a></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td width="30"></td>
                <td valign="top">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td style="padding:0 0 2px; font:14px/17px Arial,Helvetica,sans-serif; color:#363636">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoieVZlSnA1bnIyWm1iSlY2UlpzVS1IMVVvazNZIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvZGVzaWd1YWxcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjM0ZmYzZmY4YjU2MGFjODQ3NzI0N2M1YjBhOTZkOWE2MGYyMWJlNDhcIl19In0" target="_blank" style="text-decoration:none; color:#363636">Desigual</a>&nbsp;<span style="color:#e89438; display:inline-block">Sólo en Flipit</span></td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 15px; font:16px/22px Arial,Helvetica,sans-serif; color:#0077cc">
                                <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiNTRCZWRWNWxELS1TNGczYU1ZTTNCSDBNMTdNIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvZGVzaWd1YWw_dXRtX3NvdXJjZT10cmFuc2FjdGlvbmFsJnV0bV9tZWRpdW09ZW1haWwmdXRtX2NhbXBhaWduPTA3LTAxLTIwMTYmdHlwZT1jb2RlIzMwNDk0XCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCIzNGZmM2ZmOGI1NjBhYzg0NzcyNDdjNWIwYTk2ZDlhNjBmMjFiZTQ4XCJdfSJ9" target="_blank" style="text-decoration:underline; color:#0077cc">15% de descuento en tu pedido con el cupón promocional Desigual</a></td>
                        </tr>
                        <tr>
                            <td style="font:12px/17px Arial,Helvetica,sans-serif; color:#32383e">Válido desde 02 Diciembre hasta 10 De Enero De 2016</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr>
    <td bgcolor="#fafafa" align="center" style="border-radius:0 0 4px 4px; padding:12px 0 11px; font:bold 14px/17px Arial,Helvetica,sans-serif; color:#373736">
        <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiNUwtclAzYURFOVRnOEUtUnFPelJJemhOWHFrIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdG9wLTUwP3V0bV9zb3VyY2U9dHJhbnNhY3Rpb25hbCZ1dG1fbWVkaXVtPWVtYWlsJnV0bV9jYW1wYWlnbj0wNy0wMS0yMDE2JnR5cGU9Y29kZSMzMDQ5NFwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiOGU1YjhkMTk1NjljMDExNmJhOWFkZDVhYjVhNjM0NDQ1Y2M3YTI3NFwiXX0ifQ" target="_blank" style="text-decoration:none; color:#373736">Ver más descuentos populares &gt;</a></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
    <td height="45"></td>
</tr>
<tr>
    <td style="padding:0 0 25px 29px; font:24px/27px Arial,Helvetica,sans-serif; color:#32383e">
        Categoría popular: Bebés &amp; Niños </td>
</tr>
<tr>
    <td bgcolor="#ffffff" style="border:1px solid #e4e4e4; border-radius:5px">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
            <tr style="">
                <td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td width="132">
                                <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; border-radius:5px">
                                    <tbody>
                                    <tr>
                                        <td height="66" align="center"><a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiYUQ5SjNqX2ptb2JHWktNUlRZWVJJX0pGYTNjIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbWktcm9wYS1wcmVtYW1hP3V0bV9zb3VyY2U9dHJhbnNhY3Rpb25hbCZ1dG1fbWVkaXVtPWVtYWlsJnV0bV9jYW1wYWlnbj0wNy0wMS0yMDE2JnR5cGU9Y29kZSMzMTUwMVwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiN2IyMzg3NTA1NDJjYzNhMDY2ZGY0MjA4YzdjNGI3ZjJlZjFjYTczMVwiXX0ifQ" target="_blank" style="text-decoration:none; color:#fff"><img src="http://img.flipit.com/public/es/images/upload/shop/thum_big_1434460126_miropapremama.jpg" width="132" height="66" alt="Mi Ropa Premamá" title="Mi Ropa Premamá" style="vertical-align:top"></a></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; padding:6px 0 5px; font:11px/17px Arial,Helvetica,sans-serif; color:#fff">
                                            <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiYUQ5SjNqX2ptb2JHWktNUlRZWVJJX0pGYTNjIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbWktcm9wYS1wcmVtYW1hP3V0bV9zb3VyY2U9dHJhbnNhY3Rpb25hbCZ1dG1fbWVkaXVtPWVtYWlsJnV0bV9jYW1wYWlnbj0wNy0wMS0yMDE2JnR5cGU9Y29kZSMzMTUwMVwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiN2IyMzg3NTA1NDJjYzNhMDY2ZGY0MjA4YzdjNGI3ZjJlZjFjYTczMVwiXX0ifQ" target="_blank" style="text-decoration:none; color:#fff">Código</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="30"></td>
                            <td valign="top">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    <tr>
                                        <td style="padding:0 0 2px; font:14px/17px Arial,Helvetica,sans-serif; color:#363636">
                                            <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiVFNfOG1HMEhyNDZoak9rNkN4RWlzY1FPdU9zIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbWktcm9wYS1wcmVtYW1hXCIsXCJpZFwiOlwiZTFjNTI0ZGZjODRiNGU1MDg2YmM4ODgxMzZiZmQxOGRcIixcInVybF9pZHNcIjpbXCI3YjIzODc1MDU0MmNjM2EwNjZkZjQyMDhjN2M0YjdmMmVmMWNhNzMxXCJdfSJ9" target="_blank" style="text-decoration:none; color:#363636">Mi Ropa Premamá</a>&nbsp;<span style="color:#e89438; display:inline-block">Sólo en Flipit</span></td>
                                    </tr>
                                    <tr>
                                        <td style="padding:0 0 15px; font:16px/22px Arial,Helvetica,sans-serif; color:#0077cc">
                                            <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiYUQ5SjNqX2ptb2JHWktNUlRZWVJJX0pGYTNjIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbWktcm9wYS1wcmVtYW1hP3V0bV9zb3VyY2U9dHJhbnNhY3Rpb25hbCZ1dG1fbWVkaXVtPWVtYWlsJnV0bV9jYW1wYWlnbj0wNy0wMS0yMDE2JnR5cGU9Y29kZSMzMTUwMVwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiN2IyMzg3NTA1NDJjYzNhMDY2ZGY0MjA4YzdjNGI3ZjJlZjFjYTczMVwiXX0ifQ" target="_blank" style="text-decoration:underline; color:#0077cc">Codigo descuento Mi Ropa Premamá -10% en todas tus compras</a></td>
                                    </tr>
                                    <tr>
                                        <td style="font:12px/17px Arial,Helvetica,sans-serif; color:#32383e">Válido desde 06 Enero hasta 31 De Enero De 2016</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr style="">
                <td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td width="132">
                                <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; border-radius:5px">
                                    <tbody>
                                    <tr>
                                        <td height="66" align="center"><a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiaHBBV1JXWmp5bmEzTUV2WVgxaURtbmhHWU1RIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdG95c3J1cz91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMjkxMTFcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjc5YmVkODc4OTZkYTE0MTg3ZDA5ZDdjMWFmMDE3OGMzMWM3OTc3MTlcIl19In0" target="_blank" style="text-decoration:none; color:#fff"><img src="http://img.flipit.com/public/es/images/upload/shop/thum_big_1371654593_thum_medium_store_1360890160_toys-r-us.png" width="132" height="66" alt="ToysRus" title="ToysRus" style="vertical-align:top"></a></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; padding:6px 0 5px; font:11px/17px Arial,Helvetica,sans-serif; color:#fff">
                                            <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiaHBBV1JXWmp5bmEzTUV2WVgxaURtbmhHWU1RIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdG95c3J1cz91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMjkxMTFcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjc5YmVkODc4OTZkYTE0MTg3ZDA5ZDdjMWFmMDE3OGMzMWM3OTc3MTlcIl19In0" target="_blank" style="text-decoration:none; color:#fff">Código</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="30"></td>
                            <td valign="top">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    <tr>
                                        <td style="padding:0 0 2px; font:14px/17px Arial,Helvetica,sans-serif; color:#363636">
                                            <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoicGZEMUpwak1YVjBMVzdPT3RRNUVld1JDVmdRIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdG95c3J1c1wiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiNzliZWQ4Nzg5NmRhMTQxODdkMDlkN2MxYWYwMTc4YzMxYzc5NzcxOVwiXX0ifQ" target="_blank" style="text-decoration:none; color:#363636">ToysRus</a>&nbsp;<span style="color:#e89438; display:inline-block">Sólo en Flipit</span></td>
                                    </tr>
                                    <tr>
                                        <td style="padding:0 0 15px; font:16px/22px Arial,Helvetica,sans-serif; color:#0077cc">
                                            <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiaHBBV1JXWmp5bmEzTUV2WVgxaURtbmhHWU1RIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvdG95c3J1cz91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMjkxMTFcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjc5YmVkODc4OTZkYTE0MTg3ZDA5ZDdjMWFmMDE3OGMzMWM3OTc3MTlcIl19In0" target="_blank" style="text-decoration:underline; color:#0077cc">Código promocional ToysRus 7€ de descuento en todos los juguetes</a></td>
                                    </tr>
                                    <tr>
                                        <td style="font:12px/17px Arial,Helvetica,sans-serif; color:#32383e">Válido desde 31 Diciembre hasta 15 De Marzo De 2016</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr style="">
                <td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td width="132">
                                <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; border-radius:5px">
                                    <tbody>
                                    <tr>
                                        <td height="66" align="center"><a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiYmZERVQ0aDgxVkNsNXMxYXFhWGJLNXZ1cERFIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbGEtcmVkb3V0ZT91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEyNTRcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjUzZGUyMzFlNGVkNzU3MjY3ZjUzZDQ4MDcwNWRiMjk5MGM0MjdmM2RcIl19In0" target="_blank" style="text-decoration:none; color:#fff"><img src="http://img.flipit.com/public/es/images/upload/shop/thum_big_1411482079_La-Redoute.png" width="132" height="66" alt="La Redoute" title="La Redoute" style="vertical-align:top"></a></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; padding:6px 0 5px; font:11px/17px Arial,Helvetica,sans-serif; color:#fff">
                                            <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiYmZERVQ0aDgxVkNsNXMxYXFhWGJLNXZ1cERFIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbGEtcmVkb3V0ZT91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEyNTRcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjUzZGUyMzFlNGVkNzU3MjY3ZjUzZDQ4MDcwNWRiMjk5MGM0MjdmM2RcIl19In0" target="_blank" style="text-decoration:none; color:#fff">Código</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="30"></td>
                            <td valign="top">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    <tr>
                                        <td style="padding:0 0 2px; font:14px/17px Arial,Helvetica,sans-serif; color:#363636">
                                            <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiV3BpRmpONUpwT1BoVmRvRDBhMUNaQ0lCdkVVIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbGEtcmVkb3V0ZVwiLFwiaWRcIjpcImUxYzUyNGRmYzg0YjRlNTA4NmJjODg4MTM2YmZkMThkXCIsXCJ1cmxfaWRzXCI6W1wiNTNkZTIzMWU0ZWQ3NTcyNjdmNTNkNDgwNzA1ZGIyOTkwYzQyN2YzZFwiXX0ifQ" target="_blank" style="text-decoration:none; color:#363636">La Redoute</a>&nbsp;<span style="color:#e89438; display:inline-block">Sólo en Flipit</span></td>
                                    </tr>
                                    <tr>
                                        <td style="padding:0 0 15px; font:16px/22px Arial,Helvetica,sans-serif; color:#0077cc">
                                            <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoiYmZERVQ0aDgxVkNsNXMxYXFhWGJLNXZ1cERFIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvbGEtcmVkb3V0ZT91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEyNTRcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjUzZGUyMzFlNGVkNzU3MjY3ZjUzZDQ4MDcwNWRiMjk5MGM0MjdmM2RcIl19In0" target="_blank" style="text-decoration:underline; color:#0077cc">Reloj de Mickey Mouse GRATIS al comprar con el codigo descuento La Redoute</a></td>
                                    </tr>
                                    <tr>
                                        <td style="font:12px/17px Arial,Helvetica,sans-serif; color:#32383e">Válido desde 29 Diciembre hasta 29 De Febrero De 2016</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td bgcolor="#fafafa" align="center" style="border-radius:0 0 4px 4px; padding:12px 0 11px; font:bold 14px/17px Arial,Helvetica,sans-serif; color:#373736">
                    <a href="http://mandrillapp.com/track/click/15439343/www.flipit.com?p=eyJzIjoidWIyMUJCRjNPY0lfLWU5VUlGYWt6SlB3ZmtJIiwidiI6MSwicCI6IntcInVcIjoxNTQzOTM0MyxcInZcIjoxLFwidXJsXCI6XCJodHRwOlxcXC9cXFwvd3d3LmZsaXBpdC5jb21cXFwvZXNcXFwvY2F0ZWdvcmlhc1xcXC9iZWJlcy1uaW5vcz91dG1fc291cmNlPXRyYW5zYWN0aW9uYWwmdXRtX21lZGl1bT1lbWFpbCZ1dG1fY2FtcGFpZ249MDctMDEtMjAxNiZ0eXBlPWNvZGUjMzEyNTRcIixcImlkXCI6XCJlMWM1MjRkZmM4NGI0ZTUwODZiYzg4ODEzNmJmZDE4ZFwiLFwidXJsX2lkc1wiOltcIjY1NzdlMjg5MDYyYWFiNmRjMWIxZmI0MTAyZDIwMWZhYTA0NjJmYjRcIl19In0" target="_blank" style="text-decoration:none; color:#373736">Ver más descuentos populares &gt;</a></td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</div>
</td>
<td valign="top" width="50%">
    <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#0077cc">
        <tbody>
        <tr>
            <td height="140" bgcolor="#0077cc" style="">&nbsp;</td>
        </tr>
        </tbody>
    </table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>