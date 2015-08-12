<?php

/**
 * This file is part of PicaAuth.
 *
 * PicaAuth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PicaAuth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PicaAuth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */

namespace HAB\Pica\Auth;

use RuntimeException;

/**
 * Authenticate user against the LBS4 Authentication webservice.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class LBSAuthentication extends AbstractHttpAuthentication implements AuthenticationInterface
{
    /**
     * Service URL.
     *
     * @var string
     */
    private $serviceUrl;

    /**
     * Catalog number.
     *
     * @var integer
     */
    private $catalogNumber;

    /**
     * User number for connecting to LBS4.
     *
     * @var integer
     */
    private $lbsUserNumber;

    /**
     * Constructor.
     *
     * @param  string $serviceUrl
     * @param  integer $catalogNumber
     * @param  integer $lbsUserNumber
     * @return void
     */
    public function __construct ($serviceUrl, $catalogNumber, $lbsUserNumber)
    {
        $this->serviceUrl = $serviceUrl;
        $this->catalogNumber = $catalogNumber;
        $this->lbsUserNumber = $lbsUserNumber;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate ($username, $password)
    {
        $query = array(
            'UK' => $username,
            'PW' => $password,
            'UN' => $this->lbsUserNumber,
            'FNO' => $this->catalogNumber,
            'LNG' => 'EN'
        );
        $response = $this->sendRequest('GET', sprintf('%s?%s', $this->serviceUrl, http_build_query($query)));
        $attributes = $this->parseResponseBody($response);
        return $attributes;
    }

    /**
     * Parse response body and user return attributes.
     *
     * Returns false on authentication failure.
     *
     * @param  string $body
     * @return array|false
     */
    private function parseResponseBody ($body)
    {
        $response = @simplexml_load_string($body);
        if ($response === false || $response->getName() !== 'AuthenticationResponse' || (string)$response->status !== 'OK') {
            return false;
        }
        $attributes = array();
        foreach ($response as $attribute) {
            $attributes[$attribute->getName()] = (string)$attribute;
        }
        return $attributes;
    }
}