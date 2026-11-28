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

    public function testCreatesArticleWithDashSlug()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $user->articles()->create([
            'title' => 'How to train your dragon',
            'description' => 'A short description',
            'body' => 'A short body',
        ]);

        $this->assertEquals('how-to-train-your-dragon', Article::firstOrFail()->slug);
    }

    public function testRegeneratesSlugWithDashesWhenTitleChanges()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $article = Article::factory()->create(['user_id' => $user->id]);

        $article->update(['title' => 'How to train your dragon']);

        $this->assertEquals('how-to-train-your-dragon', $article->fresh()->slug);
    }
}
