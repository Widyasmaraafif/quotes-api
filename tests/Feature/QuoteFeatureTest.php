<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Quote;
use App\Models\Category;
use Tests\TestCase;

class QuoteFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_a_quote(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Inspiration']);
        $quote = Quote::create([
            'quote' => 'Stay hungry, stay foolish',
            'author' => 'Steve Jobs',
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/quotes/{$quote->id}/like");

        $response->assertStatus(200)
            ->assertJson([
                'liked' => true,
                'likes_count' => 1,
            ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'quote_id' => $quote->id,
        ]);
    }

    public function test_user_can_unlike_a_quote(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Inspiration']);
        $quote = Quote::create([
            'quote' => 'Stay hungry, stay foolish',
            'author' => 'Steve Jobs',
            'category_id' => $category->id,
        ]);

        $user->likedQuotes()->attach($quote->id);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/quotes/{$quote->id}/like");

        $response->assertStatus(200)
            ->assertJson([
                'liked' => false,
                'likes_count' => 0,
            ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'quote_id' => $quote->id,
        ]);
    }

    public function test_guest_cannot_like_a_quote(): void
    {
        $category = Category::create(['name' => 'Inspiration']);
        $quote = Quote::create([
            'quote' => 'Stay hungry, stay foolish',
            'author' => 'Steve Jobs',
            'category_id' => $category->id,
        ]);

        $response = $this->postJson("/api/quotes/{$quote->id}/like");

        $response->assertStatus(401);
    }

    public function test_quotes_list_includes_likes_info(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Inspiration']);
        $quote = Quote::create([
            'quote' => 'Stay hungry, stay foolish',
            'author' => 'Steve Jobs',
            'category_id' => $category->id,
        ]);

        $user->likedQuotes()->attach($quote->id);

        // Test as guest
        $response = $this->getJson("/api/quotes");
        $response->assertStatus(200)
            ->assertJsonPath('data.0.likes_count', 1)
            ->assertJsonPath('data.0.is_liked', false);

        // Test as authenticated user
        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/quotes");
        $response->assertStatus(200)
            ->assertJsonPath('data.0.likes_count', 1)
            ->assertJsonPath('data.0.is_liked', true);
    }

    public function test_random_quote_includes_likes_info(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Inspiration']);
        $quote = Quote::create([
            'quote' => 'Stay hungry, stay foolish',
            'author' => 'Steve Jobs',
            'category_id' => $category->id,
        ]);

        $user->likedQuotes()->attach($quote->id);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/quotes/random");
        
        $response->assertStatus(200)
            ->assertJsonPath('data.likes_count', 1)
            ->assertJsonPath('data.is_liked', true);
    }

    public function test_user_can_create_quote_with_tags(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Inspiration']);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/quotes", [
                'quote' => 'Imagination is more important than knowledge',
                'author' => 'Albert Einstein',
                'category_id' => $category->id,
                'tags' => ['science', 'genius', 'physics']
            ]);

        $response->assertStatus(201)
            ->assertJsonCount(3, 'data.tags')
            ->assertJsonPath('data.tags.0.name', 'science');

        $this->assertDatabaseHas('tags', ['name' => 'science']);
        $this->assertDatabaseHas('tags', ['name' => 'genius']);
        $this->assertDatabaseHas('tags', ['name' => 'physics']);
    }

    public function test_can_filter_quotes_by_tag(): void
    {
        $category = Category::create(['name' => 'Inspiration']);
        $quote1 = Quote::create(['quote' => 'Quote 1', 'category_id' => $category->id]);
        $quote2 = Quote::create(['quote' => 'Quote 2', 'category_id' => $category->id]);
        
        $tag = \App\Models\Tag::create(['name' => 'Tech', 'slug' => 'tech']);
        $quote1->tags()->attach($tag->id);

        $response = $this->getJson("/api/quotes?tag=tech");
        
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.quote', 'Quote 1');
    }
}
