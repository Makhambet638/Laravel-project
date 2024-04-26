<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 

class ListingController extends Controller
{
    use HasFactory;

    // protected $fillable = ['title', 'company', 'location', 'website', 'email', 'description','tags'];

    //show all listings
    public function index() {
        return view('listings.index', [
            'listings' => Listing::latest()->filter(request(['tag','search']))->paginate(6)
        ]);
    }

    //show single listing
    public function show(Listing $listing) {
        return view('listings.show', [
            'listing' => $listing
            ]);
    }

    //show create form
    public function create() {
        return view('listings.create');
    }

    //store listing data
    public function store(Request $request) {
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' => 'required',
            'website' => 'required',
            'email' =>  ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
        ]);

        if($request->hasFile('logo')){
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $formFields['user_id'] = auth()->id();

        Listing::create($formFields);

        return redirect('/')->with('message', 'Listing created sucessfully!');
    }

    // show edit form
    public function edit(Listing $listing) {
        return view('listings.edit', ['listing' => $listing]);
    }

    // update listing data
        public function update(Request $request, Listing $listing) {    
            logger('ssssssssssssssssssssssssssssss');
            // make sure logged in user is owner
            if($listing->user_id != auth()->id()){
                abort(403, 'Unauthorized action');
            }

            $formFields = $request->validate([
                'title' => 'required',
                'company' => ['required',],
                'location' => 'required',
                'website' => 'required',
                'email' =>  ['required', 'email'], 
                'tags' => 'required',
                'description' => 'required'
            ]);
    
        if($request->hasFile('logo')){
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }
    
            $listing->update($formFields);
    
            return back()->with('message', 'Listing created sucessfully!');
    }

    // delete listing
    public function delete(Listing $listing) {
        // make sure logged in user is owner
        if($listing->user_id != auth()->id()){
            abort(403, 'Unauthorized action');
        }
        $listing->delete();
        return redirect('/')->with('message', ' Listing deleted succesfully');
    }

    // manage listings
    public function manage() {
    return view('listings.manage', ['listings' => auth()->user()->listings()->get()]);
    }
}