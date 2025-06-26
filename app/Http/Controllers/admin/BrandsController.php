<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function Pest\Laravel\json;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::where("userId",Auth::id())->get();
        if ($brands == null) {
            return response()->json([
                'status' => 400,
                'massage' => 'Brand is not fund.',
                'data' => []
            ]);
        }
        return response()->json([
            'status' => 200,
            'data' => $brands
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validetor = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required'
        ]);

        if ($validetor->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validetor->errors()
            ]);
        }

        $brands = new Brand();

        $brands->name = $request->name;
        $brands->status = $request->status;
        $brands->userId =Auth::id();
        $brands->save();

        return response()->json([
            'status' => 200,
            'massage' => 'brands added successfully.',
            'data' => $brands
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       $brand = Brand::find($id);
       if($brand==null){
        return response()->json([
            'status'=>400,
            'massage'=>'brands is not fund.',
            'data' => []
        ]);
       }
      return response()->json([
            'status'=>200,
           
            'data' => $brand
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $brand = Brand::find($id);

        if($brand==null){
             return response()->json([
                'status' => 400,
                'massage' => 'Brand is not fund.',
               
            ]);
        }

        $validetor = Validator::make($request->all(),[
            'name' => 'required',
            'status' => 'required'

        ]);
        if($validetor->fails()){
               return response()->json([
                'status' => 400,
                'errors' => $validetor->errors()
            ]);
        }

        $brand->name = $request->name;
        $brand->status = $request->status;
        $brand->userId = Auth::id();
        
        $brand->save();


        return response()->json([

            'status'=>200,
            'massage' => 'Brands update successfully.',
            'data' =>$brand

        ]);

        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
          $brand = Brand::find($id);

        if($brand==null){
             return response()->json([
                'status' => 400,
                'massage' => 'Brand is not fund.',
               
            ]);
        }

        $brand->delete();
          return response()->json([

            'status'=>200,
            'massage' => 'Brands Delete successfully.',
         

        ]);

    }
}
