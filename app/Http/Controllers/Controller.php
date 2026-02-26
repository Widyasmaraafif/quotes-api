<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Quotes API Documentation",
    description: "API for managing and retrieving inspirational quotes",
    contact: new OA\Contact(email: "admin@quotes-api.com")
)]
#[OA\Server(
    url: "http://127.0.0.1:8000",
    description: "Local Development Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
#[OA\Tag(name: "Auth", description: "Authentication endpoints")]
#[OA\Tag(name: "Quotes", description: "Operations about quotes")]
#[OA\Tag(name: "Categories", description: "Operations about categories")]
abstract class Controller
{
    //
}
