<?php

namespace App\Http\Controllers;

use App\Models\ContactCustomField;
use Illuminate\Http\Request;

class ContactCustomFieldController extends Controller
{

    public function addField(Request $request)
    {
        $index = $request->index; // Get index for unique field names
        return response()->json([
            'html' => view('partials.dynamic-field', compact('index'))->render()
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactCustomField $contactCustomField)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactCustomField $contactCustomField)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactCustomField $contactCustomField)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactCustomField $contactCustomField)
    {
        //
    }
}
