<?php

namespace Pharaonic\Laravel\Translatable\Tests;

use Pharaonic\Laravel\Translatable\Tests\Models\Post;
use Pharaonic\Laravel\Translatable\Tests\Models\PostTranslation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TranslatableTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    public function testTranslate()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        $post = Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $actual = $post->translate('en')->title;

        $this->assertSame('post_test_title', $actual);
    }

    public function testTranslateOnModifyTitle()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        $post = Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $post->translate('en')->title = 'modified_title';
        $post->save();

        $this->assertSame('modified_title', $post->translate('en')->title);
    }

    public function testTranslateOrNew()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        $post = Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $post->translateOrNew('zh_TW')->title = 'modified_title';
        $post->save();

        $this->assertSame('modified_title', $post->translate('zh_TW')->title);
        $this->assertSame('post_test_title', $post->translate('en')->title);
    }

    public function testTranslateOrFail()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        $post = Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $this->expectException(NotFoundHttpException::class);
        $post->translateOrFail('zh_TW')->title;
    }

    public function testTranslateOrFailOnSettingTitle()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        $post = Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $post->translateOrFail('en')->title = 'modified_title';
        $post->save();

        $this->assertSame('modified_title', $post->translateOrFail('en')->title);
    }

    public function testTranslateOrDefaultOnDefaultTitle()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        $post = Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $this->assertSame('post_test_title', $post->translateOrDefault('en')->title);
    }

    public function testTranslateOrDefaultOnModifyingTitle()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        $post = Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $post->translateOrDefault('en')->title = 'modified_title';
        $post->save();

        $this->assertSame('modified_title', $post->translateOrDefault('en')->title);
    }

    public function testHasTranslation()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        $post = Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $this->assertTrue($post->hasTranslation('en'));
        $this->assertFalse($post->hasTranslation('zh_TW'));
    }

    public function testPostAttributes()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        $post = Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $this->assertSame('en', $post->locales[0]);
    }

    public function testTranslated()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $expected = 'post_test_title';
        $actual = Post::translated('en')->get()->toArray()[0]['translations'][0]['title'];

        $this->assertSame($expected, $actual);
    }

    public function testTranslatedOnExistedLocaleFromPostScope()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $expected = 'en';
        $actual = Post::translated('en')->get()->toArray()[0]['translations'][0]['locale'];

        $this->assertSame($expected, $actual);
    }

    public function testTranslatedOnNonExistedLocaleFromPostScope()
    {
        $actual = Post::translated('fr')->get()->toArray();

        $this->assertCount(0, $actual);
    }

    public function testNotTranslated()
    {
        $this->assertCount(0, Post::notTranslated('fr')->get()->toArray());
    }

    public function testTranslatedSortingOnAsc()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 2,
            'title' => 'post_test_title2',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 2,
            'published'=> true,
        ]);

        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title1',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $expectedPostTranslationId = 1;
        $expectedPostId = 2;
        $actual = Post::translatedSorting('en', 'title', 'asc')->get()->toArray();

        $this->assertSame($expectedPostTranslationId, $actual[0]['id']);
        $this->assertSame($expectedPostId, $actual[0]['translations'][0]['id']);
    }

    public function testTranslatedSortingOnDesc()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 2,
            'title' => 'post_test_title2',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 2,
            'published'=> true,
        ]);

        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title1',
            'content' => 'post_test_content',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $expectedPostTranslationId = 1;
        $expectedPostId = 2;
        $actual = Post::translatedSorting('en', 'title', 'desc')->get()->toArray();

        $this->assertSame($expectedPostId, $actual[0]['id']);
        $this->assertSame($expectedPostTranslationId, $actual[0]['translations'][0]['id']);
    }

    public function testTranslatedWhereTranslationOnSpecificContent()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 2,
            'title' => 'post_test_title2',
            'content' => 'post_test_content2',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 2,
            'published'=> true,
        ]);

        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title1',
            'content' => 'post_test_content1',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $actual = Post::translated('en')->whereTranslation('content', 'post_test_content1')->get()->toArray();

        $this->assertCount(1, $actual);
        $this->assertSame('post_test_title1', $actual[0]['translations'][0]['title']);
        $this->assertSame('post_test_content1', $actual[0]['translations'][0]['content']);
    }

    public function testTranslatedOrWhereTranslation()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 2,
            'title' => 'post_test_title2',
            'content' => 'post_test_content2',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 2,
            'published'=> true,
        ]);

        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title1',
            'content' => 'post_test_content1',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $actual = Post::translated('en')->orWhereTranslation('content', 'post_test_content1')->get()->toArray();

        $this->assertCount(2, $actual);
        $this->assertSame('post_test_title1', $actual[0]['translations'][0]['title']);
        $this->assertSame('post_test_content1', $actual[0]['translations'][0]['content']);
    }

    public function testTranslatedWhereTranslationLike()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 2,
            'title' => 'post_test_title2',
            'content' => 'post_test_content2',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 2,
            'published'=> true,
        ]);

        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title1',
            'content' => 'post_test_content1',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $actual = Post::translated('en')->whereTranslationLike('content', '%content1%')->get()->toArray();

        $this->assertCount(1, $actual);
        $this->assertSame('post_test_title1', $actual[0]['translations'][0]['title']);
        $this->assertSame('post_test_content1', $actual[0]['translations'][0]['content']);
    }

    public function testTranslatedOrWhereTranslationLike()
    {
        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 2,
            'title' => 'post_test_title2',
            'content' => 'post_test_content2',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 2,
            'published'=> true,
        ]);

        PostTranslation::create([
            'locale' => 'en',
            'post_id' => 1,
            'title' => 'post_test_title1',
            'content' => 'post_test_content1',
            'description' => 'post_test_description',
            'keywords' => 'post_test_keyword',
        ]);
        Post::create([
            'id' => 1,
            'published'=> true,
        ]);

        $actual = Post::translated('en')->orWhereTranslationLike('content', '%content1%')->get()->toArray();

        $this->assertCount(2, $actual);
        $this->assertSame('post_test_title1', $actual[0]['translations'][0]['title']);
        $this->assertSame('post_test_content1', $actual[0]['translations'][0]['content']);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.locale', 'en');
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
