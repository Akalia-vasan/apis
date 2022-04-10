<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::where('status', $request->status)
        ->where('user_id', auth()->user()->id)->get();
        return response()->json([
            "success" => true,
            "message" => "Fetch All Items",
            "data" => $items
        ]);
    }

    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'title' => 'required|unique:items',
            'body' => 'required',
            'attachment' => 'required',
            'reminder' => 'required',
        ]);
        if($validator->fails())
        {
        	return response()->json([
                'status'    =>  422,
                'message'   =>  'Failed!',
                'result'    =>  (object) $validator->errors()
            ]);
        } 
        $item = new Item();
        $item->title = $request->title;
        $item->body = $request->body;
        $item->user_id = auth()->user()->id;
        $item->due_date = $request->due_date;
        $item->reminder = $request->reminder;
        if($request->has('attachment'))
        {
            $backupLoc='public/item/';
            if(!is_dir($backupLoc)) {
                Storage::makeDirectory($backupLoc, 0755, true, true);
            }
            $name = time().'_'.$request->attachment->getClientOriginalName();
            Storage::disk('public')->put('item/'.$name,file_get_contents($request->attachment),'public');
            $item->attachment  = 'item/'.$name;
        }
        $item->status = $request->status;
        $item->save();

        return response()->json([
            'status'    =>  200,
            'message'   =>  'Success!',
            'result'    =>  $item
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|unique:items,title,'.$id,
            'body' => 'required',
            'attachment' => 'required',
            'reminder' => 'required',
        ]);
        if($validator->fails())
        {
        	return response()->json([
                'status'    =>  422,
                'message'   =>  'Failed!',
                'result'    =>  (object) $validator->errors()
            ]);
        } 
        $item = Item::find($id);
        $item->title = $request->title;
        $item->body = $request->body;
        $item->due_date = $request->due_date;
        $item->reminder = $request->reminder;
        if($request->has('attachment'))
        {
            if(Storage::disk('public')->exists($item->attachment))
            {
                Storage::disk('public')->delete($item->attachment);
            }
            $name = time().'_'.$request->attachment->getClientOriginalName();
            Storage::disk('public')->put('item/'.$name,file_get_contents($request->attachment),'public');
            $item->attachment  = 'item/'.$name;
        }
        $item->status = $request->status;
        $item->save();

        return response()->json([
            'status'    =>  200,
            'message'   =>  'Success!',
            'result'    =>  $item
        ]);
    }

    public function destroy($id)
    {
        $item = Item::find($id);
        if(!isset($item))
        {
            return response()->json([
                'status'    =>  500,
                'message'   =>  'Item not available!',
            ]);
        }
        $item->delete();
        return response()->json([
            'status'    =>  200,
            'message'   =>  'Delete Success!',
        ]);
    }

    public function updateStatus($id)
    {
        $item = Item::find($id);
        if(!isset($item))
        {
            return response()->json([
                'status'    =>  500,
                'message'   =>  'Item not available!',
            ]);
        }
        $item->status = 'complete';
        $item->save();
        return response()->json([
            'status'    =>  200,
            'message'   =>  'Success!',
            'result'    =>  $item
        ]);
    }

    public function order(Request $request)
    {
        $items = Item::orderBy('due_date', $request->order)->get();
        
        
        return response()->json([
            'status'    =>  200,
            'message'   =>  'Success!',
            'result'    =>  $items
        ]);
    }
}
