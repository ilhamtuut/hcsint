<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $data = Question::orderBy('name')->paginate(20);
        return view('backend.question.index', compact('data'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $hasPassword = Hash::check($request->pin_authenticator, Auth::user()->trx_password);
        if($hasPassword){
            Question::create($request->all());
            $request->session()->flash('success', 'Successfully, add data question');
        }else{
            $request->session()->flash('failed', 'Failed, PIN Authenticator is wrong');
        }
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $hasPassword = Hash::check($request->pin_authenticator, Auth::user()->trx_password);
        if($hasPassword){
            Question::find($id)->update($request->all());
            $request->session()->flash('success', 'Successfully, update data question');
        }else{
            $request->session()->flash('failed', 'Failed, PIN Authenticator is wrong');
        }
        return redirect()->back();
    }

    public function viewAnswer(Request $request)
    {
        if($request->user && Auth::id() != $request->user){
            abort(403);
        }
        $data = Question::orderBy('name')->get();
        return view('backend.question.answer', compact('data'));
    }

    public function answer(Request $request)
    {
        $this->validate($request, [
            'question' => 'required',
            'answer' => 'required',
            'link' => 'nullable',
        ]);

        $user_id = Auth::id();
        $check = Answer::where('user_id',$user_id)->first();
        if($check){
            $check->update([
                'question_id' => $request->question,
                'answer'=> Hash::make($request->answer)
            ]);
            $request->session()->flash('success', 'Successfully, reset your answer secret questions');
        }else{
            Answer::create([
                'user_id' => $user_id,
                'question_id' => $request->question,
                'answer'=> Hash::make($request->answer)
            ]);
            $request->session()->flash('success', 'Successfully, answer secret questions');
        }

        if($request->link){
            return redirect()->to($request->link);
        }else{
            return redirect('home');
        }
    }
}
