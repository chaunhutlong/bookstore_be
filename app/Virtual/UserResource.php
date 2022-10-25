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
   * 
   * @OA\Property(
   *    type="integer",
   *    property="id",
   *    description="User id",
   * )
   * 
   * @OA\Property(
   *    type="string",
   *    property="name",
   *    description="User name",
   * )
   * 
   * @OA\Property(
   *    type="string",
   *    property="email",
   *    description="User email",
   * )
   * 
   * @OA\Property(
   *    type="string",
   *    format="date-time",
   *    property="created_at",
   *    description="User creation date",
   * )
   * 
   * @OA\Property(
   *    type="string",
   *    format="date-time",
   *    property="updated_at",
   *    description="User updated date",
   * )
   * @var  \App\Virtual\Models\User
   */
  private $user;
}
