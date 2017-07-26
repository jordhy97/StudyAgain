<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class QuestionTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    /**
     * Test index.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->json('GET', '/api/questions');
        $response
            ->assertStatus(200)
            ->assertJson([
                'current_page' => 1
            ]);

        $response = $this->json('GET', '/api/questions?search=test');
        $response
            ->assertStatus(200)
            ->assertJson([
                'current_page' => 1
            ]);
    }

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
        $response
            ->assertStatus(200)
            ->assertJson([
                'title' => 'test',
                'body' => 'test',
                'author' => ['name' => 'test'],
                'tags' => [0 => ['name' => 'test1'], 1 => ['name' => 'test2']]
            ]);
        $this->assertDatabaseHas('questions', [
            'title' => 'test',
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
        //Question exists test.
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com',
            'password' => 'password']);
        $content = json_decode($response->getContent());
        $token = $content->token;
        $this->refreshApplication();
        $response = $this->json('POST', '/api/questions?token='.$token,
            ['title' => 'test', 'body' => 'test', 'tags' => 'test1, test2']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('GET', '/api/questions/'.$id.'?token='.$token);
        $response
            ->assertStatus(200)
            ->assertJson([
                'title' => 'test',
                'body' => 'test',
                'author' => ['name' => 'test'],
                'tags' => [0 => ['name' => 'test1'], 1 => ['name' => 'test2']],
                'editable' => true,
                'voteStatus' => 'none'
            ]);

        //Question does not exist test.
        $response = $this->json('GET', '/api/questions/aaaa');
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' =>'question not found'
            ]);
    }

    /**
     * Test update.
     *
     * @return void
     */
    public function testUpdate()
    {
        //Question exists test.
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com',
            'password' => 'password']);
        $content = json_decode($response->getContent());
        $token = $content->token;
        $this->refreshApplication();
        $response = $this->json('POST', '/api/questions?token='.$token,
            ['title' => 'test', 'body' => 'test', 'tags' => 'test1, test2']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('PUT', '/api/questions/'.$id.'?token='.$token,
            ['title' => 'test2', 'body' => 'test2', 'tags' => 'test3, test4']);
        $response
            ->assertStatus(200)
            ->assertJson([
                'title' => 'test2',
                'body' => 'test2',
                'author' => ['name' => 'test'],
                'tags' => [0 => ['name' => 'test3'], 1 => ['name' => 'test4']]
            ]);
        $this->assertDatabaseHas('questions', [
            'title' => 'test2',
            'body' => 'test2'
        ]);

        //Question does not exist test.
        $response = $this->json('PUT', '/api/questions/aaaa');
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' =>'question not found'
            ]);
    }

    /**
     * Test destroy.
     *
     * @return void
     */
    public function testDestroy()
    {
        //Question exists test.
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com',
            'password' => 'password']);
        $content = json_decode($response->getContent());
        $token = $content->token;
        $this->refreshApplication();
        $response = $this->json('POST', '/api/questions?token='.$token,
            ['title' => 'test', 'body' => 'test', 'tags' => 'test1, test2']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('DELETE', '/api/questions/'.$id.'?token='.$token);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Question deleted'
            ]);

        //Question does not exist test.
        $response = $this->json('DELETE', '/api/questions/aaaa');
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' =>'question not found'
            ]);
    }

    /**
     * Test upVote.
     *
     * @return void
     */
    public function testUpVote()
    {
        //Question exists test.
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com',
            'password' => 'password']);
        $content = json_decode($response->getContent());
        $token = $content->token;
        $this->refreshApplication();
        $response = $this->json('POST', '/api/questions?token='.$token,
            ['title' => 'test', 'body' => 'test', 'tags' => 'test1, test2']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('POST', '/api/questions/'.$id.'/upVote?token='.$token);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Question up voted'
            ]);
        $response = $this->json('POST', '/api/questions/'.$id.'/upVote?token='.$token);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Question already up voted'
            ]);

        //Question does not exist test.
        $response = $this->json('POST', '/api/questions/aaaa/upVote');
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' =>'question not found'
            ]);
    }

    /**
     * Test downVote.
     *
     * @return void
     */
    public function testDownVote()
    {
        //Question exists test.
        $response = $this->json('POST', '/api/login', ['email' => 'test@example.com',
            'password' => 'password']);
        $content = json_decode($response->getContent());
        $token = $content->token;
        $this->refreshApplication();
        $response = $this->json('POST', '/api/questions?token='.$token,
            ['title' => 'test', 'body' => 'test', 'tags' => 'test1, test2']);
        $content = json_decode($response->getContent());
        $id = $content->id;
        $response = $this->json('POST', '/api/questions/'.$id.'/downVote?token='.$token);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Question down voted'
            ]);
        $response = $this->json('POST', '/api/questions/'.$id.'/downVote?token='.$token);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Question already down voted'
            ]);

        //Question does not exist test.
        $response = $this->json('POST', '/api/questions/aaaa/upVote');
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' =>'question not found'
            ]);
    }

    /**
     * Test tagged.
     *
     * @return void
     */
    public function testTagged()
    {
        $response = $this->json('GET', '/api/questions/tagged/test');
        $response
            ->assertStatus(200);
    }
}
