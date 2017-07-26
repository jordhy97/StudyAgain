<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class AnswerTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    /**
     * Test store.
     *
     * @return void
     */
    public function testStore()
    {
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com',
            'password' => 'password']);
        $content = json_decode($response->getContent());
        $token = $content->token;
        $this->refreshApplication();
        $response = $this->json('POST', '/api/questions?token='.$token,
            ['title' => 'test', 'body' => 'test', 'tags' => 'test1, test2']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('POST', '/api/questions/'.$id.'/answers?token='.$token,
            ['body' => 'test']);
        $response
            ->assertStatus(200)
            ->assertJson([
                'body' => 'test',
                'author' => ['name' => 'test']
            ]);
        $this->assertDatabaseHas('answers', [
            'body' => 'test'
        ]);
    }

    /**
     * Test show.
     *
     * @return void
     */
    public function testShow()
    {
        //Answer exists test.
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com',
            'password' => 'password']);
        $content = json_decode($response->getContent());
        $token = $content->token;
        $this->refreshApplication();
        $response = $this->json('POST', '/api/questions?token='.$token,
            ['title' => 'test', 'body' => 'test', 'tags' => 'test1, test2']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('POST', '/api/questions/'.$id.'/answers?token='.$token,
            ['body' => 'test']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('GET', '/api/answers/'.$id.'?token='.$token);
        $response
            ->assertStatus(200)
            ->assertJson([
                'body' => 'test',
                'author' => ['name' => 'test']
            ]);

        //Answer does not exist test.
        $response = $this->json('GET', '/api/answers/aaaa');
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' =>'answer not found'
            ]);
    }

    /**
     * Test update.
     *
     * @return void
     */
    public function testUpdate()
    {
        //Answer exists test.
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com',
            'password' => 'password']);
        $content = json_decode($response->getContent());
        $token = $content->token;
        $this->refreshApplication();
        $response = $this->json('POST', '/api/questions?token='.$token,
            ['title' => 'test', 'body' => 'test', 'tags' => 'test1, test2']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('POST', '/api/questions/'.$id.'/answers?token='.$token,
            ['body' => 'test']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('PUT', '/api/answers/'.$id.'?token='.$token,
            ['body' => 'test2']);
        $response
            ->assertStatus(200)
            ->assertJson([
                'body' => 'test2',
                'author' => ['name' => 'test']
            ]);

        //Answer does not exist test.
        $response = $this->json('PUT', '/api/answers/aaaa');
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' =>'answer not found'
            ]);
    }

    /**
     * Test destroy.
     *
     * @return void
     */
    public function testDestroy()
    {
        //Answer exists test.
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com',
            'password' => 'password']);
        $content = json_decode($response->getContent());
        $token = $content->token;
        $this->refreshApplication();
        $response = $this->json('POST', '/api/questions?token='.$token,
            ['title' => 'test', 'body' => 'test', 'tags' => 'test1, test2']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('POST', '/api/questions/'.$id.'/answers?token='.$token,
            ['body' => 'test']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('DELETE', '/api/answers/'.$id.'?token='.$token);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Answer deleted'
            ]);

        //Answer does not exist test.
        $response = $this->json('DELETE', '/api/answers/aaaa');
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' =>'answer not found'
            ]);
    }

    /**
     * Test upVote.
     *
     * @return void
     */
    public function testUpVote()
    {
        //Answer exists test.
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com',
            'password' => 'password']);
        $content = json_decode($response->getContent());
        $token = $content->token;
        $this->refreshApplication();
        $response = $this->json('POST', '/api/questions?token='.$token,
            ['title' => 'test', 'body' => 'test', 'tags' => 'test1, test2']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('POST', '/api/questions/'.$id.'/answers?token='.$token,
            ['body' => 'test']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('POST', '/api/answers/'.$id.'/upVote?token='.$token);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Answer up voted'
            ]);
        $response = $this->json('POST', '/api/answers/'.$id.'/upVote?token='.$token);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Answer already up voted'
            ]);

        //Question does not exist test.
        //Answer does not exist test.
        $response = $this->json('POST', '/api/answers/aaaa/upVote');
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' =>'answer not found'
            ]);
    }

    /**
     * Test downVote.
     *
     * @return void
     */
    public function testDownVote()
    {
        //Answer exists test.
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com',
            'password' => 'password']);
        $content = json_decode($response->getContent());
        $token = $content->token;
        $this->refreshApplication();
        $response = $this->json('POST', '/api/questions?token='.$token,
            ['title' => 'test', 'body' => 'test', 'tags' => 'test1, test2']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('POST', '/api/questions/'.$id.'/answers?token='.$token,
            ['body' => 'test']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('POST', '/api/answers/'.$id.'/downVote?token='.$token);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Answer down voted'
            ]);
        $response = $this->json('POST', '/api/answers/'.$id.'/downVote?token='.$token);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Answer already down voted'
            ]);

        //Question does not exist test.
        //Answer does not exist test.
        $response = $this->json('POST', '/api/answers/aaaa/downVote');
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' =>'answer not found'
            ]);
    }
}
