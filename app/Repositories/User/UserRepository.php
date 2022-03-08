<?php

namespace App\Repositories\User;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client as OClient;
use GuzzleHttp\Exception\ClientException;
use App\Repositories\User\UserRepositoryInterface;
use App\Models\UserInfo;
use App\Http\Requests\UserInfoRequest;


class UserRepository implements UserRepositoryInterface
{
    const SUCCUSUS_STATUS_CODE = 200;
    const UNAUTHORISED_STATUS_CODE = 401;
    

    public function __construct(Client $client)
    {
        $this->http = $client;
        $this->baseUrl = env('APP_URL');
    }
    public function register(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user =  User::create($input);
        $info = $user->personalInfo()->make([
            "nick_name" => $request->input('name'),
            'avatar' => 'https://cdn.cnbj0.fds.api.mi-img.com/b2c-data-mishop/f790b51a76afd7b41522048fa779d69d.jpg'
        ]);
        $info->user()->associate($user);
        $info->save();
        $response = $this->getTokenAndRefreshToken($email, $password);
        return $this->response($response["data"], $response["statusCode"]);
    }

    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $response = $this->getTokenAndRefreshToken($email, $password);
            $user_id = Auth::user()->id;
            $data = array_merge($response["data"], ['user_id' => $user_id]);
            $statusCode =  $response["statusCode"];
        } else {
            $data = ['error' => 'Unauthorised'];
            $statusCode =  self::UNAUTHORISED_STATUS_CODE;
        }

        return $this->response($data, $statusCode);
    }

    public function refreshToken(Request $request)
    {
        if (is_null($request->header('Refreshtoken'))) {
            return $this->response(['error' => 'Unauthorised'], self::UNAUTHORISED_STATUS_CODE);
        }

        $refresh_token = $request->header('Refreshtoken');
        $Oclient = $this->getOClient();
        $formParams = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
            'client_id' => $Oclient->id,
            'client_secret' => $Oclient->secret,
            'scope' => '*'
        ];

        return $this->sendRequest("/oauth/token", $formParams);
    }

    public function userinfo()
    {
        $user = Auth::user();

        $userInfo = User::where('id', '=', $user->id)->with('personalInfo')->first();
        return $this->response($userInfo, self::SUCCUSUS_STATUS_CODE);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->response(['message' => 'Successfully logged out'], self::SUCCUSUS_STATUS_CODE);
    }

    public function response($data, int $statusCode)
    {
        $response = ["data" => $data, "statusCode" => $statusCode];
        return $response;
    }

    public function getTokenAndRefreshToken(string $email, string $password)
    {
        $Oclient = $this->getOClient();
        $formParams = [
            'grant_type' => 'password',
            'client_id' => $Oclient->id,
            'client_secret' => $Oclient->secret,
            'username' => $email,
            'password' => $password,
            'scope' => '*'
        ];

        return $this->sendRequest("/oauth/token", $formParams);
    }

    public function sendRequest(string $route, array $formParams)
    {
        try {
            $url = $this->baseUrl . $route;
            $response = $this->http->request('POST', $url, ['form_params' => $formParams]);

            $statusCode = self::SUCCUSUS_STATUS_CODE;
            $data = json_decode((string) $response->getBody(), true);
        } catch (ClientException $e) {
            echo $e->getMessage();
            $statusCode = $e->getCode();
            $data = ['error' => 'OAuth client error'];
        }

        return ["data" => $data, "statusCode" => $statusCode];
    }

    public function getOClient()
    {
        return OClient::where('password_client', 1)->first();
    }

    public function getPersonalInfo()
    {
        $personalInfo = Auth::user()->personalInfo;
        $response = ["data" => $personalInfo, "statusCode" => "200"];
        return $response;
    }

    public function updatePersonalInfo(UserInfoRequest $request)
    {
        $user = Auth::user();
        $userInfo = UserInfo::where('user_id', '=', $user->id);
        //如果请求包含头像则保存用户头像并写入数据库 否则不更新头像
        if ($avatar = $request->file('avatar')) {

            if ($avatar && $avatar->isValid()) {
                $destinationPath = storage_path('app/public/photos');
                //如果目标目录不存在 则创建之
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath);
                }
                //文件名
                $fileName = time() . "-" . $avatar->getClientOriginalName();
                //保存文件到 目标目录
                $avatar->move($destinationPath, $fileName);
                $file_url = env('APP_URL') . '/storage/photos/' . $fileName;
            }
            //如果用户个人信息已存在则更新 否则新建一个信息
            if ($user->PersonalInfo) {
                $userInfo->update([
                    'avatar' => $file_url,
                    'nick_name' => $request->input('nick_name'),
                    'sex' => $request->input('sex'),
                    'country' => $request->input('country')
                ]);
            } else {
                $userInfo = new UserInfo([
                    'avatar' => $file_url,
                    'nick_name' => $request->input('nick_name'),
                    'sex' => $request->input('sex'),
                    'country' => $request->input('country')
                ]);
                $userInfo->user()->associate($user);
                $userInfo->save();
            }
        } else {
            //如果用户个人信息已存在则更新 否则新建一个信息
            if ($user->PersonalInfo) {
                $userInfo->update([
                    'nick_name' => $request->input('nick_name'),
                    'sex' => $request->input('sex'),
                    'country' => $request->input('country')
                ]);
            } else {
                $userInfo = new UserInfo([

                    'nick_name' => $request->input('nick_name'),
                    'sex' => $request->input('sex'),
                    'country' => $request->input('country')
                ]);
                $userInfo->user()->associate($user);
                $userInfo->save();
            }
        };


        $response = ['statusCode' => '200'];
        return $response;
    }
}
