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
 * Abstract base class of HTTP based authentication.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
abstract class AbstractHttpAuthentication
{
    /**
     * Send a HTTP request.
     *
     * @param  string $method
     * @param  string $url
     * @param  array  $header
     * @param  string $body
     * @return string
     */
    public function sendRequest ($method, $url, array $header = array(), $body = null)
    {
        $ctxOptions = array(
            'http' => array(
                'method' => $method,
                'header' => implode("\r\n", $header),
                'content' => $body
            )
        );
        $ctx = stream_context_create($ctxOptions);
        $response = file_get_contents($url, false, $ctx);
        if ($response === false) {
            throw new RuntimeException("Error sending HTTP request");
        }

        $statuscode = null;
        if (preg_match('@^HTTP/\d+\.\d+ (?<statuscode>\d+)@i', $http_response_header[0], $matches)) {
            $statuscode = $matches['statuscode'];
        }
        if ($statuscode < 200 || $statuscode > 299) {
            throw new RuntimeException("Unexpected HTTP status code in server response: {$statuscode}");
        }
        return $response;    
    }
}