<?php

/**
 * @OA\Schema(
 *      title="Login User request",
 *      description="Login User request body data",
 *      type="object",
 *      required={"email", "password"}
 * )
 */


class LoginUserRequest
{
  /**
   * @OA\Property(
   *     property="email",
   *     type="string",
   *     description="Email of the new user",
   *     example="example@example.com"
   * )
   * @var string
   */
  public $email;

  /**
   * @OA\Property(
   *      property="password",
   *      type="string",
   *      description="Password of the new user",
   *      example="123456"
   * )
   *    * 
   * @access
   * @var string
   */
  public $password;
}
