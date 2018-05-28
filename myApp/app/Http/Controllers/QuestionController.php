<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Question;
use App\Category;

class QuestionController extends Controller
{
    
    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'username'=>'required|unique:users',
            'password'=>'required',
            'email'=>'required|unique:users'
        ]);

        if($validator->fails()){
            return array(
                'error'=>true,
                'message'=> $validator->errors()->all()
            );
        }
    
        $user = new User();
    
        $user->username=$request->input('username');
        $user->email=$request->input('email');
        $user->password = (new BcryptHasher)->make($request->input('password'));
    
        $user->save();
    
        unset($user->password);
    
        return array('error'=>false,'user'=>$user);
    }

    public function login(Request $request){
        $validator =    Validator::make($request->all(),[
            'username' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return array(
                'error'=>true,
                'message'=>$validator->errors()->all()
            );
       }

       $user = User::where('username',$request->input('username'))->first();

       if(count($user)){
           if(password_verify($request->input('password'),$user->password)){
               unset($user->password);
               return array('error'=>false,'user'=>$user);
           }else {
               return array('error'=>true,'message'=>'Invalid password');
           }
       }else{
           return array('error'=>true,'message'=>'User not exist');
       }
    }


    public function getQuestions($id){
        $questions =  User::find($id)->questions;
        foreach($questions as $question){
            $question['answercount'] = count($question->answers);
        }
        return array('error' => false,'questions' => $questions);
    }
}
