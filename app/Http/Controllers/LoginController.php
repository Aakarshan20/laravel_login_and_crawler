<?php

namespace App\Http\Controllers;

use App\Model\Users;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use \Validator;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    private $rules = null;
    private $message = null;

    public function __construct(){
        $this->rules = [
            'name'=>'required|between:6,20',
            'password'=>'required|between:6,60',
        ];
        
        $this->message = [
            'name.required'=>'用戶名不得為空',
            'name.between'=>'用戶名稱必須在6-20位之間',            
            'password.required'=>'密碼不得為空',
            'password.between'=>'密碼必須在6-20位之間',
        ];   
    }

    public function index(){
        return view('login');
    }

    public function checkUserName(){
        $input = Input::except('_token');//除外csrf token
        
        $validator = Validator::make($input, $this->rules, $this->message);

        if($validator->passes()){//驗證通過
            $user = (new Users())->where('name', '=', $input['name'])->count();

            if(!$user){ //用戶不存在 執行註冊
                $result = $this->doRegister($input);
                $this->validateLoginStatus($result);
            } else { //用戶存在 執行登入
                $result = $this->doLogin($input);
                $this->validateLoginStatus($result);
                if(!$result){
                    return back()->withErrors(['登入失敗: 密碼錯誤']);
                }
            }

        }else{ //驗證失敗
            return back()->withErrors($validator);
        }

        
        return redirect('/');

    }

    //註冊
    protected function doRegister($input){
        
        $date = date('Y-m-d H:i:s');//當下時間

        $insertData = [
            'name' => $input['name'],
            'password' =>  Crypt::encrypt($input['password']),
            'last_login' => $date,
            'updated_at' => $date,
            'created_at' => $date
        ]; 

        $user = new Users();
        return $user->create($insertData);
    }

    //登入
    protected function doLogin($input){
        $user = new Users();
        $user = $user->where('name', '=', $input['name'])->first();

        $password_ori = Crypt::decrypt($user->password); //將password解密
        
        if($password_ori == $input['password']){ //與傳入的password比對
            $updateData = [ 'last_login'=>date("y-m-d H:i:s") ];
            $updateResult = $user->where('name', '=', $input['name'])->update($updateData);
            if(!$updateResult){
                Log::error('user last_login update fail, id=',$user->id );                    
            }
            return $user;
        } else {
            return false;
        }
    }

    //登出
    public function logout(){
        $this->removeSession('user');
        return redirect('/login');
    } 

    //確認狀態 對session操作
    protected function validateLoginStatus($data){
        if($data){
            $this->writeSession('user', $data);
        } else {
            $this->removeSession('user');
        } 
    }

    //紀錄session
    protected function writeSession($key, $value){
        session([$key => $value]);
    }

    //清除session
    protected function removeSession($key){
        session([$key => null]);
    }


}
