<?php

class PartialsTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testTOS()
	{
		$response = $this->call('GET', '/p/partials/tos');

		$this->assertResponseOk();

        $tos = $response->getContent();

        $this->assertEquals('<h4>Terms and Conditions', substr($tos, 0, 24));



	}

}