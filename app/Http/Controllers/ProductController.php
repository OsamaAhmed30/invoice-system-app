<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections=Section::all();
        $products=Product::all();
        return view('products.product' , compact('products','sections'));
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
            "Product_name" => 'required',
            "description" => 'required',
        ],
       [
        "Product_name.required" => 'عفواً يجب ادخال اسم المنتج !! ',
        "description.required" => 'عفواً يجب ادخال وصف المنتج !! ',
        ]
    
    
    );

        Product::create([
            'Product_name' => $request->Product_name,
            'section_id' => $request->section_id,
            'description' => $request->description,
        ]);
        
        return redirect('/products')->with('Add', 'تم اضافة المنتج بنجاح ');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        $section_id=Section::where("section_name" , $request->section_name)->first()->id;

        $product = Product::find($request->pro_id);
        

        $this->validate($request,[
            "Product_name" => 'required',
            "description" => 'required',
        ],
       [
        "Product_name.required" => 'عفواً يجب ادخال اسم المنتج !! ',
        "description.required" => 'عفواً يجب ادخال وصف المنتج !! ',
        ]);
    

        $product->update([
            'Product_name' => $request->Product_name,
            'section_id' => $section_id,
            'description' => $request->description,
        ]);
       
        return redirect('products')->with('Edit' , 'تم تعديل القسم بنجاح' );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {

        //return $request;
       $product = Product::where("id",$request->pro_id)->first();
       $product->delete();
       return redirect('products')->with('delete' , 'تم مسح القسم بنجاح' );
    }
}
