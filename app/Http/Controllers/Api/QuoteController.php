<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    // Get all quotes
    public function index()
    {
        return response()->json(
            Quote::with('category')->get()
        );
    }

    // Generate random quote
    public function random()
    {
        $quote = Quote::with('category')
            ->inRandomOrder()
            ->first();

        return response()->json($quote);
    }

    public function byCategory($id)
    {
        return Quote::with('category')
            ->where('category_id', $id)
            ->get();
    }

    public function byCategoryName($name)
    {
        return Quote::with('category')
            ->whereHas('category', function ($query) use ($name) {
                $query->where('name', $name);
            })
            ->get();
    }

    // Store new quote
    public function store(Request $request)
    {
        $request->validate([
            'quote' => 'required',
            'author' => 'nullable',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        $quote = Quote::create($request->all());

        return response()->json($quote->load('category'), 201);
    }

    // Delete quote
    public function destroy($id)
    {
        $quote = Quote::findOrFail($id);
        $quote->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
