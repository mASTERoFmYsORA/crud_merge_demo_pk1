<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\ContactCustomField;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class ContactController extends Controller
{
    public function index()
    {
        return view('contacts.index', ['contacts' => Contact::with('customFields')->get()]);
    }

    public function getIndexData()
    {
        $query = Contact::query(); // Fetch contacts

        return DataTables::of($query)
            // ->editColumn('merged_email', function ($query) {
            //     return json_encode($query->merged_email ?? []); // If null, return empty JSON
            // })
            ->addColumn('action', function ($contact) {
                $deleteUrl = route('contacts.destroy', $contact->id);
                $editUrl = route('contacts.edit', $contact->id);
                return '  <a class="btn btn-primary btn-sm" href="' . $editUrl . '">Edit</a><button class="btn btn-sm btn-danger deleteRecord" data-id="' . $contact->id . '" data-url="' . $deleteUrl . '">Delete</button>
                 <button class="btn btn-primary btn-sm merge-btn" data-id="' . $contact->id . '">
                        Merge
                    </button>';

            })
            ->toJson();
    }

    public function mergeContacts(Request $request)
    {
        // dd($request->all());
        $masterId = $request->master_id;
        $secondaryId = $request->secondary_id;

        // Fetch contacts
        $masterContact = Contact::find($masterId);
        $secondaryContact = Contact::find($secondaryId);

        if (!$masterContact || !$secondaryContact) {
            return response()->json(['error' => 'One or both contacts not found.'], 400);
        }

        // Merge Emails
        $mergedEmails = array_unique(array_filter(array_merge(
            explode(',', $masterContact->email ?? ''),
            explode(',', $secondaryContact->email ?? '')
        )));
        $masterContact->merged_email = implode(',', $mergedEmails);
        // dd($masterContact->merged_email);
        // Merge Phone Numbers
        // $mergedPhones = array_unique(array_filter(array_merge(
        //     explode(',', $masterContact->phone ?? ''),
        //     explode(',', $secondaryContact->phone ?? '')
        // )));
        // $masterContact->phone = implode(',', $mergedPhones);

        // Merge Custom Fields
        $masterCustomFields = $masterContact->merged_email ?? [];
        $secondaryCustomFields[] = $secondaryContact->merged_email ?? [];
        // dd($masterCustomFields);

        foreach ($secondaryCustomFields as $key => $value) {
            if (!isset($masterCustomFields[$key])) {
                $masterCustomFields[$key] = $value; // Add missing fields
            }
        }

        // dd($masterContact);
        $masterContact->merged_email = $masterCustomFields;
        $masterContact->save();

        // Delete secondary contact
        // $secondaryContact->delete();

        return response()->json(['success' => 'Contacts merged successfully!']);
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:contacts',
            'phone' => 'required',
            'gender' => 'required',
            'profile_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'additional_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Handle file uploads
        $profileImagePath = $request->file('profile_image') ? $request->file('profile_image')->store('profiles', 'public') : null;
        $additionalFilePath = $request->file('additional_file') ? $request->file('additional_file')->store('documents', 'public') : null;

        // Create contact
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'profile_image' => $profileImagePath,
            'additional_file' => $additionalFilePath,
        ]);

        // Save custom fields
        if ($request->custom) {
            foreach ($request->custom as $field_value) {
                ContactCustomField::create([
                    'contact_id' => $contact->id,
                    'field_name' => $field_value['name'],
                    'field_value' => $field_value['value'],
                ]);
            }
        }

        return redirect()->route('contacts.index')->with('success', 'Contact added successfully.');
    }

    public function edit($id)
    {
        $contact = Contact::findOrFail($id);
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        // Handle file uploads
        $updateContacts = $request->all();
        $profileImagePath = $request->file('profile_image') ? $request->file('profile_image')->store('profiles', 'public') : null;
        $additionalFilePath = $request->file('additional_file') ? $request->file('additional_file')->store('documents', 'public') : null;
        // dd($request->file('profile_image') != '');
        if ($request->file('profile_image') != '') {
            $updateContacts['profile_image'] = $profileImagePath;

        }
        if ($request->file('additional_file') != '') {
            $updateContacts['additional_file'] = $additionalFilePath;
        }

        $contact->update($updateContacts);

        $customField = ContactCustomField::where('contact_id', $id)->get();
        foreach ($customField as $custom) {
            $custom->delete();
        }

        // Save custom fields
        if ($request->custom) {
            foreach ($request->custom as $field_value) {
                ContactCustomField::create([
                    'contact_id' => $contact->id,
                    'field_name' => $field_value['name'],
                    'field_value' => $field_value['value'],
                ]);
            }
        }

        return redirect()->route('contacts.index')
            ->with('success', 'Contact updated successfully');
    }

    public function destroy($id)
    {
        // dd($id);
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json(['success' => 'Record deleted successfully!']);
    }

}
