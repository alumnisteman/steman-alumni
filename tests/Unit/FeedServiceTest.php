<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\FeedService;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class FeedServiceTest extends TestCase
{
    protected $feedService;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->feedService = app(FeedService::class);
        $this->user = User::factory()->create([
            'status' => 'approved',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    public function test_create_post_creates_post_with_correct_data()
    {
        $data = [
            'content' => 'This is a test post',
            'image_url' => null,
            'type' => 'memory',
            'visibility' => 'public',
            'is_anonymous' => false,
        ];

        $post = $this->feedService->createPost($this->user, $data);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals('This is a test post', $post->content);
        $this->assertEquals('memory', $post->type);
        $this->assertEquals('public', $post->visibility);
        $this->assertEquals($this->user->id, $post->user_id);
    }

    public function test_create_post_invalidates_user_feed_cache()
    {
        // Set initial cache
        Cache::put("feed:user:{$this->user->id}", ['cached_data']);
        
        $data = [
            'content' => 'Test post',
            'type' => 'memory',
            'visibility' => 'public',
        ];

        $this->feedService->createPost($this->user, $data);

        // Cache should be invalidated
        $this->assertFalse(Cache::has("feed:user:{$this->user->id}"));
    }

    public function test_get_feed_returns_posts_for_authenticated_user()
    {
        // Create some posts
        Post::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'visibility' => 'public',
        ]);

        $posts = $this->feedService->getFeed($this->user, 1, 10);

        $this->assertIsArray($posts);
        $this->assertGreaterThan(0, count($posts));
    }

    public function test_get_feed_respects_pagination()
    {
        // Create many posts
        Post::factory()->count(25)->create([
            'user_id' => $this->user->id,
            'visibility' => 'public',
        ]);

        $postsPage1 = $this->feedService->getFeed($this->user, 1, 10);
        $postsPage2 = $this->feedService->getFeed($this->user, 2, 10);

        $this->assertLessThanOrEqual(10, count($postsPage1));
        $this->assertLessThanOrEqual(10, count($postsPage2));
    }
}
