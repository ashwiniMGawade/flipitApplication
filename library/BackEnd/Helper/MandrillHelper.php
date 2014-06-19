<?php
class BackEnd_Helper_MandrillHelper
{
	public static function getVouchercodesOfCategories($topCategories, $currentObject)
    {
        if(count($topCategories[0]['category']['categoryicon']) > 0):
            $img = PUBLIC_PATH_CDN.
                $topCategories[0]['category']['categoryicon']['path'].
                'thum_medium_'. $topCategories[0]['category']['categoryicon']['name'];
        else:
            $img = PUBLIC_PATH_LOCALE."images/NoImage/NoImage_70x60.png";
        endif;
        $permalinkCatMainEmail = HTTP_PATH_FRONTEND .
            FrontEnd_Helper_viewHelper::__link('link_categorieen') .'/'.
            $topCategories[0]['category']['permaLink'] .
            '?utm_source=transactional&utm_medium=email&utm_campaign='.date('d-m-Y');
        $currentObject->category = array(
                            array(
                                'name' => 'categoryImage',
                                'content' => "<a style='color:#333333; text-decoration:none;'
                                href='$permalinkCatMainEmail'><img src='".$img."'/></a>"
                                ),
                                array(
                                    'name' => 'categoryName',
                                    'content' => FrontEnd_Helper_viewHelper::__email('email_Populairste categorie:') .
                                    " <a style='color:#333333; text-decoration:none;'
                                    href='$permalinkCatMainEmail'>". $topCategories[0]['category']['name'] ."</a>"
                                ),
                                array(
                                    'name' => 'categoryNameMore',
                                    'content' => '<a href="'.$permalinkCatMainEmail.'"
                                    style="font-size:12px; text-decoration:none; color:#0B7DC1;" >' .
                                    FrontEnd_Helper_viewHelper::__email('email_Bekijk meer van onze') .
                                    " ". $topCategories[0]['category']['name'] ." ".
                                    FrontEnd_Helper_viewHelper::__email('email_aanbiedingen') . ' > </a>'
                                )
                            );
 ?> <tr>
								<td style="padding:0 0 19px 29px; font:24px/27px Arial, Helvetica, sans-serif; color:#32383e;"><?php echo FrontEnd_Helper_viewHelper::__email('email_More top offers'); ?></td>
							</tr><tr>
	<td bgcolor="#ffffff" style="border:1px solid #e4e4e4;">
		<table width="100%" cellpadding="0" cellspacing="0"><?php
        $vouchers = array_slice(Category::getCategoryVoucherCodes($topCategories[0]['categoryId']), 0, 3);
echo "<pre>"; print_r($vouchers); die;
        foreach ($vouchers as $key => $value) {

        if(count($value['shop']['logo']) > 0):
                $img = PUBLIC_PATH_CDN.$value['shop']['logo']['path'].'thum_medium_store_'. $value['shop']['logo']['name'];
            else:
                $img = PUBLIC_PATH_LOCALE."images/NoImage/NoImage_200x100.jpg";
            endif;

            $expiryDate = new Zend_Date($value['endDate']);

            $startDate = new Zend_Date($value['startDate']);
             ?>

			<tr>
				<td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea;">
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td valign="top" width="132" style="border:1px solid #dddddd; border-radius:5px;">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td height="70" align="center"><img src="<?php echo $img; ?>" style="vertical-align:top;" width="102" height="70" alt="<?php echo $value['shop']['name']; ?>" /></td>
									</tr>
									<tr>
										<td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; box-shadow:0 0 4px rgba(0,0,0,0.15); padding:6px 0 5px; font:11px/17px Arial, Helvetica, sans-serif; color:#fff;"><a href="<?php echo HTTP_PATH_FRONTEND . $value['shop']['permalink'].'?utm_source=transactional&utm_medium=email&utm_campaign='.date('d-m-Y'); ?>" style="text-decoration:none; color:#fff;">CODE</a></td>
									</tr>
								</table>
							</td>
							<td width="30"></td>
							<td valign="top">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td style="padding:0 0 2px; font:14px/17px Arial, Helvetica, sans-serif; color:#363636;"><?php echo $value['shop']['name']; ?></td>
									</tr>
									<tr>
										<td style="padding:0 0 15px; font:16px/22px Arial, Helvetica, sans-serif; color:#0077cc;"><a href="<?php echo HTTP_PATH_FRONTEND ."out/offer/".$value['id']; ?>" style="text-decoration:underline; color:#0077cc;"><?php echo $value['shop']['name'].' '.$value['title']; ?></a></td>
									</tr>
									<tr>
										<td style="font:12px/17px Arial, Helvetica, sans-serif; color:#32383e;">Added on <?php echo $startDate->get(Zend_Date::DATE_MEDIUM); ?>, Expires on <?php echo $expiryDate->get(Zend_Date::DATE_MEDIUM); ?></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr><?php
           
        }?>
 
 
										<tr>
											<td bgcolor="#fafafa" align="center" style="padding:12px 0 11px; font:bold 14px/17px Arial, Helvetica, sans-serif; color:#373736;"><a href="#" style="text-decoration:none; color:#373736;">Bekijk meer van onze top aanbiedingen &gt;</a></td>
										</tr>
									</table>
								</td>
							</tr><?php
    }

