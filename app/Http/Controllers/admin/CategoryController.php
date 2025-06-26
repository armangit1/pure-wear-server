<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;



class CategoryController extends Controller
{
    public function index()
    {
        $categories = Categorie::where("user_id",Auth::id())->get();

        return response()->json([
            'status' => 200,
            'data' => $categories
        ]);
    }

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

            ], 400);
        }

        $category = new Categorie();
        $category->name = $request->name;
        $category->status = $request->status;
        $category->user_id = Auth::id();
        $category->save();

        return response()->json([

            'status' => 200,
            'message' => 'Category added successfully.',
            'data' => $category

        ]);
    }
    public function show($id)
    {

        $categories = Categorie::find($id);

        if ($categories == null) {
            return response()->json([

                'status' => 400,
                'massage' => 'category not fund.',
                'data' => []

            ], 400);
        }

        return response()->json([
            'status' => 200,
            'data' => $categories

        ]);
    }
    public function update($id, Request $request)
    {


        $category = Categorie::find($id);

        if ($category == null) {
            return response()->json([
                'status' => 400,
                'massage' => 'category not fund.',
                'data' => []
            ], 400);
        }

        $validetor = Validator::make($request->all(), [
            'name' => 'required',
              'status' => 'required'
          

        ]);

        if ($validetor->fails()) {
            return response()->json([

                'status' => 400,
                'errors' => $validetor->errors()

            ], 400);
        }


        $category->name = $request->name;
        $category->status = $request->status;
        $category->user_id = Auth::id();
        $category->save();

        return response()->json([

            'status' => 200,
            'message' => 'Category Update successfully.',
            'data' => $category

        ]);
    }
public function destroy($id)
{
    $category = Categorie::findOrFail($id);

    if ($category->user_id !== Auth::id()) {
        return response()->json([
            'status' => 403,
            'message' => 'Unauthorized action.',
        ], 403);
    }

    $category->delete();

    return response()->json([
        'status' => 200,
        'message' => 'Category deleted successfully.',
    ]);
}

}
