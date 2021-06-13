<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $data = Video::orderBy('id','desc')->paginate(20);
        return view('backend.video.index',compact('data'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'file' => 'required|mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpeg,png,jpg|max:20240',
            'description' => 'required',
        ]);
        if($request->hasFile('file')){
            $file = $request->file('file');
            $filename = uniqid().'.'.$file->getClientOriginalExtension();
            $file->move('video/',$filename);
            $type = 'video';
            if($file->getClientOriginalExtension() == 'jpeg' || $file->getClientOriginalExtension() == 'png' || $file->getClientOriginalExtension() == 'jpg'){
                $type = 'image';
            }
            Video::create([
                'title'=>$request->title,
                'filename'=>$filename,
                'type' => $type,
                'description'=>$request->description
            ]);
            $request->session()->flash('success', 'Successfully, add video/image');
        }else{
            $request->session()->flash('failed', 'Failed, Add video/image');
        }
        return redirect()->back();
    }

    public function update(Request $request,$id)
    {
        $this->validate($request, [
            'title' => 'required',
            'file' => 'nullable|mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpeg,png,jpg|max:20240',
            'description' => 'required',
        ]);

        $fileVideo = Video::find($id);
        if($request->hasFile('file')){
            $unlink = public_path('video/'.$fileVideo->filename);
            if(file_exists($unlink)){
                unlink($unlink);
            }
            $file = $request->file('file');
            $filename = uniqid().'.'.$file->getClientOriginalExtension();
            $file->move('video/',$filename);
            $fileVideo->update([
                'title'=>$request->title,
                'filename'=>$filename,
                'description'=>$request->description
            ]);
        }
        $fileVideo->update([
            'title'=>$request->title,
            'description'=>$request->description
        ]);
        $request->session()->flash('success', 'Successfully, update video/image');
        return redirect()->back();
    }

    public function delete(Request $request,$id)
    {
        $fileVideo = Video::find($id);
        $unlink = public_path('video/'.$fileVideo->filename);
        if(file_exists($unlink)){
            unlink($unlink);
        }
        $fileVideo->delete();
        $request->session()->flash('success', 'Successfully, delete video/image');
        return redirect()->back();
    }
}
