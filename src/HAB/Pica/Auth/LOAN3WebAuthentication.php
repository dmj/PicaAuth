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

/**
 * Authenticate user against a LOAN3 web interface.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class LOAN3WebAuthentication extends AbstractHttpAuthentication implements AuthenticationInterface
{
    /**
     * Service URL.
     *
     * @var string
     */
    private $serviceUrl;

    /**
     * Constructor.
     *
     * @param  string $serviceUrl
     * @return void
     */
    public function __construct ($serviceUrl)
    {
        $this->serviceUrl = $serviceUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate ($username, $password)
    {
        $query = array(
            'BOR_U' => $username,
            'BOR_PW' => $password,
            'ACT' => 'UI_DATA',
        );
        $response = $this->sendRequest(
            'POST',
            $this->serviceUrl,
            array('Content-Type: application/x-www-form-urlencoded'),
            http_build_query($query)
        );
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
        $attributes = array();
        preg_match_all('@<input([^>]+)>@i', $body, $matches);
        if (!empty($matches)) {
            foreach ($matches[1] as $match) {
                if ($this->getAttributeValue('type', $match) === 'hidden') {
                    $name = $this->getAttributeValue('name', $match);
                    $value = $this->getAttributeValue('value', $match);
                    if ($name) {
                        $attributes[$name] = $value;
                    }
                }
            }
        }
        if (array_key_exists('STATUS', $attributes) && $attributes['STATUS'] === 'HML_OK') {
            return $attributes;
        }
        return false;
    }

    /**
     * Return value of attribute in attribute list.
     *
     * @param  string $name
     * @param  string $attrlist
     * @return string|null
     */
    private function getAttributeValue ($name, $attrlist)
    {
        $regex = sprintf('@\b%s=["\'](?<value>[^"\']*)@i', preg_quote($name));
        if (preg_match($regex, $attrlist, $match)) {
            return $match['value'];
        }
        return null;
    }

}