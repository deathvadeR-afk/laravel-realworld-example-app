<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function testShowArticle()
    {
        $article = Article::factory()->create();

        $response = $this->get('api/articles/' . $article->slug)
            ->assertExactJson([
                'article' => [
                    'slug' => $article->slug,
                    'title' => $article->title,
                    'body' => $article->body,
                    'description' => $article->description,
                    'tagList' => [],
                    'createdAt' => $article->created_at,
                    'updatedAt' => $article->updated_at,
                    'favorited' => false,
                    'favoritesCount' => 0,
                    'author' => [
                        'username' => $article->user->username,
                        'bio' => $article->user->bio,
                        'image' => $article->user->image,
                        'following' => false
                    ]
                ]
            ]);
    }

    public function testCreatingArticleWithMultiWordTitleProducesDashedSlug()
    {
        $user = User::factory()->create();

        $article = new Article([
            'title' => 'How to train your dragon',
            'description' => 'A description',
            'body' => 'A body',
        ]);
        $article->user_id = $user->id;
        $article->save();
        $article->refresh();

        $this->assertSame('how-to-train-your-dragon', $article->slug);
    }

    public function testRetitlingArticleRegeneratesSlugWithDashes()
    {
        $user = User::factory()->create();

        $article = new Article([
            'title' => 'Old title',
            'description' => 'A description',
            'body' => 'A body',
        ]);
        $article->user_id = $user->id;
        $article->save();

        $article->title = 'How to train your dragon';
        $article->save();
        $article->refresh();

        $this->assertSame('how-to-train-your-dragon', $article->slug);
    }
}
