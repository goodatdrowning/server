<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2020, Georg Ehrke
 *
 * @author Georg Ehrke <oc.list@georgehrke.com>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\UserStatus\Tests\Controller;

use OCA\UserStatus\Controller\StatusesController;
use OCA\UserStatus\Db\UserStatus;
use OCA\UserStatus\Service\StatusService;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\OCS\OCSNotFoundException;
use OCP\IRequest;
use Test\TestCase;

class StatusesControllerTest extends TestCase {

	/** @var StatusService|\PHPUnit\Framework\MockObject\MockObject */
	private $service;

	/** @var StatusesController */
	private $controller;

	protected function setUp(): void {
		parent::setUp();

		$request = $this->createMock(IRequest::class);
		$this->service = $this->createMock(StatusService::class);

		$this->controller = new StatusesController('user_status',
			$request, $this->service);
	}

	public function testFindAll(): void {
		$userStatus = $this->createMock(UserStatus::class);

		$this->service->expects($this->once())
			->method('findAll')
			->with(20, 40)
			->willReturn([$userStatus]);

		$response = $this->controller->findAll(20, 40);
		$this->assertEquals([$userStatus], $response->getData());
	}

	public function testFind(): void {
		$userStatus = $this->createMock(UserStatus::class);

		$this->service->expects($this->once())
			->method('findByUserId')
			->with('john.doe')
			->willReturn($userStatus);

		$response = $this->controller->find('john.doe');
		$this->assertEquals($userStatus, $response->getData());
	}

	public function testFindDoesNotExist(): void {
		$this->service->expects($this->once())
			->method('findByUserId')
			->with('john.doe')
			->willThrowException(new DoesNotExistException(''));

		$this->expectException(OCSNotFoundException::class);
		$this->expectExceptionMessage('No status for the requested userId');

		$this->controller->find('john.doe');
	}
}