<?php

/**
 * @OA\Schema(
 * title="User Authentication",
 * description="User resource",
 * @OA\Xml(
 *  name="UserResource"
 *  )
 * )
 */
class UserResource
{
  /**
   * @OA\Property(
   *    type="string",
   *    property="access_token",
   *    description="Access token",
   * )
   * 
   * @var string
   */
  public $access_token;

  /**
   * @OA\Property(
   *    type="string",
   *    property="token_type",
   *    example="bearer",
   *    description="Token type",
   * )
   * 
   * @var string
   */
  public $token_type;

  /**
   * @OA\Property(
   *    type="object",
   *    property="user",
   *    description="User",
   *  ref="#/components/schemas/User"
   * )
   * @var \App\Virtual\Models\User
   */
  public $user;
}
