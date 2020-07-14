<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Socialite;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => ['required',
                            'min:8',
                            'max:32',
                            'confirmed',
                            'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'
                            ]
        ]);
        $key = $this->genKey();
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'public_key' => $key['public_key'],
            'private_key_hash' => $this->hashPrivateKey($key['private_key'], $request->password)
        ]); 
        $user->save();
        $user->sendEmailVerificationNotification();
        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Tài khoản hoặc mật khẩu không chính xác'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'email_verified_at' => $user->email_verified_at,
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request, $username=null)
    {
        return response()->json($request->user());
    }

    public function changePassword(Request $request) {
        $request->validate([
            'current_password' => 'required|string',
            'password' => ['required',
                            'min:6',
                            'max:32',
                            'confirmed',
                            'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                            'same:password'
                            ],
            'password_confirmation' => 'required|same:password',           
        ]);
        $user = Auth::user();
        $hash_password = $user->password;
        $oldPassword = $request->current_password;
        $privateKeyHash = Auth::user()->private_key_hash;
        $newPassword = $request->password;

        if(Hash::check($oldPassword, $hash_password)) {
            $privateKey = $this->decryptPrivateKey($oldPassword, $privateKeyHash);
            User::find($user->id)->update([
                'password' => bcrypt($newPassword),
                'private_key_hash' => $this->hashPrivateKey($privateKey, $newPassword)
            ]);
            return response(['message' => 'Đổi mật khẩu thành công'], 200);
        }
        return response()->json(['message' => 'Sai mật khẩu cũ'], 500);
    }
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();
        $newUser = User::where('email', $user->email)->first();
        if (!$newUser) {
            $newUser = User::create([
                'name'     => $user->name,
                'email'    => $user->email,
                'provider' => $provider
            ]);
        }
        if (!$newUser->provider) {
            
            return redirect('http://webchat.com:8081/google/null');
        }
        $tokenResult = $newUser->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();
        
        return redirect('https://localhost:8081/token?token='.$tokenResult->accessToken);
    }

    public function verify(Request $request)
    {
        $userId = $request->route('id');
        $user = User::find($userId);
        if ($user->email_verified_at != null) {
            return response(['message'=>'Already verified']);
        }

        $user->update(['email_verified_at'=> Carbon::now()->toDateTimeString()]);
        return response(['message'=>'Already verified']);
    }


    public function pbkdf2($password)
    {
        $iterations = 1000;
        $salt = "261cf07ed6818288c492";
        $key = hash_pbkdf2("sha256", $password, $salt, $iterations, 20);
        return $key;
    }
    public function genKey() {
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 1024,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privateKey);
        $pubKey = openssl_pkey_get_details($res);
        $publicKey = $pubKey['key'];
        
        return [
            'public_key' => $publicKey,
            'private_key' => $privateKey
        ];
    }
    public function hashPrivateKey($key, $password) {
        $privateKey = $key;
        $cipher = "aes-256-cbc";
        if (in_array($cipher, openssl_get_cipher_methods()))
        {
            $iv = "1234567891234567";
            $key = $this->pbkdf2($password);
            $privateKeyHash = openssl_encrypt($privateKey, $cipher, $key, $options=0, $iv);
            return $privateKeyHash;
        }
    }
    
    public function decryptPrivateKey($password, $privateKeyHash) {
        $cipher = "aes-256-cbc";
        if (in_array($cipher, openssl_get_cipher_methods()))
        {
            $iv = "1234567891234567";
            $key = $this->pbkdf2($password);
            $privateKey = openssl_decrypt($privateKeyHash, $cipher, $key, $options=0, $iv);
            return $privateKey;
        }
    }

    public function avatar(Request $request) {
        $user = User::find(Auth::user()->id);
        if($request->file)
        {
            $fileName = time().'.'.$request->file->getClientOriginalExtension();
            $request->file->move(public_path('avatar'), $fileName);
            $user->avatar = 'http://localhost:8080/avatar/'.$fileName;
            $user->update();
            return response()->json(['success' => 'Update success'], 200);
        }

       return response()->json(['error' => 'Update failed']);
    }

    public function edit(Request $request) {
        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->update();

       return response()->json(['success' => 'Update success']);
    }

    public function test() {
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 1024,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );
       
        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privateKey);
        // dd($privateKey);
        $pubKey = openssl_pkey_get_details($res);
        $publicKey = $pubKey['key'];
        User::find(3)->update([
            'public_key' => $publicKey,
            'private_key_hash' => $privateKey,
        ]);
        $user = User::find(27);
        openssl_public_encrypt('123', $encrypted, $user->public_key);
        openssl_private_decrypt($encrypted, $decrypted, $this->decryptPrivateKey());
        dd($decrypted);
        // dd($privateKey);
        $key = $publicKey.$privateKey;
        $private_key = Str::between($key, '-----BEGIN PRIVATE KEY-----', '-----END PRIVATE KEY-----');
        
        // var_dump($private_key);
    }
    public function decryptPrivateKey1() {
        $user = User::find(27);
        $private_key = $user->private_key_hash;
        $plaintext = $private_key;
        $cipher = "aes-256-cbc";
        if (in_array($cipher, openssl_get_cipher_methods()))
        {
            // $ivlen = openssl_cipher_iv_length($cipher);
            // dd($ivlen);
            $iv = "1234567891234567";
            // dd($iv);
            $key = $this->pbkdf2('1234');
            $tag = NULL;
            // $ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options=0, $iv); b     
            //store $cipher, $iv, and $tag for decryption later
            // echo $ciphertext;
            // echo $ciphertext;
            $ciphertext = "/cN8gdl8ufYo4yuUTFHl7w==";
            $original_plaintext = openssl_decrypt($plaintext, $cipher, $key, $options=0, $iv);
            return $original_plaintext;
        }
    }
}
