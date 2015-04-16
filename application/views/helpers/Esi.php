<?php

/**
 * Copyright (c) 2010, Bas de Nooijer
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in the
 *     documentation and/or other materials provided with the distribution.
 *
 *   * Neither the name of Raspberry nor the names of its contributors may be
 *     used to endorse or promote products derived from this software without
 *     specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS &quot;AS IS&quot;
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author      Bas de Nooijer
 * @copyright   2010, raspberry.nl
 * @license     http://www.opensource.org/licenses/bsd-license.php
 */

/** Zend_Controller_Front */
require_once 'Zend/Controller/Front.php';

/** Zend_View_Helper_Abstract.php */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Helper for Varnish Edge Side Includes
 */


class Zend_View_Helper_Esi extends Zend_View_Helper_Abstract
{

    /**
     * Default ESI header name
     *
     * @var string
     */
    protected static $_varnishHeaderName = 'esi-enabled';

    /**
     * Default ESI header value
     *
     * @var string
     */
    protected static $_varnishHeaderValue = '1';

    /**
     * Has the Varnish header been sent?
     *
     * @var boolean
     */
    protected static $_varnishHeaderSent = false;

    /**
     * Sets the ESI header settings (to match your Varnish VCL)
     *
     * @param string $name
     * @param string $value
     */
    public static function setHeader($name, $value)
    {
        self::$_varnishHeaderName = $name;
        self::$_varnishHeaderValue = $value;
    }

    /**
     * Create an ESI tag for a given SRC.
     *
     * @param  string $esiSource
     * @return string
     */
    public function esi($esiSource)
    {
        if (!empty($_SERVER['HTTP_X_VARNISH']) && !isset($_COOKIE['passCache'])) {
            if (!self::$_varnishHeaderSent) {
                $response = Zend_Controller_Front::getInstance()->getResponse();
                $response->setHeader(self::$_varnishHeaderName, self::$_varnishHeaderValue);
                self::$_varnishHeaderSent = true;
            }
            return '<esi:include src="' . HTTP_PATH . ltrim($esiSource, '/') . '"/>';
        } else {
            $divID = rand(0, 99999);
            if (strpos($esiSource, 'login') !== false) {
                echo '<nav class="account-box" id="'.$divID.'"></nav>';
            } elseif (strpos($esiSource, 'followbutton') !== false) {
                echo '<span class="btn-holder" id="'.$divID.'"></span>';
            } elseif (strpos($esiSource, 'createdoffers') !== false) {
                echo '<span class="" id="'.$divID.'"></span>';
            } else {
                echo '<div class="" id="'.$divID.'"></div>';
            }
            ?>
            <script type="text/javascript">
                $.get('<?php echo HTTP_PATH . ltrim($esiSource , '/'); ?>', function(data) {
                  $('#<?php echo $divID; ?>').html(data);
                  console.log('Load of <?php echo $esiSource; ?> was performed with ajax.');
                });
            </script>
            <?php
        }

    }
}

