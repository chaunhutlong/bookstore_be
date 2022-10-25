<?php

/**
 * @OA\Schema(
 *      title="Register User request",
 *      description="Register User request body data",
 *      type="object",
 *      required={"name", "email", "password"}
 * )
 */


class RegisterUserRequest
{
  /**
   * @OA\Property(
   *      property="name",
   *      type="string",
   *      description="Name of the new user",
   *      example="John Doe"
   * )
   * @OA\Property(
   *     property="email",
   *     type="string",
   *     description="Email of the new user",
   *     example="example@example.com"
   * )
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
  public $name;
  public $email;
  public $password;
}
