<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use OpenApi\Attributes as OA;

class TagController extends Controller
{
    #[OA\Get(
        path: "/api/tags",
        tags: ["Tags"],
        summary: "Get list of tags",
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function index()
    {
        return TagResource::collection(Tag::all());
    }
}
