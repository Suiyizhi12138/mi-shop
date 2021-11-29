<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserInfoRequest;
use Illuminate\Support\Facades\Storage;
// use App\Repositories\User\UserRepositoryInterface;
//自定义工具类
use App\Repositories\User\UserRepository;


class UserController extends Controller {
    const SUCCUSUS_STATUS_CODE = 200;
    const UNAUTHORISED_STATUS_CODE = 401;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function login(UserLoginRequest $request) {
        $response = $this->userRepository->login($request);
        return response()->json($response["data"], $response["statusCode"]);
    }

    public function register(UserRegisterRequest $request) {
        $response = $this->userRepository->register($request);
        return response()->json($response["data"]);
    }

    public function userinfo() {
        $response = $this->userRepository->userinfo();
        return response()->json($response["data"], $response["statusCode"]);
    }

    public function logout(Request $request) {
        $response = $this->userRepository->logout($request);
        return response()->json($response["data"], $response["statusCode"]);
    }

    public function refreshToken(Request $request) {
        $response = $this->userRepository->refreshToken($request);
        return response()->json($response["data"], $response["statusCode"]);
    }


    //下面是用户信息userinfo 逻辑
    /**
     * 修改用户信息
     * params: id  
     * 
     */
    public function getPersonalInfo(){
        $response = $this->userRepository->getPersonalInfo();
        return response()->json($response['data'],200);
    }
    
    public function updatePersonalInfo(UserInfoRequest $request){
        $response = $this->userRepository->updatePersonalInfo($request);

        return response()->json($response['statusCode']);
    }

    //保存用户头像
    public function saveAvatar(Request $request){
      $avatar = $request->file('avatar');
      
      if($avatar && $avatar->isValid()){
        $destinationPath = storage_path('app/public/photos');
        //如果目标目录不存在 则创建之
        if(!file_exists($destinationPath)){
            mkdir($destinationPath);
        }
        //文件名
        $fileName = time()."-".$avatar->getClientOriginalName();
        //保存文件到 目标目录
        $avatar->move($destinationPath,$fileName);        
        
        $file_url = $destinationPath . DIRECTORY_SEPARATOR . $fileName;

        return response()->json($file_url);
      }
    }
    
    
    /**
     * //用户喜欢
    */
    //添加到用户喜欢
    public function addToLike(Request $request){
        $user = $request->user();
    }

    //下面是用户地址 逻辑
    /**
     * 
    */
}