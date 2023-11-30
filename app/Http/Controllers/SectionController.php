<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = Section::all();
        return view("Sections.sections",compact('sections'));
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

        $validateRequest = $request->validate([
            "section_name" => 'required|unique:sections',
            "description" => 'required',
        ],
       [
        "section_name.required" => 'عفواً يجب ادخال اسم القسم !! ',
            "section_name.unique" => 'عفواً القسم موجود مسبقا !!',
            "description.required" => 'عفواً يجب ادخال وصف القسم !! ',
        ]
    
    
    );

        Section::create([
            'section_name'=>$request->section_name,
            'description'=>$request->description,
            'created_by'=>Auth::user()->name,
        ]);
        //session()->flash('Add' , 'تم اضافة القسم بنجاح' );
        return redirect('sections')->with('Add' , 'تم اضافة القسم بنجاح' );
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $section = Section::findorfail($request->id);
        $this->validate($request,[
            "section_name" => 'required|unique:sections,section_name,'.$request->id,
            "description" => 'required',
        ],
       [
        "section_name.required" => 'عفواً يجب ادخال اسم القسم !! ',
            "section_name.unique" => 'عفواً القسم موجود مسبقا !!',
            "description.required" => 'عفواً يجب ادخال وصف القسم !! ',
        ]
    
    
    );

        $section->update([
           'section_name'=>$request->section_name,
            'description'=>$request->description,
            'created_by'=>Auth::user()->name,
        ]);
       
        return redirect('sections')->with('Edit' , 'تم تعديل القسم بنجاح' );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        Section::findorfail($request->id)->delete();
        return redirect('sections')->with('delete' , 'تم حذف القسم بنجاح' );

    }
}
