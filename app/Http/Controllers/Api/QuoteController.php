<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\QuoteResource;

use OpenApi\Attributes as OA;

class QuoteController extends Controller
{
    #[OA\Get(
        path: "/api/quotes",
        operationId: "getQuotesList",
        tags: ["Quotes"],
        summary: "Get list of quotes",
        description: "Returns list of quotes with search and pagination",
        parameters: [
            new OA\Parameter(name: "search", in: "query", description: "Search by author or quote text", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "per_page", in: "query", description: "Number of items per page", required: false, schema: new OA\Schema(type: "integer", default: 10))
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function index(Request $request)
    {
        $query = Quote::with('category')
            ->withCount('likes');

        if (Auth::check()) {
            $query->withExists(['likes as is_liked' => function ($q) {
                $q->where('user_id', Auth::id());
            }]);
        }

        // Search by author or quote text
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('author', 'like', "%{$search}%")
                  ->orWhere('quote', 'like', "%{$search}%");
            });
        }

        // Filter by category_id
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by category name
        if ($request->has('category')) {
            $categoryName = $request->input('category');
            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        return QuoteResource::collection($query->paginate($perPage));
    }

    #[OA\Get(
        path: "/api/quotes/random",
        tags: ["Quotes"],
        summary: "Get random quote",
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function random()
    {
        $query = Quote::with('category')
            ->withCount('likes');

        if (Auth::check()) {
            $query->withExists(['likes as is_liked' => function ($q) {
                $q->where('user_id', Auth::id());
            }]);
        }

        $quote = $query->inRandomOrder()->first();

        return new QuoteResource($quote);
    }

    #[OA\Get(
        path: "/api/quotes/qotd",
        tags: ["Quotes"],
        summary: "Get quote of the day",
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function qotd()
    {
        $quote = cache()->remember('qotd', now()->endOfDay(), function () {
            return Quote::with('category')
                ->withCount('likes')
                ->inRandomOrder()
                ->first();
        });

        // Set is_liked dynamically if user is logged in
        if ($quote && Auth::check()) {
            $quote->is_liked = $quote->likes()->where('user_id', Auth::id())->exists();
        }

        return new QuoteResource($quote);
    }

    #[OA\Get(
        path: "/api/quotes/category/{id}",
        tags: ["Quotes"],
        summary: "Get quotes by category ID",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function byCategory(Request $request, $id)
    {
        $perPage = $request->input('per_page', 10);
        $query = Quote::with('category')
            ->withCount('likes')
            ->where('category_id', $id);

        if (Auth::check()) {
            $query->withExists(['likes as is_liked' => function ($q) {
                $q->where('user_id', Auth::id());
            }]);
        }

        $quotes = $query->paginate($perPage);

        return QuoteResource::collection($quotes);
    }

    #[OA\Get(
        path: "/api/quotes/category/name/{name}",
        tags: ["Quotes"],
        summary: "Get quotes by category name",
        parameters: [
            new OA\Parameter(name: "name", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function byCategoryName(Request $request, $name)
    {
        $perPage = $request->input('per_page', 10);
        $query = Quote::with('category')
            ->withCount('likes')
            ->whereHas('category', function ($query) use ($name) {
                $query->where('name', $name);
            });

        if (Auth::check()) {
            $query->withExists(['likes as is_liked' => function ($q) {
                $q->where('user_id', Auth::id());
            }]);
        }

        $quotes = $query->paginate($perPage);

        return QuoteResource::collection($quotes);
    }

    #[OA\Post(
        path: "/api/quotes",
        tags: ["Quotes"],
        summary: "Store new quote",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["quote"],
                properties: [
                    new OA\Property(property: "quote", type: "string"),
                    new OA\Property(property: "author", type: "string"),
                    new OA\Property(property: "category_id", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Quote created"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'quote' => 'required',
            'author' => 'nullable',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        $quote = Quote::create($request->all());

        return new QuoteResource($quote->load('category'));
    }

    #[OA\Delete(
        path: "/api/quotes/{id}",
        tags: ["Quotes"],
        summary: "Delete a quote",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Deleted successfully"),
            new OA\Response(response: 404, description: "Quote not found")
        ]
    )]
    public function destroy($id)
    {
        $quote = Quote::findOrFail($id);
        $quote->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    #[OA\Post(
        path: "/api/quotes/{id}/like",
        tags: ["Quotes"],
        summary: "Toggle like on a quote",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function toggleLike($id)
    {
        $quote = Quote::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->likedQuotes()->where('quote_id', $id)->exists()) {
            $user->likedQuotes()->detach($id);
            $liked = false;
        } else {
            $user->likedQuotes()->attach($id);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $quote->likes()->count()
        ]);
    }
}
