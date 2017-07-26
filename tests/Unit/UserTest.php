<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class UserTest extends TestCase
{

    use DatabaseTransactions;
    use WithoutMiddleware;

    /**
     * Test login.
     *
     * @return void
     */
    public function testLogin()
    {
        //Valid credentials test.
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com', 'password' => 'password']);
        $response->assertStatus(200);

        //Invalid credentials test.
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com', 'password' => 'pass']);
        $response
            ->assertStatus(401)
            ->assertJson([
                'error' => 'invalid_credentials'
            ]);
    }

    /**
     * Test register.
     *
     * @return void
     */
    public function testRegister()
    {
        //Used email address test.
        $response = $this->json('POST', '/api/register', ['name' => 'test','email' => 'test@example.com',
            'password' => 'password']);
        $response
            ->assertStatus(500)
            ->assertJson([
                'error' => 'The email address you specified is already in use.'
            ]);

        //Valid credentials test.
        $response = $this->json('POST', '/api/register', ['name' => 'test','email' => 'abc@aaaaa.com',
            'password' => 'password']);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'User successfully registered'
            ]);
        $this->assertDatabaseHas('users', [
            'name' => 'test',
            'email' => 'abc@aaaaa.com'
        ]);
    }

    /**
     * Test userInfo.
     *
     * @return void
     */
    public function testUserInfo()
    {
        //Invalid user test.
        $response = $this->json('GET', '/api/user');
        $response
            ->assertStatus(401)
            ->assertJson([
                'error' =>'invalid user'
            ]);

        //Valid user test.
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com',
            'password' => 'password']);
        $content = json_decode($response->getContent());
        $token = $content->token;
        $this->refreshApplication();
        $response = $this->json('GET', '/api/user?token='.$token);
        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => 'test',
                'email' => 'test@example.com'
            ]);
    }
}
