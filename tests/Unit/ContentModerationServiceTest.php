<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ContentModerationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ContentModerationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_is_clean_returns_true_for_clean_text()
    {
        $result = ContentModerationService::isClean('Hello world, this is a clean message.');
        $this->assertTrue($result);
    }

    public function test_is_clean_returns_false_for_profanity()
    {
        $result = ContentModerationService::isClean('This contains anjing profanity.');
        $this->assertFalse($result);
    }

    public function test_is_clean_returns_false_for_sara()
    {
        $result = ContentModerationService::isClean('This contains kafir hate speech.');
        $this->assertFalse($result);
    }

    public function test_is_clean_returns_true_for_empty_text()
    {
        $result = ContentModerationService::isClean('');
        $this->assertTrue($result);
    }

    public function test_get_violation_returns_detected_word()
    {
        $violation = ContentModerationService::getViolation('This contains babi profanity.');
        $this->assertEquals('babi', $violation);
    }

    public function test_get_violation_returns_null_for_clean_text()
    {
        $violation = ContentModerationService::getViolation('This is a clean message.');
        $this->assertNull($violation);
    }

    public function test_mask_replaces_bad_words_with_asterisks()
    {
        $masked = ContentModerationService::mask('This contains anjing profanity.');
        $this->assertStringContainsString('******', $masked);
        $this->assertStringNotContainsString('anjing', $masked);
    }

    public function test_clear_cache_clears_blacklist_cache()
    {
        // First call should cache the blacklist
        ContentModerationService::isClean('test');
        $this->assertTrue(Cache::has('content_moderation_blacklist'));
        
        // Clear cache
        ContentModerationService::clearCache();
        $this->assertFalse(Cache::has('content_moderation_blacklist'));
    }

    public function test_is_clean_uses_database_when_available()
    {
        // Mock database to return custom word
        DB::table('content_moderation_words')->insert([
            'word' => 'testbadword',
            'category' => 'profanity',
            'is_active' => true,
        ]);

        $result = ContentModerationService::isClean('This contains testbadword.');
        $this->assertFalse($result);

        // Cleanup
        DB::table('content_moderation_words')->where('word', 'testbadword')->delete();
    }
}
