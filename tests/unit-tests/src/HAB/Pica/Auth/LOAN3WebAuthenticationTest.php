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

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Unit tests for the LOAN3WebAuthenitcation service module.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class LOAN3WebAuthenticationTest extends TestCase
{

    public function testAuthenticationFailure ()
    {
        $service = $this->getMock('HAB\Pica\Auth\LOAN3WebAuthentication', array('sendRequest'), array('invalid://example.org/'));
        $response = file_get_contents(APP_TESTDIR . '/unit-tests/data/loan3authentication.error.response');
        $service->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($response));
        $attributes = $service->authenticate('username', 'password');
        $this->assertFalse($attributes);
    }

    public function testAuthenticationSuccess ()
    {
        $service = $this->getMock('HAB\Pica\Auth\LOAN3WebAuthentication', array('sendRequest'), array('invalid://example.org/'));
        $response = file_get_contents(APP_TESTDIR . '/unit-tests/data/loan3authentication.success.response');
        $service->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($response));
        $attributes = $service->authenticate('username', 'password');
        $this->assertInternalType('array', $attributes);
        $this->assertNotEmpty($attributes);
    }
}