    public static function getDirectLoginLinks($currentObject)
    {
        $email_data = Signupmaxaccount::getAllMaxAccounts();
        $testEmail = $currentObject->getRequest()->getParam('testEmail');
        $dummyPass = MD5('12345678');
        $send = $currentObject->getRequest()->getParam('send');
        $visitorData = array();
        $visitorMetaData = array();
        $toVisitorArray = array();

        if (isset($send) && $send == 'test') {
            $getTestEmaildata =  Visitor::getVisitorDetailsByEmail($testEmail);
            $key = 0;
            $visitorData[$key]['rcpt'] = $testEmail;
            $visitorData[$key]['vars'][0]['name'] = 'loginLink';
            $visitorData[$key]['vars'][0]['content'] =  HTTP_PATH_FRONTEND .
                FrontEnd_Helper_viewHelper::__link("link_login") . "/" .
                FrontEnd_Helper_viewHelper::__link("link_directlogin") . "/" .
                base64_encode($getTestEmaildata[0]['email']) ."/". $getTestEmaildata[0]['password'];
            $visitorData[$key]['vars'][1]['name'] = 'loginLinkWithUnsubscribe';
            $visitorData[$key]['vars'][1]['content'] = HTTP_PATH_FRONTEND .
                FrontEnd_Helper_viewHelper::__link("link_login") . "/" .
                FrontEnd_Helper_viewHelper::__link("link_directloginunsubscribe") . "/" .
                base64_encode($testEmail) ."/". $dummyPass;
            $toVisitorArray[$key]['email'] = $testEmail;
            $toVisitorArray[$key]['name'] = 'Member';
            $currentObject->loginLinkAndData = $visitorData;
            $currentObject->to = $toVisitorArray;
        } else {
            if ($currentObject->_settings['administration']['rights']  == 1) {
                $visitors = new Visitor();
                $visitors = $visitors->getVisitorsToSendNewsletter();
                $mandrill = new Mandrill_Init($currentObject->getInvokeArg('mandrillKey'));
                $getUserDataFromMandrill = $mandrill->users->senders();

                foreach ($getUserDataFromMandrill as $key => $value) {
                    if ($value['soft_bounces'] >= 6 || $value['hard_bounces'] >= 2) {
                        $updateActive = Doctrine_Query::create()
                            ->update('Visitor')
                            ->set('active', 0)
                            ->where("email = '".$value['address']."'")->execute();
                    }
                }

                foreach ($visitors as $key => $value) {
                    $keywords ='' ;

                    foreach ($value['keywords'] as $k => $word) {
                        $keywords .= $word['keyword'] . ' ';
                    }

                    $visitorData[$key]['rcpt'] = $value['email'];
                    $visitorData[$key]['vars'][0]['name'] = 'loginLink';
                    $visitorMetaData[$key]['rcpt'] = $value['email'];
                    $visitorMetaData[$key]['values']['referrer'] = trim($keywords) ;
                    $visitorData[$key]['vars'][0]['content'] = HTTP_PATH_FRONTEND .
                        FrontEnd_Helper_viewHelper::__link("link_login") . "/" .
                        FrontEnd_Helper_viewHelper::__link("link_directlogin") . "/" .
                        base64_encode($value['email']) ."/". $value['password'];
                    $visitorData[$key]['vars'][1]['name'] = 'loginLinkWithUnsubscribe';
                    $visitorData[$key]['vars'][1]['content'] = HTTP_PATH_FRONTEND .
                        FrontEnd_Helper_viewHelper::__link("link_login") . "/" .
                        FrontEnd_Helper_viewHelper::__link("link_directloginunsubscribe") . "/" .
                        base64_encode($value['email']) ."/". $value['password'];
                    $toVisitorArray[$key]['email'] = $value['email'];
                    $toVisitorArray[$key]['name'] = !empty($value['firstName']) ? $value['firstName'] : 'Member';
                }

                $currentObject->recipientMetaData = $visitorMetaData;
                $currentObject->loginLinkAndData = $visitorData;
                $currentObject->to = $toVisitorArray;
            }
        }
    }
    	public static function getTopVouchercodesDataMandrill($topVouchercodes)
	{

			$path =  defined('HTTP_PATH_FRONTEND') ? HTTP_PATH_FRONTEND :  HTTP_PATH_LOCALE ;
			$publicPath  =  defined('PUBLIC_PATH_CDN') ? PUBLIC_PATH_CDN :  PUBLIC_PATH ;
			
	        $dataShopName = $dataShopImage =  $shopPermalink = $expDate = $dataOfferName = array();

		foreach ($topVouchercodes as $key => $value) { 
 			if(count($value['offer']['shop']['logo']) > 0):
				$img = $publicPath.$value['offer']['shop']['logo']['path'].'thum_medium_store_'. $value['offer']['shop']['logo']['name'];
			else:
				$img = $publicPath."images/NoImage/NoImage_200x100.jpg";
			endif;
            $expiryDate = new Zend_Date($value['offer']['endDate']);

            $startDate = new Zend_Date($value['offer']['startDate']);
			?>
<tr>
				<td style="padding:28px 10px 30px 29px; border-bottom:1px solid #eaeaea;">
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td valign="top" width="132" style="border:1px solid #dddddd; border-radius:5px;">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td height="70" align="center"><img src="<?php echo $img; ?>" style="vertical-align:top;" width="102" height="70" alt="<?php echo $value['offer']['shop']['name']; ?>" /></td>
									</tr>
									<tr>
										<td bgcolor="#ea973d" align="center" style="border-radius:0 0 4px 4px; box-shadow:0 0 4px rgba(0,0,0,0.15); padding:6px 0 5px; font:11px/17px Arial, Helvetica, sans-serif; color:#fff;"><a href="<?php echo HTTP_PATH_FRONTEND . $value['offer']['shop']['permalink'].'?utm_source=transactional&utm_medium=email&utm_campaign='.date('d-m-Y'); ?>" style="text-decoration:none; color:#fff;">CODE</a></td>
									</tr>
								</table>
							</td>
							<td width="30"></td>
							<td valign="top">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td style="padding:0 0 2px; font:14px/17px Arial, Helvetica, sans-serif; color:#363636;"><?php echo $value['offer']['shop']['name']; ?></td>
									</tr>
									<tr>
										<td style="padding:0 0 15px; font:16px/22px Arial, Helvetica, sans-serif; color:#0077cc;"><a href="<?php echo HTTP_PATH_FRONTEND ."out/offer/".$value['id']; ?>" style="text-decoration:underline; color:#0077cc;"><?php echo $value['offer']['shop']['name'].' '.$value['offer']['title']; ?></a></td>
									</tr>
									<tr>
										<td style="font:12px/17px Arial, Helvetica, sans-serif; color:#32383e;">Added on <?php echo $startDate->get(Zend_Date::DATE_MEDIUM); ?>, Expires on <?php echo $expiryDate->get(Zend_Date::DATE_MEDIUM); ?></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr><?php
		} ?>
<tr>
				<td bgcolor="#fafafa" align="center" style="padding:12px 0 11px; font:bold 14px/17px Arial, Helvetica, sans-serif; color:#373736;"><a href="#" style="text-decoration:none; color:#373736;">Bekijk meer van onze top aanbiedingen &gt;</a></td>
			</tr>
		</table>
	</td>
</tr><?php
		return array('dataShopName' => $dataShopName,
					 'dataShopImage' => $dataShopImage,
					 'shopPermalink' => $shopPermalink,
					 'expDate' => $expDate,
					 'dataOfferName' =>  $dataOfferName );
	}
}