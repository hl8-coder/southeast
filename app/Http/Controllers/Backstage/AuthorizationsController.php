<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\AuthorizationRequest;
use Illuminate\Support\Facades\Auth;

class AuthorizationsController extends BackstageController
{
    /**
     * @OA\Post(
     *      path="/backstage/authorizations",
     *      operationId="bo.authorizations.store",
     *      tags={"Backstage-授权"},
     *      summary="后台授权",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="账号"),
     *                  @OA\Property(property="password", type="string", format="password", description="密码"),
     *                  required={"name", "password"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/tokenInfo")
     *       ),
     *       @OA\Response(response=401, description="账号或密码错误"),
     *       @OA\Response(response=403, description="禁止登录"),
     * )
     */
    public function store(AuthorizationRequest $request)
    {
        $credentials = [
            'name'     => $request->name,
            'password' => $request->password,
        ];

        if (!$token = Auth::guard('admin')->attempt($credentials)) {
            return $this->response->errorUnauthorized(__('authorization.wrong_name_or_password'));
        }

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    /**
     * @OA\Put(
     *      path="/backstage/authorizations/current",
     *      operationId="backstage.authorizations.current.update",
     *      tags={"Backstage-授权"},
     *      summary="刷新token",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/tokenInfo")
     *       ),
     *       @OA\Response(response=401, description="无效token"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update()
    {
        $token = Auth::guard('admin')->refresh();
        return $this->respondWithToken($token);
    }

    /**
     * @OA\Delete(
     *      path="/backstage/authorizations/current",
     *      operationId="backstage.authorizations.current.destroy",
     *      tags={"Backstage-授权"},
     *      summary="登出",
     *      @OA\Response(
     *          response=204,
     *          description="no content"
     *       ),
     *       @OA\Response(response=401, description="无效token"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function destroy()
    {
        try {
            Auth::guard('admin')->logout();
        } catch (\Exception $exception) {

        }

        return $this->response->noContent();
    }

    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => Auth::guard('admin')->factory()->getTTL() * 60,
            'admin'        => auth('admin')->user()->only(['id', 'name', 'nick_name', 'language', 'avatar']),
        ]);
    }
}
